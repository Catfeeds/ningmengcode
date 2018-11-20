<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use app\admin\business\OrganManage;
use login\Authorize;
use think\Request;
use Verifyhelper;
use app\index\business\UserLogin;
class Index extends Authorize
{

    //自定义初始化
    protected function _initialize() {
        parent::_initialize();
        header('Access-Control-Allow-Origin: *');
//      header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
    }
    /**
     * 分组名称  application 下的目录名称  作为分组名称
     *
     *     每个目录下的 controller 下的文件名字就是 分组下的控制器的 名字
     *
     *       每个文件中的方法名字就是 就是可访问项目
     *  /admin/index/get_group_model_method
     */
    protected function get_group_model_method(){
        $toppath = './../application/' ;
        $list= scandir($toppath);
        $groupname = [] ;
        foreach ($list as $name) {
            if($name=='.' || $name=='..' || $name=='extra') continue;
            if(is_dir($toppath.$name)){
                array_push($groupname,$name);
            }else{

            }
        }
        // var_dump($groupname);
        $nodemap = $this->get_belong_group($toppath ,$groupname);

//        dump($nodemap);
        //展开 路由路径 将节点入库
        $this->explodeRoutePath($nodemap);

    }
    public function index(){
        dump($_SERVER);
    }
    /**
     * $path $namearr
     * 获取 分组下的控制器 文件名字列表
     * 寻找分组下的文件 并处理
     *
     */
    private function get_belong_group($toppath ,$namearr){
        $functionbnum = 0 ;
        foreach ($namearr as &$name) {
            $controllername = [] ;
            $newpath = $toppath.$name.'/controller/';
//            dump($newpath);
            if(!file_exists($newpath)){

			}else{
				$list= scandir($newpath);
				//获取分组中的 文件名字列表

				if($list){
					foreach ($list as $filename) {
						if($filename=='.' || $filename=='..') continue;

						$filepathname = $newpath.$filename;
						if(is_file($filepathname)){
							$tempname = substr($filename,0,-4);
							// array_push($controllername,$tempname);
							//调用文件匹配
							$actions = $this->get_method_from_file($filepathname);
							// $functionbnum+=$count;
							$controllername[$tempname] = $actions;
						}else{

						}
					}
				}
				$name = ['groupname'=>$name,'controllername'=>$controllername] ;
			}
        }
        return $namearr;

    }
    /**
     *  传入文件名字 处理返回  文件中所有的公开的方法数组
     *  从文件中 抽取 公开的方法
     *
     */
    private function get_method_from_file($filename){
        $cont = file_get_contents($filename);
        // $filesource = fopen($filename,'r+');
        // $cont = fread($filesource,filesize($filename));
        preg_match_all('/public(\s)+function(\s)+(\w+)\(*/m',$cont , $matches);
        //echo $filename;
        //var_dump($matches[3]);
        return $matches[3];
    }
    /**
     *  将收集好的 数组 展开 组合入库
     *
     */
    private function explodeRoutePath($arr){
        $module = '' ;
        $controller = '' ;
        $action = '' ;
        foreach ($arr as $key => &$val) {
        	if(is_array($val)){
				$module = strtolower($val['groupname']);
				foreach ($val as $key1 => &$val1) {
					if(!is_array($val1) )  continue;
					foreach($val1 as $key2=>$val2) {

						$controller = strtolower($key2) ;

						foreach ($val2 as $value) {

							$action = strtolower($value) ;

							// echo sprintf("group:%s;controller:%s;method:%s \r\n",$module,$controller,$action);
							$this->nodeInsertDb($module,$controller,$action);
						}
					}

				}
			}

        }
    }
    /**
     *  将节点入库
     *
     */
    private function nodeInsertDb($module,$controller,$action){
        if(in_array($action, ['__construct'])) return '';
        $data = [
                    'module'    => $module ,
                    'controller'=> $controller ,
                    'action'    => $action ,

                ] ;
        Db::table('nm_accessnode')->insert($data);
    }
    /**
     *  将学生 老师 机构的 角分 按照目录做划分 初始化
     *
     *  目前 role 1 管理员  2 教师  3 学生  验证中 0 超级管理员 没有限制
     *  /admin/Index/devideRoleInit
     *
     */
    public function devideRoleInit(){
        //查看 目前的角色
        $groups = Db::table('nm_accessrole')
        ->field('id,rolename')
        ->select();

        $data = [] ;
        foreach ($groups as $group) {
            $result = Db::table('nm_accessnode')
            ->field('id')
            ->where('module','EQ',$group['rolename'])
            ->whereOr('module','EQ','app'.$group['rolename'])
            ->select();

            foreach ($result as $value) {
                array_push($data,['roleid'=>$group['id'],'nodeid'=>$value['id']]);
            }

        }

        $result = Db::table('nm_accessroleallow')
        ->insertAll($data);

        // var_dump($data);exit();

    }
    /**
     *
     *  将当前用户 分到角色
     *  /admin/Index/userPartingRole
     *
     *
     */
    protected function userPartingRole(){
        //将教师和 管理员 分到角色
        $userarr = Db::table('nm_allaccount')
        ->field('uid,usertype')
        ->where('usertype','NEQ',0)
        ->select();

        $rolearr = [//1为老师0为超级管理员，2机构添加的管理账号
                    1 => 2 ,//老师
                    2 => 1 ,//机构管理员
                    3 => 3 ,//机构管理员
                ] ;


        //将学生分到角色
        $studarr = Db::table('nm_studentinfo')
        ->field('id uid,3 usertype')
        ->select();

        //合并数据
        $userarr = array_merge($userarr,$studarr) ;

        $data = [] ;
        foreach ($userarr as $val) {

            array_push($data,['roleid'=>$rolearr[$val['usertype']],'uid'=>$val['uid'],'usertype'=>$val['usertype']]);

        }

        Db::table('nm_accessroleuser')->insertAll($data);
        var_dump($data);

    }


    
    /**
     * [registerUser 教师|企业 注册(注册新机构的超级管理员)]
     * @Author zzq
     * @DateTime 2018-05-24
     * @param useraccount string           [账户名|用户名]       
     * @param mobile string           [联系电话]        
     * @param password string           [密码]     
     * @param repassword string           [重复密码]     
     * @param mobileCode string           [手机验证码]    
     * @param sessionId string           [图形验证码sessionId]    
     * @param imageCode string           [图形验证码]    
     * @param restype int           [注册类型 1表示个体机构老师|2代表企业]    
     * @return   [array]                   [description]
     */        
    public function registerUser(){
        header('Access-Control-Allow-Origin: *');
        //var_dump(Request::instance()->post());
        $userLogin = new UserLogin();
        $rsaStr = Request::instance()->post('data');
        $rsaData = $userLogin->rsaDecode($rsaStr);
        // var_dump($rsaData);
        // die;
        $data = [];
        $vip = $rsaData['vip'];
        $useraccount = $rsaData['useraccount'];
        $mobile = $rsaData['mobile'];
        $password = $rsaData['password'];

        $imageCode = $rsaData['imageCode'];
        $sessionId = $rsaData['sessionId'];
        $mobileCode = $rsaData['mobileCode'];
        $restype = $rsaData['restype'];
        $key = $rsaData['key'];

        $data['vip'] = $vip ? $vip : '';
        $data['useraccount'] = $useraccount ? $useraccount : '';
        $data['mobile'] = $mobile ? $mobile : '';
        $data['password'] = $password ? $password : '';
        $data['sessionId'] = $sessionId ? $sessionId : '';
        $data['imageCode'] = $imageCode ? $imageCode : '';
        $data['mobileCode'] = $mobileCode ? $mobileCode : '' ;
        $data['restype'] = $restype ? $restype : '' ;
        $data['key'] = $key ? $key : '' ;

        // var_dump($data);
        // die;
        
        $organobj = new OrganManage();
        $res = $organobj->registerUser($data);
        $this->ajaxReturn($res);
        return $res;
    }

