<?php
/**
 * 
 * 题库管理业务逻辑
 * 
 */
namespace app\admin\business;
use app\admin\model\Category;
use app\admin\model\Periodexerciseinfo;
use app\admin\model\Curriculum;
use app\admin\model\Period;
use app\admin\model\Schedulinglessoninfo;
class SubjectManage{
	
	function __construct() {
	}

	/**
	 * @getCurricukumlists 已录入习题的课程列表
	 * @Author lc
	 * @param $data 提交数组
	 * @param $limit 查询页数
	 * @return array
	 **/
	public function getCurricukumlists($data, $limit) {
		$where = [];
		if(!empty($data['coursename'])){
			$where['c.coursename'] = ['like','%'.$data['coursename'].'%'];
		}
		$where['c.delflag'] = 1;
		$where['e.delflag'] = 0;
		
		if($data['pagenum']>0){
			$start = ($data['pagenum'] - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}

		$Exercisemodle = new Periodexerciseinfo;
		$category = new Category();
		$field = 'e.courseid as id,c.coursename,c.categorystr,count(e.id) as exercise_count';
		$return = $Exercisemodle->getHaveExerciseCurriculumList($where,$field,$limitstr);
		
		$total  = $Exercisemodle->getHaveExerciseCurriculumCount($where);
		
		if( empty($return) ){
			return return_format('', 10001, lang('error_log'));
		}else{
			foreach ($return as $key => &$val) {
				$val['categoryname'] = $category->getCategoryName(explode('-', $val['categorystr']));
			}
			$list = [
				'data'=>$return,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$data['pagenum'],//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($list, 0, lang('success'));
		}
	}
	
	/**
	 * 获取列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getExerciseList($courseid,$periodname,$pagenum,$limit){
		if(empty($courseid)){
			return return_format('',70408);
		}
		$where = [] ;;
		!empty($courseid) && $where['e.courseid'] = $courseid;
		!empty($periodname) && $where['p.periodname'] = ['like','%'.$periodname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['e.delflag'] = 0;

		$Exercisemodle = new Periodexerciseinfo;
		$field = 'e.id,e.periodid,p.periodname,e.subjectcount,e.updatetime';

		$return = $Exercisemodle->getExerciseList($where,$field,$limitstr);
		foreach($return as $k => $v){
			$return[$k]['updatetime'] = date("Y-m-d H:i:s", $v['updatetime']);
		}
		
		$total  = $Exercisemodle->getExerciseListCount($where);

		if( empty($return) ){//没有符合条件的数据
			return return_format([],70400) ;
		}else{
			$result = [
				'data'=>$return,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($result,0);
		}
	}
	
	/**
	 * 获取习题详细信息
	 * @Author lc
	 * @param $id id
	 * @return object
	 *
	 */
	public function getExerciseInfo($id){
		if(empty($id)){
			return return_format('',70405);
		}
		$Exercisemodel = new Periodexerciseinfo;
		$field = '*' ;
		$baseinfo = $Exercisemodel->getExerciseData($field, $id);

		if( empty($baseinfo) ){
			return return_format([],70403);
		}else{

			return return_format($baseinfo,0);
		}

	}
	
	/**
	 * 编辑习题资料时获取习题的信息
	 * @Author lc
	 * @param $courseid 课程id
	 * @param $periodid 课时id
	 * @return object
	 *
	 */
	public function getExerciseMsg($id){
		if(empty($id)){
			return return_format('',70405);
		}
		$Exercisemodel = new Periodexerciseinfo;
		$field = '*' ;
		$baseinfo = $Exercisemodel->getExerciseData($field, $id);

		if( empty($baseinfo) ){
			return return_format([],70403);
		}else{
			return return_format($baseinfo,0);
		}

	}

	/**
	 * add exercise
	 * [addStudentInfo 录入习题]
	 * @Author
	 * @DateTime 2018-04-20T16:07:34+0800
	 * @param    [array]      
	 * @param    [int]        
	 * @return []
	 */
	public function addExercise($data){
		if(empty($data['courseid']) || empty($data['periodid'])){
			return return_format('',70402);
		}
		if(empty($data['subject'][1]) && empty($data['subject'][2]) && empty($data['subject'][3]) && empty($data['subject'][4])){
			return return_format('',70402);
		}
		
		$Exerciseobj = new Periodexerciseinfo;
		$where = [
			'courseid' => $data['courseid'],
			'periodid' => $data['periodid'],
			'delflag' => 0,
		];
		if(!empty($Exerciseobj->getFieldByCondition($where, 'id'))){
			return return_format('',70412);
		}
			
	    //过滤多余的字段
		//$allowfield = ['type','content','correctanswer','score'];
	    // $newdata = where_filter($data,$allowfield) ;
		$c = 0;
		foreach($data['subject'] as $v){
			if(!empty($v)) $c += count($v);
		}
	    $data['subjectcount'] = $c;
	    $data['updatetime'] = time();
		$return = $Exerciseobj->addExercise($data);
		return $return;
	}
	
	/**
	 * update exercise
	 * [addStudentInfo update习题]
	 * @Author
	 * @DateTime 2018-04-20T16:07:34+0800
	 * @param    [array]      
	 * @param    [int]        
	 * @return []
	 */
	public function updateExercise($data){
		if(empty($data['id']) || (empty($data['subject'][1]) && empty($data['subject'][2]) && empty($data['subject'][3]) && empty($data['subject'][4]))){
			return return_format('',70402);
		}
		
		$c = 0;
		foreach($data['subject'] as $v){
			if(!empty($v)) $c += count($v);
		}
	    $data['subjectcount'] = $c;
	    $data['updatetime'] = time();

		$Exerciseobj = new Periodexerciseinfo;
		$return = $Exerciseobj->updateExercise($data);
		return $return;
	}
	
	/**
	 * [checkHaveSchedulingLesson]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function checkHaveSchedulingLesson($id){
		if($id>0){
			$Exerciseobj = new Periodexerciseinfo;
			$sliobj = new Schedulinglessoninfo;
			$periodExercise = $Exerciseobj->getExerciseDataById($id);
			if(empty($periodExercise)){
				return return_format('', 70407);
			}
			$flag = $sliobj->checkSchedulingLessonByPeriodid($periodExercise['periodid']);
			if(!empty($flag)){
				return return_format('',70404);
			}else{
				return return_format('',0);
			}
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * [delKnowledge 伪删除信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delExercise($id){
		if($id>0){
			$Exerciseobj = new Periodexerciseinfo;
			$sliobj = new Schedulinglessoninfo;
			$periodExercise = $Exerciseobj->getExerciseDataById($id);
			if(empty($periodExercise)){
				return return_format('', 70407);
			}
			$flag = $sliobj->checkSchedulingLessonByPeriodid($periodExercise['periodid']);
			if(!empty($flag)){
				return return_format('',70404);
			}
			
			return $Exerciseobj->delExercise($id, $periodExercise['periodid']);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * @录入习题时获取所有直播课程
	 * @Author lc
	 * @param $where 查询条件
	 * @param $pagenum 每页显示行数
	 * @param $limit 查询页数
	 **/
	public function getAllCurriculum($data) {
		$where = [] ;
		!empty($data['coursename']) && $where['coursename'] = ['like','%'.$data["coursename"].'%'];
		$where['delflag'] = 1;
		$where['classtypes'] = '2';
		$curriculum = new Curriculum();
		$list = $curriculum->getAllCurriculumByName($where);
		if ($list) {
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10001, lang('error_log'));
		}
		return $data;
	}
	
	/**
	 * @录入习题时获取课时列表
	 * @Author lc
	 * @param $where 查询条件
	 * @param $pagenum 每页显示行数
	 * @param $limit 查询页数
	 **/
	public function getPeriodList($data) {
		if(empty($data['curriculumid'])){
			return return_format('',70408);
		}
		$where = [] ;
		$where['curriculumid'] = $data['curriculumid'];
		!empty($data['periodname']) && $where['periodname'] = ['like','%'.$data["periodname"].'%'];
		$where['delflag'] = 1;
		$period = new Period();
		$list = $period->getListsByCurriculumid($where);
		if ($list) {
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10001, lang('error_log'));
		}
		return $data;
	}
	
