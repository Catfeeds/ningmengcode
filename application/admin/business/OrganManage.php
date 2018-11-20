<?php
/**
 * 机构端教师管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\Accessusergroup;
use app\admin\model\Adminmember;
use app\admin\model\Allaccount;
use app\admin\model\Classroom;
use app\admin\model\Coursepackageorder;
use app\admin\model\Curriculum;
use app\admin\model\Ordermanage;
use app\admin\model\Organ;
use app\admin\model\Organaccount;
use app\admin\model\Organconfirm;
use app\admin\model\Organinfo;
use app\admin\model\Payaccount;
use app\admin\model\Refuseorgan;
use app\admin\model\Scheduling;
use app\admin\model\Studentinfo;
use app\admin\model\Studentpaylog;
use app\admin\model\Teacherinfo;
use app\admin\model\Toteachtime;
use app\index\business\UserLogin;
use Calendar;
use Messages;
use think\Cache;
use think\Log;
use think\Session;
use Verifyhelper;

class OrganManage {
	protected $wxnotifyurl;
	protected $alinotifyurl;
	/**
	 *
	 */
	public function __construct() {
		$this->wxnotifyurl = $_SERVER['HTTP_HOST'] . '/admin/ServerNotice/wxMealNotify';
		$this->alinotifyurl = $_SERVER['HTTP_HOST'] . '/admin/ServerNotice/aliMealNotify';
	}

	/**
	 * [getOrganmsgById 通过机构id获取机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:27:51+0800
	 * @param    [int]             $organid [机构登陆后存入机构id]
	 * @return   [array]                    [机构信息]
	 */
	public function getOrganmsgById() {
		$techmodel = new Organ;
		$msg = $techmodel->getOrganmsgById();
		if (empty($msg)) {
			return return_format([], 40022);
		} else {
			return return_format($msg, 0);
		}
	}
	/**
	 * [sendMessage 更改用户的密码或更换手机号 发短信验证]
	 * @Author wyx
	 * @DateTime 2018-05-21
	 * @param    [int]            $organid [要更新的机构id]
	 * @param    [int]            $token   [用户登录标记]
	 * @return   [int]                     [更新结果]
	 */
	public function sendMessage($prephone, $mobile, $token) {
		if (empty($mobile)) {
			return return_format('', 40012);
		}

		if (empty($prephone)) {
			$prephone = '86';
		}
		// 如果为空默认 中国 使用 86
		//生成验证码
		$mark = '';
		for ($i = 0; $i < 6; $i++) {
			$mark .= mt_rand(0, 9);
		}
		//设置发短信需要的参数
		$elapse = 10; // min
		$type = 4; // 4 短信验证码 7.密码找回
		$params = [
			$mark,
			$elapse,
		];
		//将信息存入session 以便用户提交后验证
		Cache::set('mobile' . $token, $mobile, 600);
		Cache::set('starttime' . $token, time(), 600);
		Cache::set('mark' . $token, $mark, 600);
		Cache::set('times' . $token, 0, 600);
		// session('phone', $mobile, 'updatemessage');
		// session('starttime', time(), 'updatemessage');
		// session('mark', $mark, 'updatemessage');
		// session('times', 0, 'updatemessage'); //比对次数
		//调用发短信接口
		Cache::rm('mobile' . $mobile);
		$sendmsg = new \Messages;
		$sendret = $sendmsg->sendMeg($mobile, $type, $params, $prephone);
		if (isset($sendret['errmsg']) && $sendret['errmsg'] == 'OK') {
			return return_format($sendret, 0);
		} else {
			return return_format('', 40013);
		}

	}
	/**
	 * [updateUserMsg 更新机构信息]
	 * @Author wyx
	 * @DateTime 2018-05-22T12:01:38+0800
	 * @param    [string]         $userimg   [需要更新的机构数据]
	 * @param    [string]         $username  [用户昵称名字]
	 * @return   [int]            $organid   [机构id]
	 * @return   [int]            $uid       [登陆用户的id]
	 */
	public function updateUserMsg($userimg, $username, $uid) {
		if ($uid > 0 && (!empty($userimg) || !empty($username))) {

			$techmodel = new Adminmember;
			$ret = $techmodel->updateUserMsg($userimg, $username, $uid);
			if ($ret > 0) {
				return return_format('', 0);
			} else {
				return return_format('', 40010);
			}
		} else {
			return return_format('', 40011);
			// return return_format('',40020,'头像和用户名不能都为空，或者没有登陆');
		}

	}
	/**
	 * [updatePass 更新机构信息]
	 * @Author wyx
	 * @DateTime 2018-05-22T12:01:38+0800
	 * @param    [string]         $mark   [验证码]
	 * @param    [string]         $pass   [密码]
	 * @return   [string]         $repass [重复密码]
	 * @return   [string]         $uid    [用户uid]
	 * @return   [string]         $token  [用户识别码]
	 * @return   [int]            $organid   [机构id]
	 */
	public function updatePass($mark, $pass, $repass, $uid, $token) {

		$originmark = Cache::get('mark' . $token);
		$origintime = Cache::get('starttime' . $token); //十分钟内有效
		$times = Cache::get('times' . $token);
		// $originmark = session('mark', '', 'updatemessage');
		// $origintime = session('starttime', '', 'updatemessage'); //十分钟内有效
		// $times = session('times', '', 'updatemessage');
		if ($times > 5 || time() - 600 > $origintime) {
			// 次数超限 或者 过期
			return return_format('', 40014);
		}
		//验证码
		if (!empty($mark) && $mark == $originmark) {
			if ($pass == $repass && !empty($pass)) {
				// 密码相同且不为空
				// $uid = session('adminid'); //获取登录用户的uid

				$techmodel = new Allaccount;
				$ret = $techmodel->updatePass($pass, $uid);

				if ($ret > 0) {
					return return_format('', 0);
				} else {
					return return_format('', 40015);
				}
			} else {
				return return_format('', 40016);
			}
		} else {
			Cache::inc('times' . $token); // 对比次数加
			return return_format('', 40017, '短信验证码输入有误');
		}
	}
	/**
	 * [changeMobile 更换手机号]
	 * @Author wyx
	 * @DateTime 2018-05-22T12:01:38+0800
	 * @param    [string]         $mark   [验证码]
	 * @param    [string]         $oldphone [旧手机号]
	 * @return   [string]         $newphone [新手机号]
	 * @return   [string]         $uid    [当前登陆用户id]
	 * @return   [string]         $token  [用户身份标识]
	 * @return   [int]            $organid   [机构id]
	 */
	public function changeMobile($mark, $oldphone, $newphone, $uid, $token) {

		// $originmark = session('mark', '', 'updatemessage');
		// $origintime = session('starttime', '', 'updatemessage'); //十分钟内有效
		// $times = session('times', '', 'updatemessage');
		$originmark = Cache::get('mark' . $token);
		$origintime = Cache::get('starttime' . $token); //十分钟内有效
		$times = Cache::get('times' . $token);
		if ($times > 5 || time() - 600 > $origintime) {
			// 次数超限 或者 过期
			return return_format('', 40018);
		}
		//验证码

		if (!empty($mark) && $mark == $originmark) {
			// $uid = session('adminid'); //获取登录用户的uid

			$techmodel = new Allaccount;
			$ret = $techmodel->changeMobile($uid, $oldphone, $newphone);

			return $ret;
		} else {
			Cache::inc('times' . $token); // 对比次数加
			return return_format('', 40019);
		}
	}
	/**
	 * [updateOrganMsg 更新机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T12:01:38+0800
	 * @param    [array]          $data    [需要更新的机构数据]
	 * @param    [int]            $organid [要更新的机构id]
	 * @return   [int]                     [更新结果]
	 */
	public function updateOrganMsg($data) {
		$data['organname'] = 'uarl';
		$data['profile'] = '123123123';
		$data['info'] = '12';
		$data['imageurl'] = '34';
		$data['hotline'] = 0;
		$data['email'] = 23;

		$allowfield = ['organname', 'profile', 'info', 'imageurl', 'hotline', 'email'];
		//过滤 多余的字段
		$newdata = where_filter($data, $allowfield);

		$techmodel = new Organ;
		return $techmodel->updateOrganMsg($data);

	}
	/**
	 * [getPayMethods 获取机构的收款方式]
	 * @Author wyx
	 * @DateTime 2018-04-23T13:46:09+0800
	 * @param    [int]          $organid [机构id]
	 * @return   [array]                  [查询结果]
	 */
	public function getPayMethods() {

		$paymodel = new Payaccount;
		$msg = $paymodel->getPaymsgById();
		if (empty($msg)) {
			return return_format([], 40025);
		} else {
			return return_format($msg, 0);

		}

	}
	/**
	 * [updatePayMethod 更新机构的付款方式]
	 * @Author wyx
	 * @DateTime 2018-04-23T15:19:15+0800
	 * @param    [array]                   $data  [需要更新的数据]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [返回更新标记]
	 */
	public function updatePayMethod($data) {

		// $data['bankname'] =  'uarl' ;
		//       $data['branchname'] =  '123123123' ;
		//       $data['cardid'] =  '12' ;
		//       $data['cardholder'] =  '34' ;
		//       $data['wechatmark'] =  0 ;
		//       $data['accountpayee'] = 23  ;
		//       $data['namepayee'] = 23  ;

		$allowfield = ['bankname', 'branchname', 'cardid', 'cardholder', 'wechatmark', 'accountpayee', 'namepayee'];
		//过滤 多余的字段
		$newdata = where_filter($data, $allowfield);

		$techmodel = new Payaccount;
		return $techmodel->updatePayMsg($data);
	}
	/**
	 * 获取课堂配置
	 * @Author wyx
	 * @DateTime 2018-04-23T15:15:18+0800
	 * @return   [array]              [更新结果的状态]
	 *
	 */
	public function getOrganConfig() {
		$organobj = new Organ;
		//获取
		$orgaininfo = $organobj->getOrganmsgById();
		if (empty($orgaininfo)) {
			return return_format($orgaininfo, 40028);
		} else {
			return return_format($orgaininfo, 0);
		}
	}
	/**
	 * 设置课堂配置
	 * @Author wyx
	 * @DateTime 2018-04-23T15:15:18+0800
	 * @return   [array]              [更新结果的状态]
	 *
	 */
