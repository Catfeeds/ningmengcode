<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use think\Log;
use Messages;
/**
* 机构转账表
**/
class Withdraw extends Model
{

	protected $pk    = 'id';
	protected $table = 'nm_withdraw';

	//获取机构提现申请列表
	//充值状态$data['paystatus'] 0提现中 1提现成功 2提现失败 3处理中

    /**
     * [getWithDrawByOrganList]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param data array           [提交的信息]     
     * @return   [array]                   [description]
     */
	public function getWithDrawByOrganList($data){
		$where = [];
		//根据域名模糊搜索
        if(!empty($data['domain'])){

            $where['b.domain']  = ['like',"%".$data['domain']."%"];
        }
		$field = 'a.id,a.price,a.addtime,a.endtime,b.organname,b.domain,a.paytype,a.paystatus,a.cashaccount,a.withsn,a.organid,a.reasons';
        $list = Db::table($this->table)
                  ->alias('a')
                  ->join('nm_organ b','a.organid = b.id','LEFT')
                  ->where($where)
                  ->where('paystatus','=',$data['paystatus'])
                  ->order($data['orderbys'])
                  ->page($data['pagenum'],$data['pernum'])
                  ->field($field)
                  ->select();
        //var_dump($list);
        //die;
        $count = Db::table($this->table)
                  ->alias('a')
                  ->join('nm_organ b','a.organid = b.id','LEFT')
                  ->where($where)
                  ->where('paystatus','=',$data['paystatus'])
                  ->count();
        //var_dump($count);
        //die;
        $arr = [] ;
        foreach($list as $k => $v){

		    //根据organid获取提现人的姓名
		    $retRes = Db::table('nm_organauthinfo')->field('idname,organname,confirmtype')->where('organid','=',$v['organid'])
		    	->find();
		    if($retRes['confirmtype'] == 1){
		    	//个人认证
		    	$arr[$k]['name'] = $retRes['idname'] ? $retRes['idname'] : '';
		    }elseif($retRes['confirmtype'] == 2){
		    	//企业认证
		    	$arr[$k]['name'] = $retRes['organname'] ? $retRes['organname'] : '';
		    }

        	$arr[$k]['id'] = $v['id'];
	    	$arr[$k]['price'] = $v['price'];
	    	$arr[$k]['addtime'] = Date('Y-m-d H:i:s',$v['addtime']);
	    	$arr[$k]['endtime'] = Date('Y-m-d H:i:s',$v['endtime']);
	    	$arr[$k]['organname'] = $v['organname'];
	    	$arr[$k]['domain'] = $v['domain'];
	    	$arr[$k]['paytype'] = getWithdrawPayType($v['paytype']);
	    	$arr[$k]['paystatus'] = getWithdrawStatus($v['paystatus']);
	    	$arr[$k]['cashaccount'] = $v['cashaccount'];
	    	$arr[$k]['withsn'] = $v['withsn'];
	    	$arr[$k]['reasons'] = $v['reasons'];
	    	$arr[$k]['type'] = '机构提现';
        }
        // var_dump($data);
        //die;
        $ret = [];
        $ret['lists'] = $arr;
        $ret['count'] = $count;
        $pagenum = ceil($count/$data['pernum']);
        $ret['pagenum'] = $pagenum;      
        $ret['pernum'] = $data['pernum'];      
        return return_format($ret,0,lang('success')) ;			
	}


