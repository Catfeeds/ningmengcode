<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;
/*
 * 分类Model
 * @ lc
 */
class Studenthomework extends Model {

	protected $table = 'nm_studenthomework';
	protected $pagenum; //每页显示行数

	// 分类添加验证规则
	protected $rule = [
		//'courseid' => 'require|number',
		//'periodid' => 'require|number',
	];	
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			//'courseid.require' => lang('10500'),
			//'courseid.require' => lang('10501'),
		];
	}
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getStudentHomeworkList($where, $field, $limitstr, $order='sh.id desc')
    {
        return Db::table($this->table)
				->alias(['nm_studenthomework'=>'sh','nm_studentinfo'=>'s'])
				->join('nm_studentinfo','sh.studentid=s.id','LEFT')
				->where($where)
				->field($field)
				->limit($limitstr)
				->order($order)
				->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @param $order    string      必填
     * @param $limitstr string      必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     *
     */   
    public function getStudentHomeworkListCount($where){
        return Db::table($this->table)
			->alias(['nm_studenthomework'=>'sh','nm_studentinfo'=>'s'])
			->join('nm_studentinfo','sh.studentid=s.id','LEFT')
			->where($where)
			->count();
    }
	
	/**
	 * 获取提交的学生总数 
	 * @param id
	 * @return [bool]
	 */
	public function getSubmitedStudentCount($classid, $lessonid){
		$where['classid'] = $classid;
		$where['lessonid'] = $lessonid;
		$where['issubmited'] = 1;
		$c = Db::table('nm_studenthomework')->where($where)->count();
		return $c;
	}
	
	/**
	 * 获取提交未批阅的学生总数 
	 * @param id
	 * @return [bool]
	 */
	public function getNotReviewStudentCount($classid, $lessonid){
		$where['classid'] = $classid;
		$where['lessonid'] = $lessonid;
		$where['issubmited'] = 1;
		$where['reviewstatus'] = 0;
		$c1 = Db::table('nm_studenthomework')->where($where)->count();
		return $c1;
	}
}
