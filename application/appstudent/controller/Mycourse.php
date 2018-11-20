<?php
/**
 * 获取推荐老师列表 业务逻辑层
 *
 *
 */
namespace app\appstudent\controller;
use app\appstudent\business\OfficalAppUserManage;
use app\microsite\business\MicroMyCourseManage;
use app\student\business\MyCourseManage;
use login\Authorize;
use think\Controller;
use app\appstudent\business\AppMyCourseManage;
use app\appstudent\business\OfficalAppMyCourseManage;
class Mycourse extends Authorize
{
    public function _initialize()
    {
        parent::_initialize();
        //获取登录后的organid
        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
        $this->nickname = $this->userInfo['info']['nickname'];

    }
    /**
     * 根据分类id查询课程列表
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    $fatherid int   父级id
     * @param    organid  int   机构id
     * @param    category_id int   当前选中的id，全部默认0；
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/searchByCids
     */
    public function searchByCids()
    {
        $organid = $this->request->param('organid');
        $fatherid = $this->request->param('fatherid');
        $categoryid = $this->request->param('category_id');
        //分页页数
        $pagenum = $this->request->param('pagenum');
        //
        $limit = $this->request->param('limit');
        //默认0
        $tagids = $this->request->param('tagids');
       /* $organid = 1;
        $fatherid = 0;
        $categoryid = 0;
        $tagids = 2;
        $limit = 8;*/
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }

