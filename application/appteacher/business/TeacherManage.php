<?php
/**
 * 机构端教师管理 业务逻辑层
 *
 *
 */
namespace app\appteacher\business;
use app\teacher\model\TeacherInfo;
use app\teacher\model\TeacherLable;
use app\teacher\model\Teachertagrelate;
use app\teacher\model\Teachertime;
use app\teacher\model\ToteachTime;
use app\teacher\model\Scheduling;
use app\teacher\model\Allaccount;
use app\appteacher\model\Organ;
use think\Cache;
use think\Log;
use Calendar;

class TeacherManage{
	protected $str = '';
	/**
	 * 获取教师列表
	 * @Author wangwy
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $organid 机构标记id      必填
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getTeacherList($mobil,$nickname,$pagenum,$organid,$limit){
		$where = [] ;
		!empty($mobil) && $where['mobile'] = $mobil ;
		!empty($nickname) && $where['nickname'] = ['like',$nickname.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['organid'] = $organid;
		$where['delflag'] = 0;

		$techmodel = new TeacherInfo;
		return $techmodel->getTeacherList($where,$limitstr);
	}
	/**
	 * 获取教师详细信息
	 * @Author wangwy
	 * @param $teachid 教师id
	 * @param $organid 机构id从session 中获取
	 * @param $allaccountid 机构人员总表id 从session 中获取
	 * @return object
	 *
	 */
	 public function getTeachInfo($teachid,$organid){
 		$techmodel = new TeacherInfo;
 		//返回教师基本信息
 		$field = 'teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus,addtime,sex,profile,birth' ;
 		$baseinfo = $techmodel->getTeacherData($field,$teachid,$organid);

 		!empty($baseinfo['addtime']) &&	$baseinfo['addtime'] = date('Y-m-d H:i:s',$baseinfo['addtime']);
 		!empty($baseinfo['birth']) && $baseinfo['birth'] = date('Y-m-d',$baseinfo['birth']);

 		if( empty($baseinfo) ){//没有符合条件的教师数据
 			return return_format([],0,'没有符合条件的教师数据') ;
 		}else{
 			//根据教师id 获取总表的id
 			$allaccount = new Allaccount;
 			$retarr = $allaccount->getTeacherAccount($teachid);
 			$allaccountid = $retarr['id'] ;
 			//获取教师最近登陆时间
 			//$allaccountid 从session 中取
 			$basetime = $techmodel->getLoginTime($allaccountid);
 			$baseinfo['logintime'] = isset($basetime[0]) ? date('Y-m-d H:i:s',$basetime[0]) : '' ;

 			//获取教师拥有的标签
 			$lablerelate = new Teachertagrelate;
 			$tagarrs  = $lablerelate->getBindLable($teachid,$organid);
 			//选中的标签的ids
 			$selectedid = $tagarrs['selectedid'] ;
 			//教师已经选用的标签
 			$lablearr = $tagarrs['alltagmsg'] ;
 			$lablearr = $this->dealArray($lablearr,'fatherid');
 			//获取一对一可预约时间
 			$timeobj = new Teachertime;
 			$resttime = $timeobj->findWeekMark($organid,$teachid);

			// $kk = [];
      // foreach ($resttime as $ky => $val) {
      // 	// code...
			// 	for ($i=0; $i < 7 ; $i++) {
			// 		if ($i == $val['week']) {
			// 			$kk[$i] = $resttime[$i];
			// 		}
			// 	}
      // }
      // $kk = array_values($kk);
      // if (isset($kk[0])) {
			// 		$ls = $kk[0];
			// 		$kk[7] = $ls;
			// 		unset($kk[0]);
      // }

 			//课程信息  2018-04-29 注释掉 功能砍掉
 			// $classobj = new Scheduling;
 			// $classarr = $classobj->getOpenClassList($organid,$teachid) ;
 			// var_dump($classarr);exit();

 			$return = ['baseinfo'=>$baseinfo,'teachlable'=>$lablearr,'selectedid'=>$selectedid,'timeavailable'=>$resttime] ;
 			return return_format($return,0,'OK') ;
 		}

 	}
	//对象转数组
	function object2array($object) {
  if (is_object($object)) {
    foreach ($object as $key => $value) {
      $array[$key] = $value;
    }
  }
  else {
    $array = $object;
  }
  return $array;
}
	/**
	 * [dealArray 将数组合并分层]
	 * @Author wangwy
	 * @DateTime 2018-04-19T14:08:12+0800
	 * @param    [array]    $arr    [description]
	 * @param    [string]   $father [description]
	 * @return   [array]            [description]
	 */
	public function dealArray($arr,$father){
		if(empty($arr)) return [] ;

		$temp  = [] ;
		$child = [] ;
		foreach ($arr as $key => $val) {
			if($val[$father]==0){
				$temp[$val['id']] = ['name'=>$val['tagname']];
			}else{
				//删除不需要的字段
				unset($val['addtime']) ;
				unset($val['status']) ;

				$child[$val[$father]][] = $val ;
			}
		}
		foreach ($temp as $key => &$val) {
			$val['list'] =  isset($child[$key]) ? $child[$key] : [] ;
		}
		return $temp ;
	}
	// /**
	//  * [addTeacherMsg 添加教师数据]
	//  * @Author wangwy
	//  * @DateTime 2018-04-19T18:43:14+0800
	//  * @param    [array]  $data    [发送过来的数据]
	//  * @param    [array]  $organid [session中获取机构id]
	//  */
	// public function addTeacherMsg($data,$organid){
	// 	$data['image'] =  'uarl' ;
 //        $data['mobile'] =  '123123123' ;
 //        $data['nickname'] =  '12' ;
 //        $data['truename'] =  '' ;
 //        $data['sex'] =  0 ;
 //        $data['country'] = 23  ;
 //        $data['province'] =  13 ;
 //        $data['city'] =  56 ;
 //        $data['birth'] =  '2018-3-5' ;
 //        $data['profile'] =  'profile'  ;
 //        $data['password'] =  '213'  ;
 //        $data['repassword'] =  'dd' ;
 //        $data['status'] =  2 ;
 //        $data['prphone'] =  '+86' ;

