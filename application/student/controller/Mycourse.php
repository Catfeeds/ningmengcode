<?php
/**
 * 获取推荐老师列表 业务逻辑层
 *
 *
 */
namespace app\student\controller;
use app\student\model\Ordermanage;
use login\Authorize;
use think\Controller;
use app\student\business\MyCourseManage;
use app\student\business\WebMyCourseManage;
class Mycourse extends Authorize
{
    /*public function __construct(Request $request = null)
    {
        $this->checktokens(1);
    }*/
    public function _initialize()
    {
        parent::_initialize();
        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
        $this->nickname = $this->userInfo['info']['nickname'];

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
        /*$limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_curriculum'];
        $courseobj = new MyCourseManage;
        $res =  $courseobj ->getBuyList($studentid,$coursetype,$pagenum,$limit);
        $this->ajaxReturn($res);

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
        $courseobj = new MyCourseManage;
        $res =  $courseobj ->getClassSchedule($coursetype,$courseid,$schedulingid);
        $this->ajaxReturn($res);

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
        $courseobj = new MyCourseManage;
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
        $obj = new MyCourseManage;
        $res =  $obj ->getRecordComment($lessonsid,$pagenum,$limit);
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
     * URL:/student/Mycourse/getLessionsList
     */
    public function getLessionsList()
    {
        $schedulingid = $this->request->param('schedulingid');
        $ordernum = $this->request->param('ordernum');
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $res = $currobj->getLessionsInfo($schedulingid,$studentid,$ordernum);
        $this->ajaxReturn($res);

    }
    /**
     * [studentCourseList 学生课表]
     * @Author yr
     * @DateTime 2018-04-25T09:44:53+0800
     * @return   [type]                   [description]
     * URL:/student/Mycourse/studentCourseList
     */
    public function studentCourseList(){
        //$organid = Session::get('organid');
        $studentid = $this->userid;
        //指定 获取的 时间
        $date = $this->request->param('date') ;
        //如果没有提供 使用当前日期
        if(empty($date)) $date = date('Y-m-d') ;
        $organobj = new MyCourseManage;
        $listarr = $organobj->studentCourseList($date,$studentid);
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
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $lessonsarr = $currobj->getLessonsByDate($date,$studentid);
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
     * [getTeacherComment 获取老师点评]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getTeacherComment
     */
    public function getTeacherComment(){
        $lessonsid = $this->request->param('lessonsid');
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->getTeacherComment($lessonsid,$studentid);
        $this->ajaxReturn($data);

    }
    /**
     * [getHomeworkByLessionid 通过课时查看学生作业]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getHomeworkByLessionid
     */
    public function getHomeworkByLessionid(){
        $lessonsid = $this->request->param('lessonsid');
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->getHomeworkByLessionid($lessonsid,$studentid);
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
        $currobj = new MyCourseManage;
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
     * URL:student/Mycourse/gotoComment
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
      /*  $data['nickname'] = '测试1';
        $data['curriculumid'] = 1;
        $data['classtype'] = 1;
        $data['content'] = '这是评论的内容大萨达阿达大';
        $data['studentid'] = 1;
        $data['allaccountid'] = 1;
        $data['score'] = 4;
        $data['schedulingid'] = 1;
        $data['organid'] = 1;
        $data['lessonsid'] = 1;*/
        $currobj = new MyCourseManage;
        $lessonsarr = $currobj->insertComment($data);
        $this->ajaxReturn($lessonsarr);
    }
    /**
     * [intoClassroom 通过toteachid获取进入教室相关信息]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/viewLessonsDetails
     */
    public function viewLessonsDetails(){
        $toteachid = $this->request->param('toteachid');
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->intoClassroom($toteachid,$studentid);
        $this->ajaxReturn($data);

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
    public function submitApplylession(){
        $curriculumid = $this->request->param('curriculumid');//原课程id
        $oldlessonsid = $this->request->param('oldlessonsid');//原班级id
        $newlessonsid = $this->request->param('newlessonsid');//新班级id
        $studentid = $this->userid;
        $currobj = new MyCourseManage;
        $data = $currobj->submitApplyLession($studentid,$curriculumid,$oldlessonsid,$newlessonsid);
        $this->ajaxReturn($data);

    }
}
