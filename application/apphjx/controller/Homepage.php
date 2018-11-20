<?php
namespace app\apphjx\controller;

use think\Controller;
use app\apphjx\business\HomePageManage;
use app\apphjx\business\ApphjxUserManage;
class Homepage extends \Base
{
    public function __construct(){
        // 必须先调用父类的构造函数
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin:*');
        parent::__construct();
    }
	
    /**
     * 首页列表
     * @Author ZQY
     * @DateTime 2018-10-16 18:04:23
     * URL:/apphjx/homePage/homePageList
     */
    public function homePageList()
    {
        $compositionstatus = $this->request->param('compositionstatus');
        $studentid = $this->studentid;
        $homepage = new HomepageManage;
        $homepage_list = $homepage->getHomepageList($compositionstatus,$studentid);
        $this->ajaxReturn($homepage_list);

    }
    /**
     * 首页列表-作文详情
     * @Author ZQY
     * @DateTime 2018-10-17 15:49:23
     * URL:/apphjx/homePage/compositionDetail
     */
    public function compositionDetail()
    {
        $compositionid = $this->request->param('compositionid');
        $composition = new HomepageManage();
        $composition_data = $composition->seeCompositionData($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;

    }
	
	/**
     * 登陆发送短信
     * @Author lc
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @return   array();
     * URL:/apphjx/Homepage/sendMobileMsg
     */
    public function sendMobileMsg(){
        $mobile = $this->request->param('mobile');
        $prphone = $this->request->param('prphone');
        $userobj = new ApphjxUserManage;
        $res = $userobj->sendMsg($mobile, $prphone);
        $this->ajaxReturn($res);
    }
}