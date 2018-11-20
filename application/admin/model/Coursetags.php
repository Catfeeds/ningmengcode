<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 课程标签Model
 * @ jcr
 */
class Coursetags extends Model {

	protected $table = 'coursetags';
	protected $pagenum; //每页显示行数

	// 课程标签添加验证规则
	protected $rule = [
		'tagname' => 'require|max:20'];
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			'tagname.require' => lang('10502'),
			'tagname.max' => lang('10503'),
		];
	}

	/**
	 * getId 根据课程标签id 查询课程标签详情
	 * @ jcr
	 * @param $id 课程标签id
	 * @param $field 查询内容 默认不传全部
	 * @return array();
	 */
	public function getId($id, $field) {
		if (!$id) {
			return false;
		}

		return Db::name($this->table)->where(array('id' => $id))->find();
	}

	/**
	 * getId 查询课程标签列表
	 * @ jcr
	 * @param $where 查询条件
	 * @param $field 查询内容 默认不传全部
	 * @param $limit 查询页数
	 * @param $pagenum 一页几条
	 * @return array();
	 */
	public function getCoursetagsList($where, $field, $orderbys = '', $limit = 1, $pagenum) {
		if (!$where) {
			$where = [];
		}

		$pagenum = $pagenum ? $pagenum : $this->pagenum;
		$lists = Db::name($this->table)->page($limit, $pagenum)->order($orderbys)->where($where)->field($field)->select();
		return $lists;
	}

	/*
		     * 根据父级id获取子级列表
		     * @ jcr
	*/
	public function getCoursetagsFatherid($data, $pagenum, $orderby = 'sort desc') {
		if (!$data['fatherid']) {
			$data['fatherid'] = 0;
		}

		$where = where_filter($data, array('fatherid'));
		$where['delflag'] = 1;
		$field = 'id,tagname,sort,fatherid,status,addtime';
		$datajson['data'] = $this->getCoursetagsList($where, $field, $orderby, $data['limit'], $pagenum);
		$datajson['pageinfo'] = array('pagesize' => $pagenum, 'pagenum' => $data['limit'], 'total' => count($datajson['data']) ? $this->getCoursetagsCount($where) : 0);
		return $datajson;
	}

	/**
	 * getId 查询移动上下项
	 * @ jcr
	 * @param $data['operate'] 课程标签操作 0上移 1下移
	 * @param $data['rank'] 课程标签操作 级别
	 * @param $data['sort'] 课程标签操作 当前排序值
	 */
	public function getCoursetagsListSort($data) {
		if (!$data) {
			$data = [];
		}

		// 处理查询条件
		$where = where_filter($data, array('fatherid', 'sort'));
		$where['sort'] = $data['operate'] ? array('elt', $data['sort']) : array('egt', $data['sort']);
		// 处理排序规则
		$order = $data['operate'] ? 'sort desc' : 'sort asc';
		$lists = Db::name($this->table)->where($where)->field('id,sort')->order($order)->limit(2)->select();
		return $lists;
	}

	/**
	 * getId 查询机构课程标签列表总行数
	 * @ jcr
	 * @param $where 查询条件
	 * @param $field 查询内容 默认不传全部
	 * @return int;
	 */
	public function getCoursetagsCount($where) {
		if (!$where) {
			$where = [];
		}

		$counts = Db::name($this->table)->where($where)->count();
		return $counts;
	}

	/**
	 * 获取机构下全部的标签
	 * @ jcr
	 * @return [type] [description]
	 */
	public function getTree() {
		$lists = Db::name($this->table)->order('sort desc')
									->where('delflag', 'eq', 1)
									->where('status', 'eq', 1)
									->field('id,tagname,fatherid,sort')->select();
		return $lists;
	}

	/**
	 * 课程标签编辑/添加
	 * @ jcr
	 * @ data 添加数据源
	 * @ $affairs 添加回调开启事务更新排序值
	 */
	public function editAdd($data, $affairs = false) {
		$data = where_filter($data, array('id', 'status', 'sort', 'delflag', 'tagname', 'fatherid', 'path', 'addtime'));
		$validate = new Validate($this->rule, $this->message);
		if (!isset($data['id']) || (isset($data['tagname']) && isset($data['id']))) {
			// 添加时验证 和 编辑类名时验证
			if (!$validate->check($data)) {
				return array('code' => 500, 'info' => $validate->getError());
			}
		}
		if (isset($data['id'])) {
			$info = Db::name($this->table)->where(['id' => $data['id']])->update($data);
			if ($info && $affairs) {
				return true;
			} else if ($affairs) {
				return false;
			}
		} else {
			//添加模块
			$data['addtime'] = time();
			//开启事务
			Db::name($this->table)->startTrans();
			$info = Db::name($this->table)->insertGetId($data);
			if ($info) {
				//回调 更新sort 同步主键
				$editstatus = $this->editAdd(['id' => $info, 'sort' => $info], true);
				if ($editstatus) {
					Db::name($this->table)->commit();
				} else {
					Db::name($this->table)->rollback();
					return array('code' => 500, 'info' => '添加失败');
				}
			}
		}
		return array('code' => 0, 'info' => isset($data['id']) ? '修改成功' : '添加成功');
	}

	/**
	 * 特殊需求，上下移动
	 * @ jcr
	 * @ data 修改数据源
	 **/
	public function editSoct($data) {
		// 开启事务 交换两个id 对应的排序值
		Db::name($this->table)->startTrans();
		$onein = $this->editAdd($data[0], false);
		$twoin = $this->editAdd($data[1], false);
		if ($onein && $twoin) {
			Db::name($this->table)->commit();
			return true;
		} else {
			Db::name($this->table)->rollback();
			return false;
		}
	}

}
