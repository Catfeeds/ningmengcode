<?php
namespace login;
use think\Controller;
use think\Session;
use think\Cache;
use think\Db;
use think\Request;
//use think\Log;
use Keyless;
/**
 * 用户登陆验证
 * 用户session 注入
 * 返回登陆后的 string  token 
 * 参数及签名认证
 * 注册后salt及密码生成
 * 
 */
class Authorize extends Controller{
    //[1为老师0为超级管理员，2机构添加的管理账号,3为学生]
    private $arrtable = [0=>'nm_allaccount' ,1=> 'nm_allaccount',2=> 'nm_allaccount',3=>'nm_studentinfo']  ;
    /**
     * 初始化操作
     * @access protected
     */
    public $userInfo;

    protected function _initialize()
    {
		header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        parent::_initialize();

        //获取访问方法路径
        $request = Request::instance();

        $modulename     = $request->module();
        $controllername = $request->controller();
        $action         = $request->action();
		
        //根据域名 获取机构id
        //获取当前 访问是否需要验证
        $return = $this->checkMethodNode($modulename,$controllername,$action);
        if($return['code']==0){

            $requestpost = Request::instance()->post(false);
           // dump($requestpost);exit;
			
            $header = Request::instance()->header();
            $header = where_filter($header,['token','sign','starttime']);
			
            if(!isset($header['token'])||!Cache::has(config('queue.login_list').getTokenKey($header['token']))){
                $this->ajaxReturn(['code'=>-40666,'info'=>lang('-40667'),'data'=>'']);
            }
 
            // 解析出真正的token
            $token = getTokenKey($header['token']);
            // 根据token 获取对应的缓存
            $this->userInfo = Cache::get(config('queue.login_list').$token);

            // 单点登陆效验
          	if ($header['token']!=$this->userInfo['token']){
				$this->ajaxReturn(['code'=>-40666,'info'=>lang('-40666'),'data'=>'']);
			}

            // 根据传输参数生成签名
			if(strtolower($modulename) == 'apphjx' && strtolower($controllername) == 'composition' && strtolower($action) == 'compositionmodifyadd'){
				$requestpost = json_decode(str_replace("'", '"', $requestpost['data']), true);
			}
			
            $sign = Keyless::getMd5String($requestpost,$this->userInfo['key'],$header['starttime'],$header['token']);
            if($sign!=$header['sign']){
				$this->ajaxReturn(['code'=>-40669,'info'=>lang('-40669'),'data'=>'']);
            }

			//验证通过 查看对应的登陆角色是否有接口操作权限
            if(!$this->userInfo['roleid']){
            	// 用户未写入权限
				$this->ajaxReturn(['code'=>-40601,'info'=>lang('-40601'),'data'=>'']);
			}

			if(!$this->getUserUrl($this->userInfo['roleid'],$return['data'])){
				$this->ajaxReturn(['code'=>-40602,'info'=>lang('-40602'),'data'=>'']);
			}

       }else if($return['code']!=1){
           exit(json_encode($return));
       }
    }


	/**
	 * [getUserUrl 对应的角色 查看是否有此接口的访问权限]
	 * @param $token
	 * @param $modulename
	 * @param $controllername
	 * @param $action
	 */
	public function getUserUrl($roidlist,$nodeid){
		if($roidlist){
			$arr = [];
			foreach ($roidlist as $k => $v){
				$arr = array_merge($this->getNoteId($v),$arr);
			}
			if(in_array($nodeid,$arr)){
				return true;
			}
		}
		return false;
	}


	/**
	 * [getNoteId 根据角色获取对应的菜单]
	 * @param $nodeid
	 */
	public function getNoteId($roleid){
		// 加缓存 后期接口权限发生变动更新缓存
//		Cache::rm('RoleKey_'.$roleid);
		if(Cache::has('RoleKey_'.$roleid)){
			return Cache::get('RoleKey_'.$roleid);
		}else{
			// 查询
			$list = Db::table('nm_accessroleallow')->where(['roleid'=>$roleid])->field('nodeid')->select();
			if($list){
				$list = array_column($list,'nodeid');
				Cache::set('RoleKey_'.$roleid,$list);
				return $list;
			}else{
				return [];
			}
		}
	}


    /**
     *  查看当前访问是否需要访问控制 在nm_accessnode 表查询
     *
     *
     *
     */
    private function checkMethodNode($modulename,$controllername,$action){
        $where = [
                    'module'=> $modulename,
                    'controller'=> strtolower($controllername),
                    'action'=> strtolower($action),
                    'status'=> 0,
                ] ;
        $ret = Db::table('nm_accessnode')
                ->where($where)
                ->field('id')
                ->find();

        if(empty($ret)){// 如果为空无需认证
			return return_format('',1,'OK');
        }else{
        	//查看 用户是否有权限
            return return_format($ret['id'],0,'OK');
        }
    }