    /**
     * [sendRegisterUserCode //教师|企业 注册|忘记密码 发送手机验证码]
     * @Author zzq
     * @DateTime 2018-05-24   
     * @param mobile string           [联系电话]        
     * @param type int           [0表示注册 1表示修改密码]        
     * @param sessionId string           [图形验证码sessionId]    
     * @param imageCode string           [图形验证码]         
     * @return   [array]                   [description]
     */
    public function sendOrganWebUserCode(){
        header('Access-Control-Allow-Origin: *');
        $mobile = Request::instance()->post('mobile','');
        $type = Request::instance()->post('type','');
        $sessionId = Request::instance()->post('sessionId','');
        $imageCode = Request::instance()->post('imageCode','');
        $vip = Request::instance()->post('vip','');

        $mobile = $mobile ? $mobile : '';
        $type = $type ? $type : '';
        $sessionId = $sessionId ? $sessionId : '';
        $imageCode = $imageCode ? $imageCode : '';
        $vip = $vip ? $vip : '';

        $organobj = new OrganManage();
        $res = $organobj->sendOrganWebUserCode($mobile,$type,$sessionId,$imageCode,$vip);
        $this->ajaxReturn($res);
        return $res;
    }

    /**
     * [showOrganWebVerify  //教师|企业 注册|忘记密码 获取图形验证码]
     * @Author zzq
     * @DateTime 2018-05-24
     * @param 无参数       []  
     * @return 返回的信息
     */
    public function showOrganWebVerify(){
        header('Access-Control-Allow-Origin: *');
        $verify = new Verifyhelper();
        $data = $verify->verify();
        $res = return_format($data,0,lang('success'));
        $this->ajaxReturn($res);
        return $res;    
    }

