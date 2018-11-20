<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
/**
* 学生交易流水（包括充值和下单第三方支付的）
**/


class Studentpaylog extends Model
{	
	//学生支付流水
	protected $pk    = 'id';
	protected $table = 'nm_studentpaylog';


	//1.统计学生下单中使用非余额的
	//2.统计学生充值中使用非余额的
	//paytype 支付类型1余额支付2微信支付3支付宝支付4银联支付
	//paystatus   类型 1下单 2充值 3退款 4提现
    /**
     * [getAccountDetailInList]
     * @Author zzq
     * @DateTime 2018-05-15     
     * @param data array           [提交的信息]        
     * @return   [array]                   [description]
     */
	public function getAccountDetailInList($data){
		$where = [];
		$where['paytype'] = ['NEQ',1];
		$where['paystatus'] = ['IN',[1,2]];
		$data['orderbys'] = 'a.paytime desc';
		$field = 'b.domain,b.organname,c.nickname,a.paystatus,a.out_trade_no,a.paytime,a.paynum,a.paytype';
        $lists = Db::table($this->table)
                  ->page($data['pagenum'],$data['pernum'])
                  ->alias('a')
                  ->join('nm_organ b','a.organid = b.id','LEFT')
                  ->join('nm_studentinfo c','a.studentid = c.id','LEFT')
                  ->order($data['orderbys'])
                  ->page($data['pagenum'],$data['pernum'])
                  ->where($where)
                  ->field($field)
                  ->select();
        $count = Db::table($this->table)
                  // ->page($data['pagenum'],$data['pernum'])
                  ->alias('a')
                  ->join('nm_organ b','a.organid = b.id','LEFT')
                  ->join('nm_studentinfo c','a.studentid = c.id','LEFT')
                  ->order($data['orderbys'])
                  ->where($where)
                  ->field($field)
                  ->count();
        $arr = [] ;
        foreach($lists as $k => $v){

	    	$arr[$k]['organname'] = $v['organname'];
	    	$arr[$k]['domain'] = $v['domain'];
	    	$arr[$k]['nickname'] = $v['nickname'];
	    	$arr[$k]['paynum'] = $v['paynum'];
	    	$arr[$k]['paytime'] = Date('Y-m-d H:i:s',$v['paytime']);
	    	$arr[$k]['paytype'] = getStuPayLogPayType($v['paytype']);
	    	$arr[$k]['paystatus'] = getStuPayLogPayStatus($v['paystatus']);
	    	$arr[$k]['out_trade_no'] = $v['out_trade_no'];
        }
        $ret = [];
        $ret['lists'] = $arr;
        $ret['count'] = $count;
        $pagenum = ceil($count/$data['pernum']);
        $ret['pagenum'] = $pagenum;        
        $ret['pernum'] = $data['pernum'];        
        return return_format($ret,0,lang('success')) ;		
	}


	//获取学生充值和买课转账的金额的总和
	//这部分就是机构的收入
    /**
     * [getSumInByStu]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param            []   
     * @return   [array]                   [description]
     */
	public function getSumInByStu(){
		$where = [];
		$where['paytype'] = ['NEQ',1];
		$where['paystatus'] = ['IN',[1,2]];

		$data = [];
		try{
	        $totalSum = Db::table($this->table)
	                  ->where($where)
	        		  ->sum('paynum');
	        // var_dump($this->getLastSql());
	        // die;
	        $data['totalSum'] = $totalSum;
	        return $data;	
		}catch(\Exception $e){
			return $data;
		}

	}



	/**
	 *	统计官方的流水数据
	 *	@author wyx
	 *	@param  $starttime string 统计数据的起始时间
	 *
	 *
	 */
	public function getOfficalCashFlow($starttime){
		return Db::table($this->table)
		->field('from_unixtime(paytime,"%Y-%m-%d") datestr,sum(paynum) totalpay,count(studentid) num')
		->where('paystatus','EQ',1)//仅仅获取 下单的数据
		->where('paytime','GT',$starttime)//仅仅获取 下单的数据
		->group('datestr')
		->select();

	}


}