<?php
namespace app\microsite\business;
use app\student\model\Applylessonsrecord;
use app\student\model\Applyschedulingrecord;
use app\student\model\Organ;
use app\student\model\Period;
use app\student\model\Scheduling;
use app\student\model\Ordermanage;
use app\student\model\Studentinfo;
use app\student\model\Studentattendance;
use app\student\model\Teachertime;
use app\student\model\Lessons;
use app\student\model\Toteachtime;
use app\student\model\Unit;
use app\student\model\Unitdeputy;
use app\student\model\Curriculum;
use app\student\model\Teacherinfo;
use app\student\model\Playback;
use app\student\model\Coursecomment;
use app\student\model\Classroom;
use app\student\model\Organconfig;
use app\admin\business\Docking;
use Calendar;
class MicroMyCourseManage
{
    protected $foo;
    protected $str;
    protected $array;
    protected $orderstatus;
    protected $type;
    protected $date;
    //定义学生进教室时间 5分钟
    protected $time = 1800;
    //定义按钮状态  0 未开始 1进教室 2 去评价 回放 3回放
    protected $buttonstatus = [0,1,2,3];
    //定义预约或修改状态 0预约 1修改
    protected $reservebuttons = [0,1,2];
    public function __construct()
    {
        //定义空的数组对象
        $this->foo = [];
        //定义空字符串
        $this->str = '';
        //定义小班课type=1
        $this->type = 1;
        //初始化课程类型
        $this->array = ['一对一', '小课班', '大课班'];
        //已购买课程状态为2
        $this->orderstatus = 20;
    }

