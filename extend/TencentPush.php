<?php
require_once "TLSSig.php";
use think\Cache;
/**
 * 腾讯消息推送 推送全部
 */
class TencentPush
{

	// 请填写在云通讯服务中申请的APPID。
	protected $SdkAppid = '1400087217';
	// 请填写APP管理员帐号。
	protected $Identifier = 'yujiafeng';
	// 设置用户
	protected $UserName = 'nm_tk123456';
	// 请填写APP管理员帐号的签名。
	protected $UserSig;
	// 机构名称
	protected $Nick = '51MenKe';
	// 机构图片
	protected $IMG = 'www.baidu.cn';



	// protected $URL = 
	
	public function getUrl(){
		// 赋值签名
		return '?usersig='.$this->UserSig.'&identifier='.$this->Identifier.'&sdkappid='.$this->SdkAppid.'&contenttype=json';
	}

	public function __construct(){
		$this->getsig();
		// $this->loginUser();
	}

	
	/**
	 * 用户名登录
	 */
	public function loginUser(){
		$url = 'https://console.tim.qq.com/v4/im_open_login_svc/account_import';
		$data = ['Identifier'=>$this->Identifier,'Nick'=>$this->Nick,'FaceUrl'=>$this->IMG];
		$result = http_req('https','post',$url.$this->getUrl(),json_encode($data,true));
		// dump($result);
		return $result;
	}
	

	/**
	 * @author JCR 
	 * [addTag 给用户打标签]
	 * $type user 直推用户  tage直推标签
	 * $tag 1学生 2老师 3机构 [1,2,3]
	 * $organid 所属机构id
	 */
	public function addTag($type,$tag,$organid){
		$url = 'https://console.tim.qq.com/v4/openim/im_add_tag';
		if($type!='user'){
			foreach ($tag as $k => $val) {
				$tag[$k] = $val.'-'.$organid;
			}
		}
		$data = ['UserTags'=>array(['To_Account'=>$this->UserName,'Tags'=>$tag])];
		$json = json_encode($data,true);
		$result = http_req('https','post',$url.$this->getUrl(),$json);
		$result = object_to_array(json_decode($result));
		return $result;
	}

	/**
	 * [deleteTag 删除用户标签]
	 * @author JCR 
	 * @param  [type] $uid     [用户uid]
	 * @param  [type] $tag     [标签数组] [1,2,3]
	 * @param  [type] $organid [description]
	 * @return [type]          [description]
	 */
	public function deleteTag($uid,$tag = [],$organid){
		$url = 'https://console.tim.qq.com/v4/openim/im_remove_tag';
		foreach ($tag as $k => $val) {
			$tag[$k] = $val.'-'.$organid;
		}
		$data = ['UserTags'=>['To_Account'=>$this->UserName,'Tags'=>$tag]];
		$result = http_req('https','post',$url.$this->getUrl(),json_encode($data,true));
		return $result;
	}


	/**
	 *  @param  [type] $attr     [属性键]
	 *  @param  [type] $info     [现有属性键 集]
	 *  给账户添加属性
	 */
	public function attrAdminAdds($attr,$info = []){
		$url = 'https://console.tim.qq.com/v4/openim/im_set_attr_name';
		$count = count($info);
		$info[(string)$count] = $attr;
		// dump(json_encode(array('To_Account'=>$info),true));
		$result = http_req('https','post',$url.$this->getUrl(),json_encode(array('To_Account'=>$info),true));
		var_dump($result);
		return $result;
	}


	/**
	 * 获取账户属性
	 * 
	 */
	public function getAdminAttr($key){
		$url = 'https://console.tim.qq.com/v4/openim/im_get_attr_name';
		$result = http_req('https','post',$url.$this->getUrl(),json_encode([],true));
		$result = object_to_array(json_decode($result));
		if($result==null){
			$this->attrAdminAdds($key,[]);
		}else if($result['ActionStatus'] == 'OK'){
			if(!in_array($key,$result['AttrNames'])){
				$this->attrAdminAdds($key,$result['AttrNames']);
			}
		}
		return true;
	}

	/**
	 * [addAttr 给用户打属性 属性将其类型极光的别名 只打在uid上]
	 * @author JCR 
	 * @param [type] $uid     [用户id]
	 * @param [type] $attr    [属性]  array('uid'=>$val,'key1'=>$val1)
	 * @param [type] $organid [用户对于机构]
	 */
	public function addAttr($uid,$attr = [],$organid){
		$url = 'https://console.tim.qq.com/v4/openim/im_set_attr';
		// foreach ($attr as $key => $v) {
		// 	  $attr[$key] = $v.'-'.$organid;
		// }
		// foreach ($variable as $key => $value) {
		// 	# code...
		// }
		
		
		$data = ['UserAttrs'=>array(['To_Account'=>$this->UserName,'Attrs'=>$attr])];
		$jsons = json_encode($data,true);
		$result = http_req('https','post',$url.$this->getUrl(),$jsons);
		return $result;
	}


