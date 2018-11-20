<?php

namespace app\appteacher\controller;

use think\Controller;
use think\Request;
use think\Session;
use app\teacher\business\StudentManage;
use login\Authorize;
//主要用于学生管理

class Student extends Authorize
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
     * [getUserList 获取学生列表]
     * @Author wangwy
     * @return   [type]        [description]
     * URL:/teacher/student/getUserList
     */
    public function getUserList()
    {
        $data = Request::instance()->post(false);
        //$mobil    = $this->request->param('mobile') ;
        //$nickname = $this->request->param('nickname') ;
        //$pagenum  = $this->request->param('pagenum') ;
        // 标识教師id
        $teacherid = $this->teacherid ;
        $organid = $this->organid;
        $limit=10;
        //$mobile = '18888888';
        //$pagenum = 1;
      //  $nickname = 'llll';
        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist = $manageobj->getUserList($data['mobil'],$data['nickname'],$data['pagenum'],$teacherid,$organid,$limit);

        $this->ajaxReturn($userlist);
        //return json_encode($userlist);
    }





      /**
     * [getUserinfo 获取学生详细信息]
     * @Author  wangwy
     *
     * @return   [type]       [description]
     * URL:/teacher/student/getUserinfo
     */
    public function getUserinfo(){
        $studentid  = $this->request->param('userid') ;
        //$studentid  = 1 ;
        //机构 标识id
        //$organid = Session::get('organid');
        $organid = $this->organid ;

        $manageobj = new StudentManage;
        //获取学生信息
        $userinfo  = $manageobj->getUserDetail($studentid,$organid) ;
        $this->ajaxReturn($userinfo);

    }





    /**
     * [addUser  添加学生信息]
     * @Author wangwy
     * URL:/teacher/student/addUser
     */
    public function addUser(){
      $data = Request::instance()->post(false);

        // $data = [
        //     'mobile'=> '18801222354',
        //     'sex'=> 1,
        //     'nickiname'=>'hahahahah'
        //     ] ;
        //机构 标识id
        //$teacherid = Session::get('teacherid');
        $teacherid = $this->teacherid ;
        $organid = $this->organid;
        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->addStudentInfo($data,$teacherid,$organid);
        $this->ajaxReturn($userlist) ;

    }



    /**
     * [addUser  修改学生信息]
     * @Author wangwy
     * URL:/teacher/student/updateUser
     */
    public function updateUser(){
        $data = $this->request->param('');

        $data = [
            'imageurl'=> 'iamgeurl.com',
            'mobile'=> '18801222354',
            'nickname'=> 'nickname',
            'username'=> 'true name',
            'sex'=> 1,
            'country'=> 33,
            'province'=> 66,
            'city'=> 45,
            'birth'=> '18-4-6',
            'profile'=> 'today is a good day',
            'prphone' => '+86' ,
            'jiashouju' => false ,
            'id' => 2,
            ] ;
        //机构 标识id
        //$teacherid = Session::get('teacherid');
        $teacherid = $this->teacherid ;

        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->updateStudentInfo($data,$teacherid);
        $this->ajaxReturn($userlist) ;

    }






    /**
     * [delUser 删除学生信息]
     * @Author wangwy
     * @return   [array]          [操作结果]
     * URL:/teacher/student/delUser
     */
    public function delUser(){
        $userid = $this->request->param('userid');
        $userid = 2 ;

        //机构 标识id
        //$teacherid = Session::get('organid');
        $teacherid = 2 ;

        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->delStudent($userid,$teacherid);
        $this->ajaxReturn($userlist) ;

    }

    /**
    * [教师添加的学生列表]
    * @Author $WangWY
    * @return [array]
    * URL:/teacher/sutdent/teachersList
    */
    public function tchStudentList(){
      //$data = Request::instance()->post(false);
      $motest = new StudentManage;
      $data['teacherid'] =$this->teacherid;
      $data['organid'] = $this->organid;
      $teacherList = $motest->tchstuList($data);
      $this->ajaxReturn($teacherList);
    }
    /**
    * [教师为学生重置密码]
    * @Author $WangWY
    * @return [array]
    * URL:/teacher/sutdent/resetPassword
    */
    public function resetPassword(){
      $data = Request::instance()->post(false);
      $motest = new StudentManage;
      //$data['mobile'] = '1333333';
      $data['organid'] = $this->organid;
      empty($data['prphone'])?'86':$data['prphone'];
      $teacherList = $motest->resetPassword($data);
      $this->ajaxReturn($teacherList);
    }


}
