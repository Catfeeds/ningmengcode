<?php
/**
**财务管理业务处理
**/
namespace app\official\business;
use app\official\model\Ordermanage;
use app\official\model\Studentpaylog;
use app\official\model\Withdraw;
use app\official\model\Recharge;
use app\official\model\Organauditbillorder;
use think\Loader; 
use alibatchtrans\AlipayBatch;
use think\Log;       
class FinanceManage{


	//获取累计交易余额
    /**
     * [getTradeTotalSum //获取累计交易金额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param 无参数         []   ]   
     * @return   [array]                   [description]
     */
	public function getTradeTotalSum(){
		$obj = new Ordermanage();
		$res = $obj->getTradeTotalSum();
		return $res;
	}

	//获取订单列表
    /**
     * [getOrderList //获取订单列表]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param          [array]   [提交的信息]   
     * @return   [array]                   [description]
     */
	public function getOrderList($data){
		$data['domain'] = trim($data['domain']);
		$data['domain'] = $this->filterKeywords($data['domain']);
		$obj = new Ordermanage();
		$res = $obj->getOrderList($data);
		return $res;
	}


	//获取机构缴费总金额
    /**
     * [getTradeTotalSum //获取机构缴费总金额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param 无参数         []   ]   
     * @return   [array]                   [description]
     */
	public function getOrganPayAuditBillTotalSum(){
		$obj = new Organauditbillorder();
		$res = $obj->getOrganPayAuditBillTotalSum();
		return $res;
	}

	//获取订单列表
    /**
     * [getOrderList //获取订单列表]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param          [array]   [提交的信息]   
     * @return   [array]                   [description]
     */
	public function getOrganPayAuditBillList($data){
		$data['domain'] = trim($data['domain']);
		$data['domain'] = $this->filterKeywords($data['domain']);
		$obj = new Organauditbillorder();
		$res = $obj->getOrganPayAuditBillList($data);
		return $res;
	}	


    /**
     * [getOrderDetail //获取订单详情]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param id int           [订单id]   ]   
     * @return   [array]                   [description]
     */
	public function getOrderDetail($id){
		if(!$this->isPositiveInteger($id)){
			return return_format('',50008,lang('50008')) ;			
		}
		$obj = new Ordermanage();
		$res = $obj->getOrderDetail('a.id',$id);
		return $res;		
	}

	//机构充值|收入列表明细
    /**
     * [getAccountDetailInList]
     * @Author zzq
     * @DateTime 2018-05-15     
     * @param data array           [提交的信息]        
     * @return   [array]                   [description]
     */
	public function getAccountDetailInList($data){
		$obj = new Studentpaylog();
		$res = $obj->getAccountDetailInList($data);
		return $res;		
	}

	//第三方充值|第三方下单逻辑分开处理
    /**
     * [getAccountInDetail]
     * @Author zzq
     * @DateTime 2018-05-15     
     * @param $data           [提交的信息]          
     * @return   [array]                   [description]
     */
    public function getAccountInDetail($data){
    	$paystatusRet = [1,2];
    	if(!in_array($data['paystatus'],$paystatusRet)){
    		return return_format('',50005,lang('50005')) ;
    	}
    	if(empty($data['out_trade_no'])){
    		return return_format('',50073,lang('50073')) ;
    	}
    	if($data['paystatus'] == 1){
    		//表示下单 直接找
			$obj = new Ordermanage();
			$res = $obj->getOrderDetail('a.ordernum',$data['out_trade_no']);
			return $res;	
    	}else{
    		//表示直接充值
			$obj = new Recharge();
			$res = $obj->getChargeDetail('a.rechargenum',$data['out_trade_no']);
			return $res;
    	}

    }

