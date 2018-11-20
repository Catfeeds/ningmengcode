<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 机构转账表
**/
class Organaccount extends Model
{

	protected $pk    = 'id';
	protected $table = 'nm_organaccount';

	/**
	 *	获取所有机构 交易总额 ，所有的下单成功的，不包含充值 
	 *
	 */
	public function getOrganTradeFlow(){
		return Db::table($this->table)
		->field('sum(tradeflow) amount')
		->find();
	}

}