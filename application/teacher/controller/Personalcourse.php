<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use app\teacher\business\TeacherManage;
use app\teacher\business\CurriculumModule;
use app\teacher\business\AllteacherInfo;
use app\teacher\model\Filemanage;
use app\teacher\model\Lessons;
use app\teacher\business\Classesbegin;
use login\Authorize;
class Personalcourse extends Authorize
//class Personalcourse extends Controller
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        //$this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }
    protected $pagesize = 20;
    /**
     * [organCourseList 教师个人当月课表]
     * @Author wyx
     * @DateTime 2018-04-25T09:44:53+0800
     * @return   [type]                   [description]
     * URL:/teacher/PersonalCourse/teachCourseList
     */
    public function teachCourseList(){

        $data = Request::instance()->POST(false);
        //$organid = Session::get('organid');
        // $organid = 2 ;
        //$teacherid = Session::ger('teacherid');
        // $teacherid = 1;
        //指定 获取的 时间
        // $date = $this->request->param('date') ;
        //$data['date']='2018-05-15';
        $data['teacherid']=$this->teacherid;
        //如果没有提供 使用当前日期
        if(empty($data['date'])) $data['date'] = date('Y-m-d') ;
        $teachobj  = new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $listarr = $teachobj->teachCourseList($data['date'],$data['teacherid']);
        $this->ajaxReturn($listarr);

    }
    /**
     * [getLessonsByDate 通过日期获取该教师当日课程列表]
     * @Author wyx
     * @DateTime 2018-04-25T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:/teacher/PersonalCourse/getLessonsByDate
     */
    public function getLessonsByDate(){
        $data = Request::instance()->POST(false);
        //$organid = Session::get('organid');
        // $organid = 2 ;
        //$teacherid = Session::get('teacherid');
        // $teacherid =1;
        // $date = $this->request->param('date') ;
        // $date = '2018-03-26' ;
        //$data['date']='2018-03-26';
        $data['teacherid']=$this->teacherid;
        $data['pagesize']= $this->pagesize;
        empty($data['pagenum'])?$data['pagenum']:1;
        $organobj  = new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $organobj->getLessonsByDate($data['pagenum'],$data['pagesize'],$data['date'],$data['teacherid']);
        $this->ajaxReturn($Lessonsarr);

    }



    /**
     * 课时查询信息课时详情
     * @Author wangwy
     * @param 使用$teacherid 做查询
     * @return [type]                   [description]
     * URL:/teacher/PersonalCourse/getPeriodinfo
     */

    public function getPeriodinfo(){
         $data = Request::instance()->POST(false);
         //$data['toteachtimeid']=1;
         $data['teacherid']=$this->teacherid;
         //$data['id'] =1;
         $data['limit'] = $this->pagesize;
         $period = new CurriculumModule;
         $list = $period->getPeriodList($data);
         $this->ajaxReturn($list);
    }

    /** 学生出勤表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttendance(){
         $data = Request::instance()->POST(false);
         $lessonsid = $data['lessonsid'];
         $teacherid = $this->teacherid;
         $pagenum = $data['pagenum'];
         $pagesize = 10;
         $period = new CurriculumModule;
         $list = $period->getAttendance($lessonsid,$teacherid,$pagenum,$pagesize);
         $this->ajaxReturn($list);
    }

    /**
     * 该课时学生列表
     */
    public function periodStulist(){
        $data = Request::instance()->POST(false);
        $toteachtimeid = isset($data['toteachtimeid'])?$data['toteachtimeid']:null;
        $schedulingid = isset($data['schedulingid'])?$data['schedulingid']:null;
        $pagenum = $data['pagenum'];
        $pagesize = 10;
        $period = new CurriculumModule;
        $list = $period->periodStulist($pagenum,$pagesize,$this->teacherid,$toteachtimeid,$schedulingid);
        $this->ajaxReturn($list);
    }

    /**
     *  批阅学生出勤情况
     */
    public function upAttendance(){
         $data = Request::instance()->POST(false);
         $period = new CurriculumModule;
         $list = $period->upAttendance($data,$this->teacherid);
         $this->ajaxReturn($list);
    }

    /**
     * 课时查询评价
     * @Author wangwy
     * @param 使用$teacherid 做查询
     * @return [type]                   [description]
     * URL:/teacher/PersonalCourse/getperComment
     */

    public function getperComment(){
        $data = Request::instance()->POST(false);
        //$teacherid = $this->request->param('teacherid');
        //$organid = Session::get('organid');
        // $organid = 1;
        // $totechatimeid = 1; //toteachtime的主键id
        // $lessonsid = 1;
        // $teacherid = 1;
         $data['teacherid']=$this->teacherid;
         $data['pagesize']=10;
//         $data['date']='2018-5-8';
        //$limitstr =5;
        // $pagenum =1;//默认第一页
        // $pagesize = 10;
        // $date = '2018-5-8' ;
        $period = new CurriculumModule;
        $list = $period->getperComment($data['teacherid'],$data['lessonsid'],$data['date'],$data['pagenum'],$data['pagesize']);

        //var_dump($list);
        $this->ajaxReturn($list);
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
        $currobj  =  new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $currobj->getLessonsPlayback($toteachid,$this->teacherid);
        $this->ajaxReturn($Lessonsarr);

    }



     /**
     * [getWarefile 课时相关的文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    // public function getWarefile(){
    //     $data = $this->request->param(false);
    //     //$data['showname'] = '文件夹1';
    //     //$data['limit']  = 1;
    //     //$data['id'] =1;
    //     $classesbegin = new CurriculumModule();
    //     $dataReturn = $classesbegin->getWarefile($data);
    //     $this->ajaxReturn($dataReturn);
    // }

     /**
     * [addWarefile 添加课时相关的文件夹列表和 资源列表关联]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    public function addWarefile(){
        $data = Request::instance()->post(false);
        //$data['showname'] = '文件夹1';
        //$data['limit']  = 1;
        // $data['id'] =1;//lessons表主键
        // $data['fileid'] = [1,2];//课件对应的fileid数组
        $classesbegin = new CurriculumModule();
        $dataReturn = $classesbegin->addCourseware($data,$fileid=$data['fileid']);
        $this->ajaxReturn($dataReturn);
    }

     /**
     * [delWarefile 删除课时相关的文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    public function delWarefile(){
        $data = Request::instance()->post(false);
        // $data['showname'] = '文件夹1';
        // $data['limit']  = 1;
        //$data['id'] =1;
        //$data['fileid'] = [1,2];
        $classesbegin = new CurriculumModule();
        $dataReturn = $classesbegin->delCourseware($data,$fileid=$data['fileid']);
        $this->ajaxReturn($dataReturn);
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
        $teacherid = $this->teacherid;
        //$toteachid = '39';
        $currobj  =  new CurriculumModule;
        $data = $currobj->intoClassroom($toteachid,$teacherid);
        $this->ajaxReturn($data);

    }
    /*
     *  @Author wangwy
     *  展示该班级的所有信息详情
     getAttendance*/
    public function showSchedulCurriinf(){
        $data = Request::instance()->post(false);
        $teacherid = $this->teacherid;
        $dirobj = new Classesbegin();
        $res = $dirobj->getSchedulCurriinf($data,$teacherid);
        $this->ajaxReturn($res);
    }






}
