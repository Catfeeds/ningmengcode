<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16 0016
 * Time: 21:23
 */
use Gregwar\Captcha\CaptchaBuilder;
use think\Cache;
class Verifyhelper
{
    /**
     * 图形验证码调用方法
     *
     */
    protected $expire  = 600;
    /**
     * [生成验证码]
     * @Author yr
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public  function verify()
    {
        $builder = new CaptchaBuilder();
        //把要输出的图片放入缓冲区
        ob_start();
        //$builder->build('82','44')->output();
        $builder->build('82','44')->output();
        $image_data = ob_get_contents();
        ob_end_clean();
        //生成sessionid 作为验证码的唯一标识
        $session_id = MD5(uniqid());
        Cache::set('verify'.$session_id,$builder->getPhrase(),$this->expire);
        $image_data_base64 = 'data:image/png;base64,'.base64_encode ($image_data);
        $data['codeimg'] = $image_data_base64;
        $data['sessionid'] = $session_id;
        return $data;
    }
    /**
     * 检测验证码是否正确
     * @param $code
     * @return bool
     */
    public  function check($code,$sessionid)
    {
        //获取缓存的验证码信息
        $servercode = Cache::get('verify'.$sessionid);
        if($code != '' && $servercode == $code){
            return true;
        }else{
            return false;
        }

    }
}