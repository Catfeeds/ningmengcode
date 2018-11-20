<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\official\business;
use app\official\model\Organ;
use app\official\model\Allaccount;
use Calendar;
use app\official\model\Refuseorgan;
use think\Db;
use app\official\model\Adminmember;
use app\official\model\Organinfo;
use app\official\model\Organconfirm;
use app\official\model\Organconfig;
use app\official\model\Organslideimg;
use app\official\model\Officialuseroperate;
use app\official\model\Teacherinfo;
use think\Cache;
use think\Log;
use Messages;
use login\Authorize;
use Verifyhelper;
use think\Session;
use app\admin\business\TimingTask;
use app\official\model\Organauditbillorder;
class OrganManage{

    /***********************官方后台*********************************/
    /**
    * getOrganList 查询分类列表
    * @ zzq
    * @param $data array() 包括organname机构名称 auditstatus
    * @param $orderbys str 排序方式
    * @param $pagenum int 查询页数
    * @param $pernum int 一页几条
    * @return array();
   */

    public function getOrganList($data){

        //if (!$where) $where = [];
        //定义状态值的数组
        $ret = [0,1,2,3]; //5代表全部
        $where = [];
        $auditstatus = $data['auditstatus'];
        if(!in_array($auditstatus,$ret)){
        	return return_format([],50076,lang('50076'));
        }
        //当传的值为3的时候表示状态值未3或者4
        if( ($data['auditstatus'] == 0) || ($data['auditstatus'] == 1) || ($data['auditstatus'] == 2)){
        	$where['auditstatus'] = $auditstatus; 
        }
        //通过（启用或者禁用）
        if($data['auditstatus'] == 3){ 
        	$where['auditstatus'] = ['in',[3,4]];        	
        }
        //状态值不能为-1
        // if($data['auditstatus'] == 4){ 
        // 	$where['auditstatus'] = ['NEQ',-1];        	
        // }

        if(!empty($data['organname'])){
            //去除空格
            $data['organname'] = trim($data['organname']);
            $data['organname'] = $this->filterKeywords($data['organname']);
        	$organname = $data['organname'];
        	//模糊查询
            $where['organname']  = ['like',"%".$organname."%"];
        }

        //此时不是vip
        $where['vip'] = ['EQ',1];

        //获取的是其他机构的列表 organid>1代表的是注册的其他机构
        //$where['id'] = ['GT',1];

        $organ = new Organ();
        // var_dump($where);
        // die;
        $arr = [];
        $ret =  $organ->getOrganList($where,$data['orderbys'],$data['pagenum'],$data['pernum']);
        $lists = $ret['lists'];
        foreach($lists as $k=>$v){
            //显示通过时间
            if(!empty($v['passtime'])){
                $lists[$k]['passtime'] = Date('Y-m-d H:i:s',$v['passtime']);
            }
        	//显示注册时间
        	$allAccount = new Allaccount();
            $lists[$k]['addtime'] = Date('Y-m-d H:i:s',$allAccount->getOrganAddTime($v['id']));
        	//显示注册人
        	$adminMember = new Adminmember();
        	$res = $adminMember->getOrganUser($v['id']);
        	//$lists[$k]['adminname'] = $res['adminname'];        	
        	$lists[$k]['useraccount'] = $res['useraccount'] ? $res['useraccount'] : ''; 
            $lists[$k]['mobile'] = $res['mobile'] ? $res['mobile'] : '';        	
	        //当被拒绝的时候显示拒绝理由
	        if($lists[$k]['auditstatus'] == 2){
	        	$refuseOrgan = new Refuseorgan();
	        	$lists[$k]['refuseinfo'] = $refuseOrgan->getRefuseOrganInfoById($v['id']);	
	        }
        }

        $arr['lists'] = $lists;
        $arr['count'] = $ret['count'];
        $arr['pagenum'] = $ret['pagenum'];
        return return_format($arr,0,lang('success'));
    }

