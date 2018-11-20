<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\admin\business;
use app\admin\model\Ordermanage;
use app\admin\model\Studentinfo;
use app\admin\model\Teacherinfo;
class OrderDealwith{
	/**
	 * [getOrderList 获取订单列表]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:44:06+0800
	 * @param    [string]        $ordertype[订单编号]  可选
	 * @param    [string]        $ordernum [订单编号]  可选
	 * @param    [int]           $pagenum  [分页页码]  可选
	 * @param    [int]           $limit    [每页行数]  可选
	 * @return   [array]                   []
	 * 订单状态0已下单，1超时未支付，2已支付，3申请退款，4已退款
	 */
	public function getOrderList($ordertype,$ordernum,$pagenum,$limit){

		$where = [] ;
		//检索处理
		!empty($ordernum) && $where['ordernum'] = ['like',$ordernum.'%'] ;
		//分页处理
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		//订单类别处理 0已下单，1超时未支付，2已支付，3申请退款，4已退款
		//订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
		if( $ordertype!='' && in_array($ordertype, [0,10,20,30,40,50] ) ){
			$where['orderstatus'] = $ordertype;
		}

		$orderobj = new Ordermanage;
		$orderdata = $orderobj->getOrderList($where,$limitstr);
		
		//获取教师名称
		$teacherarr = array_column($orderdata,'teacherid') ;
		if(empty($teacherarr)){//如果数据为空，无需在查询
			$namearr = [] ;
		}else{
			$teachobj = new Teacherinfo;
			//id=>name 结构
			$namearr = $teachobj->getTeachernameByIds($teacherarr);
		}

		//获取学生名称
		$studentarr = array_column($orderdata,'studentid') ;
		if(empty($studentarr)){
			$userarr = [] ;
		}else{
			$studentobj = new Studentinfo;
			$userarr = $studentobj->getStudentnameByIds($studentarr);
		}
		//合并数据
		foreach ($orderdata as $key => &$val) {
			$val['teachername'] = isset($namearr[$val['teacherid']]) ? $namearr[$val['teacherid']] : '' ;
			$val['studentname'] = isset($userarr[$val['studentid']]) ? $userarr[$val['studentid']] : '' ;
			$val['ordertime'] = date('Y-m-d H:i:s',$val['ordertime']) ;
		}
		// 统计各个状态的订单的数量
		$statusarr = [0=>['name'=>'待付款'],10=>['name'=>'已取消'],20=>['name'=>'已支付']] ;
		$orderstatus = $orderobj->orderAnalysis();
		foreach ($statusarr as $key => &$val) {
			$val['num'] = isset($orderstatus[$key]) ? $orderstatus[$key] : 0 ; 
		}
		// var_dump($statusarr) ;exit();
		//获取符合条件的数据的总条数
		$total = $orderobj->getOrderListCount($where);
		$result = [
				 	'data'=>[
						'orderlist'=>$orderdata,
						'statusnum'=>$statusarr,
					   ],// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=>$limit ,// 每页多少条记录
				 		'pagenum' =>$pagenum ,//当前页码
				 		'total'   => $total // 符合条件总的记录数
				 	]
				] ;
		return return_format($result,0,'OK') ;

	}
	/**
	 * [exportOrder 导出所有订单] 第一版暂时不做
	 * @Author wyx
	 * @DateTime 2018-04-21T10:05:10+0800
	 * @return   [type]            [description]
	 */
	public function exportOrder(){
		
	}
	/**
	 * [orderDetail 获取订单详情]
	 * @Author wyx
	 * @DateTime 2018-04-21T10:07:56+0800
	 * @param    [int]         $orderid [订单id]
	 * @param    [int]         $organid [机构id]
	 * @return   [array]                [返回查询结果]
	 */
	public function orderDetail($orderid){
		
       	if( $orderid>0 ){
			$orderobj = new Ordermanage ;
			$orderdata = $orderobj->getOrderDetail($orderid);
			if( !empty($orderdata) ){
				//获取学生名称
				$studentobj = new Studentinfo;
				$userdata   = $studentobj->getStudentnameById($orderdata['studentid']);
				//获取教师名称
				$field = 'teachername' ;
				$teachobj  = new Teacherinfo ;
				$teachdata = $teachobj->getTeacherData($field,$orderdata['teacherid']);
				$orderdata['studentname'] = isset($userdata['username']) ? $userdata['username'] : '' ;
				$orderdata['teachername'] = isset($teachdata['teachername']) ? $teachdata['teachername'] : '' ;
				$orderdata['ordertime'] = isset($orderdata['ordertime']) ? date('Y-m-d',$orderdata['ordertime']) : '' ;

				return return_format($orderdata,0);

			}else{
				return return_format([],40001) ;
			}
       	}else{
       		return return_format([],40002) ;
       	}
        
	}
	
}



?>