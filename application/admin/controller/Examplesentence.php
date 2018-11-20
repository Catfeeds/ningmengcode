<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\ExampleManage;
use think\Request;
use login\Authorize;
class Examplesentence extends Authorize
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
	 * 获取例句列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/Examplesentence/getExampleList
	 */
    public function getExampleList()
    {	
        $type    = $this->request->param('type') ;
        $content    = $this->request->param('content') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	//$limit = config('param.pagesize')['admin_Examplelist'];

    	$manageobj = new ExampleManage;
    	//获取例句列表信息,默认分页为5条
    	$Examplelist = $manageobj->getExampleList($type,$content,$pagenum,20);
    	// var_dump($Examplelist);
        $this->ajaxReturn($Examplelist);
        return $Examplelist ;
        // return json_encode($Examplelist) ;

    }
   
    /**
     * 编辑例句资料时获取例句的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Examplesentence/getExampleMsg
     */
    public function getExampleMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new ExampleManage;
        //获取例句列表信息,默认分页为5条
        $Examplelist = $manageobj->getExampleMsg($id);

        // var_dump($Examplelist);
        $this->ajaxReturn($Examplelist);
        return $Examplelist ;
    }
	
    /**
     * [addExampleMsg 添加例句信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Examplesentence/addExampleMsg
     */
    public function addExample(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new ExampleManage;
        //获取例句列表信息,默认分页为5条
        $Examplelist = $manageobj->addExample($data);
        $this->ajaxReturn($Examplelist);
        return $Examplelist;

    }
	
    /**
     * [updateExampleMsg 修改例句信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Examplesentence/updateExampleMsg
     */
    public function updateExample(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new ExampleManage;
        //获取例句列表信息,默认分页为5条
        $Examplelist = $manageobj->updateExample($data);
        $this->ajaxReturn($Examplelist);
        return $Examplelist ;

    }
    
    /**
     * [deleteExample 删除例句]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/Examplesentence/deleteExample
     */
    public function deleteExample(){
        $id = $this->request->param('id');
        // $id =  5;

        $manageobj = new ExampleManage;
        //获取例句列表信息,默认分页为5条
        $delflag = $manageobj->delExample($id);
        // var_dump($lablelist);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
}
