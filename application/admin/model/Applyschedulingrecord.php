<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 调班申请记录表
 * @ jcr
*/
class Applyschedulingrecord extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_applyschedulingrecord';//表名称


	/**
	 * @param $where	筛选条件
	 * @param $fiele	查询字段
	 * @param $limit	第几页
	 * @param $pagenum	一页几条
	 * @return false|\PDOStatement|string|\think\Collection
	 */
	public function getList($where,$fiele,$limit,$pagenum){
		return Db::table($this->table)->where($where)->field($fiele)->page($limit,$pagenum)->select();
	}


	/**
	 * 获取调出去 调进来的数据
	 * @param $where
	 */
	public function getOrderList($where,$left,$field){
		$list = Db::table($this->table)->alias('a')
							->join('nm_ordermanage o',$left)
							->where($where)
							->field($field)
							->limit(100)
							->select();
		return $list;

	}


	/**
	 * 获取调出去 调进来的数据
	 * @param $where
	 */
	public function getOrderListCount($where,$left,$field){
		$list = Db::table($this->table)->alias('a')
			->join('nm_ordermanage o',$left)
			->where($where)
			->field($field)
			->count();
		return $list;
	}


	/**
	 * 获取调课调班所有的纪录
	 * @param $where
	 * @param $list
	 */
	public function getScheduList($id,$field){
		$list = Db::table($this->table)->where('status',1)
				->where(function ($query) use ($id){
					$query->whereor('oldschedulingid',$id)->whereor('newschedulingid',$id);
				})->field($field)->select();
		return $list;
	}


}
