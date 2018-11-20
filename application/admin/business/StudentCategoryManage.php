<?php
/**
 * 学生分类管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\StudentCategory;
class StudentCategoryManage{
	/**
	 * 获取分类列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getCategoryList($name,$pagenum,$limit){
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

		$categorymodel = new StudentCategory;
		$field = 'id,name,status' ;

		$return = $categorymodel->getCategoryList($where,$field,$limitstr);
		$total  = $categorymodel->getCategoryListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],70001) ;
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
	 * 获取分类详细信息
	 * @Author lc
	 * @param $id 分类id
	 * @return object
	 *
	 */
	/* public function getCategoryInfo($id){
		$categorymodel = new StudentCategory;
		//返回分类基本信息
		$field = 'id,name,status' ;
		$baseinfo = $categorymodel->getCategoryData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],70089) ;
		}else{

			return return_format($baseinfo,0) ;
		}

	} */
	
	/**
	 * 编辑分类资料时获取分类的信息
	 * @Author lc
	 * @param $id 分类id
	 * @return object
	 *
	 */
	public function getCategoryMsg($id){
		$categorymodel = new StudentCategory;
		//返回分类基本信息
		$field = 'id,name' ;
		$baseinfo = $categorymodel->getCategoryData($field,$id);

		if( empty($baseinfo) ){//没有符合条件的分类数据
			return return_format([],40096) ;
		}else{
			//返回分类的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addCategoryMsg 添加分类数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addCategory($data){
		if( !empty($data['name']) ){
	        $allowfield = ['name'];
	        // $allowfield = ['imageurl','mobile','name','Categoryname','sex','country','province','city','birth','profile','password','status','id','prphone'];
	        //过滤 多余的字段
	        $newdata = where_filter($data,$allowfield) ;
	        //$newdata['addtime'] = time();

			//创建验证器规则
			$categoryobj = new StudentCategory ;
			$return = $categoryobj->addCategory($newdata);
			return $return ;
			
		}else{
			return return_format('',40097);
		}

	}
	/**
	 * [updateCategoryMsg 更新分类数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateCategory($data){
		$allowfield = ['name','id'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;
		
		$categoryobj = new StudentCategory ;
		$return = $categoryobj->updateCategory($newdata);
		return $return ;

	}
	/**
	 * [switchTeachStatus 切换分类的启用状态标记]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [分类id]
	 * @param    [int]       $status  [要修改的标记状态]
	 * @return   [array]                [返回数组]
	 */
	public function switchCategoryStatus($id,$status){
		if($id>0 && in_array($status, [0,1]) ){

			$categoryobj = new StudentCategory ;
			return $categoryobj->switchCategoryStatus($id,$status);
		}else{
			return return_format('',40113);
		}
	}
	/**
	 * [delCategory 伪删除分类信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delCategory($id){
		if($id>0){
			$categoryobj = new StudentCategory ;
			return $categoryobj->delCategory($id);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * 获取所有类型
	 */
	public function getAllCategoryList(){
		$categoryobj = new StudentCategory;
		$baseinfo = $categoryobj->getAllCategoryList();

		if( empty($baseinfo) ){
			return return_format([],70101);
		}else{
			return return_format($baseinfo,0);
		}
	}
}

