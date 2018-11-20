<?php
/**
**课程管理控制器 包括班级管理 分类管理
**/
namespace app\official\Controller;
use app\official\controller\Base;
use think\Session;
use think\Request;

use app\admin\business\CurriculumModule;
use app\official\business\CourseManage;

class Course extends Base{

	
	/**
	 * @ //添加分类  包括二级分类
	 * @Author zzq
	 * @param $data['categoryname']分类名称
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/official/Course/addCategory
	 **/
	public function addCategory() {
		$data = Request::instance()->post(false);
		//判断
		if(!isset($data['categoryname']) || !isset($data['fatherid'])){
			$dataReturn =  return_format('',50000,lang('50000')) ;
			$this->ajaxReturn($dataReturn);
		}
		//模拟测试
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addCategory($data, 1);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}


	/**
	 * @ 后台分类列表 和查询后台子类
	 * @Author zzq
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/official/Course/getCategoryIdList
	 **/
	public function getCategoryIdList() {
		$data = Request::instance()->POST(false);
		//判断
		if(!isset($data['fatherid'])){
			$dataReturn =  return_format('',50000,lang('50000')) ;
		}
		//模拟测试
		//$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$data['limit'] = config('param.pagesize')['official_category_list'],
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCategoryIdList($data, 20, 1);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台添加课程模块 分类联动
	 * @Author jcr
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/official/Course/getCurricukumCategoryList
	 **/
	public function getCurricukumCategoryList() {
		$data = Request::instance()->POST(false);
		//判断
		if(!isset($data['fatherid'])){
			$dataReturn =  return_format('',50000,lang('50000')) ;
		}
		//模拟测试
		$data['limit'] = 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCategoryIdList($data, 100, 1);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 编辑分类 是否启用 删除 名称编辑
	 * @Author zzq
	 * @param id 分类id
	 * @param status 1显示 0不显示
	 * @param categoryname 名称
	 * POST | URL:/official/Course/editCategoryId
	 **/
	public function editCategoryId() {
		$data = Request::instance()->post(false);
		//判断
		if(!isset($data['categoryname']) || !isset($data['status']) || !isset($data['id'])){
			$dataReturn =  return_format('',50000,lang('50000')) ;
		}
		//模拟测试
		// $data = array('id'=>1,'categoryname'=>12112,'status'=>1);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCategory($data, 1);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 分类删除
	 * @Author zzq
	 * @param id 分类id
	 * POST | URL:/official/Course/deleteCategory
	 **/
	public function deleteCategory() {
		$data = Request::instance()->POST(false);
		//判断
		if(!isset($data['id'])){
			$dataReturn =  return_format('',50000,lang('50000')) ;
		}
		//模拟测试
		// $data = array('id'=>1,'delflag'=>0);
		$data['delflag'] = 0;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCategory($data, 1);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台分类列表 上下移动
	 * @Author jcr*
	 * @param $data['id'] 分类id
	 * @param $data['operate'] 分类操作 0上移 1下移
	 * @param $data['rank'] 分类操作 级别
	 * @param $data['sort'] 分类操作 当前排序值
	 * POST | URL:/admin/Course/shiftCategory
	 **/
	public function shiftCategory() {
		$data = Request::instance()->post(false);
		//判断
		if( !isset($data['id']) || !isset($data['sort']) || !isset($data['operate']) || !isset($data['rank']) ){
			$dataReturn =  return_format('',50000,lang('50000')) ;
		}
		//模拟测试
		// $data = array('id'=>7,'sort'=>7,'operate'=>0,'rank'=>1);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->categorySort($data, 1);
		$this->ajaxReturn($dataReturn);
	}

	//获取班级附表中的班级数据列表
	public function getClasseslist(){
        $data = [];

        $coursename = Request::instance()->post('coursename');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [

            'coursename'=>$coursename ? $coursename : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_class_list'],
        ];
        $course = new CourseManage();
        $res = $course->getClasseslist($data);
        $this->ajaxReturn($res);
        return $res;     		
	}

	//获取班级附表中的班级的总数目(没有筛选)
	public function getClasseslistTotalCount(){
        $course = new CourseManage();
        $res = $course->getClasseslistTotalCount();
        $this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * @ //强制上下架班级
	 * @Author zzq*
	 * @param $id 当前的班级id
	 * @param $status 是否上架 1表示上架 0表示下架
	 **/
	public function doOnOrOffClass(){
		$id = Request::instance()->post('id');
		$status = Request::instance()->post('status');

		$course = new CourseManage();
        $res = $course->doOnOrOffClass($id,$status);
        $this->ajaxReturn($res);
        return $res; 
	}


}