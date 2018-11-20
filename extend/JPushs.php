<?php
require_once "jpushautoload.php";
require_once __DIR__ . '/JPush/Config.php';

use JPush\Client as JPush;

/**
 * 极光消息推送
 */
class JPushs
{

	// appKey
	protected $studentAppKey = 'a127f81761b676166de12238';
	// masterSecret
	protected $studentMasterSecret = '5e5d25306af52b7950989a60';

//	protected $teacherAppKey = '74522d2484f42185abde123d';
//	protected $teacherMasterSecret = 'c848dcf244d59bc2fef43a2a';

	// 请填写APP管理员帐号的签名。
	protected $jpushObj;


	/**
	 * JPushs constructor.
	 * @param $type 1推给老师 3 学生
	 */
	public function __construct($type){
		// 实例化
//		if($type==1){
//			$this->jpushObj = new JPush($this->teacherAppKey, $this->teacherMasterSecret);
//		}else{
			$this->jpushObj = new JPush($this->studentAppKey, $this->studentMasterSecret);
//		}
	}

	/**
	 * [pushUser 对别名和标签推送]
	 * @param  [type] $type 	  [tag标签（群） alias 别名(单)]
	 * @param  [type] $[tagAlias] [别名和标签集]
	 * @param  [type] $[organid]  [机构标识]
	 * @param  [type] $[title]    [推送标题]
	 * @param  [type] $[content]  [推送内容]
	 * @return [type] [description]
	 */
	public function pushUserTag($type,$tagAlias = [],$title,$content){


		// 推送标签 处理
		try {
			$response = $this->jpushObj->push()->setPlatform(array('ios', 'android'));
			if($type=='tag'){
				// 按标签推送
				$response = $response->addTag($tagAlias);
			}else{
				// 按别名推送
				$response = $response->addAlias($tagAlias);
			}
			$response = $response->setNotificationAlert($title)
				->iosNotification($content, array(
					'title' => $title,
					'sound' => 'sound.caf',
//		            'content_type'=>'text',
					'extras' => array(
						'key' => 'value',
					),
				))
				->androidNotification($content, array(
					'title' => $title,
					'builder_id' => 2,
//					'content_type'=>'text',
					'extras' => array(
						'key' => 'value',
					),
				))
				->options(array(
					// True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境
					'apns_production' => config('param.JPProduction'),
				))->send();
//		    dump($response);
			return $response['http_code']==200?TRUE:FALSE;
		} catch (\JPush\Exceptions\APIConnectionException $e) {
			return false;
		} catch (\JPush\Exceptions\APIRequestException $e) {
			return false;
		}
	}


	/**
	 * [addAlias 打别名]
	 * @param  $registration_id
	 * @param  $uid
	 * @param  $type
	 * @param  $organid
	 * @return []
	 */
	public function addAlias($registration_id,$uid,$type){
		try {
			$alias = $uid.'.'.$type;
			$response = $this->jpushObj->device()->updateAlias($registration_id,$alias);
			return $response['http_code']==200?TRUE:FALSE;
		} catch (\JPush\Exceptions\APIConnectionException $e) {
			return false;
		} catch (\JPush\Exceptions\APIRequestException $e) {
			return false;
		}
	}


	/**
	 * [deleteAlias 删除别名]
	 * @param $uid
	 * @param $type
	 * @return bool
	 */
	public function deleteAlias($uid,$type){
		try {
			$alias = $uid.'.'.$type;
			$response = $this->jpushObj->device()->deleteAlias($alias);
			return $response['http_code']==200?TRUE:FALSE;
		} catch (\JPush\Exceptions\APIConnectionException $e) {
			return false;
		} catch (\JPush\Exceptions\APIRequestException $e) {
			return false;
		}
	}

	/**
	 * [addAlias 打标签]
	 * @param [type] $uid [uid 对应他打别名]
	 * @return []
	 */
	public function addTags($registration_id,$uid,$tag,$organid){
		try {
			$response = $this->jpushObj->device()->addTags($uid, $tag.'-'.$organid);
			return $response['http_code']==200?TRUE:FALSE;
		} catch (\JPush\Exceptions\APIConnectionException $e) {
			return FALSE;
		} catch (\JPush\Exceptions\APIRequestException $e) {
			return FALSE;
		}
	}

	/**
	 * [addAlias 打别名]
	 * @param  $registration_id
	 * @param  $uid
	 * @param  $type
	 * @param  $organid
	 * @return []
	 */
	public function getDevices($registration_id){
		try {
			$response = $this->jpushObj->device()->getDevices($registration_id);
			return $response;
		} catch (\JPush\Exceptions\APIConnectionException $e) {
			return FALSE;
		} catch (\JPush\Exceptions\APIRequestException $e) {
			return FALSE;
		}
	}

}



?>