<?php


namespace app\teacher\business;
use app\teacher\model\Period;
use app\teacher\model\OrderManage;
use app\teacher\model\Scheduling;
use app\teacher\model\ToteachTime;
use app\teacher\model\Curriculum;
use app\teacher\model\TeacherInfo;
use app\teacher\model\TeacherLable;
use app\teacher\model\Teachertime;
use app\teacher\model\Filemanage;
use app\teacher\model\Lessons;
use app\teacher\model\Playback;
use app\teacher\model\Coursecomment;
use app\teacher\model\Category;
use app\teacher\model\Classroom;
use app\teacher\model\Organ;
use app\teacher\model\StudentInfo;
use app\admin\business\Docking;
use app\teacher\model\StudentAttendance;
use app\teacher\model\ApplyschedulingRecord;
use app\teacher\model\ApplylessonsRecord;
use think\Db;
use think\log;
use think\Config;
use Calendar;
use think\Cache;
class CurriculumModule
{
   protected $foo;
   protected $classbeforetime = 1800;//开课前多少秒钟
   protected $classendtime = 600;//课程结束后多少秒钟

    /**
     * 获取课时详情
     * @Author wangwy
     * @param $data       包括lessons表的主键id，开始时间结束时间
     * @return array
     */

	public function getPeriodList($data){
        $period = new ToteachTime;
        $whe = ['c.id'=>$data['toteachtimeid'],'c.delflag'=>1];
        #获取了c.type,t.courseware,t.periodname,c.schedulingid
		$listb = $period->getteachtime($whe);

		# 获取了curriculumid,gradename
		$perioda = new Scheduling;
		$lista = $perioda->getPeriodinfo($data['teacherid'],$listb['schedulingid']);

		#获取了imageurl,coursename,subhead,generalize
		$where=['id'=>$lista['curriculumid']];
		//$whereth = ['c.allaccountid'=>$data['teacherid']];
		$periodc = new Curriculum;
		$listc = $periodc->getCurriinfo($where);

		//获取课件列表
        $ware = self::getWarefile($data);

  		//$datearr = explode('-',$date);
        $strdate = strtotime($data['date']);
        $day = date('Y-m-d');
        $strday = strtotime($day);//当前时间
        $time = date('Y-m-d H:i:s');
        //当前时间转成unitx时间戳
        $strtime = strtotime($time);
        $strstart = strtotime($data['starttime']);
        $strend = strtotime($data['endtime']);

         //数据汇总
        $listPeriodinfo['coursename'] = $listc['coursename'];
        $listPeriodinfo['courseimage'] = $listc['imageurl'];
        $listPeriodinfo['generalize'] = $listc['generalize'];
        $listPeriodinfo['periodname'] = $listb['periodname'];
        $listPeriodinfo['type'] = $listb['type'];
        $listPeriodinfo['gradename'] = $lista['gradename'];
        $listPeriodinfo['subhead'] = $listc['subhead'];
        $listPeriodinfo['curriculumid'] = $lista['curriculumid'];
        //$listPeriodinfo['generalize'] = $listc['generalize'];
        // foreach ($ware as $ky => $val) {
        //     # code...
        //     $listPeriodinfo[$ky]['fileid'] = $val['fileid'];
        //     $listPeriodinfo[$ky]['filename'] = $val['filename'];
        // }
        //$listPeriodinfo['sum'] = $sum;
		//执行查询当天列表之前先进性判断时间是否过期
        if ($strtime>$strend) {
            # 当天时间未过期，但是已经上完课
            //获取回放
            $playback = self::getLessonsPlayback($data['toteachtimeid'],$data['teacherid']);
            //获取评价(分页)
            //$coursecomments = self::getperComment($whereth,$data['pagenum'],$data['pagesize']);
            $listall = array('listPeriodinfo'=>$listPeriodinfo,'playback'=>$playback,'ware'=>$ware);

        }else{
            # 当天未开始或正在开始的课程
            $listall  = array('listPeriodinfo'=>$listPeriodinfo,'ware'=>$ware);
        }
		return return_format($listall,0,lang('success') );
    }

