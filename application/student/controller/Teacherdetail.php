<?php
/**
 * 获取老师详情 业务逻辑层
 *
 *
 */
namespace app\student\controller;
use think\Controller;
use app\student\business\TeacherManage;
use app\student\business\WebTeacherManage;
class Teacherdetail extends \Base
{
    public function __construct(){
        // 必须先调用父类的构造函数
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
    }
    /**
     * 查询老师详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    teacherid int   老师id
     * @param    organid int   机构id
     * @return   array();
     * URL:/student/Teacherdetail/getTeacherCurriculum
     */
    public function getTeacherCurriculum()
    {
        $teacherid = $this->request->param('teacherid');
        $currobj = new TeacherManage;
        $res = $currobj ->getTeacherData($teacherid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询老师的课程
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    teacherid int   老师id
     * @param    type int   classtype 课程类型  0免费课程1在售课程
     * @return   array();
     * URL:/student/Teacherdetail/getTeacherClass
     */
    public function getTeacherClass()
    {
        $teacherid = $this->request->param('teacherid');
        $classtype = $this->request->param('classtype');
        $currobj = new TeacherManage;
        $res = $currobj ->getTeacherClass($teacherid,$classtype);
        $this->ajaxReturn($res);
    }
    /**
     * 查询老师的评论
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    teacherid int   老师id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/student/Teacherdetail/getCommentList
     */
    public function getCommentList()
    {
        $teacherid = $this->request->param('teacherid');
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $scheduobj = new TeacherManage;
        $res =  $scheduobj ->getCommentData($teacherid,$pagenum,$limit);
        $this->ajaxReturn($res);

    }

}
