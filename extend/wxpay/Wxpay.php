<?php
/**
 * 
 */
namespace wxpay;
ini_set('date.timezone','Asia/Shanghai');
require_once "lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
class Wxpay{
   /* protected $APPID = 'wx3ecf6c6dc7eaaa17';
    protected $MCHID = '1509311051';
    protected $KEY = '8menke01234567899876543210ekenm8';
    protected $APPSECRET = 'a0734e68b21beaa6d254f64c76ad484c';*/
   //以下配置均为APP端的配置 微信扫码和公众号支付都走config.php
    protected $APPID = 'wxe84e35ae248bb82f';//
    protected $MCHID = '1284715401';
    protected $KEY = 'ningmengjiaoyukejigongsi12345678';
    protected $APPSECRET = 'a0734e68b21beaa6d254f64c76ad484c';
	/**
	 *	@param  $out_trade_no 自定义的订单号 和订单表id 对应
	 *	@param  $title      支付标题
	 *	@param  $total_amount   支付金额
	 *	@param  $profile         描述
	 *	@param  $returnrul      设置通知url
	 *
	 *
	 */
	public function createWxQrcode($url){
		require_once 'phpqrcode.php';
		\QRcode::png($url);

	}
	/**
	 *	@param  $out_trade_no 自定义的订单号 和订单表id 对应
	 *	@param  $title      支付标题
	 *	@param  $total_amount   支付金额
	 *	@param  $profile         描述
	 *	@param  $returnrul      设置通知url
	 *
	 *	@return $url string 支付使用的url
	 */
	public function createWxPayUrl($out_trade_no,$title,$total_amount,$profile,$returnrul,$expire=1740){
		
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($title);
		$input->SetAttach($profile);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_amount*100);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + $expire));
		$input->SetNotify_url($returnrul);
		$input->SetTrade_type("NATIVE");
		$input->SetGoods_tag("shoptag");
		$input->SetProduct_id("12345672342342");

		$notify = new \NativePay();
		$result = $notify->GetPayUrl($input);
		// return $url2;
		return $result;
	}
    /**
     * 微信公众号支付统一下单
     *	@param  $out_trade_no 自定义的订单号 和订单表id 对应
     *	@param  $title      支付标题
     *	@param  $total_amount   支付金额
     *	@param  $profile         描述
     *	@param  $returnrul      设置通知url
     *
     *	@return $url string 支付使用的url
     */
    public function jsapiWxpay($out_trade_no,$title,$total_amount,$profile,$returnrul,$openid,$expire=1740){

        $tools = new \JsApiPay();
        file_put_contents('wxgzh.txt',print_r('----获取到的code:'.$openid ,true),FILE_APPEND) ;

        $openId = $openid;

        file_put_contents('wxgzh.txt',print_r(date('Y-m-d H:i:s',time() ).'openid:'.$openId ,true),FILE_APPEND) ;
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($title);
        $input->SetAttach($profile);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_amount*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + $expire));
        $input->SetGoods_tag("shoptag");
        $input->SetNotify_url($returnrul);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        file_put_contents('wxgzh.txt',print_r($input ,true),FILE_APPEND) ;
        $wxpayobj = new \WxPayApi();
        $order = $wxpayobj::unifiedOrder($input);
        file_put_contents('wxgzh.txt',print_r($order ,true),FILE_APPEND) ;
        $jsApiParameters = $tools->GetJsApiParameters($order);
       return $jsApiParameters;
    }
	/**
	 *	根据订单号 查询微信对应的订单的 状态
	 *	@author wyx
	 *	@param  $out_trade_no 自定义的订单号 和订单表id 对应
	 *
	 */
	public function orderQuery($out_trade_no){
        $inputObj = new \WxPayOrderQuery();
        $inputObj->SetOut_trade_no($out_trade_no);
		return \WxPayApi::orderQuery($inputObj);
	}
	/**
	 *	关闭订单  
	 *	@author wyx
	 *	@param  $out_trade_no 自定义的订单号 和订单表id 对应  必填
	 *
	 */
	public function closeOrder($out_trade_no){
		$input = new \WxPayCloseOrder();
		$input->SetOut_trade_no($out_trade_no);
		return \WxPayApi::closeOrder($input);
	}
    //下单
    public function appWxpay($out_trade_no, $subject, $total_amount, $body, $callbackurl,$expire=1740)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $input = new \WxPayUnifiedOrder();
        $notify_url = $callbackurl;

        $onoce_str = $this->createNoncestr();

        $data["appid"] = $this->APPID;
        $data["body"] = $body;
        $data["mch_id"] = $this->MCHID;
        $data["nonce_str"] = $onoce_str;
        $data["notify_url"] = $notify_url;
        $data["out_trade_no"] = $out_trade_no;
        $data["spbill_create_ip"] = $this->get_client_ip();
        $data["total_fee"] = $total_amount * 100;
        $data["trade_type"] = "APP";
        $sign = $this->getSign($data);
        $data["sign"] = $sign;
        $xml = $this->arrayToXml($data);
        $response = $this->postXmlCurl($xml, $url);
        //将微信返回的结果xml转成数组
        $response = $this->xmlToArray($response);
        $result = $this->getSecondSign($response);
        //返回数据
        return $result;
    }
    /*生成签名*/
    public function getSign($Obj){
        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->KEY;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }


    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }


    //数组转xml
    public function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }


    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }


    /**
     *  作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml,$url,$second=30){
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果

        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;
        }
    }


    /*
    获取当前服务器的IP
    */
    public function get_client_ip(){
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
        return $cip;
    }


    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode){
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
    /**
     * 执行第二次签名，才能返回给客户端使用
     * @param int $prepayId:预支付交易会话标识
     * @return array
     */
    public function getSecondSign($result)
    {
        $data["appid"] = $result['appid'];
        $data["noncestr"] = $result['nonce_str'];
        $data["package"] = "Sign=WXPay";
        $data["partnerid"] = $result['mch_id'];
        $data["prepayid"] = $result['prepay_id'];
        $data["timestamp"] = time();
        $sign = $this->getSign($data);
        $data["sign"] = $sign;
        $data['result_code'] = $result['result_code'];
        return $data;
    }
    /**
     *	根据订单号 查询微信APP支付对应的订单的 状态
     *	@author wyx
     *	@param  $out_trade_no 自定义的订单号 和订单表id 对应
     *
     */
    public function appOrderQuery($out_trade_no){
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $onoce_str = $this->createNoncestr();
        $data["appid"] = $this->APPID;
        $data["mch_id"] = $this->MCHID;
        $data["nonce_str"] = $onoce_str;
        $data["out_trade_no"] = $out_trade_no;
        $sign = $this->getSign($data);
        $data["sign"] = $sign;
        $xml = $this->arrayToXml($data);
        $response = $this->postXmlCurl($xml, $url);
        //将微信返回的结果xml转成数组
        $response = $this->xmlToArray($response);
        //返回数据
        return $response;
    }
    public function getOpenid($code){
        $tools = new \JsApiPay();
        $openid = $tools->GetOpenid($code);
        return  $openid;
    }
}