    /** 学生出勤状态
     * @param $lessonsid
     * @param $teacherid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttendance($lessonsid,$teacherid,$pagenum,$pagesize){
	    $attendaceobj = new StudentAttendance();
	    $studobj = new StudentInfo();
	    $list['data'] = $attendaceobj->getAttendance($lessonsid,$teacherid,$pagenum,$pagesize);
        //$list['sum'] = $attendaceobj->getAttendanceCount($lessonsid,$teacherid);
        $arr = array_column($list['data']['data'],'studentid');
        $stuidarr = $studobj->getStudentnameById($arr);
        if(empty($list['data']['data'])){
            $list['allstatus'] = 0;
        }else{
            $statusnum = 0;
            foreach($list['data']['data'] as $k => $v){
                $list['data']['data'][$k]['nickname'] = isset($stuidarr[$v['studentid']])?$stuidarr[$v['studentid']]:'';
                if(isset($v['attendancestatus'])){
                    $statusnum += 1;
                }
            }
            $list['allstatus'] = $statusnum >=1?1:0;//该课时的学生的出勤是否经过教师确认（0未，1已经）
        }
	    if($list){
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }

    /** 批阅学生出勤
     * @param $data
     * @return array
     */
    public function upAttendance($data,$teacherid){
        if(!$teacherid){
            return return_format('',20403,lang('20403'))                                     ;
        }
        $attendaceobj = new StudentAttendance();
        $lessobj = new Lessons();
        $schedulingid = $lessobj->getId($data[0]['lessonsid'],'schedulingid');
        $schedulingid = $schedulingid['schedulingid'];

        //首先判断当前课时是否有出勤表
        $num = $attendaceobj->getAttendanceCount($data[0]['lessonsid'],$teacherid);//
        if($num>=1){
            $addtime = time();
            foreach ($data as $k =>$v){
                //$attendancestatus = $v['attendancestatus'];//默认是出勤
                if(isset($v['attendancestatus'])){
                    $inst = array('attendancestatus'=>$v['attendancestatus'],'addtime'=>$addtime);
                }else{
                    $inst = array('score'=>$v['score'],'comment'=>$v['comment'],'status'=>1,'addtime'=>$addtime);
                }
                $attendaceobj->upAttendance($v['studentid'],$v['lessonsid'],$inst);
            }
        }else{
            foreach ($data as $k =>$v){
                $attendaceobj->addAttendance(['lessonsid'=>$v['lessonsid'],'schedulingid'=>$schedulingid,'studentid'=>$v['studentid'],'teacherid'=>$teacherid]);
            }
            $this->upAttendance($data,$teacherid);
            die();//在自调用的同时不要忘记终止接下来的函数运行
        }
        if(isset($data[0]['attendancestatus'])){
            //修改出勤状态后直接，返回结果
            return return_format('',0,lang('success'));
        }
        //老师对课堂表现评价后，推送学生
        $msgobj = new \Messages();
        $tchobj = new TeacherInfo();
        $nick = $tchobj->getTeacherData('nickname',$teacherid);
        $nickname = $nick['nickname'];
        $content = $nickname.'老师，对您进行了点评，快去查看吧';
        $title = '课堂反馈';
        $studentarr = array_column($data,'studentid');
        $studentarr = array_unique($studentarr);
        foreach($studentarr as $k => $v){
            $contentarr[$k]['content'] = $content;
            $contentarr[$k]['userid'] = $v;
            $contentarr[$k]['title'] = $title;
            $contentarr[$k]['usertype'] = 3;//学生
            // $contentarr[$k]['externalid'] = $schedulingid;//班级id
        }
        if($msgobj->addMessagearr($contentarr,12)){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',20003,lang('20003'));
        }



    }

    /**
     * @param $data
     * @return array
     */
//    public function addAttendance($data){
////        $data = ['lessonsid'=>1,'schedulingid'=>];
//        $attenobj = new StudentAttendance();
//        if($attenobj->addAttendance($data)){
//            return return_format('',0,lang('success'));
//        }else{
//            return return_format('',20003,lang('20003'));
//        }
//    }