    /**
     * 获取已购买课程的list
     * @Author yr
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return array
     *
     */
    public function getBuyList($studentid,$coursetype,$pagenum,$limit)
    {
        if (!is_intnum($studentid) ||  !is_intnum($limit)) {
            return return_format($this->str, 33000, lang('param_error'));
        }
        //判断分页页数
        if (is_intnum($pagenum) > 0) {
            $start = ($pagenum - 1) * $limit;
            $limitstr = $start . ',' . $limit;
        } else {
            $start = 0;
            $limitstr = $start . ',' . $limit;
        }
        //实例化model
        $ordermodel = new Ordermanage;
        if($coursetype == 1){
            $where = [
                'coursetype' => 1
            ];
        }else{
            $where = [
                'coursetype' => 2
            ];
        }
        $orderlist = $ordermodel->getStudentOrder($studentid, $limitstr, $this->orderstatus,$where);
        $array = [];
        $applymodel = new Applyschedulingrecord();
        $schedumodel = new Scheduling;
        $i = 0;
        foreach($orderlist as $k=>$v){
            $findwhere = [
                'oldschedulingid' => $v['schedulingid'],
                'studentid' => $studentid,
                'status' => 1,
            ];
            $field = 'newschedulingid';
            $newid = $applymodel->findData($findwhere,$field);
            //查看是否有过调班记录
            if(!empty($newid)){
                $orderlist[$k]['ischangestatus'] = 1;
                array_push($array,$orderlist[$k]);
                $i +=1;
                //以新班级的id拼装一条新的订单记录
                $scheduinfo = $schedumodel->getCourserOne($newid['newschedulingid']);
                $orderlist[$k]['schedulingid'] = $newid['newschedulingid'];
                $orderlist[$k]['teacherid'] = $scheduinfo['teacherid'];
                $orderlist[$k]['curriculumid'] = $scheduinfo['curriculumid'];
                $orderlist[$k]['orderid'] = $v['orderid'];
                $orderlist[$k]['ordernum'] = $v['ordernum'];
                $orderlist[$k]['classname'] = $scheduinfo['gradename'];
                $orderlist[$k]['coursename'] = $scheduinfo['coursename'];
                $orderlist[$k]['ordertime'] = $v['ordertime'];
                $orderlist[$k]['amount'] = $v['amount'];
                $orderlist[$k]['originprice'] = $v['originprice'];
                $orderlist[$k]['orderstatus'] = $v['orderstatus'];
                $orderlist[$k]['coursetype'] = $v['coursetype'];
                $orderlist[$k]['teachername'] = $scheduinfo['teachername'];
                $orderlist[$k]['imageurl'] = $scheduinfo['imageurl'];
                $orderlist[$k]['subhead'] = $scheduinfo['subhead'];
                $orderlist[$k]['ischangestatus'] = 0;
                array_push($array,$orderlist[$k]);
            }else{
                $orderlist[$k]['ischangestatus'] = 0;
                array_push($array,$orderlist[$k]);
            }

        }

        //计算已学课时  上课时间大于当前时间视为已学,日期作为键返回List
       // $datearray = [];
        foreach ($array as $k => $v) {
            if($coursetype == 2){
                $array[$k]['learned'] = $this->countLearned($v['schedulingid'],$v['ordernum']);
            }
            //$date = strtotime($v['ordertime']);
           // $date = date('Y年m月d日', $date);
            //$array[$k]['date'] = $date;
        }

        //foreach ($array as $key => $v) {
        //    $datearray[$v['date']][] = $v;
        //}
        //$datearray = array_values($datearray);
       
        $total = $ordermodel->studentOrderCount($studentid,$this->orderstatus,$where);
        
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $array;
        return return_format($alllist, 0, lang('success'));
    }
    /**
     * 获取我的课时安排
     * @Author yr
     * @param    studentid int   学生id
     * @param    coursetype int   课程类型1录播课2直播课
     * @return array
     *
     */
    public function getClassSchedule($coursetype,$courseid,$schedulingid)
    {
        if(!is_intnum($coursetype)){
            return return_format($this->str,33001,lang('param_error'));
        }

        //实例化model
        $coursermodel = new Curriculum;
        //获取课程相关信息
        $where = [
            'id' =>$courseid
        ];
        $courserinfo['courser'] = $coursermodel->getCourserById($where);
        switch($coursetype){
            case 1:
                $unitmodel = new Unit;
                $lessonsmodel = new Period;
                $unitlist = $unitmodel->getUnitList($courseid);
                foreach($unitlist as $k=>$v){
                    $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid']);
                }
                break;
            case 2:
                $schedumodel = new Scheduling;
                $courserinfo['courser']['teachername'] =  $schedumodel->getCourserById($schedulingid)['teachername'];
                $unitmodel = new Unitdeputy;
                $lessonsmodel = new Lessons;
                //获取班级单元和课时
                $unitlist = $unitmodel->getUnitList($schedulingid);
                $courserinfo['courser']['processs'] = $this->countLearned($schedulingid,$ordernum=0);
                //如果班级类型是一对一 需要加studentid
                foreach($unitlist as $k=>$v){
                    $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid']);
                   /* foreach( $unitlist[$k]['period'] as $key=>$value){
                        $unitlist[$k]['period'][$key]['starttime']  = date('Y-m-d H:i:s',$value['starttime']);
                    }*/
                }
                break;
            default:
                break;
        }

        //返回结果集
        $courserinfo['unit'] = $unitlist;
        if(empty($courserinfo)){
            return return_format($courserinfo,0,lang('success'));
        }else{
            return return_format($courserinfo,0,lang('success'));
        }
    }
    /**
     * 录播课观看直播
     * @Author yr
     * @param    courseid int   课程id
     * @param    lessonsid int   课节id
     * @return array
     *
     */
    public function watchPlayback($courseid,$lessonsid,$studentid){
        if(!is_intnum($courseid) || !is_intnum($lessonsid)){
            return return_format($this->str,33001,lang('param_error'));
        }
        $periodmodel = new Period;
        $data = $periodmodel->getFileInfo($lessonsid);
        //查看学生是否评论过此课时
        $commentmodel = new Coursecomment();
        $count = $commentmodel->getLessonsCommentCount($studentid,$lessonsid);
        if($count>0){
            $data['iscomment'] = 1;
        }else{
            $data['iscomment'] = 0;
        }
        return return_format($data,0,lang('success'));
    }
	
