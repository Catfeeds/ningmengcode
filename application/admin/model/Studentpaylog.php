<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Studentpaylog extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_studentpaylog';

	/**
	 *	机构的流水数据
	 *	@author wyx
	 *	@param  $starttime string 统计数据的起始时间
	 *	@param  $organid  int  机构标识id
	 */
	public function getOfficalCashFlow($starttime){
		return Db::table($this->table)
		->field('from_unixtime(paytime,"%Y-%m-%d") datestr,sum(paynum) totalpay,count(studentid) num')
		->where('paystatus','EQ',1)//仅仅获取 下单的数据
		->where('paytime','GT',$starttime)//仅仅获取 下单的数据
		->group('datestr')
		->select();
	}



	/**
	 *	机构的流水数据
	 *	@author JCR
	 *	@param  $starttime  string  统计数据的起始时间
	 *	@param  $organid  int  机构标识id
	 */
	public function getCashFlow($starttime){
		return Db::table($this->table)
			->field('from_unixtime(paytime,"%Y-%m-%d") datestr,paynum,paystatus,out_trade_no')
			->where('paystatus','neq',2)//仅仅获取 下单的数据
			->where('paytime','GT',$starttime)//仅仅获取 下单的数据
			->select();
	}


}