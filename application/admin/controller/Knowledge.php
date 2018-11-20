<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\KnowledgeManage;
use think\Request;
use login\Authorize;
class Knowledge extends Authorize
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
	 * 获取知识列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/StudentKnowledge/getKnowledgeList
	 */
    public function getKnowledgeList()
    {	
        $content    = $this->request->param('content') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	$limit = config('param.pagesize')['admin_Knowledgelist'];

    	$manageobj = new KnowledgeManage;
    	//获取知识列表信息,默认分页为5条
    	$Knowledgelist = $manageobj->getKnowledgeList($content,$pagenum,$limit);
    	// var_dump($Knowledgelist);
        $this->ajaxReturn($Knowledgelist);
        return $Knowledgelist ;
        // return json_encode($Knowledgelist) ;

    }
    /**
     * 知识详情
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentKnowledge/KnowledgeInfo
	 */
    /* public function getKnowledgeinfo(){
    	$id = $this->request->param('id') ;
    	// $id = 1 ;

    	$manageobj = new KnowledgeManage;
    	//获取知识列表信息,默认分页为5条
    	$Knowledgelist = $manageobj->getKnowledgeInfo($id);

    	// var_dump($Knowledgelist);
        $this->ajaxReturn($Knowledgelist);
        return $Knowledgelist ;
    } */
   
    /**
     * 编辑知识资料时获取知识的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentKnowledge/getKnowledgeMsg
     */
    public function getKnowledgeMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new KnowledgeManage;
        //获取知识列表信息,默认分页为5条
        $Knowledgelist = $manageobj->getKnowledgeMsg($id);

        // var_dump($Knowledgelist);
        $this->ajaxReturn($Knowledgelist);
        return $Knowledgelist ;
    }
    /**
     * [addKnowledgeMsg 添加知识信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentKnowledge/addKnowledgeMsg
     */
    public function addKnowledge(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new KnowledgeManage;
        //获取知识列表信息,默认分页为5条
        $Knowledgelist = $manageobj->addKnowledge($data);
        $this->ajaxReturn($Knowledgelist);
        return $Knowledgelist;

    }
	
    /**
     * [updateKnowledgeMsg 修改知识信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentKnowledge/updateKnowledgeMsg
     */
    public function updateKnowledge(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new KnowledgeManage;
        //获取知识列表信息,默认分页为5条
        $Knowledgelist = $manageobj->updateKnowledge($data);
        $this->ajaxReturn($Knowledgelist);
        return $Knowledgelist ;

    }
    
    /**
     * [deleteKnowledge 删除知识]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentKnowledge/deleteKnowledge
     */
    public function deleteKnowledge(){
        $id = $this->request->param('id');
        // $id =  5;

        $manageobj = new KnowledgeManage;
        //获取知识列表信息,默认分页为5条
        $delflag = $manageobj->delKnowledge($id);
        // var_dump($lablelist);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
}