//	public function setOrganConfig($organid,$data){
	//        if(!isset($data['toonetime']) || !isset($data['smallclasstime']) || !isset($data['bigclasstime']) || !isset($data['maxclass']) || !isset($data['minclass']) || empty($organid) ){
	//            return return_format('',50000,lang('50000'));
	//        }
	//		$organobj  = new OrganConf;
	//		$res = $organobj->setOrganConfig($organid,$data);
	//		return $res;
	//	}
	/**
	 * [getAdminList 根据机构id 获取机构添加的管理员]
	 * @Author wyx
	 * @DateTime 2018-04-23T20:10:12+0800
	 * @param    [string]                $username [机构管理员名字]
	 * @param    [int]                   $pagenum  [页码]
	 * @param    [int]                   $organid  [机构id]
	 * @param    [int]                   $limit    [每页显示条数]
	 * @return   [type]                            [description]
	 */
	public function getAdminList($username, $pagenum, $limit) {

		$where = [];
		!empty($username) && $where['aa.username'] = ['like', $username . '%'];
		if ($pagenum > 0) {
			$start = ($pagenum - 1) * $limit;
			$limitstr = $start . ',' . $limit;
		} else {
			$start = 0;
			$limitstr = $start . ',' . $limit;
		}
		$where['aa.usertype'] = 2; // 机构添加的管理员

		$accountmodel = new Allaccount;

		$adminarr = $accountmodel->getAdminList($where, $limitstr);
		$usergroup = new Accessusergroup();
		$grouplist = $usergroup->getList(['delflag' => 0, 'id' => array('gt', 1)], 1, 1000);
		$grouplist = $grouplist ? array_column($grouplist, 'name', 'id') : [];

		foreach ($adminarr as &$val) {
			$val['groupstr'] = isset($grouplist[$val['groupids']]) ? $grouplist[$val['groupids']] : '-';
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			$val['logintime'] = date('Y-m-d H:i:s', $val['logintime']);
		}
		if (!empty($adminarr)) {
			//获取 分页的总记录数
			$total = $accountmodel->getAdminListCount($where);
			$result = [
				'data' => $adminarr, // 内容结果集
				'pageinfo' => [
					'pagesize' => $limit, // 每页多少条记录
					'pagenum' => $pagenum, //当前页码
					'total' => $total, // 符合条件总的记录数
				],
			];
			return return_format($result, 0);
		} else {
			return return_format($adminarr, 0);
		}

	}
	/**
	 * [getAdminUser 获取单个添加的管理员信息]
	 * @Author wyx
	 * @DateTime 2018-04-24T09:43:26+0800
	 * @param    [int]                   $adminid [机构添加的管理员id]
	 * @param    [int]                   $organid [机构组织id]
	 * @return   [type]                            [description]
	 */
	public function getAdminUser($adminid) {
		if ($adminid > 0) {

			$accountmodel = new Allaccount;
			$retmsg = $accountmodel->getAdminUser($adminid);

			if (empty($retmsg)) {
				return return_format([], 0);
			} else {
				$retmsg['addtime'] = date('Y-m-d H:i:s', $retmsg['addtime']);
				$retmsg['logintime'] = date('Y-m-d H:i:s', $retmsg['logintime']);
				return return_format($retmsg, 0);
			}
		} else {
			return return_format('', 40031);
		}
	}
	/**
	 * [updateAdminUser 更新管理员数据]
	 * @Author wyx
	 * @DateTime 2018-04-24T10:36:20+0800
	 * @param    [array]                 $data    [需要更新的数据]
	 * @param    [int]                   $adminid [管理员id]
	 * @param    [int]                   $organid [组织id]
	 * @return   [array]                          [description]
	 */
	public function updateAdminUser($data, $adminid) {
		if ($adminid > 0 && !empty($data)) {

			$allowfield = ['username', 'mobile', 'password', 'repassword', 'useraccount', 'info', 'groupids'];
			//过滤 多余的字段
			$newdata = where_filter($data, $allowfield);

			$accountmodel = new Allaccount;
			$retmsg = $accountmodel->updateAdminUser($newdata, $adminid);

			return $retmsg;

		} else {
			return return_format('', 40031);
		}
	}
	/**
	 * [addAdminUser 添加管理员账号]
	 * @Author wyx
	 * @DateTime 2018-04-24T14:39:06+0800
	 * @param    [array]                 $data    [需要添加的数据]
	 * @param    [int]                   $organid [组织机构标识id]
	 */
	public function addAdminUser($data) {
		if (!empty($data)) {

			$allowfield = ['username', 'mobile', 'password', 'repassword', 'useraccount', 'info', 'groupids'];
			//过滤 多余的字段
			$newdata = where_filter($data, $allowfield);

			$accountmodel = new Allaccount;
			$retmsg = $accountmodel->addAdminUser($newdata);

			return $retmsg;

		} else {
			return return_format('', 40033);
		}
	}
	/**
	 * [delAdminUser 删除机构添加的管理员]
	 * @Author wyx
	 * @DateTime 2018-04-24T15:35:23+0800
	 * @param    [int]                   $adminid [要删除的管理员表adminmember 的id ]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function delAdminUser($adminid) {
		if ($adminid > 0) {
			$accountmodel = new Allaccount;
			$retmsg = $accountmodel->delAdminUser($adminid);

			return $retmsg;
		} else {
			return return_format('', 40037);
		}
	}
	/**
	 * [switchAdminFlag 切换管理员的可用状态]
	 * @Author wyx
	 * @DateTime 2018-04-24T15:54:20+0800
	 * @param    [int]                   $flag    [要改变的状态值]
	 * @param    [int]                   $adminid [给那个管理员改变]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function switchAdminFlag($flag, $adminid) {

		if ($adminid > 0 && in_array($flag, [0, 1])) {
			$accountmodel = new Allaccount;
			$retmsg = $accountmodel->switchAdminFlag($flag, $adminid);

			return $retmsg;
		} else {
			return return_format('', 40040);
		}

	}
	/**
	 * [organCourseList 获取机构课表]
	 * @Author wyx
	 * @DateTime 2018-04-25T09:55:13+0800
	 * @param    [string]                $date    [需要查询的日期]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function organCourseList($date) {

		$datearr = explode('-', $date);
		if (count($datearr) != 3) {
			return return_format('', 40042);
		}

		$cal = new Calendar($datearr[0], $datearr[1], $datearr[2]);
		$starttime = date('Y-m-d', $cal->starttime);
		$endtime = date('Y-m-d', $cal->endtime);
		//获取指定月的星期 和 日期数组
		$calendar = $cal->array;

		//获取每天的课节 数量信息
		$schedobj = new Toteachtime;
		$datecourse = $schedobj->organCourseList($starttime, $endtime);
		//将日历 和 数据合并
		foreach ($calendar as $key => &$val) {
			foreach ($val as &$inner) {
				$initarr = [];
				// var_dump($inner);
				$temp = explode('-', $inner);
				$initarr['timestr'] = $inner;
				$initarr['year'] = $temp[0];
				$initarr['month'] = $temp[1];
				$initarr['day'] = $temp[2];
				$initarr['num'] = isset($datecourse[$inner]) ? $datecourse[$inner] : 0;

				$inner = $initarr;
			}
		}
		//将数据结果返回
		return return_format($calendar, 0);

	}
	/**
	 * [getLessonsByDate 根据日期获取课程详情]
	 * @Author wyx
	 * @DateTime 2018-04-25T14:14:00+0800
	 * @param    [int]                   $pagenum  [要获取的日期]
	 * @param    [int]                   $limit    [要获取的日期]
	 * @param    [string]                $date    [要获取的日期]
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                          [description]
	 */
	public function getLessonsByDate($pagenum, $limit, $restype, $date) {

		$datearr = explode('-', $date);
		if (count($datearr) != 3) {
			return return_format('', 40044);
		}

		if ($pagenum > 0) {
			$start = ($pagenum - 1) * $limit;
			$limitstr = $start . ',' . $limit;
		} else {
			$pagenum = 1;
			$start = 0;
			$limitstr = $start . ',' . $limit;
		}
		//获取当前登录机构属于教师个人还是企业
		//$organobj = new Organ;
		//$restype = $organobj->getrestype($organid);
		//获取每天的课节 数量信息
		$schedobj = new Toteachtime;
		$datecourse = $schedobj->getLessonsByDate($date, $limitstr);
		if (empty($datecourse)) {
			return return_format((object) array(), 0);
		} else {
			//获取教师信息
			$teacharr = array_column($datecourse, 'teacherid');
			$currarr = array_column($datecourse, 'curriculumid');
			$teachobj = new Teacherinfo;
			$namearr = $teachobj->getTeachernameByIds($teacharr);
			$orderobj = new \app\teacher\model\OrderManage;
			//获取课程图片
			$courseobj = new Curriculum;
			$imagearr = $courseobj->getCurriculumImageById($currarr);
			$strdate = strtotime($date); //输入时间转时间戳
			$strday = strtotime(date('Y-m-d')); //当前日期
			$strtime = strtotime(date('Y-m-d H:i:s')); //当前时间时分秒

			$classroom = new Classroom();
			$organ = new Organ();

			foreach ($datecourse as $key => &$val) {
				$starttime = $val['starttime'];
				$endtime = $val['endtime'];

				$val['nickname'] = $namearr[$val['teacherid']];
				//计算开始时间和结束时间
				$timearr = explode(',', $val['timekey']);
//				var_dump($timearr[0]);
				$hourarr = explode(':', get_time_key($timearr[0]));
//				dump($hourarr);
				$datearr = explode('-', $val['intime']);
				$unixtime = mktime($hourarr[0], $hourarr[1], 0, $datearr[1], $datearr[2], $datearr[0]);
				//$unixlast = $unixtime + 1800 * count($timearr);
				$unixlast = $unixtime + 60 * $val['classhour'];
				//合并图片数据
				$val['courseimage'] = isset($imagearr[$val['curriculumid']]) ? $imagearr[$val['curriculumid']] : '';

				// var_dump($timearr) ;exit() ;
				$val['starttime'] = date('Y-m-d H:i:s', $unixtime);
				$val['endtime'] = date('Y-m-d H:i:s', $unixlast);
				//去除前台不需要显示的字段
				unset($val['timekey']);
				unset($val['intime']);
				//unset($val['teacherid']);
				$val['starttime'] = date('Y-m-d H:i:s', $unixtime);
				$val['endtime'] = date('Y-m-d H:i:s', $unixlast);

				//根据toteachtime的id获取课时信息

				if ($val['type'] == 1) {
					# 一对一
					$sum = 1;
				} else {
					# 当该课时不为一对一时
					$whe = ['schedulingid' => $val['schedulingid'], 'coursetype' => ['neq', 1]];
					//获取相关学生人数
					$list = $orderobj->getStudenAlllist($whe);
					$sum = count($list);
				}

				//$list = $schedobj->getStudlists($whe);
				# classstatus 字段存储状态
				$datecourse[$key]['classstatus'] = 0;
				$datecourse[$key]['statusinfo'] = '查看';
				$datecourse[$key]['sum'] = $sum;
				//return return_format($datecourse,0,'ok');
				//合并图片数据
				$val['courseimage'] = isset($imagearr[$val['curriculumid']]) ? $imagearr[$val['curriculumid']] : '';
				//只有是老师个人机构登录时才显示以下信息

				//判断当天日期是否过期，如果过期则返回状态0
				if ($val['status'] == 1 && $strtime <= $endtime && $strtime >= $starttime - 1800) {
					// 当天未过期则判断当前时间和开课时间相比较，时间到了开课前5分钟以内，回进教室1
					$roomInfo = $classroom->getRoomId($val['id']);
					if ($roomInfo) {
						$roomkey = $organ->getOrganid()['roomkey'];
						$time = time();
						//必填， 0：主讲(老师 )  1：助教 2: 学员   3：直播用户  4:巡检员
						$usertype = '4';
						$sign = MD5($roomkey . $time . $roomInfo['classroomno'] . $usertype);
						// 巡课密码
						$userpassword = getencrypt($roomInfo['patrolpwd'], $roomkey);
                        $jumpurl = config('param.server_url');
						$datecourse[$key]['forclassurl'] = "http://global.talk-cloud.net/WebAPI/entry/domain/cqnmjy/serial/{$roomInfo['classroomno']}/username/巡查/usertype/$usertype/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/$jumpurl";
					} else {
						$datecourse[$key]['forclassurl'] = '';
					}
					$datecourse[$key]['classstatus'] = 1;
					$datecourse[$key]['statusinfo'] = '可以进教室';
				} elseif ($strtime > $unixlast + 300) {
					$datecourse[$key]['classstatus'] = 0;
					$datecourse[$key]['statusinfo'] = '查看';
					$datecourse[$key]['forclassurl'] = '';
				} else {
					//如果未到开课前五分钟内，则返开始2；
					$datecourse[$key]['classstatus'] = 2;
					$datecourse[$key]['statusinfo'] = '未开始';
					$datecourse[$key]['forclassurl'] = '';
				}

			}
			// var_dump($datecourse) ;exit();
			//获取 分页的总记录数
			$total = $schedobj->getLessonsByDateCount($date);
			$result = [
				'data' => $datecourse, // 内容结果集
				'pageinfo' => [
					'pagesize' => $limit, // 每页多少条记录
					'pagenum' => $pagenum, //当前页码
					'total' => $total, // 符合条件总的记录数
				],
			];

			return return_format($result, 0);

		}

	}
	/*
		*  查询课时详情
		*  @Author wangwy
		*
	*/
	public function getPeriodList($data) {
		//$list = self::getSingleteacher($data['organid']);
		$tchtimeobj = new \app\teacher\model\ToteachTime();
		$res = $tchtimeobj->getMobileformg(['id' => $data['toteachtimeid']], 'teacherid');
		$data['teacherid'] = $res[0]['teacherid'];
		$period = new \app\teacher\business\CurriculumModule;
		$list = $period->getPeriodList($data);
		return $list;

	}
	/*
		*  课时查询评价
		*  @Author wangwy
		*
	*/
	public function getperComment($restype, $teacherid, $lessonsid, $date, $pagenum, $pagesize) {
		//$list = self::getSingleteacher($organid);
		if ($restype == 1) {
			//$teacherid = $list['teacherid'];
			$period = new \app\teacher\business\CurriculumModule;
			$list = $period->getperComment($teacherid, $lessonsid, $date, $pagenum, $pagesize);
			return $list;
		} else {
			return return_format('', 23016, lang('23016'));
		}
	}
	/*
		*  视频回放
		*  @Author wangwy
		*
	*/
	public function getLessonsPlayback($toteachid) {
		//$list = self::getSingleteacher($organid);
		//$data['teacherid']=$list['teacherid'];
		$period = new \app\teacher\business\CurriculumModule;
		$list = $period->getLessonsPlayback($toteachid);
		return $list;

	}
	/*
		*  添加课时相关的文件夹列表和 资源列表关联
		*  @Author wangwy
		*
	*/
	public function addCourseware($data, $fileid) {
        $period = new \app\teacher\business\CurriculumModule;
        $list = $period->addCourseware($data, $fileid);
        return $list;
//		if ($data['restype'] == 1) {
//			//$data['teacherid']=$list['teacherid'];
//
//		} else {
//			return return_format('', 23016, lang('23016'));
//		}
	}
	/*
		*  删除课时相关的文件夹列表和 资源列表
		*  @Author wangwy
		*
	*/
	public function delCourseware($data, $fileid) {
		//$list = self::getSingleteacher($data['organid']);
        $period = new \app\teacher\business\CurriculumModule;
        $list = $period->delCourseware($data, $fileid);
        return $list;

	}
	/**
	 * [intoClassroom 寻课进教室]
	 * @Author
	 * @DateTime 2018-04-25T14:14:00+0800
	 * @param    [string]                $toteachid     上课时间表id
	 * @param    [int]                   $organid       [机构id]
	 * @return   [array]                          [description]
	 */
	public function intoOranClassroom($toteachid, $organid) {
		//实例化模型
		$classmodel = new \app\teacher\model\Classroom;
		$organobj = new \app\teacher\model\Organ;
		// = $organobj->getOrganname([$organid]);
		$namearr = $organobj->getOrganname([$organid]);
		$nickname = empty($namearr) ? 'nickname' : $namearr[$organid];
		$keyarr = $organobj->getRoomkey($organid);
		$key = $keyarr['roomkey'];
		$classinfo = $classmodel->getClassInfo($toteachid);
		$time = time();
		$sign = MD5($key . $time . $classinfo['classroomno'] . '4'); //教室号
		$userpassword = getencrypt($classinfo['patrolpwd'], $key);
        $jumpurl = config('param.server_url');
		$url = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$classinfo['classroomno']}/username/$nickname/usertype/4/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/$jumpurl";
		$data['url'] = $url;
		return return_format($data, 0, lang('success'));

	}
	/**显示机构教师的文件夹和资源列表
		*  @Author wangwy
		*
	*/
	public function getFileList($data) {
		//$list = self::getSingleteacher($data['organid']);
        $period = new \app\teacher\business\Classesbegin;
        $list = $period->getFileList($data);
        return $list;
//		if ($data['restype'] == 1) {
//			//$data['teacherid'] = $list['teacherid'];
//		} else {
//			return return_format('', 23016, lang('23016'));
//		}
	}
	/**
	 *	统计机构 数据分析 获取机构数据的 头部数据 ，及概览数据
	 *	@author wyx
	 *	@param  $organid 机构标识id
	 *
	 *
	 */
	public function getOrganAnalysis() {
		//交易总额
		$accountobj = new Organaccount;
		$flow = $accountobj->getOrganTradeFlow();
		if (empty($flow['amount'])) {
			$flow['amount'] = '0.00';
		}

		if (date('d') == '01') { // 如果当天是本月第一天，为了统计昨日数据，需要提前一天统计
			$monthstart = strtotime(date('Y-m-01 00:00:00')) - 86400; //为了统计昨天的 时间减一天
		} else {
			$monthstart = strtotime(date('Y-m-01 00:00:00'));
		}
		// 成交订单笔数
		$orderwhere = [
			'orderstatus' => ['GT', 10],
		];

		$orderobj = new Ordermanage;
		$ordertotal = $orderobj->getOrderCounts($orderwhere);

		//班级总览 招生中的班级数
		$classarr = $this->getClassScan($monthstart);
		//学生总数 当日新增 昨日新增 本月新增
		$studentarr = $this->getStudentScan($monthstart);
		//老师总数
		$teachobj = new Teacherinfo;
		$teachtotal = $teachobj->getTeacherAllAccount();
		// var_dump($teachtotal);

		//课件并发统计 今日  本周  本月
		//$data['courseline'] = empty($data['courseline']) ? 'day' : $data['courseline'] ;
		//$coursearr = $this->getCourseAnalysis($data['courseline'],$organid);
		// $coursearr = $this->getCourseAnalysis('month');
		// var_dump($coursearr);

		//交易流水统计  近七天  近30天
		// $data['flowline'] = 'month';
		//$data['flowline'] = empty($data['flowline']) ? 'week' : $data['flowline'] ;
		//$flowarr = $this->getSaleAnalysis($data['flowline'],$organid);
		// var_dump($flowarr);
		$anasysdata = [
			'totalflowcash' => $flow['amount'], //总交易额
			'totalorder' => $ordertotal, //总订单数量
			'classarr' => $classarr, // 班级分析数据
			'studentarr' => $studentarr, //学生总数，学生总览
			'teachtotal' => $teachtotal, //教师总数
			// 'coursearr' => $coursearr,//课程分析
			// 'flowarr' => $flowarr,//流水分析
		];
		return return_format($anasysdata, 0, 'OK');

	}
	/**
	 *	统计机构 数据分析
	 *	@author wyx
	 *	@param  $data    要统计的条件
	 *	@param  $organid 机构标识id
	 *
	 *
	 */
	public function getOrganAnaCourse($data) {
		//课件并发统计 今日  本周  本月
		$data['courseline'] = empty($data['courseline']) ? 'day' : $data['courseline'];
		$coursearr = $this->getCourseAnalysis($data['courseline']);

		$anasysdata = [
			'coursearr' => $coursearr, //课程分析
		];
		return return_format($anasysdata, 0, 'OK');

	}
	/**
	 *	统计机构 数据分析
	 *	@author wyx
	 *	@param  $data    要统计的条件
	 *	@param  $organid 机构标识id
	 */
	public function getOrganAnaFlow($data) {
		//交易流水统计  近七天  近30天
		// $data['flowline'] = 'month';
		$data['flowline'] = empty($data['flowline']) ? 'week' : $data['flowline'];
		$flowarr = $this->getSaleAnalysis($data['flowline']);

		$anasysdata = [
			'flowarr' => $flowarr, //流水分析
		];
		return return_format($anasysdata, 0, 'OK');

	}

	/**
	 *	统计机构 数据分析
	 *	@author wyx
	 *	@param  $data    要统计的条件
	 *	@param  $organid 机构标识id
	 */
	public function getOrganPayAnaFlow($data) {
		//交易流水统计  近七天  近30天
		// $data['flowline'] = 'month';
		$data['flowline'] = empty($data['flowline']) ? 'week' : $data['flowline'];
		$flowarr = $this->getPaySaleAnalysis($data['flowline']);

		return return_format($flowarr, 0, 'OK');

	}

	/**
	 *	对课程 上课时间 统计分析  可以按照 当天 本周 本月
	 *  @Author wyx
	 *	@param $flag  day week  month 分别代表 当天 本周 本月
	 *	@param $flag  day week  month 分别代表 当天 本周 本月
	 *  @param  $organid    机构标识id
	 *	@return array
	 */
	protected function getCourseAnalysis($flag = 'day') {

		if ($flag == 'week') {
			//按周统计
			$timeline = $this->dateSliceByCondition($flag);

			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDate($timeline['startdate'], $timeline['enddate']);

			//数据组装
			$datearr = array_column($courselist, 'intime');
			foreach ($timeline['origndata'] as &$val) {
				$key = array_search($val['intime'], $datearr);
				if ($key !== false) {
					$val['num'] = $courselist[$key]['num'];
					$val['realnum'] = $courselist[$key]['allrealnum'];
				}
			}
			return $timeline['origndata'];

		} elseif ($flag == 'month') {
			//按月统计
			$timeline = $this->dateSliceByCondition($flag);
			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDate($timeline['startdate'], $timeline['enddate']);
			//数据组装
			$datearr = array_column($courselist, 'intime');
			foreach ($timeline['origndata'] as &$val) {
				$key = array_search($val['intime'], $datearr);
				if ($key !== false) {
					$val['num'] = $courselist[$key]['num'];
					$val['realnum'] = $courselist[$key]['allrealnum'];
				}
			}
			return $timeline['origndata'];

		} else {
			//按天统计 默认按照天
			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDay(date('Y-m-d'));

			$temparr = [];
			//对数据的timekey 处理 并统计首部相同的timekey对应的 人数总和
			foreach ($courselist as $key => &$val) {
				//对时间点处理 以便后面统计
				if (strpos($val['timekey'], ',')) {
					$val['timekey'] = substr($val['timekey'], 0, strpos($val['timekey'], ','));
				}

				//统计相同起点，并求和
				if (isset($temparr[$val['timekey']])) {
					// 如果已经存在 就累加
					$temparr[$val['timekey']]['realnum'] = $val['realnum'] + $temparr[$val['timekey']]['realnum'];
					$temparr[$val['timekey']]['num'] = ++$temparr[$val['timekey']]['num'];
				} else {
					// 没有的就 赋值
					$temparr[$val['timekey']]['realnum'] = $val['realnum'];
					$temparr[$val['timekey']]['num'] = 1;
				}
			}
			$inData = [];
			foreach ($temparr as $k => $v) {
				$key = round($k / 3);
				if (!isset($inData[$key])) {
					$inData[$key] = $v;
				} else {
					$inData[$key]['realnum'] = $v['realnum'] + $inData[$key]['realnum'];
					$inData[$key]['num'] = $v['num'] + $inData[$key]['num'];
				}
			}

			//组装数据 按照48个时间段 30分一段
			$resultdata = [];
			for ($i = 0; $i < 48; $i++) {
				if (isset($inData[$i])) {
					$resultdata[$i] = $inData[$i];
				} else {
					$resultdata[$i] = ['realnum' => 0, 'num' => 0];
				}
			}
			return $resultdata;
		}

	}
	/**
	 *	对交易流水 统计分析  可以按 近7天 近30天
	 *  @Author wyx
	 *	@param $flag  week  month 分别代表 近7天 近30天 默认近7天
	 *	@return array
	 *	订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
	 *  @param  $organid    机构标识id
	 */
	protected function getSaleAnalysis($flag = 'week') {
		$starttime = 0;
		$unixend = time();
		if ($flag == 'week') {
			//按近7天统计
			$timestr = date('Y-m-d 00:00:00', $unixend - 6 * 86400);
			$starttime = strtotime($timestr);

		} elseif ($flag == 'month') {
			//按近30天统计
			$timestr = date('Y-m-d 00:00:00', $unixend - 29 * 86400);
			$starttime = strtotime($timestr);
		}
		$paylogobj = new Studentpaylog;
		$result = $paylogobj->getOfficalCashFlow($starttime);

		//创建原始格式数组
		$resultdate = array_column($result, 'datestr');
		$returnarr = [];
		$temp = [];
		for ($i = $starttime; $i <= $unixend;) {
			//当前步的时间
			$tempdate = date('Y-m-d', $i);

			$key = array_search($tempdate, $resultdate);
			if ($key !== false) {
				$temp['datestr'] = date('Y-m-d', $i);
				$temp['num'] = $result[$key]['num'];
				$temp['totalpay'] = $result[$key]['totalpay'];
				$returnarr[] = $temp;
			} else {
				$temp['datestr'] = date('Y-m-d', $i);
				$temp['num'] = 0;
				$temp['totalpay'] = '0.00';
				$returnarr[] = $temp;
			}

			$i += 86400;
		}

		return $returnarr;

	}

	/**
	 *	对交易流水 统计分析  可以按 近7天 近30天
	 *  @Author wyx
	 *	@param $flag  week  month 分别代表 近7天 近30天 默认近7天
	 *	@return array
	 *	订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
	 *  @param  $organid    机构标识id
	 */
	protected function getPaySaleAnalysis($flag = 'week') {
		$starttime = 0;
		$unixend = time();
		if ($flag == 'week') {
			//按近7天统计
			$days = 6;
		} elseif ($flag == 'month') {
			//按近30天统计
			$days = 29;
		}
		$timestr = date('Y-m-d 00:00:00', $unixend - $days * 86400);
		$starttime = strtotime($timestr);

		// 初始化数组结构
		$timelist = [];
		for ($i = 0; $i <= $days; $i++) {
			$inArr = [
				'datestr' => date('Y-m-d', $starttime + $i * 86400),
				'pcprice' => 0.00,
				'wxprice' => 0.00,
				'appprice' => 0.00,
				'key' => $i,
			];
			$timelist[$inArr['datestr']] = $inArr;
		}

		$paylogobj = new Studentpaylog;
		$result = $paylogobj->getCashFlow($starttime);
		if ($result) {
			$orderman = new Ordermanage();
			$coursorder = new Coursepackageorder();
			foreach ($result as $k => &$v) {
				if ($v['paystatus'] == 1) {
					$v['ports'] = $orderman->getId(['ordernum' => $v['out_trade_no']], 'ordersource')['ordersource'];
				} else {
					$v['ports'] = $coursorder->getId(['ordernum' => $v['out_trade_no']], 'ordersource')['ordersource'];
				}
				// 下单渠道1pc 2手机 3微信
				if ($v['ports'] == 1) {
					$timelist[$v['datestr']]['pcprice'] += $v['paynum'];
				} else if ($v['ports'] == 2) {
					$timelist[$v['datestr']]['appprice'] += $v['paynum'];
				} else {
					$timelist[$v['datestr']]['wxprice'] += $v['paynum'];
				}
			}
		}
		unset($result);
		$inTime = [];
		foreach ($timelist as $k => $v) {
			$inTime[$v['key']] = $v;
		}
		unset($timelist);
		return $inTime;

	}

	/**
	 *	获取正在招生中的班级数 和 近一个月 的 今天和昨天的 新增班级数
	 *  @Author wyx
	 *	@param  $starttime 本月的开始 时间
	 *  @param  $organid   机构标识id
	 *	@return array
	 *
	 */
	protected function getClassScan($starttime) {
		//招生中的班级数
		$scheduobj = new Scheduling;
		$classingnum = $scheduobj->getClassing();

		//机构概览
		$monthclassarr = $scheduobj->getAddClassByDate($starttime);
//		dump($monthclassarr);

		$monthclass = [];
		foreach ($monthclassarr as $val) {
			$monthclass[$val['datestr']] = $val['stunum'];
		}
		//今日 新增班级数
		if (empty($monthclass[date('Y-m-d')])) {
			$classtodaynum = 0;
		} else {
			$classtodaynum = $monthclass[date('Y-m-d')];
		}
		//昨日 新增班级数
		if (empty($monthclass[date('Y-m-d', time() - 86400)])) {
			$classyesnum = 0;
		} else {
			$classyesnum = $monthclass[date('Y-m-d', time() - 86400)];
			//如果今天是本月第一天 将昨天的 置为0 然后方便统计
			if (date('d') == '01') {
				$monthclass[date('Y-m-d', time() - 86400)] = 0;
			}
		}
		//本月 机构数
		$monthclassnum = array_sum($monthclass);

		return [
			'classingnum' => $classingnum,
			'classtodaynum' => $classtodaynum,
			'classyesnum' => $classyesnum,
			'monthclassnum' => $monthclassnum,
		];
	}
	/**
	 *	获取学生的总数目 和 近一个月 的 今天和昨天的 统计数据
	 *  @Author wyx
	 *	@param $starttime 本月的开始 时间
	 *	@param $organid   机构标识id
	 *	@return array
	 */
	protected function getStudentScan($starttime) {
		//学生总数
		$studentobj = new Studentinfo;
		$studenttotal = $studentobj->getStudentAllAccount();
		//学生总览
		$monthdatarr = $studentobj->getAllMonthData($starttime);
		$monthdata = [];
		foreach ($monthdatarr as $val) {
			$monthdata[$val['formatdate']] = $val['num'];
		}
		//今日 学生数
		if (empty($monthdata[date('Y-m-d')])) {
			$stutodaynum = 0;
		} else {
			$stutodaynum = $monthdata[date('Y-m-d')];
		}
		//昨日 学生数
		if (empty($monthdata[date('Y-m-d', time() - 86400)])) {
			$stuyesnum = 0;
		} else {
			$stuyesnum = $monthdata[date('Y-m-d', time() - 86400)];
			if (date('d') == '01') {
				$monthdata[date('Y-m-d', time() - 86400)] = 0;
			}
//将昨天的清空 以便计算当月的
		}
		//本月 学生数
		$monthnum = array_sum($monthdata);

		return [
			'studenttotal' => $studenttotal,
			'stutodaynum' => $stutodaynum,
			'stuyesnum' => $stuyesnum,
			'monthnum' => $monthnum,
		];
	}
	/**
	 *	通过传入 周 或者 月。来截取本周最后一天日期，或者本月最后一天的日期
	 *  @Author wyx
	 *
	 *
	 */
	private function dateSliceByCondition($devide) {
		$unixstart = 0;
		$unixend = 0;
		if ($devide == 'week') {
//按周取
			$week = date('w');
			if ($week == 0) {
				$unixend = time();
				$unixstart = $unixend - 6 * 86400;

				$end = date('Y-m-d');
				$start = date('Y-m-d', $unixstart);
			} else {
				$sub = 7 - $week;

				$unixend = time() + $sub * 86400;
				$unixstart = $unixend - 6 * 86400;

				$end = date('Y-m-d', $unixend);
				$start = date('Y-m-d', $unixstart);
			}

		} elseif ($devide == 'month') {
// 按月取 计算本月的最后一天
			$start = date('Y-m-01');
			$end = date('Y-m-d', strtotime("$start +1 month -1 day"));

			$unixend = strtotime($end . ' 00:00:09');
			$unixstart = strtotime($start . ' 00:00:09');
		}
		// 组装数组
		$returnarr = [];
		$temp = [];
		for ($i = $unixstart; $i <= $unixend;) {
			$temp['intime'] = date('Y-m-d', $i);
			$temp['num'] = 0;
			$temp['realnum'] = 0;
			$returnarr[] = $temp;

			$i += 86400;
		}
		return ['startdate' => $start, 'enddate' => $end, 'origndata' => $returnarr];
	}

	/**
	 * post addOrganWithoutInfo 生成一个机构名称 没有域名和机构名称
	 * @ zzq  2018-05-03
	 * @param 无参数
	 * @return int id 机构id;
	 */
	public function addOrganWithoutInfo($restype) {
		$organ = new Organ;
		$data = [
			'organname' => '',
			'profile' => '',
			'imageurl' => '',
			'hotline' => '',
			'email' => '',
			'info' => '',
			'domain' => '',
			'restype' => $restype,
		];
		$res = $organ->save($data);
		// var_dump($res);
		// die;
		return $organ->id;
	}

	/**
	 * sendRegisterUserCode 注册发送手机验证码
	 * @ zzq
	 * @param mobile string           [联系电话]
	 * @param type int           [0表示注册 1表示修改密码]
	 * @param sessionId string           [图形验证码sessionId]
	 * @param imageCode string           [图形验证码]
	 * @param vip int            [0表示免费 1表示vip]
	 * @return array();
	 */
	public function sendOrganWebUserCode($mobile, $type, $sessionId, $imageCode, $vip) {

		if (empty($mobile)) {
			return return_format('', 50015, lang('50015'));
		}
		//验证手机号
		if (!$this->checkMobile($mobile)) {
			return return_format('', 50021, lang('50021'));
		}
		//调用注册机构管理员的接口
		$ret = [0, 1];
		if (!in_array($type, $ret)) {
			return return_format('', 50026, lang('50026'));
		}
		$rot = [0, 1];
		if (!in_array($vip, $rot)) {
			return return_format('', 50051, lang('50051'));
		}
		//检验图形验证码是否合法
		if (empty($sessionId)) {
			return return_format('', 50034, lang('50034'));
		}
		if (empty($imageCode)) {
			return return_format('', 50035, lang('50035'));
		}
		//验证图形验证码校验
		$verify = new Verifyhelper();
		$flag = $verify->check($imageCode, $sessionId);
		if (!$flag) {
			return return_format('', 50027, lang('50027'));
		}

		if ($type == 0) {
			//官网注册
			$typeStr = "OrganWebRegister";
			$myTypeStr = "官网注册";
			$organidStr = false;
		} elseif ($type == 1) {
			//官网找回密码
			$typeStr = "OrganWebFindPass";
			$myTypeStr = "官网找回密码";
		}
		//首次注册要先判断手机号是否已经注册了
		// var_dump($type);
		// die;
		if ($type == 0) {
			$obj = new Allaccount();
			// var_dump($obj->hasMobile($mobile,$vip));
			// die;
			if ($obj->hasMobile($mobile, $vip)) {
				return return_format('', 50016, lang('50016'));
			}
		}
		//vip机构
		if ($type == 1) {
			//根据vip 和 mobile 获取organid
			$allaccount = new Allaccount();
			$res = $allaccount->hasMobile($mobile, $vip);
			if ($res) {
				$organid = $res['organid'];
				if ($vip == 0) {
					$organidStr = false;
				} elseif ($vip == 1) {
					$organidStr = $organid;
				}
			} else {
				return return_format('', 50018, lang('50018'));
			}
		}
		//发送前删除验证码
		Cache::rm('mobile' . $mobile . $typeStr);
		$obj = new Messages();
		$mobileCode = rand(100000, 999999);
		$res = $obj->sendMeg($mobile, $type = 4, $params = [$mobileCode, '10'], '86', $typeStr, $organidStr);
		//打印日志
		Log::write($mobile . '发送验证码是:' . $mobileCode);
		//var_dump($mobileCode);
		// die;
		// var_dump(Cache::get('mobile'.$mobile.$typeStr));
		// die;
		// var_dump($res);
		// die;

		//var_dump($res);
		if ($res['result'] == 0) {
			return return_format('', 0, lang('success'));
		} else {
			Log::write($myTypeStr . '发送验证码错误号:' . $res['result'] . '发送验证码错误信息:' . $res['errmsg']);
			return return_format('', 50070, lang('50070'));
		}

	}

	/**
	 *  registerOrganUser 生成一个机构用户 没有域名和组织名称
	 * @ zzq  2018-05-03
	 * @param $array   包含注册信息
	 * @return $array   返回信息;
	 */
	public function registerUser($data) {

		if (empty($data['useraccount']) || empty($data['password']) || empty($data['imageCode']) || empty($data['mobileCode']) || empty($data['sessionId']) || empty($data['restype']) || empty($data['key']) || !isset($data['vip'])) {
			return return_format('', 50000, lang('50000'));
		}
		//检测密码
		$passFlag = verifyPassword($data['password']);
		if (!$passFlag) {
			return return_format('', 50068, lang('50068'));
		}
		//判断注册类型
		$resArr = [1, 2];
		if (!in_array($data['restype'], $resArr)) {
			return return_format('', 50036, lang('50036'));
		}

		$identifytype = "OrganWebRegister";
		$obj = new Allaccount();

		//判断手机号是否存在
		if ($obj->hasMobile($data['mobile'], $data['vip'])) {
			return return_format('', 50016, lang('50016'));
		}
		//验证手机号
		if (!$this->checkMobile($data['mobile'])) {
			return return_format('', 50021, lang('50021'));
		}

		//检测图形验证码
		$verify = new Verifyhelper();
		$flag = $verify->check($data['imageCode'], $data['sessionId']);
		if (!$flag) {
			return return_format('', 50027, lang('50027'));
		}

		//判断短信验证码是否正确
		$cacheMobilecode = Cache::get('mobile' . $data['mobile'] . $identifytype);
		if (empty($cacheMobilecode)) {
			return return_format('', 50013, lang('50013'));
		}
		// var_dump($cacheMobilecode);
		// var_dump($data['mobileCode']);
		// die;
		if ($cacheMobilecode != $data['mobileCode']) {
			if (!verifyErrorCodeNumByOfficial($data['mobile'], $identifytype)) {
				return return_format('', 50032, lang('50032'));
			}
			return return_format('', 50014, lang('50014'));
		}

		//username为空
		$data['username'] = '';
		$allAccount = new Allaccount();
		// var_dump($data);
		// die;
		$flag = $allAccount->resOrganAddAdminUser($data, 0);
		//如果$flag中的code为0表示成功的插入了一个机构
		//把配置文件复制给机构
		if ($flag['code'] == 0) {
			//注册成功后然后登录
			$userLogin = new UserLogin();
			$res = $userLogin->internalLogin(2, $flag['data']['adminid'], $data['key']);
			//如果登录异常
			if ($res['code'] != 0) {
				$flag['code'] = 50019;
				$flag['info'] = lang('50019');
			} else {
				$flag['data'] = $res['data'];
			}

			//          $organid = $flag['data']['organid'];
			// $slide = new Organslideimg();
			// $resOne = $slide->afterAddOrganChangeSlideImg($organid);
			// $organConfig = new Organconfig();
			// $resTwo = $organConfig->AfterAddOrganChangeClassConfig($organid);
		}

		return $flag;
	}

	/**
	 *  setOrganBaseInfo  //修改机构的基本信息 以及 包括企业名以及logo
	 * @ zzq  2018-05-03
	 * @param array $data 信息
	 * @return array 返回信息  ;
	 */
	public function setOrganBaseInfo($data) {

		$data = where_filter($data, ['organname', 'summary', 'imageurl', 'hotline', 'email', 'contactname', 'contactphone', 'contactemail', 'id']);

		if (!isset($data['organname']) || !isset($data['summary']) || !isset($data['imageurl'])
			|| !isset($data['hotline']) || !isset($data['email']) || !isset($data['contactname'])
			|| !isset($data['contactphone']) || !isset($data['contactphone']) || !isset($data['contactemail'])) {
			return return_format('', 50000, lang('50000'));
		}

		//限制机构名和域名长度
		$oLen = mb_strlen($data['organname'], 'UTF-8');
		$sLen = mb_strlen($data['summary'], 'UTF-8');

		if ($oLen > 12) {
			return return_format('', 50094, lang('50094'));
		} else if ($sLen > 100) {
			return return_format('', 50101, lang('50101'));
		}

		//判断所有的手机号，邮箱
		if (!$this->checkMobile($data['contactphone'])) {
			return return_format('', 50092, lang('50092'));
		}
		if (!$this->checkEmail($data['contactemail'])) {
			return return_format('', 50091, lang('50091'));
		}
		if (!$this->checkEmail($data['email'])) {
			return return_format('', 50022, lang('50022'));
		}

		$organ = new Organ();
		$res = $organ->updateOrgan($data, $data['id']);
		return $res;
	}

	/**
	 *  getOrganBaseInfo  //获取机构的基本信息  以及 包括域名和企业名以及logo
	 * @ zzq  2018-05-03
	 * @param int $organid 组织机构id
	 * @return array 返回信息  ;
	 */
	public function getOrganBaseInfo($organid) {
		//校验机构id是否存在该机构
		$data = [];

		//操作前机构状态是不是未认证
		$organ = new Organ();
		$res = $organ->getOrganById($organid);
		// if($res['auditstatus'] != 0){
		// 	return return_format('',50095,'这不是未认证的机构');
		// }
		//获取企业名和logo域名
		if ($res) {
			$data['organname'] = $res['organname'];
			$data['imageurl'] = $res['imageurl'];
			$data['hotline'] = $res['hotline'];
			$data['contactname'] = $res['contactname'];
			$data['contactphone'] = $res['contactphone'];
			$data['contactemail'] = $res['contactemail'];
			$data['summary'] = $res['summary'];
			$data['email'] = $res['email'];
			$data['organid'] = $res['id'];
		} else {
			//表示没有记录
			$data['organname'] = '';
			$data['imageurl'] = '';
			$data['hotline'] = '';
			$data['contactname'] = '';
			$data['contactphone'] = '';
			$data['contactemail'] = '';
			$data['summary'] = '';
			$data['email'] = '';
			$data['organid'] = '';
			//return return_format('', 50023, '暂无该机构的基本信息');
		}
		return return_format($data, 0, lang('success'));

	}

	/**
	 *  setOrganConfirmInfo  //设置机构的认证信息 分个人或者企业
	 * @ zzq  2018-05-03
	 * @param array $data 信息
	 * @return array 返回信息  ;
	 */
	public function setOrganConfirmInfo($data) {

		//验证传参
		$flag = $this->hasOrganById($data['organid']);
		if ($flag) {
			return $flag;
		}
		//查看这个基本信息有没有提交
		$baseobj = new Organinfo();
		$baseflag = $baseobj->hasOrganInfoById($data['organid']);
		if (!$baseflag) {
			return return_format('', 50024, lang('50024'));
		}
		//操作前机构状态是不是未认证
		$organ = new Organ();
		$preInfo = $organ->getOrganById($data['organid']);
		$set = [0, 1];
		if (!in_array($preInfo['auditstatus'], $set)) {
			return return_format('', 50095, lang('50095'));
		}
		if (empty($data['confirmtype'])) {
			return return_format('', 50075, lang('50075'));
		}

		$ret = [1, 2];
		if (!in_array($data['confirmtype'], $ret)) {
			return return_format('', 50096, lang('50096'));
		}

		//判断认证类型是否与注册的时候的注册类型一致
		$restype = $preInfo['restype'];
		if ($restype != $data['confirmtype']) {
			return return_format('', 50096, lang('50096'));
		}
		//组装字段信息
		if ($data['confirmtype'] == 1) {
			//表示的是个人认证设置多余字段为空
			$data['organname'] = '';
			$data['organnum'] = '';
			$data['organphoto'] = '';
		} elseif ($data['confirmtype'] == 2) {
			//表示的是企业认证
			$data['idname'] = '';
			$data['idnum'] = '';

		}
		$organconfirm = new Organconfirm();
		$res = $organconfirm->changeOrganConfirmInfo($data);
		return $res;

	}

	/**公共方法
		    *  getOrganConfirmInfo  //修改机构的认证信息 分个人或者企业
		    * @ zzq  2018-05-03
		    * @param int $organid 组织机构id
		    * @return array 返回信息  ;
	*/
	public function getOrganConfirmInfo($organid) {
		//验证传参
		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}
		$organconfirm = new Organconfirm();
		$data = $organconfirm->getOrganConfirmInfoById($organid);
		return $data;

	}

	/**公共方法
		    *  getOrganAuditResById  //查看该机构的审核结果
		    * @ zzq  2018-05-03
		    * @param int $organid 组织机构id
		    * @return array 返回信息  ;
	*/
	public function getOrganAuditResById($organid) {
		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}
		$organ = new Organ();
		$rot = $organ->getOrganById($organid);
		//根据auditstatus查询refuseinfo

		$arr = [];