	/**
	 * [ImportSubjects 批量导入习题]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function ImportSubjects($data){
		$curriculum = new Curriculum;
		$period = new Period;
		$Exerciseobj = new Periodexerciseinfo;
		foreach($data as $k=>$v){
			$arr[$v['A'].'_'.$v['B']][] = $v;
		}
		$num = 0;
		foreach($arr as $kl=>$vl) {
			$subject = [];
			$s = explode('_', $kl);
			if(empty($coursearr = $curriculum->getFieldByName($s[0], 'id'))){
				continue;
			}
			
			$where = [
				'curriculumid' => $coursearr['id'],
				'periodname' => $s[1],
				'delflag' => 1
			];
			if(empty($periodarr = $period->getListsByCurriculumid($where))){
				continue;
			}
			$where_e = [
				'courseid' => $coursearr['id'],
				'periodid' => $periodarr[0]['id'],
				'delflag' => 0,
			];
			if(!empty($Exerciseobj->getFieldByCondition($where_e, 'id'))){
				continue;
			}
			$subject['courseid'] = $coursearr['id'];
			$subject['periodid'] = $periodarr[0]['id'];
			if(empty($coursearr['id']) || empty($periodarr[0]['id']) || empty($vl[0]) || empty($vl[0]['C']) || empty($vl[0]['D']) || empty($vl[0]['I'])){
				continue;
			}
			
			foreach($vl as $val){
				$subject['subject'][$val['C']][] = [
					'type' => $val['C'],
					'name' => $val['D'],
					'imageurl' => $val['E'],
					'options' => explode(',', $val['F']),
					'analysis' => $val['G'],
					'correctanswer' => $val['H'],
					'score' => $val['I'],
				];
			}
			$r = $this->addExercise($subject);
			if($r['code'] > 0){
				return false;
			}
			$num++;
		}
		return $num;
	}
	
	/**
	 * [checkHaveExercise 检查课时是否已录入习题]
	 * @Author lc
	 * @DateTime 2018-04-20T16:07:34+0800
	 * @param    [array]     
	 * @return bool 
	 */
	public function checkHaveExercise($data){
		if(empty($data['courseid']) || empty($data['periodid'])){
			return return_format('',70402);
		}
		
		$Exerciseobj = new Periodexerciseinfo;
		$where = [
			'courseid' => $data['courseid'],
			'periodid' => $data['periodid'],
			'delflag' => 0,
		];
		if(!empty($Exerciseobj->getFieldByCondition($where, 'id'))){
			return return_format('',70412);
		}
		return return_format('',0);
	}
}
	