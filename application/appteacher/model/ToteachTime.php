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


        /**
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

        /**
     * [organCourseList 获取机构老师的课]
     * @Author wyx
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $starttime [开始时间]
     * @param    [int]                   $endtime   [结束时间]
     * @param    [int]                   $organid   [机构标识]
     * @return   [array]                              [description]
     */
    public function teachCourseList($starttime,$endtime,$teacherid,$organid){

        $field = 'intime,count(id) num' ;
        return Db::table($this->table)
        ->where('teacherid','EQ',$teacherid)
        ->where('organid','EQ',$organid)
        ->where('intime','EGT',$starttime)
        ->where('intime','ELT',$endtime)
        ->where('delflag','EQ',1)
        ->group('intime')
        ->column($field) ;
    }
    /**
     * [getLessonsByDate 根据日期获取课程详情]
     * @Author
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [string]                $date    [要获取的日期]
     * @param    [int]                   $organid [机构id]
     * @return   [array]                          [description]
     */
    public function getLessonsByDate($date,$teacherid,$organid,$limitstr){
        $field = 'tt.id,tt.intime,tt.timekey,tt.coursename,tt.type,tt.teacherid,ls.periodname,ls.periodsort,ls.curriculumid' ;

        return Db::table($this->table)
        ->alias([$this->table=>'tt','nm_lessons'=>'ls'])
        ->join('nm_lessons','tt.lessonsid=ls.id','LEFT')
        // ->join('nm_curriculum','ls.curriculumid=cc.id','LEFT')
        // ->join('nm_scheduling','tt.schedulingid=sd.id','LEFT')
        ->where('tt.teacherid','EQ',$teacherid)
        ->where('tt.organid','EQ',$organid)
        ->where('tt.intime','EQ',$date)
        ->where('tt.delflag','EQ',1)
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
    public function getLessonsByDateCount($date,$teacherid,$organid){

        return Db::name($this->table)
        ->where('teacherid','EQ',$teacherid)
        ->where('organid','EQ',$organid)
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





}
