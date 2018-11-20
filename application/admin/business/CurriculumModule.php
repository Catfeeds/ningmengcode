<?php
/**
 * 课程业务逻辑层
 */
namespace app\admin\business;
use app\admin\model\Category;
use app\admin\model\Coursecomment;
use app\admin\model\Coursetagrelation;
use app\admin\model\Coursetags;
use app\admin\model\Curriculum;
use app\admin\model\Filemanage;
use app\admin\model\Ordermanage;
use app\admin\model\Period;
use app\admin\model\Scheduling;
use app\admin\model\Studentinfo;
use app\admin\model\Teacherinfo;
use app\admin\model\Unit;
use app\admin\model\Coursegift;
use app\admin\model\Organ;
use Messages;
use think\Validate;

class CurriculumModule {

	function __construct() {
	}

	/**
	 * @课程列表
	 * @Author jcr
	 * @param $where 查询条件
	 * @param $pagenum 每页显示行数
	 * @param $limit 查询页数
	 **/
	public function getCurricukumlists($data, $pagenum) {
		$curriculum = new Curriculum();
		$list = $curriculum->getAdminCurriculumList($data, $pagenum);

		if ($list['data']) {
			$category = new Category();
			$classtypes = ['1'=>'录播课','2'=>'直播课'];
			foreach ($list['data'] as $key => &$val) {
				//处理分类
				$val['categoryname'] = $category->getCategoryName(explode('-', $val['categorystr']));
				$val['classtypesstr'] = $val['classtypes']?$classtypes[$val['classtypes']]:'-';
				$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
			}
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10001, lang('error_log'));
		}
	}

	/**
	 * 课程列表上方 统计各状态的课程数量
	 * @return array()
	 */
	public function getCurricukumCounts() {
		$curriculum = new Curriculum();
		// 下架数据
		$list['soldoutnum'] = $curriculum->getStatusNum(0);
		// 上架数据
		$list['putawaynum'] = $curriculum->getStatusNum(1);
		// 全部数据
		$list['allsum'] = $list['soldoutnum'] + $list['putawaynum'];
		return return_format($list, 0, lang('success'));
	}

	/**
	 * @ 添加课程 第一步
	 * @Author jcr
	 * @param $data 课程添加数据源
	 */
	public function addOneCurricukum($data) {
		$data = where_filter($data,array('id','coursename','subhead','imageurl','generalize','categoryid','classtypes','categorystr','teacherid','labellist'));
		$curriculum = new Curriculum();
		$info = [];
		if (isset($data['id'])&&$data['id']) {
			$info = $curriculum->getSelectId($data['id']);
			if (!$info) {
				return return_format('', 10002, lang('param_error'));
			}
		}
		// 过滤分类里的0
		$cateArr = array_filter(explode('-',$data['categorystr']));
		$data['categorystr'] = implode('-',$cateArr);

		//数据效验
		$validate = new Validate($curriculum->rule, $curriculum->message);
		if (!$validate->check($data)) {
			return return_format('', 10003, $validate->getError());
		}

		$cate = new Category();
		if(!$cate->getInId($cateArr[count($cateArr)-1])){
			return return_format('', 10122, lang('10122'));
		}

		if($data['classtypes'] == 1 && !isset($data['teacherid'])){
			return return_format('', 10526, lang('10526'));
		}

		//if (!$data['labellist']) {
		//	return return_format('', 10004, '课程标签必须添加');
		//}

		$infomsg = $curriculum->addOne($data, $info);

		return ['data' => '', 'code' => $infomsg['code'], 'info' => $infomsg['info'], 'id' => isset($infomsg['id']) ? $infomsg['id'] : 0];
	}

	/**
	 * @ 添加课程 第二步
	 * @Author jcr
	 * @param $data 课程添加数据源
	 */
	public function addTwoCurricukum($data) {
		if(!isset($data['id']))  return return_format('', 10145, lang('param_error'));
		$curriculum = new Curriculum();
		// 检索对应的课程是否存在
		$info = $curriculum->getSelectId($data['id']);
		if (!$info) {
			return return_format('', 10009, lang('param_error'));
		}

		//数据效验课时
		if (!$data['inperiod']) {
			return return_format('', 10010, lang('10010'));
		}

		foreach ($data['inperiod'] as $k => $v) {
			if (!trim($v['unitname'])) {
				$code = str_replace('1',$v['unitsort'],lang('10011'));
				return return_format('', 10011, $code);
			}
			if (!$v['list']) {
				$code = str_replace('1',$v['unitsort'],lang('10012'));
				return return_format('', 10012, $code);
			}

			// 数据效验课程单元课时
			foreach ($v['list'] as $i => $val) {
				if (!$val['periodname']) {
					$code = str_replace(['1@','2@'],[$v['unitsort'],$val['periodsort']],lang('10013'));
					return return_format('', 10013, $code);
				}

				// 对课件id 进行处理
				$courseware = $val['courseware']?array_unique(array_column($val['courseware'],'id')):[];
				$countCourseware = count($courseware);
				if($info['classtypes']==1 && $countCourseware==0){

					$code = str_replace(['1@','2@'],[$v['unitsort'],$val['periodsort']],lang('10130'));
					return return_format('', 10130, $code);
				}else if($info['classtypes']==1 && $countCourseware>1){
					$code = str_replace(['1@','2@'],[$v['unitsort'],$val['periodsort']],lang('10155'));
					return return_format('', 10155, $code);
				}
				$data['inperiod'][$k]['list'][$i]['courseware'] = implode('-',$courseware);
			}
		}
		$info = $curriculum->addTwo($data, $info);
		return return_format('', $info['code'], $info['info']);
	}

	/**
	 * @ 添加课程 第三步
	 * @Author jcr
	 * @param $data 课程添加数据源
	 */
	public function addTriCurricukum($data) {
		$data = where_filter($data,['id','price','status','giftstatus','gift']);
		$curriculum = new Curriculum();

		// 检索对应的课程是否存在
		$info = $curriculum->getSelectId($data['id']);
		if (!$info) {
			return return_format('', 10024, lang('param_error'));
		}

		if ($info['schedule'] == 0) {
			return return_format('', 10025, lang('10025'));
		}

		// 兼容页面字段顺序效验
		if($info['classtypes']==1){
			$validate = new Validate($curriculum->rule1, $curriculum->message1);
			if (!$validate->check($data)) {
				return return_format('', 10026, $validate->getError());
			}
			if($data['price']<0){
				return return_format('', 10499, lang('10499'));
			}
		}
		if(isset($data['giftstatus']) && $data['giftstatus']===0){
			if(!isset($data['gift']) || !$data['gift']){
				return return_format('', 11010, lang('11010'));
			}
			foreach ($data['gift'] as $k => $v){
				if(!isset($v['num']) || !is_int($v['num']) || $v['num'] <= 0){
					return return_format('', 11011, lang('11011'));
				}
			}
			$data['giftjson'] = json_encode($data['gift']);
		}else{
			$data['giftjson'] = '';
		}

		$info = $curriculum->addTri($data, $info);
		return return_format('', $info['code'], $info['info']);
	}

	/**
	 * @ 课程 上下架 删除
	 * @Author jcr
	 * @param $data 课程添加数据源
	 */
	public function eidtOperateCurricukum($data) {
		$data = where_filter($data,array('id','status','delflag'));
		$curriculum = new Curriculum();
		$info = $curriculum->getSelectId($data['id']);
		if (!$info) {
			return return_format('', 10028, lang('param_error'));
		}

		if(isset($data['delflag']) && $info['status'] == 1){
			// 课程再上架状态下不允许删除
			return return_format('', 10144, lang('10144'));
		}else if(isset($data['delflag'])){
			// 查看该课程下是否有班级
			$sched = new Scheduling();
			if($sched->getCount(['delflag'=>1,'curriculumid'=>$data['id']])){
				return return_format(1,  11017, lang('11017'));
			}
		}

		if(isset($data['status']) && $data['status'] == 1 && $info['schedule'] != 2){
			return return_format('', 10029, lang('10029'));
		}

		$info = $curriculum->eidtOperate($data);
		return return_format('', $info['code'], $info['info']);
	}

	/**
	 * 获取 课程编辑详情
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getCurricukumEditId($id) {

		$curriculum = new Curriculum();
		$category = new Category();
		// $coursetagrelation = new Coursetagrelation();

		$info = $curriculum->getSelectId($id);
//		$info['generalize'] = $info['generalize']?htmlspecialchars($info['generalize']):'';
		if (!$info) return return_format('', 10031, lang('10031'));

		// 处理分类格式
		$info['categorystrname'] = $category->getCategoryName(explode('-', $info['categorystr']));
		// 处理课程标签
//		$info['labellist'] = $coursetagrelation->getArrId($info['id']);
		// 处理课时单元和对应课时并返回结果集
		$info = $this->getUnitPeriod($info,true);
		//$info['classtypes'] = array_filter(explode(',', $info['classtypes']));

		if($info['classtypes']==1){
			$teacher = new Teacherinfo();
			$info['teachername'] = $teacher->getTeacherId($info['teacherid'],'teacherid,nickname')['nickname'];
		}

		return return_format($info, 0, lang('success'));
	}

	/**
	 * 获取 课程详情
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getCurricukumId($id) {

		$curriculum = new Curriculum();
		$category = new Category();
		$coursetagrelation = new Coursetagrelation();
		$scheduling = new Scheduling();
		$ordermanage = new Ordermanage();

		$info = $curriculum->getSelectId($id);
		if (!$info) return return_format('', 10032, lang('10032'));



//		$info['generalize'] = $info['generalize']?htmlspecialchars($info['generalize']):'';

		// 处理分类格式
		$info['categorystrname'] = $category->getCategoryName(explode('-', $info['categorystr']));
		// 处理课程标签
		$info['labellist'] = $coursetagrelation->getArrId($info['id']);
		// 解析时间
		$info['addtimestr'] = date('Y-m-d H:i:s', $info['addtime']);
		// 解析状态
		$info['statusstr'] = $info['status'] ? '已上架' : '已下架';
//		$info['classtypes'] = explode(',', $info['classtypes']);
		// 处理课时单元和对应课时并返回结果集
		$info = $this->getUnitPeriod($info,true);
		$info['unitnumstr'] = numtochr($info['unitnum'], true);

		//获取课程 招生班级数
		$info['recruitnum'] = $scheduling->getRecruitCount($info['id']);

		//查询已购买次数
		$info['payordernum'] = $ordermanage->getPayOrderCount($info['id']);

		if($info['classtypes']==1){
			$order = new Ordermanage();
			$where = [
				'o.orderstatus' => ['gt',10],
				'o.curriculumid' => $id,
			];
			$list = $order->getOrderAccountList($where,1,500);
			$listdata = [];
			if($list){
				foreach ($list as $k => $v){
					$listdata[] = [
						'studentid' => $v['studentid'],
						'studentname'=> $v['studentname'],
						'prphone'	 => $v['prphone'],
						'mobile'	 => $v['mobile'],
					];
				}
			}
			$info['studentList'] = $listdata;
		}else{
			$info['studentList'] = [];
		}

		if($info['giftjson']){
			$info['giftjson'] = json_decode($info['giftjson'],TRUE);
			$course = new Coursegift();
			$list = $course->getlist(['id'=>['in',implode(',',array_column($info['giftjson'],'id'))]]);
			$list = array_column($list,'name','id');
			foreach ($info['giftjson'] as $k => &$v){
				$v['name'] = isset($list[$v['id']])?$list[$v['id']]:'-';
			}
		}

		if($info['classtypes']==1){
			$teacher = new Teacherinfo();
			$info['teachername'] = $teacher->getTeacherId($info['teacherid'],'teacherid,nickname')['nickname'];
		}


		return return_format($info, 0, lang('success'));
	}

	/**
	 * @php jcr
	 * $param $data
	 * $pagenum 查询页数
	 */
	public function getCommentList($data, $pagenum) {
		$data = where_filter($data,['id','pagenum','lessonsid','limit']);
		$coursecomment = new Coursecomment();

		$where = ['c.curriculumid' => $data['id']];
		if(isset($data['lessonsid'])){
			$where['c.lessonsid'] = $data['lessonsid'];
		}

		$list = $coursecomment->getList($where, $data['limit'], $pagenum);
		if ($list['data']) {
			$student = new StudentInfo();
			$teacher = new TeacherInfo();
			$classtype = array('1' => '一对一', '2' => '小班', '3' => '大班');
			foreach ($list['data'] as $k => &$v) {
				$v['addtimestr'] = date('Y-m-d H:i:s', $v['addtime']);
				$v['studentinfo'] = $student->getStudentId($v['studentid'], 'imageurl,nickname');
				$v['teacherinfo'] = $teacher->getTeacherId($v['allaccountid'], 'teachername,imageurl,nickname');
				$v['classtypestr'] = $classtype[$v['classtype']];
			}
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10034, lang('error_log'));
		}
	}

	/**
	 * @添加分类
	 * @Author jcr
	 * @param $data 添加数据源
	 * @return
	 **/
	public function addCategory($data) {
		if (!$data || !isset($data['fatherid'])) {
			return return_format('', 10035, lang('10035'));
		}

		$category = new Category();
		$counts = $category->getCategoryCount(['fatherid' => $data['fatherid'], 'delflag' => 1]);
		if($data['fatherid'] == 0 && $counts>= 6){
			return return_format('', 11036, lang('11036'));
		}else if ($counts >= 10) {
			return return_format('', 10036, lang('10036'));
		}
		$data['categoryname'] = isset($data['categoryname'])?trim($data['categoryname']):'';
		if(!$data['categoryname']){
			return_format('', 10117, lang('10117'));
		}
		return $this->addModel($category, $data, 'id,rank,fatherid,path');
	}

	/**
	 * @ 查询分类
	 * @Author jcr
	 * @param $fatherid 父分类id
	 * @param $fatherid $limit 第几页
	 * @param $fatherid $pagenum 一页几条数据
	 *
	 */
	public function getCategoryIdList($data, $pagenum, $orderby = 'sort asc') {
		if (!$data['fatherid']) {
			$data['fatherid'] = 0;
		}

		$category = new Category();
		$where = ['fatherid' => $data['fatherid'], 'delflag' => 1];
		$field = 'id,categoryname,sort,rank,fatherid,status,imgs,describe,icos,icostwo';

		$catlist = $category->getCategoryList($where, $field, $orderby, $data['limit'], $pagenum);
		if ($catlist) {
			$caterank = array('1' => '一级', '2' => '二级', '3' => '三级');
			foreach ($catlist as $k => &$v) {
				$v['rankstr'] = $caterank[$v['rank']];
				//获取下级子集数量
				$v['juniorcount'] = $category->getCategoryCount(['fatherid' => $v['id'], 'delflag' => 1]);
			}
			$count = $category->getCategoryCount($where);
			$returndata = ['data' => $catlist, 'pageinfo' => array('pagesize' => $pagenum, 'pagenum' => $data['limit'], 'total' => $count)];
			return return_format($returndata, 0, lang('success'));
		} else {
			return return_format('', 10037, lang('error_log'));
		}
	}

	/**
	 * 编辑 状态切换 删除
	 * @Author jcr
	 * @param $fatherid 父分类id
	 */
	public function editCategory($data) {
		if (!$data || !isset($data['id'])) {
			return return_format('', 10038, lang('param_error'));
		}
		$category = new Category();

		if(isset($data['delflag'])||isset($data['status'])){
			// 删除
			$ids = $category->getCategoryAllId($data['id'],$data['id']);
			$curricu = new Curriculum();
			if($curricu->getCurriculumCount(['delflag'=>1,'categoryid'=>array('in',$ids)])){
				return return_format(1, 10039, lang('10039'));
			}
		}

		$add = $category->editAdd($data, false);
		if ($add['code'] == 0) {
//			if (isset($data['status'])) {
//				// 是否显示
//				$msg = $data['status'] ? '启用显示成功' : '隐藏显示成功';
//			} else if (isset($data['categoryname'])) {
//				//名称修改
//				$msg = '分类名称修改成功';
//			} else if (isset($data['delflag'])) {
//				//delflag 删除
//				$msg = '分类删除成功';
//			} else {
//				$msg = '操作成功';
//			}
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 10040, lang('error'));
		}
	}


	/**
	 * 获取要删除的分类对应的课程列表
	 * @param $data
	 * @param $limit
	 */
	public function getCatergoryCurricu($data,$pagenum){
		$data = where_filter($data,['limit','id']);
		if(!isset($data['id']) || $data['id'] == 0) return return_format('', 11014, lang('param_error'));
		$category = new Category();
		$ids = $category->getCategoryAllId($data['id'],$data['id']);
		if(!$ids) return return_format('', 11015, lang('param_error'));

		$curricu = new Curriculum();
		$where = ['delflag'=>1,'categoryid'=>array('in',$ids)];
		$list = $curricu->getCurriculumList($where,'id,coursename','id asc',$data['limit'],$pagenum);
		if($list){
			$count = $curricu->getCurriculumCount($where);
			$returnData = ['data'=>$list,'pageinfo'=>['pagesize'=>$pagenum,'pagenum'=>$data['limit'],'total'=>$count]];
			return return_format($returnData, 0, lang('success'));
		}else{
			return return_format('', 11016, lang('error_log'));
		}
	}

	/**
	 * 分类移动
	 * @Author jcr
	 * @param $data['id'] 分类id
	 * @param $data['operate'] 分类操作 0上移 1下移
	 * @param $data['rank'] 分类操作 级别
	 * @param $data['sort'] 分类操作 当前排序值
	 **/
	public function categorySort($data) {
		$category = new Category();
		$list = $category->getCategoryListSort($data);
		if (count($list) == 2) {
			// 交换数组参数键对应的值
			list($list[0]['sort'], $list[1]['sort']) = array($list[1]['sort'], $list[0]['sort']);
			$status = $category->editSoct($list);
			if ($status) {
				return return_format('', 0,  lang('success'));
			} else {
				return return_format('', 10041, lang('error'));
			}
		} else {
			$code = $data['operate']?10042:10043;
			return return_format('', $code, lang($code));
		}
	}

	/**
	 * @ 添加课程标签
	 * @Author jcr
	 * @param $data 添加数据源
	 **/
	public function addCoursetags($data) {
		if (!$data) {
			return return_format('', 10044, lang('10044'));
		}

		$coursetags = new Coursetags();

		$counts = $coursetags->getCoursetagsCount(['fatherid' => $data['fatherid'], 'delflag' => 1]);
		if ($counts >= 10) {
			return return_format('', 10045, lang('10045'));
		}

		$data['tagname'] = isset($data['tagname'])?trim($data['tagname']):'';
		if(!$data['tagname']){
			return_format('', 10116, lang('10116'));
		}
		return $this->addModel($coursetags, $data, 'id,fatherid,path');
	}

	/**
	 * @ 查询标签
	 * @Author jcr
	 * @param $fatherid 父分类id
	 * @param $fatherid $limit 第几页
	 * @param $fatherid $pagenum 一页几条数据
	 */
	public function getCoursetagsIdList($data, $pagenum) {
		$coursetags = new Coursetags();
		$catlist = $coursetags->getCoursetagsFatherid($data, $pagenum, $orderby = 'sort desc');

		if ($catlist['data']) {
			//获取上级标签名
			$tagname = $data['fatherid'] ? $coursetags->getId($data['fatherid'], 'id,tagname')['tagname'] : '';
			//处理数据结构
			foreach ($catlist['data'] as $k => &$v) {
				//获取下级子集数量
				$v['juniorcount'] = $coursetags->getCoursetagsCount(['fatherid' => $v['id'], 'delflag' => 1]);
				$v['fathertagname'] = $tagname;
				$v['addtimestr'] = date('Y-m-d H:i:s', $v['addtime']);
			}
			return return_format($catlist, 0, lang('success'));
		} else {
			return return_format('', 10111, lang('error_log'));
		}
	}

	/**
	 * 编辑标签 状态切换 删除
	 * @Author jcr
	 * @param $fatherid 父标签id
	 */
	public function editCoursetags($data) {
		if (!$data || !isset($data['id'])) {
			return return_format('', 10112, lang('param_error'));
		}

		$coursetags = new Coursetags();
		$add = $coursetags->editAdd($data, false);
		if ($add['code'] == 0) {
//			if (isset($data['status'])) {
//				// 是否显示
//				$msg = $data['status'] ? '启用成功' : '禁用成功';
//			} else if (isset($data['tagname'])) {
//				//名称修改
//				$msg = '标签名称修改成功';
//			} else if (isset($data['delflag'])) {
//				//delflag 删除
//				$msg = '分标签删除成功';
//			} else {
//				$msg = '操作成功';
//			}
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 10112, lang('error'));
		}
	}

	/**
	 * 标签移动
	 * @Author jcr
	 * @param $data['id'] 标签id
	 * @param $data['operate'] 标签操作 0上移 1下移
	 * @param $data['rank'] 标签操作 级别
	 * @param $data['sort'] 标签操作 当前排序值
	 **/
	public function coursetagsSort($data) {
		$coursetags = new Coursetags();
		$list = $coursetags->getCoursetagsListSort($data);
		if (count($list) == 2) {
			// 交换数组参数键对应的值
			list($list[0]['sort'], $list[1]['sort']) = array($list[1]['sort'], $list[0]['sort']);
			$status = $coursetags->editSoct($list);
			if ($status) {
				return return_format('', 0, lang('success'));
			} else {
				return return_format('', 10113, lang('error'));
			}
		} else {
			$code = $data['operate']?10114:10115;
			return return_format('', $code, lang($code));
		}
	}

	/**
	 * 获取机构课程标签树
	 * @return array()
	 */
	public function coursetagsTree() {
		$coursetags = new Coursetags();
		$list = $coursetags->getTree();
		if ($list) {
			// $count = count($list);
			// 过滤部分没上级的2级标签
			$arrOne = [];
			foreach ($list as $key => $value) {
				if ($value['fatherid'] == 0) {
					$arrOne[] = $value['id'];
				}
			}
			foreach ($list as $k => $v) {
				if ($v['fatherid'] != 0 && !in_array($v['fatherid'], $arrOne)) {
					unset($list[$k]);
				}
			}
			$list = array_values($list);
			$list = toTree($list, 'id', 'fatherid', '_child');
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10116, lang('error_log'));
		}
	}

	/**
	 * 添加模块摘出
	 * @php jcr
	 * @param $obj 传输对象
	 * @param $field  查询字段
	 * @return
	 */
	public function addModel($obj, $data, $field) {
		$arrpath = [];
		if ($data['fatherid']) {
			//有父级情况
			$info = $obj->getId($data['fatherid'], $field);
			if (!$info) {
				return return_format('', 10046, lang('10046'));
			} else {
				if (isset($info['rank'])) {
					$data['rank'] = $info['rank'] + 1;
				}
				$data['fatherid'] = $info['id'];
				// 处理树形上下级关系
				$arrpath = $info['path'] ? explode('-', $info['path']) : [];
				$arrpath[] = $info['id'];
				$data['path'] = str_path_join($arrpath);
			}
		} else {
			//无父级情况
			$data['fatherid'] = 0;
		}

		// 将处理好的分类数据载入
		$add = $obj->editAdd($data, false);
		if ($add['code'] == 0) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 10047, lang('error'));
		}
	}

	/**
	 * @Author jcr
	 * 处理课程课时
	 * $type 是否显示课件
	 */
	public function getUnitPeriod($info,$type=false) {
		$unit = new Unit();
		$period = new Period();

		$inperiodarr = $unit->getLists($info['id']);
		$info['unitnum'] = count($inperiodarr);
		$info['periodnum'] = 0;
		if ($inperiodarr) {
			$ids = array_column($inperiodarr, 'id');
			$perList = $period->getLists($ids);
			$files = new Filemanage();
			foreach ($inperiodarr as $k => $v) {
				foreach ($perList as $key => $val) {
					// 兼容编辑开课信息回显课件和课件名称
					if($type){
						if($val['courseware']){
							$coursewareid = implode(',',explode('-',$val['courseware']));
							$filelist = $files->getIdInField($coursewareid);
							$val['courseware'] = $filelist ?? [];
						}else{
							$val['courseware'] = [];
						}
					}
					if ($v['id'] == $val['unitid']) {
						$inperiodarr[$k]['list'][] = $val;
					}
				}
			}
			$info['periodnum'] = count($perList);
		}

		// 课程单元 和课时信息
		$info['inperiod'] = $inperiodarr;
		return $info;
	}


	/**
	 * 获取赠品列表
	 * @param $data
	 */
	public function getListGfit($data){
		$data = where_filter($data,['id']);
		$gift = new Coursegift;
		$list = $gift->getlist(['status'=>0]);
		if($list){
			$info = [];
			if(isset($data['id'])){
				$curriculum = new Curriculum();
				$info = $curriculum->getSelectId($data['id'])['giftjson'];
				$info = $info && $info!=''?array_column(json_decode($info,TRUE),'num','id') :[];
			}

			foreach ($list as $k => &$v){
				if($info && isset($info[$v['id']])){
					$v['num'] = $info[$v['id']];
					$v['selected'] = 0;
				}else{
					$v['num'] = '';
					$v['selected'] = 1;
				}
			}
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 11009, lang('error'));
		}
	}

}
