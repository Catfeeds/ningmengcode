<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 分类Model
 * @ jcr
 */
class Category extends Model {

	protected $table = 'category';
	protected $pagenum; //每页显示行数

	// 分类添加验证规则
	protected $rule = [
		'categoryname' => 'require|max:20'];
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			'categoryname.require' => lang('10500'),
			'categoryname.max' => lang('10501'),
		];
	}

	/**
	 * getId 根据分类id 查询分类详情
	 * @ jcr
	 * @param $id 分类id
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
	 * getId 根据分类id 查看分类是否存在
	 * @ jcr
	 * @param $id 分类id
	 * @param $field 查询内容 默认不传全部
	 * @return array();
	 */
	public function getInId($id) {
		if (!$id) {
			return false;
		}
		$where = [
			'id'	  => $id,
			'delflag' => 1,
			'status'  => 1
		];
		return Db::name($this->table)->where($where)->count();
	}


	/**
	 * getId 查询分类列表
	 * @ jcr
	 * @param $where 查询条件
	 * @param $field 查询内容 默认不传全部
	 * @param $limit 查询页数
	 * @param $pagenum 一页几条
	 * @return array();
	 */

	public function getCategoryList($where, $field, $orderbys = '', $limit = 1, $pagenum) {
		if (!$where) {
			$where = [];
		}

		$pagenum = $pagenum ? $pagenum : $this->pagenum;
		$lists = Db::name($this->table)->page($limit, $pagenum)->order($orderbys)->where($where)->field($field)->select();
		return $lists;
	}




	/**
	 * [getCategoryAllId 获取对应的分类下的所有分类]
	 * @param  $organid
	 * @param  $id
	 * @param  string $str
	 * @return array()
	 */
	public function getCategoryAllId($id,$str = ''){
		$list = Db::name($this->table)->where(['fatherid'=>array('in',$id),'delflag'=>1])
									  ->field('id,rank')->select();
		if($list){
			$ids = implode(',',array_column($list,'id'));
			$str .= ','.$ids;
			$str = $this->getCategoryAllId($ids,$str);
		}
		return $str;
	}


	/**
	 * 课程列表分类处理
	 * @jcr
	 */
	public function getCategoryName($data) {
		// 遍历生成对应的或关系查询
		$ids = implode(',',array_filter($data));
		$where['id'] = ['in',$ids];
		$lists = Db::name($this->table)->where($where)->field('id,categoryname,rank')->order('rank asc')->limit(6)->select();
		return $lists ? implode('/', array_column($lists, 'categoryname')) : '-';
	}

	/**
	 * getId 查询移动上下项
	 * @ jcr
	 * @param $data['operate'] 分类操作 0上移 1下移
	 * @param $data['rank'] 分类操作 级别
	 * @param $data['sort'] 分类操作 当前排序值
	 * @return array()
	 */
	public function getCategoryListSort($data) {
		if (!$data) {
			$data = [];
		}

		// 处理查询条件
		$where = where_filter($data, array('rank', 'sort'));
		$where['sort'] = $data['operate'] ? array('egt', $data['sort']) : array('elt', $data['sort']);
		// 处理排序规则
		$order = $data['operate'] ? 'sort asc' : 'sort desc';
		$lists = Db::name($this->table)->where($where)->field('id,sort')->order($order)->limit(2)->select();
		return $lists;
	}

	/**
	 * getId 查询机构分类列表总行数
	 * @ jcr
	 * @param $where 查询条件
	 * @param $organid 所属机构id
	 * @return int;
	 */
	public function getCategoryCount($where) {
		if (!$where) {
			$where = [];
		}

		$counts = Db::name($this->table)->where($where)->count();
		return $counts;
	}

	/**
	 * 分类编辑/添加
	 * @ jcr
	 * @ $data 添加数据源
	 * @ $affairs 添加回调开启事务更新排序值
	 */
	public function editAdd($data, $affairs = false) {
		$validate = new Validate($this->rule, $this->message);
		if (!isset($data['id']) || (isset($data['categoryname']) && isset($data['id']))) {
			// 添加时验证 和 编辑类名时验证
			if (!$validate->check($data)) {
				return array('code' => 500, 'info' => $validate->getError());
			}
		}

		if (isset($data['id'])) {
			// 允许传输的编辑字段
			$data = where_filter($data, array('id', 'status', 'sort', 'delflag', 'categoryname','imgs','describe','icos','icostwo'));
            if (isset($data['delflag'])||isset($data['status'])){
                $cateids = $this->getCategoryAllId($data['id'],$data['id']);
                unset($data['id']);
                $info = Db::name($this->table)->where(['id' => array('in',$cateids)])->update($data);
            }else{
                Db::name($this->table)->where(['id' => $data['id']])->update($data);
				$info = 1; // 指定字段永远成功
            }

			if ($info && $affairs) {
				return true;
			} else if ($affairs) {
				return false;
			}

		} else {
			//添加模块
			$data = where_filter($data, array('id', 'status', 'sort', 'delflag', 'categoryname', 'rank', 'fatherid', 'path','icos','icostwo','imgs','describe'));
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
		return $info?array('code' => 0, 'info' => '操作成功','id'=>$info):['code' => 10200, 'info' => '操作失败','id'=>$info];
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
	
	/**
	 * getId 查询所有分类列表
	 * @ lc
	 * @param $where 查询条件
	 * @param $field 查询内容 默认不传全部
	 * @return array();
	 */

	public function getAllCategoryList($where, $field='id,categoryname', $orderbys = 'id asc') {
		if (!$where) {
			$where = [];
		}
		$where['delflag'] = 1;
		$lists = Db::name($this->table)->order($orderbys)->where($where)->field($field)->select();
		return $lists;
	}

}
