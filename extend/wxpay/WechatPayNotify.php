<?php
namespace wxpay;
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'log.php';
//初始化日志
use app\admin\model\Wxpaypushlog;
use app\admin\business\OrganManage;
$logHandler= new \CLogFileHandler("wx_".date('Y-m-d').'.log');
$log = \Log::Init($logHandler, 15);
class WechatPayNotify extends \WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new \WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = \WxPayApi::orderQuery($input);
		\Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	/**
	 *	重写回调处理函数
	 *	@param $data   回调数据
	 *	@param $msg    处理信息
	 *	@param $from   回调来源  1 课程购买 2 购买套餐
	 *
	 */
	public function NotifyProcess($data, &$msg)
	{
		\Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}else{
			return true;
			
		}
	}
	public function dealNotifyRun($xml, &$msg,$from){
		$data = $this->FromXml($xml);
		$flag = $this->Handle(false);
        file_put_contents('flag.txt',print_r($flag,true),FILE_APPEND) ;
		if($flag){
			//将数据插入 数据库
			$logtable = new Wxpaypushlog;
	    	$logtable->addAlipayPushLog($data);
	    	//处理订单
	    	switch($from){
	    		case 1:
			    	$orderobj = new \Order;
		    	    $dealflag = $orderobj->dealwithOrder($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);
	    		break;

	    		case 2:
		    	    $orderobj = new OrganManage;
		    	    $dealflag = $orderobj->deal_pay_notice($data,2);// 2 微信支付 3 支付宝

	    		break;

	    		case 3:
		    	    $orderobj = new \Order;
		    	    $dealflag = $orderobj->dealwithRecharge($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);// 2 微信支付 3 支付宝
                case 4:
                    $orderobj = new \Order;
                    $dealflag = $orderobj->dealwithPackage($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);
                    break;
	    		break;
	    	}
		}
	}
}

