<?php
/**
 * 获取老师详情 业务逻辑层
 *
 *
 */
namespace app\appstudent\controller;
use app\student\business\TeacherManage;
use login\Authorize;
use think\Controller;
use app\appstudent\business\AppTeacherManage;
use app\appstudent\business\OfficalAppTeacherManage;
class Teacherdetail extends Authorize
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
     * 查询老师详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    teacherid int   老师id
     * @param    organid int   机构id
     * @return   array();
     * URL:/microsite/Teacherdetail/getTeacherCurriculum
     */
    public function getTeacherCurriculum()
    {
        $teacherid = $this->request->param('teacherid');
        $currobj = new AppTeacherManage;
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
     * URL:/microsite/Teacherdetail/getTeacherClass
     */
    public function getTeacherClass()
    {
        $teacherid = $this->request->param('teacherid');
        $classtype = $this->request->param('classtype');
        $currobj = new TeacherManage();
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
     * URL:/appstudent/Teacherdetail/getCommentList
     */
    public function getCommentList()
    {
        $teacherid = $this->request->param('teacherid');
        $organid = $this->request->param('organid');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
        $organid = $this->request->param('organid');
        if($organid == 1){
            $currobj = new OfficalAppTeacherManage;
        }else{
            //
            $currobj = new AppTeacherManage;
        }
        $res =  $currobj ->getCommentData($teacherid,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
}
