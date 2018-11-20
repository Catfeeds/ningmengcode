<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Toteachtime extends Model
{	
	protected $pk    = 'id';
	protected $table = 'toteachtime';

	
	 // 课程添加验证规则
    protected $rule = [
        'intime'  => 'require',
        'teacherid'  => 'require',
        'timekey'  => 'require',
    ];

    protected $message  = [];

    //自定义初始化
    protected function initialize(){        
        $this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
			'intime.require' => lang('10509'),
			'teacherid.require' => lang('10510'),
			'timekey.require' => lang('10511')
		];
    }
    
    /**
     * [organCourseList 获取机构老师的课]
     * @Author wyx
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $starttime [开始时间]
     * @param    [int]                   $endtime   [结束时间]
     * @param    [int]                   $organid   [机构标识]
     * @return   [array]                              [description]
     */
    public function organCourseList($starttime,$endtime){
        
        $field = 'tt.intime,count(tt.id) num' ;
        return Db::name($this->table)->alias('tt')
            ->join('nm_scheduling sd','tt.schedulingid=sd.id','left')
            ->where('tt.intime','EGT',$starttime)
            ->where('tt.intime','ELT',$endtime)
            ->where('tt.delflag','EQ',1)
            ->where('sd.realnum','GT',0) // 仅仅查询有报名的
            ->group('tt.intime')
            ->column($field) ;
    }
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $organid [机构id]
     * @param    [string]                $limitstr [分页信息]
     * @return   [array]                          [description]
     */
    public function getLessonsByDate($date,$limitstr){

        $field = 'tt.id,tt.schedulingid,tt.lessonsid,tt.status,tt.endtime,tt.starttime,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,ls.periodname,ls.periodsort,ls.curriculumid,ls.classhour' ;
        return Db::name($this->table)
            ->alias(['nm_'.$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sd'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sd.id','LEFT')
            ->where('tt.intime','EQ',$date)
            ->where('tt.delflag','EQ',1)
            ->where('sd.realnum','egt',1)//没有被学生下单支付的课程无法进入课程表
            ->field($field)->limit($limitstr)
            ->select();
    }
    /**
     * [getLessonsByDateCount 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-05-08
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $organid [机构id]
     * @return   [array]                          [description]
     */
    public function getLessonsByDateCount($date){
        
        return Db::name($this->table)
            ->alias(['nm_'.$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sd'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sd.id','LEFT')
            ->where('tt.intime','EQ',$date)
            ->where('tt.delflag','EQ',1)
            ->where('sd.realnum','egt',1)//没有被学生下单支付的课程无法进入课程表
            ->count();
    }
    /**
     *  获取机构 时间段内每天的课程分布
     *  @author wyx
     *  @param  $startdate  开始时间
     *  @param  $enddate    结束时间
     *  @param  $organid    机构标识id
     *
     */
    public function getCoursePlanByDate($startdate,$enddate){
        $list = Db::name($this->table)
        ->join('nm_scheduling','nm_toteachtime.schedulingid=nm_scheduling.id','LEFT')
        ->field('nm_toteachtime.intime,count(nm_toteachtime.id) num,sum(nm_scheduling.realnum) allrealnum')
        ->where('nm_toteachtime.intime','BETWEEN',$startdate.','.$enddate)
        ->where('nm_scheduling.realnum','GT',0)
		->where('nm_toteachtime.delflag','EQ',1)
        ->group('nm_toteachtime.intime')
        ->select();
//        dump(Db::getlastsql());
        return $list;
    }
    /**
     *  获取机构今天的 每个小时的课程分布
     *  @author wyx
     *  @param  $date  获取一天的数据 ，然后根据 timekey 来按时段划分
     *  @param  $organid    机构标识id
     */
    public function getCoursePlanByDay($date){
        $list = Db::name($this->table)
        ->join('nm_scheduling','nm_toteachtime.schedulingid=nm_scheduling.id','LEFT')
        ->where('nm_toteachtime.intime','EQ',$date)
        ->where('nm_scheduling.realnum','GT',0)
		->where('nm_toteachtime.delflag','EQ',1)
        ->field('timekey,nm_scheduling.realnum,nm_toteachtime.id')
        ->select();
//		dump(Db::getlastsql());
		return $list;
    }
	/**
	 * [findWeekMark 获取对应的老师当前课程占用时间
	 * @Author jcr
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $where   查询参数
	 * @param    [int]        $id 	   机构id
	 */
	public function getTimekey($where,$id){
		$list = Db::name($this->table);
		if($id){
			$list = $list->where('schedulingid','neq',$id);
		}
		$counts = count($where);
		foreach ($where as $key => $value) {
			if($key==0){
				$str = $counts==1?')':'';
				$list = $list->where(" ( intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'].$str);
			}else{
				$str = $key==$counts-1?')':'';
				$list = $list->whereor(" intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'].$str );
			}
		}

		$list = $list->field('id,intime,teacherid,timekey')->select();
		return $list;
	}

	/**
     * getId 根据课时id去查询 对应课时的预约时间
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getLessonsIds($ids){
        $lists = Db::name($this->table)
                            ->where('lessonsid','in',$ids)
                            ->where('type','neq',1)
                            ->where('delflag','eq',1)
                            ->field('id,timekey,lessonsid,intime')->select();
        return $lists;
     }


	/**
     * 添加课时 时间
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data,$type=''){
    	
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)&&$type!='all'){
            return array('code'=>10065,'info'=>$validate->getError());
        }
        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','intime','teacherid','delflag','timekey'));
            $ids = Db::name($this->table)->where('id','eq',$data['id'])->update($data);
        }else{
            //添加
            if($type=='all'){
                //批量插入
                $ids = Db::name($this->table)->insertAll($data);
            }else{
                $data = where_filter($data,array('id','intime','teacherid','delflag','timekey','lessonsid','schedulingid'));
                $ids = Db::name($this->table)->insertGetId($data);
            }
        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10066,'info'=>'添加课时时间失败');
    }


    /**
     * 根据课时编辑 对应时间
     */
    public function editlessonsid($data){
        $data = where_filter($data,array('id','intime','teacherid','delflag','timekey','insort','endtime','starttime'));
        $id = $data['id'];
        unset($data['id']);
        $ids = Db::name($this->table)->where('lessonsid','eq',$id)->update($data);
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10066,'info'=>'添加课时时间失败');
    }


    /**
     * 根据课时时间表id 进行编辑
     */
    public function editId($data){
        $data = where_filter($data,array('id','intime','teacherid','delflag','timekey','insort','status'));
        $ids = Db::name($this->table)->where('id','eq',$data['id'])->update($data);
        // var_dump($ids);
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10066,'info'=>'编辑异常');
    }

    /**
     * 根据 开课信息删除对应时间
     * @ jcr
     * @param $data 添加数据源
     */
    public function deletes($id){
        $ids = Db::name($this->table)->where('schedulingid','eq',$id)->update(['delflag'=>0]);       
        return $ids;
    }


    /**
     * [getDatalist 获取当天符合条件的数据]
     * @param $type 1 取当天的 课程开始第一条课时  2取当天的 课程最后一条课时
     * @return [type] [description]
     */
    public function getDatalist($type=1){
        $timestr = date('Y-m-d',time());

//         $timestr = '2018-06-14';
        $list = Db::name($this->table)->where('delflag','eq',1)
                                      ->where('intime','eq',$timestr)
                                      ->where('insort','eq',$type)
                                      ->field('id,intime,type,timekey,lessonsid,schedulingid,studentid,ordernum,endtime')
                                      ->select();
        return $list;
    }


	/**
	 * editToteach 更新教室结束
	 * @param $id		toteachertime id
	 * @param $serial   classroom     教室号
	 * @return int
	 */
    public function editToteach($id){
    	return Db::name($this->table)->where('id',$id)->update(['status'=>2]);
	}



    /**
     * [getTimeList  获取指定的课时去开教室 定时任务，定时查询当天预约开教室]
     * @return [type] [description]
     */
    public function getTimeList(){
        $timestr = date('Y-m-d',time());
        $field = 't.id,t.intime,t.type,t.timekey,t.starttime,t.schedulingid,l.courseware,l.periodname,l.classhour,s.periodnum,s.realnum,s.classstatus';
        $list = Db::name($this->table)->alias('t')
                                    ->join('nm_lessons l','t.lessonsid = l.id')
                                    ->join('nm_scheduling s','s.id = t.schedulingid')
                                    ->where('t.intime','eq',$timestr)
                                    ->where('t.status',0)
                                    ->where('t.delflag',1)
									->where('s.classstatus','neq',6)
                                    ->field($field)
                                    ->select();
        return $list;

    }



    public function getCount($where){
    	return Db::name($this->table)->where($where)->count();
	}

	/**
     * 根据schedulingid修改teacherid
     */
    public function editBySchedulingid($data){
        $data = where_filter($data, array('schedulingid','teacherid'));
        $schedulingid = $data['schedulingid'];
        unset($data['schedulingid']);
        $ids = Db::name($this->table)->where('schedulingid','eq',$schedulingid)->where('delflag', 1)->update($data);
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>11038,'info'=>lang('11038'));
    }
	
	/**
	 * getFieldByWhere 通过where查找字段
	 * @param $where 查找条件
	 * @param $field 返回字段
	 * @return array
	 */
	public function getFieldByWhere($where,$field){
        return Db::name($this->table)
            ->where($where)
            ->field($field)
            ->find();
    }
}
