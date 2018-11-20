<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use app\teacher\business\Classesbegin;
use app\teacher\business\CurriculumModule;
use login\Authorize;
//对教师个人的和机构提供的课件进行操作
class Resources extends Authorize
//class Resources extends Controller
{
    public $teacherid =1;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $this->teacherid = $this->userInfo['info']['uid'];
    }
      /**
     * [fileAdd 添加文件夹]
     * @return [type] [description]
     * URL:/teacher/Resources/fileAdd
     */
    public function fileAdd(){
        $data = Request::instance()->post(false);
        //$data['showname'] = '文件夹1';
        $data['teacherid'] = $this->teacherid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->addFiles($data);
        $this->ajaxReturn($dataReturn);
    }

    /**
     * [getFileList 文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     *URL:/teacher/Resources/getFileList
     */
    public function getFileList(){
        $data = Request::instance()->post(false);
        //$data['showname'] = '文件夹1';
        //$data['fatherid']=isset($data['fatherid'])?$data['fatherid']:0;
        $data['pagenum']  = isset($data['pagenum'])?$data['pagenum']:1;
        $data['teacherid'] = $this->teacherid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->getFileList($data);
        $this->ajaxReturn($dataReturn);
    }


    /**
     * [deleteFile 删除课件]
     * @param  $fileid [素材id]
     * @return [type] [description]
     * URL:/teacher/Resources/deleteFile
     */
    public function deleteFile(){
        $data = Request::instance()->post(false);
        //$data['fileid'] = 1;
         $data['delflag'] = 0;
         $data['teacherid'] = $this->teacherid;
        $classesbegin = new Classesbegin();
        $dataReturn = $classesbegin->deleteFile($data);
        $this->ajaxReturn($dataReturn);
    }


}
