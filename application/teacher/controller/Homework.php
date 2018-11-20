<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use app\teacher\business\HomeworkModule;
use login\Authorize;
class Homework extends Authorize
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $this->teacherid = $this->userInfo['info']['uid'];
        $this->pagesize = 20;
    }

    /**
     * 布置作业
     * @Author wangwy
     * @return \think\Response
     */
    public function arrangeHomework()
    {
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $re = $obj->arrangeHomework($data['schedulingid'],$data['lessonsid'],$data['starttime'],$data['endtime'],$this->teacherid);
        $this->ajaxReturn($re);
    }

    /**
     *  作业选项列表
     */
    public function getChoicelist(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $re = $obj->getChoicelist($data['schedulingid'],$data['periodid'],$data['lessonid'],$this->teacherid);
        $this->ajaxReturn($re);
    }

    /**
     * 展示作业列表
     * @Author wangwy
     * @return \think\Response
     */
    public function scheHomeworkList()
    {
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $teacherid = $this->teacherid;
        $pagesize = $this->pagesize;
        $re = $obj->scheHomeworkList($teacherid,$data['pagenum'],$pagesize,$data['coursename'],$data['schedulingname']);
        $this->ajaxReturn($re);
    }

    /** 以课时为单位统计作业
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function showLesssonsHomeList(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $teacherid = $this->teacherid;
        $pagesize = $this->pagesize;
        $re = $obj->showLesssonsHomeList($teacherid,$data['pagenum'],$pagesize,$data['schedulingid'],$data['curriculumid'],$data['periodname']);
        $this->ajaxReturn($re);
    }

    /**
     * 学员交作业明细
     * @author wangwy
     */
    public function stuHomeworkList(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $teacherid = $this->teacherid;
        $pagesize = $this->pagesize;
        $re = $obj->stuHomeworkList($teacherid,$data['pagenum'],$pagesize,$data['reviewstatus'],$data['curriculumid'],$data['schedulingid'],$data['lessonsid'],$data['studentname']);
        $this->ajaxReturn($re);
    }

    /**
     * 学生作业详情
     * @author wangwy
     */
    public function showExerciselist(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $re = $obj->showExerciselist($data['schedulingid'],$data['curriculumid'],$data['lessonsid'],$data['studentid']);
        $this->ajaxReturn($re);
    }

    /**
     * 批阅作业
     * @author wangwy
     */
    public function showMarking(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $teacherid = $this->teacherid;
        $re = $obj->showMarking($teacherid,$data['lessonsid'],$data['studentid'],$data['a'],$data['b'],$data['c'],$data['d']);
        $this->ajaxReturn($re);
    }

    /**
     * 作业统计表
     * @author wangwy
     */
    public function totalHomework(){
        $data = Request::instance()->post(false);
        $obj = new HomeworkModule();
        $teacherid = $this->teacherid;
        $re = $obj->totalHomework($teacherid,$data['schedulingid'],$data['curriculumid'],$data['lessonsid']);
        $this->ajaxReturn($re);
    }


}
