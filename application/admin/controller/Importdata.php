<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use login\Authorize;
class Importdata extends Authorize
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 批量导入数据调用
     *
     * @return \think\Response
     *
     */
    public function Import()
    {
	   header('Access-Control-Allow-Origin: *');
       $data = Request::instance()->post(false);
       $importtype = $data['importtype'];
       $data['files'] = $_FILES;
       $import = new \Import;

       $allfiles = $import->importDatas($data,$importtype);
       $this->ajaxReturn($allfiles);
    }
}
