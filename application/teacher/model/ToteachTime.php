<?php

namespace app\teacher\model;
use think\Db;
use think\Model;

class ToteachTime extends Model
{
    protected $pk    = 'id';
	protected $table = 'nm_toteachtime';
	    /**
	 * [获取用户的数据]
	 * @Author wangwy
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   [机构id]
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
    public function getteachtime($whe){
    	return Db::table($this->table)->alias('c')
    	           ->join('nm_lessons t','c.lessonsid = t.id')
                   ->where($whe)
    	           // ->where('c.teacherid','eq',$teacherid)
    	           // ->where('c.organid','eq',$organid)
    	           // ->where('c.lessonsid','eq',$lessonsid)
    	           ->field('c.type,t.courseware,t.periodname,c.schedulingid')
    	           ->find();
    }


    /*
     * [获取该课时所属课程的学生列表]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getStudlists($whe){
        return Db::table($this->table)->alias('c')
                   ->join('nm_studentinfo t','c.studentid = t.id')
                   ->where($whe)
                   // ->where('c.teacherid','eq',$teacherid)
                   // ->where('c.organid','eq',$organid)
                   // ->where('c.lessonsid','eq',$lessonsid)
                   ->field('t.nickname,t.imageurl')
                   ->select();
    }
    public function showorderStudentid($whe){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_ordermanage t','c.schedulingid = t.schedulingid','right')
            ->where($whe)
            ->column('t.studentid');
    }

    /*
     * [获取该课时所属课程的学生列表]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getorderStudlists($whe,$pagenum,$pagesize){
        $data['data'] = Db::table($this->table)
            ->alias('c')
            ->join('nm_ordermanage t','c.schedulingid = t.schedulingid','right')
            ->join('nm_studentinfo s','t.studentid = s.id','RIGHT')
            ->where($whe)
            ->page($pagenum,$pagesize)
            // ->where('c.teacherid','eq',$teacherid)
            // ->where('c.organid','eq',$organid)
            // ->where('c.lessonsid','eq',$lessonsid)
            ->column('s.id,s.nickname,s.imageurl,s.mobile,s.sex,s.birth,s.country,s.province,s.city,s.profile');

        //return Db::table($this->table)->getLastSql();
        $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getorderStucount($whe));
        return $data;
    }

    /**该课时所属课程的学生总数
     * @param $whe
     * @return int|string
     */
    public function getorderStucount($whe){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_ordermanage t','c.schedulingid = t.schedulingid')
            ->join('nm_studentinfo s','t.studentid = s.id')
            ->where($whe)
            ->count();
    }
    /*
     * [获取该课时所属课程的学生列表]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getStudlistforapp($whe){
        return Db::table($this->table)
                   ->alias('c')
                   ->join('nm_ordermanage s','c.schedulingid = s.schedulingid')
                   ->join('nm_studentinfo t','s.studentid = t.id')
                   ->where($whe)
                   // ->where('c.teacherid','eq',$teacherid)
                   // ->where('c.organid','eq',$organid)
                   // ->where('c.lessonsid','eq',$lessonsid)
                   ->field('t.nickname,t.imageurl,t.country,t.province,t.city,t.birth,t.sex')
                   ->select();
    }
    /*
    * [获取该课时所属课程的学生列表]
    * @Author wangwy
    * @DateTime 2018-04-19T15:31:56+0800
    * @param    [int]        $organid   [机构id]
    * @param    [int]        $teacherid [教师id]
    * @return   [type]                  [description]
    */
    public function getStudlistsapp($whe){
        return Db::table($this->table)->alias('c')
            ->join('nm_studentinfo t','c.studentid = t.id')
            ->where($whe)
            // ->where('c.teacherid','eq',$teacherid)
            // ->where('c.organid','eq',$organid)
            // ->where('c.lessonsid','eq',$lessonsid)
            ->field('t.nickname,t.imageurl,t.country,t.province,t.city,t.birth,t.sex')
            ->select();
    }
    /*
     * [获取该课时所属课程教师真实名字]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function getTeachnameforapp($whe){
        return Db::table($this->table)->alias('c')
                   ->join('nm_teacherinfo t','c.teacherid = t.teacherid')
                   ->where($whe)
                   // ->where('c.teacherid','eq',$teacherid)
                   // ->where('c.organid','eq',$organid)
                   // ->where('c.lessonsid','eq',$lessonsid)
                   ->field('t.teachername')
                   ->find();
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
    public function teachCourseList($starttime,$endtime,$teacherid){

        $field = 'c.intime,count(c.id) num' ;
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_scheduling s','c.schedulingid = s.id','LEFT')
            ->where('s.realnum','egt',1)//没有被学生下单支付的课程无法进入课程表
            ->where('c.teacherid','EQ',$teacherid)
            ->where('c.intime','EGT',$starttime)
            ->where('c.intime','ELT',$endtime)
            ->where('c.delflag','EQ',1)
            ->where('s.delflag','EQ',1)
            ->group('c.intime')
            ->column($field) ;
    }
    /*
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $organid [机构id]
     * @return   [array]                          [description]
     */
    public function getLessonsByDate($date,$teacherid,$limitstr){
        $field = 'tt.id,tt.schedulingid,tt.lessonsid,tt.status,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,ls.periodname,ls.periodsort,ls.curriculumid,ls.classhour,sd.gradename';

        return Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sd'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            // ->join('nm_curriculum','ls.curriculumid=cc.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sd.id','LEFT')
            ->where('tt.teacherid','EQ',$teacherid)
            ->where('tt.intime','EQ',$date)
            ->where('tt.delflag','EQ',1)
            ->where('sd.realnum','egt',1)//没有被学生下单支付的课程无法进入课程表
            ->field($field)
            ->limit($limitstr)
            // ->fetchSql()
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
    public function getLessonsByDateCount($date,$teacherid){

        return Db::table($this->table)
        ->where('teacherid','EQ',$teacherid)
        ->where('intime','EQ',$date)
        ->where('delflag','EQ',1)
        ->count();
    }


    /**
     * [findWeekMark 获取对应的老师当前课程占用时间
     * @Author jcr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $where   查询参数
     * @param    [int]        $id      机构id
     */
    public function getTimekey($where,$id){
        $list = Db::table($this->table);
        if($id){
            $list = $list->where('schedulingid','neq',$id);
        }
        $counts = count($where);
        foreach ($where as $key => $value) {
            if($key==0){
                //$list = $list->where(" ( intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'].$str);
                $list = $list->where(['intime' => $value['intime'],'teacherid' => $value['teacherid'],'delflag' => $value['delflag']]);
            }else{
                //$str = $key==$counts-1?')':'';
                //$list = $list->whereor(" intime = '".$value['intime']."' and teacherid = ".$value['teacherid']." and delflag =  ".$value['delflag'].$str );
                $list = $list->whereor(['intime' => $value['intime'],'teacherid' => $value['teacherid'],'delflag' => $value['delflag']]);
            }
        }

        $list = $list->field('id,intime,teacherid,timekey')->select();
        return $list;
    }


    /*
     * [getLessonsByall 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期数组]
     * @param    [int]                   $organid [机构id]
     * @return   [array]                          [description]
     */
    public function getLessonsByall($where,$pagenum){
        $field = 'tt.id,tt.lessonsid,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,tt.schedulingid,ls.periodname,ls.periodsort,ls.curriculumid,ls.classhour' ;

        $data['data'] = Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sk'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            // ->join('nm_curriculum','ls.curriculumid=cc.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sk.id','LEFT')
            ->where($where)
            ->field($field)
            ->select();
        //return Db::table($this->table)->getlastsql();
        $data['pageinfo'] = array('pagenum'=>$pagenum,'total'=>$this->getListCount($where));
        return $data;
    }

   /**
    * getId 查询对应日期的课程数量
    * @ WangWY
    * @param $where 查询条件
    * @return array();
    */

    public function getListCount($where){
        return  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sk'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            // ->join('nm_curriculum','ls.curriculumid=cc.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sk.id','LEFT')
            ->where($where)
            ->count();
    }
    /*
     * 获取符合条件的日期
     *  @Author wangwy
     */
    public function getListDay($where){
        return  Db::table($this->table)
            ->alias([$this->table=>'tt','nm_lessons'=>'ls','nm_scheduling'=>'sk'])
            ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
            // ->join('nm_curriculum','ls.curriculumid=cc.id','LEFT')
            ->join('nm_scheduling','tt.schedulingid=sk.id','LEFT')
            ->where($where)
            ->column('tt.id,tt.intime');
    }
    /*
     * 根据 开课信息删除对应时间
     * @ jcr
     * @param $data 添加数据源
     */
    public function deletes($id){
        $ids = Db::table($this->table)->where('schedulingid','eq',$id)->update(['delflag'=>0]);
        // print_r(Db::table($this->table)->getLastSql());
        // exit();
        return $ids;
    }

    /**
    *
    *
    */ 
    public function getTimeList($toteachid,$status=null){
        if(isset($status)){
            $whe = ['t.status'=>$status];
        }else{
            $whe = [];
        }
        $field = 't.id,t.intime,t.timekey,t.schedulingid,l.courseware,l.periodname,s.classhour,t.type,s.periodnum,s.realnum,s.classstatus';
        $list = Db::table($this->table)->alias('t')
            ->join('nm_lessons l','t.lessonsid = l.id','LEFT')
            ->join('nm_scheduling s','s.id = t.schedulingid','LEFT')
            ->where('t.id','eq',$toteachid)
            ->where($whe)
            ->where('t.delflag',1)
            ->where('s.delflag','eq',1)
            ->field($field)
            ->find();
        return $list;

    }

    /**
     *  根据schedulingid查询该表中该排课是否创建主键
     */
    public function getTotimeSchelist($schedulingid){
        return Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->field('id,schedulingid')
            ->select();
    }
    /*
     *  根据toteachid获取当前教师状态
     *  @Author wangwy
     */
    public function getStatus($toteachid){
        return Db::table($this->table)
                ->where('id','eq',$toteachid)
                ->field('status')
                ->find();
    }

    /*
     *  获取主键id，teacherid,studentid
     *  @Author wangwy
     */
    public function getMobileformg($where,$file){
        return Db::table($this->table)
            ->alias('c')
            ->where($where)
            ->field($file)
            ->select();
    }
    /**
     * @param $lessonsid
     * @return array
     */
    public function getTimeall($lessonsidarr){
        $where = ['lessonsid'=>['in',$lessonsidarr]];
        return Db::table($this->table)
            ->where($where)
            ->column('teacherid,intime,timekey','lessonsid');
    }
    public function getAllfind($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->field($field)
            ->find();
    }


}