    /**
    * getApplyVipOrganList 查询申请vip机构列表
    * @ zzq
    * @param $data array() 包括organname机构名称
    * @param $orderbys str 排序方式
    * @param $pagenum int 查询页数
    * @param $pernum int 一页几条
    * @return array();
   */

    public function getApplyVipOrganList($data){

        $where = [];
        if(!empty($data['organname'])){
            $data['organname'] = $this->filterKeywords($data['organname']);
            $organname = $data['organname'];
            //模糊查询
            $where['organname']  = ['like',"%".$organname."%"];
        }

        $where['id'] = ['GT','1'];
        //此时不是vip
        $where['vip'] = ['EQ','0'];
        //申请vip的状态是1
        $where['applyvip'] = ['EQ','1'];
        //并且是已经认证过的机构
        $where['auditstatus'] = ['IN',['3','4']];

        $organ = new Organ();
        $arr = [];
        $ret =  $organ->getApplyVipOrganList($where,$data['orderbys'],$data['pagenum'],$data['pernum']);
        // var_dump($ret);
        // die;
        $res = [];
        foreach ($ret['lists'] as $k => $v) {
            $res[$k]['id'] = $v['id'];
            $res[$k]['organname'] = $v['organname'] ? $v['organname'] : '';
            $res[$k]['contactname'] =  $v['contactname'] ? $v['contactname'] : '';
            $res[$k]['contactphone'] =  $v['contactphone'] ? $v['contactphone'] : '';
            $res[$k]['contactemail'] =  $v['contactemail'] ? $v['contactemail'] : '';
            if($v['applyviptime']){
                $res[$k]['applyviptime'] =  Date('Y-m-d H:i:s',$v['applyviptime']);
            }

        }
        // var_dump($res);
        // die;
        $res['count'] = $ret['count']; 
        $res['pagenum'] = $ret['pagenum']; 
        return return_format($res,0,lang('success'));
    }



	/**
	 * [getAllOrganListCount 获取各个分类机构数目]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            []    [筛选条件]
	 * @return   [int]              $count        [查询的数目]
	 */
    public function getAllOrganListCount(){
        $where = [];
        $organ = new Organ();
        $res = $organ->getAllOrganListCount();
        return return_format($res,0,lang('success'));   	
    }

    /**
    *  getOrganRegisterInfo  //获取机构的注册信息
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
    public function getOrganRegisterInfo($organid){
        //验证传参
        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }

        //查询用户名 手机号  注册时间
        $allaccount = new Allaccount();
        $res = $allaccount->getOrganUserInfo($organid);
        if(!$res){
            return return_format('',50084,lang('50084'));
        }
        //查询域名  审核状态
        $organ = new Organ();
        $ret = $organ->getOrganById($organid);
        if(!$ret){
            return return_format('',50083,lang('50083'));
        }
        //组装返回的信息
        $data = [];
        $data['username'] = $res['username'] ? $res['username'] : '';
        $data['mobile'] = $res['mobile'] ? $res['mobile'] : '';
        $data['addtime'] = Date("Y-m-d H:i:s",$res['addtime']) ? Date("Y-m-d H:i:s",$res['addtime']) : '';

        $data['domain'] = $ret['domain'] ? $ret['domain'] : '';
        $data['auditInfo'] = $this->getAuditInfo($ret['auditstatus']);
        return return_format($data,0,lang('success'));

    }

    /**
    *  doAudit  //进行审核
    * @ zzq  2018-05-03
    * @param array 数据 
    * @return array 返回信息  ;
   */ 
    public function doAudit($data){
        $organid = $data['organid'];
        $auditstatus = $data['auditstatus'];
        $refuseinfo = $data['refuseinfo'];

        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }
        $obj = new Organ();
        //查看是不是待审的机构
        $preInfo = $obj->getOrganById($organid);
        if($preInfo['auditstatus'] != 1){
            return return_format('',50095,lang('50095'));
        }
        if(empty($auditstatus)){
            return return_format([],50072,lang('50072'));           
        }
        $arr = [2,3];
        if(!in_array($auditstatus,$arr)){
            return return_format([],50095,lang('50095'));
        }
        if($auditstatus == 2){
            $refuseinfo = trim($refuseinfo);
            if(empty($refuseinfo) || (strlen($refuseinfo) > 300) ){
                return return_format([],50082,lang('50082'));
            }
        }

