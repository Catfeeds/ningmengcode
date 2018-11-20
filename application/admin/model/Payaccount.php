<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Payaccount extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_payaccount';
	
	/**
	 * [getPaymsgById 根据机构id获取机构付款方式]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [返回查询结果]
	 */
	public function getPaymsgById(){
		
		$field = 'id,bankname,branchname,cardid,cardholder,wechatmark,accountpayee,namepayee' ;
		return Db::table($this->table)
				->field($field)
				->where('organid','eq',1)
				->find() ;
	}
	/**
	 * [updatePayMsg 更新机构付款方式信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T12:08:01+0800
	 * @param    [array]            $data    [需要更新的数据]
	 * @param    [int]              $organid [机构标识id]
	 * @return   [int]                       [更新结果标记]
	 */
	public function updatePayMsg($data){
		// $validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		//入库
		$return = $this->allowField(true)->save($data,['organid'=>1]);
		return return_format($return,0);
		
	}
	
	

}
