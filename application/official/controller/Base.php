<?php
/**
**官方机构后台基类控制器
**/
namespace app\official\controller;
use think\Controller;
use think\Session;
use think\Request;
use login\Authorize;

class Base extends Authorize{
//class Base extends Controller{
	
    public function _initialize(){
		header('Access-Control-Allow-Origin: *');
    	parent::_initialize();	    	
    }

	public function __construct(){
		// 必须先调用父类的构造函数
		parent::__construct();
	
	}

	
}