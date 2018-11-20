<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Accessroleuser extends Model
{


	protected $pk    = 'id';
	protected $table = 'nm_accessroleuser';


	/**
	 * 获取指定部门详情
	 * @author lc
	 * @param  $id
	 * @return array
	 */
	public function getRoleidByCondition($where){
		$field = 'roleid';
		return Db::table($this->table)->where($where)->column($field);
	}


	/**
	 * 根据条件获取指定数据
	 * @author jcr
	 * @param $where
	 * @param $field
	 * @return array
	 */
	public function getRoleByTeacher($where){
		$field = 'uid';
		return Db::table($this->table)->where($where)->column($field);
	}


}	