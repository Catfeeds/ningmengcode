<?php
namespace app\microsite\business;
use app\student\model\Studentinfo;
use Login;
use think\Controller;
use think\Validate;
use think\Cache;
use app\index\business\UserLogin;
use wxpay\Wxpay;

class MicroLoginManage extends Controller
{
    protected  $logintype = 3;
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        }
    /**
     * 学生端注册
     * @Author yr
     * @param $mobile
     * @param $password
     * @return array
     *
     */
    public function register($post){
        $rule = [
            'mobile'  => 'require|length:6,15',
            'prphone' => 'require',
            'code' => 'require',
            'key' => 'require',
        ];
        $msg = [
            'mobile.require' => lang('39001'),
            'mobile.length'     => lang('39002'),
            'prphone.require'  => lang('39004'),
            'code.require'        => lang('39005'),
            'key.require'        => lang('39006'),
        ];
        $loginobj = new UserLogin;
        $post = $loginobj->rsaDecode($post['data']);
        if(!verifyPassword($post['password'])){
            return return_format('',39000,lang('39000'));
        }
        $validate = new Validate($rule,$msg);
        $result   = $validate->check($post);
        if(true !== $result){
            return return_format('',39010,$validate->getError());
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$post['mobile']);
        if(empty( $cachedata)){
            return return_format('',39007,lang('39007'));
        }
        if(trim($cachedata) !== trim($post['code'])){
            //如果验证码输入错误超限 重新发送短信验证码
            if(!verifyErrorCodeNum($post['mobile'])){
                return return_format('',39008,lang('39008'));
            }
            return return_format('',39009,lang('39009'));
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($post['mobile']);
        $studentmodel = new Studentinfo;
        if($info){
            return return_format('',39011,lang('39011'));
        }
       else{
            $encryptpass =$this->createUserMark($post['password']);
            $mix = $encryptpass['mix'];
            $password = $encryptpass['password'];
            //拼装插入信息
            $data = $post;
            $data['password'] = $password;
            $data['mix'] = $mix;
            $data['addtime'] = time();
            unset($data['key']);
            unset($data['code']);
            $registerid = $studentmodel->addStudent($data);
           if (empty($registerid)) {
               return return_format('', 39012, lang('39012'));
           }
           Cache::rm('mobile' . $post['mobile']);
           $result = $loginobj->internalLogin($this->logintype, $registerid, $post['key']);
           if ($result['code'] == 0) {
               return $result;
           } else {
               return return_format('', 39013, lang('39013'));
           }
        }
    }
    /**
     * 获取openid
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              code  授权code
     * @return   array();
     * URL:/microsite/Home/getOpenid
     */
    public function getOpenid($code,$mobile)
    {

        if(!is_weixin()){
            return return_format('',-3,'请在微信浏览器打开连接');
        }
        $wxpayobj = new Wxpay();
        $openid = Cache::get($mobile.'-'.'openid');
        if(empty($code)){//如果code为空
            if(empty($openid)){//并且openid为空
                return return_format('',-1,'参数code错误');
            }
        }else{
            $openid = $wxpayobj->getOpenid($code);
            //openid存到cache里
        }
        if(empty($openid)){
            return return_format('',-2,'未获取到openid,请重新授权');
        }
        Cache::set($mobile.'-'.'openid',$openid,864000);
        $data['openid'] = $openid;
        return return_format($data,0,'操作成功');
    }
    /**
     * 学生端登陆
     * @Author yr
     * @param $mobile
     * @param $password
     * @return array
     *
     */
    public function login ($mobile,$password)
    {

        //先判断手机号长度
        if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
            return return_format('',39014,lang('39014'));
        }else{
            $studentmodel = new Studentinfo;
            $data = $studentmodel ->checkLogin($mobile);
        //如果长度没问题判断手机号是否存在,或者手机号被删除
        if(!$data || $data['delflag'] == 0){
            return return_format('',39015,lang('39015'));
        }else{
            //判断用户登录状态，是否禁用
        if($data['status'] == 1){
            return  return_format('',39016,lang('39016'));
        }
        $res = $this->checkUserMark($password,$data['mix'],$data['password']);
            //如果用户名存在判断密码是否正确
        if($res == false){
           return  return_format('',39017,lang('39017'));
        }else{
            //设置token
            unset($data['password']);
            unset($data['mix']);
            $loginobj = new Login;
            $token = $loginobj->settoken($data['id'],1);
            $data['token'] = $token;
            return  return_format($data,0,lang('success'));
        }

        }

    }

}
    /**
     * [checkUserMark description]
     * @Author wyx
     * @DateTime 2018-04-27T16:35:02+0800
     * @param    [string]                 $pass [用户提交密码]
     * @param    [string]                 $mix  [description]
     * @param    [type]                   $sign [description]
     * @return   [bool]                         [true 代表成功，false 代表失败]
     */
    private function checkUserMark($pass,$mix,$sign){
        $md5str = md5(md5($pass).$mix);

        for ($i=0; $i < 5; $i++) {
            $md5str = md5($md5str) ;
        }
        // var_dump($sign);
        // var_dump($md5str);exit();
        if($sign==$md5str){
            return true ;
        }else{
            return false ;
        }

    }
    /**
     * 给用户生成 密码 和 mix
     * [createUserMark 生成用户的机密字符存储在数据库，当用户登陆时比对]
     * 创建用户时调用
     * @Author wyx
     * @DateTime 2018-04-27T16:22:58+0800
     * @param    [string]            $pass    [密码]
     * @return   [type]                   [description]
     */
    public function createUserMark($pass){
        $mix = $this->getRandString(16) ;
        $md5str = md5(md5($pass).$mix);

        for ($i=0; $i < 5; $i++) {
            $md5str = md5($md5str) ;
        }

        return ['mix'=>$mix,'password'=>$md5str] ;

    }
    /**
     * [getRandString 生成随机字符串]
     * @Author wyx
     * @DateTime 2018-04-27T14:53:16+0800
     * @param    [int]                      [设置需要的字符串的长度默认为8]
     * @return   [string]                   [description]
     */
    public function getRandString($length=8){
        $numstr    = '1234567890' ;
        $originstr = 'abcdefghijklmnopqrstuvwxyz' ;
        $origin = str_repeat($numstr,6).$originstr.strtoupper($originstr) ;

        return substr(str_shuffle($origin), -$length);

    }
}
