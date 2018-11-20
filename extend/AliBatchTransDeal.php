<?php
/**
*支付宝批量转账处理
**/
class AliBatchTransDeal{

	/**
	 *	支付宝创建批量转账
	 *	@param   $notify_url    服务器异步通知页面路径，必填
	 *	@param   $email         付款账号，必填
	 *	@param   $account_name  付款账户名，必填
	 *	@param   $data          请求数组参数     必填
	 *	@return   
	 *
	 *
	 */
	public function createBatchTransRequest($data,$notify_url="http://www.demo2.com/official/finance/manageWithDrawResAsync",$email="888666@qq.com",$account_name="张三"){
		require_once("alibatchtrans/alipay.config.php");
		//var_dump($alipay_config);
		require_once("alibatchtrans/lib/alipay_submit.class.php");
		//die;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "batch_trans_notify",
				"partner" => trim($alipay_config['partner']),
				"notify_url"	=> $notify_url,
				"email"	=> $email,
				"account_name"	=> $account_name,
				"pay_date"	=> $data['pay_date'],
				"batch_no"	=> $data['batch_no'],
				"batch_fee"	=> $data['batch_fee'],
				"batch_num"	=> $data['batch_num'],
				"detail_data"	=> $data['detail_data'],
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		// var_dump($parameter);
		// die;
		//建立请求对象
		$alipaySubmit = new AlipaySubmit($alipay_config);
		//构造请求的数组参数
		$request_arr = $alipaySubmit->buildRequestPara($parameter);
		//var_dump($request_arr);
		//die;
		$url = $alipaySubmit->alipay_gateway_new."_input_charset=".trim(strtolower($alipaySubmit->alipay_config['input_charset']));
	    //var_dump($url);
		//远程请求支付宝接口
		$res = curl_postAli($url, $request_arr, $options = array());
		//$res = getHttpResponsePOST($url, $cacert_url, $para, $input_charset = ''); 
		//var_dump($res);
		// die;
	}
}