        //执行更改状态
        $res = $obj->updateOrganStatus($auditstatus,$organid);
        if($auditstatus == 2){
            //添加拒绝理由
            $refuse = new Refuseorgan();
            $ret = $refuse->addRefuseInfo($organid,$refuseinfo);

        }

        //如果这个机构是老师注册的 改老师状态  如果认证通过

        if($auditstatus == 3){
            if($preInfo['restype'] == 1){
                $mobj = new Adminmember();
                $mres = $mobj->getOrganUser($organid);
                $mobile = $mres['mobile'];

                $teacheInfo = [];
                $teacheInfo['mobile'] = $mobile;
                $teacheInfo['organid'] = $organid; 
                
                $teacheobj = new Teacherinfo();
                $teacheobj->updateTeacherInfo($teacheInfo);
            }

            //认证成功后自动购买免费套餐
            $obj = new Organ();
            //查看是不是待审的机构
            $nowOrganInfo = $obj->getOrganById($organid);
            if($nowOrganInfo['auditstatus'] == '3'){
                $billobj = new Organauditbillorder();
                $billobj->buyFreeBillOrder($organid);
            }
        }
        //发送机构审核成功或者失败的短信
        $result = $this->sendOrganAuditResMessage($organid,$auditstatus);
        //var_dump($result);

        //添加操作日志
        $obj = new Officialuseroperate();
        $obj->addOperateRecord('审核了机构'); 

