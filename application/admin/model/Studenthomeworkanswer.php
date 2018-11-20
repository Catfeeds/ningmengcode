<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;
/*
 * 分类Model
 * @ lc
 */
class Studenthomeworkanswer extends Model {

	protected $table = 'nm_studenthomeworkanswer';
	protected $pagenum; //每页显示行数

	// 分类添加验证规则
	protected $rule = [
		/* 'classid' => 'require|number',
		'lessonid' => 'require|number',
		'studentid' => 'require|number', */
	];	
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			/* 'classid.require' => lang('90002'),
			'classid.number' => lang('80011'),
			'lessonid.require' => lang('90003'),
			'lessonid.number' => lang('80011'),
			'studentid.require' => lang('90004'),
			'studentid.number' => lang('80011'), */
		];
	}
	
	/**
	 * 获取学生习题总成绩 
	 * @param id
	 * @return [bool]
	 */
	public function getStudentSumCore($classid, $lessonid, $studentid){
		$where['classid'] = $classid;
		$where['lessonid'] = $lessonid;
		$where['studentid'] = $studentid;
		return Db::table($this->table)->where($where)->sum('score'); 
	}
	
	/**
     * [getAnswers 获取学生答案/得分/评语]
     * @Author lc
	 * @param  $where
     * @return   array
     */
    public function getAnswers($where){
        $result = Db::table($this->table)
            ->field('answer,score,comment')
            ->where($where)
            ->find();
        return $result;
    }
}
