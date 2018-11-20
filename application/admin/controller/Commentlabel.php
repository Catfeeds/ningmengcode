<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\CommentlabelManage;
use think\Request;
use login\Authorize;
class Commentlabel extends Authorize
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
	 * 获取标签列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/Commentlabel/getCommentlabelList
	 */
    public function getCommentlabelList()
    {	
        $star    = $this->request->param('star') ;
        $content    = $this->request->param('content') ;
        $pagenum  = $this->request->param('pagenum') ;

    	$manageobj = new CommentlabelManage;
    	$Commentlabellist = $manageobj->getCommentlabelList($star,$content,$pagenum,20);
        $this->ajaxReturn($Commentlabellist);
        return $Commentlabellist ;
    }
   
    /**
     * 编辑标签资料时获取标签的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Commentlabel/getCommentlabelMsg
     */
    public function getCommentlabelMsg(){
        $id = $this->request->param('id') ;
        $manageobj = new CommentlabelManage;
        $Commentlabellist = $manageobj->getCommentlabelMsg($id);
        $this->ajaxReturn($Commentlabellist);
        return $Commentlabellist ;
    }
	
    /**
     * [addCommentlabelMsg 添加标签信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Commentlabel/addCommentlabelMsg
     */
    public function addCommentlabel(){
        $data = Request::instance()->post();
        $manageobj = new CommentlabelManage;
        $Commentlabellist = $manageobj->addCommentlabel($data);
        $this->ajaxReturn($Commentlabellist);
        return $Commentlabellist;

    }
	
    /**
     * [updateCommentlabelMsg 修改标签信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Commentlabel/updateCommentlabelMsg
     */
    public function updateCommentlabel(){
        $data = Request::instance()->post();
        $manageobj = new CommentlabelManage;
        $Commentlabellist = $manageobj->updateCommentlabel($data);
        $this->ajaxReturn($Commentlabellist);
        return $Commentlabellist ;

    }
    
    /**
     * [deleteCommentlabel 删除标签]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/Commentlabel/deleteCommentlabel
     */
    public function deleteCommentlabel(){
        $id = $this->request->param('id');
        $manageobj = new CommentlabelManage;
        $delflag = $manageobj->delCommentlabel($id);
        $this->ajaxReturn($delflag);
        return $delflag;
    }
}
