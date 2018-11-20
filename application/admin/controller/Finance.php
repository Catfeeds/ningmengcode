<?php
/**
 * @ 财务模块控制器 
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use login\Authorize;
use app\admin\business\AccountBill;


class Finance extends Authorize
{


	//自定义初始化
	protected function _initialize() {
        parent::_initialize();
		// $this->organid = 1;
		header('Access-Control-Allow-Origin: *');
	}


    /**
     * [getAccount 账单账户明细列表]、
     * @author [name] < JCR >
     * @param  [type] $[type]    [1已结算 0待结算]
     * @param  [type] $[pagenum] [分页第几页]
     * @return [type] [description]
     */
    public function getAccount(){
    	$data = Request::instance()->POST();
    	$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
    	$account = new AccountBill();
    	$dataReturn = $account->getAccount($data,20);
        $this->ajaxReturn($dataReturn);
    }

	/**
	 * [getAccountInfo 账单账户明细详情]、
	 * @author [name] < JCR >
	 * @param  [type] $[type]    [1已结算 0待结算]
	 * @param  [type] $[pagenum] [分页第几页]
	 * @return [type] [description]
	 */
	public function getAccountInfo(){
		$orderid = Request::instance()->POST('id');
		$account = new AccountBill();
		$dataReturn = $account->getAccountInfo($orderid);
		$this->ajaxReturn($dataReturn);
	}


    /**
     * [getAccountCount 账户明细统计]
     * @author [name] <JCR>
     * @param  [type] $[type]    [1已结算 0待结算]
     * @return [type] [description]
     */
    public function getAccountCount(){
    	$data = Request::instance()->POST();
    	$data['type'] = isset($data['type'])?$data['type']:0;
    	$account = new AccountBill();
    	$dataReturn = $account->getAccountCount($data);
        $this->ajaxReturn($dataReturn);
    }


	/**
	 * [reconciliation 对账中心列表]
	 * @author	[name] <JCR>
	 * @param 	[type] [$teachername]	[老师名称]
	 * @param 	[type] [$curriculumname][课程名称]
	 * @param 	[type] [$gradename]		[班级名称]
	 * @param 	[type] [$pagenum]		[分页页码]
	 */
	public function reconciliation(){
		$data = Request::instance()->POST(FALSE);
		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
		$account = new AccountBill();
		$dataReturn = $account->reconciliation($data,20);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * [teacherReconciliation 对账中心老师对账详情]
	 * @author	[name]	[JCR]
	 * @param 	[type]	[$id]	[班级ID]
	 */
	public function teacherReconciliation(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
		$account = new AccountBill();
		$dataReturn = $account->teacherReconciliation($data,20);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * [haveClassInfo 对账中心上课明细]
	 * @author	[name]	[JCR]
	 * @param	[type]	[$id]	[课时id]
	 */
	public function haveClassInfo(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
		$account = new AccountBill();
		$dataReturn = $account->haveClassInfo($data,20);
		$this->ajaxReturn($dataReturn);
	}




    /**
     * [getAccountCount 提现申请]\
     * @author [name]  <JCR>
     * @param  [type] $[type]        [1已结算 0待结算]
     * @param  [type] $[paytype]     [提现类型]
     * @param  [type] $[cashaccount] [提现账号]
     * @return [type] [description]
     */
    public function addWithdraw(){
    	$data = Request::instance()->POST();
    	$data['paytype'] = 3;
    	$account = new AccountBill();
    	$dataReturn = $account->addWithdraw($data);
        $this->ajaxReturn($dataReturn);
    }


    /**
     * [getWithdraw 提现明细列表]
     * @author [name]  <JCR>
     * @param  [type] $[paystatus] [ 0提现中 1提现成功 2提现失败 ]
     * @return [type] [description]
     */
   	public function getWithdraw(){
   		$data = Request::instance()->POST();
   		$data['paystatus'] = isset($data['paystatus'])?$data['paystatus']:0;
   		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
   		$account = new AccountBill();
    	$dataReturn = $account->getWithdraw($data,20);
        $this->ajaxReturn($dataReturn);
   	}


   	/**
   	 * [getTeacherSum 获取机构老师的对账统计]
     * @author [name] <JCR>
   	 * @param  [type] $[teachername] [老师名称]
   	 * @param  [type] $[pagenum]     [第几页]
   	 * @param  [type] $[intime]      [时间区间]
   	 * @return [type] [description]
   	 */
   	public function getTeacherSum(){
   		$data = Request::instance()->POST();
   		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
   		$account = new AccountBill();
    	$dataReturn = $account->getTeacherSum($data,20);
    	$this->ajaxReturn($dataReturn);

   	}


   	/**
   	 * [getTeacherPaylog 获取指定老师指定时间的盈利明细]
     * @author [name] <JCR>
   	 * @param  [type] $[teacherid]   [老师id]
   	 * @param  [type] $[pagenum]     [第几页]
   	 * @param  [type] $[intime]      [时间区间]
   	 * @return [type] [description]
   	 */
   	public function getTeacherPaylog(){
   		$data = Request::instance()->POST();
   		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
   		$account = new AccountBill();
    	$dataReturn = $account->getTeacherPaylog($data,20);
    	$this->ajaxReturn($dataReturn);
   	}





}
