<?php

namespace app\teacher\controller;

use app\teacher\model\StudentInfo;
use app\teacher\model\TeacherInfo;
use app\teacher\model\ToteachTime;
use think\Controller;
use think\Request;
use app\teacher\business\CurriculumModule;
use app\teacher\business\AllteacherInfo;
use app\teacher\business\Classesbegin;
use login\Authorize;
//目前教师只有对大班课小班课的课时进行处理

class Course extends Authorize
//class Course extends Controller
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');

        //$this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }

    /**
     * @ PC 后台开课列表
     * @author  wangwy
     * @param name 课程名称
     * @param type 班级类型
     * $return array()
     * POST | URL:/teacher/Course/getSchedulingList
     */
    public function getSchedulingList(){
        $data = Request::instance()->POST(false);
         $data['teacherid'] = $this->teacherid;
         $pagesize = config('param.pagesize')['teacher_schedulinglist'];
         //$data['name'] = '高级成功学大讲堂';
        $classesbegin = new Classesbegin;
        $dataReturn = $classesbegin->getSchedulinglists($data,$pagesize);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }

    /**
     * 开课选择 课程列表接口
     * @Author wangwy
     * 提交方式 POST
     * @param  status  0下架 1上架
     * @param  coursename 课程名称
     * POST | URL:/teacher/Course/getCurricukum
     */
    public function getCurricukum()
    {
        //实例化课程逻辑层
//        if(IS_AJAX){
            $data = Request::instance()->POST(false);
            //模拟测试
            // $data = array('coursename'=>'听我讲土话','limit'=>1);
            $data['status'] = 1;
            $data['schedule'] = 2;
            $data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
            $pagesize = config('param.pagesize')['teacher_sche_curriculumlist'];
            $curriculum = new CurriculumModule();
            $dataReturn = $curriculum->getCurricukumlist($data,$pagesize);
            $this->ajaxReturn($dataReturn);
        // }
    }


    // /**
    //  * @ PC 后台开班列表
    //  * @php jcr
    //  * @param [type] $[name] [description]
    //  * @return array
    //  */
    // public function getSchedulingList(){
    //     $data = Request::instance()->post(false);
    //     $classesbegin = new Classesbegin();
    //     $dataReturn = $classesbegin->getSchedulinglists($data,20);
    //     var_dump($dataReturn);

    // }





    /**
     * [getSchedulingInfo 开班详情返回页]
     * @param   $curriculumid 课程id
     * @param   $id   开班id
     * @param   $type 课程类型
     * URL:/teacher/Course/getSchedulingInfo
     */
    public function getSchedulingInfo(){
        $data = Request::instance()->post(false);
         //$data = array('teacherid'=>1,'type'=>2,'curriculumid'=>2,'id'=>1);
        $teacherid = $this->teacherid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->getSchedulingInfo($data,$teacherid);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }


    /**
     * @ PC 后台开班
     * @php jcr
     * @param [type] $[name] [description]
     * @return array
     * URL:/teacher/Course/addEditScheduling
     */
    public function addEditScheduling(){
        header('Access-Control-Allow-Origin: *');
        $data = Request::instance()->post(false);
        $classesbegin = new Classesbegin();
        $data['classnum'] = 1;
        $dataReturn = $classesbegin->addClassEdit($data,$this->teacherid);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);

    }

    /**
     * [enrollStudent 暂停招生]
     * @param  id       id 开课表id
     * @param  $status  0是暂停招生，1是未暂停招生
     * @return [type] [description]
     * URL:/teacher/Course/enrollStudent
     */
    public function enrollStudent(){
        $data = Request::instance()->post(false);
        // var_dump($data);
         //$data = array('id'=>27,'status'=>1);
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->enrollStudent($data);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }


    /**
     * [deleteScheduling 删除开课信息]
     * @param   $[id] [开课id]
     * @return [type] [description]
     * URL:/teacher/Course/deleteScheduling
     */
    public function deleteScheduling(){
        $data = Request::instance()->post(false);
         //$data = array('id'=>42);
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->deleteScheduling($data);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }
    /**
     * [getTimeOccupy 获取对应老师的占用时间]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getTimeOccupy(){
        $data = Request::instance()->post(false);
        $data['teacherid'] = $this->teacherid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->getTimeOccupy($data);
        $this->ajaxReturn($dataReturn);
    }
    public function rcMobile(){
        $data = Request::instance()->post(false);
        $mm = new CurriculumModule();
        $cc = $mm->rcMobile();
        $this->ajaxReturn($cc);
    }
    public function RemindMessage(){
        $data = Request::instance()->post(false);
        $mm = new CurriculumModule();
        $cc = $mm->RemindMessage();
        $this->ajaxReturn($cc);
    }


}