	//账目明细之获取账户余额
    /**
     * [getRemainingSum //账目明细之获取账户余额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @return   [array]                   [description]
     */
	public function getRemainingSum(){   		
	    //第一,当前学生的充值，买课收入     
		$stuobj = new Studentpaylog();
		$sumInByStu = $stuobj->getSumInByStu();
		if(!empty($sumInByStu)){
			$sumIn = $sumInByStu['totalSum'];
		}
		//var_dump($sumIn);
		// die;
		//第二,获取机构的提现总金额
		$organobj = new Withdraw();
		$sumOutByOrgan = $organobj->getSumOutByOrgan();
		if(!empty($sumOutByOrgan)){
			$sumOutOne = $sumOutByOrgan['totalSum'];
		}
		//var_dump($sumOutOne);
		//die;		
		//第三,获取个人提现（暂不做）

		//第四，两者相减
		/*$In = $sumIn*100;
		$Out = $sumOutOne*100; 
		$remainingSum = (float)(($In-$Out)/100);*/
		$remainingSum = floatval(round(($sumIn-$sumOutOne),2));
		$data = [];
		$data['remainingSum'] = $remainingSum;
		return return_format($data,0,lang('success')) ;
	}

	//获取机构提现列表
	//这里的paystatus为0 1 2 3
    /**
     * [getWithDrawByOrganList]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param data array           [提交的信息]     
     * @return   [array]                   [description]
     */
	public function getWithDrawByOrganList($data){
		$data['domain'] = trim($data['domain']);
        $data['domain'] = $this->filterKeywords($data['domain']);
		$obj = new Withdraw();
		$res = $obj->getWithDrawByOrganList($data);
		return $res;
	}

	//获取机构提现详情
    /**
     * [getSumOutDetailByOrgan]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param id int           [机构提现表id]     
     * @return   [array]                   [description]
     */
	public function getSumOutDetailByOrgan($id){
    	if(empty($id)){
    		return return_format('',50074,lang('50074')) ;
    	}
		$obj = new Withdraw();
		$res = $obj->getSumOutDetailByOrgan($id);
		return $res;		
	}

	//处理机构提现业务第一步
    /**
     * [manageWithDraw]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param str int           [提现表中的id的集合]   
     * @return   [array]                   [description]
     */
	public function manageWithDraw($ids){
		if(empty($ids)){
			return return_format('',50074,lang('50074')) ;
		}
		//校验此时的状态是不是0 因为只处理待审核的提现申请
		$checkobj = new Withdraw();
		$flag = $checkobj->CheckWithDrawVality($ids,0);
		// var_dump($flag);
		// die;
		if(!$flag){
			return return_format('',50085,lang('50085')) ;
		}
		//检验此时的余额是否足够

		//获取此时的需要提现的金额总和
		$withdraw = new Withdraw();
		$needSum = $withdraw->getNeedSumWithDraw($ids);
		// var_dump($needSum);
		// die;
		$RemainingSum = $this->getRemainingSum();
		$actualSum = $RemainingSum['data']['remainingSum'];
		// var_dump($RemainingSum);
		// die;
		if($actualSum <= 0){
			return return_format('',50080,lang('50080')) ;
		}else{
			if($needSum > $actualSum)
			return return_format('',50080,lang('50080')) ;
		}
		//die;


		//分别获取要执行要执行支付宝转账的数据表主键id的数组

		$allIds = $this->getManageIds($ids);
		$aliArr = $allIds['aliArr'];
		log::write($aliArr);
		// var_dump($aliArr);
		// die;

		#将此时的机构提现单的状态改为待处理
		$time = time();
		$obj = new Withdraw();
		$returnRes = $obj->ChangeWithDrawPayStatus($ids,3,$time);

		//支付宝
		$res = $this->aliManageWithDraw($aliArr);
		$aliForm = [];
		if($res){
			$aliForm['data'] = $res;
		}
		return return_format($aliForm,0,lang('success'));
	}                                                                            

