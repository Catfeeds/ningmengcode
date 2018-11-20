<?php

/**
 * 课程模块.
 * User: lc
 * Date: 14-3-11
 * Time: PM5:41
 */
namespace app\admin\controller;
use app\admin\business\HomeworkManage;
use login\Authorize;
use think\Controller;
use think\Request;
use think\Log;
use app\admin\lib;

class Homework extends Authorize
{
	
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
		header('Access-Control-Allow-Origin: *');
//		header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
	}
	
	/**
	 * 
	 * @Author 
	 * 提交方式 post
	 * @param  status  
	 * @param  
	 * POST | URL:/admin/Homework/courseCategoryListOne
	 */
	public function courseCategoryListOne() {
		//$data = Request::instance()->POST(false);
		$homework = new HomeworkManage();
		$dataReturn = $homework->getcourseCategoryListOne();
		$this->ajaxReturn($dataReturn);
		return $dataReturn;
	}
	
	/**
	 * 
	 * @Author 
	 * 提交方式 post
	 * @param  status  
	 * @param  
	 * POST | URL:/admin/Homework/courseCategoryListTwo
	 */
	public function courseCategoryListTwo() {
		$data = Request::instance()->POST(false);
		$homework = new HomeworkManage();
		$dataReturn = $homework->getcourseCategoryListTwo($data);
		$this->ajaxReturn($dataReturn);
		return $dataReturn;
	}
	
	/**
	 * 
	 * @Author 
	 * 提交方式 post
	 * @param  status  
	 * @param  
	 * POST | URL:/admin/Homework/getSchedulinglessonList
	 */
	public function getSchedulinglessonList() {
		$data = Request::instance()->POST(false);
		$homework = new HomeworkManage();
		$dataReturn = $homework->getSchedulinglessonList($data, 20);
		$this->ajaxReturn($dataReturn);
		return $dataReturn;
	}
	
	/**
	 * 学生交作业明细
	 * @param classid
	 * @param lessonid
	 */
	public function getStudentHomeworkList(){
		$classid    = $this->request->param('classid');
		$lessonid    = $this->request->param('lessonid');
		//$classid = $lessonid = 1;
		$nickname    = $this->request->param('nickname');
		$pagenum  = $this->request->param('pagenum');
		
    	$limit = config('param.pagesize')['adminStudent_Taglist'];
		$homework = new HomeworkManage();
		$HomeworkList = $homework->getStudentHomeworkList($classid,$lessonid,$nickname,$pagenum,$limit);
		$this->ajaxReturn($HomeworkList);
        return $HomeworkList;
	}
	
	/**
     * 预览习题
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Homework/ExerciseInfo
	 */
    public function previewExercise(){
    	$classid = $this->request->param('classid');
    	$lessonid = $this->request->param('lessonid');
    	$manageobj = new HomeworkManage;
    	$Exerciseinfo = $manageobj->previewExercise($classid, $lessonid);
        $this->ajaxReturn($Exerciseinfo);
        return $Exerciseinfo;
    }

	/**
     * 查看作业
     * @Author lc
     * @param
     * @return 
     * URL:/admin/Homework/Viewhomework
	 */
    public function viewHomework(){
    	//$data = Request::instance()->POST(false);
        $classid = $this->request->param('classid');
		$lessonid = $this->request->param('lessonid');
        $studentid = $this->request->param('studentid');
		$status = $this->request->param('status');
    	$manageobj = new HomeworkManage;
    	$homeworkinfo = $manageobj->viewHomeworkinfo($classid, $lessonid, $studentid, $status);
        $this->ajaxReturn($homeworkinfo);
        return $homeworkinfo;
    }
}