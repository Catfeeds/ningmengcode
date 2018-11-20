<?php
/**
 * 获取课程详情列表 业务逻辑层
 *
 *
 */
namespace app\appstudent\controller;
use app\student\business\ScheduManage;
use login\Authorize;
use think\Controller;
use app\appstudent\business\AppScheduManage;
use app\appstudent\business\OfficalAppScheduManage;
class Curriculumdetail extends Authorize
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
     * 查课程详情 课程选择
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid
     * @return   array();
     * URL:/appstudent/Curriculumdetail/chooseAllList
     */
    public function chooseAllList()
    {
		$courseid= $this->request->param('courseid');
        $scheduobj = new ScheduManage;
        $res =  $scheduobj ->chooseAllList($courseid);
        $this->ajaxReturn($res);
    }
    /**
     * 查课程详情 查询日期
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid
     * @return   array();
     * URL:/appstudent/Curriculumdetail/getCurriculumDateList
     */
    public function getCurriculumDateList()
    {
        $courseid= $this->request->param('courseid');
        $scheduobj = new ScheduManage;
        $res =  $scheduobj ->getCurriculumDateList($courseid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询推荐排课详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    scheduid  int   排课表id
     * @param   organid  int [机构id]  必选
     * @param   type int [类型id]  判断是个人中心的排课详情还是首页的 可选
     * @return   array();
     * URL:/appstudent/Curriculumdetail/getCurriculumInfo
     */
    public function getCurriculumInfo()
    {
        $scheduid = $this->request->param('scheduid');
        /*$scheduid = 1;
        $organid = 1;*/
        $organid = $this->organid;
        if($organid == 1){
            $scheduobj = new OfficalAppScheduManage;
        }else{
            $scheduobj = new AppScheduManage;
        }
        $res =  $scheduobj ->getScheduOne($scheduid,$organid);
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
     * URL:/appstudent/Curriculumdetail/getCommentList
     */
    public function getCommentList()
    {
        $curriculumid = $this->request->param('curriculumid');
        $organid = $this->request->param('organid');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
        if($organid == 1){
            $scheduobj = new OfficalAppScheduManage;
        }else{
            $scheduobj = new AppScheduManage;
        }
        $res =  $scheduobj ->getCommentData($curriculumid,$organid,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
}
