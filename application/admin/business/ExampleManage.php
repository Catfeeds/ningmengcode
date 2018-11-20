<?php
namespace app\admin\business;
use app\admin\model\Hjxappexamplesentence;
use app\admin\model\Hjxappexamplesentencetype;
class ExampleManage{
	
	/**
	 * 获取例句列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getExampleList($type,$content,$pagenum,$limit){
		$where = [] ;;
		!empty($content) && $where['e.type'] = $type;
		!empty($content) && $where['e.content'] = ['like', '%'.$content.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['e.delflag'] = 0;

		$Examplemodel = new Hjxappexamplesentence;
		$field = 'e.id,et.name as typename,e.content,e.addtime';

		$return = $Examplemodel->getExampleList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		
		$total  = $Examplemodel->getExampleListCount($where);

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
	 * 编辑例句资料时获取例句的信息
	 * @Author lc
	 * @param $id 例句id
	 * @return object
	 *
	 */
	public function getExampleMsg($id){
		$Examplemodel = new Hjxappexamplesentence;
		//返回例句基本信息
		$field = 'id,type,content';
		$baseinfo = $Examplemodel->getExampleData($field,$id);

		if( empty($baseinfo) ){//没有符合条件的例句数据
			return return_format([],90020) ;
		}else{
			//返回例句的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addExample 添加例句数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addExample($data){
	    $allowfield = ['type','content'];
	    $newdata = where_filter($data,$allowfield);
		$Exampleobj = new Hjxappexamplesentence ;
		$return = $Exampleobj->addExample($newdata);
		return $return;
	}
	/**
	 * [updateExampleMsg 更新例句数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateExample($data){
		 $allowfield = ['id','type','content'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;

		$Exampleobj = new Hjxappexamplesentence ;
		$return = $Exampleobj->updateExample($newdata);
		return $return ;

	}
	
	/**
	 * [delExample 伪删除例句信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delExample($id){
		if($id>0){
			$Exampleobj = new Hjxappexamplesentence ;
			return $Exampleobj->delExample($id);
		}else{
			return return_format('',90020);
		}
	}
	
	/**
	 * [ImportExamples 批量导入例句]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [传递过来的excel数据]
	 * @return   [int]   [返回导入的数据条数]
	 */
	public function ImportExamples($data){
		$Exampleobj = new Hjxappexamplesentence;
		$typeobj = new Hjxappexamplesentencetype;
		foreach($data as $k=>$v){
			$type = $typeobj->getFieldByName($v['A'], 'id')['id'];
			if(empty($type) || empty($v['B'])){
				continue;
			}
			$exampleRet[$k]['type'] = $type;
			$exampleRet[$k]['content'] = $v['B'];
			$exampleRet[$k]['addtime'] = time();
		}
		if(!isset($exampleRet)){
			return 0;
		}
		$return = $Exampleobj->ImportExamples($exampleRet);
		return $return;
	}
}

