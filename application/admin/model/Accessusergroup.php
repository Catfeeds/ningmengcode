<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Accessusergroup extends Model
{


	protected $pk    = 'id';
	protected $table = 'nm_accessusergroup';


	/**
	 * 获取指定部门详情
	 * @author JCR
	 * @param  $id
	 * @return false|\PDOStatement|string|\think\Collection
	 */
	public function getById($id){
		$field = 'id,name,treepath,status';
		return Db::table($this->table)->where('id',$id)->field($field)->find();
	}


	/**
	 * getList 获取部门列表
	 * @author JCR
	 * @param  $where  筛选条件
	 */
	public function getList($where,$pagenum,$limit){
		$field = 'id,name,status,addtime,treepath';
		return Db::table($this->table)->where($where)->page($pagenum,$limit)->field($field)->select();
	}


	/**
	 * 获取总条数
	 * @author JCR
	 * @param  $where
	 * @return int|string
	 */
	public function getCount($where){
		return Db::table($this->table)->where($where)->count();
	}


	/**
	 * [addEdit 添加编辑分组]
	 * @Author JCR
	 */
	public function addEdit($data){
		if(isset($data['id'])){
			return Db::table($this->table)->where('id','EQ',$data['id'])->update($data);
		}else{
			$data['addtime'] = time();
			return Db::table($this->table)->insert($data);
		}
	}

}	