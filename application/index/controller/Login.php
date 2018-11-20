<?php
namespace app\index\controller;
use think\Controller;
use think\Cache;
use think\Request;
use login\Authorize;
use app\index\business\UserLogin;
use Keyless;
use login\Rsa;
class Login extends Controller
{
    //自定义初始化
    protected function _initialize() {
		header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token,lang');
		header('Access-Control-Allow-Origin: *');
    }

    /**
     * 后台控制器函数
     *
     * **/
    public function login(){
        // key
        // username
        // password
        // type  1
        // source : web/app
        $data = Request::instance()->POST();
        $login = new UserLogin();
        $dataReturn = $login->login($data['data']);
        $this->ajaxReturn($dataReturn);
    }


    /**
     * [getPublicKey 返回公钥]
     * @return [type] [description]
     */
    public function getPublicKey(){
        $ret = new Rsa;
        $this->ajaxReturn(['code'=>0,'info'=>'ok','key'=>$ret->getPublicKey()]);
    }


	/**
	 * [exitLogin 退出登陆]
	 */
    public function exitLogin(){
		$header = Request::instance()->header();
		$requestpost = Request::instance()->post(false);
		$header = where_filter($header,['token','sign','starttime']);
		if(!isset($header['token'])||!Cache::has(config('queue.login_list').getTokenKey($header['token']))){
			$this->ajaxReturn(['code'=>-40666,'info'=>'请先登录','data'=>'']);
		}
		// 解析出真正的token
        $token = getTokenKey($header['token']);
		$userInfo = Cache::get(config('queue.login_list').$token);

		if ($header['token']!=$userInfo['token']){
			$this->ajaxReturn(['code'=>-40666,'info'=>'您的账号在其他端口登陆了，请重新登陆','data'=>'']);
			exit;
		}

		// 根据传输参数生成签名
		$sign = Keyless::getMd5String($requestpost,$userInfo['key'],$header['starttime'],$header['token']);
		if($sign!=$header['sign']){
			$this->ajaxReturn(['code'=>-40009,'info'=>'数据异常，请重新提交','data'=>'']);
			exit;
		}

		Cache::rm(config('queue.login_list').$token);
		$this->ajaxReturn(['code'=>0,'info'=>'退出成功','data'=>'']);
	}



	public function cegetca(){
    	$list = Cache::get('3-104');
    	dump($list);
	}

	/**
	 * [deleRedis 删除redis指定键 角色所对接口权限id]
	 */
	 public function deleRedis(){
	 	for($i=1;$i<=5;$i++){
	 		Cache::rm('RoleKey_'.$i);
	 	}
	 }
}