	//修改机构提现的状态
    /**
     * [ChangeWithDrawPayStatus]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param ids int           [提现表中的id的集合]   
     * @return   [array]                   [description]
     */
	public function ChangeWithDrawPayStatus($ids,$paystatus,$endtime=0){
		if(!$endtime){
			$endtime = time();
		}
		$where = [];
		$arr = explode(',', $ids);
		// var_dump($arr);
		// die;
		if(count($arr) == 1){
			// var_dump(1);
			// die;
			try{
				$this->save(['paystatus'=>$paystatus,'endtime'=>$endtime],['id'=>$ids]);
				return return_format('',0,lang('success')) ;
			}catch(\Exception $e){
				return return_format('',50069,$e->getMessage());
			}
		}else{
			// var_dump(2);
			// die;
			$where['id'] = ['IN',$arr];
			foreach($arr as $k => $v){
				$list[$k]['id'] = $v;
				$list[$k]['paystatus'] = $paystatus;
				$list[$k]['endtime'] = $endtime;
			}
			// var_dump($list);
			// die;
			try{
				$this->saveAll($list);
				return return_format('',0,lang('success')) ;
			}catch(\Exception $e){
				return return_format('',50069,$e->getMessage());
			}
		}
		
		
	}

	//第三方转款异步修改提现申请的方法
	
