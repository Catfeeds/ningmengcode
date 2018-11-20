<?php
/**
 **基类控制器
 **/
namespace app\student\controller;
use think\Controller;
use think\Request;
use think\Cache;
/**
 * 学生端检查用户是否登陆
 *
 */
class Loginbase extends Controller{

    public function checkUserLogin(){
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $header = Request::instance()->header();
        //当前用户没有登陆
        if(!isset($header['token'])||!Cache::has(config('queue.login_list').getTokenKey($header['token']))) {
            return false;
        }
        $token = getTokenKey($header['token']);
       // 根据token 获取对应的缓存
         $userinfo = Cache::get(config('queue.login_list').$token);
         return $userinfo['info'];
    }

}