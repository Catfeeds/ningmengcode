<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Ordermanage extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_ordermanage';
	protected $organid;

	//自定义初始化
    protected function initialize(){
        $this->organid = 1;
        parent::initialize();
    }


	/**
	 * [getStudentOrder 根据学生id和机构id来获取订单列表]
	 * @Author wyx
	 * @DateTime 2018-04-20T14:28:00+0800
	 * @param    [int]     $organid   [description]
	 * @param    [int]     $studentid [description]
	 * @return   [array]              [学生的订单已经成功付款过]
	 */
	public function getStudentOrder($studentid){
		
		$field = 'om.id,om.coursename,om.classname,om.amount,om.coursetype,sd.imageurl,ti.nickname' ;
		return Db::table($this->table)->alias('om')
            ->join('nm_scheduling sd','om.schedulingid=sd.id')
            ->join('nm_teacherinfo ti', 'om.teacherid=ti.teacherid', 'left')
            ->field($field)
            ->where('om.studentid',$studentid)
            ->where('om.orderstatus','GT',10)
            ->select() ;
	}
	/**
	 * [getOrderList 获取订单列表 定时任务专用]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @return   [array]        [返回订单列表数组]
	 */
	public function getOrderList($where,$limitstr){
		return Db::table($this->table)->where($where)->field('ordernum,curriculumid,orderstatus,ordertime,studentid,paytype,amount,coursename,teacherid')->limit($limitstr)->order('teacherid','asc')->select();
	}


	/**
	 * 获取对应班级订
	 * @Author JCR
	 * @DateTime 2018-10-23
	 */
	public function getWhereList($where,$field){
		return Db::table($this->table)->where($where)->field($field)->select();
	}




	/**
	 * [getOrderListCount 获取订单列表总行数]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @return   [array]        [返回订单列表数组]
	 */
	public function getOrderListCount($where){
		return Db::table($this->table)->where($where)->count();
	}

	/**
	 * [getOrderAccountList 获取财务订单列表]
	 * @param  [type] $data     [条件源]
	 * @param  [type] $pagenum  [第几页]
	 * @param  [type] $limit    [一页几条]
	 * @return [type]           [description]
	 */
	public function getOrderAccountList($where,$pagenum,$limit){
		$field = 'o.id,o.ordernum,o.orderstatus,o.ordertime,o.studentid,o.classname,o.paytype,o.amount,o.coursename,o.teacherid,o.coursename,l.paytime,s.nickname as studentname,s.prphone,s.mobile,addressid';
		$list = Db::table($this->table)
					->alias('o')
					->join('nm_studentinfo s','o.studentid = s.id','LEFT')
			        ->join('nm_studentpaylog l','o.ordernum = l.out_trade_no','LEFT')
					->where($where)
					->field($field)->page($pagenum,$limit)
					->select();
		return $list;
	}


	/**
	 * [getOrderAccountList 获取财务订单列表]
	 * @param  [type] $data     [条件源]
	 * @param  [type] $pagenum  [第几页]
	 * @param  [type] $limit    [一页几条]
	 * @return [type]           [description]
	 */
	public function getAccountsList($where,$orwhere,$pagenum,$limit){
		$field = 'o.id,o.ordernum,o.orderstatus,o.ordertime,o.studentid,o.classname,o.paytype,o.amount,o.coursename,o.teacherid,o.coursename,o.paytime,s.nickname as studentname,s.prphone,s.mobile,addressid';
		$list = Db::table($this->table)
			->alias('o')
			->join('nm_studentinfo s','o.studentid = s.id','LEFT')
			->where($where);
		if($orwhere){
			$orwhere = implode(',',$orwhere);
			$list->whereor(function($query) use($orwhere){
				$query->where('o.id','in',$orwhere);
			});
		}
		$list = $list->field($field)->page($pagenum,$limit)
				->select();
		return $list;
	}


	/**
	 * [getOrderCounts 根据条件查询总行数]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function getOrderWhereCount($where){
		return Db::table($this->table)
			->alias('o')
			->join('nm_studentinfo s','o.studentid = s.id','LEFT')
			->join('nm_studentpaylog l','o.ordernum = l.out_trade_no','LEFT')
			->where($where)
			->count();
	}


	/**
	 * [getOrderAccountList 获取对应的待结单总额]
	 * @param  [type] $data     [条件源]
	 * @param  [type] $pagenum  [第几页]
	 * @param  [type] $limit    [一页几条]
	 * @return [type]           [description]
	 */
	public function getOrderMoney($where){
		return Db::table($this->table)->where($where)->sum('amount');
	}


	/**
	 * [getOrderCounts 根据条件查询总行数]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function getOrderCounts($where){
		return Db::table($this->table)->where($where)->count();
	}


	/**
	 * [getId 获取订单详情]
	 * @Author jcr
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @param    [int]     $schedulingid   [开课表id]
	 * @return   [array]        [购买次数]
	 */
	public function getId($where,$field){
		return Db::table($this->table)->where($where)->field($field)->find();
	}

	/**
	 * [getPayOrderCount 获取课程已购买次数]
	 * @Author jcr
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @param    [int]     $schedulingid   [开课表id]
	 * @return   [array]        [购买次数]
	 */
	public function getSchedulingIdCount($schedulingid){
		return Db::table($this->table)
					->where('schedulingid','eq',$schedulingid)
					->where('orderstatus','in','20,30,50')
					->count();
	}




	/**
	 * [getPayOrderCount 获取开课课程下单次数]
	 * @Author jcr
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @param    [int]     $curriculumid   [description]
	 * @return   [array]        [购买次数]
	 */
	public function getPayOrderCount($curriculumid){
		return Db::table($this->table)
					->where('curriculumid','eq',$curriculumid)
					->where('orderstatus','egt',20)
					->count();

	}


	/**
	 * [getPayOrderCount 查询对应的班多少人参加了]
	 * @Author jcr
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @param    [int]     $curriculumid   [description]
	 * @return   [array]        [购买次数]
	 */
	public function getPaySchedulingCount($schedulingid){
		return Db::table($this->table)
					->where('schedulingid','eq',$schedulingid)
					->where('orderstatus','in','20,30,50')
					->count();

	}


	/**
	 * [getOrderDetail 获取订单详情]
	 * @Author wyx
	 * @DateTime 2018-04-21T10:48:46+0800
	 * @param    [int]         $orderid [订单id]
	 * @param    [int]         $organid [机构id]
	 * @return   [array]                [返回查询结果]
	 */
	public function getOrderDetail($orderid){
		$field = 'ordernum,orderstatus,ordertime,studentid,balance,ordersource,paytype,originprice,discount,amount,coursename,classname,teacherid,schedulingid,paytime,addressid' ;
		return Db::table($this->table)->field($field)
		->where('id',$orderid)
		->find() ;
	}

	
	/**
	 * [orderAnalysis 获取订单各个状态的数量]
	 * @Author wyx
	 * @DateTime 2018-04-21T15:07:31+0800
	 * @param    [int]          $organid [机构标记]
	 * @return   [array]                  [description]
	 */
	public function orderAnalysis(){
		return Db::table($this->table)
		->group('orderstatus')
		->column('orderstatus,count(orderstatus) num') ;
	}

	/**
	 * [orderSave 订单编辑]
	 * @param  [type] $where [订单编辑条件]
	 * @param  [type] $data  [订单修改字段]
	 * @return [type]        [description]
	 */
	public function orderSave($where,$data){
		return DB::table($this->table)->where($where)->update($data);
	}
	

}
