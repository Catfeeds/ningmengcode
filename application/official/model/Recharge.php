<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 学生充值表
**/
class Recharge extends Model
{
	//学生支付流水
	protected $pk    = 'id';
	protected $table = 'nm_recharge';

    /**
     * [getChargeDetail]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param $key string           [数据表中的字段]   
     * @param $value string           [数据表中的字段对应的值]   
     * @return   [array]                   [description]
     */
	public function getChargeDetail($key,$value){
		//$key = "a.chargenum";
		//$value = "订单号";

		$field = 'a.price,a.addtime,b.organname,b.domain,c.nickname,a.source,a.paytype,a.price,a.rechargenum';
        $ret = Db::table($this->table)
                  ->alias('a')
                  ->join('nm_organ b','a.organid = b.id','LEFT')
                  ->join('nm_studentinfo c','a.studentid = c.id','LEFT')
                  ->where($key,'=',$value)
                  ->field($field)
                  ->find();
        $data = [] ;
        //
    	$data['price'] = $ret['price'];
    	$data['rechargenum'] = $ret['rechargenum'];
    	$data['addtime'] = Date('Y-m-d H:i:s',$ret['addtime']);
    	$data['organname'] = $ret['organname'];
    	$data['domain'] = $ret['domain'];
    	$data['nickname'] = $ret['nickname'];
    	$data['source'] = getOrderSource($ret['source']);
    	$data['paytype'] = getRechargePayType($ret['paytype']);
    	$data['price'] = $ret['price'];

    	
        return return_format($data,0,lang('success')) ;		
	}
			
}	