	public function getManageIds($ids){
		$arr = explode(',',$ids);

		$aliArr = [];
		$wxArr = [];
		$ylArr = [];

		foreach($arr as $k => $v){
			$obj = new Withdraw();
			$res = $obj->getWithdrawType($v);
			// var_dump($res);
			// die;
			if($res){
				if($res['paytype'] == 2){
					//微信

					$wxArr[] = $v.'-'.$res['price'].'-'.$res['cashaccount'].'-'.$res['name'].'-'.$res['withsn'];
				}elseif($res['paytype'] == 3){
					//支付宝
					$aliArr[] = $v.'-'.$res['price'].'-'.$res['cashaccount'].'-'.$res['name'].'-'.$res['withsn'];
				}elseif($res['paytype'] == 4){
					//银联
					$ylArr[] = $v.'-'.$res['price'].'-'.$res['cashaccount'].'-'.$res['name'].'-'.$res['withsn'];
				}

			}
		}

		$ret = [];
		$ret['aliArr'] = $aliArr;
		$ret['wxArr'] = $aliArr;
		$ret['ylArr'] = $aliArr;
		return $ret;
	}


	//支付宝的转账操作
    /**
     * [ListManageWithDrawResAsync]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param ids string           [提现表中的id的集合]   
     * @return   [array]                   [description]
     */
	public function aliManageWithDraw($aliArr){
		//记录日志
		//Log::record('转账的ids:'.$aliArr,'info');

		$mydate = Date('Ymd',time());
		$myRandStr = $mydate.$this->getARandLetter(7);
		$myStr = $this->getARandLetter(4);

		if(!empty($aliArr)){
			$data = [];
			$aliArrLen = count($aliArr);
			//一维转为二维数组
			$_aliArr = [];
			foreach($aliArr as $k => $v){
				$_arr = [];
				$_arr = explode('-',$v);
				$_aliArr[$k]['id'] = $_arr[0];
				$_aliArr[$k]['price'] = $_arr[1];
				$_aliArr[$k]['cashaccount'] = $_arr[2];
				$_aliArr[$k]['name'] = $_arr[3];
				$_aliArr[$k]['withsn'] = $_arr[4];
			}

			//获取付款总金额以及付款详细数据
			$aliTotalFee = 0;
			$aliDetailStrArr  = [];
			foreach($_aliArr as $k => $v){
				$aliTotalFee += $v['price'];
				$aliDetailStrArr[] = $myStr.'_'.$v['withsn']."^". $v['cashaccount']."^".$v['name']."^".$v['price']."^"."支付宝转账";
			}
			$aliDetailStr = implode('|',$aliDetailStrArr);


	        //付款当天日期
	        $data['pay_date'] =$mydate;
	        //必填，格式：年[4位]月[2位]日[2位]，如：20100801

	        //批次号
	        $data['batch_no'] = $myRandStr;
	        //必填，格式：当天日期[8位]+序列号[3至16位]，如：201008010000001

	        //付款总金额
	        $data['batch_fee'] = $aliTotalFee;
	        //必填，即参数detail_data的值中所有金额的总和

	        //付款笔数
	        $data['batch_num'] = $aliArrLen;
	        //必填，即参数detail_data的值中，“|”字符出现的数量加1，最大支持1000笔（即“|”字符出现的数量999个）

	        //付款详细数据
	        $data['detail_data'] = $aliDetailStr;
	        //必填，格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2...
	        //"Zfp2_4688584144816833^18235102743^余瑞^0.50^支付宝转账|Zfp2_5709584141216833^18739798667^赵志强^0.20^支付宝转账|Zfp2_4809584144816567^18739798667^赵志强^0.01^支付宝转账|Zfp2_4709584144816833^18235102743^余瑞^0.01^支付宝转账"
	        //调用支付宝批量转账接口

	        // var_dump($data);
	        // die;
	        
	        //记录传送给支付宝的接口的数据
	        log::write($data);

	        //建立请求
	        $alipaySubmit = new AlipayBatch;
	        $html_text = $alipaySubmit->createBatchTransRequest($data);
	        return $html_text;
	        // return $res;	


		}
		
	}


