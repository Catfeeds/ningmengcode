<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;
use app\admin\model\Exercisesubject;
use app\admin\model\Exercisesubjectoptions;
use app\admin\model\Schedulinglessoninfo;
/*
 * 分类Model
 * @ lc
 */
class Periodexerciseinfo extends Model {

	protected $table = 'nm_periodexerciseinfo';

	// 分类添加验证规则
	protected $rule = [
		/* 'courseid' => 'require|number',
		'periodid' => 'require|number', */
	];	
	protected $message = [];

	//自定义初始化
	protected function initialize() {
		parent::initialize();
		$this->message = [
			/* 'courseid.require' => lang('70405'),
			'periodid.require' => lang('70405'),
			'courseid.number' => lang('70406'),
			'periodid.number' => lang('70406'), */
		];
	}
	
	/**
     * getHaveExerciseCurriculumList 获取已经录入习题的课程
     * @ lc
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limitstr 查询页数
     * @return array();
     */
    public function getHaveExerciseCurriculumList($where,$field,$limitstr,$order='e.courseid asc'){
		 return Db::table($this->table . ' e')
				->join('nm_curriculum c','e.courseid=c.id','LEFT')
				->field($field)
				->where($where)
				->group('e.courseid')
				->limit($limitstr)
				->order($order)
				->select();
    }

