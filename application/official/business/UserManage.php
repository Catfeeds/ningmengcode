<?php
namespace app\official\business;
use app\official\model\Officialuser;
use app\official\model\Officialuseroperate;
use login\Authorize;
use think\Session;
class UserManage{
	

	/**
	 * //获取管理员列表
	 * @Author zzq
	 * @param  无参数  []
	 * @return array  [返回信息]
	 * 
	 */
	public function getOfficialUserList($data){
		$data['username'] = $this->filterKeywords($data['username']);
		$where = [];
        if(!empty($data['username'])){
        	$username = $data['username'];
        	//模糊查询
            $where['username']  = ['like',"%".$username."%"];
        }
		$obj = new Officialuser();
		$res = $obj->getOfficialUserList($where,$data['orderbys'],$data['pagenum'],$data['pernum']);
		return $res;
	}

	
	/**
	 * ///添加后台管理员
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function addOfficialUser($data){

		if( empty($data['username']) || empty($data['realname']) || empty($data['password']) || empty($data['repassword']) || empty($data['mobile']) ){
			return return_format('',50000,lang('50000')) ;
		}
		if( ctype_space($data['username']) || ctype_space($data['realname']) || ctype_space($data['password']) || ctype_space($data['repassword']) || ctype_space($data['mobile']) || ctype_space($data['info']) ){
			return return_format('',50000,lang('50000')) ;
		}
        //检测密码
        $passFlag = verifyPassword($data['password']);
        if(!$passFlag){
        	return return_format('',50068,lang('50068'));
        }
        $repassFlag = verifyPassword($data['repassword']);
        if(!$repassFlag){
        	return return_format('',50068,lang('50068'));
        }
		//验证手机号
		if(!$this->checkMobile($data['mobile'])){
			return return_format('',50021,lang('50021')) ;
		}		
		//验证两次密码相同
		if($data['password'] != $data['repassword']){
			return return_format('',50033,lang('50033')) ;
		}
        $obj = new Officialuser();
        $res = $obj->addOfficialUser($data);
        return $res;		
	}

	
	/**
	 * //查看用户详情
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOfficialUserById($id){

		if(!$this->isPositiveInteger($id)){
			return return_format('',50003,lang('50003')) ;
		}
		$obj = new Officialuser();
		$res = $obj->getOfficialUserById($id);
		return $res;
	}


	
	/**
	 * //编辑用户
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function editOfficialUser($data){

		if( empty($data['username']) || empty($data['realname'])  || empty($data['mobile']) ){
			return return_format('',50000,lang('50000')) ;
		}
		if( ctype_space($data['username']) || ctype_space($data['realname']) || ctype_space($data['password']) || ctype_space($data['repassword']) || ctype_space($data['mobile']) || ctype_space($data['info']) ){
			return return_format('',50000,lang('50000')) ;
		}
        //检测密码
        if(!empty($data['password'])){
	        $passFlag = verifyPassword($data['password']);
	        if(!$passFlag){
	        	return return_format('',50068,lang('50068'));
	        }        	
        }
        if(!empty($data['repassword'])){
	        $repassFlag = verifyPassword($data['repassword']);
	        if(!$repassFlag){
	        	return return_format('',50068,lang('50068'));
	        }        	
        }
		//验证手机号
		if(!$this->checkMobile($data['mobile'])){
			return return_format('',50021,lang('50021')) ;
		}		
		//验证两次密码相同
		if($data['password'] != $data['repassword']){
			return return_format('',50033,lang('50033')) ;
		}
		$obj = new Officialuser();
		$res = $obj->editOfficialUser($data);
		return $res;
	}

	/**
	 * //删除管理员
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOfficialUser($id){
		if(!$this->isPositiveInteger($id)){
			return return_format('',50003,lang('50003')) ;
		}
		$obj = new Officialuser();
		$res = $obj->delOfficialUser($id);
		return $res;
	}

	/**
	 * //删除管理员
	 * @Author zzq
	 * @param $id   int  [管理员id]
	 * @return array  [返回信息]
	 * 
	 */
	public function setOfficialUserOnOrOff($id,$status){
		if(!$this->isPositiveInteger($id)){
			return return_format('',50003,lang('50003')) ;
		}
		$ret = [0,1];
		if(!in_array($status,$ret)){
			return return_format('',50002,lang('50002'));
		}
		$obj = new Officialuser();
		$res = $obj->setOfficialUserOnOrOff($id,$status);
		return $res;
	}

