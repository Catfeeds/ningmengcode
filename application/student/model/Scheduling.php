<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 课程推荐Model
 * @ yr
*/
class Scheduling extends Model{
    protected $table = 'nm_scheduling';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getCourserList 获取机构推荐课程List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getCourserList(){
        $lists =Db::table($this->table.' s')
            ->field('s.curriculumid,s.type,s.totalprice,s.sortnum,t.nickname as teachername,c.imageurl,c.coursename,s.id as scheduid,s.gradename')
            ->join('nm_curriculum c','s.curriculumid = c.id','left')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','left')
            ->where('s.recommend','eq','1')
            ->where('s.status','eq','1')
            ->where('s.delflag','eq','1')
            ->where('s.classstatus','in','0,1,2,3')
            ->order('s.sortnum')
            ->limit(10)
            ->select();
        return  $lists;
    }
    /**
     * [getCourserList 获取指定老师推荐课程List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getTeacherCourse($teacherid){
        $lists =Db::table($this->table)->table('nm_scheduling s, nm_curriculum c ,nm_teacherinfo t')
            ->where('s.curriculumid = c.id')
            ->where('s.teacherid = t.teacherid')
            ->where('s.recommend','eq','1')
            ->where('s.teacherid',$teacherid)
            /*->where('s.classstatus','in','0,1,2,3,4')*/
            ->field('s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,s.id as scheduid,s.gradename')
            ->order('s.sortnum')
            ->limit(6)
            ->select();
        return  $lists;
    }
    /**
     * [getCourserList 获取指定老师课程List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getTeacherList($teacherid){
        $lists =Db::table($this->table.' s')
            ->field('s.curriculumid,s.gradename,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.generalize,c.subhead,s.id as scheduid')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.teacherid','eq',$teacherid)
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
           /* ->where('s.classstatus','in','0,1,2,3,4')*/
            ->order('s.sortnum')
            ->select();
        return  $lists;
    }
    /**
     * [getFilterCourserList 按分类筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserList($categoryid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
         /*   ->where('s.classstatus','in','0,1,2,3,4')*/

            ->where('s.curriculumid','IN',function($query) use($categoryid,$organid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','in',$categoryid);
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }

    /**
     * [getCourserListByAll 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAll($categoryid,$tagids,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
           /* ->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagids){
                $query->field('id')->table('nm_curriculum')->where('categoryid','in',$categoryid)->where($tagids);
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this ->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserCount($categoryid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            /*->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','in',$categoryid);

            })
            ->count();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAllCount($categoryid,$tagid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
     /*       ->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','in',$categoryid)->where($tagid);
            })
           ->count();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getCourserListByCname 按课程名称查询班级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByCname($search,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.gradename,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
           /* ->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($search){
                $query->field('id')->table('nm_curriculum')->where('coursename','like',"%$search%");
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListCount($search){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            /*->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($search){
                $query->field('id')->table('nm_curriculum')->where('coursename','like',"%$search%");

            })
            ->count();
        return  $lists;
    }
    /**
     * [getCateCourserList 获取指定分类下的课程班级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCateCourserList($categoryid){
        $res = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.gradename,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq','1')
            ->where('s.delflag',1)
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->table('nm_curriculum')
                    ->where('categoryid','in',$categoryid)
                    ->field('id');
            })
            ->order('s.addtime desc')
            ->limit(10)
            ->select();
        return  $res;
    }
    /**
     * [getCateCourserList 获取指定分类下的课程班级数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCateCourserCount($categoryid){
        $res = Db::table($this->table.' s')
            ->where('s.status','eq','1')
            ->where('s.delflag',1)
            /*   ->where('s.classstatus','in','0,1,2,3,4')*/
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->table('nm_curriculum')
                    ->where('categoryid','in',$categoryid)
                    ->field('id');
            })
           ->count();
        return  $res;
    }
    /**
     * [getCourserAllList 获取全部排班课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllList($limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq','1')
            ->where('s.delflag','eq','1')
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getCourserAllList 获取全部排班课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllCount(){
        $lists =Db::table($this->table)
            ->where('status','eq','1')
            ->where('delflag','eq','1')
            ->count();
        return  $lists;
    }
    /**
     * [getCourserOne 获取指定id的排课信息]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        scheduid  [排课id]
     * @return   array
     */
    public function getCourserOne($scheduid){
        $lists = Db::table($this->table.' s')
            ->field('s.curriculumid,s.fullpeople,s.id as scheduid,s.teacherid,s.type,s.price as totalprice,s.gradename,t.nickname as teachername,t.imageurl as teacherimg,c.imageurl,c.coursename,c.subhead,c.describe,c.studypeople,c.periodnum,c.categoryid,c.generalize,s.classstatus')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.id','eq',$scheduid)
            ->find();
        return  $lists;
    }
    /**
     * [getCourserOne 获取指定id的排课信息]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        scheduid  [排课id]
     * @return   array
     */
    public function getCourserById($scheduid){
        $lists = Db::table($this->table.' s')
            ->field('s.curriculumid,s.id as scheduid,s.teacherid,s.type,s.totalprice,s.gradename,t.nickname as teachername,t.imageurl as teacherimg,c.imageurl,c.coursename,c.subhead,c.describe,c.studypeople,c.periodnum,c.categoryid,c.generalize,t.teacherid,s.price')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')

            ->where('s.id','eq',$scheduid)
            ->find();
        return  $lists;
    }
    /**
     * [getApplyStatus 查询该排课报名状态]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        scheduid  [排课id]
     * @param    [int]        delflag [是否删除]
     * @param    [int]        status  [是否暂停招生]
     * @param    [int]        classstatus  [是否满员]
     * @return   array
     */
    public function getApplyStatus($scheduid){
        $data = Db::table($this->table)
            ->field('status,classstatus,delflag')
            ->where('id','eq',$scheduid)
            ->find();
        return  $data;
    }
    /**
     * [updateClassStatus修改排课表班级状态]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function updateClassStatus($where,$data){
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [getOpenClassCount 获取老师的开班数量]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function getOpenClassCount($teacherid){
        $res = Db::table($this->table)
            ->where('teacherid','eq',$teacherid)
            ->where('delflag','eq',1)
            ->where('status','eq',1)
            ->count();
        return $res;
    }
    /**
     * [getScheduById获取指定id的排课信息]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        scheduid  [排课id]
     * @return   array
     */
    public function getScheduById($scheduid){
        $lists = Db::table($this->table.' s')
            ->field('s.curriculumid,s.id as scheduid,s.teacherid,s.type,s.curriculumname,s.classhour')
            ->where('s.id','eq',$scheduid)
            ->where('s.status','eq',1)
            ->where('s.delflag',1)
            ->find();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [setRealnumSum 对班级实际报名人数+1]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        scheduid  [排课id]
     * @return   array
     */
    public function setRealnumSum($scheduid){
        $lists = Db::table($this->table)->where('id','eq', $scheduid)->setInc('realnum');
        return  $lists;
    }
    /**
     * [getClassByCourseid 查询指定课程下的班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        courseid  [课程id]
     * @return   array
     */
    public function getClassByCourseid($where){
        $lists = Db::table($this->table.' s')
            ->field('s.addtime,s.curriculumid,s.id as scheduid,s.teacherid,s.type,s.price as totalprice,s.gradename,t.nickname as teachername,t.imageurl as teacherimg,c.imageurl,c.coursename,c.subhead,c.describe,c.studypeople,c.periodnum,c.categoryid,c.generalize,s.classstatus,s.realnum,s.fullpeople,s.starttime,s.endtime')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.delflag','eq','1')
            ->where('s.status','eq','1')
            ->where('s.schedule','eq','1')
            ->where($where)
            ->order('s.sortnum')
            ->select();
        return  $lists;
    }
    /**
     * [getClassByCourseGroup查询指定课程下的班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        courseid  [课程id]
     * @return   array
     */
    public function getClassByCourseGroup($where){
        $lists = Db::table($this->table.' s')
            ->field('s.addtime,s.curriculumid,s.id as scheduid,s.teacherid,s.type,s.totalprice,s.gradename,t.nickname as teachername,t.imageurl as teacherimg,c.imageurl,c.coursename,c.subhead,c.describe,c.studypeople,c.periodnum,c.categoryid,c.generalize,s.classstatus,s.realnum,s.fullpeople,s.starttime,s.endtime')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.delflag','eq','1')
            ->where('s.status','eq','1')
            ->where('s.schedule','eq','1')
            ->where($where)
            ->group('starttime')
            ->select();
        return  $lists;
    }
    /**
     * [getClassByCourseGroup查询指定课程下的班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        courseid  [课程id]
     * @return   array
     */
    public function getClassGroup($where){
        $lists = Db::table($this->table.' s')
            ->field('s.fullpeople,s.gradename')
            ->where('s.delflag','eq','1')
            ->where('s.status','eq','1')
            ->where('s.schedule','eq','1')
            ->where($where)
            ->group('s.fullpeople')
            ->column('s.fullpeople');
        return  $lists;
    }
    /**
     * [getScheduList查询指定课程下的班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        courseid  [课程id]
     * @return   array
     */
    public function getScheduList($where,$field){
        $lists = Db::table($this->table.' s')
            ->join('nm_teacherinfo t','t.teacherid=s.teacherid')
            ->field($field)
            ->where($where)
            ->select();
        return  $lists;
    }
}







