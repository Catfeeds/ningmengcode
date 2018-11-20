<?php

namespace app\appteacher\controller;

use think\Controller;
use think\Request;
use app\appteacher\business\LoginManage;
class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function login()
    {
        //机构 标识id
        $mobile = $this->request->param('mobile');
        $passwd   = $this->request->param('passwd');
        $domain   = $this->request->param('domain');

        //$organid = Session::get('organid');

        $organobj = new LoginManage;
        //获取教师列表信息,默认分页为5条
        $return = $organobj->teacherLogin($mobile,$passwd,$domain);
        $this->ajaxReturn($return);

    }



}