 //        $allowfield = ['imageurl','mobile','nickname','truename','sex','country','province','city','birth','profile','password','status','teacherid','prphone'];
 //        //过滤 多余的字段
 //        $newdata = where_filter($data,$allowfield) ;
 //        $newdata['organid'] =  $organid ;

	// 	//创建验证器规则
	// 	$teachobj = new TeacherInfo ;
	// 	$return = $teachobj->addTeacher($newdata);
	// 	var_dump($return) ;
	// }

	/*
	 * [updateTeacherMsg 更新教师数据]
	 * @Author wyx
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 * @param    [array]  $organid [session中获取机构id]
	 */
	public function updateTeacherMsg($data){
	    $allowfield = ['imageurl','nickname','truename','sex','country','province','city','birth','profile','teacherid','organid'];
			//过滤 多余的字段
	    $newdata = where_filter($data,$allowfield);
	    if(!empty($newdata['province'])){
            $newdata['city'] = isset($newdata['city'])?$newdata['city']:'';
		}
	    $teachobj = new \app\appteacher\model\TeacherInfo;
	    $return = $teachobj->updateTeacher($newdata);
	    if(isset($return)){
	    	return return_format($return,0,lang('success'));
	    }else{
		    return $return ;
	    }

	}
	/*
	 * [updateTeacherMsg 更新app教师端头像]
	 * @Author wyx
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 * @param    [array]  $organid [session中获取机构id]
	 */
	public function uploadHeadimg($data){
		//更新
        $allfile = new \Upload;
        $allfiles = $allfile->getUploadFiles($data,1,$data['organid']);
        if($allfiles['code'] != 0){
        	return $allfiles;
        }
		$data['imageurl'] = $allfiles['data']['data']['source_url'];
        
        if (isset($allfiles)) {
        	# 上传成功后,将地址插入数据库
			$cc = new TeacherInfo;
        	$res = $cc->updateTeacherimg($data);
        	return return_format($data['imageurl'],0,lang('success'));
        }else{
            # 上传失败
            return return_format('',20501,lang('20501'));
        }
	}


