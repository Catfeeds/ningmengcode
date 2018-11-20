<?php
/**
*官方后台设置
**/
namespace app\official\Controller;
use app\official\controller\Base;
use think\Session;
use think\Request;
use app\official\business\ConfigManage;
use app\official\model\Organconfig;
use app\teacher\business\UploadFiles;
use login\Authorize;
class Config extends Authorize
{

	public function _initialize(){
		parent::_initialize();
	}
	
	/**
	 * //进行课堂配置
	 * @Author zzq
	 * @param $toonetime   非负int  [1对1时间]
	 * @param $smallclasstime   非负int  [小班时间]
	 * @param $bigclasstime   非负int  [大班时间]
	 * @param $maxclass   非负int  [最大人数]
	 * @param $organid   非负int  [默认为0]
	 * @return array  [返回信息]
	 * 
	 */
	public function setOrganClassConfig(){
		$data = [];
		$toonetime = Request::instance()->post('toonetime');
		$smallclasstime = Request::instance()->post('smallclasstime');
		$bigclasstime = Request::instance()->post('bigclasstime');
		$maxclass = Request::instance()->post('maxclass');
		$minclass = Request::instance()->post('minclass');
		$data['toonetime'] = $toonetime ? $toonetime : 0;
		$data['smallclasstime'] = $smallclasstime ? $smallclasstime : 0;
		$data['bigclasstime'] = $bigclasstime ? $bigclasstime : 0;
		$data['maxclass'] = $maxclass ? $maxclass : 0;
		$data['minclass'] = $minclass ? $minclass : 0;
		$configManage = new ConfigManage();
		$res = $configManage->changeOrganClassConfig($data);
		$this->ajaxReturn($res);
        return $res; 

	}
	
	
	/**
	 * //获取课堂配置
	 * @Author zzq
	 * @param  无参数
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganClassConfig(){
		$organConfig = new Organconfig();
		$res = $organConfig->getOrganClassConfig(0);
		$this->ajaxReturn($res);
        return $res; 		
	}


	
	/**
	 * //添加广告
	 * @Author zzq
	 * @param $remark   string  [描述]
	 * @param $imagepath   string  [路径]
	 * @return array  [返回信息]
	 * 
	 */
	public function addOrganSlideImg(){
		$data = [];
		$remark = Request::instance()->post('remark');
		$imagepath = Request::instance()->post('imagepath');
		$data['remark'] = $remark ? $remark : '';
		$data['imagepath'] = $imagepath ? $imagepath : '';
		$configManage = new ConfigManage();
		$res = $configManage->addOrganSlideImg($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * //获取广告列表
	 * @Author zzq
	 * @param [无参数]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganSlideImgList(){
		$configManage = new ConfigManage();
		$res = $configManage->getOrganSlideImgList();
		$this->ajaxReturn($res);
        return $res; 
	}


	
	/**
	 * //获取单个广告配置
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganSlideImgById(){
		$id = Request::instance()->post('id');
		$configManage = new ConfigManage();
		$res = $configManage->getOrganSlideImgById($id);
		$this->ajaxReturn($res);
        return $res; 		
	}

	/**
	 * //编辑广告
	 * @Author zzq
	 * @param $remark   string  [描述]
	 * @param $imagepath   string  [路径]
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function editOrganSlideImg(){
		$remark = Request::instance()->post('remark');
		$imagepath = Request::instance()->post('imagepath');
		$id = Request::instance()->post('id');
		$data['remark'] = $remark ? $remark : '';
		$data['imagepath'] = $imagepath ? $imagepath : '';
		$data['id'] = $id ? $id : '';
		$configManage = new ConfigManage();
		$res = $configManage->editOrganSlideImg($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * //删除广告
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOrganSlideImg(){
		$id = Request::instance()->post('id');
		$configManage = new ConfigManage();
		$res = $configManage->delOrganSlideImg($id);
		$this->ajaxReturn($res);
        return $res; 
	}



	/**
	 * //添加套餐
	 * @Author zzq
	 * @param $remark   string  [描述]
	 * @param $imagepath   string  [路径]
	 * @return array  [返回信息]
	 * 
	 */
	public function addOrganAuditBill(){
		$data = [];
		$name = Request::instance()->post('name');
		$logo = Request::instance()->post('logo');
		$info = Request::instance()->post('info');
		$indate = Request::instance()->post('indate');
		$price = Request::instance()->post('price');
		$ontrial = Request::instance()->post('ontrial');

		$data['name'] = $name ? $name : '';
		$data['logo'] = $logo ? $logo : '';
		$data['info'] = $info ? $info : '';
		$data['indate'] = $indate ? $indate : '';
		$data['price'] = $price ? $price : '';
		$data['ontrial'] = isset($ontrial) ? $ontrial : '';


		$configManage = new ConfigManage();
		$res = $configManage->addOrganAuditBill($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * //获取套餐列表
	 * @Author zzq
	 * @param [status] [1表示有效的 2表示所有的都返回]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganAuditBillList(){
		$status = Request::instance()->post('status');
		$status = isset($status) ? $status : 2;
		$configManage = new ConfigManage();
		$res = $configManage->getOrganAuditBillList($status,0);
		$this->ajaxReturn($res);
        return $res; 
	}


	
	/**
	 * //获取单个套餐配置
	 * @Author zzq
	 * @param $id   int  [套餐id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganAuditBillById(){
		$id = Request::instance()->post('id');
		$configManage = new ConfigManage();
		$res = $configManage->getOrganAuditBillById($id);
		$this->ajaxReturn($res);
        return $res; 		
	}

	/**
	 * //编辑套餐
	 * @Author zzq
	 * @param $remark   string  [描述]
	 * @param $imagepath   string  [路径]
	 * @param $id   int  [套餐id]
	 * @return array  [返回信息]
	 * 
	 */
	public function editOrganAuditBill(){
		$name = Request::instance()->post('name');
		$logo = Request::instance()->post('logo');
		$info = Request::instance()->post('info');
		$indate = Request::instance()->post('indate');
		$price = Request::instance()->post('price');
		$id = Request::instance()->post('id');
		$ontrial = Request::instance()->post('ontrial');

		$data['name'] = $name ? $name : '';
		$data['logo'] = $logo ? $logo : '';
		$data['info'] = $info ? $info : '';
		$data['indate'] = $indate ? $indate : '';
		$data['price'] = $price ? $price : '';
		$data['id'] = $id ? $id : '';
		$data['ontrial'] = isset($ontrial) ? $ontrial : '';
		
		$configManage = new ConfigManage();
		$res = $configManage->editOrganAuditBill($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * //删除套餐
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOrganAuditBill(){
		$id = Request::instance()->post('id');
		$configManage = new ConfigManage();
		$res = $configManage->delOrganAuditBill($id);
		$this->ajaxReturn($res);
        return $res; 
	} 

	/**
	 * //修改单个套餐状态
	 * @Author zzq
	 * @param $id   int  [套餐id]
	 * @return array  [返回信息]
	 * 
	 */
	public function updateOrganAuditBillStatusById(){
		$id = Request::instance()->post('id');
		$status = Request::instance()->post('status');

		$status = isset($status) ? $status : '';
		$id = isset($id) ? $id : '';

		$data = [];
		$data['status'] = $status;
		$data['id'] = $id;

		$configManage = new ConfigManage();
		$res = $configManage->updateOrganAuditBillStatusById($data);
		$this->ajaxReturn($res);
        return $res; 		
	}  
}	