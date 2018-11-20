<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 机构购买套餐表
 * @ wyx
 */
class Organauditbill extends Model {

	protected $pk    = 'id';
	protected $table = 'nm_organauditbill';
	/**
	 * getId 根据分类id 查询分类详情
	 * @author wyx
	 * @param $id      分类id
	 * @param $field 查询内容 默认不传全部
	 * @return array
	 */
	public function getMealInfoById($id) {
		return Db::table($this->table)->field('name,info,indate,price,ontrial')->where(['id'=>$id,'status'=>1])->find();
	}

	

}
