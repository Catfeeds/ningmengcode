<?php
namespace app\student\business;
use app\student\model\Studentinfo;
use Login;
use think\Controller;
use think\Validate;
use think\Cache;
use app\index\business\UserLogin;
class WebLoginManage extends Controller
{
    protected  $logintype = 3;
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义登录类型 学生默认为3

        }
    /**
     * 学生端注册
     * @Author yr
     * @param $mobile
     * @param $password
     * @return array
     *
     */
    public function register($post,$organid){
        $rule = [
            'mobile'  => 'require|length:6,12',
            'organid'   => 'number',
            'prphone' => 'require',
            'code' => 'require',
            'key' => 'require',
        ];
        $msg = [
            'mobile.require' => '手机号必须填写',
            'mobile.length'     => '请输入6-12手机号',
            'organid.number'   => '机构id必须是数字',
            'prphone.require'  => '国家区号必须填写',
            'code.require'        => '手机验证码必须填写',
            'key.require'        => '请输入密钥',
        ];
        $loginobj = new UserLogin;
        $post = $loginobj->rsaDecode($post['data']);
        if(!verifyPassword($post['password'])){
            return return_format($this->str,39021,'请输入正确的密码');
        }
        $validate = new Validate($rule,$msg);
        $result   = $validate->check($post);
        if(true !== $result){
            return return_format('',39125,$validate->getError());
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$post['mobile']);
        if(empty( $cachedata)){
            return return_format('',39122,'验证码已失效,请重新发送短信验证码');
        }
        if(trim($cachedata) !== trim($post['code'])){
            //如果验证码输入错误超限 重新发送短信验证码
            if(!verifyErrorCodeNum($post['mobile'])){
                return return_format('',39126,'验证码错误次数超限,请重新发送短信验证码');
            }
            return return_format('',39127,'验证码不正确');
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($post['mobile'],$organid);
        if($info){
            return return_format('',39128,'该手机号已经注册');
        }
       else {
           $loginobj = new UserLogin;
           $studentmodel = new Studentinfo;
           $encryptpass = $this->createUserMark($post['password']);
           $mix = $encryptpass['mix'];
           $password = $encryptpass['password'];
           //拼装插入信息
           $data = $post;
           $data['password'] = $password;
           $data['mix'] = $mix;
           $data['addtime'] = time();
           $data['organid'] = $organid;
           unset($data['key']);
           unset($data['code']);
           $registerid = $studentmodel->addStudent($data);
           if (empty($registerid)) {
               return return_format('', 39129, '注册失败');
           }
           Cache::rm('mobile' . $post['mobile']);
           $result = $loginobj->internalLogin($this->logintype, $registerid, $post['key']);
           if ($result['code'] == 0) {
               return $result;
           } else {
               return return_format('', 39135, '注册成功,请重新登陆');
           }
       }
    }
    /**
     * 学生端登陆
     * @Author yr
     * @param $mobile
     * @param $password
     * @return array
     *
     */
    public function login ($mobile,$password,$organid)
    {

        //先判断手机号长度
        if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
            return return_format('',32000,'请输入6-12位手机号');
        }else{
            $studentmodel = new Studentinfo;
            $data = $studentmodel ->checkLogin($mobile,$organid);
        //如果长度没问题判断手机号是否存在,或者手机号被删除
        if(!$data || $data['delflag'] == 0){
            return return_format('',32001,'手机号不存在');
        }else{
            //判断用户登录状态，是否禁用
        if($data['status'] == 1){
            return  return_format('',32002,'该手机号已被禁用!请联系管理员');
        }
        $res = $this->checkUserMark($password,$data['mix'],$data['password']);
            //如果用户名存在判断密码是否正确
        if($res == false){
           return  return_format('',32003,'密码错误');
        }else{
            //设置token
            unset($data['password']);
            unset($data['mix']);
            $loginobj = new Login;
            $token = $loginobj->settoken($data['id'],1,$data['organid']);
            $data['token'] = $token;
            return  return_format($data,0,'登录成功');
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
