<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Teachertime extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_teachertime';
	/**
	 * [findWeekMark 获取一对一时间掩码]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   [机构id]
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
	public function findWeekMark($organid,$teacherid){
		$where  = [
					'teacherid' => $teacherid
				] ;
		$field = 'id,week,mark' ;
		return Db::table($this->table)->field($field)->where($where)->select() ;
	}


	/**
	 * [findWeekCount 查看对应的老师设置的可预约时间个数]
	 * @param $organid
	 * @param $teacherid
	 * @return int|string
	 */
	public function findWeekCount($teacherid){
		$where  = [
			'teacherid' => $teacherid
		] ;
		return Db::table($this->table)->where($where)->count() ;
	}



	/**
	 * [updateBindLabel 获取教师已经绑定的标签]
	 * @Author wyx
	 * @DateTime 2018-05-17T21:35:06+0800
	 * @param    [int]  $week [需要给老师设定的时间数组]
	 * @param    [int]  $teachid [给那个老师绑定]
	 * @param    [int]  $organid [老师所属的机构]
	 * @return   [type]   [description]
	 */
	public function updateWeekIdle($week,$teachid){
		Db::startTrans();
		try{
			Db::table($this->table)
				->where('teacherid','EQ',$teachid)
				->delete();

			// 处理绑定数据
			$data = [] ;
			foreach ($week as $value) {
				$data[] = [
						'teacherid' => $teachid,
						'week' => $value['weekday'],
						'mark' => $value['flag']
					] ;
			}
			// 如果不为空 将数据入库
			Db::table($this->table)->insertAll($data);
			// 提交事务
			Db::commit();
			return return_format('',0) ;
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return return_format('',40095) ;
		}
	}


	/**
	 * [addAll copy老师可预约时间]
	 * @param $data
	 * @return int
	 */
	public function addAll($data){
		return Db::table($this->table)->insertAll($data);
	}

	

}
