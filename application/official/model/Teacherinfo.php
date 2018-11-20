<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 机构转账表
**/
class Teacherinfo extends Model
{

	protected $pk    = 'id';
	protected $table = 'nm_teacherinfo';
	/**
	 *	获取平台 的教师总数
	 *	@author wyx
	 */
	public function getTeacherAllAccount(){
		return Db::table($this->table)->where('delflag','EQ',1)
		->count();
	}
	
	/**
	 *	获取平台 的教师总数
	 *	@author zzq
	 */
	public function updateTeacherInfo($data){
		return Db::table($this->table)->where('organid', $data['organid'])->where('mobile', $data['mobile'])->update(['accountstatus' => '0']);

	}
}	
