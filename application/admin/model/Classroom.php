<?php namespace app\admin\model;
use think\Db;
use think\Model;

class Classroom extends Model {

	protected $table = 'classroom';
	protected $pagenum; //每页显示行数

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
	}

	/**
	 * [findWeekMark 获取对应的资源列表]
	 * @Author JCr
	 * @DateTime 2018-04-19T15:31:56+0800
	 */
	public function getRoomId($id) {
		$where = ['toteachtimeid' => $id];
		$field = 'id,classroomno,chairmanpwd,assistantpwd,patrolpwd,confuserpwd,passwordrequired';
		return Db::name($this->table)->field($field)->where($where)->find();
	}

	/**
	 * 获取对应的房间的时间id
	 */
	public function getRoomInfo($classroomno) {
		$field = 'cl.id,cl.classroomno,cl.toteachtimeid';
		$where['cl.classroomno'] = $classroomno;
		return Db::name($this->table)->alias('cl')
			->join('nm_toteachtime t', 't.id = cl.toteachtimeid')
			->field($field)->where($where)->find();
	}

	public function getRoomInfoId($classroomno) {
		$field = 'id,classroomno,toteachtimeid';
		$where['classroomno'] = $classroomno;
		return Db::name($this->table)->field($field)->where($where)->find();
	}

	/**
	 * [addFile 新增开课教室]
	 * @author JCR
	 * @param [type] $data [description]
	 */
	public function addRoom($data) {
		if (isset($data['id']) && isset($data['videostatus'])) {
			// 编辑
			$data = where_filter($data, array('id', 'videostatus'));
			$id = Db::name($this->table)->where('id', 'eq', $data['id'])->update($data);
		} else {
			$adddata = where_filter($data, array('addtime', 'shuttime', 'classroomno', 'toteachtimeid', 'chairmanpwd', 'assistantpwd', 'patrolpwd', 'confuserpwd', 'passwordrequired'));
			$id = Db::name($this->table)->insert($adddata);
		}
		return $id ? array('code' => 0, 'info' => $id) : array('code' => 10088, 'info' => '操作失败');
	}

}