	/**
	 * [deleteAttr 删除用户打的属性]
	 * @author JCR 
	 * @param  [type] $uid     [用户uid]
	 * @param  [type] $attr    [属性] array('key','key1')
	 * @return [type]          [description]
	 */
	public function deleteAttr($uid,$attr){
		$url = 'https://console.tim.qq.com/v4/openim/im_remove_attr';
		$data = ['UserAttrs'=>['To_Account'=>$this->UserName,'Attrs'=>$attr]];
		// $result = curl_post($url.$this->getUrl(),$data);
		$result = http_req('https','post',$url.$this->getUrl(),json_encode($data,true));
		return $result;
	}


	/**
	 * [pushUserId 对指定用户推送]
	 * @author JCR 
	 * @param  [type] $uid     [用户uid]
	 * @param  [type] $title   [推送标题]
	 * @param  [type] $content [推送内容]
	 * @return [type]          [description]
	 */
	public function pushUserId($uid,$title,$content){
		$url = 'https://console.tim.qq.com/v4/openim/im_push';
		$data = ['MsgRandom'=>time(),
				 'From_Account'=>$uid,
				 'MsgBody'=>[
				 		'MsgType'=>$title,
				 		'MsgContent'=>['Text'=>$content],
					]];
		// var_dump($url.$this->getUrl());
		// $result = curl_post($url.$this->getUrl(),$data);
		$result = http_req('https','post',$url.$this->getUrl(),json_encode($data,true));
		return $result;
	}

	/**
	 * [pushUserTag 按用户标签推送]
	 * @author JCR 
	 * @param  [type] * $type user 直推用户  tage直推标签
	 * @param  [type] $tag     [标签]
	 * @param  [type] $title   [标题]
	 * @param  [type] $content [推送内容]
	 * @return [type]          [description]
	 */
	public function pushUserTag($type,$tag,$organid,$title,$content){
		$url = 'https://console.tim.qq.com/v4/openim/im_push';
		if($type!='user'){
			foreach ($tag as $k => $val) {
				$tag[$k] = $val.'-'.$organid;
			}
		}		
		$data = ['MsgRandom'=>rand(100000,999999),
				 'Condition'=>['TagsAnd'=>$tag],
				 'MsgBody'=>array([
				 		'MsgType'=>'TIMTextElem',
				 		'MsgContent'=>['Text'=>$content]
					])
				];

		$data = json_encode($data,true);
		$result = http_req('https','post',$url.$this->getUrl(),$data);
		$result = object_to_array(json_decode($result));
		return $result;
	}


	/**
	 * [pushUserTag 按用户属性推送]
	 * @author JCR 
	 * @param  [type] $uid     [用户id]
	 * @param  [type] $attr    [属性] array('key'=>$val,'key1'=>$val1)
	 * @param  [type] $title   [标题]
	 * @param  [type] $content [内容]
	 * @return [type]          [description]
	 */
	public function pushUserAttr($uid,$attr,$organid,$title,$content){
		$url = 'https://console.tim.qq.com/v4/openim/im_push';
		// foreach ($attr as $k => $val) {
		// 	$attr[$k] = $val.'-'.$organid;
		// }
		$data = ['MsgRandom'=>round(1000,9999),
				 'Condition'=>['AttrsAnd'=>$attr],
				 'MsgBody'=>[
				 		'MsgType'=>$title,
				 		'MsgContent'=>['Text'=>$content],
					]];
		$result = http_req('https','post',$url.$this->getUrl(),json_encode($data,true));
		return $result;
	}


	/**
	 * 生成签名sig
	 */
	public function getsig(){

		if(Cache::get('sigTen')){
			$this->UserSig = Cache::get('sigTen');
			return true;
		}

		// var_dump(Cache::get('sigTen'));

		$TLSSig = new \TLSSigAPI();
		$TLSSig->setAppid($this->SdkAppid);

		//私钥
		$private = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/TLss/ec_key.pem');
    	$TLSSig->SetPrivateKey($private);
    	//公钥
    	$public = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/TLss/public.pem');
    	$TLSSig->SetPublicKey($public);
    	// 生成 对应的Sig
		$sig = $TLSSig->genSig($this->Identifier);

		// var_dump($sig);
		//验证对应的usersig
		$init_time = time();
		$expire_time = 180 * 24 * 3600;
		$error_msg = '奶奶个熊';
		// 验证秘钥生成是否正确
		$result = $TLSSig->verifySig($sig, $this->Identifier, $init_time, $expire_time, $error_msg);
		// var_dump($result);
		if($result){
			Cache::set('sigTen',$sig,$expire_time);
			$this->UserSig = $sig;
		}else{
			return false;
		}
		return true;
	}

}








?>