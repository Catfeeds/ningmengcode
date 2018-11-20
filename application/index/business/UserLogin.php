<?php
/**
 * 对接网课端 对接业务逻辑
 */
namespace app\index\business;
use think\Cache;
use login\Rsa;
use app\index\model\Login;
use app\apphjx\model\Hjxappstudentinfo;
use think\Validate;
use login\Particle;
use Keyless;


class UserLogin{
    
    public function login($data){
        $data = $this->rsaDecode($data);
        $data = where_filter($data,['key','username','password','type','prephone','source','organtype','code']);

        $login = new Login;
        $validate = new Validate($login->rule, $login->message);
        if (!$validate->check($data)) {
            return return_format('',10210,$validate->getError());
        }
		
		//非好迹星app学生登陆则需填写密码
		if(($data['type'] != 4) && !isset($data['password'])){
			return return_format('',10532,lang('10532'));
		}
		
		//好迹星学生登陆则需要填写短信验证码
		if(($data['type'] == 4) && !isset($data['code'])){
			return return_format('',10209,lang('10209'));
		}

		if(($data['type'] != 2) && !isset($data['prephone'])){
			return return_format('',10210,lang('10210'));
		}

		// 登陆类型
		$logninfo['type'] = $data['type'];

		switch ($data['type']) {
			case 1:
				# 老师
				$where = ['mobile'=>$data['username'],'usertype'=>1,'prephone'=>$data['prephone'],'status'=>['neq',2]];
				$info = $login->teacherLogin($where);
				// 登录验证
				if(!$info) return return_format('',10212,lang('10212'));
				if(!checkUserMark($data['password'],$info['mix'],$info['password'])){
					return return_format('',10213,lang('10213'));
				}else if($info['status']==1){
					return return_format('',10214,lang('10214'));
				}

				$userinfo = $login->getTeacher($info['uid']);
				$userinfo['uid'] = $info['uid'];
				foreach($login->getUserUrl($logninfo['type'],$userinfo['uid']) as $v) $typearr[] = config('param.teacherTypeArr')[$v];
				$userinfo['teachertype'] = join(',', $typearr);
				break;
			case 2:
				# 机构
				$where = ['username'=>$data['username'],'usertype'=>array('in','0,2')];
				$info = $login->teacherLogin($where);

				// 登录验证
				if(!$info) return return_format('',10216,lang('10216'));
				if(!checkUserMark($data['password'],$info['mix'],$info['password'])){
					return return_format('',10217,lang('10217'));
				}else if($info['status']==1){
					return return_format('',10218,lang('10218'));
				}

				$userinfo = $login->getOrganInfo(1,$info['uid']);
				$userinfo['uid'] = $info['uid'];
				// 管理员类型特殊处理
				$logninfo['type'] = $info['usertype'];
				break;
			case 3:
				// 学生
				$where = ['mobile'=>$data['username'],'delflag'=>1,'prphone'=>$data['prephone']];
				$info = $login->studentLogin($where,false,'password,id,mix,status');
				// 登录验证
				if(!$info) return return_format('',10220,lang('10220'));
				if(!checkUserMark($data['password'],$info['mix'],$info['password'])){
					return return_format('',10221,lang('10221'));
				}else if($info['status'] == 1){
					return return_format('',10222,lang('10222'));
				}
				$userinfo = $login->studentLogin($where,TRUE);

				break;
			case 4:
				// 好迹星学生
				//判断验证码是否正确
				$cachedata = Cache::get('mobile'.$data['username']);
				if(empty( $cachedata)){
					return return_format('',39007,lang('39007'));
				}
				if(trim($cachedata) !== trim($data['code'])){
					//如果验证码输入错误超限 重新发送短信验证码
					if(!verifyErrorCodeNum($data['username'])){
						return return_format('',39008,lang('39008'));
					}
					return return_format('',39009,lang('39009'));
				}
				$where = ['mobile'=>$data['username'],'delflag'=>1,'prphone'=>$data['prephone']];
				$info = $login->hjxstudentLogin($where,false,'id,status');
				
				if(!$info){
					$sdata['mobile'] = $data['username'];
					$sdata['prephone'] = $data['prephone'];
					$sdata['addtime'] = time();
					$studentmodel = new Hjxappstudentinfo;
					$registerid = $studentmodel->addStudent($sdata);
				   if (empty($registerid)) {
					   return return_format('', 39012, lang('39012'));
				   }
				}	
				if($info['status'] == 1){
					return return_format('',10222,lang('10222'));
				}
				$userinfo = $login->hjxstudentLogin($where,TRUE);
				break;
			default:
				return return_format('',10226,lang('10226'));
				break;
		}

		// 获取登陆信息
		unset($userinfo['status']);
		$userinfo['token'] = token_str($logninfo['type'],$userinfo['uid']);
		$userinfo['logintime'] = date('Y-m-d H:i',time());
		// 存储数据结构
		$logninfo['token'] = $userinfo['token'];
		$logninfo['key']  = $data['key'];
		$logninfo['info'] = $userinfo;
		$logninfo['source'] = isset($data['source'])?$data['source']:'web';

		// 获取登陆角色的角色ID
		$logninfo['roleid'] = $login->getUserUrl($logninfo['type'],$userinfo['uid']);
		// 存储1天
		$expire  = $logninfo['type'] == 4 ? 86400 * 7 : 86400;
		Cache::set(config('queue.login_list').$logninfo['type'].'-'.$userinfo['uid'],$logninfo,$expire);

		return return_format($logninfo['info'],0,lang('10227'));
    }


