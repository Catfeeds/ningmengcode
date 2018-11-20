<?php
namespace app\index\model;
use think\Db;

class Login
{
    // 分类添加验证规则
	public $rule = [
		'username' => 'require',
		//'password' => 'require',
		'type' => 'require',
		'source' => 'require',
		'key' => 'require',
	];
	public $message = [];

	//自定义初始化
	protected function initialize(){
		parent::initialize();
		$this->message = [
			'username.require' => lang('10531'),
			//'password.require' => lang('10532'),
			'type.require'	   => lang('10533'),
			'source.require'   => lang('10533'),
			'key.require'	   => lang('10533'),
		];
	}


	/**
	 * [studentLogin 学生登录]
	 * @param  [type] $where [登录条件]
	 * @
	 * @return [type]           [description]
	 */
	public function studentLogin($where,$typestatus = false,$field = 'id as uid,nickname,prphone,mobile,addtime,status,imageurl'){
		if($typestatus){
			Db::table('nm_studentinfo')->where($where)->update(['logintime'=>time()]);
		}
		return Db::table('nm_studentinfo')->where($where)->field($field)->find();
	}
	
	/**
	 * [hjxstudentLogin 好迹星学生登录]
	 * @param  [type] $where [登录条件]
	 * @
	 * @return [type]           [description]
	 */
	public function hjxstudentLogin($where,$typestatus = false,$field = 'id as uid,nickname,prphone,mobile,addtime,status,imageurl'){
		if($typestatus){
			Db::table('nm_hjxappstudentinfo')->where($where)->update(['logintime'=>time()]);
		}
		return Db::table('nm_hjxappstudentinfo')->where($where)->field($field)->find();
	}


	/**
	 * [studentLogin 学生登录]
	 * @param  [type] $where [登录条件]
	 * @return [type]           [description]
	 */
	public function officialuserLogin($where,$status,$field = 'id as uid,realname as nickname,usertype as type,mobile,status,addtime,lastlogintime,logintime'){
		$info = Db::table('nm_officialuser')->where($where)->field($field)->find();
		if($status){
			Db::table('nm_officialuser')->where($where)->update(['lastlogintime'=>$info['logintime'],'logintime'=>time()]);
		}
		return $info;
	}

	/**
	 * [teacherLogin 老师登录]
	 * @param  [type] $where    [登录条件]
	 * @return [type]           [description]
	 */
	public function teacherLogin($where){
		return Db::table('nm_allaccount')->where($where)->field('password,uid,mix,status,usertype')->find();
	}

	/**
	 * [teacherLoginFree 线上收费机构登陆]
	 * @param $where
	 */
//	public function teacherLoginFree($where){
//		$info = Db::table('nm_allaccount')->alias('a')
//									->join('nm_organ o','a.organid = o.id')
//									->where($where)
//									->field('a.password,a.uid,a.mix,a.status,a.usertype,a.organid')
//									->find();
//
////		var_dump(Db::getlastsql());
//
//		return $info;
//
//	}

	/**
	 * [getTeacher 获取老师的详细信息]
	 * @param  [type] $teacherid [description]
	 * @return [type]            [description]
	 */
	public function getTeacher($teacherid){
		$field = 'teacherid as uid,imageurl,prphone,mobile,teachername,nickname,accountstatus';
		return Db::table('nm_teacherinfo')->where(['teacherid'=>$teacherid])->field($field)->find();
	}

	/**
	 * [getOrganInfo 获取机构的详细信息]
	 * @param  [type] $organid [description]
	 * @return [type]          [description]
	 */
	public function getOrganInfo($organid,$uid,$usertype=2){
		$field = 'id,organname,imageurl';
		$userinfo = Db::table('nm_organ')->where(['id'=>$organid])->field($field)->find();
		
		$logintime = Db::table('nm_adminmember')->where(['id'=>$uid])->field('logintime,userimage,adminname,useraccount,mobile,groupids')->find() ;
                     Db::table('nm_adminmember')->where(['id'=>$uid])->update(['logintime'=>time()]) ;

		$userinfo['adminname'] = $logintime['adminname'];
		$userinfo['useraccount'] = $logintime['useraccount'];
		$userinfo['userimage'] = $logintime['userimage'];
		$userinfo['mobile'] = $logintime['mobile'];
		$userinfo['groupids'] = $logintime['groupids'];
		$userinfo['logintime'] = $logintime?date('Y-m-d H:i:s',$logintime['logintime']):'-';

		return $userinfo;
	}




	/**
     * [getUserOrgan 根据用户的访问获取用户的机构标记]
     * @Author wyx
     * @DateTime 2018-04-27T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function getUserOrgan($urlhost = false){
        $hostname = $urlhost?$urlhost:$_SERVER['HTTP_HOST'] ;
        $arr = explode('.', $hostname) ;
        //严格校验域名必须三段
        $organstr = $arr[0] ;
        $organmsg = Db::table('nm_organ')->field('id,organname,profile')->where(['domain'=>$organstr])->find() ;
        return $organmsg?$organmsg['id']:0;
    }


	/**
	 * [getUserOrgan 根据用户的访问获取用户的机构标记]
	 * @Author wyx
	 * @DateTime 2018-04-27T19:29:24+0800
	 * @return   [type]                   [description]
	 */
	public function getUserOrganinfo($urlhost = false){
		$hostname = $urlhost?$urlhost:$_SERVER['HTTP_HOST'] ;
		$arr = explode('.', $hostname) ;
		//严格校验域名必须三段
		$organstr = $arr[0] ;
		$organmsg = Db::table('nm_organ')->field('id,organname,profile,vip')->where(['domain'=>$organstr])->find() ;
		return $organmsg;
	}




	/**
	 * [getUserUrl 登陆后获取对应的roleid]
	 * @param $usertype
	 * @param $uid
	 * @param $controllername
	 * @param $action
	 */
	public function getUserUrl($usertype,$uid){
		$roidlist = Db::table('nm_accessroleuser')->where(['uid'=>$uid,'usertype'=>$usertype])
			->field('roleid')->select();
		if($roidlist){
			return array_column($roidlist,'roleid');
		}else{
			return [];
		}
	}

}
