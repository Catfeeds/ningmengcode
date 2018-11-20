<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;
use app\admin\model\Lessons;
/*
 * 分类Model
 * @ lc
 */
class Schedulinglessoninfo extends Model {

	protected $table = 'nm_schedulinglessoninfo';
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
    public function getSchedulingLessonList($where, $field, $limitstr, $ca_one_findin=false, $ca_two_findin=false, $order='sl.id desc')
    {
        $slDb = Db::table($this->table)
				->alias(['nm_schedulinglessoninfo'=>'sl','nm_scheduling'=>'s', 'nm_curriculum'=>'cu', 'nm_lessons'=>'le', 'nm_teacherinfo'=>'te'])
				->join('nm_scheduling','sl.classid=s.id','LEFT')
				->join('nm_curriculum','s.curriculumid=cu.id','LEFT')
				->join('nm_lessons','sl.lessonid=le.id','LEFT')
				->join('nm_teacherinfo','sl.teacherid=te.teacherid','LEFT')
				->where($where)
				->field($field)
				->limit($limitstr)
				->order($order);
		if($ca_one_findin){
            $slDb = $slDb->where($ca_one_findin);
        }
		if($ca_two_findin){
            $slDb = $slDb->where($ca_two_findin);
        }
		$lists = $slDb->select();
        return $lists;
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
    public function getSchedulingLessonListCount($where, $ca_one_findin=false, $ca_two_findin=false){
        $slcDb = Db::table($this->table)
			->alias(['nm_schedulinglessoninfo'=>'sl', 'nm_scheduling'=>'s', 'nm_curriculum'=>'cu',])
			->join('nm_scheduling','sl.classid=s.id','LEFT')
			->join('nm_curriculum','s.curriculumid=cu.id','LEFT')
			->where($where);
		if($ca_one_findin){
            $slcDb = $slcDb->where($ca_one_findin);
        }
		if($ca_two_findin){
            $slcDb = $slcDb->where($ca_two_findin);
        }
		$slcount = $slcDb->count();
		return $slcount;
    }
	
	/**
	 * 检查某课时是否已布置作业
	 * @param id
	 * @return [bool]
	 */
	public function checkSchedulingLessonByPeriodid($periodid){
		$lessonsmodel = new Lessons;
		$lessonsarr = $lessonsmodel->getFieldByPeriodid($periodid);
		
		$lessonids = array_column($lessonsarr, 'id');
		return Db::table($this->table)
        ->whereIn('lessonid',$lessonids)
        ->field('id')->find();
	}
	
	/**
	 * 检查班级课时的批阅状态
	 * @param classid
	 * @param lessonid
	 * @return [bool]
	 */
	public function getReviewStatus($classid, $lessonid){
		return Db::table($this->table)
        ->where('classid', $classid)
        ->where('lessonid', $lessonid)
        ->field('reviewstatus')->find();
	}
}
