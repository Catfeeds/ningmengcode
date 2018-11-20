<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\admin\business\StudentManage;
use login\Authorize;
class Student extends Authorize
{

    public function __construct(){
        parent::__construct();
    }
	/**
     * [getUserList 获取学生列表]
     * @Author wyx
     * @DateTime 2018-04-20T11:01:54+0800
     * @return   [type]        [description]
     * URL:/admin/student/getUserList
     */
    public function getUserList()
    {	
        $mobil    = $this->request->param('mobile');
        $nickname = $this->request->param('nickname');
        $pagenum  = $this->request->param('pagenum');
    	$limit   = config('param.pagesize')['adminstu_userlist'] ;
		
    	$manageobj = new StudentManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getUserList($mobil,$nickname,$pagenum,$limit);
        $this->ajaxReturn($userlist);
        return $userlist;
    }
    /**
     * [getUserinfo 获取学生详细信息]
     * @Author  wyx
     * @DateTime 2018-04-20T13:37:15+0800
     * 
     * @return   [type]       [description]
     * URL:/admin/student/getUserinfo
     */
    public function getUserinfo(){
        $studentid  = $this->request->param('userid') ;
         //$studentid  = 1 ;
        $manageobj = new StudentManage;
        //获取学生信息
        $userinfo  = $manageobj->getUserDetail($studentid) ;
        
        $this->ajaxReturn($userinfo);
        return $userinfo;
        
    }
    /**
     * [addUser  添加学生信息]
     * @Author wyx
     * @DateTime 2018-04-20T12:07:30+0800
     * URL:/admin/student/addUser
     */
    /* public function addUser(){
        $data    = Request::instance()->post();

        $manageobj = new StudentManage();
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->addStudentInfo($data);
        // var_dump($userlist) ;
        $this->ajaxReturn($userlist);
        return $userlist;

    } */
    /**
     * [updateUser  修改学生信息]
     * @Author wyx
     * @DateTime 2018-04-20T12:07:30+0800
     * URL:/admin/student/updateUser
     */
   /*  public function updateUser(){
        $data    = Request::instance()->post();
        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->updateStudentInfo($data);
        $this->ajaxReturn($userlist);
        return $userlist ;

    } */
    /**
     * [changeUserStatus 更改学生状态]
     * @Author wyx
     * @DateTime 2018-04-20T21:13:39+0800
     * @return   [array]          [操作结果]
     * URL:/admin/student/changeUserStatus
     */
    public function changeUserStatus(){
        $userid = $this->request->param('userid');
        // $userid = 2 ;
        $flag   = $this->request->param('flag');
        // $flag   = 2 ;

        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userflag  = $manageobj->changeUserStatus($userid,$flag);
        $this->ajaxReturn($userflag);
        return $userflag ;

    }
    /**
     * [delUser 删除学生信息]
     * @Author wyx
     * @DateTime 2018-04-20T21:13:39+0800
     * @return   [array]          [操作结果]
     * URL:/admin/student/delUser
     */
    /* public function delUser(){
        $userid = $this->request->param('userid');
        // $userid = 2 ;

        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userlist  = $manageobj->delStudent($userid);
        var_dump($userlist) ;

    } */
	
	/**
     * [sendStudentMessage APP推送]
     * @Author lc
     * @return   [array]          [操作结果]
     * URL:/admin/student/sendStudentMessage
     */
    public function sendStudentMessage(){
		$data = Request::instance()->post(false);
        $manageobj = new StudentManage;
        $userflag  = $manageobj->sendStudentMessage($data);
        $this->ajaxReturn($userflag);
        return $userflag ;

    }
   
	/**
     * [getHjxUserList 获取学生列表]
     * @Author wyx
     * @DateTime 2018-04-20T11:01:54+0800
     * @return   [type]        [description]
     * URL:/admin/student/getHjxUserList
     */
    public function getHjxUserList()
    {	
        $mobil    = $this->request->param('mobile');
        $nickname = $this->request->param('nickname');
        $pagenum  = $this->request->param('pagenum');
    	$limit   = config('param.pagesize')['adminstu_userlist'] ;
		
    	$manageobj = new StudentManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getHjxUserList($mobil,$nickname,$pagenum,$limit);
        $this->ajaxReturn($userlist);
        return $userlist;
    }
	
	/**
     * [changeHjxUserStatus 更改学生状态]
     * @Author wyx
     * @DateTime 2018-04-20T21:13:39+0800
     * @return   [array]          [操作结果]
     * URL:/admin/student/changeHjxUserStatus
     */
    public function changeHjxUserStatus(){
        $userid = $this->request->param('userid');
        // $userid = 2 ;
        $flag   = $this->request->param('flag');
        // $flag   = 2 ;

        $manageobj = new StudentManage;
        //获取教师列表信息,默认分页为5条
        $userflag  = $manageobj->changeHjxUserStatus($userid,$flag);
        $this->ajaxReturn($userflag);
        return $userflag ;

    }
}
