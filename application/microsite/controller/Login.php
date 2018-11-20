<?php
/**
 * 机构学生登录 业务逻辑层
 * 
 * 
 */
namespace app\microsite\controller;
use think\Controller;
use app\microsite\business\MicroLoginManage;
class Login extends Controller
    {
    /**
     * 检测用户登录信息
     * @Author yr
     * @DateTime 2018-04-20T13:11:19+0800
     * @return   array();
     * URL:/microsite/login/login
     */
    public function login ()
    {
       
        $mobile = input('mobile');

        $password = input('password');

        $loginobj = new MicroLoginManage;

        $loginobj->login($mobile,$password);

    }


}
