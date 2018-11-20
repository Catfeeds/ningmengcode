<?php
use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;
use think\Cache;
use think\Log;
use app\admin\model\Messagesign;
use app\admin\model\Message;


require_once "sms/SmsSingleSender.php";
// require_once "sms/SmsMultiSender.php";
// require_once "sms/SmsVoiceVerifyCodeSender.php";
// require_once "sms/SmsVoicePromptSender.php";
// require_once "sms/SmsStatusPuller.php";
// require_once "sms/SmsMobileStatusPuller.php";

/**
 * 短信发送
 */

class Messages
{
	// 短信应用SDK AppID
	protected $appid = '1400134605';
	// 短信应用SDK AppKey
	protected $appkey = '1cab32d368b18863d7e5d3b7f7070e8c';
	protected $smsSign = '柠檬教育';


	/**
	 * 短信发送接口 指定模版
	 * @Author jcr
	 * @param $[mobile]  [手机号]
	 * @param $[type]    [模板  4 短信验证码 ]
	 * @param $[params]  [传数组 对应短信模板值 模板所需参数几个就传几个 顺序和模板顺序一样 键从0开始]
	 * @param $[prphone] [手机号前缀]
	 * @param $[identifytype] [标识类型 根据业务传输字符串 限制验证码使用区间]
	 * @param $[organid] [机构id 传他时获取对应的短信签名]
	 * @return array()
	 */
	public function sendMeg($mobile,$type=4,$params = [],$prphone='86',$identifytype='',$organid = FALSE){
		if($type==4 || $type==15){
			// 短信模板时 缓存对应的验证码10分钟
			if(Cache::get('mobile'.$mobile.$identifytype)){
				$params[0] = Cache::get('mobile'.$mobile.$identifytype);
			}else{
				Cache::set('mobile'.$mobile.$identifytype,$params[0],600);
			}
		}
		$sign = "";

		// 中文模版
		$typearr = [
			'4'=>'183495',
			'5'=>'193919',
			'6'=>'193914',
			'7'=>'193916',
			'8'=>'193918',
			'9'=>'193910',
			'10'=>'193913',
            '11'=>'196659',
            '12'=>'196662',
            '13'=>'196661',
            '14'=>'202512',
            '15'=>'217824',
		];

		try {
			$ssender = new SmsSingleSender($this->appid, $this->appkey);
			$result = $ssender->sendWithParam($prphone, $mobile, $typearr[$type],$params, $sign, "", "");

			// 签名参数未提供或者为空时，会使用默认签名发送短信
			$rsp = object_to_array(json_decode($result));
			return $rsp;
		} catch(\Exception $e) {
			return ['result'=>100,'info'=>'短信发送失败'];
		}
	}




	/**
	 * [addSign 腾讯云生成签名]
	 * @param $signName
	 * @return array()
	 */
	public function addSign($signName,$organid){
		$signs = new Messagesign();
		$info = $signs->getSignById($organid);
		if($info && $info['status']!=2){
			return ['code'=>10100,'info'=>'机构只能签一次名'];
		}

		try {
			$ssender = new SmsSingleSender($this->appid, $this->appkey);
			if($info){
				// 修改签名
				$result = $ssender->saveSign($signName,$info['signid']);
				// 签名参数未提供或者为空时，会使用默认签名发送短信
				$rsp = object_to_array(json_decode($result));
				if($rsp['result']==0){
					$data['signname'] = $signName;
					$data['status'] = $rsp['data']['status'];
					$signs->updateSign($data,['id'=>$info['id']]);
					return ['code'=>0,'info'=>'签名提交成功，请等待审核'];
				}else{
					return ['code'=>10101,'info'=>'签名提交失败'];
				}
			}else{
				// 添加签名
				$result = $ssender->addSign($signName);
				// 签名参数未提供或者为空时，会使用默认签名发送短信
				$rsp = object_to_array(json_decode($result));
				if($rsp['result']==0){
					$data['organid'] = $organid;
					$data['signid'] = $rsp['data']['id'];
					$data['signname'] = $signName;
					$data['status'] = $rsp['data']['status'];
					$signs->updateSign($data);
					return ['code'=>0,'info'=>'签名提交成功，请等待审核'];
				}else{
					return ['code'=>10102,'info'=>'签名提交失败'];
				}
			}
		} catch(\Exception $e) {
			return ['code'=>10103,'info'=>'签名提交失败'];
		}
	}