        return $res;
    }

    public function sendOrganAuditResMessage($organid,$auditstatus=3){
        //获取当前机构id 对应的超级管理员的用户名  以及超级管理员的手机号
        $allAccount = new Allaccount();
        $res =  $allAccount->getOrganUserInfo($organid);
        $username = $res['username'];
        $mobile = $res['mobile'];
        $obj = new Messages();
        if($auditstatus == 2){
            $res = $obj->sendMeg($mobile,5,$params = [$username],'86','auditFail');
            //var_dump($res);
            if($res['result'] != 0){
                Log::write('审核机构失败发送验证码错误号:'.$res['result'].'发送验证码错误信息:'.$res['errmsg']);                
            }

        }elseif($auditstatus == 3){
            //var_dump($res);
            $res = $obj->sendMeg($mobile,6,$params = [$username],'86','auditSuccess');
            if($res['result'] != 0){
                Log::write('审核机构成功发送验证码错误号:'.$res['result'].'发送验证码错误信息:'.$res['errmsg']);                
            }
        }
        
        return $res;
    }

    
    /**
    *  setOrganOnOrOff  //设置已经通过后的机构启用或者禁用
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @param int $auditstatus 3表示当前启用 4表示当前禁用 
    * @return array 返回信息  ;
   */ 
    public function setOrganOnOrOff($organid,$auditstatus){
        //验证传参
        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }        
        $ret = [3,4];
        $organ = new Organ();
        $preInfo = $organ->getOrganById($organid);
        if(!in_array($preInfo['auditstatus'],$ret)){
            return return_format('',50095,lang('50095'));
        }
        if(!in_array($auditstatus,$ret)){
            return return_format('',50095,lang('50095'));
        }
        if($auditstatus == 3){
            $auditstatus = 4;
        }else{
            $auditstatus = 3;
        }
        //清除域名控制的缓存
        Cache::rm('domaincontrol-'.$preInfo['domain']);
        $organ = new Organ();
        $res = $organ->updateOrganStatus($auditstatus,$organid);
        return $res;        
    }


    /***************************官方后台*********************************/

    /***********************机构信息操作*********************************/



    /**公共方法
    *  getOrganAuditResById  //查看该机构的审核结果
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
	public function getOrganAuditResById($organid){
		$flag = $this->hasOrganById($organid);
		if($flag){
			return $flag;
		}
		$organ = new Organ();
		$rot = $organ->getOrganById($organid);
		//根据auditstatus查询refuseinfo
		$ret = [2,3,4];
		$auditstatus = $rot['auditstatus'];
		if(!in_array($auditstatus, $ret)){
			return return_format('',50095,lang('50095'));
		}
		$arr = [];
		$refuse = new Refuseorgan();
		$refuseinfo = $refuse->getRefuseOrganInfoById($organid);
		if($auditstatus == 2){
			$arr['auditinfo'] = "未通过";
			$arr['refuseinfo'] = $refuseinfo ? $refuseinfo : '' ;
		}elseif( ($auditstatus == 3) || ($auditstatus == 4)){
            //获取学堂的名称 域名 超级管理员用户名
            $allAccount = new Allaccount();
            $reg = $allAccount->getOrganUserInfo($organid);
            $arr['organname'] = $rot['organname'];
            $arr['domain'] = $rot['domain'];
            $arr['webDomain'] = "https://".$rot['domain'].".51menke.com";
            $arr['adminDomain'] = "https://".$rot['domain'].".51menke.com";
            $arr['adminname'] = $reg['username'];
            $arr['auditinfo'] = "已通过";
            //$arr['refuseinfo'] = '' ;
		}
		return return_format($arr,0,lang('success'));

	}

    /**公共方法
    *  getOrganBaseInfo  //获取机构的基本信息  以及 包括域名和企业名以及logo
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
	public function getOrganBaseInfo($organid){
		//校验机构id是否存在该机构

		//验证传参
		$flag = $this->hasOrganById($organid);
		if($flag){
			return $flag;
		}
		$data = [];
		//获取企业名和logo域名
		$organ = new Organ();
		$res = $organ->getOrganById($organid);
		if($res){
			$data['organname'] = $res['organname'];
            $data['imageurl'] = $res['imageurl'];
			$data['domain'] = $res['domain'];
		}else{
			$data['organname'] = '';
            $data['imageurl'] = '';             
			$data['domain'] = '';				
		}
		//获取其他的基本信息
		$organinfo = new Organinfo();
		$ret = $organinfo->getOrganInfoById($organid);
        if(!$ret){
            return return_format('',50023,lang('50023'));
        }
		//组装信息
		if($ret){
			$data['contactname'] = $ret['contactname'];
			$data['contactphone'] = $ret['contactphone'];
			$data['contactemail'] = $ret['contactemail'];
			$data['summary'] = $ret['summary'];
			$data['phone'] = $ret['phone'];
			$data['email'] = $ret['email'];
			$data['organid'] = $ret['organid'];
			$data['baseinfoid'] = $ret['organid'];
		}else{
			//表示没有记录
			$data['contactname'] = '';
			$data['contactphone'] = '';
			$data['contactemail'] = '';
			$data['summary'] = '';
			$data['phone'] = '';
			$data['email'] = '';
			$data['organid'] = '';			
			$data['baseinfoid'] = 0;  			
		}
		return return_format($data,0,lang('success'));

	}
	
    /**公共方法
    *  getOrganConfirmInfo  //修改机构的认证信息 分个人或者企业
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
	public function getOrganConfirmInfo($organid){
		//验证传参
		$flag = $this->hasOrganById($organid);
		if($flag){
			return $flag;
		}
		$organconfirm = new Organconfirm();
		$data = $organconfirm->getOrganConfirmInfoById($organid);
		return $data;

	}

    /**公共方法
    *  hasOrganById  //判断该机构是否存在
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
    public function hasOrganById($organid){
        if(empty($organid)){
            return return_format('',50071,lang('50071'));
        }
        //校验机构id是否存在该机构
        $organ = new Organ();
        $res = $organ->getOrganById($organid);
        if(!$res){
            return return_format('',50006,lang('50006'));
        }
        return false;       
    }


    /**
    *  getOrganIntroduceInfo  //官网机构审核中的介绍机构的信息
    * @ zzq  2018-05-03
    * @param array $data 信息 
    * @return array 返回信息  ;
   */ 
    public function getOrganIntroduceInfo($organid){
        if(empty($organid)){
            return return_format('',50071,lang('50071'));
        }

        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }
        $data = [];
        $organ = new Organ();
        $resInfo = $organ->getOrganById($organid);
        if($resInfo){
            if($resInfo['organname']){
                $data['organname'] = $resInfo['organname'];
            }else{
                $data['organname'] = '';
            }
            if($resInfo['imageurl']){
                $data['imageurl'] = $resInfo['imageurl'];
            }else{
                $data['imageurl'] = '';
            }
        }else{
            //暂无该机构的信息
            return return_format('',50025,lang('50025'));
        }

        $organInfo = new Organinfo();

        $resBaseInfo = $organInfo->getOrganInfoById($organid);
        if($resBaseInfo){
            if($resBaseInfo['summary']){
                $data['summary'] = $resBaseInfo['summary'];
            }else{
                $data['summary'] = '';
            }
        }else{
            return return_format('',50023,lang('50023'));
        }

        return return_format($data,0,lang('success'));


    }

    /**
    *  getOrganRefuseInfo  //查看该机构的被拒绝的原因(最新的)
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @param int $type 0表示首次填写信息  1表示从审核拒绝结果跳转过来 
    * @return array 返回信息  ;
   */ 
    public function getOrganRefuseInfo($organid,$type=0){
        if(empty($organid)){
            return return_format('',50071,lang('50071'));
        }
        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }
        $organ = new Organ();
        $rot = $organ->getOrganById($organid);
        $ret = [0,1,2];
        $auditstatus = $rot['auditstatus'];
        if(!in_array($auditstatus, $ret)){
            return return_format('',50095,lang('50095'));
        }
        $refuse = new Refuseorgan();
        $refuseinfo = $refuse->getRefuseOrganInfoById($organid);
        $data = [];
        if($type == 0){
            $data['refuseinfo'] = '';
            return return_format($data,0,lang('success'));
        }else{
            $data['refuseinfo'] = $refuseinfo;
            return return_format($data,0,lang('success'));
        }
        
    }

    /**
    *  FromRefusedToUnAudited  //设置被拒绝后的机构返回未认证的状态值
    * @ zzq  2018-05-03
    * @param int $organid 组织机构id 
    * @return array 返回信息  ;
   */ 
    public function FromRefusedToUnAudited($organid){
        //验证传参
        $flag = $this->hasOrganById($organid);
        if($flag){
            return $flag;
        }
        $organ = new Organ();
        $ret = $organ -> getOrganById($organid);
        if($ret['auditstatus'] != 2 ){
            return return_format('',50095,lang('50095'));
        }

        $organ = new Organ();
        $res = $organ->updateOrganStatus(0,$organid);
        return $res;        
    }

    /***********************机构信息操作*********************************/

    /**
     * [copyFromOldOrganToNewOrgan //批准机构申请vip,生成新机构]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param $oldOrganid int 原有的免费的机构的id 
     * @return array 返回生成新的vip机构的机构主键id
     */
    public function copyFromOldOrganToNewOrgan($oldOrganid){
        
        //1判断参数是否为空
        if(empty($oldOrganid)){
            return return_format('',50071,lang('50071'));
        }
        //2判断当前这个免费机构是否存在,auditstatus = 3或4,并且appvip = 1
        $organ = new Organ();
        $organinfo = $organ->getOrganById($oldOrganid);
        if(!$organinfo){
            return return_format('',50039,lang('50039'));
        }
        $ret = [3,4];
        if(!in_array($organinfo['auditstatus'],$ret)){
            return return_format('',50041,lang('50041'));
        }
        if($organinfo['applyvip']  == 0 ){
            return return_format('',50042,lang('50042'));
        }
        if($organinfo['applyvip']  == 2 ){
            return return_format('',50089,lang('50089'));
        }
        //判断当前机构的注册类型
		
        //3获取当前的nm_organ表的信息，并进行修改->newOrganData 
        $newOrganData = [];
        $newOrganData = $organinfo;
        $newOrganData['vip'] = 1;
        $newOrganData['applyvip'] = 0;
        $newOrganData['applyviptime'] = 0;
        unset($newOrganData['id']);


        //4获取旧的nm_allaccount->newAllccountData
        $allaccount = new Allaccount();
        $allaccountinfo = $allaccount->getOrganUserInfo($oldOrganid);
        if(!$allaccountinfo){
            return return_format('',50043,lang('50043'));
        }
        $newAllccountData = [];
        $newAllccountData = $allaccountinfo;
        unset($newAllccountData['id']);

        //5获取旧的nm_adminmember->newAdminmemberData
        $adminmember = new Adminmember();
        $adminmemberinfo = $adminmember->getOrganUser($oldOrganid);
        if(!$adminmemberinfo){
            return return_format('',50044,lang('50044'));
        }
        $newAdminmemberData = [];
        $newAdminmemberData = $adminmemberinfo;
        unset($newAdminmemberData['id']);

        //6获取旧的nm_organbaseinfo->newOrganBaseData
        $baseobj = new Organinfo();
        $baseinfo = $baseobj->getOrganInfoById($oldOrganid);
        if(!$baseinfo){
            return return_format('',50023,lang('50023'));
        }
        $newOrganBaseData = [];
        $newOrganBaseData = $baseinfo;
        unset($newOrganBaseData['id']);

        //6获取旧的nm_organauthinfo->newOrganAuthData
        $authobj = new Organconfirm();
        $authinfo = $authobj->getOrganAuthInfoById($oldOrganid);
        
        if(!$authinfo){
            return return_format('',50024,lang('50024'));
        }
        $newOrganAuthData = [];
        $newOrganAuthData = $authinfo;
        unset($newOrganAuthData['id']);


        $imgobj = new Organslideimg();
        $imgs = $imgobj->getOrganSlideImgList();

        if($imgs['data']){
            $count = count($imgs['data']);
        }else{
            return return_format('',50046,lang('50046'));
        }

        //否则导入图片
        $data = $imgs['data'];
        $newOrganImgData = [];
        foreach($data as $k => $v){
            $newOrganImgData[$k]['remark'] = $v['remark']; 
            $newOrganImgData[$k]['imagepath'] = $v['imagepath']; 
            $newOrganImgData[$k]['sortid'] = $v['sortid'];
            $newOrganImgData[$k]['addtime'] = time(); 
        }

        // var_dump($newOrganData);
        // var_dump($newAllccountData);
        // var_dump($newAdminmemberData);
        // var_dump($newOrganBaseData);
        // var_dump($newOrganAuthData);
        // die;
        $obj = new Organ();
        $res = $obj->copyFromOldOrganToNewOrgan($oldOrganid,$newOrganData,$newAllccountData,$newAdminmemberData,$newOrganBaseData,$newOrganAuthData,$newOrganImgData);
        return $res;    
    }

    public function updateOrganToHasVip($oldOrganid){
        //原机构的申请vip状态结束 appvip = 2
        $obj = new Organ();
        $res = $obj->updateOrganToHasVip($oldOrganid);
        return $res;
    }

    /**
     * [addRedisCopyList //将要复制机构id投入redis list队列]
     * @Author zzq
     * @DateTime 2018-05-26   s
     * @param $organid int 原有的免费的机构的id 
     * @return array 
     */
    public function addRedisCopyList($organid){
        if(!isset($organid)){
            return return_format('',50071,lang('50071'));
        }
        //判断是否已经复制过数据
        $redis = new \Redis();
        $link = $redis->connect('127.0.0.1',6379);

        $arr = $redis->lrange('copyOrganList','0','-1');
        $ret = in_array($organid, $arr);
        // var_dump($ret);
        // die;
        if($ret){
            //存在表示已经在复制队列里了
            return return_format('',50050,lang('50050'));
        }

        $organ = new Organ();
        $res = $organ->getOrganById($organid);
        // var_dump($res);
        // die;
        if( ($res['applyvip'] == 2) && ($res['vip'] == 1) ){
            return return_format('',50050,lang('50050'));
        }

        if(!$link){
            return return_format('',50048,lang('50048'));
        }
        //加入队列
        $flag = $redis->rpush('copyOrganList',$organid);
        if($flag){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',50049,lang('50049'));
        }
    }

    /**
     * [timeTaskCopy //定时任务执行复制机构的数据]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param [] 
     * @return array 
     */
    public function timeTaskCopy(){

        $redis = new \Redis();
        $link = $redis->connect('127.0.0.1',6379);
        //出队列
        $oldOrganid =  $redis->lpop('copyOrganList');
        if($oldOrganid){
            //复制机构基本信息
            $res = $this->copyFromOldOrganToNewOrgan($oldOrganid);
            //复制机构的课程信息
            $newOrganid = $res['data']['newOrganid'];
            $timingTask  =  new TimingTask();
            $timingTask->copyOrgan($oldOrganid,$newOrganid);            
        }


          
    }

    /**
    *  getAuditInfo //返回当前的机构审核状态
    * @ zzq  2018-05-03
    * @param int $auditstatus 组织机构审核状态
    * @return array 返回信息  ;
   */ 
    public function getAuditInfo($auditstatus){
        $ret = [-1,0,1,2,3,4];
        if(!in_array($auditstatus, $ret)){
            return  false;
        }
        $str = '';
        switch ($auditstatus) {
            case -1:
                $str = "未填写域名和机构名称";
                break;
            case 0:
                $str = "未认证";
                break;
            case 1:
                $str = "待审核";
                break;
            case 2:
                $str = "已被拒绝";
                break;
            case 3:
                $str = "通过审核并且已启用";
                break;
            case 4:
                $str = "通过审核并且已禁用";
                break;              
            default:
                $str = "";
                break;
        }
        return $str;
    }

    //产生随机的手机验证码
    public function getMobileCode(){
        $str = '';
        $arr = [0,1,2,3,4,5,6,7,8,9];
        $length = count($arr);
        for($i = 0;$i < 6;$i++){
            $str = $str.array_rand($arr);
        }
        return $str;
    }

    /**
     * //检测手机号
     * @Author zzq
     * @param $mobile   int  [手机号]
     * @return bool  [返回信息]
     * 
     */
    public function checkMobile($mobile){
        
        $pattern = "/^[1][0-9]{10}$/";
        if (preg_match($pattern, $mobile)) {
            return true;
        }
        return false;   
    }

    /**
     * //检测邮箱
     * @Author zzq
     * @param $email   int  [邮箱号]
     * @return bool  [返回信息]
     * 
     */
    public function checkEmail($email){

        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
          return false; 
        }
        return true;
    }

    /**
     * //过滤搜索字符串
     * @Author zzq
     * @param $str   string  [搜索字符串]
     * @return string  [返回信息]
     * 
     */
    public function filterKeywords($str){
        $str = strip_tags($str);   //过滤html标签
        $str = htmlspecialchars($str);   //将字符内容转化为html实体
        $str = addslashes($str);

        return $str;
    }


}



?>