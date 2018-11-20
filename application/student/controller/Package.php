<?php
namespace app\student\controller;
use app\student\business\PackageManage;
use app\student\model\Coursepackageorder;
use app\student\model\Toteachtime;
use login\Authorize;
use app\index\controller\Index;
use Order;
use think\Config;
use think\Controller;
use think\Loader;
use CURLFile;
use app\student\model\City;
use Messages;
use think\Request;
use Base;
use think\Db;
use think\Lang;
use app\student\model\Lessons;
use app\index\business\UserLogin;
Loader::import('alipay.config');
Loader::import('alipay.pagepay.service.AlipayTradeService');
Loader::import('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
use Redis;
use app\student\model\Playback;
use app\student\model\Teacherinfo;
use wxpay\Wxpay;
use phpqrcode\qrcode;
use alipay\Alipaydeal;
use app\student\business\MyOrderManage;
use wxpay\WechatPayNotify;
use app\admin\model\Organ;

class Package extends Authorize
{
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin: *');
        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
    }
    /**
     * 查询我的套餐列表
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    pagenum int   分页页数
     * @return   array();
     * URL:/student/Package/getPackageList
     */
    public function getPackageList()
    {
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_packagelist'];
        $packageobj = new PackageManage;
        $res =  $packageobj->getPackageList($pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     * URL:/student/Package/getPackageDetail
     */
    public function getPackageDetail()
    {
        $packageid = $this->request->param('packageid');
        $packageobj = new PackageManage;
        $res =  $packageobj->getPackageDetail($packageid);
        $this->ajaxReturn($res);

    }
    /**
     * 套餐下单
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param studentid int 学生用户id
     * @param  amount  float 套餐会被
     * @param  int  ordersource    下单渠道 1web 2app
     * @return   array();
     * URL:/student/Package/gotoOrder
     */
    public function gotoOrder()
    {
        //$studentid = $this->userid;
        $studentid = $this->userid;
        $packageid = $this->request->param('packageid');
        $ordersource = $this->request->param('ordersource');
        $orderobj = new PackageManage();
        $res = $orderobj->gotoOrder($studentid,$packageid,$ordersource);
        $this->ajaxReturn($res);
    }
    /**
     * 学生显示套餐订单详情
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     * URL:/student/Package/showOrderDetail
     */
    public function showOrderDetail()
    {
        $ordernum = $this->request->param('ordernum');
        $orderobj = new PackageManage();
        $res = $orderobj->showOrderDetail($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  paytype支付方式 如: 1 或 1,2
     * @return   array();
     * URL:/student/Package/gotoPay
     */
    public function gotoPay()
    {
        //$studentid = $this->userid;
        $studentid = $this->userid;
        $ordernum = $this->request->param('ordernum');
        $paytype = $this->request->param('paytype');
        $orderobj = new PackageManage();
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
     * 查询套餐订单列表
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param userid int 用户id
     * @param pagenum int 分页页数
     * @param limit int  每页条数
     * @return   array();
     * URL:/student/Package/getPackageOrderList
     */
    public function getPackageOrderList()
    {
        //$userid = $this->userid;
        $studentid = $this->userid;
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $orderobj = new PackageManage();
        $res = $orderobj->getPackageOrderList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 订单取消接口
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Package/cancelOrder
     */
    public function cancelOrder(){
        $ordernum = $this->request->param('ordernum');
        $orderobj = new PackageManage();
        $res = $orderobj->cancelOrder($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 查询订单详情和支付状态
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Package/orderSuccess
     */
    public function orderSuccess()
    {
        $ordernum = $this->request->post('ordernum');
        $orderobj = new PackageManage;
        $res = $orderobj->orderSuccess($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的套餐使用列表
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param status int 0待使用 1已使用 2已过期
     * @param pagenum int 分页页数
     * @return   array();
     * URL:/student/Package/packageUseList
     */
    public function packageUseList()
    {
        $status = $this->request->post('status');
        $pagenum = $this->request->post('pagenum');
        //$studentid = $this->request->post('studentid');
        $studentid = $this->userid;
        $limit = config('param.pagesize')['student_packageuselist'];
        $orderobj = new PackageManage;
        $res = $orderobj->packageUseList($studentid,$pagenum,$status,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 删除优惠券
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param packageuseid int 优惠券使用的id
     * @return   array();
     * URL:/student/Package/deletePackageUse
     */
    public function deletePackageUse()
    {
        $packageuseid = $this->request->post('packageuseid');
        $orderobj = new PackageManage;
        $res = $orderobj->deletePackageUse($packageuseid);
        $this->ajaxReturn($res);
    }
}
