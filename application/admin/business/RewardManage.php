<?php
/**
 * 学生奖品管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\Reward;
class RewardManage{
	/**
	 * 获取奖品列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getRewardList($name,$pagenum,$limit){
		$where = [] ;;
		!empty($name) && $where['name'] = ['like',$name.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['delflag'] = 0;

		$Rewardmodel = new Reward;
		$field = 'id,name,type,condition1,condition2,status,addtime';

		$return = $Rewardmodel->getRewardList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		$total  = $Rewardmodel->getRewardListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],70300) ;
		}else{
			$result = [
				'data'=>$return,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($result,0) ;
		}
	}
	/**
	 * 获取奖品详细信息
	 * @Author lc
	 * @param $id 奖品id
	 * @return object
	 *
	 */
	public function getRewardInfo($id){
		$Rewardmodel = new Reward;
		$where['delflag'] = 0;
		//返回奖品基本信息
		$field = 'id,name,type,condition1,condition2,value,mixamount,expiretype,expirevalue,forcoursetype,forcoursevalue,note';
		$baseinfo = $Rewardmodel->getRewardData($where,$field,$id);

		if( empty($baseinfo) ){
			return return_format([],70301) ;
		}else{

			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * 编辑奖品资料时获取奖品的信息
	 * @Author lc
	 * @param $id 奖品id
	 * @return object
	 *
	 */
	public function getRewardMsg($id){
		$Rewardmodel = new Reward;
		$where = [];
		//返回奖品基本信息
		$field = 'id,name,type,condition1,condition2,value,mixamount,expiretype,expirevalue,forcoursetype,forcoursevalue,note';
		$baseinfo = $Rewardmodel->getRewardData($where,$field,$id);

		if( empty($baseinfo) ){//没有符合条件的奖品数据
			return return_format([],40096) ;
		}else{
			//返回奖品的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addReward 添加奖品数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addReward($data){
		if(!empty($data['name']) && !empty($data['type']) && !empty($data['value']) && !empty($data['mixamount']) && !empty($data['expiretype']) && !empty($data['expirevalue']) && !empty($data['note'])){
	        $allowfield = ['name', 'condition1', 'condition2', 'type', 'value', 'mixamount', 'expiretype', 'expirevalue', 'forcoursetype', 'forcoursevalue', 'note'];
	        $newdata = where_filter($data,$allowfield);
			$Rewardobj = new Reward ;
			$return = $Rewardobj->addReward($newdata);
			return $return ;
			
		}else{
			return return_format('',70302);
		}

	}
	
	/**
	 * [updateRewardMsg 更新奖品数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateReward($data){
		if(!empty($data['name']) && !empty($data['type']) && !empty($data['value']) && !empty($data['mixamount']) && !empty($data['expiretype']) && !empty($data['expirevalue']) && !empty($data['note'])){
			$allowfield = ['id', 'name', 'condition1', 'condition2', 'type', 'value', 'mixamount', 'expiretype', 'expirevalue', 'forcoursetype', 'forcoursevalue', 'note'];
			$newdata = where_filter($data,$allowfield);

			$Rewardobj = new Reward ;
			$return = $Rewardobj->updateReward($newdata);
			return $return;
		}else{
			return return_format('',70302);
		}
	}
	/**
	 * [switchTeachStatus 切换奖品的启用状态标记]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [奖品id]
	 * @param    [int]       $status  [要修改的标记状态]
	 * @return   [array]                [返回数组]
	 */
	public function switchRewardStatus($id,$status){
		if($id>0 && in_array($status, [0,1]) ){

			$Rewardobj = new Reward ;
			return $Rewardobj->switchRewardStatus($id,$status);
		}else{
			return return_format('',40113);
		}
	}
	/**
	 * [delReward 伪删除奖品信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delReward($id){
		if($id>0){
			$Rewardobj = new Reward ;
			return $Rewardobj->delReward($id);
		}else{
			return return_format('',40115);
		}
	}
}

