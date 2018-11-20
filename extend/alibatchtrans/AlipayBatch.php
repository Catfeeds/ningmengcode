<?php
/**
 *	支付宝批量转账处理
 *
 */
namespace alibatchtrans;
use app\official\business\FinanceManage;
use think\Log;
class AlipayBatch{

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
	public function createBatchTransRequest($parameter,$method="get", $button="确认"){
		require_once("alipay.config.php");
		require_once("lib/alipay_submit.class.php");
		$parameter["notify_url"] = config('param.server_url')."/admin/ServerNotice/batchPayNotify";
		$parameter["service"] = "batch_trans_notify" ;
		$parameter["partner"] = trim($alipay_config['partner']);
		$parameter["email"]   = trim($alipay_config['email']);
		$parameter["account_name"] = trim($alipay_config['account_name']);
		$parameter["_input_charset"] = trim(strtolower($alipay_config['input_charset']));
		// var_dump($parameter);exit();
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,$method, $button);
		// file_put_contents('logbatch.txt', $html_text);exit();
		return $html_text;
	}
	/**
	 *	批量付款状态异步通知
	 *
	 *
	 *
	 */
	public function batchNotifyUrl(){
		require_once("alipay.config.php");
		require_once("lib/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//批量付款数据中转账成功的详细信息

			$success_details = $_POST['success_details'];

			//批量付款数据中转账失败的详细信息
			$fail_details = $_POST['fail_details'];

			//判断是否在商户网站中已经做过了这次通知返回的处理
				//如果没有做过处理，那么执行商户的业务程序
				//如果有做过处理，那么不执行商户的业务程序
		        
			echo "success";		//请不要修改或删除

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}


	/**
	 *	异步操作方法，修改转账状态
	 *  $post为支付宝服务器返回的异步的post请求信息
	 *
	 *
	 */
	public function batchNotifyUrlPost($post){
		require_once("alipay.config.php");
		require_once("lib/alipay_notify.class.php");

        //打印$_POST的信息
        Log::write('------支付宝批量转账回调POST信息开始------');
        Log::write($post);
        Log::write('------支付宝批量转账回调POST信息结束------');

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotifyPost($post);
		//记录校验结果

		//$verify_result = true;
		if($verify_result){
			$logstr = "签名检验正确";
		}else{
			$logstr = "签名检验错误";
		}
		Log::write('------支付宝批量转账回调签名校验开始------');
		Log::write('支付宝批量转账回调签名校验结果:'.$logstr);
		Log::write('------支付宝批量转账回调签名校验结束------');
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			echo "success";
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//批量付款数据中转账成功的详细信息

			//$success_details = $_POST['success_details'];

			//批量付款数据中转账失败的详细信息
			//$fail_details = $_POST['fail_details'];

			//执行异步回调修改转账状态的方法
			$obj = new FinanceManage();
			//模拟测试支付宝的返回的数据
            //"Zfp2_4688584144816833^18235102743^余瑞^0.50^支付宝转账|Zfp2_5709584141216833^18739798667^赵志强^0.20^支付宝转账|Zfp2_4809584144816567^18739798667^赵志强^0.01^支付宝转账|Zfp2_4709584144816833^18235102743^余瑞^0.01^支付宝转账"

	        /*$_POST = [
	            'success_details'=>'Zfp2_4809584144816567^18739798667^赵志强^0.01^S^null^201805048427067^20180504070809|Zfp2_4709584144816833^18235102743^余瑞^0.01^S^null^201805058427067^20180505070809|',
	            'fail_details'=>'Zfp2_4688584144816833^18235102743^余瑞^0.50^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180501248427065^20180501143651|Zfp2_5709584141216833^18739798667^赵志强^0.20^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180502248427065^20180502143651|'

	        ];*/
			// var_dump($_POST);
			// die;			
			$obj->manageWithDrawResAsync($post);



			//判断是否在商户网站中已经做过了这次通知返回的处理
				//如果没有做过处理，那么执行商户的业务程序
				//如果有做过处理，那么不执行商户的业务程序
		        
			echo "success";		//请不要修改或删除

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
}