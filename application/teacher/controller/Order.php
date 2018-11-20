<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use think\Session;
use app\appteacher\business\OrderDeal;
use login\Authorize;
class Order extends Authorize
//class Order extends Controller
{

    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        //$this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }

    /**
     * [getOrderList 显示该教师订单列表]
     * @Author wyx
     * @DateTime 2018-04-21T09:38:18+0800
     * @return   [array]        [description]
     * URL:/appteacher/Order/getOrderList
     */
    public function getOrderList()
    {
        $datestatus = $this->request->param('datestatus') ;//查询时间码
        //$orderstatus = $this->request->param('orderstatus') ;//订单状态
        $pagenum  = $this->request->param('pagenum') ;//分页页码
        $timeap = $this->request->param('timeap/a');//获取开始结束时间 /a强制转换成数组
        //机构 标识id
        $teacherid = $this->teacherid;
        $limit    = config('param.pagesize')['teacher_oderlist'];
        isset($datestatus)?$datestatus:1;
        $orderstatus = 20;//已支付
        empty($pagenum)?1:$pagenum;
        $orderobj = new OrderDeal;
        //获取教师列表信息,默认分页为20条
        $orderlist = $orderobj->getOrderList($orderstatus,$pagenum,$teacherid,$limit,$timeap);
        $this->ajaxReturn($orderlist);
    }
    /**
     * 订单详情
     * @Author wyx
     * @param 使用orderid 做查询
     * @return
     * URL:/appteacher/Order/orderInfo
     */
    public function orderInfo(){
        $orderid = $this->request->param('orderid') ;
        $orderobj  = new OrderDeal;
        //获取教师列表信息,默认分页为5条
        $teachlist = $orderobj->orderDetail($orderid);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
    }


}
