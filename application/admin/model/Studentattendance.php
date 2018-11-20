<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Studentattendance extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_studentattendance';
	/**
	 * 查看课程课时对应的出勤人数
	 * @author JCR
	 * @param  $lessonsidid int 课时ID
	 * @return []
	 */
	public function getOfficalCashFlow($lessonsidid){
		return Db::table($this->table)->where('lessonsid',$lessonsidid)
									->where('attendancestatus',1)
									->count();
	}


	/**
	 * 获取对应课时的所有学员
	 * @author JCR
	 * @param  $where 筛选条件
	 */
	public function getList($where,$pagenum,$limit){
		$field = 'id,attendancestatus,studentid';
		return Db::table($this->table)->where($where)->page($pagenum,$limit)->field($field)->select();
	}

	/**
	 * 获取对应课时的所有学员
	 * @author JCR
	 * @param  $where 筛选条件
	 */
	public function getAllList($where){
		$field = 'attendancestatus,studentid as id';
		return Db::table($this->table)->where($where)->field($field)->select();
	}

	/**
	 * 获取对应课时的学员总数
	 * @author JCR
	 * @param  $where 筛选条件
	 */
	public function getCount($where){
		return Db::table($this->table)->where($where)->count();
	}




}