<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\SigninManage;
use think\Request;
use login\Authorize;
class Signin extends Authorize
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
	 * 获取签到列表
	 * @Author lc
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/signin/getSigninList
	 */
    public function getSigninList()
    {	
        $nickname  = $this->request->param('nickname');
        $pagenum  = $this->request->param('pagenum');
    	$limit = config('param.pagesize')['admin_Rewardlist'];

    	$manageobj = new SigninManage;
    	$Signinlist = $manageobj->getSigninList($nickname,$pagenum,$limit);
    	
        $this->ajaxReturn($Signinlist);
        return $Signinlist ;
        // return json_encode($Signinlist) ;

    }
}
