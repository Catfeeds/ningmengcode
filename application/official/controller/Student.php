<?php
/**
 *
 *	学生管理控制器 包括学生列表和学生详情
 *
 */
namespace app\official\Controller;
use app\official\controller\Base;
use app\official\business\FinanceManage;
use app\official\business\StudentManage;
use think\Session;
use think\Request;

class Student extends Base{
	/**
     * [getUserList 获取学生列表]
     * @Author wyx
     * @DateTime 2018-05-25
     * @return   [type]        [description]
     * URL:/official/student/getUserList
     */
    public function getUserList()
    {	
        $mobil    = $this->request->param('mobile') ;
        $nickname = $this->request->param('nickname') ;
        $pagenum  = $this->request->param('pagenum') ;
    	//机构 标识id
        // $organid = Session::get('organid');
    	//$organid = 2 ;
        //官方的机构id统一为1 zzq 2018-05-26修改
        $organid = 1;
    	$limit   = config('param.pagesize')['officialstu_userlist'] ;

    	$manageobj = new StudentManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getUserList($mobil,$nickname,$pagenum,$organid,$limit);
    	
    	// var_dump($userlist);
        $this->ajaxReturn($userlist);
        return $userlist;
    }
    /**
     * [getUserinfo 获取学生详细信息]
     * @Author  wyx
     * @DateTime 2018-05-25
     * 
     * @return   [type]       [description]
     * URL:/official/student/getUserinfo
     */
    public function getUserinfo(){
        $studentid  = $this->request->param('userid') ;
        // $studentid  = 1 ;
        //机构 标识id
        // $organid = Session::get('organid');
        //$organid = 2 ;
        //官方的机构id统一为1 zzq 2018-05-26修改
        $organid = 1;

        $manageobj = new StudentManage;
        //获取学生信息
        $userinfo  = $manageobj->getUserDetail($studentid) ;
        // var_dump($userinfo);
        $this->ajaxReturn($userinfo);
        return $userinfo;
        
    }
    
    /**
     * [changeUserStatus 启用禁用学生]
     * @Author  wyx
     * @DateTime 2018-05-25
     * 
     * @return   [type]       [description]
     * URL:/official/student/getUserinfo
     */
    public function changeUserStatus(){

        //官方的机构id统一为1 zzq 2018-05-26修改
        $organid = 1;
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

}