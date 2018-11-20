<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 充值表Model
 * @ yr
*/
class Recharge extends Model{
    protected $table = 'nm_recharge';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [updateRechargeStatus 修改充值表状态]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @param id int 订单id
     * @return   array
     */
    public function updateRechargeStatus($rechargenum,$paystatus){
        $where = ['rechargenum'=>$rechargenum];
        $data['paystatus'] = $paystatus;
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [insert 插入充值表]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    $data
     * @return   array
     */
    public function insert($data){
        $id = Db::table($this->table)->insert($data);
        return $id;
    }
    /**
     * [getRechargeInfo 根据充值订单号查询充值信息]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    $data
     * @return   array
     */
    public function getRechargeByOrdernum($rechargenum){
        $res  = Db::table($this->table)
            ->field('studentid,price,paytype,paystatus')
            ->where('rechargenum','eq',$rechargenum)
            ->find();
        return $res;
    }
}







