<?php

use think\Controller;
use think\Cache;
/**
 * 教师端登录生成token
 *
 */
class TeacherLogin extends Controller
{
    public $userid;
    public $organid;
    /**
     * [登录成功后设置token]
     * @Author wangwy
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function settoken($uid,$orgid,$type)
    {
        if($type == 1){
            //学生登录
            $type = 'studentid';
        }else{
            $type = 'teacherid';
        }
        //加密
        $str = md5(uniqid(md5(microtime(true)),true));  //生成一个不会重复的字符串
        $token = sha1($str);  //
        $token  = $uid.'-'.$token.'-'.$orgid;
        //token存入缓存里 过期时间一天
        $cacheobj = new Cache;
        $cacheobj::set($type.$uid,array('token' => $token,'uid'=>$uid,'organid'=>$orgid),86400);
        return $token;
    }
    /**
     * [验证token]
     * @Author yr
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public  function checktokens($type)
    {
        $request = \think\Request::instance();
        $token = $request->header('token');
        if(empty($token)){
            $this->ajaxReturn('',32002,'请先登录');
        }
        if($type == 1){
            //学生登录
            $type = 'studentid';
        }else{
            $type = 'teacherid';
        }
        $arr  = explode('-',$token);
        $uid = $arr[0];
        $orgid = $arr[2];
        //下面是每个接口都必须调用的token验证代码，验证具体实现是在
        $args['token'] = $token;
        $cacheobj = new Cache;
        $uidarray = $cacheobj->get($type.$uid);
        //登录超时
        if(empty($uidarray)){
            $data  = [
                'code' => 38000,
                'data' => '',
                'info' => '请先登录'
            ];
            $this->ajaxReturn($data);
        }
        //未登录
        $servertoken  = $uidarray['token'];
        if($servertoken !== $token){
            $data = [
                'code' =>38001,
                'data' => '',
                'info' => '您的账号已经在另一端重新登陆',
            ];
            $this->ajaxReturn($data);
        }
        $this->userid = $uid;
        $this->organid = $orgid;
        return true;
    }
    /**
     * [登录成功后设置token]
     * @Author yr
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function logout($uid,$type)
    {
        if($type == 1){
            //学生登录
            $type = 'studentid';
        }else{
            $type = 'teacherid';
        }
        //token存入缓存里 过期时间一天
        $cacheobj = new Cache;
        $cacheobj::rm($type.$uid);
        return true;
    }
    /**
     * [从token中获取organid]
     * @Author yr
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function gettokenorganid($type){
      $request = \think\Request::instance();
      $token = $request->header('token');
      if($type == 1){
          //学生登录
          $type = 'studentid';
      }else{
          $type = 'teacherid';
      }
      $arr  = explode('-',$token);
      $uid = $arr[0];
      $orgid = $arr[2];
      //下面是每个接口都必须调用的token验证代码，验证具体实现是在
      $args['token'] = $token;
      $cacheobj = new Cache;
      $uidarray = $cacheobj->get($type.$uid);
      //登录超时
      if(empty($uidarray)){
          $data  = [
              'code' => 38000,
              'data' => '',
              'info' => '请先登录'
          ];
          $this->ajaxReturn($data);
      }
      //未登录
      $serverorganid  = $uidarray['organid'];
    }
}
