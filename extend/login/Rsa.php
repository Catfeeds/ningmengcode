<?php
/**
 * Created by PhpStorm.
 * User: koastal
 * Date: 2016/6/2
 * Time: 22:54
 */
namespace login;
/**
 *  实现使用 本地私钥解密
 *  使用用户公钥加密
 *
 */
class Rsa
{   
    private $private_key_path ;
    private $public_key_path = './cacert/public.pem' ;
    /**
     *  初始化
     *
     *
     */
    public function __construct($public=''){
        $this->private_key_path = dirname(__FILE__).'/cacert/private.pem' ;
        $this->public_key_path = dirname(__FILE__).'/cacert/public.pem' ;
    }

    public function getPublicKey(){
        return file_get_contents($this->private_key_path);
    }

    /**
     * RSA解密
     * @param $content 需要解密的内容，密文
     * @param $private_key_path 商户私钥文件路径
     * return 解密后内容，明文
     */
    function rsaDecryptorign($content) {
        $priKey = file_get_contents($this->private_key_path);
        $res = openssl_get_privatekey($priKey);
        //把需要解密的内容，按128位拆开解密
        $crypto = '';
        // foreach (str_split($this->urlsafe_b64decode($content), 256) as $chunk) {str_split(base64_decode($content), 344)
        $arr = str_split($content, 344) ;
        foreach ($arr as $chunk) {
            $chunk = base64_decode($chunk);
            $nmae = openssl_private_decrypt($chunk, $decryptData, $res);
            $crypto .= $decryptData; 
            $decryptData = '' ;
        }
        openssl_free_key($res);
        return $crypto;
    }
}