	/**
     * 查询录播课时评论
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    lessonsid int   录播课课节id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     */
    public function getRecordComment($lessonsid,$pagenum,$limit)
    {
        //判断参数是否合法
        if(!is_intnum($lessonsid) || !is_intnum($limit)){
            return return_format($this->str,36004,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        //实例化模型
        $commentmodel = new Coursecomment;
        //查询评论信息
        $commentinfo['data'] = $commentmodel->getLessonsComment($lessonsid,$limitstr);
        $total = $commentmodel->getLessonsCount($lessonsid);
        $commentinfo['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        if(empty($commentinfo)){
            return return_format($commentinfo,0,lang('success'));
        }else{
            return return_format($commentinfo,0,lang('success'));
        }

    }
	
    /**
     * 查询约课课节信息
     * @Author yr
     * @param    curriculumid int   课程id
     * @param    organid  int   机构id
     * @param    schedulingid int   排课id
     * @return array
     *
     */
    public function getLessionsInfo($schedulingid,$studentid,$ordernum)
    {

        //实例化model
        $unitmodel = new Unitdeputy;
        $lessonsmodel = new Lessons;
        $toteachmodel = new Toteachtime;
        $unitlist = $unitmodel->getUnitList($schedulingid);
        if (empty($unitlist)) {
            return return_format('', 33003, lang('33003'));
        }
        //获取课节信息
        foreach($unitlist as $k=>$v){
            //如果是下班课大班课直接查询对应上课时间，一对一需另走方法
                $unitlist[$k]['period'] = $lessonsmodel->getLessonsByUnitid($v['unitid'],$schedulingid);
                foreach($unitlist[$k]['period'] as $key=>$value){
                    $where = [
                        'studentid'=>$studentid,
                        'lessonsid'=>$value['lessonsid'],
                        'ordernum'=>$ordernum,
                    ];
                    $unitlist[$k]['period'][$key]['teacheinfo'] =  $toteachmodel->getToteachInfo($where);
                    $unitlist[$k]['period'][$key]['timekey']  = explode(',',$unitlist[$k]['period'][$key]['teacheinfo']['timekey']);
                    $unitlist[$k]['period'][$key]['intime']  = $unitlist[$k]['period'][$key]['teacheinfo']['intime'];
                    $unitlist[$k]['period'][$key]['toteachid']  = $unitlist[$k]['period'][$key]['teacheinfo']['toteachid'];
                    if(empty($unitlist[$k]['period'][$key]['timekey'][0])){
                        $unitlist[$k]['period'][$key]['time'] = '';
                        $unitlist[$k]['period'][$key]['reservebuttons'] = $this->reservebuttons[0];
                    }else{
                        $unitlist[$k]['period'][$key]['time'] = get_time_key($unitlist[$k]['period'][$key]['timekey'][0]);
                        $time = time();
                        //查看当前时间是否大于上课时间
                        $classtime = $unitlist[$k]['period'][$key]['intime'].' '. $unitlist[$k]['period'][$key]['time'];
                        $classtime = strtotime($classtime);
                        if($time>$classtime){
                            $unitlist[$k]['period'][$key]['reservebuttons'] = $this->reservebuttons[2];
                        }else{
                            $unitlist[$k]['period'][$key]['reservebuttons'] = $this->reservebuttons[1];
                        }

                    }
                    unset($unitlist[$k]['period'][$key]['teacheinfo']);
                    unset($unitlist[$k]['period'][$key]['timekey']);
                }
            }

        return return_format($unitlist, 0, lang('success'));
    }

    /**
     * 查询老师可约课时间 老师的空余时间和学生已约课时间
     * @Author yr
     * @param    organid  int   机构id
     * @param    date     string  选择的年月日
     * @param    week     int   星期几 0代表星期天 1,2,3,4,5,6
     * @param    teacherid int   老师id
     * @return   array();
     */
    public function getFreeList($teacherid, $date)
    {
        if ( !is_intnum($teacherid)  || empty($date)) {
            return return_format($this->str, 33004, lang('param_error'));
        }
        $newarray = $this->getTeacherFreeTime($date,$teacherid);
        //返回老师可约课的时间
        if (empty($newarray)) {
            return return_format($newarray, 33005, lang('33005'));
        }
        return return_format($newarray, 0, lang('success'));

    }

    /**
     * 添加学生预约时间
     * @Author yr
     * @param    $data;
     * @return   array();
     */
    public function addEdit($data){
        $schedumodel = new Scheduling;
        if(empty($data['intime']) || empty($data['timekey'])){
            return return_format('',33006,lang('33006'));
        }
        if(empty($data['ordernum'])){
            return return_format('',33007,lang('33007'));
        }
        if(!is_intnum($data['studentid'])){
            return return_format('',33008,lang('33008'));
        }
        $scheduinfo = $schedumodel->getScheduById($data['schedulingid']);
        if(!$scheduinfo){
            return return_format('',33009,lang('33009'));
        }
        $addtime = strtotime($data['intime'].' '.get_time_key($data['timekey']));
        $nowtime = time();
        if($addtime<=$nowtime){
            return return_format('',33010,lang('33010'));
        }
        //查询出所有的单元信
        $lessonsmodel = new Lessons;
        $toteachmodel = new Toteachtime;
        $lessonslist = $lessonsmodel->getLessonsByscheduid($data['schedulingid']);
        $lessonids = array_column($lessonslist,'lessonsid');
        if(!in_array($data['lessonsid'],$lessonids)){
            return return_format('',33011,lang('33011'));
        }
        //获取该机构的一对一课时时长
  /*      $configmodel = new Organconfig;
        $configlist = $configmodel->getRoomkey($scheduinfo['organid']);*/
        $toonetime = $scheduinfo['classhour'];
        //拼装一节课所占用都得课时key
        $timekeys =  array_series($data['timekey'],$toonetime);
        $freearray =  $this->getFreeTime($data['intime'], $scheduinfo['teacherid']);
        foreach($timekeys as $k=>$v){
            if(!in_array($v,$freearray)){
                return return_format('',33012,lang('33012'));
            }
        }

        //取出所有数组
        $preid = '';
        $length  = count($lessonslist);
        foreach($lessonslist as $k=>$v){
            if($data['lessonsid'] == $v['lessonsid']){
                if($k==0){
                    //这是第一条数据
                    if($length == 1){
                        $insort = 1;
                        $sort = 2;
                    }else{
                        $insort = 1;
                        $sort = 0;
                        $afterid = $lessonslist[$k+1]['lessonsid'];
                    }

                }elseif($k == $length-1&&$length >1){
                    //这是最后一条数据
                    $insort = 2;
                    $sort = 2;
                    $preid = $lessonslist[$k-1]['lessonsid'];
                }else{
                    $insort = 0;
                    $sort = 0;
                    $preid = $lessonslist[$k-1]['lessonsid'];
                    $afterid = $lessonslist[$k+1]['lessonsid'];
                }
            }
        }
        //判断是添加还是修改
        if(!$data['toteachid']){
            //添加
            //查询出该课节的前后课节信息
            $currtime = strtotime($data['intime'].' '.get_time_key($data['timekey']));
            if($insort !==1){
                $prelist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$preid);
                if(empty($prelist)){
                    return return_format('',33013,lang('33013'));
                }
                $prelist['timekey'] = explode(',',$prelist['timekey']);
                $pretime = strtotime($prelist['intime'].' '.get_time_key($prelist['timekey'][0]));
                if($currtime<=$pretime){
                    return return_format('',33014,lang('33014'));
                }
            }
            //拼装插入条件
            $toteachmodel = new Toteachtime;
            $info['intime'] = $data['intime'];
            $info['teacherid'] = $scheduinfo['teacherid'];
            $info['coursename'] = $scheduinfo['curriculumname'];
            $info['type'] = $scheduinfo['type'];
            $info['timekey'] = implode(',',$timekeys);
            $info['lessonsid'] = $data['lessonsid'];
            $info['schedulingid'] = $data['schedulingid'];
            $info['studentid'] = $data['studentid'];
            $info['insort'] = $sort;
            $info['ordernum'] = $data['ordernum'];
            $info['endtime'] = $currtime +$toonetime*60;
            $res = $toteachmodel->addEdit($info);
            if($res['code'] == 0 ){
                return return_format('',0,lang('success'));
            }else{
                return return_format('',33015,lang('33015'));
            }
        }else {
            //修改
            $currtime = strtotime($data['intime'] . ' ' . get_time_key($data['timekey']));
            switch ($insort) {
                case 1:
                    //第一条数据
                    if(!empty($afterid)){
                        $afterlist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$afterid);
                    }
                    if (!empty($afterlist)) {
                        $afterlist['timekey'] = explode(',', $afterlist['timekey']);
                        $aftertime = strtotime($afterlist['intime'] . ' ' . get_time_key($afterlist['timekey'][0]));
                        if ($currtime >= $aftertime) {
                            return return_format('', 33016, lang('33016'));
                        }
                    }
                    break;
                case 0:
                    $prelist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$preid);
                    if (empty($prelist)) {
                        return return_format('', 33017, lang('33017'));
                    }
                    $prelist['timekey'] = explode(',', $prelist['timekey']);

                    $pretime = strtotime($prelist['intime'] . ' ' . get_time_key($prelist['timekey'][0]));
                    if ($currtime < $pretime) {
                        return return_format('', 33018, lang('33014'));
                    }
                    $afterlist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$afterid);
                    if (!empty($afterlist)) {
                        $afterlist['timekey'] = explode(',', $afterlist['timekey']);
                        $aftertime = strtotime($afterlist['intime'] . ' ' . get_time_key($afterlist['timekey'][0]));
                        if ($currtime >= $aftertime) {
                            return return_format('', 33019, lang('33016'));
                        }
                    }
                case 2:
                    $prelist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$preid);
                    if (empty($prelist)) {
                        return return_format('', 33020, lang('33013'));
                    }
                    $prelist['timekey'] = explode(',', $prelist['timekey']);
                    $pretime = strtotime($prelist['intime'] . ' ' . get_time_key($prelist['timekey'][0]));
                    if ($currtime <= $pretime) {
                        return return_format('', 33021, lang('33014'));
                    }

            }
            //拼装插入条件
            $toteachmodel = new Toteachtime;
            $info['intime'] = $data['intime'];
            $info['id'] = $data['toteachid'];
            $info['timekey'] = implode(',', $timekeys);
            $info['endtime'] = $currtime + $toonetime * 60;
            $res = $toteachmodel->addEdit($info);
            if ($res['code'] == 0) {
                return return_format('', 0, lang('success'));
            } else {
                return return_format('', 33022, lang('error'));
            }
        }
    }
    /**
     * [studentCourseList 获取学生课表]
     * @Author yr
     * @DateTime 2018-04-25T09:55:13+0800
     * @param    [string]                $date    [需要查询的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function studentLessonsList($studentid,$status,$pagenum,$limit){
		if(!is_intnum($limit) || !is_intnum($pagenum)){
            return return_format('',39138,lang('param_error'));
        }
		 $nowtime = time();
		/* if($status == 1){  //已结束
			$where = [
				'endtime' < $nowtime,
			];
		}else{
			$where = [
				'endtime' >= $nowtime,
			];
		} */
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }

        $newidarr = $this->getAllLessonsidarr($studentid);
		
        $schedobj = new Toteachtime;
		$teachobj = new Teacherinfo ;
        $lessonarr = $schedobj->getStudentLessonsList($status,$limitstr,$newidarr,$nowtime);
		if(!empty($lessonarr)){
			foreach($lessonarr as $val){
				$val['teachername'] = $teachobj->getTeacherName($val['teacherid']);
				if($nowtime>$val['endtime']){
                    //查询学生是否评价过该课程
                    $commitmodel = new Coursecomment;
                    $iscommit = $commitmodel->getCommentBylessonid($val['toteachid']);
                    if(empty($iscommit)){
                        $val['buttonstatus'] = $this->buttonstatus[2];
                    }else{
                        $val['buttonstatus'] = $this->buttonstatus[3];
                    }
                }else{
                    //如果当当前时间大于进教室时间，状态为1 剩下为 未开始时间
                    $goroomtime  =  $val['starttime'] - $this->time;
                    if($nowtime<$goroomtime){
                        $val['buttonstatus'] = $this->buttonstatus[0];
                    }else{
                        $val['buttonstatus'] = $this->buttonstatus[1];
                    }
                }
				$val['starttime'] = date('Y-m-d H:i:s', $val['starttime']);
                $val['endtime'] = date('Y-m-d H:i:s', $val['endtime']);
				$lessonarrbydate[$val['intime']]['time'] = $val['intime'];
				$lessonarrbydate[$val['intime']]['arr'][] = $val;
			}
			$data['data'] = array_values($lessonarrbydate);
			$total = $schedobj->getStudentLessonsListCount($status,$newidarr,$nowtime);
			$data['pageinfo'] = [
				'pagenum'=> $pagenum,
				'limit' => $limit,
				'total' => $total
			];
			return return_format($data,0,lang('success'));
		}else{
			return return_format([], 0, lang('success'));
		}
    }
	
	/**
	 * [getFeedback 已结束课时查看点评]
	 * @param $lessonsid 课时id
	 * @param $schedulingid 排班id
	 * @param $studentid 学生id
	 */
	public function getFeedback($lessonsid,$schedulingid,$studentid)
    {
        //判断参数是否合法
        if(!is_intnum($lessonsid) || !is_intnum($schedulingid)){
            return return_format($this->str,36004,lang('param_error'));
        }
		$where = [
			'lessonsid' => $lessonsid,
			'schedulingid' => $schedulingid,
			'studentid' => $studentid,
		];
        
        //实例化模型
        $attendancemodel = new Studentattendance;
        //查询评论信息
        $atendancetinfo = $attendancemodel->getFeedbackOne($where,'score,comment');
        if(empty($atendancetinfo)){
            return return_format('',0,lang('success'));
        }else{
            return return_format($atendancetinfo,0,lang('success'));
        }

    }
    
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @return   [array]                          [description]
     */
    public function getLessonsPlayback($toteachid){
        //实例化模型
        $playbackmodel = new Playback;
        $teachermodel = new Teacherinfo;
        $data = $playbackmodel->getVideourl($toteachid);
        if(empty($data)){
            return return_format('',33028,lang('33028'));
        }
        foreach($data as $k=>$v){
            $videoinfo[$k]['playpath'] = $v['playpath'];
            $videoinfo[$k]['https_playpath'] = $v['https_playpath'];
            //时间戳转化为时分秒
            $videoinfo[$k]['duration'] = secToTime(ceil($v['duration']/1000));
            $videoinfo[$k]['part'] = $k+1;
        }
        //获取老师名称
        $teachername = $teachermodel->getTeacherId($data[0]['teacherid'],'nickname');
        $newarr['teachername'] = $teachername['nickname'];
        //获取上课时间
        if(!empty($timearr[0])){
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
     * [intoClassroom 进教室]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @return   [array]                          [description]
     */
    public function intoClassroom($toteachid,$studentid){

        //获取用户昵称
        $studentobj = new Studentinfo;
        $studentinfo = $studentobj->getStudentInfo($studentid);
        $nickname = $studentinfo['nickname'];
        $classmodel = new Classroom;
        $organconfigmodel = new Organ();
        //获取教室key
        $keyarr  = $organconfigmodel->getRoomkey();
        $key = $keyarr['roomkey'];
        //获取教室信息
        $classinfo = $classmodel->getClassInfo($toteachid);
        $toteachmodel = new Toteachtime();
        $list = $toteachmodel->getTimeList($toteachid);
        //查看教室状态 如果教室状态等于2
     /*   $status  =  $list['status'];
        if($status == 2){
            return return_format('',33030,lang('33030'));
        }*/
        //如果获取教室信息失败，创建教室
        if(empty($classinfo)){
            $obj = new Docking;
            $adminteachmodel = new \app\admin\model\Toteachtime();
            $obj->operateRoomInfo($list, $adminteachmodel);
            $classinfo = $classmodel->getClassInfo($toteachid);
            if(empty($classinfo)){
                return return_format('',33031,lang('33031'));
            }
        }
        $time  = time();
        //必填， 0：主讲(老师 )  1：助教 2: 学员   3：直播用户  4:巡检员
        $usertype = '2';
        //，auth 值为 MD5(key + ts +serial + usertype)
        $sign =  MD5($key.$time.$classinfo['classroomno'].$usertype);
        //学生密码
        $userpassword = getencrypt($classinfo['confuserpwd'],$key);
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/cqnmjy/serial/{$classinfo['classroomno']}/username/$nickname/usertype/$usertype/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
        $data['url'] = $url;
        return return_format($data,0,lang('success'));

    }
    /**
     * [insertComment 去评论]
     * @Author
     * @DateTime 2018-04-27T14:14:00+0800
     * @param    [string]           nickname  必填用户昵称
     * @param    [int]              curriculumid  必填课程id
     * @param    [int]              type 必填课程类型
     * @param    [string]           content  必填评价内容
     * @param    [int]              studentid  必填学生id
     * @param    [int]              teacherid  必填老师id
     * @param    [int]              score  必填分数
     * @param    [int]              schedulingid 排课id
     * @return   [array]                          [description]
     */
     public function insertComment($data){
         if(!is_intnum($data['curriculumid']) || !is_intnum($data['classtype']) || !is_intnum($data['studentid']) ||!is_intnum($data['allaccountid'])  ||!is_intnum($data['schedulingid']) ){
            return return_format($this->str,33032,lang('param_error'));
         }
         if(!is_numeric($data['score']) || empty($data['score'])){
             return return_format('',33033,lang('33033'));
         }
         $studentmodel = new Studentinfo;
         $where = [
             'id' => $data['studentid']
         ];
         $field = 'nickname';
         $arr = $studentmodel->getFieldByid($where,$field);
         $data['nickname'] = $arr['nickname'];
         $commmentmodel = new Coursecomment;
         //添加成功返回新增id
         $id = $commmentmodel->add($data);
         if(is_numeric($id) || $id>0){
             return return_format($id,0,lang('success'));
         }elseif($id){
             return return_format('',33034,$id);
         }else{
             return return_format('',33035,lang('error'));
         }
     }
     
    /**
     * 计算一对一排课是否预约完成
     * @Author yr
     * @param $curriculumid int  课程id
     * @param $schedulingidint  排课id
     */
    function reserveLearned($schedulingid,$studentid,$ordernum)
    {
        //统计排课下所有的课节
        $learnsmodel = new Lessons;
        $learnlist = $learnsmodel->getLessonsNum($schedulingid);
        $learnlist = array_column($learnlist, 'learnid');
        $length = count($learnlist);
        $timemodel = new Toteachtime;
        //查询所有预约课程数量
        $reservednum = $timemodel->getClssTime($studentid,$schedulingid,$ordernum);
        if($length !== $reservednum){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * 计算已学课时
     * @Author yr
     * @param $curriculumid int  课程id
     */
    function countLearned($schedulingid,$ordernum){
        if(empty($schedulingid)){
            return '0%';
        }
        //统计排课下所有的课节
        $learnsmodel = new Lessons;
        $learnlist   = $learnsmodel->getLessonsNum($schedulingid);
        $learnlist = array_column($learnlist,'learnid');
        $length = count($learnlist);
        $timemodel  = new Toteachtime;
            //下班课，大班课已学课时
        $learndnum = $timemodel->getLessonsCount($learnlist);
        //已学除以总数
        if($learndnum == 0){
            $radio = '0%';
        }else{
            $radio = round(intval($learndnum)/$length*100);
            if($radio>=100){
                $radio = '100%';
            }else{
                $radio = $radio.'%';
            }

        }
        return $radio;
    }
    /* 查询老师可约时间
     * @Author yr
     * @param $curriculumid int  课程id
     */
    function getTeacherFreeTime($date,$teacherid){
        $week = date('w',strtotime($date));
        if($week == 0){
            $week = 7;
        }
        //实例化模型
        $teachertimemodel = new Teachertime;
        //查看老师可预约时间
        $freeinfo = $teachertimemodel->findWeekdayMark($teacherid, $week);
        $freearray = explode(',', $freeinfo['mark']);
        //查看学生占用的预约时间
        $toteahcermodel = new Toteachtime;
        $toteahcerinfo = $toteahcermodel->getDateInfo( $teacherid, $date);

        $newstr = '';
        //拼接被占用时间的字符串
        foreach ($toteahcerinfo as $k => $v) {
            $newstr .= $v['timekey'] . ',';
        }
        //如果老师空余时间存在学生占用时间的数组里，则删除
        $newarray = explode(',', rtrim($newstr, ','));
        foreach ($freearray as $k => $v) {
            if (in_array($v, $newarray)) {
                unset($freearray[$k]);
            } else {
                $freearray[$k] = get_time_key($v);
            }
        }
        $newarray = array_values($freearray);
        return $newarray;
    }
    /* 查询老师可约时间
     * @Author yr
     * @param $curriculumid int  课程id
     */
    function getFreeTime($date,$teacherid){
        $week = date('w',strtotime($date));
        if($week == 0){
            $week = 7;
        }
        //实例化模型
        $teachertimemodel = new Teachertime;
        //查看老师可预约时间
        $freeinfo = $teachertimemodel->findWeekdayMark($teacherid, $week);
        $freearray = explode(',', $freeinfo['mark']);
        //查看学生占用的预约时间
        $toteahcermodel = new Toteachtime;
        $toteahcerinfo = $toteahcermodel->getDateInfo( $teacherid, $date);

        $newstr = '';
        //拼接被占用时间的字符串
        foreach ($toteahcerinfo as $k => $v) {
            $newstr .= $v['timekey'] . ',';
        }
        //如果老师空余时间存在学生占用时间的数组里，则删除
        $newarray = explode(',', rtrim($newstr, ','));
        foreach ($freearray as $k => $v) {
            if (in_array($v, $newarray)) {
                unset($freearray[$k]);
            }
        }
        $newarray = array_values($freearray);
        return $newarray;
    }
	
	 /* 查询学生的课节集合 调课和调班
     * @Author yr
     * @param $curriculumid int  课程id
     */
    public function getAllLessonsidarr($studentid){
        $ordermodel = new Ordermanage();
        $where = [
            'studentid' => $studentid,
            'orderstatus' => 20,
        ];
        $scheduingarr = $ordermodel->getAllScheding($where);
        $applyclassmodel = new Applyschedulingrecord();
        $changeawhere = [
            'studentid' => $studentid,
            'status' => 1,
        ];
        $changefield = 'oldschedulingid';
        $changearr  = $applyclassmodel->getColumnIds($changeawhere,$changefield);//把调班的班级id剔除
        foreach($scheduingarr as $k=>$v){
            if(in_array($v,$changearr)){
                unset($scheduingarr[$k]);
            }
        }
        $scheduingarr = array_values($scheduingarr);
        $lessonmodel = new Lessons();
        $lessonwhere = [
            'schedulingid' => ['in',$scheduingarr],
            'delflag' => 1
        ];
        $lessonsids = $lessonmodel->getLessonids($lessonwhere);//未调过课的所有课时id
        //剔除所有调过课的原课时id 新增所有新的课时id
        $applylessonmodel = new Applylessonsrecord();
        $applywhere = ['studentid'=>$studentid,'status'=>1];
        $applyoldfield = 'oldlessonsid';
        $applynewfield = 'newlessonsid';
        $oldlessonsids = $applylessonmodel->getColumnIds($applywhere,$applyoldfield);//所有调过课的课时id
        $newlessonsids = $applylessonmodel->getColumnIds($applywhere,$applynewfield);//所有调过课的新的课时id
        $oldclassids = $applyclassmodel->getColumnIds($applywhere,$applynewfield);//旧班级的所有课时id集合
        $newclassids = $applyclassmodel->getColumnIds($applywhere,$applynewfield);//新班级的所有课时id集合
        //调过班的课时id追加
        if(!empty($oldclassids)){
            foreach($oldclassids as $k=>$v){
                if(!empty($v)){
                    $oldclassids[$k] = explode(',',$v);
                    foreach($oldclassids[$k] as $key=>$value){
                        array_push($lessonsids,$value);
                    }
                }
            }
        }
        //调过班的新课时id追加
        if(!empty($newclassids)){
            foreach ($newclassids as $k=>$v){
                if(!empty($v)){
                    $newclassids[$k] = explode(',',$v);
                    foreach($newclassids[$k] as $key=>$value){
                        array_push($lessonsids,$value);
                    }
                }
            }
        }
        if(!empty($oldlessonsids)){
            foreach($lessonsids as $k=>$v){
                if(in_array($v,$oldlessonsids)){
                    unset($lessonsids[$k]);
                }
            }
        }
        $newidarr = array_merge($lessonsids,$newlessonsids);
        return $newidarr;
    }
}