        $res =  $courseobj ->searchByCids($organid,$fatherid,$categoryid,$tagids,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * [studentLessonsList 学生课表]
     * @Author lc
     * @DateTime 2018-04-25T09:44:53+0800
     * @return   [type]                   [description]
     * URL:/microsite/Mycourse/studentLessonsList
     */
    public function studentLessonsList(){
        $studentid = $this->userid;
        $status = $this->request->param('status') ;
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
        $organobj = new AppMyCourseManage;
        $listarr = $organobj->studentLessonsList($studentid,$status,$pagenum,$limit);
        $this->ajaxReturn($listarr);
    }
    /**
     * 查询课程标签
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    $fatherid int   父级id
     * @param    organid  int   机构id
     * @param    category_id int   当前选中的id，全部默认0；
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getCourseTags
     */
    public function getCourseTags()
    {
        $organid = $this->request->param('organid');
        /*$organid = 1;*/
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res =  $courseobj ->getCourseTags($organid);
        $this->ajaxReturn($res);
    }
    /**
     * 我的课表-今日课表
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getTodayCurriculum
     */
    public function getTodayCurriculum()
    {
        $date = $this->request->param('date') ;
        $studentid = $this->request->param('studentid');
        $date = isset($date)?$date:date('Y-m-d',time());
       /* $studentid = 1;
        $date = '2018-03-26';*/
       $organid = $this->organid;
        if($organid == 1){
            $currobj = new OfficalAppMyCourseManage;
        }else{
            $currobj = new AppMyCourseManage;
        }
        $lessonsarr = $currobj->getLessonsByDate($date,$studentid);
        $this->ajaxReturn($lessonsarr);
    }
    /**
     * 我的课表-待上课或已结束
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    status  int   1,代表待上课2代表结束的课表
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getWaitOrEndCurriculum
     */
    public function getWaitOrEndCurriculum()
    {
        $studentid = $this->request->param('studentid');
        $status = $this->request->param('status');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
  /*      $status = 2;
        $studentid = 1;
        $organid = 1;
        $pagenum = 1;
        $limit = 10;*/
        $organid = $this->organid;
        if($organid == 1){
            $currobj = new OfficalAppMyCourseManage;
        }else{
            $currobj = new AppMyCourseManage;
        }
        $lessonsarr = $currobj->getWaitOrEndCurriculum($status,$studentid,$organid,$pagenum,$limit);
        $this->ajaxReturn($lessonsarr);
    }
    /**
     * 我的课表-我的班级
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getMyClass
     */
    public function getMyClass()
    {
        $studentid = $this->request->param('studentid');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
       /* $studentid = 1;
        $organid = 1;
        $pagenum = 1;
        $limit = 10;*/
        $organid = $this->organid;
        if($organid == 1){
            $currobj = new OfficalAppMyCourseManage;
        }else{
            $currobj = new AppMyCourseManage;
        }
        $res =  $currobj ->getMyClass($studentid,$organid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 我的预约列表
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @return   array();
     * URL:/appstudent/Mycourse/getReserveStatus
     */
    public function getReserveStatus()
    {
        $studentid = $this->request->param('studentid');
        /*  $studentid = 1;
          $organid = 1;
          $pagenum = 1;
          $limit = 10;*/
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res =  $courseobj ->getReserveStatus($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 我的预约列表
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getReserveClass
     */
    public function getReserveClass()
    {
        $studentid = $this->request->param('studentid');
        $organid = $this->request->param('organid');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
      /*  $studentid = 1;
        $organid = 1;
        $pagenum = 1;
        $limit = 10;*/
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res =  $courseobj ->getReserveClass($studentid,$organid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 预约
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getReserveLessons
     */
    public function getReserveLessons()
    {
        $studentid = $this->request->param('studentid');
        $organid = $this->request->param('organid');
        $schedulingid = $this->request->param('schedulingid');
        $ordernum= $this->request->param('ordernum');
      /*  $studentid = 1;
        $organid = 1;
        $schedulingid = 1;*/
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res =  $courseobj ->getReserveLessons($studentid,$organid,$schedulingid,$ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 一对一约课添加修改
     * @Author yr
     * @DateTime 2018-04-26T17:11:19+0800
     * @param    organid  int   机构id
     * @param    date     string  选择的年月日
     * @param    week     int   星期几 0代表星期天 1,2,3,4,5,6
     * @param    teacherid int   老师id
     * @param    toteachid int   预约时间id
     * @return   array();
     * URL:/appstudent/Mycourse/addEdit
     */
    public function addEdit(){
        //实例化模型
        /* $data['intime'] = $this->request->param('intime');
         $data['teacherid'] = $this->request->param('teacherid');
         $data['coursename'] = $this->request->param('coursename');
         $data['type'] = $this->request->param('type');
         $data['organid ']= $this->request->param('organid');
         $data['lessonsid'] = $this->request->param('lessonsid');
         $data['timekey'] = $this->request->param('timekey');
         $data['schedulingid'] = $this->request->param('chedulingid ');
         $data['studentid'] = $this->request->param('studentid');
         $data['list'] = $this->request->param('list');*/
        $data = $this->request->param('');
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $dataReturn = $courseobj->addEdit($data);

        $this->ajaxReturn($dataReturn);
    }
    /**
     * 查询我的课时安排或者录播课详情
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    courseid int   课程id
     * @param    coursetype int   课程类型 1录播课2直播课
     * @param    coursetype int   排课id
     * @return   array();
     * URL:/student/Mycourse/getClassSchedule
     */
    public function getClassSchedule()
    {
        $studentid = $this->userid;
        $courseid = $this->request->param('courseid');
        $coursetype = $this->request->param('coursetype');
        $schedulingid = $this->request->param('schedulingid');
        $courseobj = new AppMyCourseManage;
        $res =  $courseobj ->getClassSchedule($coursetype,$courseid,$schedulingid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的课程
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    coursetype  int   1录播课2直播课
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/student/Mycourse/getBuyCurriculum
     */
    public function getBuyCurriculum()
    {
        $studentid = $this->userid;
        $pagenum = $this->request->param('pagenum');
        $coursetype = $this->request->param('coursetype');
        $limit = config('param.pagesize')['student_curriculum'];
        $courseobj = new AppMyCourseManage;
        $res =  $courseobj ->getBuyList($studentid,$coursetype,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * 查询约课的课节信息
     * @Author yr
     * @DateTime 2018-04-26T14:11:19+0800
     * @param    organid  int   机构id
     * @param    curriculumid int   课程id
     * @param    schedulingid int   排课id
     * @param    studentid int   学生用户id
     * @return   array();
     * URL:/appstudent/Mycourse/getLessionsList
     */
    public function getLessionsList()
    {
        $organid = $this->request->param('organid');
        $curriculumid = $this->request->param('curriculumid');
        $schedulingid = $this->request->param('schedulingid');
        $studentid = $this->request->param('studentid');
     /*   $organid = 1;
        $curriculumid = 1;
        $schedulingid = 1;
        $studentid = 1;*/
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res = $courseobj->getLessionsInfo($organid,$curriculumid,$schedulingid,$studentid);
        $this->ajaxReturn($res);

    }
    /**
     * 查询一对一老师可约课时间
     * @Author yr
     * @DateTime 2018-04-26T14:11:19+0800
     * @param    organid  int   机构id
     * @param    date     string  选择的年月日
     * @param    week     int   星期几 0代表星期天 1,2,3,4,5,6
     * @param    teacherid int   老师id
     * @return   array();
     * URL:/appstudent/Mycourse/getFreeList
     */
    public function getFreeList()
    {
        $date = $this->request->param('date') ;
        //如果没有提供 使用当前日期
        if(empty($date)) $date = date('Y-m-d') ;
        $teacherid = $this->request->param('teacherid');
        $organid = $this->request->param('organid');
      /*  $teacherid = 1;
        $organid = 1;*/
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res = $courseobj->getFreeList($organid,$teacherid,$date);
        $this->ajaxReturn($res);
    }
    /**
     * [studentCourseList 学生课表]
     * @Author yr
     * @DateTime 2018-04-25T09:44:53+0800
     * @return   [type]                   [description]
     * URL:/appstudent/Mycourse/studentCourseList
     */
    public function studentCourseList(){
        //$organid = Session::get('organid');
        $organid = $this->request->param('organid');
        $studentid = $this->request->param('studentid') ;
        //指定 获取的 时间
        $date = $this->request->param('date') ;
        //如果没有提供 使用当前日期
        if(empty($date)) $date = date('Y-m-d') ;
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $listarr = $courseobj->studentCourseList($date,$organid,$studentid);
        $this->ajaxReturn($listarr);
    }
    /**
     * [getLessonsByDate 通过日期获取课节信息]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getLessonsByDate
     */
    public function getLessonsByDate(){
        $date = $this->request->param('date') ;
        $studentid = $this->request->param('studentid');
        $organid = $this->organid;
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $lessonsarr =  $courseobj->getLessonsByDate($date,$studentid);
        $this->ajaxReturn($lessonsarr);

    }
    /**
     * [getLessonsPlayback 通过toteachid获取视频回放的相关信息]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getLessonsPlayback
     */
    public function getLessonsPlayback(){
        $toteachid = $this->request->param('toteachid');
        $currobj = new MyCourseManage;
        $data = $currobj->getLessonsPlayback($toteachid);
        $this->ajaxReturn($data);

    }
    /**
     * [intoClassroom 通过toteachid获取进入教室相关信息]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/intoClassroom
     */
    public function intoClassroom(){
        $toteachid = $this->request->param('toteachid');
        $studentid = $this->userid;
        $currobj = new OfficalAppMyCourseManage();
        $data = $currobj->intoClassroom($toteachid,$studentid);
        $this->ajaxReturn($data);
    }
    /**
     * [GotoComment 课程完成评论]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           nickname  必填用户昵称
     * @param    [int]              curriculumid  必填课程id
     * @param    [int]              type 必填课程类型
     * @param    [string]           content  必填评价内容
     * @param    [int]              studentid  必填学生id
     * @param    [int]              teacherid  必填老师id
     * @param    [int]              score  必填分数
     * @param    [int]              schedulingid 排课id
     * @param   [int]              organid 机构id
     * @return   array()
     * URL:appstudent/Mycourse/gotoComment
     */
    public function gotoComment(){
        $data['curriculumid'] = $this->request->param('curriculumid');
        $data['classtype'] = $this->request->param('classtype');
        $data['content'] = $this->request->param('content');
        $data['studentid'] = $this->userid;
        $data['allaccountid'] = $this->request->param('teacherid');
        $data['score'] = $this->request->param('score');
        $data['schedulingid'] = $this->request->param('schedulingid');
        $data['lessonsid'] = $this->request->param('lessonsid');
        $data['toteachid'] = $this->request->param('toteachid');
        //测试数据
 /*       $data['nickname'] = '测试1';
        $data['curriculumid'] = 1;
        $data['classtype'] = 1;
        $data['content'] = '这是评论的内容大萨达阿达大';
        $data['studentid'] = 1;
        $data['allaccountid'] = 1;
        $data['score'] = 4;
        $data['schedulingid'] = 1;
        $data['organid'] = 1;
        $data['lessonsid'] = 1;*/
        $courseobj = new MyCourseManage();
        $lessonsarr = $courseobj->insertComment($data);
        $this->ajaxReturn($lessonsarr);
    }
    /**
     * 已结束课表查看点评
     */
    public function getFeedback(){
        $lessonsid = $this->request->param('lessonsid') ;
        $schedulingid = $this->request->param('schedulingid');
        $studentid = $this->userid;
        $organobj = new AppMyCourseManage;
        $listarr = $organobj->getFeedback($lessonsid, $schedulingid, $studentid);
        $this->ajaxReturn($listarr);
    }
    /**
     * [getAllClassList 获取学生买过的所有班级]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getAllClassList
     */
    public function getAllClassList(){
        $studentid = $this->userid;
        $obj = new MyCourseManage();
        $result = $obj->getAllClassList($studentid);
        $this->ajaxReturn( $result);
    }
    /**
     * [applyChangeClass 调班申请]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/submitApplyClasss
     */
    public function submitApplyClasss(){
        $curriculumid = $this->request->param('curriculumid');//原课程id
        $oldschedulingid = $this->request->param('oldschedulingid');//原班级id
        $newschedulingid = $this->request->param('newschedulingid');//新班级id
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->submitApplyClasss($studentid,$curriculumid,$oldschedulingid, $newschedulingid);
        $this->ajaxReturn($data);

    }
    /**
     * [getBuyCourseList 获取学生买过的所有班级]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getBuyCourseList
     */
    public function getBuyCourseList(){
        $studentid = $this->userid;
        $obj = new MyCourseManage();
        $result = $obj->getBuyCourseList($studentid);
        $this->ajaxReturn( $result);
    }
    /**
     * [ 获取可选择的课时名称]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getSelectableLessons
     */
    public function getSelectableLessons(){
        $studentid = $this->userid;
        $lessonsid = $this->request->post('lessonsid');//原课时id
        $oldschedulingid = $this->request->param('schedulingid');//原班级id
        $curriculumid = $this->request->param('curriculumid');//原班级id
        $periodid = $this->request->param('periodid');//原班级id
        $obj = new MyCourseManage();
        $result = $obj->getSelectableLessons($studentid,$lessonsid,$oldschedulingid,$curriculumid,$periodid);
        $this->ajaxReturn( $result);
    }
    /**
     * [submitApplylession 调班申请]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/submitApplyClasss
     */
    public function submitApplylession()
    {
        $curriculumid = $this->request->param('curriculumid');//原课程id
        $oldlessonsid = $this->request->param('oldlessonsid');//原班级id
        $newlessonsid = $this->request->param('newlessonsid');//新班级id
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->submitApplyLession($studentid, $curriculumid, $oldlessonsid, $newlessonsid);
        $this->ajaxReturn($data);
    }
    /**
     * 观看录播
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    courseid int   课程id
     * @param    lessonsid int   课节id
     * @return   array();
     * URL:/student/Mycourse/watchPlayback
     */
    public function watchPlayback()
    {
        $courseid = $this->request->param('courseid');
        $lessonsid= $this->request->param('lessonsid');
        $studentid = $this->userid;
        $courseobj = new AppMyCourseManage;
        $res =  $courseobj ->watchPlayback($courseid,$lessonsid,$studentid);
        $this->ajaxReturn($res);

    }
    /**
     * 查询录播课时评论
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    lessonsid int   录播课课节id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/student/Mycourse/getRecordComment
     */
    public function getRecordComment()
    {
        $lessonsid = $this->request->param('lessonsid');
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $obj = new AppMyCourseManage;
        $res =  $obj ->getRecordComment($lessonsid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
}
