<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Organaccount extends Model
{
    protected $table = 'nm_organaccount';
    /**
     * [getOrganmsgById 根据机构id获取机构信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function updateTradeflow($amount){

        return Db::table($this->table)
            ->where('organid','eq',1)
            ->setInc('tradeflow',$amount);
    }
    /**
     * [getOrganmsgById 根据机构id获取机构信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function updateUsablemoney($amount){

        return Db::table($this->table)
            ->where('organid','eq',1)
            ->setInc('usablemoney',$amount);
    }
    /**
     * [delFreezeMoney(混合支付冻结余额]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [int]        $userid  [学生id]
     * @param    [decimal]    $price   余额+第三方支付金额
     * @param    [decimal]    $amount  第三方支付金额
     * @return   array
     */
    public function updateFlowOrUsable($where,$price,$amount){
        $res = Db::table($this->table)->where($where)
            ->exp('usablemoney','usablemoney + '.$amount)
            ->exp('tradeflow','tradeflow + '.$price)
            ->update();
        return $res;
    }

}