	/**
	 * [refreshSign 刷新签名状态]
	 * @param $signId
	 * @return array();
	 */
	public function refreshSign($signId){
		if(!$signId){
			return ['code'=>10104,'info'=>'签名ID不能为空'];
		}
		$signs = new Messagesign();
		try {
			$ssender = new SmsSingleSender($this->appid, $this->appkey);
			$result = $ssender->getSign($signId);
			// 签名参数未提供或者为空时，会使用默认签名发送短信
			$rsp = object_to_array(json_decode($result));
			if($rsp['result']==0 && $rsp['data']){
				foreach ($rsp['data'] as $k => $v){
					$signs->updateSign(['status'=>$v['status']],['signid'=>$v['id']]);
				}
				return ['code'=>10105,'info'=>'刷新成功'];
			}else{
				return ['code'=>10106,'info'=>'暂无刷新'];
			}
		} catch(\Exception $e) {
			return ['code'=>10107,'info'=>'暂无刷新'];
		}
	}

    /******* 消息模块 *******/

    /**
     * 消息添加
     * @Author JCR
     * @param $data['title']	标题	 如:上课提醒/预约提醒/购买提醒/课程上架。。。
     * @param $data['content']	消息内容
     * @param $data['usertype']	用户类型  0为机构超级管理员 1为老师 2机构添加的管理账号 3学生 4 官方 5官方添加管理员
     * @param $data['userid']	用户userid
     * @param $data['externalid'] 外部ID 做跳转关联使用 详细看表注释
     * @param $type				1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
     * @param $organid			机构ID
     * @return []
     */
    public function addMessage($data,$type){
        $data = where_filter($data,['title','content','usertype','userid','externalid']);
        if(!isset($data['content']) || !isset($data['usertype']) || !isset($data['userid'])){
            return return_format('',10153,lang('param_error'));
        }
        // 需关联ID的类型
        $inType = [1,3,4,5,6,8,10,13];
        if(in_array($type,$inType) && !isset($data['externalid'])){
            return return_format('',10152,lang('10152'));
        }

        $message = new Message;
        //$data['organid'] = $organid;
        $data['addtime'] = time();
        $data['type'] = $type;
        $data['userid'] = substr($data['userid'],1);
        if($message->updateMessage($data)){
            //$contentarr = array(['uid'=>substr($data['userid'],1),'content'=> $data['title'].$data['content']]);
            //$res = $this->useRedis($contentarr);//单条发布消息
            // 消息添加成功

            // 是否推送 externalid
            if($data['usertype']==1 || $data['usertype']==3 || $data['usertype']==4){
                // 查看该学生和老师是否在app上登陆过
                $pushs = new \app\admin\model\Pushs();
                $info = $pushs->getPushsId($data['userid'],$data['usertype']);
                if($info&&$info['logintype']==1){
                    // 在app上登陆过推送 使劲推
                    $jpush = new JPushs($data['usertype']);
                    $aad = $jpush->pushUserTag('alias',[$data['userid'].'.'.$data['usertype']],$data['title'],$data['content']);
//					dump($aad);
                }
            }
            return true;
        }else{
            // 消息添加失败
            return false;
        }
    }
    /*
     * 批量添加信息到message中
     * @param $data['title']	标题	 如:上课提醒/预约提醒/购买提醒/课程上架。。。
     * @param $data['content']	消息内容
     * @param $data['usertype']	用户类型  0为机构超级管理员 1为老师 2机构添加的管理账号 3学生 4 官方 5官方添加管理员
     * @param $data['userid']	用户userid
     * @param $data['externalid'] 外部ID 做跳转关联使用 详细看表注释
     * @param $type				1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
     * @param $organid			机构ID
     * @return []
     */
    public function addMessagearr($dataarr,$type){
        foreach($dataarr as $k => $v){
            $arrs[$k] = where_filter($v,['title','content','usertype','userid','externalid']);
            if(!isset($arrs[$k]['content']) || !isset($arrs[$k]['usertype']) || !isset($arrs[$k]['userid'])){
                return return_format('',10153,lang('param_error'));
            }
            // 需关联ID的类型
            $inType = [1,3,4,5,6,8];
            if(in_array($type,$inType) && !isset($arrs[$k]['externalid'])){
                return return_format('',10152,lang('10152'));
            }
        }
        $pushs = new \app\admin\model\Pushs();
        $message = new Message;
        foreach($arrs as $k => $v){
            //$v['userid'] = substr($v['userid'],1);
            $res[$k]['uid'] = $v['userid'];
//            $arrs[$k]['title'] = '【'.$v['title'].'】';//拼接中括号
            //$arrs[$k]['organid'] = empty($v['organid'])?1:$v['organid'];
            $arrs[$k]['addtime'] = time();
            $arrs[$k]['type'] = $type;
            $arrs[$k]['userid'] = $v['userid'];
            //$res[$k]['content'] = $v['title'].$v['content'];
            //$arrs[$k]['content'] = $v['title'].$v['content'];

            if($v['usertype']==1 || $v['usertype']==3){
                // 查看该学生和老师是否在app上登陆过
                $info = $pushs->getPushsId($v['userid'],$v['usertype']);
                if($info&&$info['logintype']==1){
                    // 在app上登陆过推送 使劲推
                    $jpush = new JPushs($v['usertype']);
                    $jpush->pushUserTag('alias',[$v['userid'].'.'.$v['usertype']],$v['title'],$v['content']);
                }
            }

        }
        if(!$message->addMessage($arrs)){
            // 消息添加失败
            return false;
        }
        //$res = $this->useRedis($arrs);//发布消息
        // 消息添加成功
        // 是否推送 externalid
        return true;
    }
    /*
     * 将实时提醒消息发布到订阅频道上
     * @Author wangwy
     */
    protected function useRedis($contentarr){
        //开始将数据存储在redis中
        $redis = new \redis();
        //log::write(date('Y-m-d H:i:s',time()).'开始链接reids','log',TRUE);
        $link = $redis->connect('10.0.0.9',6379);
        $auth = $redis->auth('vkpKvCgiA*wN4Xo*');
        // 再消息订阅频道发布
        $channel = 'info';
        $res = $redis->publish($channel,json_encode($contentarr));
        return $res;
    }



