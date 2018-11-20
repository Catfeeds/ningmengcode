<?php
/**
**官方后台财务管理控制器
**/
namespace app\official\controller;
use app\official\controller\Base;
use app\official\business\FinanceManage;
use think\Session;
use think\Request;
use alibatchtrans\AlipayBatch;
use think\Log;

class Finance extends Base{

	
    /**
     * [getTradeTotalSum //获取累计交易金额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param 无参数         []   ]   
     * @return   [array]                   [description]
     */
	public function getTradeTotalSum(){
		$financeManage = new FinanceManage();
		$res = $financeManage->getTradeTotalSum();
		$this->ajaxReturn($res);
        return $res; 
	}

	
    /**
     * [getOrderList //订单列表]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param fromdate string           [开始日期]     
     * @param enddate string           [截止日期]     
     * @param domain string               [机构与名]     
     * @param orderbys string           [排序方式]        
     * @param pagenum int           [每页数目]     
     * @param pernum int           [页码数]   ]   
     * @return   [array]                   [description]
     */
	public function getOrderList(){
		//获取订单余额
        $data = [];
        $fromdate = Request::instance()->post('fromdate');
        $enddate = Request::instance()->post('enddate');
        $domain = Request::instance()->post('domain');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'fromdate'=>$fromdate ? $fromdate : '',  
        	'enddate'=>$enddate ? $enddate : '',
            'domain'=>$domain ? $domain : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_order_list'],
        ];
		$financeManage = new FinanceManage();
		$res = $financeManage->getOrderList($data);
		$this->ajaxReturn($res);
        return $res;  
	}

	
    /**
     * [getOrderDetail //订单详情]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param id int           [订单id]   ]   
     * @return   [array]                   [description]
     */
	public function getOrderDetail(){
		$id = Request::instance()->post('id');
		$id = $id ? $id : '';
		$financeManage = new FinanceManage();
		$res = $financeManage->getOrderDetail($id);
		$this->ajaxReturn($res);
        return $res;  
	}

	
    /**
     * [getRemainingSum //账目明细之获取账户余额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @return   [array]                   [description]
     */
	public function getRemainingSum(){
		//当前机构的充值收入减去机构的提现支出
		$financeManage = new FinanceManage();
		$res = $financeManage->getRemainingSum();
		$this->ajaxReturn($res);
        return $res;  
	}
 
	//账目明细收入列表  
	//现阶段 只包括1.学生第三方充值 2.学生的第三方下单
    /**
     * [getAccountDetailInList]
     * @Author zzq
     * @DateTime 2018-05-15     
     * @param orderbys string           [排序方式]        
     * @param pagenum int           [每页数目]     
     * @param pernum int           [页码数]   ]   
     * @return   [array]                   [description]
     */
	public function getAccountDetailInList(){
        $data = [];
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_account_detail_in'],
        ];
		$financeManage = new FinanceManage();
		$res = $financeManage->getAccountDetailInList($data);
		$this->ajaxReturn($res);
        return $res;  		
	}


	//根据paystatus（买课|充值）out_trade_no 订单号 查看详情
	// 1表示下单  2表示充值
    /**
     * [getAccountInDetail]
     * @Author zzq
     * @DateTime 2018-05-15     
     * @param out_trade_no string           [订单号|充值号]        
     * @param paystatus int           [流水类型]     
     * @return   [array]                   [description]
     */
	public function getAccountInDetail(){
		$data = [];
        $paystatus = Request::instance()->post('paystatus');
        $out_trade_no = Request::instance()->post('out_trade_no');
        $data = [
            'paystatus'=>$paystatus ? $paystatus : '',
            'out_trade_no'=>$out_trade_no ? $out_trade_no : '',
        ];
		$financeManage = new FinanceManage();
		$res = $financeManage->getAccountInDetail($data);
		$this->ajaxReturn($res);
        return $res;  		
	}


	// 机构提现申请列表(机构)
	#   提现成功的支出  $domain为空 paystatus为1
	#   所有的提现类表  $domain可有可无 paystatus为1,2,3,4
    /**
     * [getWithDrawByOrganList]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param paystatus int           [检索日期方式]     
     * @param domain string               [机构与名]     
     * @param orderbys string           [排序方式]        
     * @param pagenum int           [每页数目]     
     * @param pernum int           [页码数]   ]   
     * @return   [array]                   [description]
     */
	public function getWithDrawByOrganList(){
        $data = [];
        $domain = Request::instance()->post('domain');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $paystatus = Request::instance()->post('paystatus');
        $data = [
            'paystatus'=>$paystatus ? $paystatus : 0,//默认待处理
        	'domain'=>$domain ? $domain : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_withdraw_by_organ_list'],
        ];
		$financeManage = new FinanceManage();
		$res = $financeManage->getWithDrawByOrganList($data);
		$this->ajaxReturn($res);
        return $res;  	        
	}

	//获取机构提现详情
    /**
     * [getSumOutDetailByOrgan]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param id int           [提现表中的id]   
     * @return   [array]                   [description]
     */
	public function getSumOutDetailByOrgan(){
		$id = Request::instance()->post('id');
		$id = $id ? $id : '';
		$financeManage = new FinanceManage();
		$res = $financeManage->getSumOutDetailByOrgan($id);
		$this->ajaxReturn($res);
        return $res;  	
	}


	//处理提现申请（机构）第一步，将状态改为3    
	//第一个参数是提现申请的id的集合字符串1 或者 1,2,3
    /**
     * [manageWithDraw]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param str int           [提现表中的id的集合]   
     * @return   [array]                   [description]
     */
	public function manageWithDraw(){
		$ids = Request::instance()->post('ids');
        // var_dump($ids);
        // die;
        $ids = trim($ids);
		$ids = $ids ? $ids : '';
        //$ids = '15,16';
        // var_dump($ids);
        // die;
		$financeManage = new FinanceManage();
		$res = $financeManage->manageWithDraw($ids);
        // var_dump($res);
        // die;
        //echo $res;
        $this->ajaxReturn($res);
        return $res;    
	}

     
    //循环redis队列执行支付宝批量转款,定时任务
    /**
     * [manageRedisListAliQueue]
     * @Author zzq
     * @DateTime 2018-05-17
     * @param            []   
     * @return   [array]                   [description]
     */
    public function manageRedisListAliQueue(){
        
        $financeManage = new FinanceManage();
        $res = $financeManage->manageRedisListAliQueue();
    }    

	//提现成功或者失败的异步回调接口
	//返回的结果是成功或者失败 paystatus 1 或者 2
    /**
     * [manageWithDrawResAsync]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param str int           [提现表中的id的集合]   
     * @param paystatus int           [提现成功值为1 失败否则为2]   
     * @return   [array]                   [description]
     */
	public function manageWithDrawResAsync(){
		
        // var_dump($_POST);
        // die;

        //测试
        /*$_POST = [
            'success_details'=>'Zfp2_4809584144816567^18739798667^赵志强^0.01^S^null^201805048427067^20180504070809|Zfp2_4709584144816833^18235102743^余瑞^0.01^S^null^201805058427067^20180505070809|',
            'fail_details'=>'Zfp2_4688584144816833^18235102743^余瑞^0.50^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180501248427065^20180501143651|Zfp2_5709584141216833^18739798667^赵志强^0.20^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180502248427065^20180502143651|'

        ];*/
        //打印$_POST的信息
        Log::write($_POST);

        $obj = new AlipayBatch();
        $res = $obj->batchNotifyUrlPost($_POST);

	}

    //手动修改提现状态
    public function manualChangeWithDrawPayStatus(){
        $data = [];
        $ret = [];
        $id = Request::instance()->post('id');
        $paystatus = Request::instance()->post('paystatus');
        $reasons = Request::instance()->post('reasons');
        $price = Request::instance()->post('price');
        $type = 2;
        $endtime = time();
        $ret['id'] = $id;
        $ret['paystatus'] = $paystatus;
        $ret['reasons'] = $reasons;
        $ret['price'] = $price;
        $ret['type'] = $type;
        $ret['endtime'] = $endtime;
        $data[] = $ret;
        // var_dump($data);
        // die;
        $financeManage = new FinanceManage();
        $res = $financeManage->manualChangeWithDrawPayStatus($data);
        $this->ajaxReturn($res);
        return $res;
    }

}