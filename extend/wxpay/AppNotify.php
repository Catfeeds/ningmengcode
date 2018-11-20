<?php
/**
 *
 */
namespace wxpay;
//模式二
use app\admin\model\Wxpaypushlog;

/**
 * 流程：
 * app支付回调统一调用
 * 在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
class AppNotify
{
    protected $APPID = 'wxe84e35ae248bb82f';//
    protected $MCHID = '1284715401';
    protected $KEY = 'ningmengjiaoyukejigongsi12345678';
    protected $APPSECRET = 'a0734e68b21beaa6d254f64c76ad484c';
    /**
     *  买课回调
     *
     *
     */
    public function dealwithCourse($xml)
    {
        //将服务器返回的XML数据转化为数组
        $data = $this->xmltoarray1($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        $sign = $this->MakeSign($data);
        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') ) {
            $logtable = new Wxpaypushlog();
            $logtable->addAlipayPushLog($data);
            $orderobj = new \Order();
            $result = $orderobj->dealwithOrder($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);
        }else{
            $result = false;
        }
// 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;


    }
    /**
     *  套餐回调
     *
     *
     */
    public function dealwithPackage($xml)
    {
        //将服务器返回的XML数据转化为数组
        $data = $this->xmltoarray1($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        $sign = $this->MakeSign($data);
        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') ) {
            $logtable = new Wxpaypushlog();
            $logtable->addAlipayPushLog($data);
            $orderobj = new \Order();
            $result = $orderobj->dealwithPackage($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);

        }else{
            $result = false;
        }
// 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;


    }
    /**
     *  充值回调
     *
     *
     */
    public function dealwithRecharge($xml)
    {
        //将服务器返回的XML数据转化为数组
        $data = $this->xmltoarray1($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        $sign = $this->MakeSign($data);
        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') ) {
            $logtable = new Wxpaypushlog();
            $logtable->addAlipayPushLog($data);
            $orderobj = new \Order();
            $result = $orderobj->dealwithRecharge($data['out_trade_no'],$data['transaction_id'],$data['openid'],$data['total_fee']/100,2);

        }else{
            $result = false;
        }
// 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;


    }
    /**
     * xml转化数组
     * @return
     */
    function xmltoarray1($xml)
    {
        libxml_disable_entity_loader(true);
        $res = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $res;
    }

    function MakeSign($data)
    {
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->ToUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    function ToUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;

    }
}
