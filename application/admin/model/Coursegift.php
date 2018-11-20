<?php namespace app\admin\model;
use think\Db;
use think\Model;

class Coursegift extends Model {

	protected $table = 'coursegift';
	protected $pagenum; //每页显示行数

	//自定义初始化
	protected function initialize() {
		parent::initialize();
	}

	/**
	 * [getlist 获取所有的赠品]
	 * @Author JCr
	 * @DateTime 2018-04-19T15:31:56+0800
	 */
	public function getlist($where) {
		$field = 'id,name';
		return Db::name($this->table)->field($field)->where($where)->select();
	}




}
