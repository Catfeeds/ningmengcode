<?php
/**
 * 课程模块.
 * User: jcr
 * Date: 14-3-11
 * Time: PM5:41
 */
namespace app\admin\controller;
use app\admin\business\Classesbegin;
use app\admin\business\CurriculumModule;
use app\admin\business\Docking;
use app\admin\business\TimingTask;
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

class Course extends Authorize {

	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
		header('Access-Control-Allow-Origin: *');
	}

	/**
	 * 课程列表接口
	 * @Author jcr
	 * 提交方式 get
	 * @param  status  0下架 1上架
	 * @param  coursename 课程名称
	 * POST | URL:/admin/Course/getCurricukumList
	 */
	public function getCurricukumList() {
		//实例化课程逻辑层
		$data = Request::instance()->POST(false);
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		//模拟测试
		// $data = array('status'=>1,'coursename'=>'听我讲土话','limit'=>1);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCurricukumlists($data, 20);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 课程列表上方 统计各状态的课程数量
	 * @return array();
	 * POST | URL:/admin/Course/getCurricukumCounts
	 */
	public function getCurricukumCounts() {
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCurricukumCounts();
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}


	/**
	 * checkClass 检查开课分类 和 添加班级课程
	 * @author Jcr
	 * @param  type  1 添加课程检查分类 2 开课检查课程
	 * @param  classtypes   检查课程类型
	 * @return array()
	 */
	public function checkClass(){
		$data = Request::instance()->post(false);
		$data = where_filter($data,['type','classtypes']);
		$classBegin = new Classesbegin();
		$dataReturn = $classBegin->checkClass($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程赠品
	 * @Author jcr
	 * @param  $data
	 */
	public function getListGfit() {
		$data = Request::instance()->post(false);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getListGfit($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * @ 课程模块的添加模块 第一步
	 * @Author jcr*
	 * @param $data 添加编辑数据源
	 * POST | URL:/admin/Course/addOneCurricukum
	 */
	public function addOneCurricukum() {
		$data = Request::instance()->post(false);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addOneCurricukum($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程模块的添加编辑 第二步
	 * @Author jcr*
	 * @param $data 添加编辑数据源
	 * POST | URL:/admin/Course/addTwoCurricukum
	 */
	public function addTwoCurricukum() {
		$data = Request::instance()->post();
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addTwoCurricukum($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程模块的添加编辑 第三步
	 * @Author jcr*
	 * @param $data 添加编辑数据源
	 * POST | URL:/admin/Course/addTriCurricukum
	 */
	public function addTriCurricukum() {
		$data = Request::instance()->post(false);
		//模拟测试

		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addTriCurricukum($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程模块  启用 禁用
	 * @Author jcr
	 * @param $data 添加编辑数据源
	 * POST | URL:/admin/Course/editCurricukum
	 */
	public function editCurricukum() {
		$data = Request::instance()->post(false);
		//模拟测试
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->eidtOperateCurricukum($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程模块 获取课程对应的班级列表
	 * @Author jcr
	 */
	public function getCurricuClass() {
		$data = Request::instance()->post(false);
		//模拟测试
		$class = new Classesbegin();
		$data['limit'] = isset($data['limit'])?$data['limit']:1;
		$dataReturn = $class->getCurricuClass($data,10);
		$this->ajaxReturn($dataReturn);
	}



	/**
	 * 获取 编辑课程详情数据回显
	 * @author jcr
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 * POST | URL:/admin/Course/getCurricukumEditId
	 */
	public function getCurricukumEditId() {
		$data = Request::instance()->POST(false);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCurricukumEditId($data['id']);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 获取 课程详情
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 * POST | URL:/admin/Course/getCurricukumId
	 */
	public function getCurricukumId() {
		$data = Request::instance()->POST(false);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCurricukumId($data['id']);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 获取课程对应的评论列表
	 * @php jcr
	 * @param  [type] $id 课程名称
	 * @param  [type] $id limit 第几页
	 * POST | URL:/admin/Course/getComment
	 */
	public function getComment() {
		$data = Request::instance()->POST(false);
		$curriculum = new CurriculumModule();
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$dataReturn = $curriculum->getCommentList($data, 10);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 添加分类
	 * @Author jcr
	 * @param $data['categoryname']分类名称
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/admin/Course/addCategory
	 **/
	public function addCategory() {
		$data = Request::instance()->post(false);
		//模拟测试
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addCategory($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台分类列表 和查询后台子类
	 * @Author jcr
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/admin/Course/getCategoryIdList
	 **/
	public function getCategoryIdList() {
		$data = Request::instance()->POST(false);
		//模拟测试
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCategoryIdList($data, 20);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台添加课程模块 分类联动
	 * @Author jcr
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/admin/Course/getCurricukumCategoryList
	 **/
	public function getCurricukumCategoryList() {
		$data = Request::instance()->POST(false);
		//模拟测试
		$data['limit'] = 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCategoryIdList($data, 100);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 编辑分类 是否启用 删除 名称编辑
	 * @Author jcr
	 * @param id 分类id
	 * @param status 1显示 0不显示
	 * @param categoryname 名称
	 * POST | URL:/admin/Course/editCategoryId
	 **/
	public function editCategoryId() {
		$data = Request::instance()->post(false);
		//模拟测试
		// $data = array('id'=>1,'categoryname'=>12112,'status'=>1);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCategory($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 分类删除
	 * @Author jcr
	 * @param id 分类id
	 * POST | URL:/admin/Course/deleteCategory
	 **/
	public function deleteCategory() {
		$data = Request::instance()->POST(false);
		//模拟测试
		// $data = array('id'=>1,'delflag'=>0);
		$data['delflag'] = 0;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCategory($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 获取要删除的分类对应的课程列表
	 * @Author jcr
	 * @param id 分类id
	 * POST | URL:/admin/Course/deleteCategory
	 **/
	public function getCatergoryCurricu() {
		$data = Request::instance()->POST(false);
		//模拟测试
		// $data = array('id'=>1,'delflag'=>0);
		$data['limit'] = isset($data['limit']) ? $data['limit'] : 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCatergoryCurricu($data,10);
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
		//模拟测试
		// $data = array('id'=>7,'sort'=>7,'operate'=>0,'rank'=>1);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->categorySort($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 添加课程标签
	 * @Author jcr
	 * @param $data['categoryname']分类名称
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/admin/Course/addCoursetags
	 **/
	public function addCoursetags() {
		$data = Request::instance()->post(false);
		//模拟测试
		// $data = array('tagname'=>'呵呵','fatherid'=>3);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->addCoursetags($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台课程标签列表 和查询后台子课程标签
	 * @Author jcr
	 * @param $data['fatherid'] 父级分类id
	 * POST | URL:/admin/Course/getCoursetagsIdList
	 **/
	public function getCoursetagsIdList() {
		$data = Request::instance()->POST(false);
		//模拟测试
		// $data = array('fatherid'=>3,'limit'=>1);

		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCoursetagsIdList($data, 20);
//        var_dump($dataReturn);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 编辑课程标签 是否启用 删除 名称编辑
	 * @Author jcr
	 * @param id 标签id
	 * @param status 1显示 0不显示
	 * @param tagname 名称
	 * POST | URL:/admin/Course/editCoursetagsId
	 **/
	public function editCoursetagsId() {
		$data = Request::instance()->post(false);
		//模拟测试
		// $data = array('id'=>1,'tagname'=>12112,'status'=>1);

		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCoursetags($data);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 课程标签删除
	 * @Author jcr
	 * @param id 标签id
	 * POST | URL:/admin/Course/deleteCoursetags
	 **/
	public function deleteCoursetags() {
		$data = Request::instance()->POST(false);
		//模拟测试
		$data['delflag'] = 0;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->editCoursetags($data);
		$this->ajaxReturn($dataReturn);
		// $this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台标签列表 上下移动
	 * @Author jcr*
	 * @param $data['id'] 标签id
	 * @param $data['operate'] 标签操作 0上移 1下移
	 * @param $data['fatherid'] 标签操作 级别
	 * @param $data['sort'] 标签操作 当前排序值
	 * POST | URL:/admin/Course/shiftCategory
	 **/
	public function shiftCoursetags() {
		$data = Request::instance()->post(false);
		//模拟测试
		// $data = array('id'=>7,'sort'=>11,'operate'=>0,'fatherid'=>3);
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->coursetagsSort($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ 后台课程添加 标签树
	 * @author  JCR
	 * @return array()
	 * POST | URL:/admin/Course/getCoursetagsTree
	 */
	public function getCoursetagsTree() {
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->coursetagsTree();
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ PC 后台开课列表
	 * @author  JCR
	 * @param name 课程名称
	 * @param type 班级类型
	 * $return array()
	 * POST | URL:/admin/Course/getSchedulingList
	 */
	public function getSchedulingList() {
		$data = Request::instance()->POST(false);
		$data['type'] = 2;
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getSchedulinglists($data, 20);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 开课选择 课程列表接口
	 * @Author jcr
	 * 提交方式 POST
	 * @param  status  0下架 1上架
	 * @param  coursename 课程名称
	 * POST | URL:/admin/Course/getCurricukumList
	 */
	public function getCurricukum() {
		//实例化课程逻辑层
		//        if(IS_AJAX){
		$data = Request::instance()->POST(false);
		//模拟测试
		// $data = array('coursename'=>'听我讲土话','limit'=>1);

//		$data['status'] = 1;
		$data['classtypes'] = 2;
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$curriculum = new CurriculumModule();
		$dataReturn = $curriculum->getCurricukumlists($data, 20);
		$this->ajaxReturn($dataReturn);
		// }
	}

	/**
	 * @ PC 查询授课老师
	 * @php jcr
	 */
	public function getTeacherList() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getTeacherList($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * 资源模块老师列表
	 * @author jcr
	 */
	public function getFileTeacher(){
		$data = Request::instance()->post(false);
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getFileTeacher($data,20);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * 班级对应的老师列表
	 * @author jcr
	 */
	public function getStudentList(){
		$data = Request::instance()->post(false);
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getStudentList($data,20);
		$this->ajaxReturn($dataReturn);
	}



	/**
	 * [getSchedulingInfo 开班详情返回页]
	 * @param   $curriculumid 课程id
	 * @param   $id   开班id
	 * @param   $type 课程类型
	 */
	public function getSchedulingInfo() {
		$data = Request::instance()->post(false);
		// $data = array('type'=>2,'curriculumid'=>32,'id'=>41);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getSchedulingInfo($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * @ 开班第一步
	 * @php JCR
	 * @param  $data
	 * @return []
	 */
	public function addOneSchedu(){
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->oneEdit($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * @ PC 后台开班第二步
	 * @php jcr
	 * @param [type] $[name] [description]
	 * @return array
	 */
	public function addEditScheduling() {
		header('Access-Control-Allow-Origin: *');
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$data['classnum'] = 0;
		$dataReturn = $classesbegin->addEdit($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 *
	 */
	/**
	 * [getTimeOccupy 获取对应老师的占用时间]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getTimeOccupy() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getTimeOccupy($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [enrollStudent 上下架]
	 * @param  id       id 开课表id
	 * @param  $status  0是暂停招生，1是未暂停招生
	 * @return [type] [description]
	 */
	public function enrollStudent() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->enrollStudent($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [deleteScheduling 删除开课信息]
	 * @param   $[id] [开课id]
	 * @return [type] [description]
	 */
	public function deleteScheduling() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->deleteScheduling($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * [getSchedulingOrder 获取班级对应的订单列表]
	 * @param   $[id] [开课id]
	 * @return [type] [description]
	 */
	public function getSchedulingOrder() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$data['limit'] = isset($data['limit'])?$data['limit']:1;
		$dataReturn = $classesbegin->getSchedulingOrder($data,10);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [fileAdd 添加文件夹]
	 * @return [type] [description]
	 */
	public function fileAdd() {
		$data = Request::instance()->post();
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->addFiles($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [getFileList 文件夹列表和 资源列表]
	 * @param  $showname 文件夹名称
	 * @param  $limit    第几页
	 * @return [type] [description]
	 */
	public function getFileList() {
		$data = Request::instance()->post(false);
		$data['limit'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getFileList($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [deleteFile 删除课件]
	 * @param  $fileid [素材id]
	 * @return [type] [description]
	 */
	public function deleteFile() {
		$data = Request::instance()->post(false);
		$data['delflag'] = 0;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->deleteFile($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 编辑机构信息
	 * @return [type] [description]
	 */
	public function editOrgan() {
		$data = Request::instance()->post(false);
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->editConfig($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * 获取课时对应的 教室学生列表
	 */
	public function getClassStudent(){
		$data = Request::instance()->post(FALSE);
		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
		$classesbegin = new Classesbegin();
		$dataReturn = $classesbegin->getClassStudent($data,20);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * 学生消息发送
	 * @author JCR
	 */
	public function sendMessage(){
		$data = Request::instance()->post(FALSE);
		$classBegin = new Classesbegin();
		$dataReturn = $classBegin->sendMessage($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * 课时学生列表
	 * @author JCR
	 */
	public function getLessonsStudent(){
		$data = Request::instance()->post(false);
		$classBegin = new Classesbegin();
		$data['pagenum'] = isset($data['pagenum'])?$data['pagenum']:1;
		$dataReturn = $classBegin->getLessonsStudent($data,20);
		$this->ajaxReturn($dataReturn);
	}




	/**
	 * [deleteFile 预约课堂
	 * @param  $fileid [素材id]
	 * @return [type] [description]
	 */
	public function addRoom() {
//		Db::table('nm_organaccount')->insert(['tradeflow'=>0,'usablemoney'=>0,'frozenmoney'=>0,'organid'=>1]);
//	    Cache::clear();
//	    exit();
		$data = Request::instance()->post(false);
		$docking = new Docking();
//		$dataReturn = $docking->addClassRoom($data,$this->organid);
//		var_dump(date('i'));

//		$timing = new TimingTask();
//		$dataReturn = $timing->setOrderTime();
		TimingTask::savelog();

//	   	$crond = new \app\admin\lib\Crond();
//	   	$crond->doCron();

//		dump(
//			array_diff([1,2,3],[1,2])
//		);

//		$docking->ceshiUploadFiles(1,"./static/timg.jpeg","哈哈哈",  1, 0);
//		$docking->uploadToFiles(1);
//		$docking->updateTk();
	}

	/**
	 * [getTimeStatus 定时任务 实施更新对应的课程状态]
	 * @return [type] [description]
	 */
	public function getTimeStatus() {
		$timing = new TimingTask();
		$dataReturn = $timing->getTimeStatus();
	}


	/**
	 * [send 短信发送测试]
	 * @return [type] [description]
	 */
	public function send() {
		$sends = new \Messages();
//		$sends = $sends->sendMeg(18610374671, 4, ['22222', '10']);
		$sends = $sends->addSign('月月教育',1);
		dump($sends);
	}

	// 腾讯云推送测试
	public function sendpush() {
		$TencentPush = new TencentPush();
		// var_dump($TencentPush->addTag('1',['11','2'],'1'));
		// var_dump($TencentPush->addAttr(1,['sex'=>'1'],1));
		// var_dump($TencentPush->pushUserId(1,'呢哈哈哈','2222'));
		// var_dump(getAttr());
		var_dump($TencentPush->addTag('tag', ['标签', '标签2'], 1));
		var_dump($TencentPush->pushUserTag('tag', ['标签', '标签2'], 1, '阿萨德', '阿大声道'));

		// var_dump($TencentPush->getsig());
	}

	public function jpushSend() {
		$jpush = new JPushs();
		// $info = $jpush->pushUserTag('tag',[1],1,'呵呵','丢失的记忆');
		// $info = object_to_array($info);
		// dump($info);

		$lis = $jpush->addTags('12122132131231231232112', 1, 1);
		$lis = object_to_array($lis);
		dump($lis);
	}




}
