<?php
/**
 * 学生标签管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\StudentTag;
use app\admin\model\Studentchildtag;
class StudentTagManage{
	/**
	 * 获取标签列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getTagList($name,$pagenum,$limit){
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

		$Tagmodel = new StudentTag;
		$Childtagmodel = new Studentchildtag;
		$field = 'id,name,status';

		$return = $Tagmodel->getTagList($where,$field,$limitstr);
		$total  = $Tagmodel->getTagListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],80001) ;
		}else{
			foreach($return as &$v){
				$v['childtag'] = join(',', $Childtagmodel->getchildTagByfatherid($v['id']));
			}
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
	 * 获取标签详细信息
	 * @Author lc
	 * @param $id 标签id
	 * @return object
	 *
	 */
	public function getTagInfo($id){
		$Tagmodel = new StudentTag;
		$Childtagmodel = new Studentchildtag;
		//返回标签基本信息
		$field = 'id,name';
		$baseinfo = $Tagmodel->getTagData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],80089) ;
		}else{
			$baseinfo['childtag'] = join(',', $Childtagmodel->getchildTagByfatherid($baseinfo['id']));
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * 编辑标签资料时获取标签的信息
	 * @Author lc
	 * @param $id 标签id
	 * @return object
	 *
	 */
	public function getTagMsg($id){
		$Tagmodel = new StudentTag;
		$Childtagmodel = new Studentchildtag;
		//返回标签基本信息
		$field = 'id,name' ;
		$baseinfo = $Tagmodel->getTagData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],40096) ;
		}else{
			$baseinfo['childtag'] = join(',', $Childtagmodel->getchildTagByfatherid($baseinfo['id']));
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addTag 添加标签数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addTag($data){
	    $allowfield = ['name','childname'];
	    //过滤 多余的字段
	    $newdata = where_filter($data,$allowfield) ;

		$Tagobj = new StudentTag ;
		$return = $Tagobj->addTag($newdata);
		return $return;
	}
	/**
	 * [updateTagMsg 更新标签数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateTag($data){
		$allowfield = ['name', 'id', 'childname'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;

		$Tagobj = new StudentTag ;
		$return = $Tagobj->updateTag($newdata);
		return $return;

	}
	/**
	 * [switchTeachStatus 切换标签的启用状态标记]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [标签id]
	 * @param    [int]       $status  [要修改的标记状态]
	 * @return   [array]                [返回数组]
	 */
	public function switchTagStatus($id,$status){
		if($id>0 && in_array($status, [0,1]) ){

			$Tagobj = new StudentTag ;
			return $Tagobj->switchTagStatus($id,$status);
		}else{
			return return_format('',40113);
		}
	}
	/**
	 * [delTag 伪删除标签信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delTag($id){
		if($id>0){
			$Tagobj = new StudentTag ;
			return $Tagobj->delTag($id);
		}else{
			return return_format('',40115);
		}
	}
}