	// /**
	//  * [delTeacher 伪删除教师信息]
	//  * @Author
	//  * @DateTime 2018-04-20T09:57:00+0800
	//  * @param    [int]       $organid   [description]
	//  * @param    [int]       $teacherid [description]
	//  * @return   [type]                 [description]
	//  */
	// public function delTeacher($organid,$teacherid){
	// 	if($organid>0 && $teacherid>0){
	// 		$teachobj = new Teacherinfo ;
	// 		$return = $teachobj->delTeacher($organid,$teacherid);

	// 		if($return){
	// 			return return_format($return,0,'操作成功');
	// 		}else{
	// 			return return_format($return,-1,'操作失败');
	// 		}

	// 	}else{
	// 		return return_format('',-1,'参数异常');
	// 	}
	// }
	/**
	 * 获取教师详细信息
	 * @Author wangwy
	 * @param $organid 机构id从session 中获取
	 * @return object
	 *
	 */
	public function getTeachLable($organid){
		//教师标签
		$organid = 2;
		$lablemodel = new Teacherlable;
		$lablearr = $lablemodel->getLableList($organid);
		$idarr    = array_column($lablearr, 'id');
		//获取所有 标签值个数
		$temparr = [] ;
		$lablevaluenum = $lablemodel->getValueNum($idarr);
		foreach ($lablevaluenum as $lable) {
			$temparr[$lable['fatherid']] = $lable['num'];
		}
		foreach ($lablearr as &$value) {
			$value['num'] = empty($temparr[$value['id']]) ? 0 : $temparr[$value['id']] ;
			$value['strdate'] = date('Y-m-d',$value['addtime']) ;
		}
		return $lablearr ;

	}
	/**
	 * 添加 标签
	 * @Author wangwy
	 * @param $organid 机构id从session 中获取
	 * @param $tagname 标签名称
	 * @return array
	 *
	 */
	public function addLable($organid,$tagname){
		//教师标签
		$lablemodel = new Teacherlable;
		$retarr = $lablemodel->addLable($organid,$tagname);
		return $retarr ;

	}
	/**
	 * 添加教师 标签
	 * @Author wangwy
	 * @param $lablename 标签名称
	 * @param $lableid   要被更新的标签的id
	 * @return array
	 *
	 */
	public function updateLable($lablename,$lableid){
		//教师标签
		$where = ['id'=>$lableid] ;
		$data  = ['tagname'=>$lablename] ;

		$lablemodel = new Teacherlable;
		$retarr = $lablemodel->updateLable($where,$data);
		return $retarr ;

	}
	/**
	 * 删除教师 标签
	 * @Author wangwy
	 * @param $organid   所属机构id
	 * @param $lableid   要被更新的标签的id
	 * @param $enforce=0 默认不强制删除
	 * @return array
	 *
	 */
	public function deleteLable($organid,$lableid,$enforce=0){

		if($enforce!=0){//强制删除
			$lablemodel = new Teacherlable;
			$retflag = $lablemodel->deleteLable($organid,$lableid) ;
			return return_format('',0,'处理成功') ;
		}else{
			//判断 标签是否已经有教师在使用
			$lablerelate = new Teachertagrelate;
			$usestatus = $lablerelate->findLableUsing($organid,$lableid);
			if(empty($usestatus)){//如果没有使用 可以删除
				//教师标签
				$lablemodel = new Teacherlable;
				$retflag = $lablemodel->deleteLable($organid,$lableid);

				return return_format($retflag,0,'处理成功') ;
			}else{//删除失败 标签正在使用
				return return_format('',-1,'标签正在被使用') ;
			}

		}

	}
	/**
	 * [getValueList 获取标签值列表]
	 * @Author wangwy
	 * @DateTime 2018-04-18T16:30:12+0800
	 * @param 需要lableid  根据标签id 获取他的子级
     * @param 需要organid  机构类别id
	 * @return   [type]                   [description]
	 */
	public function getValueList($organid,$lableid){
		$lablemodel = new Teacherlable;
		return $lablemodel->getValueList($organid,$lableid);

	}
	/**
	 * [exchangeSort 交换两个标签的位置]
	 * @Author wangwy
	 * @DateTime 2018-04-18T16:30:12+0800
	 * @param  $organid   机构类别id
	 * @param  $lableid1  根据标签id 获取他的子级
     * @param  $lableid2  机构类别id
	 * @return   [type]                   [description]
	 */
	public function exchangeSort($organid,$lableid1,$lableid2){
		if($organid>0 && $lableid1>0 && $lableid2>0 && $lableid1!=$lableid2){
			$lablemodel = new Teacherlable;
			return $lablemodel->exchangeSort($organid,[$lableid1,$lableid2]);

		}else{
			return return_format('',-1,'要求要交换的两个数据id有效') ;
		}

	}
	/**
	 * [removeLableVal 伪删除将删除标记置为1 使用父级的处理函数]
	 * @Author wangwy
	 * @DateTime 2018-04-18T16:30:12+0800
	 * @param  $organid   机构类别id
	 * @param  $lableid   根据标签id
	 * @return   [type]                   [description]
	 */
	public function removeLableVal($organid,$lableid){
		if($organid>0 && $lableid>0 ){
			$lablemodel = new Teacherlable;
			return $lablemodel->deleteLable($organid,$lableid);

		}else{
			return return_format('',-1,'要求要交换的两个数据id有效') ;
		}

	}

