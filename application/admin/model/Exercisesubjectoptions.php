<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 分类Model
 * @ lc
 */
class Exercisesubjectoptions extends Model {

	protected $table = 'nm_exercisesubjectoptions';
	protected $pagenum; //每页显示行数

	// 分类添加验证规则
	protected $rule = [
		//'courseid' => 'require|number',
		//'periodid' => 'require|number',
	];	
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		//$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		/* $this->message = [
			//'courseid.require' => lang('10500'),
			//'courseid.require' => lang('10501'),
		]; */
	}
	
	/**
	 * 根据subjectids 检查是否有options
	 */
	public function getDatasBySubjectids($subjectids){
		return Db::table($this->table)->where(['delflag' => 0])->whereIn('subjectid', $subjectids)->field('id')->find();
	}
	 
	/**
     * [根据subjectids删除题目选项]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [array]    $subjectids 
     * @return   [type]     [description]
     */
    public function delOptionsBySubjectid($subjectids){
    	return Db::table($this->table)->whereIn('subjectid', $subjectids)->update(['delflag'=>1]);
		
    }
	
	/**
     * [getSubjectOptions 根据习题id获取选择题选项]
     * @Author lc
     * @return   array
     */
    public function getSubjectOptions($where){
        $lists = Db::table($this->table)
            ->where($where)
            ->column('optionname');
        return $lists;
    }
	
	/**
	 * 插入options数据
	 * @param $optionRet array
	 * @return int
	 */
	public function insertOptions($optionRet){
		return Db::table($this->table)->insertAll($optionRet);
	} 
}
