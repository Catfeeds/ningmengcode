<?php
namespace app\admin\business;
use app\admin\model\Studentsignin;
class SigninManage{
	
	/**
	 * 获取签到列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getSigninList($nickname,$pagenum,$limit){
		$where = [] ;;
		!empty($nickname) && $where['s.nickname'] = ['like','%'.$nickname.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['s.delflag'] = 1;
		$where['ss.delflag'] = 0;

		$signinmodel = new Studentsignin;
		$field = 's.id,s.nickname,s.addtime,count(ss.studentid) as totalsignin';

		$return = $signinmodel->getSigninList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
			$r = $signinmodel->getAllSigninList($v['id'],'signdate', 'id desc');
			$r = array_column($r, 'signdate');
			$return[$k]['consignin'] = get_consecutive_count($r);
		}
		$total  = $signinmodel->getSigninListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],90007) ;
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
}