	/**
	 * 编辑教师资料时获取教师的信息
	 * @Author wyx
	 * @param $teachid 教师id
	 * @param $organid 机构id从session 中获取
	 * @param $allaccountid 机构人员总表id 从session 中获取
	 * @return object
	 *
	 */
	public function getTeachMsg($teachid,$organid){
		$techmodel = new TeacherInfo;
		//返回教师基本信息
		$field = 'teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus,sex,profile,birth,country,province,city' ;
		$baseinfo = $techmodel->getTeacherData($field,$teachid,$organid);

		!empty($baseinfo['birth']) &&	$baseinfo['birth'] = date('Y-m-d',$baseinfo['birth']);

		if( empty($baseinfo) ){//没有符合条件的教师数据
			return return_format([],0,lang('success')) ;
		}else{
			//返回教师的信息
			return return_format($baseinfo,0,lang('success')) ;
		}

	}


	/**
	 * 修改手机号发送手机验证码
	 * @Author wangwy
	 * @param $mobile
	 * @param $prphone
	 * @param $organid
	 * @return array
	 *
	 */
	public function sendUpdatemobileMsg($newmobile,$prphone,$organid)
	{

			//先判断手机号长度
			if(strlen($newmobile)<6 || strlen($newmobile)>12 || !is_numeric(rtrim($newmobile))){
					return return_format($this->str,20517,lang('20517'));
			}else{
					$teachermodel = new TeacherInfo;
					$data = $teachermodel ->checkLogin($newmobile);
					if($data){
							return return_format($this->str,20518,lang('20518'));
					}else{
							$mobile_code = rand(100000,999999);
							//此处调用短信接口,发送验证码
							$messageobj = new \Messages;
							$send_result = $messageobj->sendMeg($newmobile,$type=4,$params = [$mobile_code,'10'],$prphone);
							$cachedata = Cache::get('moblie'.$newmobile);
							if($send_result['result'] == 0){
									return return_format([],0,lang('success'));
							}else{
									Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
									return return_format([],20519,lang('20519'));
							}
					}

			}

	}
	/**
	 * 修改密码号发送验证码
	 * @Author wangwy
	 * @param $mobile
	 * @param $prphone
	 * @param $organid
	 * @return array
	 *
	 */
	public function sendUpdatePassMsg($newmobile,$prphone,$organid)
	{

			//先判断手机号长度
			if(strlen($newmobile)<6 || strlen($newmobile)>12 || !is_numeric(rtrim($newmobile))){
					return return_format($this->str,20520,lang('20520'));
			}else{
					$teachermodel = new TeacherInfo;
					$data = $teachermodel ->checkLogin($newmobile);

							$mobile_code = rand(100000,999999);
							//此处调用短信接口,发送验证码
							$messageobj = new \Messages;
							$send_result = $messageobj->sendMeg($newmobile,$type=4,$params = [$mobile_code,'10'],$prphone);
							$cachedata = Cache::get('moblie'.$newmobile);
							if($send_result['result'] == 0){
									return return_format([],0,lang('success'));
							}else{
									Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
									return return_format([],20521,lang('20521'));
							}

			}

	}



