<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 调班申请记录表
 * @ jcr
*/
class Applylessonsrecord extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_applylessonsrecord';//表名称


	/**
	 * 获取调课调班所有的纪录
	 * @param $where
	 * @param $list
	 */
	public function getLessonList($id,$field){
		$list = Db::table($this->table)->where('status',1)
				->where(function ($query) use ($id){
					$query->whereor('oldlessonsid',$id)->whereor('newlessonsid',$id);
				})->field($field)->select();
		return $list;
	}


}
