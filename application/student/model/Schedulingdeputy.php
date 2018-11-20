<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 课程推荐Model
 * @ yr
*/
class Schedulingdeputy extends Model{
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
     * @param    [int]        $organid  [机构id]
     * @return   array
     */
    public function getCourserList($organid){
        $lists =Db::table($this->table.' s')
            ->field('s.curriculumid,s.type,s.totalprice,s.sortnum,t.teachername,c.imageurl,c.coursename,s.id as scheduid')
            ->join('nm_curriculum c','s.curriculumid = c.id','left')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','left')
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.recommend','eq','1')
            ->where('s.status','eq','1')
            ->where('s.delflag','eq','1')
            ->where('s.organid',$organid)
            ->where('s.classstatus','in','0,1,2,3')
            ->order('s.sortnum')
            ->limit(10)
            ->select();
        return  $lists;
    }
    /**
     * [getIndexCourserList 获取首页轮播推荐课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @return   array
     */
    public function getIndexCourserList(){
        $lists =Db::table($this->table.' s')
            ->field('s.curriculumid,s.type,s.totalprice,s.sortnum,t.teachername,c.imageurl,c.coursename,s.id as scheduid')
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
    public function getTeacherCourse($teacherid,$organid){
        $lists =Db::table($this->table)->table('nm_scheduling s, nm_curriculum c ,nm_teacherinfo t')
            ->where('s.curriculumid = c.id')
            ->where('s.teacherid = t.teacherid')
            ->where('s.recommend','eq','1')
            ->where('s.teacherid',$teacherid)
            ->where('s.organid',$organid)
            ->field('s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,s.id as scheduid')
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
    public function getTeacherList($teacherid,$organid){
        $lists =Db::table($this->table.' s')
            ->field('s.curriculumid,s.gradename,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.generalize,c.subhead,s.id as scheduid')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.organid','eq',$organid)
            ->where('s.teacherid','eq',$teacherid)
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->order('s.sortnum')
            ->select();
        return  $lists;
    }
    /**
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserList($categoryid,$tagid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid)
                    ->union("SELECT id FROM nm_curriculum WHERE categoryid IN ($categoryid)");
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserCount($categoryid,$tagid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid)
                    ->union("SELECT id FROM nm_curriculum WHERE categoryid IN ($categoryid)");
            })
            ->count();
        return  $lists;
    }
    /**
     * [getFilterCourserList 按分类筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getWebFilterCourserList($categoryid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename,o.organname')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_organ o','o.id = s.organid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','IN',$categoryid);
            })
            ->order('s.sortnum desc')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getWebFilterCourserCount($categoryid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','IN',$categoryid);
            })
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->count();
        return  $lists;
    }
    /**
     * [getCourserListByCname 按课程名称查询班级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByCname($search,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.gradename,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead,o.organname')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_organ o','s.organid = o.id','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.curriculumid','IN',function($query) use($search){
                $query->field('id')->table('nm_curriculum')->where('coursename','like',"%$search%")->where('delflag','eq',1);
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListCount($search){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.curriculumid','IN',function($query) use($search){
                $query->field('id')->table('nm_curriculum')->where('coursename','like',"%$search%")->where('delflag','eq',1);
            })
            ->count();
        return  $lists;
    }
    /**
     * [getCateCourserList 获取指定分类下的课程班级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCateCourserList($categoryid){
        $res = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.gradename,s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq','1')
            ->where('s.delflag',1)
            ->where('s.curriculumid','IN',function($query) use($categoryid){
                $query->table('nm_curriculum')
                    ->where('categoryid','eq',$categoryid)
                    ->where('delflag','eq',1)
                    ->where('status','eq',1)
                    ->where('organid','in',function($query) {
                        $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
                    })
                    ->field('id');
            })
            ->order('s.addtime desc')
            ->limit(10)
            ->select();
        return  $res;
    }
    /**
     * [getIndexCateCourserList 获取APP首页指定分类下的课程班级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getIndexCateCourserList($categoryid){
        $res = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.gradename,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,o.organname')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_organ o','s.organid = o.id','LEFT')
            ->where('s.status','eq','1')
            ->where('s.delflag',1)
            /*   ->where('s.classstatus','in','0,1,2,3,4')*/
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
     * [getCourserAllList 获取全部排班课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllList($limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename,o.organname')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_organ o','o.id = s.organid','LEFT')
            ->where('s.status','eq','1')
            ->where('s.delflag','eq','1')
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->order('s.sortnum desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getCourserAllList 获取全部排班课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllCount($organid){
        $lists =Db::table($this->table)
            ->where('status','eq','1')
            ->where('delflag','eq','1')
            ->where('organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
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
            ->field('s.curriculumid,s.organid,s.id as scheduid,s.teacherid,s.fullpeople,s.type,s.totalprice,s.gradename,t.teachername,t.imageurl as teacherimg,c.imageurl,c.coursename,c.subhead,c.describe,c.studypeople,c.periodnum,c.categoryid,c.generalize,s.classstatus,o.organname')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_organ o','o.id = s.organid','LEFT')
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
    public function getApplyStatus($scheduid,$organid){
        $data = Db::table($this->table)
            ->field('status,classstatus,delflag')
            ->where('id','eq',$scheduid)
            ->where('organid','eq',$organid)
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
    public function getOpenClassCount($teacherid,$organid){
        $res = Db::table($this->table)
            ->where('teacherid','eq',$teacherid)
            ->where('organid','eq',$organid)
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
            ->field('s.curriculumid,s.organid,s.id as scheduid,s.teacherid,s.type,s.curriculumname')
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
     * [searchOfficialByCname 通过课程名称搜索班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function searchOfficialByCname($keywords,$limitstr){
        if(empty($keywords)){
            $where = '';
        }else{
            $where['s.curriculumname'] = ['like',"%$keywords%"];
        }
        $result = Db::table($this->table.' s')
            ->field('s.id as schedulingid,s.curriculumid,s.curriculumname,s.type,s.imageurl,s.totalprice,s.gradename,t.teachername,c.subhead,c.generalize,o.organname')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_organ o','s.organid = o.id','LEFT')
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where($where)
            ->limit($limitstr)
            ->order('s.sortnum')
            ->select();
        return $result;
    }
    /**
     * [searchOfficialByCname 统计搜索的班级数量]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function searchOfficialCount($keywords){
        if(empty($keywords)){
            $where = '';
        }else{
            $where['s.curriculumname'] = ['like',"%$keywords%"];
        }
        $result = Db::table($this->table.' s')
            ->where('s.delflag','eq',1)
            ->where('s.organid','in',function($query) {
                $query->field('id')->table('nm_organ')->where('vip','eq','0')->where('auditstatus','eq','3');
            })
            ->where('s.status','eq',1)
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * [getOrgainClassList 通过课程名称搜索班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function getOrgainClassList($organid,$limitstr){
        $result = Db::table($this->table.' s')
            ->field('s.id as schedulingid,s.curriculumid,s.curriculumname as coursename,s.type,s.imageurl,s.totalprice,s.gradename,t.nickname as teachername')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->limit($limitstr)
            ->order('s.sortnum')
            ->select();
        return $result;
    }
    /**
     * [getOrgainClassList 通过课程名称搜索班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function getOrgainClassCount($organid){
        $result = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->count();
        return $result;
    }
    /**
     * [getOrgainAllClassList 通过课程名称搜索班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function getOrgainAllClassList($organid){
        $result = Db::table($this->table.' s')
            ->field('s.id as schedulingid,s.curriculumid,s.curriculumname as coursename,s.type,s.imageurl,s.totalprice,s.gradename,t.nickname as teachername')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->order('s.sortnum')
            ->select();
        return $result;
    }
    /**
     * [getOrgainAllClassCount 通过课程名称搜索班级]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @return   array
     */
    public function getOrgainAllClassCount($organid){
        $result = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->count();
        return $result;
    }
}







