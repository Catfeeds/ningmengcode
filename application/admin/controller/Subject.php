<?php

/**
 * 课程模块.
 * User: lc
 * Date: 14-3-11
 * Time: PM5:41
 */
namespace app\admin\controller;
use app\admin\business\SubjectManage;
use JPushs;
use Keyless;
use Messages;
use TencentPush;
use login\Authorize;
use think\Controller;
use think\Request;
use think\Log;
use app\admin\lib;
use login\Particle;
use think\Cache;

class Subject extends Authorize
{
	
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
		header('Access-Control-Allow-Origin: *');
//		header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
	}
	
	/**
	 * 课程列表接口
	 * @Author 
	 * 提交方式 post
	 * @param  status  0下架 1上架
	 * @param  coursename 课程名称
	 * POST | URL:/admin/Subject/getCurricukumList
	 */
	public function getCurricukumList() {
		//实例化课程逻辑层
		$data = Request::instance()->POST(false);
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$subject = new SubjectManage();
		$dataReturn = $subject->getCurricukumlists($data, 20);
		$this->ajaxReturn($dataReturn);
	}
	
	/**
	 * 习题列表
	 * @param courseid
	 */
	public function getExerciseList(){
		$courseid    = $this->request->param('courseid');
		$periodname    = $this->request->param('periodname');
		$pagenum  = $this->request->param('pagenum');
		
    	$limit = config('param.pagesize')['adminStudent_Taglist'];
		$subject = new SubjectManage();
		$ExerciseList = $subject->getExerciseList($courseid,$periodname,$pagenum,$limit);
		$this->ajaxReturn($ExerciseList);
        return $ExerciseList ;
	}
	
	/**
     * 查看习题
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Subject/ExerciseInfo
	 */
    public function getExerciseinfo(){
    	$id = $this->request->param('id');
    	$manageobj = new SubjectManage;
    	$Exerciselist = $manageobj->getExerciseInfo($id);
        $this->ajaxReturn($Exerciselist);
        return $Exerciselist ;
    }
   
    /**
     * 编辑习题时获取习题的信息
     * @Author lc
     * @param 使用id 做查询
     * @return 
     * URL:/admin/Subject/getExerciseMsg
     */
    public function getExerciseMsg(){
		$id = $this->request->param('id');
        $manageobj = new SubjectManage;
        $Exerciselist = $manageobj->getExerciseMsg($id);
        $this->ajaxReturn($Exerciselist);
        return $Exerciselist ;
    }

	/**
	 * 录入习题
	 */
	public function addExercise(){
		$data = Request::instance()->post(false);

		$subject = new SubjectManage();
		$dataReturn = $subject->addExercise($data);
		$this->ajaxReturn($dataReturn);
        return $dataReturn;
		
	}
	
	/**
	 * 检查课时是否已录入习题
	 */
	public function checkHaveExercise(){
		$data = Request::instance()->post(false);
		$subject = new SubjectManage();
		$dataReturn = $subject->checkHaveExercise($data);
		$this->ajaxReturn($dataReturn);
        return $dataReturn;
	}
	
	/**
	 * 录入习题时获取直播课程列表
	 */
	public function getAllCurriculum(){
		$data = Request::instance()->post(false);

		$subject = new SubjectManage();
		$dataReturn = $subject->getAllCurriculum($data);
		$this->ajaxReturn($dataReturn);
        return $dataReturn;
		
	}
	
	/**
	 * 录入习题时获取课时列表
	 */
	public function getPeriodList(){
		$data = Request::instance()->post(false);

		$subject = new SubjectManage();
		$dataReturn = $subject->getPeriodList($data);
		$this->ajaxReturn($dataReturn);
        return $dataReturn;
		
	}
	
	/**
	 * 编辑习题
	 */
	public function updateExercise(){
		$data = Request::instance()->post(false);

		$subject = new SubjectManage();
		$dataReturn = $subject->updateExercise($data);
		$this->ajaxReturn($dataReturn);
        return $dataReturn;
		
	}
	
	/**
     * [删除前判断该习题是否有班级正在使用]
     * @Author lc
     * @return   [type]      [description]
     */
    public function checkHaveSchedulingLesson(){
        $id = $this->request->param('id');
		
        $manageobj = new SubjectManage;
        $checkflag = $manageobj->checkHaveSchedulingLesson($id);
        $this->ajaxReturn($checkflag);
        return $checkflag ;

    }
	
	/**
     * [deleteKnowledge 删除]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/StudentKnowledge/deleteKnowledge
     */
    public function deleteExercise(){
        $id = $this->request->param('id');
        $manageobj = new SubjectManage;
		
        //获取知识列表信息,默认分页为5条
        $delflag = $manageobj->delExercise($id);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
}