<?php
/**
 * 账单模块业务逻辑层
 */
namespace app\admin\business;
use app\admin\controller\Student;
use app\admin\model\Scheduling;
use app\admin\model\Studentinfo;
use app\admin\model\Ordermanage;
use app\admin\model\Teacherinfo;
use app\admin\model\Organpaylog;
use app\admin\model\Organaccount;
use app\admin\model\Toteachtime;
use app\admin\model\Lessons;
use app\admin\model\Withdraw;
use app\admin\model\Studentattendance;
use app\admin\model\Studentaddress;
use think\Validate;
use think\Db;


class AccountBill{

	/**
	 * [getAccount 账户明细列表]
	 * @param  [type] $data  [提交参数数据源]
	 * @param  [type] $limit [一页几条]
	 * @return [type]        [description]
	 */
	public function getAccount($data,$limit){

		$data = where_filter($data,['studentname','pagenum','coursename','intime']);

		// 查询待结算 数据
		$order = new Ordermanage();
		$where = ['o.orderstatus'=>array('in','20,30,50')];
		// 学生名称
		isset($data['studentname']) && $where['s.nickname'] = ['like','%'.$data['studentname'].'%'];
		// 课程名称
		isset($data['coursename']) && $where['o.coursename'] = ['like',$data['coursename'].'%'];
		// 时间区间
		if(isset($data['intime'])){
			$times =  explode('~',$data['intime']);
			$intime = strtotime($times[0].' 00:00:00').','.strtotime($times[1].' 23:59:59');
			$where['l.paytime'] = ['between', $intime];
		}

		$list = $order->getOrderAccountList($where,$data['pagenum'],$limit);
		
		if($list){
			// $student = new Studentinfo();
			// $teacher = new Teacherinfo();
			$studentaddress = new Studentaddress;
			foreach ($list as $k => &$v) {
				// 处理老师和学生对应数据
				// $v['studentname'] = $student->getStudentId($v['studentid'],'nickname')['nickname'];
				// $v['teachername'] = $teacher->getTeacherId($v['teacherid'],'nickname')['nickname'];
				$v['paytime'] = date('Y-m-d H:i:s',$v['paytime']);
				$v['address'] = $v['addressid']?$studentaddress->getId($v['addressid'])['addressStr']:'-';
			}
			$count = $order->getOrderWhereCount($where);
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10085,lang('error_log'));
		}
	}


	/**
	 * 订单详情
	 * @param $orderid
	 * @return array
	 */
	public function getAccountInfo($orderid){
		if(!$orderid) return return_format('',10140,lang('param_error'));
		// 查询待结算 数据
		$order = new Ordermanage();
		$info = $order->getOrderDetail($orderid);
		if(!$info) return return_format('',10141,lang('param_error'));

		$orderstatus = ['0'=>'已下单','10'=>'已取消','20'=>'已支付','30'=>'申请退款','40'=>'已退款','50'=>'退款驳回'];
		$paytype = ['0'=>'其他','1'=>'余额支付','2'=>'微信支付','3'=>'支付宝支付','4'=>'银联支付','5'=>'paypal支付'];
		$ordersource = ['1'=>'WEB网站','2'=>'APP'];

		$student = new Studentinfo();
		$info['studentname'] = $student->getStudentId($info['studentid'],'nickname')['nickname'];
		$teacher = new Teacherinfo();
		$info['teachername'] = $teacher->getTeacherId($info['teacherid'],'nickname')['nickname'];
		$scheduling = new Scheduling();
		$info['gradename']	 = $info['schedulingid']?$scheduling->getInfoId($info['schedulingid'])['gradename']:'';
		$info['paytime']	 = date('Y-m-d H:i:s',$info['ordertime']);
		$info['orderstatus'] = $orderstatus[$info['orderstatus']];
		$info['paytype'] 	 = $info['balance']>0?'混合支付':$paytype[$info['paytype']];
		$info['ordersource'] = $ordersource[$info['ordersource']];

		if($info['addressid']){
			$studentaddress = new Studentaddress;
			$info['address'] = $studentaddress->getId($info['addressid'])['addressStr'];
		}else{
			$info['address'] = '-';
		}

		$info = where_filter($info,['studentname','address','ordersource','teachername','gradename','paytime','orderstatus','paytype','coursename','originprice','discount','amount']);
		return return_format($info,0,lang('success'));
	}



	/**
	 * [getAccountCount 账户明细统计]
	 * @param  $[type]    [1已结算 0待结算]
	 * @return [type]     [description]
	 */
	public function getAccountCount($data){
		$organaccount = new Organaccount();
//		$order = new Ordermanage();

		//获取对应的机构余额
		$info['usablemoney'] = $organaccount->getOrganAccountId()['tradeflow'];
//		$where = [
//					'orderstatus'=>array('in','20,30,50'),
//				    'closingstatus'=>array('neq',2),
//				];
		// 待结算金额
		//$info['forthemoney'] = $data['type']==1?0:$order->getOrderMoney($where);
		return return_format($info,0,lang('success'));
	}


	/**
	 * [reconciliation 对账中心列表]
	 * @return [type]     [description]
	 */
	public function reconciliation($data,$limit){
		$data = where_filter($data,['teachername','curriculumname','gradename','pagenum']);
		$schedu = new Scheduling();
		// 组装where条件
		$where = [
			's.delflag' => 1,
			's.schedule' => 1,
			's.classstatus' => ['between','1,5'],
			's.realnum'		=> ['gt',0]
		];
		isset($data['teachername']) && $where['t.nickname'] = ['like',$data['teachername'].'%'];
		isset($data['gradename']) && $where['s.gradename'] = ['like',$data['gradename'].'%'];
		isset($data['curriculumname']) && $where['s.curriculumname'] = ['like',$data['curriculumname'].'%'];

		$list = $schedu->getBillList($where,$data['pagenum'],$limit);
		if($list){
			$toteachtime = new Toteachtime;
			foreach ($list as $k => &$v){
				// 查看以完成的课时数
				if($v['classstatus']<4 || $v['realnum']==0){
					// 没人购买或者状态还没到授课中
					$v['completenum'] = 0;
				}else{
					$inwhere = ['delflag'=>1,'schedulingid'=>$v['id'],'endtime'=>['lt',time()]];
					$v['completenum'] = $toteachtime->getCount($inwhere);
				}
			}
			$count = $schedu->getBillCount($where);
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));

		}else{
			return return_format('',10142,lang('error_log'));
		}
	}


	/**
	 * 对账中心老师详情
	 * @param $id
	 */
	public function teacherReconciliation($data,$limit){
		$data = where_filter($data,['id','pagenum']);
		if(!isset($data['id'])) return return_format('',10145,lang('param_error'));
		$lessons = new Lessons;
		$list = $lessons->getLists($data['id'],$data['pagenum'],$limit);
		if($list){
			// 应到人数
			$order = new Ordermanage();
			$student = new Studentattendance;
			$schedu = new Scheduling();
			$info = $schedu->getInfoId($data['id']);

			$countOrder = $order->getOrderCounts(['schedulingid'=>$data['id'],'orderstatus'=>['egt',20]]);
			$time = time();
			foreach ($list as $k => &$v){
				$v['starttime'] = date('Y-m-d H:i:s',$v['starttime']);
				$v['countOrder'] = $countOrder;
				if($time < $v['endtime']){
					$v['realnumber'] = 0;
				}else{
					$v['realnumber'] = $student->getOfficalCashFlow($v['id']);
				}
			}
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$info['periodnum']);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10146,lang('error_log'));
		}
	}


	/**
	 * 对账中心老师对账详情
	 * @param $data
	 * @param $limit
	 * @return array
	 */
	public function haveClassInfo($data,$limit){
		$data = where_filter($data,['id','pagenum']);
		if(!isset($data['id'])) return return_format('',10147,lang('param_error'));
		$studentattendance = new Studentattendance;
		$where = ['lessonsid'=>$data['id']];
		$list = $studentattendance->getList($where,$data['pagenum'],$limit);

		if($list){
			$student = new Studentinfo();
			$attendancestatus = ['0'=>'缺勤','1'=>'出勤'];
			foreach ($list as $k => &$v){
				$v['studentname'] = $student->getStudentId($v['studentid'],'nickname')['nickname'];
				$v['attendancestatusStr'] = $attendancestatus[$v['attendancestatus']];
			}
			$count = $studentattendance->getCount($where);
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10148,lang('error_log'));
		}
	}



	/**
	 * [addWithdraw 添加提现申请]
	 * @param [type] $data [description]
	 */
	public function addWithdraw($data){

		$withdraw = new Withdraw();
		$organaccount = new Organaccount();

		// 数据效验
		$validate = new Validate($withdraw->rule,$withdraw->message);
        if(!$validate->check($data)){
            return return_format('',10086,$validate->getError());
        }
        if($data['price']<=0){
        	return return_format('',10087,lang('10087'));
        }

        // 金额方面效验
		$usablemoney = $organaccount->getOrganAccountId()['usablemoney'];
		// 提现金额要大于账户余额
		if($data['price']>$usablemoney){
			return return_format('',10088,lang('10088'));
		}

		$info = $withdraw->addEdit($data);
		if($info['code']==0){
			return return_format('',0,lang('success'));
		}else{
			return return_format('',$info['code'],$info['info']);
		}
	}

	/**
	 * [getWithdraw 获取对应提现列表]
	 * @param  [type] $data  [筛选条件]
	 * @param  [type] $limit [一页几条]
	 * @return [type]        [description]
	 */
	public function getWithdraw($data,$limit){
		$withdraw = new Withdraw();

		if($data['paystatus']==0){
			$data['paystatus'] = ['in','0,3'];
		}
		
		$list = $withdraw->getList($data,$data['pagenum'],$limit);
		$count = $withdraw->getCount($data);
		if($list){
			$paystatus = [0=>'提现中',1=>'提现成功',2=>'提现失败','3'=>'处理中'];
			$paytype = ['2'=>'微信支付','3'=>'支付宝','4'=>'银联'];
			foreach ($list as $k => &$v) {
				$v['addtimestr'] = date('Y-m-d H:i:s',$v['addtime']);
				$v['endtimestr'] = $v['endtime']?date('Y-m-d H:i:s',$v['endtime']):'-';
				$v['paytypestr'] = $paytype[$v['paytype']];
				$v['paystatusstr']  = $paystatus[$v['paystatus']];
			}

			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10091,lang('error_log'));
		}
	}


	/**
	 * [getTeacherSum 获取对应机构老师在指定时间里完成单数]
	 * @param  [type] $data  [description]
	 * @param  [type] $limit [description]
	 * @return [type]        [description]
	 */
	public function getTeacherSum($data,$limit){
		
		$teacher = new Teacherinfo();
		$data = where_filter($data,['intime','teachername','pagenum']);

		$paywhere = [];
		if(isset($data['intime'])){
			$times = explode('~',$data['intime']);
			if(count($times)!=2){
				return return_format('',10093,lang('10093'));
			}
			$paywhere = ['paytime'=>array('between',[strtotime($times[0]),strtotime($times[1])+86000])];
		}


		// 查询老师条件组合
		$where = ['delflag'=>1,'accountstatus'=>0];
		if(isset($data['teachername'])) $where['nickname'] = $data['teachername'];

		$list = $teacher->getTeachlist($where,$data['pagenum'],$limit,'initials asc');

		if($list){
			$count = $teacher->getTeachCount($where);
			$organpay = new Organpaylog();
			foreach ($list as $k => &$v) {
				//查询条件补全
				$paywhere['teacherid'] = $v['teacherid'];
				// 对应的抽成收入 和 实际单数
				$loglist = $organpay->getListlog($paywhere);
				if($loglist){
					$v['price'] = $loglist[0]['realityprice']?$loglist[0]['realityprice']:0.00;
					$v['ordersum'] = $loglist[0]['counts'];
				}else{
					$v['price'] = 0.00;
					$v['ordersum'] = 0;
				}
			}
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10094,lang('error_log'));
		}
	}
	

	/**
	 * [getTeacherPaylog description]
	 * @param  [type] $data  [description]
	 * @param  [type] $limit [description]
	 * @return [type]        [description]
	 */
	public function getTeacherPaylog($data,$limit){
		$data = where_filter($data,['pagenum','teacherid','intime']);

		$paywhere = [];
		if(isset($data['intime'])){
			$times = explode('~',$data['intime']);
			if(count($times)!=2){
				return return_format('',10096,lang('10096'));
			}
			$paywhere = ['paytime'=>array('between',[strtotime($times[0]),strtotime($times[1])+86000])];
		}

		if(!isset($data['teacherid'])||!$data['teacherid']){
			return return_format('',10097,lang('10097'));
		}

		$paywhere['teacherid'] = $data['teacherid'];

		$organpay = new Organpaylog();
		$list = $organpay->getOrganList($paywhere,$data['pagenum'],$limit);
		if($list){
			$count = $organpay->getOrganCount($paywhere);
			$student = new Studentinfo();
			$teacher = new Teacherinfo();
			foreach ($list as $k => &$v) {
				// 处理老师和学生对应数据
				$v['studentname'] = $student->getStudentId($v['studentid'],'nickname')['nickname'];
				$v['teachername'] = $teacher->getTeacherId($v['teacherid'],'nickname')['nickname'];
				$v['addtimestr'] = date('Y-m-d H:i:s',$v['addtime']);
				$v['orderstatus'] = $v['paystatus']==1?'已入账':'已退款';
			}
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',10098,lang('error_log'));
		}
	}

    
}



?>