	//提现成功
	//1 减冻结金额  nm_organaccount -frozenmoney
	//2 修改提现状态  nm_withdraw paystatus->1
	//3 生成流水--》nm_organpaylog表
	//提现失败
	//1 减冻结金额  nm_organaccount -frozenmoney
	//2 添加机构余额 nm_organaccount +usablemoney
	//3 修改提现状态 nm_withdraw paystatus->2
	//4 添加理由(手动有)  nm_withdraw 添加reasons字段
	//$data中
	//id-提现id 
	//paystatus-目标提现状态(1成功,2失败)
	//endtime(结束时间) 
	//type(类型1表示支付宝回调 2表示手动)
	//reasons(备注 人工失败的时候的时候有理由) 
	//price 转账的金额
	public function asyncChangeWithDrawPayStatus($data){

		//定义数组存储转账失败的电话的集合
		$mobileArr = [];
		//对于人工操作记录返回的信息的数组
		$infoData=[];
		// var_dump($data);
		// die;
		//循环批量操作
		$i = 0;
		Log::write('------@@@循环开始@@@------');
		foreach($data as $k => $v){
			$i++;
			Log::write('------%%%第'.$i.'次循环开始%%%------');
			//提取数据
			$id = $data[$k]['id'];
			$paystatus = $data[$k]['paystatus'];
			$endtime = $data[$k]['endtime'];
			$reasons = $data[$k]['reasons'];
			$type = $data[$k]['type'];
			$price = $data[$k]['price'];
			$withsn = $data[$k]['withsn'];
			//机构流水数组
			$organData = [];
			if($type == 1){
				Log::write('##################支付宝批量转账更改状态开始##################');
				//查找当前的执行状态是否已经操作过(******)
				$checkInfo = Db::table('nm_withdraw')->where('withsn','=',$withsn)->where('paystatus','=',$paystatus)->find();
				if($checkInfo){
					Log::write('循环中-'.'-'.$withsn.'批次'.'已经处理过');
					Log::write('------%%%第'.$i.'次循环开始%%%------');
					continue;
				}
			}elseif($type == 2){
				Log::write('------------------人工更改转账状态开始##------------------');
				//查找当前真实的转账状态和转账金额
				$validinfo = Db::table('nm_withdraw')->field('paystatus,price')->where('id','=',$id)->find();
				$price = $validinfo['price'];
				if($validinfo['paystatus'] != 3){
					//组装返回前端的信息数组
					$infoData['data'] = '';
					$infoData['code'] = 50085;
					$infoData['info'] = lang('50085');
					Log::write('提现'.$id.'非法更改提现状态');	
					Log::write($infoData);
					Log::write('提现'.$id.'非法更改提现状态');
					return $infoData ;				
				}
			}
			//事务开始
			Db::startTrans();
			try{
				//查找id对应的organid
				$organinfo = Db::table('nm_withdraw')->field('organid')->where('id','=',$id)->find();
				$organid = $organinfo['organid'];
				//查找organid对应的超级管理员的手机号
				$organacc = Db::table('nm_allaccount')->field('mobile,username')->where('organid','=',$organid)->find();
				$mobile = $organacc['mobile'];
				$username = $organacc['username'];
				if($paystatus == 1){
					//提现成功
					Log::write('success-----提现成功开始------success');
					//减少机构冻结余额
					Log::write('organaccount-----修改冻结余额开始------organaccount');
					//查找当前的冻结金额
					$findFrozenResOne = Db::table('nm_organaccount')
		                            ->where(['organid'=>$organid])
		                            ->field('frozenmoney')
		                            ->find();
		            Log::write('price:'.$price.'显示冻结余额'.$findFrozenResOne['frozenmoney']);
		            if( ($findFrozenResOne['frozenmoney'] <= 0) ||($findFrozenResOne['frozenmoney'] < $price) ){
		            	//如果当前冻结余额不大于0
						//组装返回前端的信息数组
					    $infoData['data'] = '';
					    $infoData['code'] = 50105;
					    $infoData['info'] = '机构冻结余额不足';
						Log::write('事务失败原因执行批次:转账成功-批次号:'.$withsn.'冻结余额不足');
						Log::write('------%%%第'.$i.'次循环结束%%%------');
						continue;		            	
		            }
		            $infoOne = Db::table('nm_organaccount')
		                            ->where(['organid'=>$organid])
		                            ->exp('frozenmoney','frozenmoney - '.$price)
		                            ->update();
		            Log::write($infoOne);
		            Log::write('organaccount-----修改冻结余额结束------organaccount');
		            //生成机构流水
		            Log::write('organpaylog-----添加机构流水开始------organpaylog');
		            $organData = [
		                'studentid' => '',
						'courseid'  => '',
						'paynum'    => $price,
	                    'paytype'   => 3,
	                    'paystatus' => 4,
	                    'paytime'   => $endtime,
	                    'organid'   => $organid,
	                    'out_trade_no'=> $withsn,
	                    'rake' => 0,
	                    'rakeprice' => 0.00,
	                    'realityprice' => 0.00, 
	                    'teacherid' => '',
		            ];
		            $infoTwo = Db::table('nm_organpaylog')->insert($organData);
		            Log::write($infoTwo);
		            Log::write('organpaylog-----添加机构流水结束------organpaylog');
		            Log::write('success-----提现成功结束------success');
				}elseif($paystatus == 2){
					//提现失败减掉冻结余额 加上账户余额
		            Log::write('fail-----提现失败开始------fail');
		            Log::write('organaccount-----修改冻结余额,账户余额开始------organaccount');


					//查找当前的冻结金额
					$findFrozenResTwo = Db::table('nm_organaccount')
		                            ->where(['organid'=>$organid])
		                            ->field('frozenmoney')
		                            ->find();
		            Log::write('price:'.$price.'显示冻余额'.$findFrozenResTwo['frozenmoney']);
		            if( ($findFrozenResTwo['frozenmoney'] <= 0 )||($findFrozenResTwo['frozenmoney'] < $price) ){
		            	//如果当前冻结余额不大于0
						//组装返回前端的信息数组
					    $infoData['data'] = '';
					    $infoData['code'] = 50105;
					    $infoData['info'] = '机构冻结余额不足';
					    Log::write('事务失败原因执行批次:转账失败-批次号:'.$withsn.'冻结余额不足');
					    Log::write('------%%%第'.$i.'次循环结束%%%------');
						continue;								            	
		            }else{
			            //填充电话号码
						$mobileArr[] = [
							'mobile'=>$mobile,
							'username'=>$username,
							'withdrawId'=>$id,
							'endtime'=>Date('Y-m-d H:i:s',$endtime),
							'withsn'=>$withsn,
							'price'=>$price,
						];		            	
		            }
                    $infoThree = Db::table('nm_organaccount')
                            ->where(['organid'=>$organid])
                            ->exp('usablemoney','usablemoney + '.$price)
                            ->exp('frozenmoney','frozenmoney - '.$price)
                            ->update();
		            Log::write('organaccount-----修改冻结余额,账户余额结束------organaccount');
		            Log::write('fail-----提现失败结束------fail');
				}

				//修改提现状态nm_withdraw
				Log::write('withdraw-----修改提现状态开始------withdraw');
				Db::table('nm_withdraw')->where('id','EQ',$id)->update([
			        'endtime'  => $endtime,
			        'paystatus' => $paystatus,
			        'reasons'=>$reasons
			    ]);
	            // 提交事务
	            Db::commit();
			    Log::write('withdraw-----修改提现状态结束------withdraw');
				if($type == 1){
					Log::write('##支付宝批量转账更改状态成功##');
				}elseif($type == 2){
					Log::write('##人工更改转账状态成功##');;
				}
				if($type == 2){
					//组装返回前端的信息数组
					$infoData['data'] = '';
					$infoData['code'] = 0;
					$infoData['info'] = lang('success');
				}
			}catch(\Exception $e){
				//echo 123123123;
				Db::rollback();
				Log::write('事务失败原因'.$e->getMessage());
				if($type == 1){
					Log::write('------##支付宝批量转账更改状态失败##------');
				}elseif($type == 2){
					Log::write('------##人工更改转账状态失败##------');
					Log::write('------------------人工更改转账状态结束------------------');
					//对于人工操作要再次添加返回前端的信息
					//组装返回前端的信息数组
					$infoData['data'] = $e->getMessage();
					$infoData['code'] = 50004;
					$infoData['info'] = lang('50004');
				}				
			}
			if($type == 1){
				Log::write('##################支付宝批量转账更改状态结束##################');
			}
			Log::write('------%%%第'.$i.'次循环结束%%%------');	
		}
		Log::write('------@@@循环结束@@@------');
		//对于提现失败的发送短信
		if(!empty($mobileArr)){
			Log::write('------发送失败短信开始------');
			//记录哪些需要发短信
			Log::write($mobileArr);
			foreach($mobileArr as $a => $b){
				$obj = new Messages();
				$codeRes = $obj->sendMeg($b['mobile'],8,$params = [$b['username'],'编号:'.$b['withdrawId'].',批次号:'.$b['withsn'].',转账金额:'.$b['price'].'元已失败,处理结束时间为:'.$b['endtime']],'86','BatchFail');
				Log::write('批次:'.$b['withsn'].'提现id:'.$b['withdrawId'].'--'.'手机号:'.$b['mobile'].'--发送结果:'.$codeRes['result']);
			}
			Log::write('------发送失败短信结束------');			
		}
		//对于人工操作将返回值
		if(!empty($infoData)){
			Log::write('人工操作返回前端信息');
			Log::write($infoData);
			return $infoData ;
		}
		
		
	}

