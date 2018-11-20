<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\RewardManage;
use think\Request;
use login\Authorize;
class Reward extends Authorize
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
	 * 获取奖品列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/Reward/getRewardList
	 */
    public function getRewardList()
    {	
        $name    = $this->request->param('name') ;
        $pagenum  = $this->request->param('pagenum') ;
		
    	$limit = config('param.pagesize')['admin_Rewardlist'];

    	$manageobj = new RewardManage;
    	//获取奖品列表信息,默认分页为5条
    	$Rewardlist = $manageobj->getRewardList($name,$pagenum,$limit);
    	
        $this->ajaxReturn($Rewardlist);
        return $Rewardlist ;
        // return json_encode($Rewardlist) ;

    }
    /**
     * 奖品详情
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Reward/RewardInfo
	 */
    public function getRewardinfo(){
    	$id = $this->request->param('id') ;
    	// $id = 1 ;

    	$manageobj = new RewardManage;
    	//获取奖品列表信息,默认分页为5条
    	$Rewardlist = $manageobj->getRewardInfo($id);

    	// var_dump($Rewardlist);
        $this->ajaxReturn($Rewardlist);
        return $Rewardlist ;
    }
   
    /**
     * 编辑奖品资料时获取奖品的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Reward/getRewardMsg
     */
    public function getRewardMsg(){
        $id = $this->request->param('id') ;
        // $id = 1 ;
        // $allaccountid = 1 ;

        $manageobj = new RewardManage;
        //获取奖品列表信息,默认分页为5条
        $Rewardlist = $manageobj->getRewardMsg($id);

        // var_dump($Rewardlist);
        $this->ajaxReturn($Rewardlist);
        return $Rewardlist ;
    }
    /**
     * [addRewardMsg 添加奖品信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Reward/addRewardMsg
     */
    public function addReward(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new RewardManage;
        //获取奖品列表信息,默认分页为5条
        $Rewardlist = $manageobj->addReward($data);
        $this->ajaxReturn($Rewardlist);
        return $Rewardlist;

    }
    /**
     * [updateRewardMsg 修改奖品信息]
     * @Author lc 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/Reward/updateRewardMsg
     */
    public function updateReward(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new RewardManage;
        //获取奖品列表信息,默认分页为5条
        $Rewardlist = $manageobj->updateReward($data);
        $this->ajaxReturn($Rewardlist);
        return $Rewardlist ;

    }
    
    /**
     * 切换奖品的启用状态标记
     * @Author lc
     * @param 使用organid 做查询
     * @return 
     * URL:/admin/Reward/switchRewardStatus
     */
    public function switchRewardStatus(){
        $id = $this->request->param('id');
        // $id =  5;
        $status  = $this->request->param('status');
        // $status =  5;

        $manageobj = new RewardManage;
        //获取奖品列表信息,默认分页为5条
        $lablelist = $manageobj->switchRewardStatus($id,$status);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     * [deleteReward 删除奖品]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/Reward/deleteReward
     */
    public function deleteReward(){
        $id = $this->request->param('id');
        // $id =  5;

        $manageobj = new RewardManage;
        //获取奖品列表信息,默认分页为5条
        $delflag = $manageobj->delReward($id);
        // var_dump($lablelist);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
}