	//异步处理提现的结果
    /**
     * [manageWithDrawResAsync]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param verifyResult boolean           [提现表中的id的集合] 
     * @param post array           [支付宝返回信息]   
     * @return   [array]                   [description]
     */
	public function manageWithDrawResAsync($post){
        //验证成功
        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        
        //批量付款数据中转账成功的详细信息
        //流水号^收款方账号^收款账号姓名^付款金额^成功标识(S)^成功原因(null)^支付宝内部流水号^完成时间
        //0315001^gonglei1@handsome.com.cn^龚本林^20.00^S^null^200810248427067^20081024143652|
        if(!empty($post['success_details'])){
	        $successDetails = $post['success_details'];
	        //拆成数组
	        $successDetailsArrOne = explode('|',$successDetails);
	        //弹出最后一个元素
	        array_pop($successDetailsArrOne);
	        $successArr = [];
	        foreach($successDetailsArrOne as $k => $v){
	            $arrOne = [];
	            $findSuccessRes = [];
	            $withsn = '';
	            $arrOne = explode('^',$v);
	            $withsn= substr($arrOne[0], strrpos($arrOne[0], '_')+1);
	            //查询这个批次的转账的功能已经是否已经执行过了
	            $withobj = new Withdraw();
	            $findSuccessRes = $withobj->where('withsn','EQ',$withsn)->where('paystatus','EQ','1')->find();
	            if($findSuccessRes){
	            	//已经更改过状态
	            	log::write('success首次检测ID为'.$findSuccessRes['id'].'-'.'流水号'.$withsn.'已经处理成功过');
	            	continue;
	            }else{
	            	//还没改状态
	            	//获取当前的转账的主键的id
	            	$letObj = new Withdraw();
	            	$letSuccessRes = $letObj->where('withsn','EQ',$withsn)->find();
	            	$successArr[$k]['id']=$letSuccessRes['id'];   
	            	$successArr[$k]['withsn'] = $withsn;
		            $successArr[$k]['paystatus'] = 1;
		            //mktime(时, 分, 秒, 月, 日, 年)
		            $dateTime = $arrOne[7];
		            $year = substr($dateTime,0,4);
		            $month = substr($dateTime,4,2);
		            $day = substr($dateTime,6,2);
		            $hour = substr($dateTime,8,2);
		            $minute = substr($dateTime,10,2);
		            $second = substr($dateTime,12,2);
		            $successArr[$k]['reasons'] = '';
		            $successArr[$k]['price'] = $arrOne[3];
		            $successArr[$k]['type'] = 1;
		            //操作完成时间
		            $successArr[$k]['endtime'] = mktime($hour,$minute,$second,$month,$day,$year);	            	
	            } 
	        }
	        //修改这些id对应的转账申请状态为1
			$time = time();
			$obj = new Withdraw();
			//var_dump($successArr);
			// die;
			if(!empty($successArr)){
				$returnSuccessRes = $obj->asyncChangeWithDrawPayStatus($successArr);
			}	        	
        }

		// var_dump($returnSuccessRes);
		// die;
        //批量付款数据中转账失败的详细信息
        //格式为：流水号^收款方账号^收款账号姓名^付款金额^失败标识(F)^失败原因^支付宝内部流水号^完成时间。
        //0315006^xinjie_xj@163.com^星辰公司1^20.00^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^200810248427065^20081024143651|
        
        if(!empty($post['fail_details'])){
        	$failDetails = $post['fail_details'];
	        //拆成数组
	        $failDetailsArrOne = explode('|',$failDetails);
	        array_pop($failDetailsArrOne);
	        $failArr = [];
	        foreach($failDetailsArrOne as $k => $v){
	            $arrTwo = [];
	            $findFailRes = [];
	            $withsn = '';
	            $arrTwo = explode('^',$v);
	            $withsn = substr($arrTwo[0], strrpos($arrTwo[0], '_')+1);
	            //查询这个批次的转账的功能已经是否已经执行过了
	            $withobj = new Withdraw();
	            $findFailRes = $withobj->where('withsn','EQ',$withsn)->where('paystatus','EQ','2')->find();
	            if($findFailRes){
	            	//已经更改过状态
	            	log::write('fail首次检测ID为'.$findFailRes['id'].'-'.'流水号'.$withsn.'已经处理成功过');
	            	continue;
	            }else{
	            	//获取当前的转账的主键的id
	            	//获取当前的转账的主键的id
	            	$letObj = new Withdraw();
	            	$letFailRes = $letObj->where('withsn','EQ',$withsn)->find();
	            	$failArr[$k]['id']=$letFailRes['id']; 
	            	$failArr[$k]['withsn'] = $withsn;
		            $failArr[$k]['paystatus'] = 2;
		            //mktime(时, 分, 秒, 月, 日, 年)
		            $dateTime = $arrTwo[7];
		            $year = substr($dateTime,0,4);
		            $month = substr($dateTime,4,2);
		            $day = substr($dateTime,6,2);
		            $hour = substr($dateTime,8,2);
		            $minute = substr($dateTime,10,2);
		            $second = substr($dateTime,12,2);
		            $failArr[$k]['reasons'] = '';
		            $failArr[$k]['price'] = $arrTwo[3];
		            $failArr[$k]['type'] = 1;
		            //操作完成时间
		            $failArr[$k]['endtime'] = mktime($hour,$minute,$second,$month,$day,$year);
	            } 
	        }
	        //修改这些id对应的转账申请状态为1
			$time = time();
			$obj = new Withdraw();
			//var_dump($failArr);
			if(!empty($failArr)){
				$returnfailRes = $obj->asyncChangeWithDrawPayStatus($failArr);
			}
        }
        //die;
	}