	//检测提现申请是否都是为指定的状态 即paystatus是否都为目标值
    /**
     * [ChangeWithDrawPayStatus]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param ids str           [提现表中的id的集合]   
     * @param targetStatus int           [目标提现状态]   
     * @return   [array]                   [description]
     */
	public function CheckWithDrawVality($ids,$targetStatus){
		$arr = explode(',', $ids);
		$len = count($arr);
		
		if($len == 1){
			$where = [];
			$where['id'] = ['EQ',$ids];
            $needSum = Db::table($this->table)
                  ->where($where)
                  ->where('paystatus','=',$targetStatus)
        		  ->find();
			if(empty($needSum)){
				return false;
			}else{
				return true;
			}	
		}else{
			//var_dump($arr);
			$num = 0;
        	foreach($arr as $k=>$v){
        		$where = [];
        		$where['id'] = ['EQ',$v];
	            $needSum = Db::table($this->table)
	                  ->where($where)
	                  ->where('paystatus','=',$targetStatus)
	        		  ->find();
	        	//var_dump($needSum);
	            if($needSum){
	            	$num ++;
	            }        		
        	}
        	// var_dump($num);
        	// die;
			if($num != $len){
				return false;
			}else{
				return true;
			}

		}
	
	
	}

	//需要提现的资金的总金额
    /**
     * [getNeedSumWithDraw]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param ids str           [提现表中的id的集合]   
     * @return   [array]                   [description]
     */
	public function getNeedSumWithDraw($ids){
		$arr = explode(',', $ids);

		$where = [];
		if(count($arr) == 1){
			$where['id'] = ['EQ',$ids];
		}else{
			$where['id'] = ['IN',$arr];
		}
        $needSum = Db::table($this->table)
                  ->where($where)
                  ->where('paystatus','=',0)
        		  ->sum('price');
        return $needSum;	
	}