	/**
	 * 修改手机号
	 * @Author yr
	 * @param    [string]              oldmobile  必填原有手机号
	 * @param    [string]              code     验证码
	 * @param   [string]               uniqid    tokenid
	 * @param   [int]                  organid 机构id
	 * @param   [int]                   newmobile  新手机号
	 * @param   [int]                   teacherid   用户Id
	 * @return array
	 *
	 */
	public function updateMobile($oldmobile,$newmobile,$code ,$organid,$teacherid,$prphone){
			if( empty($code) || empty($newmobile)|| !is_intnum($organid)){
					return return_format($this->str,20502,lang('20502'));
			}
			if(strlen($newmobile)>12 || strlen($newmobile)<6){
					return return_format($this->str,20503,lang('20503'));
			}
			//判断验证码是否正确
			$cachedata = Cache::get('mobile'.$newmobile);
			if(empty($cachedata)){
					return return_format($this->str,20504,lang('20504'));
			}
			if(trim($cachedata) !== trim($code)){
					return return_format($this->str,20505,lang('20505'));
			}
			$teachermodel = new TeacherInfo;
			$accountmodel = new Allaccount;
			$oldpassworda = $teachermodel->checkLogin($oldmobile);//teacherinfo表
			$oldpasswordb = $accountmodel->checkLogin($oldmobile);//allaccount表
			if(empty($oldpassworda) && empty($oldpasswordb)){
					return return_format($this->str,20506,lang('20506'));
			}elseif($oldmobile == $newmobile){
					return return_format($this->str,20507,lang('20507'));
			}else{
					//$prphone = '\''.'+'.$prphone.'\'';
					$result = $teachermodel->updateMobile($teacherid,$newmobile,$prphone);
					$resultaccount = $accountmodel->updateMobile($teacherid,$newmobile,$prphone);
					if(isset($result) && isset($resultaccount)){
							Cache::rm($newmobile);
							return return_format($this->str,0,lang('success'));
					}else{
							return return_format($this->str,20508,lang('20508'));
					}
			}


	}



