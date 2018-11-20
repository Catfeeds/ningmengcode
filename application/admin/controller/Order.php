<?php
namespace app\admin\controller;
use app\admin\business\OrderDealwith;
use think\Session;
use login\Authorize;
use think\Controller;
class Order extends Authorize
{
    /**
     *  
     *
     */
    public function __construct(){
        parent::__construct();
    }
	/**
	 * [getOrderList 获取订单列表]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:38:18+0800
	 * @return   [array]        [description]
	 * URL:/admin/order/getOrderList
	 */
    public function getOrderList()
    {	
        $ordernum  = $this->request->param('ordernum') ;//订单号
        $ordertype = $this->request->param('ordertype') ;//订单类型
        $pagenum  = $this->request->param('pagenum') ;//分页页码
    	//机构 标识id
    	$limit    = config('param.pagesize')['adminorder_orderlist'] ;

    	$orderobj = new OrderDealwith;
    	//获取教师列表信息,默认分页为5条
    	$orderlist = $orderobj->getOrderList($ordertype,$ordernum,$pagenum,$limit);
    	
    	// var_dump($orderlist);
        $this->ajaxReturn($orderlist);
        // return $orderlist;
    }
    /**
     * 订单详情
     * @Author wyx
     * @param 使用orderid 做查询
     * @return
     * URL:/admin/order/orderInfo
	 */
    public function orderInfo(){
        $orderid = $this->request->param('orderid') ;
        // $orderid = 1 ;

    	$orderobj  = new OrderDealwith;
    	//获取教师列表信息,默认分页为5条
    	$teachlist = $orderobj->orderDetail($orderid);

    	// var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
    }
    
}
