<?php
namespace app\official\business;
use app\official\model\Organconfig;
use app\official\model\Organslideimg;
use app\official\model\Organauditbill;
class ConfigManage{
	

	
	/**
	 * //进行课堂配置
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function changeOrganClassConfig($data){

		//所有参数大于0并且都是非负整数
		foreach($data as $k => $v){

			if(!$this->isPositiveInteger($v)){

				return return_format('',50098,lang('50098'));
				
			}	
		}
		// die;
		//组装$data;
		$data['regionprefix'] = '';
		$data['roomkey'] = 'crmf32aQ5Qr1MXpC';
		$data['organid'] = 0;

		$organConfig = new Organconfig();
		$res = $organConfig->changeOrganClassConfig($data);
		return $res;
	}


	/**
	 * //获取广告列表
	 * @Author zzq
	 * @param  无参数  []
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganSlideImgList(){
		$slideimgobj = new Organslideimg();
		$res = $slideimgobj->getOrganSlideImgList();
		return $res;
	}

	
	/**
	 * ///添加广告
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function addOrganSlideImg($data){
        $slideimgobj = new Organslideimg();
        //获取机构的轮播图数量
        $ret = $slideimgobj->getOrganSlideImgList(0);
        $result = $ret['data'];
        $count = count($result);
        //轮播图不能超过 5个
        if($count>=5){
        	return return_format('',50103,lang('50103')) ;
        }elseif($count==0){
        	$sortnum = 1 ;
        }else{
        	$sortnum = 1 ;
        	foreach ($result as $val) {
        		$sortnum = $val['sortid'] > $sortnum ? $val['sortid'] : $sortnum ;
        	}
        	$sortnum++ ;
        }
        //添加数据
        $data['sortnum'] = $sortnum;
        $res = $slideimgobj->addOrganSlideImg($data);
        return $res;		
	}

	
	/**
	 * //查看广告详情
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganSlideImgById($id){

		if(!$this->isPositiveInteger($id)){
			return return_format('',50099,lang('50099')) ;
		}
		$slideimgobj = new Organslideimg();
		$res = $slideimgobj->getOrganSlideImgById($id);
		return $res;
	}


	
	/**
	 * //编辑广告
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function editOrganSlideImg($data){
		if(!$this->isPositiveInteger($data['id'])){
			return return_format('',50099,lang('50099')) ;
		}
		$slideimgobj = new Organslideimg();
		$res = $slideimgobj->editOrganSlideImg($data);
		return $res;
	}

	/**
	 * //删除广告
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOrganSlideImg($id){
		if(!$this->isPositiveInteger($id)){
			return return_format('',50099,lang('50099')) ;
		}
		$slideimgobj = new Organslideimg();
		$res = $slideimgobj->delOrganSlideImg($id);
		return $res;
	}




	
	/**
	 * ///添加套餐
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function addOrganAuditBill($data){
		if( empty($data['name']) || empty($data['logo']) || empty($data['info']) || empty($data['indate']) || empty($data['price']) || !isset($data['ontrial']) ){
			return return_format('',50000,lang('50000')) ;
		}
        $obj = new Organauditbill();
        $res = $obj->addOrganAuditBill($data);
        return $res;		
	}

	
	/**
	 * //查看套餐详情
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganAuditBillById($id){

		if(!$this->isPositiveInteger($id)){
			return return_format('',50052,lang('50052')) ;
		}
		$obj = new Organauditbill();
		$res = $obj->getOrganAuditBillById($id);
		return $res;
	}


	
	/**
	 * //编辑套餐
	 * @Author zzq
	 * @param $array   数组  []
	 * @return array  [返回信息]
	 * 
	 */
	public function editOrganAuditBill($data){
		if( empty($data['name']) || empty($data['logo']) || empty($data['info']) || empty($data['indate']) || empty($data['price']) || !isset($data['ontrial']) ){
			return return_format('',50000,lang('50000')) ;
		}
		if(!$this->isPositiveInteger($data['id'])){
			return return_format('',50052,lang('50052')) ;
		}
		$slideimgobj = new Organauditbill();
		$res = $slideimgobj->editOrganAuditBill($data);
		return $res;
	}

	/**
	 * //删除套餐
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOrganAuditBill($id){
		if(!$this->isPositiveInteger($id)){
			return return_format('',50052,lang('50052')) ;
		}
		$slideimgobj = new Organauditbill();
		$res = $slideimgobj->delOrganAuditBill($id);
		return $res;
	}

	/**
	 * //更改套餐状态
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function updateOrganAuditBillStatusById($data){
		if(!$this->isPositiveInteger($data['id'])){
			return return_format('',50052,lang('50052')) ;
		}
		$ret = [0,1];
		if(!in_array($data['status'],$ret)){
			return return_format('',50100,lang('50052')) ;
		}
		$slideimgobj = new Organauditbill();
		$res = $slideimgobj->updateOrganAuditBillStatusById($data);
		return $res;
	}


	/**
	 * //判断是否是非负整数
	 * @Author zzq
	 * @param $value   int  [广告id]
	 * @return bool  [返回信息]
	 * 
	 */
    protected function isPositiveInteger($value)
    {
    	$pattern = "/^\d+$/";
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
        
    }


}