<?php
/**
 * 学生知识类型管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\KnowledgeType;
use app\admin\model\Signinbackgroundimage;
use app\admin\model\Knowledgesetupqrcode;
class KnowledgeSetupManage{
	
	/**
	 * 获取背景图列表
	 * @Author lc
	 * @return array
	 */
	public function getSigninbgiList(){
		$where = [];
		$where['delflag'] = 0;

		$sbgimodel = new Signinbackgroundimage;
		$field = 'id,imageurl,addtime';

		$return = $sbgimodel->getSigninbgiList($where, $field, 'id desc');
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}

		if( empty($return) ){
			return return_format([],70106) ;
		}else{
			$result = [
				'data'=>$return,
			] ;
			return return_format($result,0) ;
		}
	}
	
	/**
	 * 获取二维码列表
	 * @Author lc
	 * @return array
	 */
	public function getQrList(){
		$where = [];
		$where['delflag'] = 0;

		$qrmodel = new KnowledgeSetupqrcode;
		$field = 'imageurl';

		$return = $qrmodel->getQrList($where, $field, 'id desc');
		return return_format($return, 0) ;
	}
	
	/**
	 * 获取知识类型列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getKnowledgeTypeList($name,$pagenum,$limit){
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

		$KnowledgeTypemodel = new KnowledgeType;
		$field = 'id,name,addtime' ;

		$return = $KnowledgeTypemodel->getKnowledgeTypeList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
		}
		$total  = $KnowledgeTypemodel->getKnowledgeTypeListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],70100) ;
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
	 * 获取知识类型详细信息
	 * @Author lc
	 * @param $id 知识类型id
	 * @return object
	 *
	 */
	public function getKnowledgeTypeInfo($id){
		$KnowledgeTypemodel = new KnowledgeType;
		//返回知识类型基本信息
		$field = 'id,name' ;
		$baseinfo = $KnowledgeTypemodel->getKnowledgeTypeData($field,$id);

		if( empty($baseinfo) ){
			return return_format([],70101) ;
		}else{

			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * 编辑知识类型资料时获取知识类型的信息
	 * @Author lc
	 * @param $id 知识类型id
	 * @return object
	 *
	 */
	public function getKnowledgeTypeMsg($id){
		$KnowledgeTypemodel = new KnowledgeType;
		//返回知识类型基本信息
		$field = 'id,name' ;
		$baseinfo = $KnowledgeTypemodel->getKnowledgeTypeData($field,$id);

		if( empty($baseinfo) ){//没有符合条件的知识类型数据
			return return_format([],70101) ;
		}else{
			//返回知识类型的信息
			return return_format($baseinfo,0) ;
		}

	}
	
	/**
	 * [addKnowledgeType 添加知识类型数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addKnowledgeType($data){
		if( !empty($data['name'])){
	        $allowfield = ['name'];
	        // $allowfield = ['imageurl','mobile','name','KnowledgeTypename','sex','country','province','city','birth','profile','password','status','id','prphone'];
	        //过滤 多余的字段
	        $newdata = where_filter($data,$allowfield) ;
	        //$newdata['addtime'] = time();

			//创建验证器规则
			$KnowledgeTypeobj = new KnowledgeType ;
			$return = $KnowledgeTypeobj->addKnowledgeType($newdata);
			return $return ;
			
		}else{
			return return_format('',70102);
		}

	}
	/**
	 * [updateKnowledgeTypeMsg 更新知识类型数据]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateKnowledgeType($data){
		$allowfield = ['name', 'id'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;

		$KnowledgeTypeobj = new KnowledgeType ;
		$return = $KnowledgeTypeobj->updateKnowledgeType($newdata);
		return $return ;

	}
	
	/**
	 * [delKnowledgeType 伪删除知识类型信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delKnowledgeType($id){
		if($id > 0){
			$KnowledgeTypeobj = new KnowledgeType ;
			return $KnowledgeTypeobj->delKnowledgeType($id);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * [伪删除]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delSigninbgi($id){
		if($id > 0){
			$sbgimodel = new Signinbackgroundimage ;
			return $sbgimodel->delSigninbgi($id);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * [ 上传签到背景图片]
	 * @return [type] [description]
	 * ./static/code.txt
	 * 
	 */
	public function uploadToFiles($inurl = '') {
		if(!$inurl) return false;
		$sbgimodel = new Signinbackgroundimage();
		$filedata['imageurl'] = $inurl;
		$upInfo = $sbgimodel->addFile($filedata);
		if($upInfo['code']==0){
			return TRUE;
		}else{
			return false;
		}
	}
	
	/**
	 * [ 更新知识配置二维码]
	 * @return [type] [description]
	 * ./static/code.txt
	 * 
	 */
	public function updateToFiles($inurl = '') {
		if(!$inurl) return false;
		$qrcodemodel = new Knowledgesetupqrcode();
		$filedata['imageurl'] = $inurl;
		$upInfo = $qrcodemodel->updateFile($filedata);
		if($upInfo['code']==0){
			return TRUE;
		}else{
			return false;
		}
	}
	
	/**
	 * 获取所有知识类型
	 */
	public function getAllKnowledgeTypeList(){
		$KnowledgeTypemodel = new KnowledgeType;
		$baseinfo = $KnowledgeTypemodel->getAllKnowledgeTypeList();

		if( empty($baseinfo) ){
			return return_format([],70101);
		}else{
			return return_format($baseinfo,0);
		}
	}
}

