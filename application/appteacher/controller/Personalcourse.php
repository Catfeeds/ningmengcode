<?php

namespace app\appteacher\controller;

use think\Controller;
use think\Request;
use app\appteacher\business\TeacherManage;
use app\appteacher\business\CurriculumModule;
use app\appteacher\business\AllteacherInfo;
use app\teacher\model\Filemanage;
use app\teacher\model\Lessons;
use login\Authorize;
class Personalcourse extends Authorize
{
    public $organid;
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Origin: *');
        $this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }
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
        $data['organid']=$this->organid;
        //如果没有提供 使用当前日期
        if(empty($data['date'])) $data['date'] = date('Y-m-d') ;

        $teachobj  = new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $listarr = $teachobj->teachCourseList($data['date'],$data['teacherid'],$data['organid']);
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
        $data['date']='2018-03-26';
        $data['teacherid']=$this->teacherid;
        $data['organid']=$this->organid;
        $data['pagesize']= 10;
        empty($data['pagenum'])?$data['pagenum']:1;
        $organobj  = new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $organobj->getLessonsByDate($data['pagenum'],$data['pagesize'],$data['date'],$data['teacherid'],$data['organid']);
        $this->ajaxReturn($Lessonsarr);

    }
    /**
     * [getLessonsByDate 获取该教师的开始和结束的课程，按照时间排列]
     * @Author wyx
     * @DateTime 2018-04-25T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:/teacher/PersonalCourse/getLessonsByDate
     */
    public function getLessonsByall(){
        $data = Request::instance()->POST(false);
        $teacherid=$this->teacherid;
        $organid=$this->organid;
        $timesize = 10;//当前每页显示几天的数据
        empty($data['datecode'])?0:$data['datecode'];
        $organobj  = new CurriculumModule;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $organobj->getLessonsByall($data['date'],$teacherid,$organid,$data['datecode'],$data['pagenum'],$timesize);
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
        $data['organid'] =$this->organid;
         // $data['toteachtimeid']=1;
        $data['teacherid']=$this->teacherid;
         // $data['id'] =1;
        $data['limit'] = 10;
         // $data['starttime'] = '2018-5-8 6:30:23';//该课时开始时间
         // $data['endtime'] = '2018-5-8 19:30:23';//该课时结束时间
         // $data['date']='2018-5-8';
        //$limitstr =5;
        // $pagenum =1;//默认第一页
        // $pagesize = 10;
        // $date = '2018-5-8' ;
        $period = new CurriculumModule;
        $list = $period->getPeriodList($data);
        $this->ajaxReturn($list);
    }
    /**
     * 课时查询信息课时详情
     * @Author wangwy
     * @param 使用$teacherid 做查询
     * @return [type]                   [description]
     * URL:/teacher/PersonalCourse/getperComment
     */

    public function getperComment(){
        $data = Request::instance()->POST(false);
        //$teacherid = $this->request->param('teacherid');
        $organid = $this->organid;
        $data['teacherid']=$this->teacherid;
         // $data['pagesize']=10;
         // $data['date']='2018-5-8';
         //$limitstr =5;
         // $pagenum =1;//默认第一页
         // $pagesize = 10;
         // $date = '2018-5-8' ;
        $period = new CurriculumModule;
        $list = $period->getperComment($data['teacherid'],$organid,$data['lessonsid'],$data['date'],$data['pagenum'],$data['pagesize']);
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
        $organid = $this->organid ;
        $currobj  =  new MyCourseManage;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $currobj->getLessonsPlayback($toteachid,$organid);
        $this->ajaxReturn($Lessonsarr);

    }



     /**
     * [getWarefile 课时相关的文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    public function getWarefile(){
        $data = $this->request->param(false);
        //$data['showname'] = '文件夹1';
        //$data['limit']  = 1;
        //$data['id'] =1;
        $classesbegin = new CurriculumModule();
        $dataReturn = $classesbegin->getWarefile($data);
        $this->ajaxReturn($dataReturn);
    }

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
         //$data['id'] =1;//lessons表主键
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
     * URL:appteacher/Personalcourse/intoClassroom
     */
    public function intoClassroom(){
        $toteachid = $this->request->param('toteachid');
        $organid = $this->organid;
        $teacherid = $this->teacherid;
        //$toteachid = '39';
        $currobj  =  new CurriculumModule;
        $data = $currobj->intoClassroom($toteachid,$teacherid,$organid);
        $this->ajaxReturn($data);

    }

}
