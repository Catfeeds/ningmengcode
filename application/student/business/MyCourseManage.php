<?php
namespace app\student\business;
use app\student\model\Applylessonsrecord;
use app\student\model\Applyschedulingrecord;
use app\student\model\Coursepackage;
use app\student\model\Organ;
use app\student\model\Period;
use app\student\model\Scheduling;
use app\student\model\Ordermanage;
use app\student\model\Studentinfo;
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
use app\teacher\model\StudentAttendance;
use app\teacher\model\StudentHomework;
use Calendar;
class MyCourseManage
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
        $datearray = [];
        foreach ($array as $k => $v) {
            if($coursetype == 2){
                $array[$k]['learned'] = $this->countLearned($v['schedulingid'],$v['ordernum']);
            }
            $date = strtotime($v['ordertime']);
            $date = date('Y年m月d日', $date);
            $array[$k]['date'] = $date;
        }

        foreach ($array as $key => $v) {
            $datearray[$v['date']][] = $v;
        }
        $datearray = array_values($datearray);
       /* dump($orderlist);die();*/
        $total = $ordermodel->studentOrderCount($studentid,$this->orderstatus,$where);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $datearray;
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
                $scheduinfo = $schedumodel->getCourserById($schedulingid);//查询班级信息
                $courserinfo['courser']['teachername'] =  $scheduinfo['teachername'];
                $courserinfo['courser']['classname'] =  $scheduinfo['gradename'];
                $unitmodel = new Unitdeputy;
                $lessonsmodel = new Lessons;
                //获取班级单元和课时
                $unitlist = $unitmodel->getUnitList($schedulingid);
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
    public function studentCourseList($date,$studentid){
        $datearr = explode('-',$date) ;
        if( count($datearr)!=3 ) return return_format('',33024,lang('33024'));

        $cal = new Calendar($datearr[0],$datearr[1],$datearr[2]);
        $starttime = date('Y-m-d',$cal->starttime) ;
        $endtime   = date('Y-m-d',$cal->endtime) ;
        //获取指定月的星期 和 日期数组
        $calendar = $cal->array ;
        //获取学生可上的课时
        $newidarr = $this->getAllLessonsidarr($studentid);
        //查询
        //获取每天的课节 数量信息
        $schedobj = new Toteachtime;
        $datecourse = $schedobj->studentCourseDateList($starttime,$endtime,$newidarr);
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
        return return_format($calendar,0,lang('success'));

    }
    /**
     * [getLessonsByDate 根据日期获取当天课节]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getLessonsByDate($date,$studentid){
        if($studentid<1) return return_format('',33025,lang('param_error'));
        $datearr = explode('-',$date) ;
        if( count($datearr)!=3 ) return return_format('',33026,lang('33026'));

        //获取每天的课节 数量信息
        $schedobj = new Toteachtime;
        //获取学生的可上的课时
        $newidarr = $this->getAllLessonsidarr($studentid);
        $datecourse = $schedobj->getStudentLessonsByDate($date,$newidarr) ;
        if( empty($datecourse) ){
            return return_format([],0,lang('success'));
        }else{
            //获取教师信息
            $teacharr = array_column($datecourse, 'teacherid') ;
            $teachobj = new Teacherinfo ;
            $namearr = $teachobj->getTeachernameByIds($teacharr) ;
            //获取当前时间
            $nowtime = time();
            $coursemodel = new Curriculum;
            foreach ($datecourse as $key => &$val) {
                $val['teachername'] = $namearr[$val['teacherid']] ;
                $where = [
                    'id' => $val['curriculumid']
                ];
                $datecourse[$key]['generalize'] = $coursemodel->getCourserById($where)['generalize'];
                $datecourse[$key]['subhead'] = $coursemodel->getCourserById($where)['subhead'];
                //计算开始时间和结束时间
                $starttime =  $val['starttime'];
                $endtime = $val['endtime'];
                //定义列表按钮状态

                if($nowtime>$endtime){
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
                    $goroomtime  =  $starttime -$this->time;
                    if($nowtime<$goroomtime){
                        $val['buttonstatus'] = $this->buttonstatus[0];
                    }else{
                        $val['buttonstatus'] = $this->buttonstatus[1];
                    }
                }
                $val['starttime'] = date('Y-m-d H:i:s',$starttime) ;
                $val['endtime']   = date('Y-m-d H:i:s',$val['endtime']) ;
            }
            return return_format($datecourse,0,lang('success'));
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
        $toteachmodel = new Toteachtime();
        //查询班级和课程是否被删除
        $toteachwhere = ['t.id'=>$toteachid];
        $classarr = $toteachmodel->getCourseidOrscheduid($toteachwhere);
        $coursemodel = new Curriculum;
        $coursewhere = ['c.id'=>$classarr['curriculumid'],'c.delflag'=>1];
        $courseinfo = $coursemodel->getCourserById($coursewhere);
        if(empty($courseinfo) || empty($classarr['delflag'])){
            return return_format('',33965,'该课程已被删除,不能进入教室');
        }
        $nickname = $studentinfo['nickname'];
        $classmodel = new Classroom;
        $organconfigmodel = new Organ();
        //获取教室key
        $keyarr  = $organconfigmodel->getRoomkey();
        $key = $keyarr['roomkey'];
        //获取教室信息
        $classinfo = $classmodel->getClassInfo($toteachid);

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
        $jumpurl = config('param.server_url');
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/cqnmjy/serial/{$classinfo['classroomno']}/username/$nickname/usertype/$usertype/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/$jumpurl";
        $data['url'] = $url;
        return return_format($data,0,lang('success'));

    }
    /**
     * [getTeacherComment 获取老师评论]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @return   [array]                          [description]
     */
    public function getTeacherComment($lessonsid,$studentid){
        if(!is_intnum($lessonsid) || !is_intnum($studentid)){
            return return_format('',33700,lang('param_error'));
        }
        $attendmodel  = new \app\student\model\Studentattendance();
        $where = [
            'a.lessonsid' => $lessonsid,
            'a.studentid' => $studentid,
            'a.status' => 1,
        ];
        $field = 'l.periodname,s.curriculumname,s.gradename,a.comment,a.score,a.addtime,t.nickname as teachername,t.imageurl,a.status,FROM_UNIXTIME(a.addtime) as addtime';
        $result = $attendmodel->getFindInfo($where,$field);
        return return_format($result,0,lang('success'));
    }
    /**
     * [getHomeworkByLessionid 获取学生某课时的作业信息]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @return   [array]                          [description]
     */
    public function getHomeworkByLessionid($lessonsid,$studentid){
        if(!is_intnum($lessonsid) || !is_intnum($studentid)){
            return return_format('',33700,lang('param_error'));
        }
        $homeworkmodel = new \app\student\model\Studenthomework();
        $where = [
            'h.lessonid' => $lessonsid,
            'h.studentid' => $studentid
        ];
        $result = $homeworkmodel->getFindHomework($where);
        if(empty($result)){
            //定义一个空对象
            $result = (object)[];
        }
        return return_format($result,0,lang('success'));
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
             /* 发送站内消息    start*/
             $msgList['teacherid'] = $data['allaccountid'];
             $msgList['lessonsid'] = $data['lessonsid'];
             $msgList['nickname'] =  $data['nickname'];
             $msgobj = new \StudendMsg();
             $msgobj->commentMsg($msgList);
             /*----------end-----------*/
             return return_format($id,0,lang('success'));
         }elseif($id){
             return return_format('',33034,$id);
         }else{
             return return_format('',33035,lang('error'));
         }
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
     * getAllCourseList 获取所有的课程列表
     * @Author yr
     * @param $curriculumid int  课程id
     */
    public function getAllClassList($studentid){
        $currobj = new Ordermanage();
        $where = [
            'o.studentid'=>$studentid,
            'o.orderstatus'=> 20,
            'o.coursetype'=> 2,
            's.classstatus'=> ['lt',5],
        ];
        //查询出该学生购买的所有直播课程
        $data = $currobj->getStudentAllOrder($where);
        $time = time();
        $toteachmodel = new Toteachtime;
        $schedumodel = new Scheduling;
        $applyscheduling = new Applyschedulingrecord();
        foreach($data as $k=>$v){
            //查询该班级已经上过得课时数量
            //查询该班级是否调过班级
            $appwhere = [
                'status' => 1,
                'oldschedulingid' => $v['schedulingid'],
                'studentid' => $studentid,
            ];
            $isapply = $applyscheduling->getCourseCount($appwhere);
            if($isapply>0){
                unset($data[$k]);
                continue;
            }
            $wherearr = [
                'schedulingid' => $v['schedulingid'],
                'delflag' => 1,
                'starttime' => ['lt',$time],
            ];
            $data[$k]['num'] = $toteachmodel->getStudentAttendNum( $wherearr);
            $data[$k]['surplusnum'] = $v['periodnum'] -  $data[$k]['num'];
            //查询出该课程下的所有班级
            $scheduarr = [
                's.id' => ['neq',$v['schedulingid']],
                's.delflag' => 1,
                's.status' => 1,
                's.curriculumid' =>$v['curriculumid'],
                's.realnum' => ['gt',0],
                's.classstatus' => ['lt',5] //未结束 且有课时的班级
            ];
            $schedufield = 's.id as schedulingid,s.gradename,t.nickname as teachername,s.starttime,s.endtime';
            $data[$k]['classlist'] =  $schedumodel->getScheduList($scheduarr,$schedufield);
        }
        $data = array_values($data);
      return return_format($data,0,lang('success'));
    }
    /**
     * getAllCourseList 获取所有的课程列表
     * @Author yr
     * @param $curriculumid int  课程id
     */
    public function getBuyCourseList($studentid){
        $ordermodel = new Ordermanage();
        $where = [
            'studentid'=>$studentid,
            'orderstatus'=> 20,
            'coursetype'=> 2,
        ];
        //查询出该学生购买的所有直播课程
        $courseinfo =  $ordermodel->getBuyCourseList($where);
        $toteachmodel = new Toteachtime();
        $time = time();
        $applyscheduling = new Applyschedulingrecord();
        foreach($courseinfo as $k=>$v){
            $orderarr = [
                'o.studentid'=>$studentid,
                'o.orderstatus'=> 20,
                'o.coursetype'=> 2,
                'o.curriculumid'=> $v['curriculumid'],
                's.classstatus'=> ['lt',5],
            ];
            $courseinfo[$k]['classinfo'] = $ordermodel->getStudentAllOrder($orderarr);//购买过得班级
            if(empty($courseinfo[$k]['classinfo'])){
                unset($courseinfo[$k]);
                continue;
            }
            //获取该班级下未
            foreach($courseinfo[$k]['classinfo'] as $key=>$value){
                //查询该班级是否调过班级
                $appwhere = [
                    'status' => 1,
                    'oldschedulingid' => $value['schedulingid']
                ];
                $isapply = $applyscheduling->getCourseCount($appwhere);
               if($isapply>0){
                    unset($courseinfo[$k]['classinfo'][$key]);
                    continue;
                }
                //查询该班级已经上过得课时数量
                $wherearr = [
                    't.schedulingid' => $value['schedulingid'],
                    't.delflag' => 1,
                    't.starttime' => ['gt',$time],
                ];
                $field = 't.teacherid,ti.teachername,t.lessonsid,l.periodname,l.periodid';
                $courseinfo[$k]['classinfo'][$key]['lessons'] = $toteachmodel->getStudentAttendLessons($wherearr,$field);
                if(empty($courseinfo[$k]['classinfo'][$key]['lessons'])){
                    unset($courseinfo[$k]['classinfo'][$key]);
                }
            }
            if(empty($courseinfo[$k]['classinfo'])){
                unset($courseinfo[$k]);
                continue;
            }
        }
        $courseinfo = array_values($courseinfo);
        foreach($courseinfo as $k=>$v){
            $courseinfo[$k]['classinfo'] = array_values($courseinfo[$k]['classinfo']);
        }
        return return_format($courseinfo,0,lang('success'));
    }

    /**
     * submitApplyClasss 申请提交调班
     * @Author yr
     * @param $studentid int  学生id
     * @param $curriculumid int  课程id
     * @param $oldschedulingid int  原课程id
     * @param $newschedulingid int  新课程id
     */
    public function submitApplyClasss($studentid,$curriculumid,$oldschedulingid,$newschedulingid){
        if(!is_intnum($studentid) || !is_intnum($curriculumid) || !is_intnum($oldschedulingid) || !is_intnum($newschedulingid)){
            return return_format('',33500,lang('param_error'));
        }
        $schedumodel = new Scheduling();
        $oldscheduinfo = $schedumodel->getCourserById($oldschedulingid);
        $newscheduinfo = $schedumodel->getCourserById($newschedulingid);
        //查询该班级已经上过得课时数量
        $time = time();
        $wherearr = [
            'schedulingid' => $oldschedulingid,
            'delflag' => 1,
            'starttime' => ['lt',$time],
        ];
        $toteachmodel = new Toteachtime;
        $num = $toteachmodel->getStudentAttendNum($wherearr);
        $oldlessons = $toteachmodel->getStudentAttendLesson($wherearr);   //查询该学生已经上过的课时集合
        $oldlessonstr = implode(',',$oldlessons);
        if($num >=3){
            return return_format('',33501,'该班级已学习三次课');
        }
        $surplusnum = $oldscheduinfo['periodnum'] -  $num;
        if($surplusnum == 0){
            return return_format('',33502,'该班级已经学习完成');
        }
        //判断该班级对应的课程是否超过两次调班
        $applyclassmodel = new Applyschedulingrecord();
        $where = [
            'curriculumid'=> $curriculumid,
            'studentid' =>$studentid,
            'status' =>1,
        ];
        $coursecount = $applyclassmodel->getCourseCount($where);
        if($coursecount >=2){
            return return_format('',33503,'每个课程仅有两次调班的机会');
        }
        //获取新班级开始要上课的课时id
        $toteachwhere = [
            'schedulingid' => $newschedulingid,
            'delflag' => 1,
            'starttime' => ['gt',$time],
        ];
        $toteachfield = 'lessonsid';
        $startlessonsid = $toteachmodel->getStartLessons($toteachwhere,$toteachfield)['lessonsid'];
        $newlessonsarr = $toteachmodel->getStudentPrepareLesson($toteachwhere);//新班级未上课的课时集合
        $newlessonstr = implode(',',$newlessonsarr);
        //拼装插入信息
        $data['studentid'] = $studentid;
        $data['applytime'] = time();
        $data['curriculumid'] = $curriculumid;
        $data['oldschedulingid'] = $oldschedulingid;
        $data['newschedulingid'] = $newschedulingid;
        $data['startlessonsid'] = $startlessonsid;//新班级的最新课时id
        $data['oldteacherid'] = $oldscheduinfo['teacherid'];
        $data['newteacherid'] = $newscheduinfo['teacherid'];
        $data['oldattendclass'] = $num;//新班级的最新课时id
        $data['oldlessonsid'] = $oldlessonstr;
        $data['newlessonsid'] =  $newlessonstr;
        $insert_res = $applyclassmodel->insertData($data);
        if($insert_res){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',33504,lang('error'));
        }

    }
    public function getSelectableLessons($studentid,$lessonsid,$oldschedulingid,$curriculumid,$periodid){
        if(!is_intnum($studentid) || !is_intnum($lessonsid) || !is_intnum($oldschedulingid)){
            return return_format('',33505,lang('param_error'));
        }
        $schedumodel = new Scheduling;
        $toteachmodel = new Toteachtime();
        $lessonsmodel = new Lessons();
        $scheduarr = [
                    's.id' => ['neq',$oldschedulingid],
                    's.delflag' => 1,
                    's.status' => 1,
                    's.curriculumid' =>$curriculumid,
                    's.classstatus' => ['lt',5] //未结束 且有课时的班级
                ];
        $schedufield = 's.id as schedulingid,s.gradename,t.nickname as teachername,s.starttime,s.endtime';
        $courseinfo['newclassinfo'] = $schedumodel->getScheduList($scheduarr,$schedufield);
        foreach($courseinfo['newclassinfo'] as $key=>$value){
            //查询原班级需要调课的课时id 所对应的其他班级下的课时id
            $where = [
                'schedulingid' => $value['schedulingid'],
                'periodid' => $periodid,
            ];
            $field = 'id as newlessonsid';
            $courseinfo['newclassinfo'][$key]['newlessonsid'] =  $lessonsmodel->getLessonsFind($where,$field)['newlessonsid'];
            $toteacharr = [
                't.lessonsid' => $courseinfo['newclassinfo'][$key]['newlessonsid']
            ];
            $toteachfield = 'ti.nickname as newteachername,t.starttime,t.endtime';
            $courseinfo['newclassinfo'][$key]['info'] =  $toteachmodel->getStudentAttendLessons($toteacharr,$toteachfield);
            $courseinfo['newclassinfo'][$key]['newteachername'] =  $courseinfo['newclassinfo'][$key]['info'][0]['newteachername'];
            $courseinfo['newclassinfo'][$key]['starttime'] =  $courseinfo['newclassinfo'][$key]['info'][0]['starttime'];
            $courseinfo['newclassinfo'][$key]['endtime'] =  $courseinfo['newclassinfo'][$key]['info'][0]['endtime'];
            unset($courseinfo['newclassinfo'][$key]['info']);
        }
        $result = $courseinfo['newclassinfo'];
        return return_format($result,0,lang('success'));
    }
    /**
     * submitApplyClasss 申请提交调班
     * @Author yr
     * @param $studentid int  学生id
     * @param $curriculumid int  课程id
     * @param $oldlessonsid int  原班级id
     * @param $newlessonsid int  新班级id
     */
    public function submitApplyLession($studentid,$curriculumid,$oldlessonsid,$newlessonsid){
        if(!is_intnum($studentid) || !is_intnum($curriculumid) || !is_intnum($oldlessonsid) || !is_intnum($newlessonsid)){
            return return_format('',33500,lang('param_error'));
        }
        $toteachmodel = new Toteachtime();
        //查看该课节是否超过48小时
        $nowtime = time();
        $where = [
            'lessonsid' => $oldlessonsid
        ];
        $field = 'starttime';
        $oldstarttime = $toteachmodel->getStartLessons($where,$field)['starttime'];
        if($oldstarttime-$nowtime <= 48*3600){
            return return_format('',33506,'课程开始前48小时内不能申请调课');
        }
        $date = date("Y-m-d");
        $firstday = date('Y-m-01 00:00:00', strtotime($date));  //本月第一天
        $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day")); //本月最后一天
        $startday = strtotime( $firstday);
        $endday = strtotime(  $lastday);
        $applymodel = new Applylessonsrecord();
        $condition = [
            'studentid' => $studentid,
            'status' => 1,
            'applytime' => ['lt',$endday],
            'status' => ['gt',$startday],
        ];
        $count = $applymodel->getCount($condition);
        if($count+1>3){
            return return_format('',33507,'每月只有3次数调课机会');
        }
        $lessonsmodel = new Lessons();
        $oldwhere = [
            'id' => $oldlessonsid
        ];
        $oldfield = 'teacherid';
        $oldteacherid =  $lessonsmodel->getLessonsFind($oldwhere,$oldfield)['teacherid'];
        $newwhere = [
            'id' => $newlessonsid
        ];
        $newfield = 'teacherid';
        $newteacherid =  $lessonsmodel->getLessonsFind($newwhere,$newfield)['teacherid'];
        //拼装插入信息
        $data['studentid'] = $studentid;
        $data['applytime'] = time();
        $data['curriculumid'] = $curriculumid;
        $data['oldlessonsid'] = $oldlessonsid;
        $data['newlessonsid'] = $newlessonsid;
        $data['oldteacherid'] = $oldteacherid;
        $data['newteacherid'] = $newteacherid;
        $insert_res = $applymodel->insertData($data);
        if($insert_res){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',33504,lang('error'));
        }

    }
    /**
     * 计算已学课时
     * @Author yr
     * @param $curriculumid int  课程id
     */
    function countLearned($schedulingid,$ordernum){
        if(empty($schedulingid)){
            return '0';
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
            $radio = '0';
        }else{
            $radio = round(intval($learndnum)/$length*100);
            if($radio>=100){
                $radio = '100';
            }else{
                $radio = $radio;
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
        $oldclassids = $applyclassmodel->getColumnIds($applywhere,$applyoldfield);//旧班级的所有课时id集合
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
