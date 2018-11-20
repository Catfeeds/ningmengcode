<?php
use think\Cache;
use think\Db;

/**
 * RSA 加密
 */

class MkRsa
{
	public $pi_key;
	public $pu_key;
	public $private_key;
	public $public_key;

	//判断公钥和私钥是否可用
	public function __construct()
	{
		$this->private_key = file_get_contents('./pem/nm_private_key.pem');
		$this->public_key  = file_get_contents('./pem/nm_public_key.pem');
		$this->pi_key = openssl_pkey_get_private($this->private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
		$this->pu_key = openssl_pkey_get_public($this->public_key);//这个函数可用来判断公钥是否是可用的
	}


	/**
	 * 返回前台公钥
	 * @return bool|string
	 */
	public function getPublicKey(){
		return $this->public_key;
	}

	/**
	 * 私钥加密
	 * @param $data
	 * @return mixed|string
	 */
	public function PrivateEncrypt($data){
		// openssl_private_encrypt($data,$encrypted,$this->pi_key);
		$crypto = '';
		foreach (str_split($data, 117) as $chunk) {
			openssl_private_encrypt($chunk, $encryptData, $this->pi_key);
			$crypto .= $encryptData;
		}
		$encrypted = $this->urlsafe_b64encode($crypto);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
		return $encrypted;
	}

	//加密码时把特殊符号替换成URL可以带的内容
	function urlsafe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_',''),$data);
		return $data;
	}

	//解密码时把转换后的符号替换特殊符号
	function urlsafe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	//私钥加密的内容通过公钥可用解密出来
	public function PublicDecrypt($encrypted){
		// $encrypted = $this->urlsafe_b64decode($encrypted);
		$crypto = '';
		foreach (str_split($this->urlsafe_b64decode($encrypted), 128) as $chunk) {
			openssl_public_decrypt($chunk, $decryptData, $this->pu_key);
			$crypto .= $decryptData;
		}
		//openssl_public_decrypt($encrypted,$decrypted,$this->pu_key);//私钥加密的内容通过公钥可用解密出来
		return $crypto;
	}

	//公钥加密
	public function PublicEncrypt($data){
		//openssl_public_encrypt($data,$encrypted,$this->pu_key);//公钥加密
		$crypto = '';
		foreach (str_split($data, 117) as $chunk) {
			openssl_public_encrypt($chunk, $encryptData, $this->pu_key);
			$crypto .= $encryptData;
		}
		$encrypted = $this->urlsafe_b64encode($crypto);
		return $encrypted;
	}

	//私钥解密
	public function PrivateDecrypt($encrypted)
	{
		$crypto = '';
		foreach (str_split($this->urlsafe_b64decode($encrypted), 128) as $chunk) {
			openssl_private_decrypt($chunk, $decryptData, $this->pi_key);
			$crypto .= $decryptData;
		}
		//$encrypted = $this->urlsafe_b64decode($encrypted);
		//openssl_private_decrypt($encrypted,$decrypted,$this->pi_key);
		return $crypto;
	}


}
?>