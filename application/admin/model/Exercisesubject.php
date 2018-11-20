<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 分类Model
 * @ lc
 */
class Exercisesubject extends Model {

	protected $table = 'nm_exercisesubject';
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
     * [ 通过课时id删除题干]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [知识id]
     * @return   [type]               [description]
     */
    public function delExerciseSubjectByPeriodid($periodid){
		$where['periodid'] = $periodid;
    	return Db::table($this->table)->where($where)->update(['delflag'=>1]);
		
    }
	
	/**
	 * 通过习题id获取所有题干ID
	 */
	public function getSubjectidsByPeriodid($periodid){
	     $r = Db::table($this->table)->where(['periodid'=>$periodid, 'delflag'=>0])->field('id')->select();
		 return array_column($r, 'id');
    }
	
	/**
     * [getSubjectList 根据指定条件查询作业列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getSubjectList($where,$field,$order='id asc'){
        $lists = Db::table($this->table)
            ->field($field)
            ->where($where)
			->order($order)
            ->select();
        return $lists;
    }
	
	/**
	 * 插入题干数据
	 * @param $subjectRet array
	 * @return int
	 */
	public function insertOneSubject($subjectRet){
		return Db::table($this->table)->insertGetId($subjectRet);
	} 
}
