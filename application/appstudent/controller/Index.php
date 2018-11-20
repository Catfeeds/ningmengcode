<?php
/**
 * 首页分类课程筛选 业务逻辑层
 *
 *
 */
namespace app\appstudent\controller;
use think\Controller;
use app\appstudent\business\AppHomepageManage;
use app\appstudent\business\AppLoginManage;
use app\appstudent\business\AppUserManage;
use app\appstudent\business\OfficalAppLoginManage;
use app\appstudent\business\OfficalAppUserManage;
use app\index\business\UserLogin;

class Index extends Controller
{

    /**
     * 学生注册
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone  国家区号
     * @param    [string]              code     验证码
     * @param   [int]                  organid 机构id
     * @param   [string]               password   密码
     * @return   array();
     * URL:/appstudent/Home/register
     */
    public function register()
    {
        $post  = $this->request->post(false);
        $loginobj = new AppLoginManage();
        $res = $loginobj->register($post);
        $this->ajaxReturn($res);

    }
    /**
     * 学生修改密码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return   array();
     * URL:/student/Homepage/updatePass
     */
    public function updatePass(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $newpass = $this->request->param('newpass');
        $userobj = new AppUserManage();
        $res = $userobj->updatePassword($mobile,$code,$newpass);
        $this->ajaxReturn($res);
    }
    /**
     * 首页获取机构相关信息接口
     * @Author yr
     * @DateTime 2018-05-5T16:20:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getOrganInfo
     */
    public function getOrganInfo()
    {
        $homepageobj  = new AppHomepageManage;
        $res = $homepageobj->getOrganInfo();
        $this->ajaxReturn($res);
    }
    /**
     * 随机返回4位验证码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param
     * @return   array();
     * URL:/appstudent/Homepage/randomCode
     */
    public function randomCode(){
        $userobj = new AppUserManage;
        $res = $userobj->getCaptcha();
        $this->ajaxReturn($res);
    }
    /**
     * 学生找回密码 发送短信
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                   organid 机构id
     * @param   [str]                   domain string
     * @return   array();
     * URL:/appstudent/Homepage/sendMobileMsg
     */
    public function sendMobileMsg(){
        $mobile = $this->request->param('mobile');
        $prphone = $this->request->param('prphone');

        /*    $mobile = '18235102743';
            $domain = 'http://test.menke.com';*/
        $class = new AppUserManage;
        $res = $class->sendMsg($mobile, $prphone);
        $this->ajaxReturn($res);
    }
}
