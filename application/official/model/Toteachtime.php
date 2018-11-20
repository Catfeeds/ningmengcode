<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 课程排布表
**/
class Toteachtime extends Model
{

	protected $pk    = 'id';
	protected $table = 'nm_toteachtime';
	/**
	 *	获取平台 时间段内每天的课程分布
	 *	@author wyx
	 *	@param  $startdate  开始时间
	 *	@param  $enddate    结束时间
	 *
	 */
	public function getCoursePlanByDate($startdate,$enddate){
		return Db::table($this->table)
		->join('nm_scheduling',$this->table.'.schedulingid=nm_scheduling.id','LEFT')
		->field('nm_toteachtime.intime,count(nm_toteachtime.id) num,sum(nm_scheduling.realnum) allrealnum')
		->where('nm_toteachtime.intime','BETWEEN',$startdate.','.$enddate)
        ->where('nm_scheduling.realnum','GT',0)
		->group('nm_toteachtime.intime')->select();

	}
	/**
	 *	获取今天的 每个小时的课程分布
	 *	@author wyx
	 *	@param  $date  获取一天的数据 ，然后根据 timekey 来按时段划分
	 *
	 */
	public function getCoursePlanByDay($date){
		return Db::table($this->table)
		->join('nm_scheduling',$this->table.'.schedulingid=nm_scheduling.id','LEFT')
		->where('nm_toteachtime.intime','EQ',$date)
        ->where('nm_scheduling.realnum','GT',0)
		->field('timekey,nm_scheduling.realnum')
		->select();
	}

}