	//手动修改转账状态
	public function manualChangeWithDrawPayStatus($data){
		// var_dump($data);
		// die;
		if(empty($data[0]['id']) || empty($data[0]['paystatus']) || empty($data[0]['price']) || empty($data[0]['type']) || empty($data[0]['endtime']) ){
			return return_format('',50000,lang('50000')) ;
		}

		if($data[0]['type'] == 2){
			if($data[0]['paystatus'] == 2){
				if(empty($data[0]['reasons'])){
					return return_format('',50104,lang('50104')) ;
				}
			}
		}
		// var_dump($data);
		// die;
		//查询当前的流水号
		$resObj = new Withdraw();
		$res = $resObj->field('withsn')->where('id','=',$data[0]['id'])->find();
		if($res){
			$data[0]['withsn'] = $res['withsn'];
		}else{
			return return_format('',50000,lang('50000')) ;
		}
		$obj = new Withdraw(); 
		$res = $obj->asyncChangeWithDrawPayStatus($data);
		return $res;
	}
	/**
	 * //判断是否是非负整数
	 * @Author zzq
	 * @param $value   int  [广告id]
	 * @return bool  [返回信息]
	 * 
	 */
    protected function isPositiveInteger($value)
    {
    	$pattern = "/^\d+$/";
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
        
    }

	/**
	 * //过滤搜索字符串
	 * @Author zzq
	 * @param $str   string  [搜索字符串]
	 * @return string  [返回信息]
	 * 
	 */
    public function filterKeywords($str){
    	$str = strip_tags($str);   //过滤html标签
        $str = htmlspecialchars($str);   //将字符内容转化为html实体
        $str = addslashes($str);

        return $str;
    }


	public function getARandLetter($number = 1) {
	 if ($number == 0)
	  return FALSE; //去除0
	 $number = $number < 0 ? - $number : $number; //如果小于零取正值
	 $letterArr = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z','0','1','2','3','4','5','6','7','8','9');
	 $returnStr ='';
	 for($i= 0; $i < $number; $i ++) {
	  $returnStr .= $letterArr [rand ( 0, 61 )];
	 }
	 return $returnStr;
	}


}