<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\KnowledgeSetupManage;
use think\Request;
use login\Authorize;
class Knowledgesetup extends Authorize
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
	 * 背景图列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/StudentKnowledgeType/
	 */
    public function getSigninbgiList()
    {
    	$manageobj = new KnowledgeSetupManage;
    	$list = $manageobj->getSigninbgiList();
        $this->ajaxReturn($list);
        return $list;
    }
	
	/**
	 * 获取知识分类列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/StudentKnowledgeType/getKnowledgeTypeList
	 */
    public function getKnowledgeTypeList()
    {	
        $name    = $this->request->param('name') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	$limit = config('param.pagesize')['admin_KnowledgeTypelist'];

    	$manageobj = new KnowledgeSetupManage;
    	//获取知识分类列表信息,默认分页为5条
    	$KnowledgeTypelist = $manageobj->getKnowledgeTypeList($name,$pagenum,$limit);
    	// var_dump($KnowledgeTypelist);
        $this->ajaxReturn($KnowledgeTypelist);
        return $KnowledgeTypelist ;
        // return json_encode($KnowledgeTypelist) ;

    }
    /**
     * 知识分类详情
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentKnowledgeType/KnowledgeTypeInfo
	 */
    public function getKnowledgeTypeinfo(){
    	$id = $this->request->param('id') ;
    	// $id = 1 ;

    	$manageobj = new KnowledgeSetupManage;
    	//获取知识分类列表信息,默认分页为5条
    	$KnowledgeTypelist = $manageobj->getKnowledgeTypeInfo($id);

    	// var_dump($KnowledgeTypelist);
        $this->ajaxReturn($KnowledgeTypelist);
        return $KnowledgeTypelist ;
    }
   
    /**
     * 编辑知识分类资料时获取知识分类的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentKnowledgeType/getKnowledgeTypeMsg
     */
    public function getKnowledgeTypeMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new KnowledgeSetupManage;
        //获取知识分类列表信息,默认分页为5条
        $KnowledgeTypelist = $manageobj->getKnowledgeTypeMsg($id);

        // var_dump($KnowledgeTypelist);
        $this->ajaxReturn($KnowledgeTypelist);
        return $KnowledgeTypelist ;
    }
    /**
     * [addKnowledgeTypeMsg 添加知识分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentKnowledgeType/addKnowledgeTypeMsg
     */
    public function addKnowledgeType(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new KnowledgeSetupManage;
        //获取知识分类列表信息,默认分页为5条
        $KnowledgeTypelist = $manageobj->addKnowledgeType($data);
        $this->ajaxReturn($KnowledgeTypelist);
        return $KnowledgeTypelist;

    }
    /**
     * [updateKnowledgeTypeMsg 修改知识分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentKnowledgeType/updateKnowledgeTypeMsg
     */
    public function updateKnowledgeType(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new KnowledgeSetupManage;
        //获取知识分类列表信息,默认分页为5条
        $KnowledgeTypelist = $manageobj->updateKnowledgeType($data);
        $this->ajaxReturn($KnowledgeTypelist);
        return $KnowledgeTypelist ;

    }
    
    /**
     * [deleteKnowledgeType 删除知识分类]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentKnowledgeType/deleteKnowledgeType
     */
    public function deleteKnowledgeType(){
        $id = $this->request->param('id');
         //$id =  1;

        $manageobj = new KnowledgeSetupManage;
        //获取知识分类列表信息,默认分页为5条
        $delflag = $manageobj->delKnowledgeType($id);
        // var_dump($lablelist);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
	
	/**
     * [删除]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentKnowledgeType/
     */
    public function deleteSignInBgi(){
        $id = $this->request->param('id');
        $manageobj = new KnowledgeSetupManage;
        $delflag = $manageobj->delSigninbgi($id);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
	
	/**
	 * 二维码列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/StudentKnowledgeType/
	 */
    public function getQrList()
    {
    	$manageobj = new KnowledgeSetupManage;
    	$list = $manageobj->getQrList();
        $this->ajaxReturn($list);
        return $list;
    }
	
	/**
	 * 获取所有知识类型
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]  [description]
	 */
    public function getAllKnowledgeTypeList()
    {
    	$manageobj = new KnowledgeSetupManage;
    	$list = $manageobj->getAllKnowledgeTypeList();
        $this->ajaxReturn($list);
        return $list ;
    }
}
