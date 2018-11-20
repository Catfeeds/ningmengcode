<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\StudentTagManage;
use think\Request;
use login\Authorize;
class Studenttag extends Authorize
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
	 * URL:/admin/StudentTag/getTagList
	 */
    public function getTagList()
    {	
        $name    = $this->request->param('name') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	$limit = config('param.pagesize')['adminStudent_Taglist'];

    	$manageobj = new StudentTagManage;
    	//获取学生分类列表信息,默认分页为5条
    	$Taglist = $manageobj->getTagList($name,$pagenum,$limit);
        $this->ajaxReturn($Taglist);
        return $Taglist ;
        // return json_encode($Taglist) ;

    }
    /**
     * 学生分类详情
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentTag/TagInfo
	 */
    public function getTaginfo(){
    	$id = $this->request->param('id') ;
    	// $id = 1 ;

    	$manageobj = new StudentTagManage;
    	//获取学生分类列表信息,默认分页为5条
    	$Taglist = $manageobj->getTagInfo($id);
        $this->ajaxReturn($Taglist);
        return $Taglist ;
    }
   
    /**
     * 编辑学生分类资料时获取学生分类的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/StudentTag/getTagMsg
     */
    public function getTagMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new StudentTagManage;
        //获取学生分类列表信息,默认分页为5条
        $Taglist = $manageobj->getTagMsg($id);

        // var_dump($Taglist);
        $this->ajaxReturn($Taglist);
        return $Taglist ;
    }
    /**
     * [addTagMsg 添加学生分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentTag/addTagMsg
     */
    public function addTag(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new StudentTagManage;
        //获取学生分类列表信息,默认分页为5条
        $Taglist = $manageobj->addTag($data);
        $this->ajaxReturn($Taglist);
        return $Taglist;

    }
    /**
     * [updateTagMsg 修改学生分类信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/StudentTag/updateTagMsg
     */
    public function updateTag(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new StudentTagManage;
        //获取学生分类列表信息,默认分页为5条
        $Taglist = $manageobj->updateTag($data);
        $this->ajaxReturn($Taglist);
        return $Taglist ;

    }
    
    /**
     * 切换学生分类的启用状态标记
     * @Author lc
     * @param 使用organid 做查询
     * @return 
     * URL:/admin/StudentTag/switchTagStatus
     */
    public function switchTagStatus(){
        $id = $this->request->param('id');
        $status  = $this->request->param('status');

        $manageobj = new StudentTagManage;
        $lablelist = $manageobj->switchTagStatus($id,$status);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
	
    /**
     * [deleteTag 删除学生分类]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentTag/deleteTag
     */
    public function deleteTag(){
        $id = $this->request->param('id');
        $manageobj = new StudentTagManage;
        $delflag = $manageobj->delTag($id);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
}