	/**
	 * [internalLogin 登陆接口]
	 * @param $type     1 老师      2机构   3学生 4官方管理员登录
	 * @param $uid      teacherid
	 * @param $key
	 * @param $intype   是否是app注册 ture
	 * @return array
	 */
	public function internalLogin($type,$uid,$key,$intype = false){
		$login = new Login;
		$logninfo['type'] = $type;
		switch ($type) {
			case 1:
				# 老师
				$userinfo = $login->getTeacher($uid);
				break;
			case 2:
				# 机构
				$where = ['uid'=>$uid,'usertype'=>array('in','0,2')];
				$info = $login->teacherLogin($where);
				$userinfo = $login->getOrganInfo(1,$uid);
				$userinfo['uid'] = $info['uid'];
				$logninfo['type'] = $info['usertype'];
				break;
			case 3:
				// 学生
				$userinfo = $login->studentLogin(['id'=>$uid],TRUE);
				break;
			case 4:
				// 管理员
				$userinfo = $login->officialuserLogin(['id'=>$uid],true);
				// 赋予对应的角色 type
				$logninfo['type'] = $userinfo['type'];
				break;
			default:
				return return_format('',10226,lang('10226'));
				break;
		}

		// 获取登陆信息
		unset($userinfo['status']);
		$userinfo['token'] = token_str($type,$userinfo['uid']);
		$userinfo['logintime'] = date('Y-m-d H:i',time());

		// 存储数据结构
		$logninfo['token'] = $userinfo['token'];
		$logninfo['key']  = $key;
		$logninfo['info'] = $userinfo;

		// 获取登陆角色的角色ID
		$logninfo['roleid'] = $login->getUserUrl($logninfo['type'],$userinfo['uid']);

		if($intype){
//			$this->setPush($userinfo['uid'],$logninfo['type'],$registrationid);
			$logninfo['source'] = 'app';
		}else{
			$logninfo['source'] = 'web';
		}

		// 存储1天
		Cache::set(config('queue.login_list').$type.'-'.$userinfo['uid'],$logninfo,86400);

		return return_format($logninfo['info'],0,lang('10227'));
	}


	/**
	 * 公钥解密
	 * @param $data
	 * @return array|mixed
	 */
	public function rsaDecode($data){
		if(!empty($data)){
			$ret = new Rsa ;// 1加密
			return json_decode($ret->rsaDecryptorign($data),true);
		}else{
			return [];
		}
	}


	/**
	 * 登陆给用户打标签
	 * @param $uid
	 * @param $type
	 * @param $registrationid
	 */
	public function setPush($userid, $usertype, $registrationid) {
		$pushs = new \app\admin\model\Pushs();
		// 别名手机唯一ID发生变动或被修改
		$jpu = new \JPushs($usertype);

		$info = $pushs->getPushsId($userid, $usertype);
		if ($info && $info['registrationid'] == $registrationid) {
			if ($info['logintype'] == 0) {
				$pushs->exidPush(['id' => $info['id']], 1);
			}

			return TRUE;
		} else if ($info && $info['registrationid'] != $registrationid) {
			// 做了修改 ，如果一个签名可以对应多个registrationid 在这做一次删除操作
			$jpu->deleteAlias($userid, $usertype);
		}

		$return = $jpu->addAlias($registrationid, $userid, $usertype);
		if ($return) {
			$data = ['userid' => $userid, 'usertype' => $usertype, 'registrationid' => $registrationid, 'logintype' => 1];
			if ($info) {
				$data['id'] = $info['id'];
			}

			$pushs->addPush($data);
		}
		return TRUE;
	}



}
?>