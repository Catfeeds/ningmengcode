<?php
namespace app\official\business;
use app\official\model\Organaccount;
use app\official\model\Organ;
use app\official\model\Studentinfo;
use app\official\model\Teacherinfo;
use app\official\model\Toteachtime;
use app\official\model\Studentpaylog;
class AnalysisManage{
	/**
	 * 官方首页 数据统计
	 * @Author wyx
	 * @param  $data  [array]  过滤条件 包含时间 课件时间选择 交易流水时间选择
	 * @return array  [返回信息]
	 * 
	 */
	public function getAnalysisData($organid=0){
		//交易总额
		$accountobj = new Organaccount;
		$flow = $accountobj->getOrganTradeFlow($organid);
		if(empty($flow['amount'])) $flow['amount'] = '0.00' ;
		// $flow['amount'] 
		if(date('d') == '01' ){// 如果当天是本月第一天，为了统计昨日数据，需要提前一天统计
			$monthstart = strtotime(date('Y-m-01 00:00:00')) - 86400;//为了统计昨天的 时间减一天
		}else{
			$monthstart = strtotime(date('Y-m-01 00:00:00')) ;
		}
		//机构总数 当日 昨日 本月
		$organarr   = $this->getOrganScan($monthstart);
		
		//学生总数 当日新增 昨日新增 本月新增
		$studentarr = $this->getStudentScan($monthstart);
		
		//老师总数
		$teachobj = new Teacherinfo;
		$teachtotal = $teachobj->getTeacherAllAccount();
		// var_dump($teachtotal);
		$anasysdata = [
				'totalflowcash' => $flow['amount'],//总交易额
				'organarr' => $organarr,//机构总数，机构总览
				'studentarr' => $studentarr,//学生总数，学生总览
				'teachtotal' => $teachtotal,//教师总数
			];
		return return_format($anasysdata,0);
	}
	/**
	 * 官方首页 数据统计 课时及并发统计
	 * @Author wyx
	 * @param  $data  [array]  过滤条件 包含时间 课件时间选择 交易流水时间选择
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganAnaCourse($data){
		
		$data['courseline'] = empty($data['courseline']) ? 'day' : $data['courseline'] ;
		//课件并发统计 今日  本周  本月
		$coursearr = $this->getCourseAnalysis($data['courseline']);
		// var_dump($flowarr);
		$anasysdata = [
				'coursearr' => $coursearr,//课程分析
			];
		return return_format($anasysdata,0);
	}
	/**
	 * 官方首页 数据统计 交易流水统计
	 * @Author wyx
	 * @param  $data  [array]  过滤条件 包含时间 课件时间选择 交易流水时间选择
	 * @return array  [返回信息]
	 * 
	 */
	public function getOrganAnaFlow($data){
		//交易流水统计  近七天  近30天
		// $data['flowline'] = 'month';
		$data['flowline'] = empty($data['flowline']) ? 'week' : $data['flowline'] ;
		$flowarr = $this->getSaleAnalysis($data['flowline']);
		// var_dump($flowarr);
		$anasysdata = [
				'flowarr' => $flowarr,//流水分析
			];
		return return_format($anasysdata,0);
	}
	/**
	 *	对课程 上课时间 统计分析  可以按照 当天 本周 本月
	 *  @Author wyx
	 *	@param $flag  day week  month 分别代表 当天 本周 本月
	 *	@return array
	 */
	protected function getCourseAnalysis($flag='day'){

		if($flag == 'week'){
			//按周统计
			$timeline = $this->dateSliceByCondition( $flag ) ;
			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDate($timeline['startdate'],$timeline['enddate']);
			//数据组装
			$datearr = array_column($courselist, 'intime');
			foreach ($timeline['origndata'] as &$val) {
				$key = array_search($val['intime'], $datearr);
				if($key!==false){
					$val['num'] = $courselist[$key]['num'];
					$val['realnum'] = $courselist[$key]['allrealnum'];
				}
			}
			return $timeline['origndata'];

		}elseif($flag == 'month'){
			//按月统计
			$timeline = $this->dateSliceByCondition( $flag ) ;
			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDate($timeline['startdate'],$timeline['enddate']);
			//数据组装
			$datearr = array_column($courselist, 'intime');
			foreach ($timeline['origndata'] as &$val) {
				$key = array_search($val['intime'], $datearr);
				if($key!==false){
					$val['num'] = $courselist[$key]['num'];
					$val['realnum'] = $courselist[$key]['allrealnum'];
				}
			}
			return $timeline['origndata'];

		}else{
		//按天统计 默认按照天
			$toteachobj = new Toteachtime;
			$courselist = $toteachobj->getCoursePlanByDay(date('Y-m-d'));

			$temparr = [] ;
			//对数据的timekey 处理 并统计首部相同的timekey对应的 人数总和
			foreach ($courselist as $key => &$val) {
				//对时间点处理 以便后面统计
				if(strpos($val['timekey'],',')){
					$val['timekey'] = substr($val['timekey'],0,strpos($val['timekey'],',')) ;
				}
				//统计相同起点，并求和
				if( isset($temparr[$val['timekey']]) ){// 如果已经存在 就累加
					$temparr[$val['timekey']]['realnum'] = $val['realnum'] + $temparr[$val['timekey']]['realnum'] ;
					$temparr[$val['timekey']]['num'] = ++$temparr[$val['timekey']]['num'] ;
				}else{// 没有的就 赋值
					$temparr[$val['timekey']]['realnum'] = $val['realnum'] ;
					$temparr[$val['timekey']]['num'] = 1 ;
				}
			}
			//组装数据 按照48个时间段 30分一段
			$resultdata = [] ;
			for($i=0;$i<48;$i++){
				if(isset($temparr[$i])){
					$resultdata[$i] = $temparr[$i] ;
				}else{
					$resultdata[$i] = ['realnum'=>0,'num'=>0] ;
				}
			}
			return $resultdata;
		}

	}
	/**
	 *	对交易流水 统计分析  可以按 近7天 近30天
	 *  @Author wyx
	 *	@param $flag  week  month 分别代表 近7天 近30天 默认近7天
	 *	@return array 
	 *	订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
	 */
	protected function getSaleAnalysis($flag='week'){
		$starttime = 0;
		$unixend   = time();
		if($flag == 'week'){
		//按近7天统计
			$timestr = date('Y-m-d 00:00:00',$unixend-6*86400) ;
			$starttime = strtotime($timestr);

		}elseif($flag == 'month'){
		//按近30天统计
			$timestr = date('Y-m-d 00:00:00',$unixend-29*86400) ;
			$starttime = strtotime($timestr);
		}
		$paylogobj = new Studentpaylog;
		$result = $paylogobj->getOfficalCashFlow($starttime);

		//创建原始格式数组
		$resultdate = array_column($result, 'datestr');
		$returnarr = [] ;
		$temp = [] ;
		for($i=$starttime;$i<=$unixend;){
			//当前步的时间
			$tempdate = date('Y-m-d',$i) ;

			$key = array_search($tempdate, $resultdate );
			if($key!==false){
				$temp['datestr']  = date('Y-m-d',$i) ;
				$temp['num']      = $result[$key]['num'] ;
				$temp['totalpay'] = $result[$key]['totalpay'] ;
				$returnarr[] = $temp ;
			}else{
				$temp['datestr']  = date('Y-m-d',$i) ;
				$temp['num']      = 0 ;
				$temp['totalpay'] = '0.00';
				$returnarr[] = $temp ;
			}

			$i+=86400;
		}

		return $returnarr;

	}
	/**
	 *	获取机构总用户数目 和 近一个月 的 今天和昨天的 统计数据
	 *  @Author wyx
	 *	@param $starttime 本月的开始 时间
	 *	@return array
	 *
	 */ 
	protected function getOrganScan($starttime){
		//机构总数
		$organobj = new Organ;
		$organtotal = $organobj->getValidOrganCount() ;
		
		//机构概览
		$monthorganarr = $organobj->getAllMonthData($starttime) ;
		$monthorgan = [] ;
		foreach ($monthorganarr as $val) {
			$monthorgan[$val['formatdate']] = $val['num'] ;
		}
		//今日 机构数
		if(empty($monthorgan[date('Y-m-d')])){
			$organtodaynum = 0 ;
		}else{
			$organtodaynum = $monthorgan[date('Y-m-d')] ;
		}
		//昨日 机构数
		if(empty($monthorgan[date('Y-m-d',time()-86400)])){
			$organyesnum = 0 ;
		}else{
			$organyesnum = $monthorgan[date('Y-m-d',time()-86400)] ;
			//如果今天是本月第一天 将昨天的 置为0 然后方便统计
			if(date('d') == '01' ) $monthorgan[date('Y-m-d',time()-86400)] = 0 ;
		}
		//本月 机构数
		$monthorgannum = array_sum($monthorgan) ;

		return [
				'organtotal'    =>$organtotal,
				'organtodaynum' =>$organtodaynum,
				'organyesnum'   =>$organyesnum,
				'monthorgannum' =>$monthorgannum,
			] ;
	}
	/**
	 *	获取学生的总数目 和 近一个月 的 今天和昨天的 统计数据
	 *  @Author wyx
	 *	@param $starttime 本月的开始 时间
	 *	@return array
	 */ 
	protected function getStudentScan($starttime){
		//学生总数
		$studentobj = new Studentinfo;
		$studenttotal = $studentobj->getStudentAllAccount();
		//学生总览
		$monthdatarr    = $studentobj->getAllMonthData($starttime);

		$monthdata = [] ;
		foreach ($monthdatarr as $val) {
			$monthdata[$val['formatdate']] = $val['num'] ;
		}
		//今日 学生数
		if(empty($monthdata[date('Y-m-d')])){
			$stutodaynum = 0 ;
		}else{
			$stutodaynum = $monthdata[date('Y-m-d')] ;
		}
		//昨日 学生数
		if(empty($monthdata[date('Y-m-d',time()-86400)])){
			$stuyesnum = 0 ;
		}else{
			$stuyesnum = $monthdata[date('Y-m-d',time()-86400)] ;
			if(date('d') == '01' ) $monthdata[date('Y-m-d',time()-86400)] = 0 ;//将昨天的清空 以便计算当月的
		}
		//本月 学生数
		$monthnum = array_sum($monthdata) ;

		return [
				'studenttotal' =>$studenttotal,
				'stutodaynum'  =>$stutodaynum,
				'stuyesnum'    =>$stuyesnum,
				'monthnum'     =>$monthnum,
			] ;
	}
	/**
	 *	通过传入 周 或者 月。来截取本周最后一天日期，或者本月最后一天的日期
	 *  @Author wyx
	 *
	 *
	 */
	private function dateSliceByCondition($devide){
		
		$unixstart = 0 ;
		$unixend   = 0 ;
		if($devide == 'week'){//按周取
			$week = date('w') ;
			if($week==0){
				$unixend   = time() ;
				$unixstart = $unixend - 6*86400 ;

				$end   = date('Y-m-d');
				$start = date('Y-m-d',$unixstart );
			}else{
				$sub = 7 - $week ;

				$unixend   = time()+$sub*86400 ;
				$unixstart = $unixend - 6*86400 ;
				
				$end   = date('Y-m-d', $unixend ) ;
				$start = date('Y-m-d', $unixstart ) ;
			}
			
		}elseif( $devide == 'month' ){// 按月取 计算本月的最后一天
   			$start = date('Y-m-01');
   			$end   = date('Y-m-d', strtotime("$start +1 month -1 day")) ;

   			$unixend   = strtotime($end.' 00:00:09') ;
			$unixstart = strtotime($start.' 00:00:09') ;
		}
		// 组装数组
		$returnarr = [] ;
		$temp = [] ;
		for($i=$unixstart;$i<=$unixend;){
			$temp['intime']  = date('Y-m-d',$i) ;
			$temp['num']     = 0 ;
			$temp['realnum'] = 0;
			$returnarr[] = $temp ;

			$i+=86400;
		}
		return ['startdate'=>$start,'enddate'=>$end,'origndata'=>$returnarr] ;

	}
	

}
