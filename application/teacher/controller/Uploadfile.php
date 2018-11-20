<?php
namespace app\teacher\controller;
use think\Controller;
use think\Request;
use think\Session;
use login\Authorize;
class Uploadfile extends Authorize
{
    //public $organid;
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
    
        //$this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }
    // protected $teacherid= 1;
    // protected $organid = 1;
    /**
     * 文件上传调用，上传图片等，腾讯云
     *
     * @return \think\Response
     *
     */
    public function Upload()
    {
       header('Access-Control-Allow-Origin: *');
       // 获取表单上传文件

       // $qcloud = new \QcloudManage;
       // $cos = $qcloud->listFolder('courseware/0/');
       // echo '<img src="[图片]http://cat-1254220117.cosbj.myqcloud.com/courseware/0/timg.jpg">';
       // exit();

       $data = Request::instance()->post(false);
       $filetype = isset($data['filetype']) && $data['filetype']?$data['filetype']:1;
       $data['files'] = $_FILES;
       //$data['dstfolder'] = 'courseware/0/cmr';//默认文件夹
       // 平台：官方平台 1，机构平台 2，教师平台 3，学生平台4，app端教师5，app端学生6，
       // 机构id：官方默认 0，
       // 上传文件夹名字：headimg 1,advertisement 2 ,logo 3 ,frontphoto 4,backphoto 5,organphoto 6,
       // recommphoto 7,recordingparts 8
       //$data['allpathnode'] =[1,2,3];
       $allfile = new \Upload;
       $organid = 1;
       //只为上传图片用
       $allfiles = $allfile->getUploadFiles($data,$filetype,$organid);
       //print_r($_FILES);
       $this->ajaxReturn($allfiles);
    }

    /**
     * 只为上传录制件用
     */
    public function Uploadc()
    {
       header('Access-Control-Allow-Origin: *');
       // 获取表单上传文件
       $data = Request::instance()->post(false);
       $data['files'] = $_FILES;
       //$data['dstfolder'] = 'courseware/0/cmr';//默认文件夹
       // 平台：官方平台 1，机构平台 2，教师平台 3，学生平台4，app端教师5，app端学生6，
       // 机构id：官方默认 0，
       // 上传文件夹名字：headimg 1,advertisement 2 ,logo 3 ,frontphoto 4,backphoto 5,organphoto 6,
       // recommphoto 7,recordingparts 8
       //$data['allpathnode'] =[1,2,3];
       $allfile = new \Upload;
       $organid = 1;
       $data['teacherid'] = $this->teacherid;
       $allfiles = $allfile->getUploadFiles($data,3,$organid);
       //print_r($_FILES);
       $this->ajaxReturn($allfiles);
    }
    /**
     * 文件上传调用,只允许上传课件，拓客服务器
     *
     * @return \think\Response
     */
    public function Uploadb()
    {
       header('Access-Control-Allow-Origin: *');
        //fatherid 文件夹id
        // 获取表单上传文件

       $data = Request::instance()->post(false);
       $data['files'] =$_FILES;
       $data['teacherid'] = $this->userInfo['type']==1?$this->teacherid:0;
       //$data['fatherid'] = 1;
       $allfile = new \Upload;
       $organid = 1;
       $allfiles = $allfile->getUploadFiles($data,2,$organid);
       //print_r($_FILES);
       $this->ajaxReturn($allfiles);
    }

    /**
     * 基于base64进行上传Ucloud操作，不进行存储
     * @Author wangwy
     */
    public function Uploadd()
    {
       header('Access-Control-Allow-Origin: *');
       $data = Request::instance()->post(false);
       $allfile = new \Upload;
       $allfiles = $allfile->SaveFormUpload('',$data['img']);
       $this->ajaxReturn($allfiles);
    }


}
