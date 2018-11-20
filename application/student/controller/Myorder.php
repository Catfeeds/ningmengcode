<?php
/**
 * 机构学生我的订单列表 业务逻辑层
 *
 *
 */
namespace app\student\controller;
use think\Controller;
use app\student\business\MyOrderManage;
use app\student\business\WebMyOrderManage;
use login\Authorize;
use think\Session;
class Myorder extends Authorize{
    /*public function __construct(Request $request = null)
    {
        $this->checktokens(1);
    }*/
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin: *');
        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
    }
    /**
     * 学生统一下单
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param studentid int 学生用户id
     * @param schedulingid int 班级id
     * @param courseid int 课程id
     * @param  amount  float 实付金额
     * @param  originprice float  课程原价
     * @param  int  ordersource    下单渠道 1web 2app
     * @param  int  organid  机构id
     * @return   array();
     * URL:/student/Myorder/gotoOrder
     */
    public function gotoOrder()
    {
        $studentid = $this->userid;
        $schedulingid = $this->request->param('schedulingid');//班级Id
        $courseid = $this->request->param('courseid');//课程id
        $amount = $this->request->param('amount');//实际金额
        $ordersource = $this->request->param('ordersource');//下单渠道
        $originprice = $this->request->param('originprice');//原始金额
        $addressid = $this->request->param('addressid');//地址id
        $usestatus = $this->request->param('usestatus');//是否使用优惠券 未使用 1使用
        $type = $this->request->param('type');//优惠券类型
        $packageid = $this->request->param('packageid');//优惠券id
        $packagegiftid = $this->request->param('packagegiftid');//赠送优惠券id
        $packageuseid = $this->request->param('packageuseid');//赠送优惠券id
      /*  $studentid = 1;
        $schedulingid = 1;
        $amount = '50.00';
        $ordersource = 1;
        $originprice = 50.01;
        $courseid = 3;*/
        $orderobj = new MyOrderManage;
        $res = $orderobj->gotoOrder($studentid,$schedulingid,$amount,$ordersource,$originprice,$courseid,$addressid,$usestatus,$type,$packageid,$packagegiftid,$packageuseid);
        $this->ajaxReturn($res);
    }
    /**
     * 学生显示订单详情和账户余额信息
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     * URL:/student/Myorder/showOrderDetail
     */
    public function showOrderDetail()
    {
        $ordernum = $this->request->param('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->showOrderDetail($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生满足该课程的优惠券
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     * URL:/student/Myorder/getUserPackage
     */
    public function getUserPackage()
    {
        $curriculumid = $this->request->param('curriculumid');
        $amount = $this->request->param('amount');
        $studentid = $this->userid;
        $orderobj = new MyOrderManage;
        $res = $orderobj->getUserPackage($studentid,$curriculumid,$amount);
        $this->ajaxReturn($res);
    }
    /**
     * 查询订单状态
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @param type int 类型1查询课程2查询套餐
     * @return   array();
     * URL:/student/Myorder/queryOrderStatus
     */
    public function queryOrderStatus()
    {
        $ordernum = $this->request->param('ordernum');
        $type = $this->request->param('type');
        $type = isset($type)?$type:1;
        $orderobj = new MyOrderManage;
        $res = $orderobj->queryOrderStatus($ordernum,$type);
        $this->ajaxReturn($res);
    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 如: 1 或 1,2
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     * URL:/student/Myorder/gotoPay
     */
    public function gotoPay()
    {
        $studentid = $this->userid;
        $ordernum = $this->request->param('ordernum');
        $paytype = $this->request->param('paytype');
        $orderobj = new MyOrderManage;
      /*  $studentid = 1;
        $ordernum = '201805261741194972438610';
        $amount = '200.00';
        $usablemoney = '99998404.00';
        $paytype = '2';
        $coursename = 'php精讲';
        $classtype = '2';
        $gradename = '班级名称';*/
        $res = $orderobj->gotoPay($studentid,$ordernum,$paytype);
        $this->ajaxReturn($res);

    }
    /**
     * 查询用户订单信息
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param userid int 用户id
     * @param pagenum int 分页页数
     * @param limit int  每页条数
     * @return   array();
     * URL:/student/Myorder/getMyOrderList
     */
    public function getMyOrderList()
    {
        $userid = $this->userid;
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $orderobj = new MyOrderManage;
        $res = $orderobj->myOrderList($userid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 查询未支付订单信息
     * @Author yr
     * @DateTime 2018-04-24T15:11:19+0800
     * @param orderid int 订单Id
     * @param ordernum int 订单号
     * @param userid int 用户id 用户ID
     * @return   array();
     * URL:/student/Myorder/getUnpaidOrder
     */
    public function getUnpaidOrder(){
        $userid = $this->userid;
        $ordernum = $this->request->param('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->getUnpaidOrder($userid,$ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 订单取消接口
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Myorder/cancelOrder
     */
    public function cancelOrder(){
        $ordernum = $this->request->param('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->cancelOrder($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 微信订单状态查询
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Myorder/orderQuery
     */
    public function orderQuery()
    {
        $ordernum = $this->request->post('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->queryOrder($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 查询订单详情和支付状态
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Myorder/orderQuery
     */
    public function orderSuccess()
    {
        $ordernum = $this->request->post('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->orderSuccess($ordernum);
        $this->ajaxReturn($res);
    }
}
