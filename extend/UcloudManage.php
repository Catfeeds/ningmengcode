<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10
 * Time: 15:02
 */
require('include.php');
//use Ucloud\proxy;
date_default_timezone_set('PRC');
class UcloudManage
{


    //config your information
    private $config = [] ;
    // cos  object
    private $cosobj = '' ;
    /**
     *	初始化数据
     *
     */
    public function __construct(){
          $temp = config('ucos.webconfig') ;
          $this->bucket = $temp['bucket'] ;
    }

    /**
     * @param $src 本地文件路径 eg: filename.txt   upload/image.png
     * @param $dst cos 上保存位置及名称 eg: course/2/image.png
     * @param string $bucket
     */
    public function uploadpost($src, $dst,$bucket=''){
        //存储空间名
        if(empty($bucket)) $bucket = $this->bucket;
        //上传至存储空间后的文件名称(请不要和API公私钥混淆)
        $key    = $bucket.'_'.$dst;
        //待上传文件的本地路径
        $file   = $src;
        //该接口适用于web的POST表单上传,本SDK为了完整性故带上该接口demo.
        //服务端上传建议使用分片上传接口,而非POST表单
        list($data, $err) = UCloud_MultipartForm($bucket, $key, $file);
        //list($data, $err) = UCloud_MInit($bucket, $key);
        if ($err) {
            return return_format('',29007,"error: " .$err->ErrMsg ."code: ".$err->Code);
        }else{
            //如果上传成功则调用返回链接地址
            $url = UCloud_MakePublicUrl($bucket, $key);
            return return_format($url,0,lang('sucess'));
        }
    }


}