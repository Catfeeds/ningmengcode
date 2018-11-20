<?php
namespace app\admin\business;
use app\admin\model\Hjxappcommentlabel;
class CommentlabelManage{
	/**
	 * 获取知识列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getCommentlabelList($star,$content,$pagenum,$limit){
		$where = [] ;;
		!empty($star) && $where['star'] = $star;
		!empty($content) && $where['content'] = ['like', '%'.$content.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['delflag'] = 0;

		$Commentlabelmodel = new Hjxappcommentlabel;
		$field = 'id,star,content,addtime';

		$return = $Commentlabelmodel->getCommentlabelList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		
		$total  = $Commentlabelmodel->getCommentlabelListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],0) ;
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
	 * 编辑知识资料时获取知识的信息
	 * @Author lc
	 * @param $id 知识id
	 * @return object
	 *
	 */
	public function getCommentlabelMsg($id){
		$Commentlabelmodel = new Hjxappcommentlabel;
		//返回知识基本信息
		$field = 'id,star,content';
		$baseinfo = $Commentlabelmodel->getCommentlabelData($field,$id);

		if( empty($baseinfo) ){//没有符合条件的知识数据
			return return_format([],90020) ;
		}else{
			//返回知识的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addCommentlabel 添加知识数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addCommentlabel($data){
		$allowfield = ['star','content'];
	    $newdata = where_filter($data,$allowfield);
		$Commentlabelobj = new Hjxappcommentlabel;
		if($Commentlabelobj->getCommentlabelListCount(['star'=>$newdata['star'], 'delflag'=>0]) == 10){
			return return_format([], 90026);
		}
		
		$return = $Commentlabelobj->addCommentlabel($newdata);
		return $return;
	}
	/**
	 * [updateCommentlabelMsg 更新知识数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateCommentlabel($data){
		 $allowfield = ['id','star','content'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;

		$Commentlabelobj = new Hjxappcommentlabel ;
		$return = $Commentlabelobj->updateCommentlabel($newdata);
		return $return ;

	}
	
	/**
	 * [delCommentlabel 伪删除知识信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delCommentlabel($id){
		if($id>0){
			$Commentlabelobj = new Hjxappcommentlabel ;
			return $Commentlabelobj->delCommentlabel($id);
		}else{
			return return_format('',40115);
		}
	}
}

