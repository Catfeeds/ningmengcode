<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 课程标签Model
 * @ jcr
 */
class Coursepackageorder extends Model {

	protected $table = 'coursepackageorder';

	//自定义初始化
	protected function initialize() {
		parent::initialize();
	}

	/**
	 * getId 根据课程标签id 查询课程标签详情
	 * @author jcr
	 * @param  $id		课程标签id
	 * @param  $field	查询内容 默认不传全部
	 * @return array();
	 */
	public function getId($where, $field) {
		return Db::name($this->table)->where($where)->field($field)->find();
	}


}
