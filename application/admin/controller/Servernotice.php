<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\OrganManage;
use wxpay\AppNotify;
use wxpay\Wxpay;
use wxpay\WechatPayNotify;
use alipay\Alipaydeal;
use alibatchtrans\AlipayBatch;
class ServerNotice extends Controller
{	
	/**
	 * 微信购买课程 回调入口
	 * @Author wyx
	 * @DateTime 2018-06-11
	 * @return   [type]                   [description]
	 * URL:/admin/ServerNotice/wxCourseNotify
	 */
    public function wxCourseNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new WechatPayNotify;
        $obj->dealNotifyRun( $xml, $msg , 1);// 1 课程回调
    }
    /**
     * 微信购买套餐 回调入口
     * @Author wyx
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxMealNotify
     */
    public function wxMealNotify()
    {   
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;

        $msg = '' ;
        $obj = new WechatPayNotify;
        // $obj->NotifyProcess($data, $msg ,2);// 2 套餐回调
        $obj->dealNotifyRun($xml, $msg ,2);// 2 套餐回调
    }
    /**
     * 购买课程的支付宝 回调入口
     * @Author wyx
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/aliCourseNotify
     */
    public function aliCourseNotify()
    {   
        $obj = new Alipaydeal;
        $obj->notifyUrl($_POST,1);// 1 课程回调
    }
    /**
     * 支付宝购买套餐 回调入口
     * @Author wyx
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/aliMealNotify
     */
    public function aliMealNotify()
    {   
        $obj = new Alipaydeal;
        $obj->notifyUrl($_POST,2);// 2 套餐回调
    }
    /** 
     *  充值的回调  wx
     * @Author wyx
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxRechargeNotify
     */
    public function wxRechargeNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new WechatPayNotify;
        $obj->dealNotifyRun( $xml, $msg , 3);// 3课程回调
    }
    /**
     *  微信APP充值的回调
     * @Author yr
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxRechargeNotify
     */
    public function wxappRechargeNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new AppNotify();
        $obj->dealwithRecharge( $xml);
    }
    /**
     *  微信APP套餐支付的回调
     * @Author yr
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxappPackageNotify
     */
    public function wxappPackageNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new AppNotify();
        $obj->dealwithPackage( $xml);
    }
    /**
     *  微信APP课程支付的回调
     * @Author yr
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxappCourseNotify
     */
    public function wxappCourseNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new AppNotify();
        $obj->dealwithCourse( $xml);
    }
    /** 
     *  充值的回调  wx
     * @Author wyx
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/aliRechargeNotify
     */
    public function aliRechargeNotify()
    {   
        $obj = new Alipaydeal;
        $obj->notifyUrl($_POST,3);// 3 充值回调
    }
    /** 
     * 批量付款的回调
     * @Author wyx
     * @DateTime 2018-06-12
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/batchPayNotify
     */
    public function batchPayNotify()
    {   
        /*$obj = new \AliBatchTransNotify;
        $check = $obj->verifyNotify($_POST);// 3 充值回调
        //检验成功后 进行处理
        if($check){
            $orderobj = new OrganManage;
            // $dealflag = $orderobj->dealBatchPay($_POST);//支付宝批量付款回调
        }*/
        $obj = new AlipayBatch();
        $res = $obj->batchNotifyUrlPost($_POST);

    }
    /**
     * 微信购买套餐 回调入口
     * @Author yr
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/wxPackageNotify
     */
    public function wxPackageNotify()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('wxpay.txt',print_r($xml,true),FILE_APPEND) ;
        $msg = '' ;
        $obj = new WechatPayNotify;
        $obj->dealNotifyRun( $xml, $msg , 4);// 4 微信套餐购买回调
    }
    /**
     * 购买课程的支付宝 回调入口
     * @Author yr
     * @DateTime 2018-06-11
     * @return   [type]                   [description]
     * URL:/admin/ServerNotice/aliPackageNotify
     */
    public function aliPackageNotify()
    {
        $obj = new Alipaydeal;
        file_put_contents('packagenotify.txt',print_r($_POST,true),FILE_APPEND) ;
        $obj->notifyUrl($_POST,4);// 4 支付宝套餐购买回调
    }
}
