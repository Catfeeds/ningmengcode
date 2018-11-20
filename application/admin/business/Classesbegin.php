<?php
/**
 * 课程业务逻辑层
 */
namespace app\admin\business;
use app\admin\controller\Student;
use app\admin\model\Applylessonsrecord;
use app\admin\model\Applyschedulingrecord;
use app\admin\model\Category;
use app\admin\model\Curriculum;
use app\admin\model\Filemanage;
use app\admin\model\Lessons;
use app\admin\model\Message;
use app\admin\model\Ordermanage;
use app\admin\model\Organconfig;
use app\admin\model\Period;
use app\admin\model\Scheduling;
use app\admin\model\Studentattendance;
use app\admin\model\Studentinfo;
use app\admin\model\Teacherinfo;
use app\admin\model\Toteachtime;
use app\admin\model\Unit;
use app\admin\model\Unitdeputy;
use app\admin\model\Accessroleuser;
use think\Validate;
use app\admin\model\Organ;
use app\admin\model\Dockinglog;

class Classesbegin {
	protected $WKURL = 'http://global.talk-cloud.net';
	function __construct() {
	}

	/**
	 * @ 开课列表 Schedulingdeputy
	 * @Author jcr
	 * @param $where 查询条件
	 * @param $pagenum 每页显示行数
	 * @param $limit 查询页数
	 * @return []
	 **/
	public function getSchedulinglists($data, $pagenum) {
		$scheduling = new Scheduling();
		$list = $scheduling->getClassesList($data, $pagenum);

		if ($list['data']) {
			$category = new Category();
//            $order = new Ordermanage();

			$statusarr = ['0' => '未招生', '1' => '招生中', '2' => '招生中', '3' => '已满员', '4' => '授课中', '5' => '已结束', 6 => '已超时'];
			$teacher = new Teacherinfo();
			$record = new Applyschedulingrecord();

			foreach ($list['data'] as $key => &$val) {

				$field = 'a.id,a.oldschedulingid,a.newschedulingid,a.status,a.updatetime,o.id as orderid';
				// 调出去的
				$left = 'a.oldschedulingid = o.schedulingid and a.oldteacherid = o.teacherid and a.studentid = o.studentid';
				$outCount = $record->getOrderListCount(['a.status' => 1, 'a.oldschedulingid' => $val['id']], $left, $field);

				// 调进来的
				$intoCount = $record->getOrderListCount(['a.status' => 1, 'a.newschedulingid' => $val['id']], $left, $field);

				$val['payordernum'] = $val['payordernum'] - $outCount + $intoCount;

				// 处理分类
				$val['categoryname'] = $category->getCategoryName(explode('-', $val['categorystr']));
//                $val['payordernum'] = $order->getPaySchedulingCount($val['id']);
				// 开班状态的转义
				//				$status = $val['classstatus']>1?$val['classstatus']:$val['status'];
				$val['teachername'] = $teacher->getTeacherId($val['teacherid'], 'nickname')['nickname'];
				$val['classstatusStr'] = $statusarr[$val['classstatus']];
			}
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10048, lang('error_log'));
		}
	}

	/**
	 * 获取课程对应的班级列表
	 * @param $data
	 * @param $pagenum
	 */
	public function getCurricuClass($data, $pagenum) {
		$data = where_filter($data, ['id', 'limit']);
		if (!isset($data['id'])) {
			return return_format('', 11019, lang('param_error'));
		}

		$schedu = new Scheduling();
		$where = ['curriculumid' => $data['id'], 'delflag' => 1];
		$list = $schedu->getList($where, 'id asc', 'id,gradename', $data['limit'], $pagenum);
		if ($list) {
			$count = $schedu->getCount($where);
			$infos['data'] = $list;
			$infos['pageinfo'] = ['pagesize' => $pagenum, 'pagenum' => $data['limit'], 'total' => $count];
			return return_format($infos, 0, lang('success'));
		} else {
			return return_format('', 11018, lang('error_log'));
		}
	}

	/**
	 * 获取班级对应的订单列表
	 */
	public function getSchedulingOrder($data, $pagenum) {
		$data = where_filter($data, ['id', 'limit']);
		if (!isset($data['id'])) {
			return return_format('', 11020, lang('param_error'));
		}

		$order = new Ordermanage();

		$where = ['o.orderstatus' => ['in', '20,30,50'], 'o.schedulingid' => $data['id']];
		$list = $order->getOrderAccountList($where, $data['limit'], $pagenum);
		if ($list) {
			$count = $order->getSchedulingIdCount($data['id']);

			foreach ($list as $k => $v) {
				$val = [
					'orderid' => $v['id'],
					'studentname' => $v['studentname'],
				];
				$list[$k] = $val;
			}
			$infos['data'] = $list;
			$infos['pageinfo'] = ['pagesize' => $pagenum, 'pagenum' => $data['limit'], 'total' => $count];
			return return_format($infos, 0, lang('success'));
		} else {
			return return_format('', 11021, lang('error_log'));
		}

	}

	/**
	 * 获取机构开课老师
	 * @return [type] [description]
	 */
	public function getTeacherList() {
		$access = new Accessroleuser();
		$teachlist = $access->getRoleByTeacher(['roleid'=>2,'usertype'=>1]);
		if(!$teachlist) return return_format('', 10001, lang('error_log'));

		$ids = implode(',',$teachlist);
		$teacher = new Teacherinfo();
		$list = $teacher->getLists($ids);
		if ($list) {
			return return_format($list, 0, lang('success'));
		} else {
			return return_format('', 10051, lang('error_log'));
		}
	}

	/**
	 * 获取老师列表
	 * @return [type] [description]
	 */
	public function getFileTeacher($data, $limit) {
		$data = where_filter($data, ['pagenum', 'teachername']);
		$teacher = new Teacherinfo();
		$where = [
			'accountstatus' => 0,
			'delflag' => 1,
		];
		isset($data['teachername']) && $where['nickname'] = ['like', $data['teachername'] . '%'];

		$list = $teacher->getTeachlist($where, $data['pagenum'], $limit);
		if ($list) {
			foreach ($list as $k => &$v) {
				$v['teachername'] = $v['nickname'];
				unset($v['nickname']);
			}

			$indata['data'] = $list;
			$indata['pageinfo'] = array('pagesize' => $data['pagenum'], 'pagenum' => $limit, 'total' => $teacher->getTeachCount($where));
			return return_format($indata, 0, lang('success'));
		} else {
			return return_format('', 10150, lang('error_log'));
		}
	}

	/**
	 * 获取班级对应的学生列表
	 * @author JCR
	 * @param $data
	 * @param $limit
	 */
	public function getStudentList($data, $limit) {
		$data = where_filter($data, ['id', 'pagenum']);
		$order = new Ordermanage();
		$where = [
			'o.orderstatus' => ['gt', 10],
			'o.schedulingid' => $data['id'],
		];
		// 查询班级调班记录数据
		$record = new Applyschedulingrecord();
		$field = 'a.id,a.oldschedulingid,a.newschedulingid,a.status,a.updatetime,o.id as orderid';
		// 调出去的
		$left = 'a.oldschedulingid = o.schedulingid and a.oldteacherid = o.teacherid and a.studentid = o.studentid';
		$outlist = $record->getOrderList(['a.status' => 1, 'a.oldschedulingid' => $data['id']], $left, $field);

		// 调进来的
		$intolist = $record->getOrderList(['a.status' => 1, 'a.newschedulingid' => $data['id']], $left, $field);
//		dump($intolist);
		$outOrder = array_column($outlist, 'orderid');
		$intoOrder = array_column($intolist, 'orderid');

//		$intersect = array_intersect($outOrder,$intoOrder);

		if ($outOrder) {
			$where['o.id'] = ['not in', implode(',', $outOrder)];
		}

		$list = $order->getAccountsList($where, $intoOrder, $data['pagenum'], $limit);
		if ($list) {
			$listdata = [];
			foreach ($list as $k => $v) {
				$listdata[] = [
					'studentid' => $v['studentid'],
					'studentname' => $v['studentname'],
					'prphone' => $v['prphone'],
					'mobile' => $v['mobile'],
				];
			}
			unset($list);
			$indata['data'] = $listdata;
			$count = $order->getOrderWhereCount($where);
			$count = $count - count($outOrder) + count($intolist);
			$indata['pageinfo'] = array('pagesize' => $data['pagenum'], 'pagenum' => $limit, 'total' => $count);
			return return_format($indata, 0, lang('success'));
		} else {
			return return_format('', 10151, lang('error_log'));
		}
	}

	/**
	 * 开课第一步
	 * @author JCR
	 * @param  $data
	 */
	public function oneEdit($data) {
		$data = where_filter($data, ['id', 'gradename', 'price', 'curriculumid', 'fullpeople']);
		if (!isset($data['curriculumid'])) {
			return return_format('', 10052, lang('param_error'));
		}
		$curriculum = new Curriculum();
		$curriculumInfo = $curriculum->getSelectId($data['curriculumid']);
		if (!$curriculumInfo) {
			return return_format('', 10052, lang('10052'));
		}

		$scheduling = new Scheduling();
		$validate = new Validate($scheduling->rulemax, $scheduling->messagemax);

		if (!$validate->check($data)) {
			return return_format('', 10054, $validate->getError());
		}

		if (isset($data['id'])) {
			$scheduinfo = $scheduling->getInfoId($data['id']);
			if (!$scheduinfo) {
				return return_format('', 10535, lang('param_error'));
			}

			$returnData = $scheduling->oneEdit($data, $curriculumInfo, $scheduinfo);
			return return_format($scheduinfo['id'], $returnData['code'], $returnData['info']);

		} else {
			$returnData = $scheduling->oneEdit($data, $curriculumInfo, []);
			return return_format($returnData['id'], $returnData['code'], $returnData['info']);
		}
	}

	/**
	 * 开课、编辑
	 * @author jcr
	 * $data 开课数据源
	 */
	public function addEdit($data) {

		$scheduling = new Scheduling();
		$curriculum = new Curriculum();

		$scheduleInfo = $scheduling->getInfoId($data['id']);

		/* if ($scheduleInfo && $scheduleInfo['schedule'] == 1) {
			$data['classhour'] = $scheduleInfo['classhour'];
		} else {
			$data['classhour'] = $scheduling->getConfigKey()['classhours'];
		} */

		$curriculumInfo = $curriculum->getSelectId($scheduleInfo['curriculumid']);
		if (!$curriculumInfo) {
			return return_format('', 10052, lang('10052'));
		}

		if ($scheduleInfo['type'] != 1) {
			// 在不为1对1的情况、
			if (!$data['list']) {
				return return_format('', 10055, lang('param_error'));
			}

			$keyArr = [];
			for ($i = 0; $i < 144; $i++) {
				$keyArr[] = $i;
			}

			// 获取老师课程安排时间
			$toteach = new Toteachtime();

			$timeArr = array_column($data['list'], 'intime');
			$where = [];
			foreach ($timeArr as $key => $val) {
				$where[$key]['intime'] = $val;
				$where[$key]['teacherid'] = $data['teacherid'];
				$where[$key]['delflag'] = 1;
			}

			// 如果是编辑 不过滤当前时间
			if (isset($data['id'])) {
				$toteachlist = $toteach->getTimekey($where, $data['id']);
			} else {
				$toteachlist = $toteach->getTimekey($where, false);
			}

			$inArrs = [];
			foreach ($toteachlist as $k => $val) {
				$inArrs[$val['intime']][] = $val['timekey'];
			}
			//取出对应的时间占用的时间段
			foreach ($inArrs as $key => $value) {
				$inArrs[$key] = explode(',', implode(',', $value));
			}
			// 处理数组结构
			$data['list'] = sortByCols($data['list'], ['unitsort' => 'SORT_ASC', 'periodsort' => 'SORT_ASC']);

			$timeInArr = [];
			foreach ($data['list'] as $k => $v) {
				if (!isset($v['timekey']) || !isset($v['intime'])) {
					$code = $code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10056'));
					return return_format('', 10056, $code);
				}
				if (!$v['timekey']) {
					$code = $code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10056'));
					return return_format('', 10056, $code);
				}
				// 前端传输数组

				// 根据最小时间自己计算数组
				$inkeys = get_time_key_value($v['timekey']);
				if ($inkeys === false) {
					$code = $code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10131'));
					return return_format('', 10131, $code);
				}
                $hourarr = explode(':', $v['classhour']);
				$explodearr = array_series($inkeys, $hourarr[0] * 60 + $hourarr[1]);
				$data['list'][$k]['timekey'] = implode(',', $explodearr);
				$data['list'][$k]['classhour'] = $hourarr[0] * 60 + $hourarr[1];

				// 数组大于1 可能存在跨天问题
				$explodearrCount = count($explodearr);
				if ($explodearr[0] > $explodearr[$explodearrCount - 1]) {
					//跨天了 起始键 大于 终止键
					$datatime = strtotime($v['intime'] . ' ' . get_time_key($explodearr[$explodearrCount - 1])) + 86400;
				} else {
					$datatime = strtotime($v['intime'] . ' ' . get_time_key($explodearr[$explodearrCount - 1]));
				}

				$starttime = strtotime($v['intime'] . ' ' . get_time_key($explodearr[0]));
				if ($starttime <= time() && $scheduleInfo['schedule'] == 0) {
					// 选择时间不能小于当前时间
					$code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10099'));
					return return_format('', 10099, $code);
				}

				// 当前时间必须比前一课时时间大
				if (count($timeInArr) > 0) {
					if (strtotime($v['intime'] . ' ' . get_time_key($explodearr[0])) <= $timeInArr[count($timeInArr) - 1]) {
						$code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10057'));
						return return_format('', 10057, $code);
					}
				}

				$timeInArr[] = $datatime;

				// 根据日期去获取对应的空余时间
				if (isset($inArrs[$v['intime']])) {
					// $sparetime = array_diff($teacheArr[date('w',strtotime($v['intime']))],$inArrs[$v['intime']]);
					$sparetime = array_diff($keyArr, $inArrs[$v['intime']]);
				} else {
					// $sparetime = $teacheArr[date('w',strtotime($v['intime']))];
					$sparetime = $keyArr;
				}

				// 对比现提交时间和老师空余时间
				if (array_diff($explodearr, $sparetime)) {
					$code = str_replace(['1@', '2@'], [$v['unitsort'], $v['periodsort']], lang('10058'));
					return return_format('', 10058, $code);
				}

				// 处理到此 该排查的都排查了 end
			}
		}

		if ($scheduleInfo['schedule'] == 1) {
			$info = $scheduling->edits($data, $scheduleInfo);
		} else {
			$info = $scheduling->adds($data, $scheduleInfo, $curriculumInfo);
		}
		return return_format('', $info['code'], $info['info']);
	}

	/**
	 * [getTimeOccupy 获取对应老师的占用时间]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getTimeOccupy($data) {
		$data = where_filter($data, ['teacherid', 'intime']);
		if (!isset($data['teacherid'])) {
			return return_format('', 10067, lang('10067'));
		}

		if (!isset($data['intime'])) {
			return return_format('', 10068, lang('10068'));
		}

		$data['delflag'] = 1;
		$toteach = new Toteachtime();
		$listTime = $toteach->getTimekey([$data], false);
		if ($listTime) {
			$listTime = explode(',', implode(',', array_column($listTime, 'timekey')));
			return return_format($listTime, 0, lang('success'));
		} else {
			return return_format([], 0, lang('error_log'));
		}
	}

	/**
	 * [enrollStudent 暂停开课]
	 * @return [type] [description]
	 */
	public function enrollStudent($data) {
		$scheduling = new Scheduling();
//        $orders = new Ordermanage();
		//获取开课详情
		$info = $scheduling->getInfoId($data['id']);
		if (!$info) {
			return return_format('', 10069, lang('10069'));
		}

		if ($info['status'] == $data['status']) {
			$code = $data['status'] == 0 ? 10071 : 10072;
			return return_format('', $code, lang($code));
		}

		if ($data['status'] == 1 && $info['schedule'] == 0) {
			return return_format('', 10143, lang('10143'));
		}

//		if($orders->getSchedulingIdCount($data['id'])>0){
		//			return return_format('',10073,lang('10073'));
		//		}

		$ids = $scheduling->enrollStudent($data, $info);
		if ($ids) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 10074, lang('error'));
		}
	}

	/**
	 * [enrollStudent 删除开课信息]
	 * @return [type] [description]
	 */
	public function deleteScheduling($data) {
		$scheduling = new Scheduling();
//        $orders = new Ordermanage();
		$info = $scheduling->getInfoId($data['id']);

		// 已结束的课程可以直接删除
		//		if($info['classstatus']!=5){
		//			// 有人购买的课程不能删除
		//			if($orders->getSchedulingIdCount($data['id'])>0){
		//				return return_format(1,10075,lang('10075'));
		//			}
		//		}
		$infos = $scheduling->deleteScheduling($data, $info);

		return return_format('', $infos['code'], $infos['info']);
	}

	/**
	 * [getSchedulingInfo 开班详情返回数据]
	 * @param  [type] $data [参数源]
	 * @return [type]       [description]
	 */
	public function getSchedulingInfo($data) {
		if (!isset($data['curriculumid']) || !$data['curriculumid']) {
			return return_format('', 10079, lang('param_error'));
		}
		$id = isset($data['id']) ? $data['id'] : false;
		$scheduling = new Scheduling();

		$info = $scheduling->onetooneClass($data['curriculumid'], $data['type'], $id);
		if (!$info) {
			return return_format('', 10079, lang('param_error'));
		}

//      $info['curriculumidSumPrice'] = $info['periodnum'] * $info['price'];
		if ($data['type'] != 1) {

			if ($info['schedule'] == 1) {
				$teacher = new TeacherInfo();
				$lessons = new Lessons();
				$unitdeputy = new Unitdeputy();

				// 获取课程表的单元和课时
				$inperiodarr = $unitdeputy->getLists($info['curriculumid'], $info['id']);
				$inlist = $lessons->getInLists($info['id'], $info['teacherid']);

				foreach ($inperiodarr as $k => $v) {
					foreach ($inlist as $key => $val) {
						if ($v['id'] == $val['unitid']) {
							// 处理数据结构
							$val['unitsort'] = $v['unitsort'];
							$val['timekey'] = get_time_key(explode(',', $val['timekey'])[0]);
							$val['classhour'] = sprintf("%02d:%02s", floor($val['classhour']/60), $val['classhour'] - floor($val['classhour']/60) * 60);
							$val['timestr'] = $val['intime'] . ' ' . $val['timekey'];
							$val['teachername'] = $teacher->getTeacherId($val['teacherid'], 'nickname,imageurl')['nickname'];
							$inperiodarr[$k]['list'][] = $val;
						}
					}
				}
				$info['list'] = $inperiodarr;
			} else {
				// 获取课程表的单元 和 开课表的课时
				$period = new Period();
				$unit = new Unit();

				$inperiodarr = $unit->getLists($data['curriculumid']);
				$perList = $period->getIdsLists($data['curriculumid']);
				foreach ($inperiodarr as $k => $v) {
					foreach ($perList as $key => $val) {
						if ($v['id'] == $val['unitid']) {
							$val['unitsort'] = $v['unitsort'];
							$inperiodarr[$k]['list'][] = $val;
						}
					}
				}
				$info['list'] = $inperiodarr;
			}
		}

		if (isset($info['teacherid'])) {
			$teacher = new Teacherinfo();
			$info['teachername'] = $teacher->getTeacherId($info['teacherid'], 'teacherid,nickname')['nickname'];
		}

		if ($info) {
			return return_format($info, 0, lang('success'));
		} else {
			return return_format('', 10080, lang('error_log'));
		}
	}

	/**
	 * [addFiles 添加文件夹]
	 * @param [type] $data [description]
	 */
	public function addFiles($data) {
		$filemanage = new Filemanage();
		$info = $filemanage->addFile($data);
		if ($info['code'] == 0) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', $info['code'], $info['info']);
		}
	}

	/**
	 * [getFileList 文件夹列表 和 资源列表]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getFileList($data) {
		$filemanage = new Filemanage();
		$data = where_filter($data, array('limit', 'fatherid', 'showname', 'filetype', 'teacherid', 'usetype'));

		$data['fatherid'] = isset($data['fatherid']) ? $data['fatherid'] : 0;
		// 当没传类型 默认为
		if (!isset($data['filetype']) && $data['fatherid'] == 0) {
			$data['filetype'] = 0;
		}

		$pagenum = $data['fatherid'] == 0 ? 1000 : 20;
		$info = $filemanage->getFileList($data, $data['limit'], $pagenum);

		if ($info['data']) {
			$usetype = ['1' => '录播素材', '2' => '直播素材'];
			$region = new Organ();
			$dockinglog = new Dockinglog();
			$newdata['authKey'] = $region->getOrganid()['roomkey'];
			foreach ($info['data'] as $k => &$val) {
				$inwhere = ['fatherid' => $val['fileid'], 'delflag' => 1];
				if (isset($data['usetype'])) {
					$inwhere['usetype'] = $data['usetype'];
				}
				$val['usetype'] = isset($usetype[$val['usetype']]) ? $usetype[$val['usetype']] : '-';
				$val['addtimestr'] = date('Y-m-d H:i:s', $val['addtime']);
				$val['juniorcount'] = $val['fatherid'] == 0 ? $filemanage->getFileCount($inwhere) : 0;
				// $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
				$ar = explode('.', $val['showname']);
				$ext = strtolower($ar[count($ar)-1]);
				if($val['usetype'] == '直播素材' && ($ext == 'ppt' || $ext == 'pptx')){
					$newdata['fileId'] = $val['relateid'];
					$infos = curl_postFile($this->WKURL . '/WebAPI/getDynamicPptInfo', $newdata);
					$infos = json_decode($infos, true);
					if ($infos['result'] != 0) {
						//记录异常
						$datalog = [
							'dockingurl' => $this->WKURL . '/WebAPI/getDynamicPptInfo',
							'code' => $infos['result'],
							'content' => json_encode($newdata)
						];
						$dockinglog->addEdit($datalog);
						$val['previewpath'] = '';
					} else {
						$val['previewpath'] = $infos['path'];
					}
				}else{
					$val['previewpath'] = config('param.http_name') . $val['cosurl'];
				}
			}
			return return_format($info, 0, lang('success'));
		} else {
			return return_format('', 10082, lang('error_log'));
		}
	}

	/**
	 * [deleteFile 删除课件]
	 * @param  $fileid [素材id]
	 * @return [type] [description]
	 */
	public function deleteFile($data) {
		$filemanage = new Filemanage();
		$info = $filemanage->addFile($data);
		if ($info['code'] == 0) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', $info['code'], lang('error'));
		}
	}

	/**
	 * [editConfig 编辑机构基本配置信息]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function editConfig($data) {
		$Organconfig = new Organconfig();
		$info = $Organconfig->addEdit($data);
		if ($info) {
			return return_format('', 0, lang('success'));
		} else {
			return return_format('', 10084, lang('error'));
		}
	}

	/**
	 * 检查开课分类和添加班级课程
	 * @param $data
	 */
	public function checkClass($data) {
		if (!isset($data['type'])) {
			return return_format('', 10118, lang('param_error'));
		}

		if ($data['type'] == 1) {
			// 添加课程检查分类
			$cate = new Category();
			$count = $cate->getCategoryCount(['delflag' => 1, 'fatherid' => 0]);
			if ($count == 0) {
				return return_format(['count' => $count], 10120, lang('10120'));
			}

		} else {
			// 开课检查课程
			// if (isset($data['classtypes'])) return return_format('',10119,lang('param_error'));
			$curricu = new Curriculum();
			$where = [
				'delflag' => 1,
				'status' => 1,
			];
			$count = $curricu->getCurriculumCount($where);
			if ($count == 0) {
				return return_format(['count' => $count], 10121, lang('10121'));
			}

		}
		return return_format(['count' => $count], 0, lang('success'));
	}

	/**
	 * getClassStudent 获取对应课时教室学生老师列表
	 * @param $data
	 * @param $limit	一页多少条
	 */
	public function getClassStudent($data, $limit) {
		$data = where_filter($data, ['id', 'pagenum']);
		if (!isset($data['id'])) {
			return return_format('', 11012, lang('param_error'));
		}

		$lessons = new Lessons();
		$info = $lessons->getId($data['id'], 'id,teacherid');
		if (!$info) {
			return return_format('', 11013, lang('param_error'));
		}

		// 获取老师信息
		$teacher = new Teacherinfo();
		$teachInfo = $teacher->getTeacherId($info['teacherid'], 'teacherid as id,nickname');
		$teachInfo['teacherType'] = 1;
		$teachInfo['attendancestatus'] = 1;
		$teachInfo['typestr'] = '老师';

		// 获取学生信息
		$studentatte = new Studentattendance();
		$studentList = $studentatte->getAllList(['lessonsid' => $data['id']]);
		$mergeArr = [];
		$mergeArr[] = $teachInfo;
		if ($studentList) {
			$student = new Studentinfo();
			foreach ($studentList as $k => $v) {
				$v['teacherType'] = 3;
				$v['typestr'] = '学生';
				$v['nickname'] = $student->getStudentId($v['id'], 'nickname')['nickname'];
				$mergeArr[$k + 1] = $v;
			}
		}
		// 获取对应的 拓课信息

		// 合并数据

		// 手动分页
		$indata['pageinfo'] = array('pagesize' => $data['pagenum'], 'pagenum' => $limit, 'total' => count($mergeArr));
		$indata['data'] = pageLimit($mergeArr, $data['pagenum'], $limit);
		return return_format($indata, 0, lang('success'));

	}

	/**
	 * 学生消息发送
	 * @param $data
	 */
	public function sendMessage($data) {
		$data = where_filter($data, ['id', 'ids', 'title', 'content', 'type']);
		if (!isset($data['id']) || !$data['id']) {
			return return_format('', 11027, 'param_error');
		}

		$message = new Message();
		$validate = new Validate($message->rule, $message->message);
		if (!$validate->check($data)) {
			return return_format('', 11025, $validate->getError());
		}
		//type 1班级 2 课程

		$student = new Studentinfo();
		$list = $student->getStudentnameByIds($data['ids']);
		if (!$list) {
			return return_format('', 11026, 'param_error');
		}

		$intype = $data['type'] == 1 ? 13 : 5;

		$megs = new \Messages();
		foreach ($list as $key => $val) {
			$vals['usertype'] = 3;
			$vals['userid'] = '3' . $key;
			$vals['externalid'] = $data['id'];
			$vals['title'] = $data['title'];
			$vals['content'] = $data['content'];
			$info = $megs->addMessage($vals, $intype);
		}
		return return_format('', 0, lang('success'));
	}


	/**
	 * 课时学生列表
	 */
	public function getLessonsStudent($data,$limit){
		if(!isset($data['id']) || !$data['id']) return_format('', 11030, lang('param_error'));
		//
		$lessons = new Lessons();
		$info = $lessons->getId($data['id'],'id,teacherid,schedulingid');
		if(!$info) return_format('', 11031, lang('param_error'));

		// 获取下单学生id
		$order = new Ordermanage();
		$orderlist = $order->getWhereList(['orderstatus'=>['gt',10],'schedulingid'=>$info['schedulingid']],'id,studentid');
		if($orderlist){
			$studentid = array_column($orderlist,'studentid');
			// 查看调课
			$applyLessons = new Applylessonsrecord();
			$applyLessonList = $applyLessons->getLessonList($info['id'],'id,studentid,oldlessonsid,newlessonsid');
			if($applyLessonList){
				foreach ($applyLessonList as $k => $v){
					if($v['oldlessonsid'] == $info['id']){
						// 调出去
						$key = array_search($v['studentid'],$studentid);
						if($key !== false) unset($studentid[$key]);
					}else if($v['newlessonsid'] == $info['id']){
						$studentid[] = $v['studentid'];
					}
				}
			}

			// 查看调班
			$applySchedu = new Applyschedulingrecord();
			$applyList = $applySchedu->getScheduList($info['schedulingid'],'id,studentid,oldschedulingid,newschedulingid,newlessonsid,oldlessonsid');
			if($applyList){
				foreach ($applyList as $k => $v){
					if($v['oldschedulingid'] == $info['schedulingid']){
						// 调出去
						$oldlessonsid = $v['oldlessonsid']?explode(',',$v['oldlessonsid']):[];
						if($oldlessonsid && in_array($info['id'],$oldlessonsid)){
							$key = array_search($v['studentid'],$studentid);
							if($key !== false) unset($studentid[$key]);
						}
					}else if($v['newschedulingid'] == $info['schedulingid']){
						// 调进来
						$newlessonsid = $v['newlessonsid']?explode(',',$v['newlessonsid']):[];
						if($newlessonsid && in_array($info['id'],$newlessonsid)){
							$studentid[] = $v['studentid'];
						}
					}
				}
			}

			if($studentid){
				$student = new Studentinfo();
				$studentids = implode(',',$studentid);
				$studentList = $student->getStudenslist(['id'=>['in',$studentids]],$data['pagenum'],$limit);
				$indata['data'] = $studentList;
				$indata['pageinfo'] = ['pagesize' => $limit, 'pagenum' => $data['pagenum'], 'total' => count($studentid)];
				return return_format($indata, 0, lang('success'));
			}else{
				return return_format('', 11032, lang('error_log'));
			}
		}else{
			return return_format('', 11032, lang('error_log'));
		}

	}
}
