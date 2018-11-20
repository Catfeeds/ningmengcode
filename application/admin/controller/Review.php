<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\admin\business\ReviewManage;
use app\admin\business\CompositionManage;
use login\Authorize;
class Review extends Authorize
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
    public function getReviewList()
    {	
        $teachername = $this->request->param('teachername');
        $pagenum  = $this->request->param('pagenum');
    	$limit   = config('param.pagesize')['adminstu_userlist'] ;
		
    	$manageobj = new ReviewManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getReviewList($teachername,$pagenum,$limit);
        $this->ajaxReturn($userlist);
        return $userlist;
    }
	
	/**
     * [getUserList 获取学生列表]
     * @Author wyx
     * @DateTime 2018-04-20T11:01:54+0800
     * @return   [type]        [description]
     * URL:/admin/student/getUserList
     */
    public function getTeacherReviewList()
    {	
        $teacherid = $this->request->param('teacherid');
        $nickname = $this->request->param('nickname');
        $pagenum  = $this->request->param('pagenum');
    	$limit   = config('param.pagesize')['adminstu_userlist'] ;
		
    	$manageobj = new ReviewManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getTeacherReviewList($teacherid,$nickname,$pagenum,$limit);
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
    public function getCompositioninfo(){
        $compositionid  = $this->request->param('compositionid') ;
         //$studentid  = 1 ;
        $manageobj = new CompositionManage;
        //获取学生信息
        $userinfo  = $manageobj->getCompositionDetail($compositionid) ;
        
        $this->ajaxReturn($userinfo);
        return $userinfo;
        
    }
    
}