	/**
	 * //获取管理员列表
	 * @Author zzq
	 * @param  无参数  []
	 * @return array  [返回信息]
	 * 
	 */
	public function getUserOperateRecordList($data){
		$where = [];
        if(!empty($data['username'])){
        	$username = $data['username'];
        	//模糊查询
            $where['username']  = ['like',"%".$username."%"];
        }
        if(!empty($data['date'])){
        	//获取当天的时间戳
        	//比如"20170808"
        	$nowtime = strtotime($data['date']);
        	$nexttime = $nowtime + 3600*24;
        	//模糊查询
            $where['addtime']  = ['between',[$nowtime,$nexttime]];
            //$where['addtime']  = ['LT',$nexttime];
        }

		$obj = new Officialuseroperate();
		$res = $obj->getUserOperateRecordList($where,$data['orderbys'],$data['pagenum'],$data['pernum']);
		return $res;
	}

	//登录的校验
	/**
	 * //登录的校验
	 * @Author zzq
	 * @param  array  $data[]
	 * @return array  [返回信息]
	 * 
	 */
	public function checkUserAndPass($data){

		if(empty($data['username'])){
			 return return_format('',50000,lang('50000')) ;
			
		}

		$ouid = session('ouid', '', 'official');
		/*if($ouid){
			//表示已经登录
			$data = ['ouid'=>$ouid];
			return return_format($data,0,'您已经登录了') ;
		}*/
		$obj = new Officialuser();
		$res = $obj->checkUserAndPass($data['username'],$data['password']);
		//获取上次的登录时间
		$prevLogintime = $res['logintime'];
		if($res){
			$currenttime = time();
			$ouid = $res['id'];
			$ousername = $res['username'];
			$ret = [];
			$ret['ouid'] = $ouid;
			$ret['ousername'] = $ousername;
			$ret['logintime'] = $currenttime;
			$ret['lastlogintime'] = $prevLogintime;

			session('ouid', $ouid , 'official');
			session('ousername', $ousername , 'official');
			$arr = ['logintime'=>$currenttime,'lastlogintime'=>$prevLogintime];


			$obj->changeOfficialUserField($arr,$ouid);
			return return_format($ret,0,lang('success')) ;
		}else{
			return return_format('',50031,lang('50031')) ;
		}


	}

	public function delUserOperateRecord($ids){

		$obj = new Officialuseroperate();
		$res = $obj->delUserOperateRecord($ids);
		return $res;
	}
	
	/**
	 * //退出的业务逻辑
	 * @Author zzq
	 * @param  无参数 []
	 * @return array  [返回信息]
	 * 
	 */
	public function doLogout(){
		
		Session::delete('ouid','official');
		Session::delete('ousername','official');
		return return_format('',0,lang('success')) ; 
	}

	public function getOfficialUserLoginInfo($id){
		if(empty($id)){
			return return_format('',50003,lang('50003')) ;
		}
		$obj = new Officialuser();
		$res = $obj->getOfficialUserById($id);
		$ret = $res['data'];
		$data = [];
		$data['realname'] = $ret['realname'];
		$data['logintime'] = Date('Y-m-d H:i:s',$ret['logintime']);
		$data['lastlogintime'] = Date('Y-m-d H:i:s',$ret['lastlogintime']);
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		return return_format($data,0,lang('success')) ;
	}


	/**
	 * //判断是否是非负整数
	 * @Author zzq
	 * @param $value   int  [广告id]
	 * @return bool  [返回信息]
	 * 
	 */
    protected function isPositiveInteger($value)
    {
    	$pattern = "/^\d+$/";
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
        
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