    /** 本课时学生列表(或该班级的学生列表)
     * @param $toteachtimeid
     * @param $pagenum
     * @param $pagesize
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function periodStulist($pagenum,$pagesize,$teacherid,$toteachtimeid = null,$schedulingid = null){
        $period = new ToteachTime;
        $applayobj = new ApplyschedulingRecord;
        $stuobj = new StudentInfo;
        if(isset($toteachtimeid)&&!isset($schedulingid)){
            $whe = ['c.id'=>$toteachtimeid,'c.delflag'=>1,'t.orderstatus'=>20];
        }elseif(!isset($toteachtimeid)&&isset($schedulingid)){
            $whe = ['c.schedulingid'=>$schedulingid,'c.delflag'=>1,'t.orderstatus'=>20];
        }
        //$studentlists = $period->getorderStudlists($whe,$pagenum,$pagesize);//获取头像，昵称等
        $studentlists = $period->showorderStudentid($whe);//获取当前订单所有学生id
        $studentlists = array_unique($studentlists);
        //到调班表中查询改班级的学生
        $reB = $applayobj->getAlschedulinglist(['newschedulingid'=>$schedulingid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reC = $applayobj->getAlschedulinglist(['oldschedulingid'=>$schedulingid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');//调走的学生数
        if(isset($toteachtimeid)&&!isset($schedulingid)){
            $rePlus = array_merge($studentlists,$reB);
            $redel  = array_merge($reC);
        }elseif(!isset($toteachtimeid)&&isset($schedulingid)){
            $applesonobj = new ApplylessonsRecord;
            $lessonsid = $period->getAllfind(['id'=>$toteachtimeid],'lessonsid');
            $lessonsid = $lessonsid['lessonsid'];
            //到调班表中查询改班级和课时的学生
            $reD = $applesonobj->getAltlessonslist(['newlessonsid'=>$lessonsid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
            $reE = $applesonobj->getAltlessonslist(['oldlessonsid'=>$lessonsid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');
            $rePlus = array_merge($studentlists,$reB,$reD);
            $redel  = array_merge($reC,$reE);
        }
        if(!empty($redel)){
            foreach ($rePlus as $k =>$v){
                if(in_array($v,$redel)){
                    array_splice($rePlus,$k,1);
                }
            }
        }
        //获取头像，昵称等
        $field = 'id,nickname,imageurl,mobile,sex,birth,country,province,city,profile';
        $studentlists = $stuobj->getAllcolumn(['id'=>['in',$rePlus]],$field,$pagenum,$pagesize);
        foreach ($studentlists['data'] as $k =>$v){
            $studentlists['data'][$k]['age'] = date('Y',time())-date('Y',$v['birth']);
        }
        //$list = array_values($studentlists);
        //$list['sum'] = $period->getorderStucount($whe);
        //$sum = count($studentlists);
        if($studentlists){
            return return_format($studentlists,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }
    /** 调班学生
     * @param $teacherid
     * @param $schedulingid
     * @param $lessonsid
     * @return array
     */
    public function getScedulingstuid($teacherid,$schedulingid){
        $orderobj = new OrderManage;
        $applayobj = new ApplyschedulingRecord;
        $reA = $orderobj->getStudenAlllist(['teacherid'=>$teacherid,'schedulingid'=>$schedulingid,'orderstatus'=>20]);
        //到调班表中查询改班级数量
        $reB = $applayobj->getAlschedulinglist(['newschedulingid'=>$schedulingid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reC = $applayobj->getAlschedulinglist(['oldschedulingid'=>$schedulingid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');//调走的学生数

        $rePlus = array_merge($reA,$reB);
        $redel  = array_merge($reC);
        foreach ($rePlus as $k =>$v){
            if(in_array($v,$redel)){
                array_splice($replus,$k,1);
            }
        }
        $receive = $rePlus;
        return $receive;
    }
    /**
     * @param $teacherid
     * @param $schedulingid
     * @param $lessonsid
     * @return array
     */
    public function getAllstuid($teacherid,$schedulingid,$lessonsid){
        $orderobj = new OrderManage;
        $applayobj = new ApplyschedulingRecord;
        $applesonobj = new ApplylessonsRecord;
        $reA = $orderobj->getStudenAlllist(['teacherid'=>$teacherid,'schedulingid'=>$schedulingid,'orderstatus'=>20]);
        //到调班表中查询改班级数量
        $reB = $applayobj->getAlschedulinglist(['newschedulingid'=>$schedulingid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reC = $applayobj->getAlschedulinglist(['oldschedulingid'=>$schedulingid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');//调走的学生数

        $reD = $applesonobj->getAltlessonslist(['newlessonsid'=>$lessonsid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reE = $applesonobj->getAltlessonslist(['oldlessonsid'=>$lessonsid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');
        $rePlus = array_merge($reA,$reB,$reD);
        $redel  = array_merge($reC,$reE);
        foreach ($rePlus as $k =>$v){
            if(in_array($v,$redel)){
                array_splice($replus,$k,1);
            }
        }
        $receive = $rePlus;
        return $receive;
    }


    /**
     * [organCourseList 课时详情中获取当前课程评价]
     * @Author wyx
     * @DateTime 2018-04-25T09:55:13+0800
     * @param    [string]                $where   [查询条件]
     * @param    [int]                   $pagenum [当前页码]
     * @param    [int]                   $pagesize [每页多少行]
     * @return   [array]                          [description]
     */
    public function getperComment($teacherid,$lessonsid,$date,$pagenum,$pagesize){
        $where = ['c.allaccountid'=>$teacherid,'c.lessonsid' => $lessonsid];
        $coursecomment = new Coursecomment;
        $coursecomments = $coursecomment->getperComment($where,$pagenum,$pagesize);
        $coursecomments['data'] = self::alltodate($coursecomments['data']);//将时间戳转换成可读
        return return_format($coursecomments,0,lang('success'));
        //      $where = ['c.allaccountid'=>$teacherid];
        //      $where = ['c.lessonsid' => $lessonsid];
        //      $where = ['c.organid'=>$organid];
        //      $strdate = strtotime($date);//被选择的日期
        //      $day = date('Y-m-d');
        //      $strday = strtotime($day);//当前时间
        //      $coursecomment = new Coursecomment;
        //      	if ($strday>$strdate){
        //            $coursecomments =         $coursecomment->getperComment($w        here,$pagenum,$pagesize);
        //            $coursecomments['d        ata'] =         self::alltodate($coursecomments['data']);//将时间戳转换成可读
        //            return return_format($coursecomments,0,'当前评价');
        //        }else {
        //          if ($strtime>$strend){
        //            $coursecomments =         $coursecomment->getperComment($w        here,$pagenum,$pagesize);
        //            $coursecomments['d        ata'] =         self::alltodate($coursecomments[        'data'])        ;//将时间戳转换成可读
        //            return return_form        at($cour        secomments,0,'当前评价');
        //          }else {
        //            retur        n return_format('',233333,'课程未结束');
        //          }
        //      }
    }
    //将时间戳转换成可读
    public function alltodate($data){
      foreach ($data as $ky => $val) {
        $data[$ky]['addtimedate'] = date('Y-m-d H:i:s',$val['addtime']);
      }
      return $data;
    }







	/**
	 * [organCourseList 获取教师课表]
	 * @Author wyx
	 * @DateTime 2018-04-25T09:55:13+0800
	 * @param    [string]                $date    [需要查询的日期]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function teachCourseList($date,$teacherid){
		if($teacherid<1) return return_format('',25003,lang('25003'));
		$datearr = explode('-',$date) ;
		if( count($datearr)!=3 ) return return_format('',23005,lang('23005'));

		if ($date&&$teacherid) {
		    $cal = new Calendar($datearr[0],$datearr[1],$datearr[2]);
		    $starttime = date('Y-m-d',$cal->starttime) ;
		    $endtime   = date('Y-m-d',$cal->endtime) ;
		    //获取指定月的星期 和 日期数组
            $calendar = $cal->array ;

            //获取每天的课节 数量信息
            $schedobj = new Toteachtime;
            $datecourse = $schedobj->teachCourseList($starttime,$endtime,$teacherid) ;
            //将日历 和 数据合并
            foreach ($calendar as $key => &$val) {
                foreach ($val as &$inner) {
                    $initarr = [] ;
                    // var_dump($inner);
                    $temp = explode('-',$inner) ;
                    $initarr['timestr'] = $inner ;
                    $initarr['year'] = $temp[0] ;
                    $initarr['month'] = $temp[1] ;
                    $initarr['day'] = $temp[2] ;
                    $initarr['num'] = isset($datecourse[$inner]) ? $datecourse[$inner] : 0 ;
			        $inner=$initarr ;
                }
            }
            //将数据结果返回
            //return $calendar ;
            return return_format($calendar,0,lang('success'));
		}else{
		    return return_format([],23006,lang('23006'));
		}

	}
	// /**
	//  * [getLessonsByDate 根据日期获取教师当天课程详情]
	//  * @Author
	//  * @DateTime 2018-04-25T14:14:00+0800
	//  * @param    [string]                $date    [要获取的日期]
	//  * @param    [int]                   $organid [机构id]
	//  * @return   [array]                          [description]
	//  */
	// public function getLessonsByDate($date,$teacherid,$organid){
	// 	if($organid<1) return return_format('',-1,'参数异常');
	// 	$datearr = explode('-',$date) ;
	// 	if( count($datearr)!=3 ) return return_format('',-1,'日期参数异常');
  //
  //
	//         // var_dump($datecourse) ;exit();
	//         //return $datecourse ;
	// 					//获取每天的课节 数量信息
	// 			        $schedobj = new Toteachtime;
	// 			        $datecourse = $schedobj->getLessonsByDate($date,$teacherid,$organid) ;
	// 			        if( empty($datecourse) ){
	// 			        	return return_format([],0,'获取数据为空');
	// 			        }else{
	// 				        //获取教师信息
	// 				        $teacharr = array_column($datecourse, 'teacherid') ;
	// 				        $teachobj = new Teacherinfo ;
	// 				        $namearr = $teachobj->getTeachernameByIds($teacharr) ;
	// 				        $conftime = [
	// 				        		1 => '8:00' ,
	// 				        		2 => '8:30' ,
	// 				        		3 => '9:00' ,
	// 				        	] ;
	// 			            $strdate = strtotime($date);
	// 			            $strday = strtotime(date('Y-m-d'));//当前日期
	// 			            $strtime =strtotime(date('s'));//当前时间秒
  //
  //
	// 				        foreach ($datecourse as $key => &$val) {
	// 				        	$val['teachername'] = $namearr[$val['teacherid']] ;
	// 				        	//计算开始时间和结束时间
	// 				        	$timearr = explode(',',$val['timekey']) ;
	// 				        	$hourarr = explode(':',$conftime[$timearr[0]]) ;
	// 				        	$datearr = explode('-',$val['intime']) ;
	// 				        	$unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]) ;
	// 				        	$unixlast = $unixtime+1800*count($timearr) ;
  //
	// 				        	// var_dump($timearr) ;exit() ;
	// 				        	$val['starttime'] = date('Y-m-d H:i:s',$unixtime) ;
	// 				        	$val['endtime']   = date('Y-m-d H:i:s',$unixlast) ;
  //
	// 			                  //判断当天日期是否过期，如果过期则返回状态0
	// 			                if ($strday>$strdate) {
	// 			                     //根据toteachtime的id获取课时信息
	// 			                    $whe = ['c.id'=>$val['id']];
	// 			                     //获取相关学生人数
	// 			                    $list = $schedobj->getStudlists($whe);
	// 			                    $sum = count($list);
	// 			                    # classstatus 字段存储状态
	// 			                    $datecourse[$key]['classstatus'] = 0;
	// 			                    $datecourse[$key]['sum'] = $sum;
  //
	// 													return return_format($datecourse,0,'ok');
  //
	// 			                }else{
	// 			                    if ($strtime>=$unixtime-300) {
	// 			                        //当天未过期则判断当前时间和开课时间相比较，时间到了开        课前5分钟以内，回进教室1
	// 			                        $datecourse[$key]['classstatus'] = 1;
	// 															return return_format($datecourse,-20009,'可以进教室');
	// 			                    }else{
	// 			                        //如果未到开课前五分钟内，则返开始2；
	// 			                        $datecourse[$key]['classstatus'] = 2;
	// 															return return_format($datecourse,-20010,'未开始');
	// 			                    }
  //
  //
	// 			                }
  //
	// 				        }
  //
  //
  //
  //       }
  //
  //
  //
	// }
    /**
	 * [getLessonsByDate 根据日期获取教师当天课程详情]
	 * @Author
	 * @DateTime 2018-04-25T14:14:00+0800
	 * @param    [int]                   $pagenum  [要获取的日期]
	 * @param    [int]                   $limit    [要获取的日期]
	 * @param    [string]                $date    [要获取的日期]
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                 [description]
	 */
	public function getLessonsByDate($pagenum,$limit,$date,$teacherid){
		if($teacherid<1) return return_format('',23007,lang('23007'));
		$datearr = explode('-',$date) ;
		if( count($datearr)!=3 ) return return_format('',23008,lang('23008'));

		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$pagenum = 1 ;
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		//获取每天的课节 数量信息
        $schedobj = new Toteachtime;
        $datecourse = $schedobj->getLessonsByDate($date,$teacherid,$limitstr) ;
        $orderobj = new OrderManage;
        if( empty($datecourse) ){
        	return return_format([],0,lang('success'));
        }else{
	        //获取教师信息
	        //$teacharr = array_column($datecourse, 'teacherid') ;
	        $currarr  = array_column($datecourse, 'curriculumid') ;
	        //$teachobj = new TeacherInfo ;
	        //$namearr = $teachobj->getTeachernameByIds($teacharr) ;
            //$namearr = $teachobj->getTeachernameById($teacherid) ;
	        //获取课程图片
	        $courseobj = new Curriculum ;
	        $imagearr  = $courseobj->getCurriculumImageById($currarr) ;
	        $strdate = strtotime($date);
	        $strday = strtotime(date('Y-m-d'));//当前日期
	        $strtime =strtotime(date('Y-m-d H:i:s'));//当前时间时分秒

	        foreach ($datecourse as $key => &$val) {
	        	//计算开始时间和结束时间
	        	$timearr = explode(',',$val['timekey']) ;
	        	$hourarr = explode(':',get_time_key($timearr[0]) );//开始
	        	$datearr = explode('-',$val['intime']) ;
	        	$unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]) ;
	        	//$unixlast = $unixtime + 1800*count($timearr) ;
                $unixlast = $unixtime + 60 * $val['classhour'] ;
	        	$val['starttime'] = date('Y-m-d H:i:s',$unixtime) ;
	        	$val['endtime']   = date('Y-m-d H:i:s',$unixlast) ;
	        	//去除前台不需要显示的字段
	        	unset($val['timekey']) ;
	        	unset($val['intime']) ;
	        	unset($val['teacherid']) ;

                //根据toteachtime的id获取课时信息

                if ($val['type'] == 1) {
                	# 一对一
                	$sum = 1;
                }else{
                	# 当该课时不为一对一时
                	$whe = ['schedulingid'=>$val['schedulingid'],'teacherid'=>$teacherid,'coursetype'=>2];
                	 //获取相关学生人数
                    $list = $orderobj->getStudenAlllist($whe);
                    $sum = count($list);
                }

                //$list = $schedobj->getStudlists($whe);
                # classstatus 字段存储状态
                $datecourse[$key]['classstatus'] = 0;
                $datecourse[$key]['statusinfo'] = '查看';
                $datecourse[$key]['sum'] = $sum;
                //合并图片数据
                $val['courseimage'] = isset($imagearr[$val['curriculumid']]) ? $imagearr[$val['curriculumid']] : '' ;
                //判断当天日期是否过期，如果过期则返回状态0
                $classbefore = $this->classbeforetime;//开课前多少分钟
                if ($strtime >= $unixtime-$classbefore && $strtime <= $unixlast ) {
                    //当天未过期则判断当前时间和开课时间相比较，时间到了开        课前5分钟以内，回进教室1
                    $datecourse[$key]['classstatus'] = 1;
                    $datecourse[$key]['statusinfo'] = '可以进教室';
                    //return return_format($datecourse,0,'可以进教室');
                }elseif($strtime > $unixlast){
                    $datecourse[$key]['classstatus'] = 0;
                    $datecourse[$key]['statusinfo'] = '查看';
                    //return return_format($datecourse,0,'查看');
                }else{
                    //如果未到开课前五分钟内，则返开始2；
                    $datecourse[$key]['classstatus'] = 2;
                    $datecourse[$key]['statusinfo'] = '未开始';
                    //return return_format($datecourse,0,'未开始');
                }
            }
            //获取 分页的总记录数
            $total = $schedobj->getLessonsByDateCount($date,$teacherid);
            $result = [
                'data'=>$datecourse,// 内容结果集
                'pageinfo'=>[
                    'pagesize'=>$limit ,// 每页多少条记录
                    'pagenum' =>$pagenum ,//当前页码
                    'total'   => $total // 符合条件总的记录数
                ]
            ] ;
	        return return_format($result,0,lang('success'));

        }

	}


	/**
     * [getWarefile 根据lessons的courseware获取该课时的课件列表]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getWarefile($data){
        $whe= ['id'=>$data['id']];
        $filemanage = new Filemanage;
        $ttt = new Lessons;
        $courseware = $ttt->getCourseware($whe);//获取课件
		if(!$courseware['courseware']) return return_format('',20080,'查询失败');
        $coursewarearr = explode('-',$courseware['courseware']);
        $data['courseware'] = $coursewarearr;
        //获取fileid，filenameid；
        $info = $filemanage->getWarefile($data);
        if($info['data']){
            // foreach ($info['data'] as $k => &$val) {
            //     //$val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
            //     $val['juniorcount'] = $val['fatherid']==0?$filemanage->getFileCount(['fatherid'=>$val['fileid'],'delflag'=>1]):0;
            // }
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',20080,'查询失败');
        }
    }

     /**
     * [addCourseware 添加lessons表的courseware字段]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function addCourseware($data,$fileid){
     	//将数组转成字符串
     	$filelist = implode("-",$fileid);
     	//将输入的数字类型转换成字符串
     	//$fileid = strval($data['fileid']);
     	$whe = ['id'=>$data['id']];
     	$list = new Lessons;
        $coursewarea = $list->getCourseware($whe);//获取课件
        $courseware = $coursewarea['courseware']."-".$filelist;
        //判断插入的课件id是否与原来的数组中的元素重复
        $strcourse = explode("-",$courseware);
        //数组去除空值
         $arr = [];
        foreach ($strcourse as $k => $v){
            if (empty($v)){
                continue;
            }
            $arr[] = $strcourse[$k];
        }
         $strcourse = $arr;//将去除空值后的数组重新赋值
         $courseware = implode('-',$strcourse);
        if (count($strcourse)!=count(array_unique($strcourse))) {
        	# 如果插入的课件出现重复则判断添加失败
        	return return_format('',23003,lang('23003'));
        }else{
        	//将filemanage表中的fileid存入lessons的courseware中；
     	    $returninfo = $list->upCourseware($whe,$courseware);
     	    if(!$returninfo){
     	     return return_format('',23004,lang('23004'));
     	    }else{
     	        //查找教室是否创建 ，如果创建则进行课件关联，否则不执行
                $toteachmodel=  new \app\admin\model\Toteachtime;
                $domodel=  new \app\admin\business\Docking;
                $region = new \app\admin\model\Organ;
                $classroom = new \app\admin\model\Classroom;
                $filemodel = new \app\admin\model\Filemanage;
                $key = $region->getOrganid()['roomkey'];
                $where = [
                    'lessonsid' => $data['id'],
                    'delflag' => 1,
                ];
                $toteacharr = $toteachmodel->getFieldByWhere($where, 'id,status');
                if($toteacharr['status'] == 1){
                    //查找教室是否创建 ，如果创建则进行课件关联，否则不执行
                    $classroomno = $classroom->getRoomId($toteacharr['id'])['classroomno'];
                    $listid = $filemodel->getIdIn(implode(',', $fileid));
                    $domodel->relateCourse($key, $classroomno, array_column($listid, 'relateid'));//关联到拓课云
                }
     	     return return_format('',0,lang('success'));
     	    }
        }

        //根据时间判断能否对课件进行更改

    }


    /**
     * [delWarefile 删除lessons表的courseware字段]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function delCourseware($data,$fileid){
     	//将数组转成字符串
     	$filelist = implode("-",$fileid);
     	//$fileid = strval($data['fileid']);
     	$whe = ['id'=>$data['id']];
     	$list = new Lessons;
        //获取课件(数组)
        $coursewarea = $list->getCourseware($whe);
        //判断插入的课件id是否与原来的数组中的元素重复

        $strcourse = explode("-",$coursewarea['courseware']);
        foreach ($strcourse as $ky => $val) {
            if(in_array($val,$fileid)){
                array_splice($strcourse,$ky,1);
            }
        }
        $courseware = implode("-",$strcourse);
        //将filemanage表中的fileid存入lessons的courseware中；
     	$returninfo = $list->upCourseware($whe,$courseware);
        if(!$returninfo){
 	        return return_format('',23015,lang('23015'));
        }else{
            //在查询到教室号后，调用拓课接口对课件进行解除关联
            $toteachmodel=  new \app\admin\model\Toteachtime;
            $domodel=  new \app\admin\business\Docking;
            $region = new \app\admin\model\Organ;
            $classroom = new \app\admin\model\Classroom;
            $filemodel = new \app\admin\model\Filemanage;
            $key = $region->getOrganid()['roomkey'];
            $where = [
                'lessonsid' => $data['id'],
                'delflag' => 1,
            ];
            $toteacharr = $toteachmodel->getFieldByWhere($where, 'id,status');
            if($toteacharr['status'] == 1){
                //在查询到教室号后，调用拓课接口对课件进行解除关联
                $classroomno = $classroom->getRoomId($toteacharr['id'])['classroomno'];
                $listid = $filemodel->getIdIn(implode(',', $fileid));
                $domodel->unrelateCourse($key, $classroomno, array_column($listid, 'relateid'));
            }
            return return_format('',0,lang('success'));
        }
    }



     /**
    * [getLessonsPlayback 根据日期获取课时回放详情]
    * @Author
    * @DateTime 2018-04-25T14:14:00+0800
    * @param    [string]                $toteachid     上课时间表id
    * @param    [int]                   $organid       [机构id]
    * @return   [array]                          [description]
    */
    public function getLessonsPlayback($toteachid,$teacherid){
        if($toteachid<1) return return_format('',-1,'参数异常');
        //实例化模型
        $playbackmodel = new Playback;
        $teachermodel = new TeacherInfo;
        $data = $playbackmodel->getVideourl($toteachid);
        if(empty($data)){
            return return_format([],0,lang('success'));
        }
        $videoinfo = [];
        foreach($data as $k=>$v){
            $videoinfo[$k]['playpath'] = $v['playpath'];
            $videoinfo[$k]['https_playpath'] = $v['https_playpath'];
            //时间戳转化为时分秒
            $videoinfo[$k]['duration'] = secToTime(ceil($v['duration']/1000));
            $videoinfo[$k]['part'] = $k+1;
        }
        //获取老师名称
        $teachername = $teachermodel->getTeacherId($teacherid,'teachername');
        $newarr['teachername'] = $teachername['teachername'];
        //获取上课时间
        if (!empty($timearr[0])) {
        	$timearr = explode(',',$data['timekey']) ;
            $hourarr = explode(':',get_time_key($timearr[0])) ;
            $datearr = explode('-',$data[0]['intime']) ;
            $unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]) ;
            $newarr['starttime'] = date('Y-m-d H:i:s',$unixtime);
        }else{
        	$newarr['starttime'] = '';
        }

        //获取课时名称
        $learnsmodel = new Lessons;
        $lessonsname = $learnsmodel->getFieldName($data[0]['lessonsid'],'periodname');
        $newarr['lessonsname'] = $lessonsname['periodname'];
        $newarr['video'] =  $videoinfo;
        return return_format($newarr,0,lang('success'));
    }


    /**
    * @课程列表
    * @Author jcr
    * @param $where 查询条件
    * @param $pagenum 每页显示行数
    * @param $limit 查询页数
    **/
    // public function getCurricukumlists($data,$pagenum){
    // 	//从scheduling中获取curriculumid
    // 	$schedul = new scheduling;
    // 	$schedulist = $schedul->getcurriid($data);
    // 	$data['curriculumid'] = '';
    // 	foreach ($schedulist as $ky => $value) {
    // 		# 将得到的curriculumid合并成整个字符串
    // 		$data['curriculumid'] .= ",".strval($value['curriculumid']);
    // 	}
    //
    // 	$data['curriculumid'] = $schedulist;
    //     $curriculum = new Curriculum();
    //     $list = $curriculum->getAdminCurriculumList($data,$pagenum);
    //     if($list['data']){
    //         $category = new Category();
    //         foreach ($list['data'] as $key => &$val){
    //             //处理分类
    //             $val['categoryname'] = $category->getCategoryName(explode('-',$val['categorystr']));
    //         }
    //         return return_format($list,0,'查询成功');
    //     }else{
    //         return return_format('',10001,$data['pagenum']==1?'查询失败':'已经没有更多数据了');
    //     }
    //     return $data;
    // }

    /**
    * @课程列表
    * @Author jcr
    * @param $where 查询条件
    * @param $pagesize 每页显示行数
    * @param $limit 查询页数
    **/
    public function getCurricukumlist($data,$pagesize){
        $curriculum = new Curriculum();
        $list = $curriculum->getAdminCurriculumList($data,$pagesize);
        if($list['data']){
            $category = new Category();
            foreach ($list['data'] as $key => &$val){
                //处理分类
                $val['categoryname'] = $category->getCategoryName(explode('-',$val['categorystr']));
            }
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
        return $data;
    }

    /**
     * [intoClassroom 进教室]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @param    [int]                   $organid       [机构id]
     * @return   [array]                          [description]
     */
    public function intoClassroom($toteachid,$teacherid){
        //进教室前先判断，班级是否已经被删除
        //实例化模型
        $classmodel = new Classroom;
        $organmodel = new Organ;
        $teacherobj = new TeacherInfo;
        $nicknamearr = $teacherobj->getNick($teacherid);
        $nickname = $nicknamearr['nickname'];
        $keyarr  = $organmodel->getRoomkey();
        $key = $keyarr['roomkey'];
        $classinfo = $classmodel->getClassInfo($toteachid);
        $toteachmodel = new Toteachtime();
        //如果当前课时的班级已经删除
        $listd = $toteachmodel->getTimeList($toteachid);
        if(empty($listd)){
            return return_format('',20002,'该班级已经被删除，无法进入教室');
        }
        //如果无法获取教室信息？则开教室
        if(empty($classinfo)){
            $obj = new Docking;
            $adminteachmodel = new \app\admin\model\Toteachtime();
            $list = $toteachmodel->getTimeList($toteachid,0);
            $obj->operateRoomInfo($list, $adminteachmodel);
            $classinfo = $classmodel->getClassInfo($toteachid);
            if(empty($classinfo)){
                return return_format('',23001,lang('23001'));
            }
        }
//        $status = $toteachmodel->getStatus($toteachid);
//        if($status == 2){
//            //如果当前状态教室已结束
//            return return_format('',23002,lang('23002'));
//        }
        $time  = time();
        $sign =  MD5($key.$time.$classinfo['classroomno'].'0');
        $userpassword = getencrypt($classinfo['chairmanpwd'],$key);
        $jumpurl = config('param.server_url');
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/cqnmjy/serial/{$classinfo['classroomno']}/username/$nickname/usertype/0/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/$jumpurl";
        $data['url'] = $url;
        return return_format($data,0,lang('success'));

    }

    /*
     * 定时任务，统计每半小时上课的老师和学生的手机号
     * @Author wangwy
     *  @return [type]
     * URL:/teacher/CurriculumMobdule/rcMobile
            */
    public function rcMobile(){
        //定时任务1：（：00和：30开始运行）每个小时中的：30和：00这两个时间的教师和学生手机号，toteachtime表主键做下标的数组形式存入redis中
        //定时任务2：（：14和：44开始运行）每个小时中的：30和：00这两个时间的前16分钟开始发短信，给短信模板留出1分钟时间
        $timestamp = mktime(date('H'),date('i'),0,date('m'),date('d'),date('Y'));//精确到分钟的时间戳
        $hrrstamp = mktime(date('H'),0,0,date('m'),date('d'),date('Y'));//当前小时开始时间，精确到小时
        switch ($timestamp){
            case $timestamp<$hrrstamp+10*60 && $timestamp>=$hrrstamp:
                $starclass = $hrrstamp + 30*60;//20分钟后开课的时间戳
                break;
            case $timestamp<$hrrstamp+20*60 && $timestamp>=$hrrstamp+10*60:
                 $starclass = $hrrstamp + 40*60;//20分钟后开课的时间戳
            break;
            case $timestamp<$hrrstamp+30*60 && $timestamp>=$hrrstamp+20*60:
                 $starclass = $hrrstamp + 50*60;//20分钟后开课的时间戳
            break;
            case $timestamp<$hrrstamp+40*60 && $timestamp>=$hrrstamp+30*60:
                 $starclass = $hrrstamp + 60*60;//20分钟后开课的时间戳
            break;
            case $timestamp<$hrrstamp+50*60 && $timestamp>=$hrrstamp+40*60:
                 $starclass = $hrrstamp + 70*60;//20分钟后开课的时间戳
            break;
            case $timestamp<$hrrstamp+60*60 && $timestamp>=$hrrstamp+50*60:
                 $starclass = $hrrstamp + 80*60;//20分钟后开课的时间戳
            break;
        }
        $year = date('Y-m-d',$timestamp);

        //$timekeyarr = 待获取;//需要开始时间
        //当前半小时内上课的课程
        $toteachobj = new ToteachTime();
        //$fileone = 'id,teacherid,studentid,intime,timekey,coursename,starttime';//一对一
        $fileother = 'id,teacherid,schedulingid,lessonsid,type,intime,timekey,coursename,starttime';
        //班型一对一的状态下，如果出现studentid则必然已经购买过了，然后预约了上课时间
        //$where = ['intime'=>['eq',$year],'delflag'=>['eq',1],'type'=>['eq',1]];
        //小班课和直播课状态下，直接查询订单
        $whe = ['intime'=>['eq',$year],'delflag'=>['eq',1],'type'=>['neq',1],'starttime'=>['eq',$starclass]];
        //$toteachA = $toteachobj->getMobileformg($where,$fileone);
        $toteachB = $toteachobj->getMobileformg($whe,$fileother);
        //获取该课时的学生的手机号
        $sudentobj = new StudentInfo();

        //非一对一情况下
        //$teacheridarrs = array_column($toteachB,'teacherid','id');
        //$schdulingidarr = array_column($toteachB,'schedulingid');
        //$teachmbils = $teacherobj->getTeacherMobile($teacheridarrs);
        //$whel = ['schedulingid'=>['in',$schdulingidarr],'coursetype'=>['eq',2],'orderstatus'=>['eq',20]];
        //$studentidBarr = $orderobj->getStudenAlllist($whel);
        //$stuidBarr = array_column($studentidBarr,'studentid');
        $arrStudid = [];
        foreach($toteachB as $k =>$v){
            $stuidBarr[$k] = $this->getAllstuid($v['teacherid'],$v['schedulingid'],$v['lessonsid']);
            $arrStudid = array_merge($arrStudid,$stuidBarr[$k]);
        }
        $arrStudids = array_column($arrStudid,'studentid');
        $arrStudids = array_unique($arrStudids);
        $nickarr = $sudentobj->getStudentnameById($arrStudids); //获取学生昵称
        $stubils = $sudentobj->getStudentMobile($arrStudids);
        $arrB =[];
        foreach($toteachB as $k => $v){
            foreach($stubils as $ky =>$vl){
                $arrB[$ky] = ['prphone'=> $vl['prphone'],
                    'mobile'=>$vl['mobile'],
                    'params'=> [$nickarr[$ky],$v['coursename']]
                ];
            }
        }
        $arrs = !isset($arrB)?[]:$arrB;
        foreach($arrs as $key => $val){
            $arrsstr[] = json_encode($val);
        }
        //将查询出来的数据写入reids队列中
        $redis = new \redis();
        $url = Config::get('cache.host');
        $pass = Config::get('cache.password');
        log::write(date('Y-m-d H:i:s',time()).'开始链接redis','log',TRUE);
        $link = $redis->connect($url,6379);
        $auth = $redis->auth($pass);

        //判断是否已经复制过数据
        //$arr = $redis->lrange('list','0','-1');
        // $ret = in_array($organid, $arr);
        // if($ret){
        //     //存在表示已经在复制队列里了
        //     return return_format('',29012,'数据已经存储在redis中，无需再次存储');
        // }
        if(!$link){
            log::write(date('Y-m-d H:i:s',time()).'redis链接失败','log',TRUE);
        }
        $arrsstr = !empty($arrsstr)?$arrsstr:[];
        $dt = '';
        //将手机信息存入redis缓存中
        foreach($arrsstr as $ky => $vl){
            $dt =$redis->lpush('list',$vl);
        }
        //$arr = $redis->lrange('list','0','-1');
        if($dt){
            log::write(date('Y-m-d H:i:s',time()).'联系信息存储成功','log',TRUE);
        }else{
            log::write(date('Y-m-d H:i:s',time()).'存储失败','log',TRUE);
        }

    }
    /*
     * 定时任务，上课前15分钟向老师和学生发短信
     * @Author wangwy
     *  @return [type]
     * URL:/teacher/CurriculumMobdule/RemindMessage
     */
    public function RemindMessage(){
        //定时任务1：（：00和：30开始运行）每个小时中的：30和：00这两个时间的教师和学生手机号，toteachtime表主键做下标的数组形式存入redis中
        //定时任务2：（：14和：44开始运行）每个小时中的：30和：00这两个时间的前16分钟开始发短信，给短信模板留出1分钟时间
        $redis = new \redis();
        $url = Config::get('cache.host');
        $pass = Config::get('cache.password');
        $link = $redis->connect($url,6379);
        $auth = $redis->auth($pass);
        //$arr = $redis->lrange('list','0','-1');
        $lenth = $redis->llen('list');
        $timeobj = new \Messages();
        $sum = 0;//发送成功次数
        $unsum = 0;//发送失败次数
        $rephone = [];
        for($i=0;$i<$lenth;$i++){
            //$ph = $redis->brpop('list',1,60);//从结尾处弹出一个值,超时时间为60s
            $ph = $redis->rpop('list');//移除并返回列表的最后一个元素。
            $ph = json_decode($ph,true);
            $sendmsg = $timeobj->sendMeg($ph['mobile'],14,$ph['params'],$ph['prphone'],'',FALSE);
            if($sendmsg){
                $sum +=1;
            }else{
                Log::write('发送短信错误:'.$sendmsg['result'].'发送提醒短信错误信息:'.$sendmsg['errmsg']);
                $unsum += 1;
                $rephone[] = $ph;
            }
        }
        if($sum == $lenth){
            //当所有的短信发送成功后，删除key
            $redis->del('list');

        }else{
            log::write(date('Y-m-d H:i:s',time()).'短信部分发送失败','log',TRUE);

        }
        usleep(500000);//微秒，调用第三方接口，需要注意频率

    }

}





 ?>