//		$refuse = new Refuseorgan();
		//		$refuseinfo = $refuse->getRefuseOrganInfoById($organid);
		//		if($auditstatus == 0){
		//			$arr['auditinfo'] = "未认证";
		//			$arr['auditstatus'] = $auditstatus;
		//		}elseif($auditstatus == 1){
		//			$arr['auditinfo'] = "待审核";
		//			$arr['auditstatus'] = $auditstatus;
		//		}elseif($auditstatus == 2) {
		//			$arr['auditinfo'] = "未通过";
		//			$arr['auditstatus'] = $auditstatus;
		//			$arr['refuseinfo'] = $refuseinfo ? $refuseinfo : '';
		//		}elseif(($auditstatus == 3) || ($auditstatus == 4)) {
		//获取学堂的名称 域名 超级管理员用户名
		$allAccount = new Allaccount();
		//$arr['organname'] = $rot['organname'];
		//$arr['domain'] = $rot['domain'];
		//$arr['webDomain'] = "https://" . $rot['domain'] . ".51menke.com";
		//$arr['adminDomain'] = "https://" . $rot['domain'] . ".51menke.com";
		//$arr['adminname'] = $reg['username'];
		$arr['auditstatus'] = 3;
		$arr['auditinfo'] = "已通过";
		//$arr['refuseinfo'] = '' ;
		//		}
		return return_format($arr, 0, lang('success'));

	}

	/**
	 *  getOrganRefuseInfo  //查看该机构的被拒绝的原因(最新的)
	 * @ zzq  2018-05-03
	 * @param int $organid 组织机构id
	 * @param int $type 0表示首次填写信息  1表示从审核拒绝结果跳转过来
	 * @return array 返回信息  ;
	 */
	public function getOrganRefuseInfo($organid) {
		if (empty($organid)) {
			return return_format('', 50071, lang('50071'));
		}
		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}

		$refuse = new Refuseorgan();
		$refuseinfo = $refuse->getRefuseOrganInfoById($organid);
		$data = [];
		$data['refuseinfo'] = $refuseinfo ? $refuseinfo : '';
		return return_format($data, 0, lang('success'));

	}

	/**
	 *  getOrganIntroduceInfo  //官网机构审核中的介绍机构的信息
	 * @ zzq  2018-05-03
	 * @param array $data 信息
	 * @return array 返回信息  ;
	 */
	public function getOrganIntroduceInfo($organid) {
		if (empty($organid)) {
			return return_format('', 50071, lang('50071'));
		}

		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}
		$data = [];
		$organ = new Organ();
		$resInfo = $organ->getOrganById($organid);
		if ($resInfo) {
			if ($resInfo['organname']) {
				$data['organname'] = $resInfo['organname'];
			} else {
				$data['organname'] = '';
			}
			if ($resInfo['imageurl']) {
				$data['imageurl'] = $resInfo['imageurl'];
			} else {
				$data['imageurl'] = '';
			}
		} else {
			//暂无该机构的信息
			return return_format('', 50025, lang('50025'));
		}

		$organInfo = new Organinfo();

		$resBaseInfo = $organInfo->getOrganInfoById($organid);
		if ($resBaseInfo) {
			if ($resBaseInfo['summary']) {
				$data['summary'] = $resBaseInfo['summary'];
			} else {
				$data['summary'] = '';
			}
		} else {
			return return_format('', 50023, lang('50023'));
		}

		return return_format($data, 0, lang('success'));

	}

	/**
	 *  FromRefusedToUnAudited  //设置被拒绝后的机构返回未认证的状态值
	 * @ zzq  2018-05-03
	 * @param int $organid 组织机构id
	 * @return array 返回信息  ;
	 */
	public function FromRefusedToUnAudited($organid) {
		//验证传参
		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}
		$organ = new Organ();
		$ret = $organ->getOrganById($organid);
		if ($ret['auditstatus'] != 2) {
			return return_format('', 50097, lang('50097'));
		}

		$organ = new Organ();
		$res = $organ->updateOrganStatus(0, $organid);
		return $res;
	}

	//找回密码第一步
	/**
	 * [findPassOne //官网前台忘记密码(第一步)]
	 * @Author zzq
	 * @DateTime 2018-05-18
	 * @param data array           [信息]
	 * @return   [array]                   [description]
	 */
	public function findPassOne($data) {
		if (empty($data['mobile']) || empty($data['imageCode']) || empty($data['sessionId'])) {
			return return_format('', 50000, lang('50000'));
		}
		if (!$this->checkMobile($data['mobile'])) {
			return return_format('', 50021, lang('50021'));
		}
		//检测手机号
		$allaccount = new Allaccount();
		$res = $allaccount->hasMobile($data['mobile'], $data['isVip']);
		if (!$res) {
			return return_format('', 50018, lang('50018'));
		}

		//检测图形验证码
		$verify = new Verifyhelper();
		$flag = $verify->check($data['imageCode'], $data['sessionId']);
		if (!$flag) {
			return return_format('', 50027, lang('50027'));
		}
		//首次发送手机验证码
		$ret = $this->sendOrganWebUserCode($data['mobile'], 1, $data['sessionId'], $data['imageCode'], $data['isVip']);
		return return_format('', 0, lang('success'));
	}

	//找回密码第二步
	/**
	 * [findPassTwo //官网前台忘记密码(第二步)]
	 * @Author zzq
	 * @DateTime 2018-05-18
	 * @param data array           [信息]
	 * @return   [array]                   [description]
	 */
	public function findPassTwo($data) {
		$identifytype = "OrganWebFindPass";
		if (empty($data['mobile']) || empty($data['mobileCode']) || empty($data['newPassword']) || empty($data['imageCode']) || empty($data['sessionId'])) {

			return return_format('', 50000, lang('50000'));
		}
		//检测手机号
		if (!$this->checkMobile($data['mobile'])) {
			return return_format('', 50021, lang('50021'));
		}
		//检测手机号
		$allaccount = new Allaccount();
		$res = $allaccount->hasMobile($data['mobile'], $data['isVip']);
		// var_dump($res);
		// die;
		if (!$res) {
			return return_format('', 50018, lang('50018'));
		}

		//判断短信验证码是否正确
		$cacheMobilecode = Cache::get('mobile' . $data['mobile'] . $identifytype);

		if (empty($cacheMobilecode)) {
			return return_format('', 50013, lang('50013'));
		}
		if ($cacheMobilecode != $data['mobileCode']) {
			if (!verifyErrorCodeNumByOfficial($data['mobile'], $identifytype)) {
				return return_format('', 50032, lang('50032'));
			}
			return return_format('', 50014, lang('50014'));
		}

		//检测新密码长度
		$passFlag = verifyPassword($data['newPassword']);
		if (!$passFlag) {
			return return_format('', 50068, lang('50068'));
		}
		//最后一步再次检测图形验证码
		$verify = new Verifyhelper();
		$flag = $verify->check($data['imageCode'], $data['sessionId']);
		if (!$flag) {
			return return_format('', 50027, lang('50027'));
		}
		//更新新密码
		$obj = new Allaccount();
		$result = $obj->modifyPassByMobile($data['mobile'], $data['newPassword'], $data['isVip']);
		return $result;
	}

	//申请vip机构
	public function applyVipOrgan($organid) {

		$flag = $this->hasOrganById($organid);
		if ($flag) {
			return $flag;
		}
		$organ = new Organ();
		$res = $organ->applyVipOrgan($organid);
		return $res;
	}

	/**
	 *  hasOrganById  //判断该机构是否存在
	 * @ zzq  2018-05-03
	 * @param int $organid 组织机构id
	 * @return array 返回信息  ;
	 */
	public function hasOrganById($organid) {
		if (empty($organid)) {
			return return_format('', 50071, lang('50071'));
		}
		//校验机构id是否存在该机构
		$organ = new Organ();
		$res = $organ->getOrganById($organid);
		if (!$res) {
			return return_format('', 50006, lang('50006'));
		}
		return false;
	}

	public function getOrganAuditstatusById($organid) {
		if (empty($organid)) {
			return return_format('', 50000, lang('50000'));
		}
		$organ = new Organ();
		$res = $organ->getOrganAuditstatusById($organid);
		if ($res) {
			return return_format($res, 0, lang('success'));
		} else {
			return return_format('', 50039, lang('50039'));
		}

	}

	/**
	 * [getUserMeader 获取登陆管理员信息]
	 * @param $uid
	 * @return array
	 */
	public function getUserMeader($uid) {
		$merber = new Adminmember();
		$info = $merber->getOrganUserField($uid);
		if ($info) {
			$info['logintime'] = $info['logintime'] ? date('Y-m-d H:i:s', $info['logintime']) : '-';
			return return_format($info, 0, '查询成功');
		} else {
			return return_format('', $uid, '没有该管理员');
		}
	}

	/**
	 *  getAuditInfo //返回当前的机构审核状态
	 * @ zzq  2018-05-03
	 * @param int $auditstatus 组织机构审核状态
	 * @return array 返回信息  ;
	 */
	public function getAuditInfo($auditstatus) {
		$ret = [-1, 0, 1, 2, 3, 4];
		if (!in_array($auditstatus, $ret)) {
			return false;
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
	public function getMobileCode() {
		$str = rand(100000, 999999);
		return $str;
	}

	/**
	 * //检测手机号
	 * @Author zzq
	 * @param $mobile   int  [手机号]
	 * @return bool  [返回信息]
	 *
	 */
	public function checkMobile($mobile) {

		$pattern = "/^[1][0-9]{10}$/";
		if (preg_match($pattern, $mobile)) {
			return true;
		}
		return false;
	}

	/**
	 * //检测热线打电话(座机或者手机号)
	 * @Author zzq
	 * @param $tel   int  [座机或者手机号]
	 * @return bool  [返回信息]
	 *
	 */
	public function checkTel($tel) {

		$isTel = "/^[\d-]+$/";
		//$isMob = "/^[1][0-9]{10}$/";

		if (preg_match($isTel, $tel)) {
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
	public function checkEmail($email) {

		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
			return false;
		}
		return true;
	}

	/**
	 * 判断是否是个体教师，查询教师id
	 * @Author wangwy
	 *
	 **/
	public function getSingleteacher($organid) {
		$organobj = new Organ;
		$restypear = $organobj->getrestype($organid);
		$restype = $restypear['restype']; //判断个体教师
		$teacherobj = new Teacherinfo;
		$teacheridar = $teacherobj->getSingleid($organid);
		$teacherid = $teacheridar['teacherid']; //获取教师id
		return array('restype' => $restype, 'teacherid' => $teacherid);
	}

	/**
	 * 修改 关于我们
	 */
	public function setOrgan($aboutus, $id) {
		if (!$aboutus) {
			return return_format('', 11019, lang('param_error'));
		}

		$organ = new Organ();
		$nun = $organ->updateOrgans($aboutus, $id);
		if ($nun) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 11020, lang('error'));
		}
	}

	/**
	 * 查询关于我们
	 */
	public function getOrganAboutus($id) {
		$organ = new Organ();
		$info = $organ->getOrganById($id, 'aboutus');
		if ($info) {
			return return_format($info, 0, lang('success'));
		} else {
			return return_format('', 11021, lang('error'));
		}
	}
	
	/**
	 * 修改下载配置json
	 */
	public function setOrganDownloadJson($downloadjson, $id) {
		if (!$downloadjson) {
			return return_format('', 11019, lang('param_error'));
		}

		$organ = new Organ();
		$nun = $organ->updateOrgansDownloadJson(json_encode($downloadjson), $id);
		//if ($nun) {
			return return_format('', 0, lang('success'));
		//} else {
		//	return return_format('', 11020, lang('error'));
		//}
	}

	/**
	 * 查询下载配置json
	 */
	public function getOrganDownloadJson($id) {
		$organ = new Organ();
		$info = $organ->getOrganById($id, 'downloadjson');
		if ($info) {
			return return_format(json_decode($info['downloadjson'], true), 0, lang('success'));
		} else {
			return return_format('', 11021, lang('error'));
		}
	}

}
