<?php
/**
**登录,退出控制器
**/
namespace app\official\controller;
use think\Controller;
use think\Session;
use think\Request;
use app\official\business\UserManage;
use app\official\business\OrganManage;
use Messages;
use think\Cache;

class Login extends Controller{

	


    /**
     * [login 	//官网后台登录]
     * @Author zzq
     * @DateTime 2018-05-18
     * @param useraccount string           [账户名|用户名]           
     * @param password string           [密码]     
     * @return   [array]                   [description]
     * @return 返回的信息
     */
	public function login(){

		$data = [];
        $data['username'] = Request::instance()->post('username') ? Request::instance()->post('username') : '';
        $data['password'] = Request::instance()->post('password') ? $data['password'] = Request::instance()->post('password') : '' ;
        $obj = new UserManage();
        $res = $obj->checkUserAndPass($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
    /**
     * [logout 	//官网后台退出]
     * @Author zzq
     * @DateTime 2018-05-18
     * @param 无参数       []  
     * @return 返回的信息
     */
	public function logout(){

		//判断是否已经退出
        $obj = new UserManage();
        $res = $obj->doLogout();
		$this->ajaxReturn($res);
        return $res; 	
	}



}