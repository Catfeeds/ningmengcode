<?php
namespace app\admin\business;
use app\admin\model\Hjxappexamplesentencetype;
use app\admin\model\Signinbackgroundimage;
use app\admin\model\Examplesetupqrcode;
class ExampletypeManage{
	
	/**
	 * 获取例句类型列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getExampleTypeList($name,$pagenum,$limit){
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

		$ExampleTypemodel = new Hjxappexamplesentencetype;
		$field = 'id,name,addtime' ;

		$return = $ExampleTypemodel->getExampleTypeList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		$total  = $ExampleTypemodel->getExampleTypeListCount($where);

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
	 * 编辑例句类型资料时获取例句类型的信息
	 * @Author lc
	 * @param $id 例句类型id
	 * @return object
	 *
	 */
	public function getExampleTypeMsg($id){
		$ExampleTypemodel = new Hjxappexamplesentencetype;
		$field = 'id,name' ;
		$baseinfo = $ExampleTypemodel->getExampleTypeData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],90020) ;
		}else{
			//返回例句类型的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addExampleType 添加例句类型数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addExampleType($data){
		if( !empty($data['name'])){
	        $allowfield = ['name'];
	        $newdata = where_filter($data,$allowfield) ;
			$ExampleTypeobj = new Hjxappexamplesentencetype ;
			$return = $ExampleTypeobj->addExampleType($newdata);
			return $return ;
			
		}else{
			return return_format('',90022);
		}

	}
	/**
	 * [updateExampleTypeMsg 更新例句类型数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateExampleType($data){
		$allowfield = ['name', 'id'];
		$newdata = where_filter($data,$allowfield) ;

		$ExampleTypeobj = new Hjxappexamplesentencetype ;
		$return = $ExampleTypeobj->updateExampleType($newdata);
		return $return ;

	}
	
	/**
	 * [delExampleType 伪删除例句类型信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delExampleType($id){
		if($id > 0){
			$ExampleTypeobj = new Hjxappexamplesentencetype ;
			return $ExampleTypeobj->delExampleType($id);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * 获取所有例句类型
	 */
	public function getAllExampleTypeList(){
		$ExampleTypemodel = new Hjxappexamplesentencetype;
		$baseinfo = $ExampleTypemodel->getAllExampleTypeList();

		if( empty($baseinfo) ){
			return return_format([],70101);
		}else{
			return return_format($baseinfo,0);
		}
	}
}

