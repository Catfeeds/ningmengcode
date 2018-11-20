<?php


namespace app\appteacher\business;
use app\teacher\model\Period;
use app\teacher\model\OrderManage;
use app\teacher\model\Scheduling;
use app\teacher\model\ToteachTime;
use app\teacher\model\Curriculum;
use app\teacher\model\TeacherInfo;
use app\teacher\model\TeacherLable;
use app\teacher\model\Teachertagrelate;
use app\teacher\model\TeacherTime;
use app\teacher\model\Filemanage;
use app\teacher\model\Lessons;
use app\teacher\model\Playback;
use app\teacher\model\Coursecomment;
use app\teacher\model\Category;
use app\teacher\model\Classroom;
use Calendar;
class CurriculumModule
{
    protected $foo;

   /*
    * 获取课时详情
    * @Author wangwy
    * @param $id         toteachtime的主键id
    * @param $data       包括lessons表的主键id，开始时间结束时间
    * @param $teacherid  排课表scheduling的teacherid
    * @param $organid    机构id
    * @param $pagenum    查询页码
    * @param $pagesize   当前页多少行
    * @param $date       输入的时间
    * @return array
    */

	public function getPeriodList($data){
		$period = new ToteachTime;
        if ($data['type'] == 1){
            //根据toteachtime的id获取课时信息(一对一时)
            $whe = ['c.id'=>$data['toteachtimeid'],'c.delflag'=>1,'t.delflag'=>1];
            //获取相关学生列表（包括人数）
            $studentlists = $period->getStudlistsapp($whe);//获取头像，昵称,地址,年龄
        }else{
            $whe = ['c.id'=>$data['toteachtimeid'],'c.delflag'=>1];
            $studentlists = $period->getStudlistforapp($whe);//获取头像，昵称,地址,年龄
        }
        //求出学生年龄
        foreach ($studentlists as $ky => $val) {
          $studentlists[$ky]['age'] = date('Y')- date('Y',$val['birth']);
        }
        //获取教师的名字
        $teachername = $period->getTeachnameforapp($whe);
        #获取了c.type,t.courseware,t.periodname,c.schedulingid
		$listb = $period->getteachtime($whe);
        $sum = count($studentlists);

		# 获取了curriculumid,gradename
		$perioda = new Scheduling;
		$lista = $perioda->getPeriodinfo($data['teacherid'],$data['organid'],$listb['schedulingid']);

		#获取了imageurl,coursename,subhead,generalize
		$where=['id'=>$lista['curriculumid'],'organid'=>$data['organid']];
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
        $listPeriodinfo['periodname'] = $listb['periodname'];
        $listPeriodinfo['type'] = $listb['type'];
        $listPeriodinfo['gradename'] = $lista['gradename'];
        $listPeriodinfo['subhead'] = $listc['subhead'];
        $listPeriodinfo['teachername'] = $teachername['teachername'];
        $listPeriodinfo['sum'] = $sum;

		//执行查询当天列表之前先进性判断时间是否过期
		if ($strday>$strdate) {
			# 当前时间已经过期，则返回该操作返回回放等
			//获取回放
           $playback = self::getLessonsPlayback($data['toteachtimeid'],$data['organid']);
            //获取评价(分页)
           //$coursecomments = self::getperComment($whereth,$data['pagenum'],$data['pagesize']);
           $listall = array('listPeriodinfo'=>$listPeriodinfo,'studentlists'=>$studentlists,'playback'=>$playback,'ware'=>$ware);

		}else{
			if ($strtime>$strend) {
				# 当天时间未过期，但是已经上完课
				//获取回放
               $playback = self::getLessonsPlayback($data['toteachtimeid'],$data['organid']);
               //获取评价(分页)
               //$coursecomments = self::getperComment($whereth,$data['pagenum'],$data['pagesize']);
               $listall = array('listPeriodinfo'=>$listPeriodinfo,'studentlists'=>$studentlists,'playback'=>$playback,'ware'=>$ware);
			// return $listall ;

			}
        else{
				# 当天未开始或正在开始的课程
				$listall  = array('listPeriodinfo'=>$listPeriodinfo,'studentlists'=>$studentlists,'ware'=>$ware);
			}
		}
    	return return_format($listall,0,lang('success') );

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
    public function getperComment($teacherid,$organid,$lessonsid,$date,$pagenum,$pagesize){
        $where = ['c.allaccountid'=>$teacherid];
        $where = ['c.lessonsid' => $lessonsid];
        $where = ['c.organid'=>$organid];
        $coursecomment = new Coursecomment;
        $coursecomments = $coursecomment->getperComment($where,$pagenum,$pagesize);
        $coursecomments['data'] = self::alltodate($coursecomments['data']);//将时间戳转换成可读
        return return_format($coursecomments,0,lang('success'));
        //      $where = ['c.allaccountid'=>$teacherid];
        //      $strdate = strtotime($date);//被选择的日期
        //      $day = date('Y-m-d');
        //      $strday = strtotime($day);//当前时间
        //        $strend = strtotime($date['endtime']);
        //      $coursecomment = new Coursecomment;
        //      	if ($strday>$strdate){
        //            $coursecomments = $coursecomment->getperComment($where,$pagenum,$pagesize);
        //            return return_format($coursecomments,0,'当前评价');
        //        }else {
        //          if ($strtime>$strend){
        //            $coursecomments = $coursecomment->getperComment($where,$pagenum,$pagesize);
        //            return return_format($coursecomments,0,'当前评价');
        //          }else {
        //            return return_format('',233333,'课程未结束');
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
	public function teachCourseList($date,$teacherid,$organid){
		if($organid<1) return return_format('',-1,'参数异常');
		if($teacherid<1) return return_format('',-1,'参数异常');
		$datearr = explode('-',$date) ;
		if( count($datearr)!=3 ) return return_format('',-1,'日期参数异常');

				if ($date&&$teacherid&&$organid) {
					$cal = new Calendar($datearr[0],$datearr[1],$datearr[2]);
					$starttime = date('Y-m-d',$cal->starttime) ;
					$endtime   = date('Y-m-d',$cal->endtime) ;
					//获取指定月的星期 和 日期数组
			        $calendar = $cal->array ;

			        //获取每天的课节 数量信息
			        $schedobj = new Toteachtime;
			        $datecourse = $schedobj->teachCourseList($starttime,$endtime,$teacherid,$organid) ;
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

							return return_format($calendar,0,'ok');
				}else{
					return return_format([],-20007,'参数不合法');
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
	 * [getLessonsByall 根据日期获取教师当天已经结束和开始]
	 * @Author
	 * @DateTime 2018-04-25T14:14:00+0800
	 * @param    [int]                   $pagenum  [要获取的日期]
	 * @param    [int]                   $limit    [要获取的日期]
	 * @param    [string]                $date    [要获取的日期]
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                          [description]
	 */
	public function getLessonsByall($date,$teacherid,$organid,$datecode,$pagenum,$timesize){
		if($organid<1) return return_format('',-1,'参数异常');
		if(!empty($date) && $datecode == 2){
		    return return_format('',29999,'获取当天数据时，无需传入时间');
        }
        $date = !empty($date)?$date:time();//当前时间戳,显示当天时间时不传入该数据
		$hour = date('Y-m-d',$date);
		$hrr = explode('-',$hour);
        $start_time = mktime( 0, 0, 0, $hrr[1],$hrr[2],$hrr[0]); //当天的起始时间(时间戳)
        $end_time   = mktime( 23, 59, 59,$hrr[1],$hrr[2],$hrr[0]);//当天的结束时间

		//获取每天的课节 数量信息
        //判断当前查询条件
        //为避免因数据不满十天而陷入死循环，我们将循环次数限制在90次
        $x = 0;
        do{
            if($datecode == 0){
                // 未开始和正在进行
                $m = $timesize-1+$x;
                $enddate = $end_time + $m*24*60*60;
                $where['tt.endtime'] = $pagenum != 1 ?['between',[$end_time+1,$enddate]]:['between',[$date,$enddate]];
                $where['tt.intime'] = ['elt',date('Y-m-d',$enddate)];
            }elseif($datecode == 1){
                //已结束
                $m = $timesize-1+$x;
                $enddate = $start_time - $m*24*60*60;
                $where['tt.endtime'] = $pagenum != 1 ?['between',[$enddate,$start_time-1]]:['between',[$enddate,$date]];
                $where['tt.intime'] = ['egt',date('Y-m-d',$enddate)];
            }elseif($datecode == 2){
                //$where['tt.endtime'] = [['lt',$end_time],['egt',$start_time],'AND'];
                $where['tt.intime'] = ['eq',date('Y-m-d',$date)];
            }
            $where['tt.teacherid']=['EQ',$teacherid];
            $where['tt.organid']=['EQ',$organid];
            $where['tt.delflag']=['EQ',1];
            $where['sk.realnum']=['EGT',1];
            $schedobj = new ToteachTime;
            $datecourse = $schedobj->getLessonsByall($where,$pagenum);
            //每次查询十天内的数据，如果数量不等于十天则再加一天，以此类推，直到满足十天
            $intimearr = array_column($datecourse['data'],'intime');
            $intimearr = array_unique($intimearr);//去重
            $x++;
        }while($datecode != 2 && count($intimearr)<$timesize && $x <= 90);
        if( empty($datecourse['data']) ){
            $fal = ['data'=>[],'pageinfo'=>['pagesize'=>0,'pagenum'=>$pagenum,'total'=>0,'pagesum'=>0,'daysum'=>0]] ;
            return return_format($fal,0,lang('success'));
        }else{
	            //获取教师信息
	        $teacharr = array_column($datecourse['data'], 'teacherid') ;
	        $currarr  = array_column($datecourse['data'], 'curriculumid') ;
            $gradearr = array_column($datecourse['data'],'schedulingid');
	        $teachobj = new TeacherInfo ;
	        $namearr = $teachobj->getTeachernameByIds($teacharr) ;

	        //获取课程图片
	        $courseobj = new Curriculum ;
	        $imagearr  = $courseobj->getCurriculumImageById($currarr) ;
            //获取班级名称
            $gradeobj = new Scheduling;
            $gradenamearr = $gradeobj->getgradename($gradearr);
	        //$strdate = strtotime($date);
	        //$strday = strtotime(date('Y-m-d'));//当前日期
	        $strtime =strtotime(date('Y-m-d H:i:s'));//当前时间时分秒
            //获取房间教室号和昵称
            $classmodel = new Classroom;
            $teacherobj = new TeacherInfo;
            $nicknamearr = $teacherobj->getNick($teacherid);
            $nickname = $nicknamearr['nickname'];//教师昵称
            //遍历toteachtime表的数据
	        foreach ($datecourse['data'] as $key => &$val) {
	            $val['teachername'] = $namearr[$val['teacherid']] ;
	            //计算开始时间和结束时间
	            $timearr = explode(',',$val['timekey']) ;
	            $hourarr = explode(':',get_time_key($timearr[0]));
	            $datearr = explode('-',$val['intime']) ;
	            $unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]) ;
                $unixlast = $unixtime + 60 * $val['classhour'] ;
	            $val['starttime'] = date('Y-m-d H:i:s',$unixtime) ;
	            $val['endtime']   = date('Y-m-d H:i:s',$unixlast) ;
	            //去除前台不需要显示的字段
	            unset($val['timekey']) ;
	            unset($val['intime']) ;
	            unset($val['teacherid']) ;
                //合并图片数据
                $val['courseimage'] = isset($imagearr[$val['curriculumid']]) ? $imagearr[$val['curriculumid']] : '' ;
                //合并班级名称
                $val['gradename'] = isset($gradenamearr[$val['schedulingid']]) ? $gradenamearr[$val['schedulingid']] : '' ;
          	    if ($strtime >= $unixtime-300 && $strtime <= $unixlast+300) {
          	        //当天未过期则判断当前时间和开课时间相比较，时间到了开课前5分钟以内，回进教室1
          	        $datecourse['data'][$key]['classstatus'] = 1;
          	        $datecourse['data'][$key]['statusinfo'] = '可以进教室';
                  	//return return_format($datecourse,0,'可以进教室');
          	    }elseif($strtime > $unixlast+300){
                    $datecourse['data'][$key]['classstatus'] = 0;
                    //return return_format($datecourse,0,'查看');
                    $datecourse['data'][$key]['statusinfo'] = '查看';
                }else{
          	        //如果未到开课前五分钟内，则返开始2；
          	        $datecourse['data'][$key]['classstatus'] = 2;
                  	//return return_format($datecourse,0,'未开始');
                    $datecourse['data'][$key]['statusinfo'] = '未开始';
                }
                $classinfo = $classmodel->getClassInfo($val['id']);
                $classroomno = !empty($classinfo['classroomno'])?$classinfo['classroomno']:'';//教室号
                $datecourse['data'][$key]['nickname'] = $nickname;
                $datecourse['data'][$key]['classroomno'] = $classroomno;
	        }
            //根据日期将所得结果分组
            // $mm = array_column('starttime',$datecourse);
            // //获取时间日期的时间戳
            // foreach ($mm['starttime'] as $ky => $vl) {
            //   $tt = explode('', $vl);
            //   $mm['starttime'][$ky] = strtotime($tt[0]);
            // }
            //$spmm = array_unique($mm['starttime']);//数组去重
            $result = [];
            foreach ($datecourse['data'] as $ky => $vl) {
              $cc = explode(' ',$vl['starttime']);
              ////$ct = strtotime($cc[0]);//日期时间戳
              ////$result['data'][$cc[0]][]= $val;
              $result['data'][$cc[0]]['time']= $cc[0];
              $result['data'][$cc[0]]['data'][]= $vl;
            }
            if($datecode == 1){
                krsort($result['data']);//按时间降序排列(键名排序)
            }else{
                ksort($result['data']);//按时间升序排列
            }
            $result['data'] = array_values($result['data']);
            $result['pageinfo'] = $datecourse['pageinfo'];
            $result['pageinfo']['pagesum'] = count($datecourse['data']);
            if($datecode != 2 ){
                $datetr = time();
                if($datecode == 0){
                    $whe['tt.endtime'] = ['egt',$datetr];
                }elseif($datecode == 1){
                    $whe['tt.endtime'] = ['lt',$datetr];
                }
                //获取所有当前条件下不分页的总天数
                $whe['tt.teacherid']=['EQ',$teacherid];
                $whe['tt.organid']=['EQ',$organid];
                $whe['tt.delflag']=['EQ',1];
                $whe['sk.realnum']=['EGT',1];
                $alltotal = $schedobj->getListDay($whe);
                $alltotal = array_unique($alltotal);
                $result['pageinfo']['daysum'] = count($alltotal);
            }else{
                $result['pageinfo']['daysum'] = '';
            }

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
        $data['courseware'] = $courseware['courseware'];
        // print_r($data['courseware']);
        // exit();
        //获取fileid，filenameid；
        $info = $filemanage->getWarefile($data);
        if($info['data']){
            foreach ($info['data'] as $k => &$val) {
                $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
                $val['juniorcount'] = $val['fatherid']==0?$filemanage->getFileCount(['fatherid'=>$val['fileid'],'delflag'=>1]):0;
                // $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
            }
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }

    /**
     * [addCourseware 添加lessons表的courseware字段]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function addCourseware($data,$fileid){
     	//将数组转成字符串
     	$filelist = implode(",",$fileid);

     	//将输入的数字类型转换成字符串
     	//$fileid = strval($data['fileid']);
     	$whe = ['id'=>$data['id']];
     	$list = new Lessons;
        $coursewarea = $list->getCourseware($whe);//获取课件
        $courseware = $coursewarea['courseware'].",".$filelist;
        //判断插入的课件id是否与原来的数组中的元素重复
        $strcourse = explode(",",$courseware);
        if (count($strcourse)!=count(array_unique($strcourse))) {
        	# 如果插入的课件出现重复则判断添加失败
        	return return_format('',20201,lang('20201'));
        }else{
        	//将filemanage表中的fileid存入lessons的courseware中；
     	    $returninfo = $list->upCourseware($whe,$courseware);
     	    if(!$returninfo){
     	     return return_format('',20202,lang('20202'));
     	    }else{
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
     	$filelist = implode(",",$fileid);
     	//$fileid = strval($data['fileid']);
     	$whe = ['id'=>$data['id']];
     	$list = new Lessons;
     	//获取课件(数组)
        $coursewarea = $list->getCourseware($whe);
        //判断插入的课件id是否与原来的数组中的元素重复

        $strcourse = explode(",",$coursewarea['courseware']);
        foreach ($strcourse as $ky => $val) {
          if(in_array($val,$fileid)){
            unset($strcourse[$ky]);
          }
        }
        $courseware = implode(",",$strcourse);
        //将filemanage表中的fileid存入lessons的courseware中；
     	$returninfo = $list->upCourseware($whe,$courseware);
        if(!$returninfo){
 	     return return_format('',20203,lang('20203'));
        }else{
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
    public function getLessonsPlayback($toteachid){
        if($toteachid<1) return return_format('',-1,'参数异常');
        //实例化模型
        $playbackmodel = new Playback;
        $teachermodel = new TeacherInfo;
        $data = $playbackmodel->getVideourl($toteachid);
        if(empty($data)){
            return return_format($this->foo,'30311','没有数据');
        }
        foreach($data as $k=>$v){
            $videoinfo[$k]['playpath'] = $v['playpath'];
            $videoinfo[$k]['https_playpath'] = $v['https_playpath'];
            //时间戳转化为时分秒
            $videoinfo[$k]['duration'] = secToTime(ceil($v['duration']/1000));
            $videoinfo[$k]['part'] = $k+1;
        }
        //获取老师名称
        $teachername = $teachermodel->getTeacherId($data['teacherid'],'teachername');
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
        $lessonsname = $learnsmodel->getFieldName($data['lessonsid'],'periodname');
        $newarr['lessonsname'] = $lessonsname['periodname'];
        $newarr['video'] =  $videoinfo;
        return return_format($newarr,0,'查询成功');
    }


   /*
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
   /*
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
            return return_format($list,0,'查询成功');
        }else{
            return return_format('',10001,$data['pagenum']==1?'查询失败':'已经没有更多数据了');
        }
        return $data;
    }

    /**
     * 获取课程分类
     * @Author yr
     * @param    organid  int   机构id
     * @return array
     *
     */
    public function getCategoryArr($organid)
    {
        //机构下添加分类统一类型分类
        if ( !is_intnum($organid)) {
            return return_format($this->str, 20401, lang('20401'));
        }
        $catemodel  = new Category;
        $catelist = $catemodel->getCategory($organid);
        if(empty($catelist)){
            return return_format('',20402,lang('20402'));
        }
        //判断该机构添加的分类是那种类型
        $rank2 = '';
        $rank3 = '';
        foreach($catelist as $k=>$v){
            if($v['rank'] == 2){
                $rank2++;
            }elseif($v['rank'] == 3){
                $rank3++;
            }
        }
        if($rank2<=0){
            $data['scene'] = 1;
        }elseif($rank3>0){
            $data['scene'] = 3;
        }else{
            $data['scene'] = 2;
        }
        $data['categorydata'] = generateTree($catelist,'category_id');
        return return_format($data,0,lang('success'));
    }

    /**
     * 获取课程分类下的班级
     * @Author yr
     * @param    organid  int   机构id
     * @param    fatherid  int   父级id
     * @param    categoryid  int   分类id
     * @return array
     *
     */
    // public function searchByCids($organid,$fatherid,$categoryid,$tagids,$pagenum,$limit){
    //     if(!is_intnum($organid) || !is_intnum($fatherid) || !is_intnum($categoryid)){
    //         return return_format('',33101,'参数错误');
    //     }
    //     $tagids = isset($tagids)?$tagids:0;
    //     $pagenum = isset($pagenum)?$pagenum:0;
    //     //判断分页页数
    //     if($pagenum>0){
    //         $start = ($pagenum - 1 ) * $limit ;
    //         $limitstr = $start.','.$limit ;
    //     }else{
    //         $start = 0 ;
    //         $limitstr = $start.','.$limit ;
    //     }
    //     $schedumodel = new  Scheduling;
    //     $categorymodel = new Category;
    //     //查询该分类下的所有同级分类
    //     $catearr = $categorymodel->getChildList($organid,$fatherid);
    //     //查询该分类下的所有班级
    //     if($categoryid == 0){
    //         $categoryid = array_column($catearr,'category_id');
    //         $categoryid = implode(',',$categoryid);
    //     }
    //     $scheduarr  = $schedumodel->getFilterCourserList($organid,$categoryid,$tagids,$limitstr);
    //     $total = $schedumodel->getFilterCourserCount($organid,$categoryid,$tagids);
    //     //分页信息
    //     $data['pageinfo'] = [
    //         'pagesize'=>$limit ,// 每页多少条记录
    //         'pagenum' =>$pagenum ,//当前页码
    //         'total'   => $total // 符合条件总的记录数
    //     ];
    //     $data['schedulist'] = $scheduarr;
    //     $data['catearr'] = $catearr;
    //     if(empty($scheduarr)){
    //         return return_format($data,0,'数据为空');
    //     }else{
    //         return return_format($data,0,'查询成功');
    //     }
    // }

    public function searchByCids($type,$teacherid,$organid,$fatherid,$categoryid,$tagids,$pagenum,$limit){
        if(!is_intnum($organid) || !is_intnum($fatherid) || !is_intnum($categoryid)){
            return return_format('',20403,lang('20403'));
        }
        $tagids = isset($tagids)?$tagids:0;
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }

        $currimodel = new Curriculum;
        $categorymodel = new Category;
        $catearr = $categorymodel->getChildList($organid,$categoryid);
        if(empty($catearr)){
            //查询出所有平级下的分类
            $catearr = $categorymodel->getChildList($organid,$fatherid);
        }
        //查找该分类下的所有子分类
        $categorylist = $categorymodel->get_category($categoryid);
        $categoryids = rtrim($categorylist,',');

        if(empty($tagids)){
            //查询该分类下的所有课程
            $curriarr = $currimodel -> getFilterCourserApp($organid,$categoryids,$limitstr);
            //$scheduarr  = $schedumodel->getFilterCourserList($organid,$categoryids,$limitstr);
            $total = $currimodel->getFilterCourserCount($organid,$categoryids);
        }else{
            $tagid  = explode(',',$tagids);
            $sql = '';
            foreach($tagid as $k=>$v){
                if(!is_numeric($v)){
                    return return_format('',20404,lang('20404'));
                }
                $sql .= " FIND_IN_SET($v,tagids) and ";
            }
            $sql = substr($sql, 0, -4);
            $curriarr  = $currimodel->getCourserListByAll($type,$organid,$categoryids,$sql,$limitstr);
            $total = $currimodel->getCourserListByAllCount($type,$organid,$categoryids,$sql);
        }
//        如果开班类型是一对一
//        if ($type == 1) {
//            $schedumodel = new  Scheduling;
//            //获取一对一的所有课程
//            $schearr = $schedumodel->getOneClass($organid);
//            foreach ($curriarr as $ky => $val) {
//
//                if(in_array($val['id'], $schearr)){
//                  $curriarr[$ky]['selectstatus'] = 1;
//                }else{
//                  $curriarr[$ky]['selectstatus']= 0;
//                }
//            }
//        }
        $schedumodel = new  Scheduling;
        //获取所有班级的所有课程
        $schearr = $schedumodel->getAllClass($teacherid,$organid,$type);
        foreach ($curriarr as $ky => $val) {
            if(in_array($val['id'], $schearr)){
                $curriarr[$ky]['selectstatus'] = 1;
            }else{
                $curriarr[$ky]['selectstatus']= 0;
            }
        }
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['currilist'] = $curriarr;
        $data['catearr'] = $catearr;
        if(empty($curriarr)){
            return return_format($data,0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
        }
    }

    /**
     * [intoClassroom 进教室]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @param    [int]                   $organid       [机构id]
     * @return   [array]                          [description]
     */
    public function intoClassroom($toteachid,$teacherid,$organid){
        //实例化模型
        $classmodel = new Classroom;
        $organconfigmodel = new Organconfig;
        $teacherobj = new TeacherInfo;
        $nicknamearr = $teacherobj->getNick($teacherid);
        $nickname = $nicknamearr['nickname'];
        $keyarr  = $organconfigmodel->getRoomkey($organid);
        $key = $keyarr['roomkey'];
        $classinfo = $classmodel->getClassInfo($toteachid);
        $toteachmodel = new Toteachtime();
        //如果无法获取教室信息？则开教室
        if(empty($classinfo)){
            $obj = new Docking;
            $list = $toteachmodel->getTimeList($toteachid);
            $adminteachmodel = new \app\admin\model\Toteachtime();
            $obj->operateRoomInfo($list, $adminteachmodel,$organid);
            $classinfo = $classmodel->getClassInfo($toteachid);
            if(empty($classinfo)){
                return return_format('',23001,lang('23001'));
            }
        }
        $time  = time();
        $sign =  MD5($key.$time.$classinfo['classroomno'].'0');
        $userpassword = getencrypt($classinfo['chairmanpwd'],$key);
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$classinfo['classroomno']}/username/$nickname/usertype/0/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
        $data['url'] = $url;
        $data['chairmanpwd'] = $classinfo['chairmanpwd'];//教师密码
        $data['classroomno'] = $classinfo['classroomno'];//教室号
        return return_format($data,0,lang('success'));

    }

}





 ?>
