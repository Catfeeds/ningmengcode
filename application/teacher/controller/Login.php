<?php

namespace app\teacher\controller;
use think\Controller;
use think\Request;
use app\teacher\business\LoginManage;

class Login extends Controller
{
  /**
     * 检测用户登录信息
     * @Author wangwy
     * @return   array();
     * URL:/student/login/login
     */
    public function login ()
    {

      //机构 标识id
      $mobile = $this->request->param('mobile');
      $passwd   = $this->request->param('passwd');
      $domain   = $this->request->param('domain');

      $loginobj = new LoginManage;
      $return = $loginobj->teacherLogin($mobile,$passwd,$domain);
      $this->ajaxReturn($return);
    }


}
