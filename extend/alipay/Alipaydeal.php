<?php
/**
 * 
 */
namespace alipay;
use alipay\AlipayTradeService;
use alipay\AlipayTradePagePayContentBuilder;
use app\admin\model\Alipaypushlog;
use app\admin\business\OrganManage;
class Alipaydeal
{
	/**
	 *	创建订单请求
	 *	@param   $out_trade_no  订单号            商户订单号，商户网站订单系统中唯一订单号，必填
	 *	@param   $subject       订单标题          订单名称，必填
	 *	@param   $total_amount  订单金额          付款金额，必填
	 *	@param   $body          订单描述          商品描述，可空
	 *	@param   $returnurl     支付后的跳转页面
	 *
	 *
	 */
	public function createPayRequest($out_trade_no,$subject,$total_amount,$body,$returnurl,$callbackurl,$timeExpress = '29m'){
        include('config.php');
		//构造参数
		$payRequestBuilder = new AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($body);
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);
		$payRequestBuilder->setTimeExpress($timeExpress);// 设置订单 有效时间
		//支付成功后的 跳转地址页面
		$config['return_url'] = $returnurl ;
		$aop = new AlipayTradeService($config);
		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
	 	*/
		$response = $aop->pagePay($payRequestBuilder,$returnurl,$callbackurl);
		//输出表单,表单会自动
		return $response;
    }
    /**
	 *	关闭订单请求
	 *	@param   $out_trade_no  订单号            商户订单号，商户网站订单系统中唯一订单号，必填
	 *
	 */
	public function closeOrder($out_trade_no){
    	require_once 'config.php';
		//构造参数
		$payRequestBuilder = new AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setOutTradeNo($out_trade_no);
		$payRequestBuilder->cleanProduct_code();
		//支付成功后的 跳转地址页面
		$aop = new AlipayTradeService($config);
		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
	 	*/
		$response = $aop->Close($payRequestBuilder);
		//输出表单,表单会自动
		return $response;
    }
    /**
     *	通知回掉 接口处理
     *
     *	签名验证 及订单处理
     *	@param $data 支付宝推送过来的 数据
     *	
     *
     */
    public function notifyUrl($data,$from=1){
    	//引入支付宝配置
    	require_once 'config.php';
    	$aop = new AlipayTradeService($config);
		$bool = $aop->check($data);
		$aop->writeLog("1\r\n".print_r($data,true).'|||','notifyUrl.log');
		$aop->writeLog("签名是否成功\r\n".print_r($bool,true).'|||','notifyUrl.log');
		// $bool = true;
		if($bool){//签名成功验证
			$tableobj = new Alipaypushlog;
			//将支付宝的回掉插入数据库
            $aop->writeLog("入库前\r\n".print_r('---'.$from,true).'|||','notifyUrl.log');
    	    $flag = $tableobj->addAlipayPushLog($data);
            $aop->writeLog("入库后\r\n".print_r($flag.'---'.$from,true).'|||','notifyUrl.log');
    	    switch ($from) {
    	    	case 1:
		    	    $orderobj = new \Order;
		    	    $dealflag = $orderobj->dealwithOrder($data['out_trade_no'],$data['trade_no'],$data['buyer_id'],$data['total_amount'],3);
    	    		break;
    	    	
    	    	case 2:
    	    		$orderobj = new OrganManage;
		    	    $dealflag = $orderobj->deal_pay_notice($data,3);// 2 微信支付 3 支付宝
    	    		break;

    	    	case 3:
		    	    $orderobj = new \Order;
		    	    $dealflag = $orderobj->dealwithRecharge($data['out_trade_no'],$data['trade_no'],$data['buyer_id'],$data['total_amount'],3);
    	    		break;

                case 4:
                    //套餐购买回调
                    $orderobj = new \Order;
                    $aop->writeLog("是否进入套餐回调\r\n".print_r('进入套餐回调',true).'|||','notifyUrl.log');
                    $dealflag = $orderobj->dealwithPackage($data['out_trade_no'],$data['trade_no'],$data['buyer_id'],$data['total_amount'],3);
                    break;
    	    }
			
			$aop->writeLog("验证签名\r\n".print_r($data,true).'|||'.print_r($dealflag,true),'notifyUrl.log');
			echo 'success';
		}else{//验证失败
			$aop->writeLog("验证签名失败\r\n".print_r($data,true),'notifyUrl.log');
			echo 'failure';
		}
    }
    /*
     * app支付宝支付
     * $body            名称
     * $total_amount    价格
     * $product_code    订单号
     * $notify_url      异步回调地址
     */
    public function appAlipay($out_trade_no,$subject,$total_amount,$body,$returnurl,$callbackurl,$timeExpress = '29m')
    {
        include('config.php');
        /**
         * 调用支付宝接口。
         */
        $aop = new AopClient();
        $aop->gatewayUrl            = $config['gatewayUrl'];
        $aop->appId                 = $config['app_id'];
        $aop->rsaPrivateKey         = $config['merchant_private_key'];
        $aop->charset               = $config['charset'];
        $aop->signType              = $config['sign_type'];
        $aop->alipayrsaPublicKey    = $config['alipay_public_key'];

        $request = new AlipayTradeAppPayRequest();
        $arr['body']                = $subject;
        $arr['subject']             = $body;
        $arr['out_trade_no']        = $out_trade_no;
        $arr['timeout_express']     = $timeExpress;
        $arr['total_amount']        = floatval($total_amount);
        $arr['product_code']        = 'QUICK_MSECURITY_PAY';

        $json = json_encode($arr);
        $request->setNotifyUrl($callbackurl);
        $request->setBizContent($json);
        $response = $aop->sdkExecute($request);
        return $response;
    }
    /*
     * app支付宝支付统一收单线下交易查询
     * $out_trade_no     String length 64  订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
        trade_no,out_trade_no如果同时存在优先取trade_no
     * return code
     */
    public function alipayTradeQueryRequest($out_trade_no)
    {
        include('config.php');
        $aop = new AopClient ($config);
        $aop->gatewayUrl = $config['gatewayUrl'];
        $aop->appId = $config['app_id'];;
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->alipayrsaPublicKey=$config['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = $config['charset'];
        $aop->format='json';
        $request = new AlipayTradeQueryRequest();
        $request->setBizContent("{" .
            "\"out_trade_no\":\"$out_trade_no\"" .
            "}");
        $result = $aop->execute ($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode;
        return $resultCode;
    }

}








?>