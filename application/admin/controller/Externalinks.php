<?php
/**
 * @ 财务模块控制器 
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\Todocking;


class Externalinks extends Controller
{


	//自定义初始化
	protected function _initialize() {
        parent::_initialize();
	}


    /**
     * [setRoomRecord 课程上完 查询课程视频存储起来]、
     * @author [name] < JCR >
     * @param  [type] $[serial] [教室id]
     * @return [type] [description]
     */
    public function setRoomRecord(){
    	$data = Request::instance()->get(false);
//    	$data['serial'] = 754611620;
		$todocking = new Todocking();
    	$dataReturn = $todocking->setRoomRecord($data);
        $this->ajaxReturn($dataReturn);
    }



	/**
	 * [setDownRoom 课程上完 关闭课程]、
	 * @author [name] < JCR >
	 * @param  [type] $[serial] [教室id]
	 * @return [type] [description]
	 */
	public function setDownRoom(){
		$data = Request::instance()->get(false);
		if(!isset($data['serial'])){
			return return_format('',10108,'教室号不存在');;
		}

		$todocking = new Todocking();
		$dataReturn = $todocking->setDownRoom($data['serial']);
		$this->ajaxReturn($dataReturn);
	}










   	


}