    /**
     * getHaveExerciseCurriculumCount 获取已经录入习题的课程总数
     * @ lc
     * @param $where 查询条件
     * @return int;
     */
    public function getHaveExerciseCurriculumCount($where){
		return Db::table($this->table . ' e')
				->join('nm_curriculum c','e.courseid=c.id','LEFT')
				->where($where)
				->group('e.courseid')
				->count();
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
    public function getExerciseList($where,$field,$limitstr,$order='e.id asc')
    {
        return Db::table($this->table)
				->alias(['nm_periodexerciseinfo'=>'e','nm_period'=>'p'])
				->join('nm_period','e.periodid=p.id','LEFT')
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
    public function getExerciseListCount($where){
        return Db::table($this->table)
		->alias(['nm_periodexerciseinfo'=>'e','nm_period'=>'p'])
		->join('nm_period','e.periodid=p.id','LEFT')
		->where($where)
		->count();
    }
	
	/**
	 * 根据courseid和periodid获取习题的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getExerciseData($field, $id)
    {
		$periodExercise = $this->getExerciseDataById($id);
		if(empty($periodExercise)){
			return '';
		}
		$where['courseid'] = $periodExercise['courseid'];
		$where['periodid'] = $periodExercise['periodid'];
		$where['delflag'] = 0;
        
		$periodmodel = new Period;
        $periodarr = $periodmodel->getId($periodExercise['periodid'], 'periodname');
		if(empty($periodarr)) return return_format('',70409);
		
		$result['courseid'] = $periodExercise['courseid'];
		$result['periodid'] = $periodExercise['periodid'];
		$result['periodname'] = $periodarr['periodname'];
		$subjectmodel = new Exercisesubject;
		$optionsmodel = new Exercisesubjectoptions;
		$subjectarr = $subjectmodel->getSubjectList($where,$field,'type asc');
		
		$typearr = [1,2,3,4];
		if(is_array($subjectarr)){
			foreach($subjectarr as $v){
				if(in_array($v['type'], [1,2])){
					$where_o['subjectid'] = $v['id'];
					$where_o['delflag'] = 0;
					$v['options'] = $optionsmodel->getSubjectOptions($where_o);
				}else{
					$v['options'] = '';
				}	
				$subject[$v['type']][] = array('type'=>$v['type'], 'name'=>$v['name'], 'imageurl'=>$v['imageurl'], 'options'=>$v['options'], 'analysis'=>$v['analysis'], 'correctanswer'=>$v['correctanswer'], 'score'=>$v['score']);
			}
			foreach($typearr as $type){
				if(!isset($subject[$type])) $subject[$type] = [];
			}
			$result['subject'] = $subject;
		}
		return $result;
    }
	
	/** 
	 * 添加习题 
	 *
	 */
	public function addExercise($data){
		/* $validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$result){
            return array('code'=>10050,'info'=>$validate->getError());
        } */
		$subjectmodel = new Exercisesubject;
		$optionstmodel = new Exercisesubjectoptions;
		$alldata = [
			'courseid' => $data['courseid'] ,
			'periodid' => $data['periodid'] ,
			'subjectcount' => $data['subjectcount'] ,
			'updatetime' => $data['updatetime'] ,
		];
		//开启事务
        Db::startTrans();
		$logflag = Db::table($this->table)->insert($alldata);
        if(!$logflag){
			Db::rollback();
			return return_format('',40101);
		}
		foreach($data['subject'] as $val){
			if(!empty($val)){
				foreach($val as $v){
					$subjectRet['courseid'] = $data['courseid'];
					$subjectRet['periodid'] = $data['periodid'];
					$subjectRet['type'] = $v['type'];
					$subjectRet['name'] = $v['name'];
					if(!empty($v['imageurl'])) $subjectRet['imageurl']= $v['imageurl'];
					$subjectRet['analysis'] = $v['analysis'];
					$subjectRet['correctanswer'] = $v['correctanswer'];
					$subjectRet['score'] = $v['score'];
					$subjectid = $subjectmodel->insertOneSubject($subjectRet);
					
					if(!$subjectid){
							Db::rollback();
							return return_format('',40101);
						}
					if(in_array($v['type'], [1,2]) && !empty($v['options'])){
						foreach($v['options'] as $k=>$option){
							$optionRet[$k]['subjectid'] = $subjectid;
							$optionRet[$k]['optionname'] = $option;
						}
						$flagoption = $optionstmodel->insertOptions($optionRet);
						if(!$flagoption){
							Db::rollback();
							return return_format('',40101);
						}
					}
				}
			}
		}
		
        //处理完成 提交事务
        Db::commit();
        return array('code'=>0,'info'=>lang('success'));
	}
	 
	/** 
	 * 编辑习题 
	 *
	 */
	public function updateExercise($data){
		//检查该课时习题是否存在
		$periodExercise = $this->getExerciseDataById($data['id']);
		if(empty($periodExercise)){
			return return_format('',70407);
		}
		$alldata = [
			'subjectcount' => $data['subjectcount'],
			'updatetime' => $data['updatetime'],
		];
		
		$where['id'] = $data['id'];
		
		//开启事务
        Db::startTrans();
		$logflag = Db::table($this->table)->where($where)->update($alldata);
        if(!$logflag){
			Db::rollback();
			return return_format('',40101);
		}
		
		$subjectmodel = new Exercisesubject;
		$subjectids = $subjectmodel->getSubjectidsByPeriodid($periodExercise['periodid']);
		
		//假删除原来习题题干
		$r2 = $subjectmodel->delExerciseSubjectByPeriodid($periodExercise['periodid']);
        if(!$r2){
			Db::rollback();
			return return_format('',40101);
		}
		
		//删除习题原来题目选项
		$optionstmodel = new Exercisesubjectoptions;
		if(!empty($optionstmodel->getDatasBySubjectids($subjectids))){
			$r3 = $optionstmodel->delOptionsBySubjectid($subjectids);
			if(!$r3){
				Db::rollback();
				return return_format('',40101);
			}
		}
		
		foreach($data['subject'] as $val){
			if(!empty($val)){
				foreach($val as $v){
					$subjectRet['courseid'] = $periodExercise['courseid'];
					$subjectRet['periodid'] = $periodExercise['periodid'];
					$subjectRet['type'] = $v['type'];
					$subjectRet['name'] = $v['name'];
					if(!empty($v['imageurl'])) $subjectRet['imageurl']= $v['imageurl'];
					$subjectRet['analysis'] = $v['analysis'];
					$subjectRet['correctanswer'] = $v['correctanswer'];
					$subjectRet['score'] = $v['score'];
					$subjectid = $subjectmodel->insertOneSubject($subjectRet);
					if(!$subjectid){
						Db::rollback();
						return return_format('',40101);
					}
					if(in_array($v['type'], [1,2]) && !empty($v['options'])){
						foreach($v['options'] as $k=>$option){
							$optionRet[$k]['subjectid'] = $subjectid;
							$optionRet[$k]['optionname'] = $option;
						}
						$flagoption = $optionstmodel->insertOptions($optionRet);
						if(!$flagoption){
							Db::rollback();
							return return_format('',40101);
						}
					}
				}
			}	
			
		}
		
        //处理完成 提交事务
        Db::commit();
        return array('code'=>0,'info'=>lang('success'));
	}
	 
	 /**
     * [delKnowledge 删除]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [知识id]
     * @return   [type]    [description]
     */
    public function delExercise($id, $periodid){	
		$where['id'] = $id;
		
		//开启事务
        Db::startTrans();
		//删除习题数据
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if(!$r){
			Db::rollback();
            return array('code'=>10020,'info'=>lang('error'));
		}
		
		//删除题干
		$Subjectobj = new ExerciseSubject;
		$subjectids = $Subjectobj->getSubjectidsByPeriodid($periodid);
		if(!empty($subjectids)){
			$r2 = $Subjectobj->delExerciseSubjectByPeriodid($periodid);
			if(!$r2){
				Db::rollback();
				return array('code'=>10020,'info'=>lang('error'));
			}
		}
		
		//删除习题
		$SubjectOptionsobj = new ExerciseSubjectOptions;
		if(!empty($SubjectOptionsobj->getDatasBySubjectids($subjectids))){
			$r3 = $SubjectOptionsobj->delOptionsBySubjectid($subjectids);
			if(!$r3){
				Db::rollback();
				return array('code'=>10020,'info'=>lang('error'));
			}
		}
		
		//return return_format('',0);
		
		//处理完成 提交事务
        Db::commit();
        return array('code'=>0,'info'=>lang('success'));
		
    }
	
	/** 获取课程下试题数
	 * 
	 * @lc
	 */
	public function getPeriodExerciseCount($courseid){
		$where['courseid'] = $courseid;
		$where['delflag'] = 0;
		return Db::table($this->table)->where($where)->count();
	}

	/**
	 * checkCategoryExsit 检查是否存在
	 * @param id
	 * @return [bool]
	 */
	/* public function checkExerciseExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->field('id')->find();
		return !empty($result) ? true : false;
	} */
	
	/**
	 * 通过习题ID获取courseid和periodid
	 * @param id
	 * @return [bool]
	 */
	public function getExerciseDataById($id){
		$periodExercise = Db::table($this->table)->where(['id'=>$id, 'delflag'=>0])->field('courseid,periodid')->find();
		
		if($periodExercise){
			return $periodExercise;
		}else{
			return '';
		}
	}
	
	/**
	 * 通过condition获取习题数据
	 * @param $where
	 * @param $field
	 * @return array
	 */
	public function getFieldByCondition($where, $field){
		return Db::table($this->table)->where($where)->field($field)->find();
	}
}
