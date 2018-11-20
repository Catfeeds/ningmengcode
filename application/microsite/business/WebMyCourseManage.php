<?php
namespace app\student\business;
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
use app\student\model\Schedulingdeputy;
use app\admin\business\Docking;
use Calendar;
class WebMyCourseManage
{
    protected $foo;
    protected $str;
    protected $array;
    protected $orderstatus;
    protected $type;
    protected $date;
    //定义学生进教室时间 5分钟
    protected $time = 300;
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
    public function getBuyList($studentid,$organid,$pagenum,$limit)
    {
        if (!is_intnum($studentid) || !is_intnum($organid) || !is_intnum($limit)) {
            return return_format($this->str, 33000, '参数错误');
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
        $orderlist = $ordermodel->getStudentOrder($studentid, $limitstr, $this->orderstatus);
        //计算已学课时  上课时间大于当前时间视为已学,日期作为键返回List
        $datearray = [];
        foreach ($orderlist as $k => $v) {
            //如果是一对一课程，查看是否预约完毕；1为预科完毕
            $orderlist[$k]['learned'] = $this->countLearned($v['schedulingid'],$v['ordernum'],$v['type']);
            if ($v['type'] == $this->type) {
                if($orderlist[$k]['learned'] == '100%'){
                    $orderlist[$k]['reserve'] = 0;
                }else{
                    $orderlist[$k]['reserve'] = 1;
                }
                /* $orderlist[$k]['reserve'] = $this->reserveLearned($v['schedulingid'], $studentid,$v['ordernum']);*/
            }
            $date = strtotime($v['ordertime']);
            $date = date('Y年m月d日', $date);
            $orderlist[$k]['date'] = $date;
        }

        foreach ($orderlist as $key => $v) {
            $datearray[$v['date']][] = $v;
        }
        $datearray = array_values($datearray);
       /* dump($orderlist);die();*/
        $total = $ordermodel->studentOrderCount($studentid,$this->orderstatus);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $datearray;
        return return_format($alllist, 0, '请求成功');
    }
    /**
     * 获取我的课时安排
     * @Author yr
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    schedulingid int   排课id
     * @return array
     *
     */
    public function getClassSchedule($studentid,$organid,$schedulingid,$ordernum)
    {
        if(!is_intnum($schedulingid) || !is_intnum($organid)){
            return return_format($this->str,35001,'参数错误');
        }
        //实例化model
        $coursermodel = new Schedulingdeputy;
        $unitmodel = new Unitdeputy;
        $lessonsmodel = new Lessons;
        $toteachmodel = new Toteachtime;
        //获取课程相关信息
        $courserinfo['courser'] = $coursermodel->getCourserOne($schedulingid);
        $organid = $courserinfo['courser']['organid'];
        //获取课程单元和课时
        $unitlist = $unitmodel->getUnitList($schedulingid,$organid);
        //如果班级类型是一对一 需要加studentid
        foreach($unitlist as $k=>$v){
            //如果是小班课大班课直接查询对应上课时间，一对一需另走方法
            if($courserinfo['courser']['type'] !== 1){
                $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid'],$organid,$courserinfo['courser']['type']);
                foreach( $unitlist[$k]['period'] as $key=>$value){
                    $unitlist[$k]['period'][$key]['timekey']  = explode(',',$value['timekey']);
                    if(empty($unitlist[$k]['period'][$key]['timekey'][0])){
                        $unitlist[$k]['period'][$key]['time'] = '';
                    }else{
                        $unitlist[$k]['period'][$key]['time'] = get_time_key($unitlist[$k]['period'][$key]['timekey'][0]);
                    }
                }
            }else{
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
                    if(empty($unitlist[$k]['period'][$key]['timekey'][0])){
                        $unitlist[$k]['period'][$key]['time'] = '';
                    }else{
                        $unitlist[$k]['period'][$key]['time'] = get_time_key($unitlist[$k]['period'][$key]['timekey'][0]);
                    }
                    unset($unitlist[$k]['period'][$key]['teacheinfo']);
                }
            }
        }
        //返回结果集
        $courserinfo['unit'] = $unitlist;
        if(empty($courserinfo)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($courserinfo,0,'查询成功');
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
    public function getLessionsInfo($organid,$schedulingid,$studentid,$ordernum)
    {
        if (!is_intnum($organid)) {
            return return_format($this->str, 33003, '参数错误');
        }
        $schedumodel = new Schedulingdeputy;
        //获取课程相关信息
        $info = $schedumodel->getCourserOne($schedulingid);
        $organid = $info['organid'];
        //实例化model
        $unitmodel = new Unitdeputy;
        $lessonsmodel = new Lessons;
        $toteachmodel = new Toteachtime;
        $unitlist = $unitmodel->getUnitList($schedulingid, $organid);
        if (empty($unitlist)) {
            return return_format([], 0, '数据为空');
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

        return return_format($unitlist, 0, '查询成功');
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
    public function getFreeList($organid, $teacherid, $date)
    {
        if (!is_intnum($organid) || !is_intnum($teacherid)  || empty($date)) {
            return return_format($this->str, 33006, '参数错误');
        }
        $week = date('w',strtotime($date));
        if($week == 0){
            $week = 7;
        }
        //实例化模型
        $teachertimemodel = new Teachertime;
        //查看老师可预约时间
        $freeinfo = $teachertimemodel->findWeekdayMark($teacherid, $week);
        $organid = $freeinfo['organid'];
        $freearray = explode(',', $freeinfo['mark']);
        //查看学生占用的预约时间
        $toteahcermodel = new Toteachtime;
        $toteahcerinfo = $toteahcermodel->getDateInfo($organid, $teacherid, $date);
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
        //返回老师可约课的时间
        if (empty($newarray)) {
            return return_format($newarray, 33007, '没有可预约的时间');
        }
        return return_format($newarray, 0, '查询成功');

    }

    /**
     * 添加学生预约时间
     * @Author yr
     * @param    $data;
     * @return   array();
     */
    public function addEdit($data){
        $schedumodel = new Schedulingdeputy;
        if(empty($data['intime']) || empty($data['timekey'])){
            return return_format('',33105,'请选择时间');
        }
        if(empty($data['ordernum'])){
            return return_format('',33106,'订单参数错误');
        }
        if(!is_intnum($data['studentid'])){
            return return_format('',33106,'学生id为空');
        }
        $scheduinfo = $schedumodel->getScheduById($data['schedulingid']);
        if(!$scheduinfo){
            return return_format('',33009,'您所选的课程发生异常、或不存在');
        }
        $addtime = strtotime($data['intime'].' '.get_time_key($data['timekey']));
        $nowtime = time();
        if($addtime<=$nowtime){
            return return_format('',33100,'预约时间不能早于当前时间');
        }
        //查询出所有的单元信
        $lessonsmodel = new Lessons;
        $toteachmodel = new Toteachtime;
        $lessonslist = $lessonsmodel->getLessonsByscheduid($data['schedulingid'],$scheduinfo['organid']);
        $lessonids = array_column($lessonslist,'lessonsid');
        if(!in_array($data['lessonsid'],$lessonids)){
            return return_format('',33104,'参数错误,没有此课节信息');
        }
        //获取该机构的一对一课时时长
     /*   $configmodel = new Organconfig;
        $configlist = $configmodel->getRoomkey($scheduinfo['organid']);*/
        $toonetime =  $scheduinfo['classhour'];;
        //拼装一节课所占用都得课时key
        $timekeys =  array_series($data['timekey'],$toonetime);
        $freearray =  $this->getFreeTime($data['intime'], $scheduinfo['teacherid']);
        foreach($timekeys as $k=>$v){
            if(!in_array($v,$freearray)){
                return return_format('',33400,'当前时间设置不合理或已被占用');
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
                        return return_format('',33102,'当前课时不能预约,请依次选择约课');
                    }
                    $prelist['timekey'] = explode(',',$prelist['timekey']);
                    $pretime = strtotime($prelist['intime'].' '.get_time_key($prelist['timekey'][0]));
                    if($currtime<=$pretime){
                        return return_format('',33103,'当前课时时间设置早于前一课时的时间');
                    }
                }
                //拼装插入条件
                $toteachmodel = new Toteachtime;
                $info['intime'] = $data['intime'];
                $info['teacherid'] = $scheduinfo['teacherid'];
                $info['coursename'] = $scheduinfo['curriculumname'];
                $info['type'] = $scheduinfo['type'];
                $info['timekey'] = implode(',',$timekeys);
                $info['organid'] = $scheduinfo['organid'];
                $info['lessonsid'] = $data['lessonsid'];
                $info['schedulingid'] = $data['schedulingid'];
                $info['studentid'] = $data['studentid'];
                $info['insort'] = $sort;
                $info['ordernum'] = $data['ordernum'];
                $info['endtime'] = $currtime +$toonetime*60;
                $res = $toteachmodel->addEdit($info);
                if($res['code'] == 0 ){
                    return return_format('',0,'预约成功');
                }else{
                    return return_format('',33012,'预约失败');
                }
            }else{
                //修改
                $currtime = strtotime($data['intime'].' '.get_time_key($data['timekey']));
                switch ($insort){
                    case 1:
                        //第一条数据
                        //第一条数据
                        if(!empty($afterid)){
                            $afterlist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$afterid);
                        }
                        if(!empty($afterlist)){
                        $afterlist['timekey'] = explode(',',$afterlist['timekey']);
                        $aftertime = strtotime($afterlist['intime'].' '.get_time_key($afterlist['timekey'][0]));
                            if($currtime>=$aftertime){
                                return return_format('',33103,'当前课时时间设置晚于后一课时的时间');
                            }
                        }
                        break;
                    case 0:
                        $prelist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$preid);
                        if(empty($prelist)){
                            return return_format('',33104,'当前课时不能预约,请依次选择约课');
                        }
                        $prelist['timekey'] = explode(',',$prelist['timekey']);

                        $pretime = strtotime($prelist['intime'].' '.get_time_key($prelist['timekey'][0]));
                        if($currtime<$pretime){
                            return return_format('',33105,'当前课时时间设置早于前一课时的时间');
                        }
                        $afterlist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$afterid);
                        if(!empty($afterlist)){
                            $afterlist['timekey'] = explode(',',$afterlist['timekey']);
                            $aftertime = strtotime($afterlist['intime'].' '.get_time_key($afterlist['timekey'][0]));
                            if($currtime>=$aftertime){
                                return return_format('',33107,'当前课时时间设置晚于后一课时的时间');
                            }
                        }
                    case 2:
                        $prelist = $toteachmodel->getTimekeyByOrdernum($data['ordernum'],$preid);
                        if(empty($prelist)){
                            $prelist['timekey'] = explode(',',$prelist['timekey']);
                            return return_format('',33108,'当前课时不能预约,请依次选择约课');
                        }
                        $pretime = strtotime($prelist['intime'].' '.get_time_key($prelist['timekey'][0]));
                        if($currtime<=$pretime){
                            return return_format('',33109,'当前课时时间设置早于前一课时的时间');
                        }

                }
                //拼装插入条件
                $toteachmodel = new Toteachtime;
                $info['intime'] = $data['intime'];
                $info['id'] = $data['toteachid'];
                $info['timekey'] = implode(',',$timekeys);
                $info['endtime'] = $currtime +$toonetime*60;
                $res = $toteachmodel->addEdit($info);
                if($res['code'] == 0 ){
                    return return_format('',0,'修改成功');
                }else{
                    return return_format('',33015,'修改失败');
                }
            }
    }
    /**
     * [studentCourseList 获取学生课表]
     * @Author yr
     * @DateTime 2018-04-25T09:55:13+0800
     * @param    [string]                $date    [需要查询的日期]
     * @param    [int]                   $organid [机构标识id]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function studentCourseList($date,$organid,$studentid){
        if($organid<1) return return_format('',33017,'参数异常');
        $datearr = explode('-',$date) ;
        if( count($datearr)!=3 ) return return_format('',33018,'日期参数异常');

        $cal = new Calendar($datearr[0],$datearr[1],$datearr[2]);
        $starttime = date('Y-m-d',$cal->starttime) ;
        $endtime   = date('Y-m-d',$cal->endtime) ;
        //获取指定月的星期 和 日期数组
        $calendar = $cal->array ;

        //获取每天的课节 数量信息
        $schedobj = new Toteachtime;
        $datecourse = $schedobj->studentCourseList($starttime,$endtime,null,$studentid) ;
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
        return return_format($calendar,0,'查询成功');

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
        if($studentid<1) return return_format('',33019,'参数异常');
        $datearr = explode('-',$date) ;
        if( count($datearr)!=3 ) return return_format('',33020,'日期参数异常');

        //获取每天的课节 数量信息
        $schedobj = new Toteachtime;
        $datecourse = $schedobj->getLessonsByDate($date,$studentid) ;
        if( empty($datecourse) ){
            return return_format([],0,'获取数据为空');
        }else{
            //获取教师信息
            $teacharr = array_column($datecourse, 'teacherid') ;
            $teachobj = new Teacherinfo ;
            $namearr = $teachobj->getTeachernameByIds($teacharr) ;
            //获取当前时间
            $nowtime = time();
            foreach ($datecourse as $key => &$val) {
                $val['teachername'] = $namearr[$val['teacherid']] ;
                //计算开始时间和结束时间
                $timearr = explode(',',$val['timekey']) ;
                $hourarr = explode(':',get_time_key($timearr[0])) ;
                $datearr = explode('-',$val['intime']) ;
                $unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]);
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
                    $goroomtime  = $unixtime -$this->time;
                    if($nowtime<$goroomtime){
                        $val['buttonstatus'] = $this->buttonstatus[0];
                    }else{
                        $val['buttonstatus'] = $this->buttonstatus[1];
                    }

                }
                $val['starttime'] = date('Y-m-d H:i:s',$unixtime) ;
                $val['endtime']   = date('Y-m-d H:i:s',$val['endtime']) ;
            }
            return return_format($datecourse,0,'查询成功');
        }

    }
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @param    [int]                   $organid       [机构id]
     * @return   [array]                          [description]
     */
    public function getLessonsPlayback($toteachid,$organid){
        if($toteachid<1 || $organid<1) return return_format('',33022,'参数异常');
        //实例化模型
        $playbackmodel = new Playback;
        $teachermodel = new Teacherinfo;
        $data = $playbackmodel->getVideourl($toteachid);
        if(empty($data)){
            return return_format([],0,'没有数据');
        }
        foreach($data as $k=>$v){
            $videoinfo[$k]['playpath'] = $v['playpath'];
            $videoinfo[$k]['https_playpath'] = $v['https_playpath'];
            //时间戳转化为时分秒
            $videoinfo[$k]['duration'] = secToTime(ceil($v['duration']/1000));
            $videoinfo[$k]['part'] = $k+1;
        }
        //获取老师名称
        $teachername = $teachermodel->getTeacherId($data[0]['teacherid'],'teachername');
        $newarr['teachername'] = $teachername['teachername'];
        //获取上课时间
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
        return return_format($newarr,0,'查询成功');
    }
    /**
     * [intoClassroom 进教室]
     * @Author
     * @DateTime 2018-04-25T14:14:00+0800
     * @param    [string]                $toteachid     上课时间表id
     * @param    [int]                   $organid       [机构id]
     * @return   [array]                          [description]
     */
    public function intoClassroom($toteachid,$organid,$studentid){
        //实例化模型
        //查询学生上课的机构
        $toteachmodel = new Toteachtime;
        $where = [
            'id'=> $toteachid
        ];
        $field = 'organid';
        $organid = $toteachmodel->getArrById($where,$field);
        if(empty($organid )){
            return return_format('',33124,'无效的参数');
        }
        //获取用户昵称
        $studentobj = new Studentinfo;
        $nickname = $studentobj->getStudentInfo($studentid)['nickname'];
        $organid = $organid[0];
        //实例化模型
        if(empty($organid )){
            return return_format('',33124,'无效的参数');
        }
        $classmodel = new Classroom;
        $organconfigmodel = new Organconfig;
        //获取教室key
        $keyarr  = $organconfigmodel->getRoomkey($organid);
        $key = $keyarr['roomkey'];
        //获取教室信息
        $classinfo = $classmodel->getClassInfo($toteachid);
        $toteachmodel = new Toteachtime();
        $list = $toteachmodel->getTimeList($toteachid);
        //查看教室状态 如果教室状态等于2
        $status  =  $list['status'];
        if($status == 2){
            return return_format('',33900,'该节课已经结束');
        }
        //如果获取教室信息失败，创建教室
        if(empty($classinfo)){
            $obj = new Docking;
            $adminteachmodel = new \app\admin\model\Toteachtime();
            $obj->operateRoomInfo($list, $adminteachmodel);
            $classinfo = $classmodel->getClassInfo($toteachid);
            if(empty($classinfo)){
                return return_format('',33125,'系统繁忙请稍后再试');
            }
        }
        $time  = time();
        //必填， 0：主讲(老师 )  1：助教 2: 学员   3：直播用户  4:巡检员
        $usertype = '2';
        //，auth 值为 MD5(key + ts +serial + usertype)
        $sign =  MD5($key.$time.$classinfo['classroomno'].$usertype);
        //学生密码
        $userpassword = getencrypt($classinfo['confuserpwd'],$key);
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$classinfo['classroomno']}/username/$nickname/usertype/$usertype/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
        $data['url'] = $url;
        return return_format($data,0,'查询成功');

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
     * @param   [int]              organid 机构id
     * @return   [array]                          [description]
     */
     public function insertComment($data){
         if(!is_intnum($data['curriculumid']) || !is_intnum($data['classtype']) || !is_intnum($data['studentid']) ||!is_intnum($data['allaccountid'])||!is_intnum($data['schedulingid']) ||!is_intnum($data['organid']) ){
            return return_format($this->str,33024,'参数错误');
         }
         if(!is_numeric($data['score']) || empty($data['score'])){
             return return_format('',33124,'评分必须填写');
         }
         $schedumodel = new Schedulingdeputy;
         $info = $schedumodel->getCourserOne($data['schedulingid'],$data['organid']);
         $data['organid']  = $info['organid'];
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
             return return_format($id,0,'添加成功');
         }elseif($id){
             return return_format('',33116,$id);
         }else{
             return return_format('',33117,'添加失败');
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
    function countLearned($schedulingid,$ordernum,$type){
        if(empty($schedulingid)){
            return '0%';
        }
        //统计排课下所有的课节

        $learnsmodel = new Lessons;
        $learnlist   = $learnsmodel->getLessonsNum($schedulingid);
        $learnlist = array_column($learnlist,'learnid');
        $length = count($learnlist);
        $timemodel  = new Toteachtime;
        if($type == 1){
            //一对一已学课时
            $learndnum = $timemodel->getLessonsTime($learnlist,$ordernum);
        }else{
            //下班课，大班课已学课时
            $learndnum = $timemodel->getLessonsCount($learnlist);
        }
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
    function getFreeTime($date,$teacherid){
        $week = date('w',strtotime($date));
        if($week == 0){
            $week = 7;
        }
        //实例化模型
        $teachertimemodel = new Teachertime;
        //查看老师可预约时间
        $freeinfo = $teachertimemodel->findWeekdayMark($teacherid, $week);
        $organid = $freeinfo['organid'];
        $freearray = explode(',', $freeinfo['mark']);
        //查看学生占用的预约时间
        $toteahcermodel = new Toteachtime;
        $toteahcerinfo = $toteahcermodel->getDateInfo($organid, $teacherid, $date);

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
}
