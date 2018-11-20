<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 机构转账表
**/
class Studentinfo extends Model
{

	protected $pk    = 'id';
	protected $table = 'nm_studentinfo';
	/**
	 *	获取平台 的学生总数
	 *
	 */
	public function getStudentAllAccount(){
		return Db::table($this->table)
		->count();
	}
	/**
	 *	获取当月截至到今天的 每天的注册学生数
	 *	@author wyx
	 *	官方数据统计使用
	 *
	 */
	public function getAllMonthData($monthstart){
		return Db::table($this->table)
		->where('addtime','GT',$monthstart)
		->field('from_unixtime(addtime,"%Y-%m-%d") formatdate,count(id) num')
		->group('formatdate')
		->select();
	}

    /**
     * [changeUserStatus 更改学生状态]
     * @Author wyx
     * @DateTime 2018-04-20T20:53:36+0800
     * @param    [int]         $userid    [需要更新的学生id]
     * @param    [int]         $flag      [机构标记id]
     * @param    [int]         $organid   [机构标记id]
     * @return   [array]                
     */
    public function changeUserStatus($userid,$flag,$organid){
        $where = ['id'=>$userid,'organid'=>$organid] ;
        $data  = ['status'=>$flag] ;
        Db::table($this->table)->where($where)->update($data) ;
    }

}