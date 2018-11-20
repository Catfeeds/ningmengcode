<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\admin\business;
use app\admin\model\Compositioncomment;
use \Messages;
class ReviewManage{
	
	/**
	 * [getReviewList description]
	 * @Author lc
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getReviewList($teachername,$pagenum,$limit){
		$where = [] ;
		!empty($teachername) && $where['t.nickname'] = ['like', '%'.$teachername.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['cc.type'] = 1;

		$commodel = new Compositioncomment;
		$resultarr = $commodel->getReviewList($where,$limitstr);
		
		$total = $commodel->getReviewListCount($where);
		//返回数组组装
		$result = [
				'data'=>$resultarr,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;

		return return_format($result,0);
	}

	/**
	 * [getTeacherReviewList description]
	 * @Author lc
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getTeacherReviewList($teacherid,$nickname,$pagenum,$limit){
		if(!is_intnum($teacherid)){
			return return_format('', 90008, lang('param_error')) ;
		}
		$where = [] ;
		$where['cc.userid'] = $teacherid;
		!empty($nickname) && $where['hs.nickname'] = ['like', '%'.$nickname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['cc.type'] = 1;

		$commodel = new Compositioncomment;
		$resultarr = $commodel->getTeacherReviewList($where,$limitstr);
		
		foreach ($resultarr as &$val) {
			$val['reviewtime'] = date('Y-m-d H:i',$val['reviewtime']);
		}
		
		$total = $commodel->getTeacherReviewListCount($where);
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
	}
}
