<?php

namespace app\appteacher\controller;

use think\Controller;
use think\Request;
use app\appteacher\business\CurriculumModule;
use app\appteacher\business\AllteacherInfo;
use app\appteacher\business\Classesbegin;
use login\Authorize;
//目前教师只有对大班课小班课的课时进行处理

class Course extends Authorize
{
    public $organid;
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
    
        $this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }
    /**
     * @ PC 后台开课列表
     * @author  JCR
     * @param name 课程名称
     * @param type 班级类型
     * $return array()
     * POST | URL:/teacher/Course/getSchedulingList
     */
    public function getSchedulingList(){
        $data = Request::instance()->POST(false);
         $data['teacherid'] = $this->teacherid;
         $data['organid'] = $this->organid;
         //$data['name'] = '高级成功学大讲堂';
        $classesbegin = new Classesbegin;
        $dataReturn = $classesbegin->getSchedulinglists($data,$data['limit']);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }

    /**
     * 开课选择 课程列表接口
     * @Author jcr
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
            $curriculum = new CurriculumModule();
            $dataReturn = $curriculum->getCurricukumlist($data,20);
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

        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->getSchedulingInfo($data,$this->teacherid,$this->organid);
        $this->ajaxReturn($dataReturn);

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
        //$data['curriculumid'] =1;
        // $data = array(
        //     'curriculumid'=>30,
        //     'type'=>1,
        //     'price'=>1,
        //     'teacherid'=>1,
        //     );

        // $data = array(
        //     'id'=>42,
        //     'curriculumid'=>32,
        //     'type'=>3,
        //     'totalprice'=>1,
        //     'teacherid'=>1,
        //     'gradename'=>'班级名称',
        //     'classnum'=>23,
        //     'list'=>[
        //             0=>[
        //                 'intime'=>'2018-05-09',
        //                 'teacherid'=>2,
        //                 'timekey'=>'35',
        //                 'id'=>'43',
        //                 'unitsort'=>1
        //             ],
        //             1=>[
        //                 'intime'=>'2018-05-08',
        //                 'teacherid'=>1,
        //                 'timekey'=>'35',
        //                 'id'=>'44',
        //                 'unitsort'=>2
        //             ],
        //             2=>[
        //                 'intime'=>'2018-05-10',
        //                 'teacherid'=>1,
        //                 'timekey'=>'35',
        //                 'id'=>'45',
        //                 'unitsort'=>3
        //             ]
        //         ]
        //     );
        //$data['teacherid'] = $this->teacherid;
        $data['classnum'] = 1;
        $dataReturn = $classesbegin->addclassEdit($data, $this->organid,$this->teacherid);
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
        $data['organid'] = $this->organid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->enrollStudent($data);
        $this->ajaxReturn($dataReturn);
        // var_dump($dataReturn);
    }
    /**
     * [enrollStudent 开课编辑]
     * @param  id       id 开课表id
     * @param  $status  0是暂停招生，1是未暂停招生
     * @return [type] [description]
     * URL:/teacher/Course/enrollStudent
     */
     public function editClassforapp(){
       $data = Request::instance()->post(false);
       $data['organid'] = $this->organid;
       $classesbegin = new Classesbegin();
       $dataReturn = $classesbegin->editClassforapp($data);
       $this->ajaxReturn($dataReturn);
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
        $data['organid'] = $this->organid;
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

    /**
     * 课程分类
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getCategoryArr
     */
    public function getCategoryArr()
    {
        //$organid = $this->request->param('organid');
        $organid = $this->organid;
        $courseobj = new \app\appteacher\business\CurriculumModule;
        $res =  $courseobj ->getCategoryArr($organid);
        $this->ajaxReturn($res);

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
        $organid = $this->organid;
        $teacherid = $this->teacherid;
        $fatherid = $this->request->param('fatherid');
        $categoryid = $this->request->param('category_id');
        //分页页数
        $pagenum = $this->request->param('pagenum');
        //
        $limit = $this->request->param('limit');
        //默认0
        $tagids = $this->request->param('tagids');
        $type = $this->request->param('type');
       /* $organid = 1;
        $fatherid = 0;
        $categoryid = 0;
        $tagids = 2;
        $limit = 8;*/
        $courseobj = new  CurriculumModule;
        $res =  $courseobj ->searchByCids($type,$teacherid,$organid,$fatherid,$categoryid,$tagids,$pagenum,$limit);
        $this->ajaxReturn($res);

    }

    /**
     * @ 后台分类列表 和查询后台子类
     * @Author jcr
     * @param $data['fatherid'] 父级分类id
     * POST | URL:/admin/Course/getCategoryIdList
     **/
    public function getCategoryIdList(){
        $data = Request::instance()->POST(false);
        //模拟测试
        $data['limit'] = isset($data['pagenum'])?$data['pagenum']:1;
        $curriculum = new \app\admin\business\CurriculumModule();
        $dataReturn = $curriculum->getCategoryIdList($data,20);
        $this->ajaxReturn($dataReturn);
        // $this->ajaxReturn($dataReturn);
    }



}