    /**
     * 查看消息
     * @Auchor JCR
     * @param $id		// 消息ID
     * @param $istoview	// 是否被查看 0未查看 1已查看
     * @return
     */
    public function setMessage($id,$istoview){
        $message = new Message;
        $istoview = $istoview?$istoview:0;
        if($message->updateMessage(['istoview'=>$istoview],['id'=>$id])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @Author wangwy
     * @param $idarr
     * @param $istoview
     * @return bool
     */
    public function viewMessagearr($idarr,$istoview){
        $message = new Message;
        $istoview = $istoview?$istoview:0;
        if($message->updateMessage(['istoview'=>$istoview],['id'=>['in',$idarr]])){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',20508,lang('error'));
        }
    }


    /**
     * 查询对应的消息列表
     * @Auchor		JCR
     * @param 		$where	 查询条件
     * @pagenum		$pagenum 第几页
     * @param		$organid 机构ID
     * @return 		[]
     */
    public function getMessageList($where,$pagenum){
        $message = new Message;
        $list = $message->getMessageList($where,$pagenum,20);
        if($list){
            foreach ($list as $k => &$v){
                $v['addtime'] = date('Y-m-d',$v['addtime']);
            }
            $indata['data'] = $list;
            $indata['pageinfo'] = ['pagesize'=>20,'pagenum'=>$pagenum,'total'=>$message->getMessageCount($where)];
            return return_format($indata,0,lang('success'));
        }else{
            return return_format('',10151,lang('error_log'));
        }
    }
    /**
     * 查询对应的消息列表
     * @Auchor		JCR
     * @param 		$where	 查询条件
     * @pagenum		$pagenum 第几页
     * @param		$organid 机构ID
     * @return 		[]
     */
    public function getAllmsgcount($where,$organid){
        $message = new \app\appteacher\model\Message;
        $where['organid'] = $organid;
        $where['istoview'] = 0;//查询未查看的内容
        $num = $message->getMessageCount($where);
        return $num;
        //$list = $message->getMessageList($where,$pagenum,1000);
//		if($list){
//			foreach ($list as $k => &$v){
//				$v['addtime'] = date('Y-m-d',$v['addtime']);
//			}
//			$indata['data'] = $list;
//			//$indata['pageinfo'] = ['pagesize'=>20,'pagenum'=>$pagenum,'total'=>$message->getMessageCount($where)];
//			return return_format($indata,0,lang('success'));
//		}else{
//			return return_format('',10151,lang('error_log'));
//		}
    }














}








?>