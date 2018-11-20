<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Toteachtime extends Model
{
    protected $pk    = 'id';
    protected $table = 'nm_toteachtime';
    protected $completestatus = 20;
    //订单取消状态
    protected $cancelstatus = 10;
    //已经下单状态
    protected $orderstatus = 0;

    // 课程添加验证规则
    protected $rule = [
        'intime'  => 'require',
        'timekey'  => 'require',
    ];

    protected $message  = [
        'intime.require' => '课时上课日期',
        'timekey.require' => '课时指定上课时间不能为空'
    ];


    public function __construct(){
        $this->pagenum = config('paginate.list_rows');
    }
    /**
     * [getLessonsTime 获取已学上课时间数量]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getLessonsTime($learnlist,$ordernum){
        $date = time();
        $data =  Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('ordernum','eq',$ordernum)
            ->where('endtime','lt',$date)
            ->where('lessonsid','in',$learnlist)
            ->count();
        return $data;
    }
    /**
     * [getLessonsCount 小班课大班课获取已学上课时间数量]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getLessonsCount($learnlist){
        $date = time();
        $data =  Db::table($this->table)
            ->where('endtime','lt',$date)
            ->where('lessonsid','in',$learnlist)
            ->count();
        return $data;
    }
    /**
     * [getToteachInfo 按条件查询]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @return   [type]                  [description]
     */
    public function getToteachInfo($where){
        $data =  Db::table($this->table)
            ->field('id as toteachid,intime,timekey,lessonsid,studentid')
            ->where($where)
            ->find();
        $sql = Db::table($this->table)->getLastSql();
        return $data;
    }
    /**
     * [getClssTime 获取上课时间总数]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $scheduid [排课id]
     * @return   [type]                  [description]
     */
    public function getClssTime($studentid,$schedulingid,$ordernum){
        $data =  Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('studentid','eq',$studentid)
            ->where('schedulingid','eq',$schedulingid)
            ->where('ordernum','eq',$ordernum)
            ->count();
        return $data;
    }
    /**
     * [getDateInfo 获取指定日期是否有排课]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   [type]                  [description]
     */
    public function getDateInfo($teacherid,$date){
        $data =  Db::table($this->table)
            ->field('id,intime,teacherid,timekey')
            ->where('delflag','eq',1)
            ->where('teacherid','eq',$teacherid)
            ->where('intime','eq',$date)
            ->select();
        return $data;
    }
    /**
     * [findWeekMark 获取一对一时间掩码]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getTimekey($where){
        $list = Db::table($this->table);
        foreach ($where as $key => $value) {
            if($key==0){
                $list = $list->where(" intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'] );
            }else{
                $list = $list->whereor(" intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'] );
            }
        }
        $list = $list->field('id,intime,teacherid,timekey')->select();
        return $list;
    }
    /**
     * [findWeekMark 获取一对一时间掩码]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getTimekeyByOrdernum($ordernum,$lessonsid){
        $res = Db::table($this->table)
            ->field('timekey,id as toteachid,intime')
            ->where('ordernum','eq',$ordernum)
            ->where('lessonsid','eq',$lessonsid)
            ->where('delflag','eq',1)
            ->find();
        $sql = Db::table($this->table)->getLastSql();
        return $res;
    }
    /**
     * [findWeekMark 获取一对一时间掩码]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getTimekeyByStudent($studentid,$schedulingid,$lessonsid){
        $res = Db::table($this->table)
            ->field('timekey,id as toteachid,intime')
            ->where('studentid','eq',$studentid)
            ->where('schedulingid','eq',$schedulingid)
            ->where('lessonsid','eq',$lessonsid)
            ->where('delflag','eq',1)
            ->find();
        $sql = Db::table($this->table)->getLastSql();
        return $res;
    }
    /**
     * [studentCourseList 获取学生的课]
     * @Author yr
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $starttime [开始时间]
     * @param    [int]                   $endtime   [结束时间]
     * @return   [array]                              [description]
     */
    public function studentCourseList($starttime,$endtime,$studentid){
        $field = 'intime,count(id) num' ;
        $result = Db::table($this->table)->where(function ($query) use($starttime,$endtime,$studentid) {
            $query->where('intime','EGT',$starttime)
                ->where('intime','ELT',$endtime)
                ->where('schedulingid','IN',function($query) use($studentid){
                    $query->table('nm_ordermanage')->where('studentid','eq',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                });
            })
        ->group('intime')
        ->column($field) ;
        return $result;
    }
    /**
     * [studentCourseList 获取学生的课]
     * @Author yr
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $starttime [开始时间]
     * @param    [int]                   $endtime   [结束时间]
     * @return   [array]                              [description]
     */
    public function studentCourseDateList($starttime,$endtime,$lessonsids){
        $field = 'intime,count(id) num' ;
        $result = Db::table($this->table)
                ->where('intime','EGT',$starttime)
                ->where('intime','ELT',$endtime)
                ->where('lessonsid','IN',$lessonsids)
                ->group('intime')
                ->column($field) ;

        return $result;
    }
    /**
     * [studentCourseList 获取官方学生的课]
     * @Author yr
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $starttime [开始时间]
     * @param    [int]                   $endtime   [结束时间]
     * @return   [array]                              [description]
     */
    public function webStudentCourseList($starttime,$endtime,$studentid){
        $field = 'intime,count(id) num' ;
        $result = Db::table($this->table)->where(function ($query) use($starttime,$endtime,$studentid) {
            $query->where('intime','EGT',$starttime)
                ->where('intime','ELT',$endtime)
                ->where('type','GT',1)
                ->where('schedulingid','IN',function($query) use($studentid){
                    $query->table('nm_ordermanage')->where('studentid','eq',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                });
        })->whereOr(function ($query) use($starttime,$endtime,$studentid) {
            $query->where('intime','EGT',$starttime)
                ->where('intime','ELT',$endtime)
                ->where('type','EQ',1)
                ->where('studentid','EQ',$studentid)
                ->where('schedulingid','IN',function($query) use($studentid){
                    $query->table('nm_ordermanage')->where('studentid','EQ',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                });
        })
            ->group('intime')
            ->column($field) ;
        return $result;
    }
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getLessonsByDate($date,$studentid){
        $field = 'sg.curriculumid,sg.gradename,tt.starttime,tt.endtime,tt.schedulingid,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,tt.id as toteachid,ls.periodname,ls.periodsort,ls.curriculumid,sg.classhour,ls.id as lessonsid,sg.imageurl' ;
        $result =  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sg'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sg.id','LEFT')
            ->where(function ($query) use($date,$studentid) {
                $query->where('tt.intime','EQ',$date)
                    ->where('tt.schedulingid','IN',function($query) use($studentid){
                        $query->table('nm_ordermanage')->where('studentid','eq',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                    });
            })
            ->field($field)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $result;
    }
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getStudentLessonsByDate($date,$newidarr){
        $field = 'sg.curriculumid,sg.gradename,tt.starttime,tt.endtime,tt.schedulingid,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,tt.id as toteachid,ls.periodname,ls.periodsort,ls.curriculumid,sg.classhour,ls.id as lessonsid,sg.imageurl' ;
        $result =  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sg'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sg.id','LEFT')
            ->where('tt.intime','EQ',$date)
            ->where('tt.lessonsid','in',$newidarr)
            ->field($field)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $result;
    }
    /**
     * [getWaitOrEndLessons 获取待上课列表和已结束列表]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getWaitOrEndLessons($status,$studentid,$limitstr){
        $time = time();
        if($status == 1){
            $where = 'LT';
        }else{
            $where = 'GT';
        }
        $field = 'sg.curriculumid,tt.endtime,tt.schedulingid,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,tt.id as toteachid,ls.periodname,ls.periodsort,ls.curriculumid,sg.classhour,ls.id as lessonsid' ;
        $result =  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sg'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sg.id','LEFT')
            ->where(function ($query) use($studentid,$where,$time) {

                $query->where('tt.endtime',$where,$time)
                    ->where('tt.type','GT',1)
                    ->where('tt.delflag','eq',1)
                    ->where('tt.schedulingid','IN',function($query) use($studentid){
                        $query->table('nm_ordermanage')->where('studentid','eq',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                    });
            })->whereOr(function ($query) use($studentid,$where,$time) {
                $query->where('tt.endtime',$where,$time)
                    ->where('tt.type','EQ',1)
                    ->where('tt.delflag','eq',1)
                    ->where('tt.studentid','EQ',$studentid)
                    ->where('tt.schedulingid','IN',function($query) use($studentid){
                        $query->table('nm_ordermanage')->where('studentid','EQ',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                    });
            })
            ->order('intime')
            ->field($field)
            ->limit($limitstr)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $result;
    }
    /**
     * [getWaitOrEndCount 获取待上课列表或者已结束列表的数量]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getWaitOrEndCount($status,$studentid){
        $time = time();
        if($status == 1){
            $where = 'GT';
        }else{
            $where = 'LT';
        }
        $result =  Db::table($this->table)
            ->alias([$this->table=>'tt'])
            ->where(function ($query) use($studentid,$where,$time) {
                $query->where('tt.endtime',$where,$time)
                    ->where('tt.type','GT',1)
                    ->where('tt.delflag','eq',1)
                    ->where('tt.schedulingid','IN',function($query) use($studentid){
                        $query->table('nm_ordermanage')->where('studentid','eq',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                    });
            })->whereOr(function ($query) use($studentid,$where,$time) {
                $query->where('tt.endtime',$where,$time)
                    ->where('tt.type','EQ',1)
                    ->where('tt.delflag','eq',1)
                    ->where('tt.studentid','EQ',$studentid)
                    ->where('tt.schedulingid','IN',function($query) use($studentid){
                        $query->table('nm_ordermanage')->where('studentid','EQ',$studentid)->where('orderstatus','EQ',$this->completestatus)->field('schedulingid');
                    });
            })
            ->count();
        $sql = Db::table($this->table)->getLastSql();
        return $result;
    }
    /**
     * 添加课时 时间
     * @ yr
     * @param $data 添加数据源
     */
    public function addEdit($data,$type=''){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)&&$type!='all'){
            return array('code'=>10065,'info'=>$validate->getError());
        }

        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','intime','endtime','teacherid','delflag','timekey','insort','endtime'));
            $ids = Db::table($this->table)->where('id','eq',$data['id'])->update($data);
            if($ids>=0){
                $ids = true;
            }
        }else{

            $data = where_filter($data,array('intime','teacherid','type','timekey','lessonsid','schedulingid','coursename','studentid','insort','endtime','ordernum'));
            $ids = Db::table($this->table)->insertGetId($data);

        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10066,'info'=>'添加课时时间失败');
    }
    /**
     * 获取指定字段的数据
     * @ yr
     * @param $data 添加数据源
     */
    public function getArrById($where,$field){
        $result = Db::table($this->table)
            ->where($where)
            ->column($field);
        return $result;
    }
    public function getTimeList($toteachid){
        $field = 't.id,t.intime,t.timekey,t.schedulingid,l.courseware,l.periodname,s.classhour,t.type,t.status,s.periodnum,s.realnum,s.classstatus';
        $list = Db::table($this->table)->alias('t')
            ->join('nm_lessons l','t.lessonsid = l.id','LEFT')
            ->join('nm_scheduling s','s.id = t.schedulingid','LEFT')
            ->where('t.id','eq',$toteachid)
            ->where('t.status',0)
            ->where('t.delflag',1)
            ->field($field)
            ->find();
        return $list;

    }
    /**
     * 获取学生已经上过课的课时数量
     * @ yr
     * @param $data 添加数据源
     */
    public function getStudentAttendNum($where){
        $result = Db::table($this->table)
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * 获取学生已经上过课的课时集合
     * @ yr
     * @param $data 添加数据源
     */
    public function getStudentAttendLesson($where){
        $result = Db::table($this->table)
            ->where($where)
            ->column('lessonsid');
        return $result;
    }
    /**
     * 获取学生已经上过课课时信息
     * @ yr
     * @param $data 添加数据源
     */
    public function getStudentAttendLessons($where,$field){
        $result = Db::table($this->table.' t')
            ->join('nm_teacherinfo ti','t.teacherid=ti.teacherid','left')
            ->join('nm_lessons l','t.lessonsid=l.id','left')
            ->field($field)
            ->where($where)
            ->select();
        return $result;
    }
    /**
     * 获取学生已经上过课的课时数量
     * @ yr
     */
    public function getStartLessons($where,$field){
        $result = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->order('starttime asc')
            ->limit(1)
            ->find();
        return $result;
    }
    /**
     * 获取学生新班级准备上课的课时集合
     * @ yr
     */
    public function getStudentPrepareLesson($where){
        $result = Db::table($this->table)
            ->where($where)
            ->column('lessonsid');
        return $result;
    }
	
	 /**
     * [getStudentLessonsList app/microsite获取课表]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getStudentLessonsList($status,$limitstr,$newidarr,$nowtime){
        $operation = $status == 1 ? '<' : '>=';
        $order = $status == 1 ? 'tt.intime desc,tt.starttime asc' : 'tt.intime asc,tt.starttime asc';
		$field = 'sg.curriculumid,sg.gradename,tt.starttime,tt.endtime,tt.schedulingid,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,tt.id as toteachid,ls.periodname,ls.periodsort,ls.curriculumid,sg.classhour,ls.id as lessonsid,sg.imageurl' ;
        $result =  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sg'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sg.id','LEFT')
            ->where('tt.lessonsid','in',$newidarr)
			->where('tt.endtime', $operation, $nowtime)
            ->field($field)
			->order($order)
			->limit($limitstr)
            ->select();
        return $result;
    }
	
	/**
     * [getStudentLessonsListCount app/microsite获取课表总数]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $studentid [学生id]
     * @return   [array]                          [description]
     */
    public function getStudentLessonsListCount($status,$newidarr,$nowtime){
		$operation = $status == 1 ? '<' : '>=';
        return  Db::table($this->table)
            ->alias([$this->table=>'tt'])
            ->where('tt.lessonsid','in',$newidarr)
			->where('tt.endtime', $operation, $nowtime)
            ->field('tt.id')
            ->count();
    }
    /**
     * 获取学生新班级准备上课的课时集合
     * @ yr
     */
    public function getCourseidOrscheduid($where){
        $result = Db::table($this->table.' t')
            ->field('s.curriculumid,s.delflag')
            ->join('nm_scheduling s','t.schedulingid=s.id','LEFT')
            ->where($where)
            ->find();
        return $result;
    }
}
