<?php
namespace app\appteacher\business;
use app\appteacher\model\Organ;
use app\teacher\model\TeacherInfo;
use app\appteacher\model\Ordermanage;
use app\teacher\model\StudentInfo;
use app\teacher\model\StudentPaylog;
use app\teacher\model\Curriculum;
use think\Request;

class OrderDeal{
	/**
	 * [getOrderList 获取订单列表]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:44:06+0800
	 * @param    [string]        $orderstatus[订单状态]  可选
	 * @param    [string]        $datestatus [时间码]  可选
	 * @param    [int]           $pagenum  [分页页码]  可选
	 * @param    [int]           $limit    [每页行数]  可选
	 * @param    [int]           $organid  [机构id]    必选
	 * @return   [array]                   []
	 * 订单状态0已下单，1超时未支付，2已支付，3申请退款，4已退款
	 */
	public function getOrderList($orderstatus,$pagenum,$teacherid,$limit,$timeap){

//		if ($datestatus == 1) {
//			# 当天
//			//当天开始结束时间戳
//		    $beginTime=mktime(0,0,0,date('m'),date('d'),date('Y'));
//		    $endTime=mktime(23,59,59,date('m'),date('d'),date('Y'));
//
//		}elseif ($datestatus == 2) {
//			# 本周
//			//本周开始结束时间戳
//		    $beginTime=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
//            $endTime=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
//            //$datesearch = [$beginweek,$endweek];
//		}elseif($datestatus == 3){
//			#本月
//			 //当月开始结束时间戳
//		    $beginTime=mktime(0,0,0,date('m'),1,date('Y'));
//            $endTime=mktime(23,59,59,date('m'),date('t'),date('Y'));
//		}


		$timearr = time();//当前时间戳

		if($teacherid<1) return return_format([],26001,lang('26001')) ;

		$where = [] ;
		//检索处理
		//!empty($orderstatus) && $where['orderstatus'] = ['like',$orderstatus.'%'] ;
		//分页处理
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		//$where['organid'] = $organid;
		//$where['teacherid'] = $teacherid;
		//订单类别处理 0已下单，1超时未支付，2已支付，3申请退款，4已退款
		//订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
		// if( $orderstatus!='' && in_array($orderstatus, [20,30,40,50] ) ){
		// 	$where['orderstatus'] = $orderstatus;
		// }elseif ($orderstatus == 21) {
		// 	$where['orderstatus'] = '';
		// }
		$where['teacherid'] = $teacherid;
		$where['orderstatus'] = $orderstatus;
        if(!empty($timeap)){
            //将输入的时间转换为时间戳
            $beginTime = strtotime($timeap[0]);
            $endTime   = strtotime($timeap[1]);
            $where['ordertime'] = ['between',[$beginTime,$endTime]];
        }
        //无时间限制，则全部
		$orderobj = new Ordermanage;
		$orderdata = $orderobj->getOrderList($where,$limitstr);
        //根据curriculumid获取课程图片
		$imagecurriobj = new Curriculum;
		$curriidarr = array_column($orderdata,'curriculumid');
        //var_dump($curriidarr);
		$curriimage = $imagecurriobj->getCurriculumImageById($curriidarr);
		//获取教师名称
		$teachobj = new TeacherInfo;
		//id=>name 结构
		$field = 'teachername';
		$name = $teachobj->getTeacherId($teacherid,$field);
		// $teacherarr = array_column($orderdata,'teacherid') ;
		// if(empty($teacherarr)){//如果数据为空，无需在查询
		// 	$namearr = [] ;
		// }else{
		// 	$teachobj = new Teacherinfo;
		// 	//id=>name 结构
		// 	$namearr = $teachobj->getTeachernameByIds($teacherarr);
		// }

		//获取学生名称
		$studentarr = array_column($orderdata,'studentid') ;
		if(empty($studentarr)){
			$userarr = [] ;
		}else{
			$studentobj = new StudentInfo;
			$userarr = $studentobj->getStudentnameById($studentarr);
		}

		//合并数据
		foreach ($orderdata as $key => &$val) {
			$val['teachername'] = isset($name[$val['teacherid']]) ? $namearr[$val['teacherid']] : '' ;
			$val['studentname'] = isset($userarr[$val['studentid']]) ? $userarr[$val['studentid']] : '' ;
			$val['ordertime'] = date('Y-m-d H:i:s',$val['ordertime']) ;
			$val['imageurl'] = isset($curriimage[$val['curriculumid']])?$curriimage[$val['curriculumid']] : '';
			if($val['orderstatus'] == 20){
				$val['orderstatusname'] = '已支付';
			}
		}

		// 统计各个状态的订单的数量
			// $statusarr = [0=>['name'=>'待付款'],10=>['name'=>'已取消'],20=>['name'=>'已支付']] ;
			// $orderstatuslist = $orderobj->orderAnalysis($teacherid,$organid);
			// foreach ($statusarr as $key => &$val) {
			// 	$val['num'] = isset($orderstatuslist[$key]) ? $orderstatuslist[$key] : 0 ;
			// }
		//$allstatus = [0,10,20,30,40,50];
		//统计所有订单数量
		// $sum = 0;
		// foreach ($orderstatuslist as $vl) {
		// 	$sum += $vl;
		// }
		// $statusarr[21]['name']= '全部订单';
		// $statusarr[21]['num']= $sum;
		// var_dump($statusarr) ;exit();
		//获取符合条件的数据的总条数
		$total = $orderobj->getOrderListCount($where);
		$result = [
				 	'data'=>[
						'orderlist'=>$orderdata,
						//'statusnum'=>$statusarr,
					   ],// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=>$limit ,// 每页多少条记录
				 		'pagenum' =>$pagenum ,//当前页码
				 		'total'   => $total // 符合条件总的记录数
				 	]
				] ;
		return return_format($result,0,lang('success')) ;

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
			if(!empty($orderdata) ){
                //根据订单号查询支付时间
                $studentpay = new StudentPaylog;
                $paytime = $studentpay->getStudentpaytime($orderdata['ordernum']);
                $orderdata['paytime'] = date('Y-m-d H:i:s',$paytime['paytime']);
				//获取学生名称
				$studentobj = new StudentInfo;
				$userdata   = $studentobj->getStudentById($orderdata['studentid']);
				//根据curriculumid获取课程图片
				$imagecurriobj = new Curriculum;
				$curriidarr[] = $orderdata['curriculumid'] ;//h存储curriculumid
				$curriimage = $imagecurriobj->getCurriculumImageById($curriidarr);
				//获取教师名称
				$field = 'teachername,nickname' ;
				$teachobj  = new TeacherInfo ;
				$teachdata = $teachobj->getTeacherData($field,$orderdata['teacherid']);
				$orderdata['studentname'] = isset($userdata['nickname']) ? $userdata['nickname'] : '' ;
				$orderdata['teachername'] = isset($teachdata['teachername']) ? $teachdata['teachername'] : '' ;
				$orderdata['nickname'] = isset($teachdata['nickname']) ? $teachdata['nickname'] : '' ;
				$orderdata['ordertime'] = isset($orderdata['ordertime']) ? date('Y-m-d',$orderdata['ordertime']) : '' ;
                $orderdata['imageurl'] = $curriimage[$orderdata['curriculumid']];
				return return_format($orderdata,0,lang('success'));

			}else{
				return return_format([],0,lang('success')) ;
			}
       	}else{
       		return return_format([],26002,lang('26002')) ;
       	}

	}
}


 ?>
