<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\admin\business\CompositionManage;
use login\Authorize;
class Composition extends Authorize
{

    public function __construct(){
        parent::__construct();
    }
	
	/**
     * [getCompositionStatistics 获取统计信息]
     * @Author lc
     * @DateTime 2018-04-20T11:01:54+0800
     * @return   [type]        [description]
     * URL:/admin/composition/getCompositionStatistics
     */
    public function getCompositionStatistics()
    {
    	$manageobj = new CompositionManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getCompositionStatistics();
        $this->ajaxReturn($userlist);
        return $userlist;
    }
	
	/**
     * [getCompositionList 获取作文列表]
     * @Author lc
     * @DateTime 2018-04-20T11:01:54+0800
     * @return   [type]        [description]
     * URL:/admin/composition/getCompositionList
     */
    public function getCompositionList()
    {	
        $status    = $this->request->param('status');
        $nickname = $this->request->param('nickname');
        $studentreviewscore = $this->request->param('studentreviewscore');
        $pagenum  = $this->request->param('pagenum');
    	$limit   = config('param.pagesize')['adminstu_userlist'] ;
		
    	$manageobj = new CompositionManage;
    	//获取教师列表信息,默认分页为5条
    	$userlist = $manageobj->getCompositionList($status,$nickname,$studentreviewscore,$pagenum,$limit);
        $this->ajaxReturn($userlist);
        return $userlist;
    }
    /**
     * [getCompositioninfo 获取作文详细信息]
     * @Author  lc
     * @DateTime 2018-04-20T13:37:15+0800
     * 
     * @return   [type]       [description]
     * URL:/admin/composition/getCompositioninfo
     */
    public function getCompositioninfo(){
        $compositionid  = $this->request->param('compositionid') ;
        $manageobj = new CompositionManage;
        //获取学生信息
        $compositioninfo  = $manageobj->getCompositioninfo($compositionid) ;
        
        $this->ajaxReturn($compositioninfo);
        return $compositioninfo;
        
    }
    
	/**
     * [deleteComposition 删除作文]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/Commentlabel/deleteComposition
     */
    public function deleteComposition(){
        $id = $this->request->param('id');
        $manageobj = new CompositionManage;
        $delflag = $manageobj->delComposition($id);
        $this->ajaxReturn($delflag);
        return $delflag;
    }
}