	/**
	 * 修改密码
	 * @Author yr
	 * @param    [string]              mobile  必填手机号
	 * @param    [string]              code     验证码
	 * @param   [string]               uniqid    tokenid
	 * @param   [int]                  organid 机构id
	 * @param   [string]               newpass   新密码
	 * @return array
	 *
	 */
	 public function updatePassword($mobile,$code,$organid,$newpass,$repass){
 			if(empty($mobile) || empty($code) || empty($newpass)|| !is_intnum($organid)||empty($repass)){
 					return return_format($this->str,20509,lang('20509'));
 			}
 			if(trim($newpass) !== trim($repass)){
 					return return_format('',20510,lang('20510'));
 			}
 			if(strlen($newpass)>12 || strlen($newpass)<6){
 					return return_format($this->str,20511,lang('20511'));
 			}
 			//判断验证码是否正确
 			$cachedata = Cache::get('mobile'.$mobile);
 			if(empty( $cachedata)){
 					return return_format($this->str,20512,lang('20512'));
 			}
 			if(trim($cachedata) !== trim($code)){
 					return return_format($this->str,20513,lang('20513'));
 			}
 			$teachermodel = new TeacherInfo;
			$accountmodel = new Allaccount;
			$createArr = new \login\Authorize;
 			$info = $teachermodel->checkLogin($mobile);
			$infoaccount = $accountmodel->checkLogin($mobile);
 			$encryptpass = $createArr->createUserMark($newpass);
 			// $mix = $encryptpass['mix'];
 		  $password = $encryptpass['password'];
 			if(empty($info) && empty($infoaccount)){
 					return return_format($this->str,20514,lang('20514'));
 			}elseif(trim($infoaccount['password']) == trim($password)){
 					return return_format($this->str,20515,lang('20515'));
 			}else{
 					$result = $accountmodel->updateTeacherPass($encryptpass,$mobile);
 					if($result){
 							Cache::rm('mobile'.$mobile);
 							return return_format($this->str,0,lang('success'));
 					}else{
 							return return_format($this->str,20516,lang('20516'));
 					}
 			}
 	}

	/**
	 *在未登录的情况下，重新设置密码
	 * @Author wangwy
	 * @param 使用teacherid 做查询
	 * @return
	 * URL:/teacher/teacher/updateWeekIdle
	 */
	 public function updateTeacherPass($mobile,$code,$domain,$newpass){
		 if (is_numeric($domain)) {
			 $organid = $domain;
		 }else{
			 $organ = new Organ;
			 $organidarr = $organ->getOrganid($domain);
			 $organid = $organidarr['id'];
		 }
		 if(empty($mobile) || empty($code) || empty($newpass)|| !is_intnum($organid)){
				 return return_format($this->str,20522,lang('20522'));
		 }
		 if(strlen($newpass)>12 || strlen($newpass)<6){
				 return return_format($this->str,20523,lang('20523'));
		 }
		 //判断验证码是否正确
		 $cachedata = Cache::get('mobile'.$mobile);
		 if(empty( $cachedata)){
				 return return_format($this->str,20524,lang('20524'));
		 }
		 if(trim($cachedata) !== trim($code)){
				 return return_format($this->str,20525,lang('20525'));
		 }
		 $teachermodel = new TeacherInfo;
		 $accountmodel = new Allaccount;
		 $createArr = new \login\Authorize;
		 $info = $teachermodel->checkLogin($mobile);
		 $infoaccount = $accountmodel->checkLogin($mobile);
		 $encryptpass = $createArr->createUserMark($newpass);
		 // $mix = $encryptpass['mix'];
		 $password = $encryptpass['password'];
		 if(empty($info) && empty($infoaccount)){
				 return return_format($this->str,20526,lang('20526'));
		 }elseif(trim($infoaccount['password']) == trim($password)){
				 return return_format($this->str,20527,lang('20527'));
		 }else{
				 $result = $accountmodel->updateTeacherPass($encryptpass,$mobile);
				 if($result){
						 Cache::rm('mobile'.$mobile);
						 return return_format($this->str,0,lang('success'));
				 }else{
						 return return_format($this->str,20528,lang('20528'));
				 }
		 }
	 }

	 /**获取教师可预约时间
	 *Author Wangwy
	 *
	 */
     public function getTeachertime($teachid,$organid){
		//获取一对一可预约时间
		$timeobj = new Teachertime;
		$resttime = $timeobj->findWeekMark($organid,$teachid);
		if($resttime){
			return return_format($resttime,0,lang('success'));
		}else{
			return return_format('',0,lang('success'));
		}
     }




}



?>
