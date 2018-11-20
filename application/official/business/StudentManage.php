<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\official\business;
use app\admin\model\Studentinfo;
use app\admin\model\Ordermanage;
use app\official\model\Officialuseroperate;
class StudentManage{
	/**
	 * [getUserList description]
	 * @Author wyx
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $organid 机构标记id      必填
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getUserList($mobil,$nickname,$pagenum,$organid,$limit){
		if( $organid > 0 ){
			$where = [] ;
			!empty($mobil) && $where['mobile'] = $mobil ;
			!empty($nickname) && $where['nickname'] = ['like',$nickname.'%'] ;
			if($pagenum>0){
				$start = ($pagenum - 1 ) * $limit ;
				$limitstr = $start.','.$limit ;
			}else{
				$start = 0 ;
				$limitstr = $start.','.$limit ;
			}
			$where['organid'] = $organid;
			$where['delflag'] = 0;

			$usermodel = new Studentinfo;
			$resultarr = $usermodel->getUserList($where,$limitstr);
			//学生登录时间转换
			foreach ($resultarr as &$val) {
				$val['logintime'] = date('Y-m-d H:i:s',$val['logintime']) ;
			}
			$total = $usermodel->getUserListCount($where);
			//返回数组组装
			$result = [
				 	'data'=>$resultarr,// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=>$limit ,// 每页多少条记录
				 		'pagenum' =>$pagenum ,//当前页码
				 		'total'   => $total // 符合条件总的记录数
				 	]
				] ;

			return return_format($result,0) ;

		}else{
			return return_format([],40141) ;
		}
	}
	/**
	 * [getUserDetail 获取学员的详细信息]
	 * @Author wyx
	 * @DateTime 2018-04-20T13:56:36+0800
	 * @param    [int]        $studentid [学生标识id]
	 * @param    [int]        $organid   [机构标识id]
	 * @return   [array]                 [description]
	 */
	public function getUserDetail($studentid){
		if($studentid>0){
			$usermodel = new Studentinfo;
			$data = $usermodel->getUserDetail($studentid);
			if(!empty($data)){
				$data['birthday']  = date('Y-m-d',$data['birth']);
				$data['logintime'] = date('Y-m-d H:i:s',$data['logintime']);
				$data['addtime'] = date('Y-m-d H:i:s',$data['addtime']);

				//获取学生购买过的课程
				$orderobj  = new Ordermanage;
		        $orderlist = $orderobj->getStudentOrder($studentid) ;
		        // var_dump($orderlist) ;
				return return_format(['data'=>$data,'courselist'=>$orderlist],0) ;
			}else{
				return return_format([],40142) ;

			}
			return '';
		}else{
			return return_format([],40143) ;
		}
	}

	/**
	 * [changeUserStatus 更改学生状态]
	 * @Author wyx
	 * @DateTime 2018-04-20T20:53:36+0800
	 * @param    [int]         $userid    [需要更新的学生id]
	 * @param    [int]         $flag      [机构标记id]
	 * @param    [int]         $organid   [机构标记id]
	 * @return   [array]                
	 */
	public function changeUserStatus($userid,$flag){
		if($userid>0  && in_array($flag, [0,1]) ){
			$usermodel = new Studentinfo;
        	$ret = $usermodel->changeUserStatus($userid,$flag);
        	//添加管理员的操作日志
	        $obj = new Officialuseroperate();
	        $obj->addOperateRecord('禁用了学生'); 
	        return  return_format('',0);
        	return $ret ;
		}else{
			return return_format([],40144) ;
		}	
	}
	
}



?>