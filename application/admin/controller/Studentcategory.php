<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\StudentCategoryManage;
use think\Request;
use login\Authorize;
class Studentcategory extends Authorize
{	

    /**
     *  
     *
     */
    public function __construct(){
        parent::__construct();
		header('Access-Control-Allow-Origin: *');

    }
	/**
	 * 获取学生分类列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/StudentCategory/getCategoryList
	 */
    public function getCategoryList()
    {
        $name    = $this->request->param('name') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	$limit = config('param.pagesize')['adminStudent_Categorylist'];

    	$manageobj = new StudentCategoryManage;
    	//获取学生分类列表信息,默认分页为5条
    	$Categorylist = $manageobj->getCategoryList($name,$pagenum,$limit);
    	// var_dump($Categorylist);
        $this->ajaxReturn($Categorylist);
        return $Categorylist ;
        // return json_encode($Categorylist) ;

    }
    /**
     * 学生分类详情
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentCategory/CategoryInfo
	 */
    /* public function getCategoryinfo(){
    	$id = $this->request->param('id') ;
    	// $id = 1 ;

    	$manageobj = new StudentCategoryManage;
    	//获取学生分类列表信息,默认分页为5条
    	$Categorylist = $manageobj->getCategoryInfo($id);

    	// var_dump($Categorylist);
        $this->ajaxReturn($Categorylist);
        return $Categorylist ;
    } */
   
    /**
     * 编辑学生分类资料时获取学生分类的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentCategory/getCategoryMsg
     */
    public function getCategoryMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new StudentCategoryManage;
        //获取学生分类列表信息,默认分页为5条
        $Categorylist = $manageobj->getCategoryMsg($id);

        // var_dump($Categorylist);
        $this->ajaxReturn($Categorylist);
        return $Categorylist ;
    }
    /**
     * [addStudentCategoryMsg 添加学生分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentCategory/addStudentCategoryMsg
     */
    public function addCategory(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new StudentCategoryManage;
        //获取学生分类列表信息,默认分页为5条
        $Categorylist = $manageobj->addCategory($data);
        $this->ajaxReturn($Categorylist);
        return $Categorylist;

    }
    /**
     * [updateStudentCategoryMsg 修改学生分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentCategory/updateStudentCategoryMsg
     */
    public function updateCategory(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new StudentCategoryManage;
        //获取学生分类列表信息,默认分页为5条
        $Categorylist = $manageobj->updateCategory($data);
        $this->ajaxReturn($Categorylist);
        return $Categorylist ;

    }
    
    /**
     * 切换学生分类的启用状态标记
     * @Author lc
     * @param 使用organid 做查询
     * @return 
     * URL:/admin/StudentCategory/switchCategoryStatus
     */
    public function switchCategoryStatus(){
        $id = $this->request->param('id');
        // $id =  5;
        $status  = $this->request->param('status');
        // $status =  5;

        $manageobj = new StudentCategoryManage;
        //获取学生分类列表信息,默认分页为5条
        $lablelist = $manageobj->switchCategoryStatus($id,$status);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     * [deleteStudentCategory 删除学生分类]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentCategory/deleteStudentCategory
     */
    public function deleteCategory(){
        $id = $this->request->param('id');
        // $id =  11;var_dump($id);exit;

        $manageobj = new StudentCategoryManage;
        //获取学生分类列表信息,默认分页为5条
        $delflag = $manageobj->delCategory($id);
        
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
	
	/**
	 * 获取所有类型
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]  [description]
	 */
    public function getAllCategoryList()
    {
    	$manageobj = new StudentCategoryManage;
    	$list = $manageobj->getAllCategoryList();
        $this->ajaxReturn($list);
        return $list ;
    }
}
