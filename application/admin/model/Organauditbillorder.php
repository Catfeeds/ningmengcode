<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 机构购买套餐表
 * @ wyx
 */
class Organauditbillorder extends Model {

	protected $pk    = 'id';
	protected $table = 'nm_organauditbillorder';
	/**
	 * 创建 套餐订单 
	 * @param $id 分类id
	 * @param $field 查询内容 默认不传全部
	 * @return array();
	 */
	public function createOrder($data){
		return Db::table($this->table)->insertGetId($data);
	}
	/**
	 *	查看订单是否存在或者已经被处理
	 *	@param $out_trade_no  下单时在本地服务器生成的订单号
	 *	@return  array
	 *	
	 */
	public function checkOrderStatus( $out_trade_no ){
		return Db::table($this->table)->field('paystatus,billid')->where(['out_trade_no'=>$out_trade_no])->find();
	}
	/**
	 *	根据回调更新 订单信息
	 *	@param  $updatedata  用回调数据 填充需要更新的字段
	 *	@param  $out_trade_no  下单时在本地服务器生成的订单号
	 *	@param  $mealid  下单时在本地服务器生成的订单号
     *  @return boolean
	 */
	public function updateOrder($updatedata,$out_trade_no,$mealid){
        Db::startTrans();
        try{
            $update = Db::table($this->table)->where(['out_trade_no'=>$out_trade_no])->update($updatedata);
            $orderinfo = $this->getOrderDuring($out_trade_no);
            //增加会员时长
            $organobj = new Organ;// 增加会员时长
            $uptime = $organobj->upVipTime($orderinfo,$mealid);// 增加会员时长
            if($update>0 && $uptime>0){
                Db::commit();
                return true ;
            }else{
                Db::rollback();
                return false ;
            }
        }catch(\Exception $e){
            var_dump($e->getMessage());
            Db::rollback();
            return false ;
        }
	}
	/**
	 *	获取购买套餐的 时长
     *  @author wyx
	 *	@param  $out_trade_no  下单时在本地服务器生成的订单号
	 *  @return array
	 */
	public function getOrderDuring($out_trade_no){
		return Db::table($this->table)->field('during,organid')->where(['out_trade_no'=>$out_trade_no])->find();
	}

	

}
