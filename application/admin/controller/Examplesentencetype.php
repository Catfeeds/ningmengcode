<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\ExampletypeManage;
use think\Request;
use login\Authorize;
class Examplesentencetype extends Authorize
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
	 * 获取例句类型列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/Examplesentencetype/getExampleTypeList
	 */
    public function getExampleTypeList()
    {	
        $name    = $this->request->param('name') ;
        $pagenum  = $this->request->param('pagenum') ;

    	$manageobj = new ExampletypeManage;
    	//获取例句类型列表信息,默认分页为5条
    	$ExampleTypelist = $manageobj->getExampleTypeList($name,$pagenum,20);
    	// var_dump($ExampleTypelist);
        $this->ajaxReturn($ExampleTypelist);
        return $ExampleTypelist ;
        // return json_encode($ExampleTypelist) ;

    }
   
    /**
     * 编辑例句类型资料时获取例句类型的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Examplesentencetype/getExampleTypeMsg
     */
    public function getExampleTypeMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new ExampletypeManage;
        //获取例句类型列表信息,默认分页为5条
        $ExampleTypelist = $manageobj->getExampleTypeMsg($id);

        // var_dump($ExampleTypelist);
        $this->ajaxReturn($ExampleTypelist);
        return $ExampleTypelist ;
    }
    /**
     * [addExampleTypeMsg 添加例句类型信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Examplesentencetype/addExampleTypeMsg
     */
    public function addExampleType(){
        $data = Request::instance()->post();
        $manageobj = new ExampletypeManage;
        $ExampleTypelist = $manageobj->addExampleType($data);
        $this->ajaxReturn($ExampleTypelist);
        return $ExampleTypelist;

    }
    /**
     * [updateExampleTypeMsg 修改例句类型信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Examplesentencetype/updateExampleTypeMsg
     */
    public function updateExampleType(){
        $data = Request::instance()->post();
        $manageobj = new ExampletypeManage;
        $ExampleTypelist = $manageobj->updateExampleType($data);
        $this->ajaxReturn($ExampleTypelist);
        return $ExampleTypelist ;

    }
    
    /**
     * [deleteExampleType 删除例句类型]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/Examplesentencetype/deleteExampleType
     */
    public function deleteExampleType(){
        $id = $this->request->param('id');
        $manageobj = new ExampletypeManage;
        $delflag = $manageobj->delExampleType($id);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
	
	/**
	 * 获取所有例句类型
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]  [description]
	 */
    public function getAllExampleTypeList()
    {
    	$manageobj = new ExampletypeManage;
    	$list = $manageobj->getAllExampleTypeList();
        $this->ajaxReturn($list);
        return $list ;
    }
}
