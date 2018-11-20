<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 后台 收货地址
 * @ jcr
*/
class Studentaddress extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_studentaddress';//表名称


	/**
	 * 获取收获地址详情
	 * @param $id
	 */
	public function getId($id){
		$info = Db::table($this->table)->where('id',$id)->find();
		if($info){
			$city = new City();
			$addres = $city->getInOrder($info['pid'].','.$info['cityid'].','.$info['areaid']);
			$info['addressStr'] = implode('',$addres).$info['address'];
		}
		return $info;
	}




}
