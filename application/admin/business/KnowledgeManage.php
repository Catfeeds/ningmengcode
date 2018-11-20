<?php
/**
 * 学生知识管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\Knowledge;
use app\admin\model\KnowledgeType;
use app\admin\model\StudentCategory;
class KnowledgeManage{
	/**
	 * 获取知识列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getKnowledgeList($content,$pagenum,$limit){
		$where = [] ;;
		!empty($content) && $where['k.content'] = ['like', '%'.$content.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['k.delflag'] = 0;

		$Knowledgemodel = new Knowledge;
		$field = 'k.id,kt.name as typename,k.content,k.answer,sc.name as forstudenttypename,k.addtime';

		$return = $Knowledgemodel->getKnowledgeList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		
		$total  = $Knowledgemodel->getKnowledgeListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],70200) ;
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
	 * 获取知识详细信息
	 * @Author lc
	 * @param $id 知识id
	 * @return object
	 *
	 */
	/* public function getKnowledgeInfo($id){
		$Knowledgemodel = new Knowledge;
		//返回知识基本信息
		$field = 'id,typeid,content,answer,forstudenttype';
		$baseinfo = $Knowledgemodel->getKnowledgeData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],70201) ;
		}else{

			return return_format($baseinfo,0) ;
		}

	} */
	
	/**
	 * 编辑知识资料时获取知识的信息
	 * @Author lc
	 * @param $id 知识id
	 * @return object
	 *
	 */
	public function getKnowledgeMsg($id){
		$Knowledgemodel = new Knowledge;
		//返回知识基本信息
		$field = 'id,typeid,content,answer,forstudenttype';
		$baseinfo = $Knowledgemodel->getKnowledgeData($field,$id);

		if( empty($baseinfo) ){//没有符合条件的知识数据
			return return_format([],70201) ;
		}else{
			//返回知识的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addKnowledge 添加知识数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addKnowledge($data){
	    $allowfield = ['typeid','content','answer','forstudenttype'];
	    $newdata = where_filter($data,$allowfield);
		$Knowledgeobj = new Knowledge ;
		$return = $Knowledgeobj->addKnowledge($newdata);
		return $return;
	}
	/**
	 * [updateKnowledgeMsg 更新知识数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateKnowledge($data){
		 $allowfield = ['id','typeid','content','answer','forstudenttype'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;

		$Knowledgeobj = new Knowledge ;
		$return = $Knowledgeobj->updateKnowledge($newdata);
		return $return ;

	}
	
	/**
	 * [delKnowledge 伪删除知识信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delKnowledge($id){
		if($id>0){
			$Knowledgeobj = new Knowledge ;
			return $Knowledgeobj->delKnowledge($id);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * [ImportKnowledges 批量导入知识]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [传递过来的excel数据]
	 * @return   [int]   [返回导入的数据条数]
	 */
	public function ImportKnowledges($data){
		$Knowledgeobj = new Knowledge;
		$typeobj = new KnowledgeType;
		$scobj = new StudentCategory;
		foreach($data as $k=>$v){
			$typeid = $typeobj->getFieldByName($v['A'], 'id')['id'];
			$forstudenttype = $scobj->getFieldByName($v['D'], 'id')['id'];
			if(empty($typeid) || empty($v['B']) || empty($forstudenttype)){
				continue;
			}
			$knowledgeRet[$k]['typeid'] = $typeid;
			$knowledgeRet[$k]['content'] = $v['B'];
			$knowledgeRet[$k]['answer'] = $v['C'];
			$knowledgeRet[$k]['forstudenttype'] = $forstudenttype;
			$knowledgeRet[$k]['addtime'] = time();
		}
		if(!isset($knowledgeRet)){
			return 0;
		}
		$return = $Knowledgeobj->ImportKnowledges($knowledgeRet);
		return $return;
	}
}