	//获取机构提现详情
    /**
     * [getSumOutDetailByOrgan]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param id int           [提现表中的id]   
     * @return   [array]                   [description]
     */
	public function getSumOutDetailByOrgan($id){

	
		try{
			$field = 'a.id,a.price,a.addtime,a.endtime,b.organname,b.domain,a.paytype,a.paystatus,a.cashaccount,a.withsn,a.reasons';
	        $ret = Db::table($this->table)
	                  ->alias('a')
	                  ->join('nm_organ b','a.organid = b.id','LEFT')
	                  ->where('a.id','=',$id)
	                  ->field($field)
	                  ->find();
	        // var_dump($ret);
	        // die;
	        if(empty($ret)){
	        	return return_format('',50102,lang('50102')) ;
	        }
	        $data = [] ;
	        //
	    	$data['id'] = $ret['id'];
	    	$data['price'] = $ret['price'];
	    	$data['addtime'] = Date('Y-m-d H:i:s',$ret['addtime']);
	    	$data['endtime'] = Date('Y-m-d H:i:s',$ret['endtime']);
	    	$data['organname'] = $ret['organname'];
	    	$data['domain'] = $ret['domain'];
	    	$data['paytype'] = getWithdrawPayType($ret['paytype']);
	    	$data['paystatus'] = getWithdrawStatus($ret['paystatus']);
	    	$data['cashaccount'] = $ret['cashaccount'];
	    	$data['withsn'] = $ret['withsn'];
	    	$data['reasons'] = $ret['reasons'];

	        return return_format($data,0,lang('success')) ;		
		}catch(\Exception $e){
			return return_format($e->getMessage(),50004,lang('50004')) ;
		}

	}


	//获取机构提现成功的总金额  状态值paystatus为1
    /**
     * [getSumOutByOrgan]
     * @Author zzq
     * @DateTime 2018-05-15  
     * @return   [array]                   [description]
     */
	public function getSumOutByOrgan(){
		$data = [];
		try{
	        $totalSum = Db::table($this->table)
	                  ->where('paystatus','=',1)
	        		  ->sum('price');
	        $data['totalSum'] = $totalSum;
	        return $data;	
		}catch(\Exception $e){
			$data['totalSum'] = 0;
			return $data;
		}
	
	}

	//获取当前的提现
    /**
     * [getWithdrawType]
     * @Author zzq
     * @DateTime 2018-05-15  
     * @return   [array]                   [description]
     */
	public function getWithdrawType($id){

		//连表获取机构认证人的姓名或者认证企业的名称
	    $res = Db::table($this->table)
	                  ->field('paytype,price,cashaccount,withsn,organid')
	                  ->where('id','=',$id)
	        		  ->find();
	    if($res){
	    	$organid = $res['organid'];
	    }
	    //根据organid获取注册人姓名
	    $ret = Db::table('nm_organauthinfo')->field('idname,organname,confirmtype')->where('organid','=',$organid)
	    	->find();
	    if($ret['confirmtype'] == 1){
	    	//个人认证
	    	$res['name'] = $ret['idname'] ? $ret['idname'] : '';
	    }elseif($ret['confirmtype'] == 2){
	    	//企业认证
	    	$res['name'] = $ret['organname'] ? $ret['organname'] : '';
	    }
		return $res;
	
	}	
		
}