    /**
     * [getUserOrgan 根据用户的访问获取用户的机构标记]
     * @Author wyx
     * @DateTime 2018-04-27T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function getUserOrgan(){
        // $organid  = Session::get('organid') ;
        // if( !empty($organid) ) return true ;// 如果organid 存在在session中 不再验证

        $hostname = $_SERVER['HTTP_HOST'] ;
        $arr = explode('.', $hostname) ;
        //严格校验域名必须三段
            $organstr = $arr[0] ;
            $organmsg = Db::table('nm_organ')->field('id,organname,profile')->where(['domain'=>$organstr])->find() ;
            return $organmsg?$organmsg['id']:1;
            // if(!empty($organmsg)){// 恶意解析 导致问题 
                
                // Session::set('organid',$organmsg['id']) ;
                // Session::set('organname',$organmsg['organname']) ;
            //     return true ;
            // }else{
            //     return false ;//没有匹配的机构 信息，一般不会发生，除了恶意解析
            // }
        // }
        // }else{//域名异常
        //     return false ;
        // }
    }
    /**
     * [checkUser 检测用户提交的信息验证登陆]
     * @Author wyx
     * @DateTime 2018-04-27T15:16:36+0800
     * @param    [string]          $user  [用户名]
     * @param    [string]          $pass  [密码]
     * @param    [int]             $type  [1为老师0为超级管理员，2机构添加的管理账号,3为学生]
     * @param    [int]             $organid [机构信息id]
     * @return   [type]                         [description]
     */
    public function checkUser($user,$pass,$type,$organid){
        if(!empty($user) && !empty($pass) && in_array($type,[0,1,2,3]) && $organid>0){
            //用户 登陆验证
            $returnmsg = $this->checkTable($user,$pass,$type,$organid);
            
            //需要返回 sessionid randstring
            return $returnmsg;
           
        }else{
            return return_format('',-10001,'登陆参数异常');
        }
    
    }
    /**
     * [checkTable 验证用户名密码信息]
     * @Author wyx
     * @DateTime 2018-04-27T15:43:03+0800
     * @param    [string]            $user    [用户名]
     * @param    [string]            $pass    [密码]
     * @param    [int]               $type    [用户类型]
     * @param    [int]               $organid [机构标识id]
     * @return   [bool]                checkTable       [验证成功后存入基本信息，并返回状态]
     */
    private function checkTable($user,$pass,$type,$organid){
        switch($type){
            case 0 : //超级管理员 信息
            
            case 1 :// 机构教师信息
           
            case 2 : // 机构添加的管理员
            $field = 'id,uid,username,usertype,mobile,password,mix' ;
            $where = ['username|mobile'=>$user,'organid'=>$organid,'status'=>0] ;
            $tablemsg = Db::table($this->arrtable[$type])->field($field)->where($where)->find() ;
            
            //检测身份
            $flag = $this->checkUserMark($pass,$tablemsg['mix'],$tablemsg['password']) ;
            if($flag){//认证成功
                Session::set('organid',$organid) ;
                Session::set('allaccountid',$tablemsg['id']) ;
                Session::set('adminid',$tablemsg['uid']) ;
                Session::set('usertype',$tablemsg['usertype']) ;

                //设置权限
                $aclret = $this->getUserAcl($tablemsg['uid'],$tablemsg['usertype']);
                if( $aclret['code'] != 0) return $aclret;
                //获取上次登录时间 并记录本次登陆时间
                $logintime = Db::table('nm_adminmember')->where([ 'id'=>$tablemsg['uid'] ])->field('logintime,userimage')->find() ;
                             Db::table('nm_adminmember')->where([ 'id'=>$tablemsg['uid'] ])->update(['logintime'=>time()]) ;
                             
                return return_format(['logintime'=>date('Y-m-d H:i:s',$logintime['logintime']),'useraccount'=>$tablemsg['username'],'mobile'=>$tablemsg['mobile'],'headimage'=>$logintime['userimage']],0,'登陆成功');
            }else{// 认证失败
                return return_format('',-10000,'登陆失败');
            }
            break;
            case 3 :// 学生登陆
            $field = 'id,username,prphone,mobile,password,mix' ;
            $where = ['username|mobile'=>$user,'status'=>0,'organid'=>$organid,'delflag'=>0] ;
            $tablemsg = Db::table($this->arrtable[$type])->field($field)->where($where)->find() ;
            //检测身份
            $flag = $this->checkUserMark($pass,$tablemsg['mix'],$tablemsg['password']) ;
            if($flag){//认证成功
                Session::set('organid',$organid) ;
                Session::set('teachid',$tablemsg['id']) ;
                Session::set('usertype',3) ;
                //设置权限
                $aclret = $this->getUserAcl($tablemsg['id'],3);
                if( $aclret['code'] != 0) return $aclret;

                return return_format('',0,'登陆成功');

            }else{// 认证失败
                return return_format('',-10000,'登陆失败');
            }
        }
    }
    /**
     *  登录后将权限计入session  登陆成功后调用， 下次访问需要验证的方法即生效
     *  将用户信息 权限入库
     *  用户       $uid  
     *  用户类型   $usertype  
     *  
     */ 
    private function getUserAcl($uid,$usertype){
        // 获取 登陆用户 所在的 权限组
        $roleid = Db::table('nm_accessroleuser')
                  ->where(['uid'=>$uid,'usertype'=>$usertype])
                  ->field('roleid')
                  ->find();
        if(empty($roleid)){// 无法获取信息
            return return_format('',40012,'获取权限失败');
        }else{
            $aclstr = Db::table('nm_accessroleallow')
                  ->where(['roleid'=>$roleid['roleid']])
                  ->field('group_concat(nodeid) aclstr')
                  ->find();
            // var_dump($aclstr);
            if(isset($aclstr['aclstr'])) Session::set('aclstr',$aclstr['aclstr']);

            return return_format('',0,'OK');
        }

    }
    /**
     *  新增用户后 ，给用户添加的默认的组
     *  用户       $uid  
     *  用户类型   $usertype   
     *  根据用户的类型 添加到默认分组
     *
     */
    public function addUserDefaultAcl($uid,$usertype){
        $typevsrole = [//1为老师0为超级管理员，2机构添加的管理账号
                    1 => 2 ,//1老师类型   对应 
                    2 => 1 ,//用户类型 2  对应 机构管理员 角色 
                    3 => 3 ,//3学生类型 对应3 学生角色
                    4 => 4 ,//4官方类型 对应4 官方角色
                    5 => 5 ,//5 对应5 好际星app教师组
                ] ;
        $data = [
                'roleid' => $typevsrole[$usertype],
                'uid' => $uid, 
                'usertype' => $usertype == 5 ? 1 : $usertype,

            ] ;
        Db::table('nm_accessroleuser')
        ->insert($data);

    }
    /**
     * [checkUserMark description]
     * @Author wyx
     * @DateTime 2018-04-27T16:35:02+0800
     * @param    [string]                 $pass [用户提交密码]
     * @param    [string]                 $mix  [description]
     * @param    [type]                   $sign [description]
     * @return   [bool]                         [true 代表成功，false 代表失败]
     */
    public function checkUserMark($pass,$mix,$sign){
        $md5str = md5(md5($pass).$mix);

        for ($i=0; $i < 5; $i++) { 
            $md5str = md5($md5str) ;
        }
        // var_dump($sign);
        // var_dump($md5str);exit();
        if($sign==$md5str){
            return true ;
        }else{
            return false ;
        }

    }
    /**
     * 给用户生成 密码 和 mix
     * [createUserMark 生成用户的机密字符存储在数据库，当用户登陆时比对]
     * 创建用户时调用
     * @Author wyx
     * @DateTime 2018-04-27T16:22:58+0800
     * @param    [string]            $pass    [密码]
     * @return   [type]                   [description]
     */
    public function createUserMark($pass){
        $mix = $this->getRandString(16) ;
        $md5str = md5(md5($pass).$mix);

        for ($i=0; $i < 5; $i++) { 
            $md5str = md5($md5str) ;
        }

        return ['mix'=>$mix,'password'=>$md5str] ;

    }
    /**
     * 注入用户登陆信息
     * [insertTokenString 将token植入当前请求环境]
     * @Author wyx wyx web ver1 不使用
     * @DateTime 2018-04-27T17:43:39+0800
     * 
     * @return   [type]                              [description]
     */
    private function insertTokenString(){
        $sessionid = $_POST['mixauth'] ;//客户端带过来的认证标记
        
        $truestr = substr($sessionid,0,strlen($sessionid)-7);
        session_id($truestr) ;
    }
    /**
     * [getRandString 生成随机字符串]
     * @Author wyx
     * @DateTime 2018-04-27T14:53:16+0800
     * @param    [int]                      [设置需要的字符串的长度默认为8]
     * @return   [string]                   [description]
     */
    public function getRandString($length=8){
        $numstr    = '1234567890' ;
        $originstr = 'abcdefghijklmnopqrstuvwxyz' ;
        $origin = str_repeat($numstr,6).$originstr.strtoupper($originstr) ;

        return substr(str_shuffle($origin), -$length);

    }
    /**
     * [checkDataSign 验证签名]
     * @Author wyx web ver1 不使用
     * @DateTime 2018-04-27T18:56:46+0800
     * @return   [bool]                   [验证数据合法性]
     */
    public function checkDataSign(){
        $origindata = $_POST;
        //提取传送的签名
        $noncestr = $origindata['noncestr'];//sign 客户端签名字符串
        //获取参与签名的字符串
        $mixstr  = Session::get('presign') ;
        // var_dump($mixstr);
        //去除不需要签名的字段
        unset($origindata['noncestr']);
        unset($origindata['mixauth']);

        sort($origindata);
        $tempstr = '' ;
        foreach ($origindata as $key => $val) {
            $tempstr.= $key.'='.$val.'&';
        }
        $tempstr = rtrim($tempstr,'&').$mixstr;

        $crypt = sha1($tempstr) ;
        // var_dump($crypt);
        if($crypt==$noncestr){
            return true ;
        }else{
            return false ;
        }
    }
}