    /**
     * [findPassOne //机构忘记密码(第一步)]
     * @Author zzq
     * @DateTime 2018-05-24
     * @param mobile string           [手机号]           
     * @param imageCode string           [图形验证码]     
     * @param sessionId string           [图形验证码的sessionId]     
     * @param isVip int           [是不是vip机构 默认是0是免费 1是vip]     
     * @return   [array]                   [description]
     */
    public function findPassOne(){
        header('Access-Control-Allow-Origin: *');
        //检测手机号
        //检测图形验证码
        $data = [];
        $mobile = Request::instance()->post('mobile','');
        $imageCode = Request::instance()->post('imageCode','');
        $sessionId = Request::instance()->post('sessionId','');
        $isVip = Request::instance()->post('isVip','');

        $data['mobile'] = $mobile ? $mobile : '';
        $data['imageCode'] = $imageCode ? $imageCode : '';
        $data['sessionId'] = $sessionId ? $sessionId : '';
        $data['isVip'] = $isVip ? $isVip : 0;

        $organobj = new OrganManage();
        $res = $organobj->findPassOne($data);
        $this->ajaxReturn($res);
        return $res;

    }

    
    /**
     * [findPassTwo //机构忘记密码(第二步)]
     * @Author zzq
     * @DateTime 2018-05-24
     * @param mobile string           [手机号]           
     * @param mobileCode string            [该手机号的验证码]           
     * @param newPassword string           [新密码]     
     * @param imageCode string             [图形验证码]     
     * @param sessionId string             [图形验证码的sessionId]  
     * @return   [array]                   [description]
     */
    public function findPassTwo(){
        header('Access-Control-Allow-Origin: *');
        //检测手机验证码,修改密码
        $data = [];
        $mobile = Request::instance()->post('mobile','');
        $mobileCode = Request::instance()->post('mobileCode','');
        $newPassword = Request::instance()->post('newPassword','');
        $imageCode = Request::instance()->post('imageCode','');
        $sessionId = Request::instance()->post('sessionId','');
        $isVip = Request::instance()->post('isVip','');

        $data['mobile'] = $mobile ? $mobile : '';
        $data['mobileCode'] = $mobileCode ? $mobileCode : '';
        $data['newPassword'] = $newPassword ? $newPassword : '';
        $data['imageCode'] = $imageCode ? $imageCode : '';
        $data['sessionId'] = $sessionId ? $sessionId : '';
        $data['isVip'] = $isVip ? $isVip : 0;

        $organobj = new OrganManage();
        $res = $organobj->findPassTwo($data);
        $this->ajaxReturn($res);
        return $res;
    }

    /**
     * [applyVipOrgan //免费机构申请vip机构]
     * @Author zzq
     * @DateTime 2018-05-24
     * @param organid int           [机构id]            
     * @return   [array]                   [description]
     */
    public function applyVipOrgan(){
        header('Access-Control-Allow-Origin: *');
        $organid = Request::instance()->post('organid','');
        $organid = $organid ? $organid : '';
        $organobj = new OrganManage();
        $res = $organobj->applyVipOrgan($organid);
        $this->ajaxReturn($res);
        return $res;
    }

    // public function getSessionUser(){
    //     $obj = new Authorize();
    //     $flag = $obj->checkUserMark('123456','3818hm490MS28FA5','62ee5a54ba52860c9feebb83a12fd266');
    //     var_dump($flag);
    // }
}
