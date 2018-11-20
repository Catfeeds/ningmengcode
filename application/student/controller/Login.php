<?php
/**
 * 机构学生登录 业务逻辑层
 * 
 * 
 */
namespace app\student\controller;
use think\Controller;
use app\student\business\LoginManage;
class Login extends Controller
    {
    /**
     * 检测用户登录信息
     * @Author yr
     * @DateTime 2018-04-20T13:11:19+0800
     * @return   array();
     * URL:/student/login/login
     */
    public function login ()
    {
       
        $mobile = input('mobile');

        $password = input('password');

        $loginobj = new LoginManage;

        $loginobj->login($mobile,$password);

    }


}
