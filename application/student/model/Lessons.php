<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课时Model
 * @ yr
*/
class Lessons extends Model{
    protected $table = 'nm_lessons';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getLessonsList 获取指定单元的课时List]
     * @Author yr
     * 小班和大班课程关联查询出上课时间
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $unitid[课程单元id]
     * @param    [int]        $schedulingid[排课 id]
     * @return   array
     */
    public function getLessonsList($unitid){
        $where = [
            'l.unitid'=> $unitid,
        ];
            $lists =Db::table($this->table.' l')
                ->field('l.curriculumid,l.periodname,l.periodsort,t.intime,l.id as lessonsid,t.timekey,t.id as toteachid,FROM_UNIXTIME(t.starttime) as starttime')
                ->join('nm_toteachtime t','l.id=t.lessonsid','LEFT')
                ->where($where)
                ->order('l.periodsort')
                ->select();
        if(empty($lists)){
            return [];
        }
        return  $lists;
    }
    /**
     * [getLessonsList 获取指定单元的课时List]
     * @Author yr
     * 一对一查出课节信息和上课时间
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $unitid[课程单元id]
     * @param    [int]        $schedulingid[排课 id]
     * @return   array
     */
    public function getLessonsByStudent($unitid,$schedulingid,$studentid){
        $where = [
            'l.unitid'=> $unitid,
            't.studentid'=> $studentid,
            't.schedulingid'=> $schedulingid,
        ];
            $lists =Db::table($this->table.' l')
                ->field('l.curriculumid,l.periodname,l.periodsort,t.intime,l.id as lessonsid,t.timekey,t.id as toteachid')
                ->join('nm_toteachtime t','l.id=t.lessonsid','LEFT')
                ->where($where)
                ->order('l.periodsort')
                ->select();
        return  $lists;
    }
    /**
     * [getLessonsNum 获取课程的课节数量]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getLessonsNum($schedulingid){
        $lists =Db::table($this->table)
            ->field('id as learnid')
            ->where('schedulingid','eq',$schedulingid)
            ->where('delflag','eq',1)
            ->select();
        return  $lists;
    }
    /**
     * 根据课节id 获取课节指定字段
     * @php yr
     * $id learnsid
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getFieldName($id,$field){
        return Db::table($this->table)->where('id','eq',$id)->field($field)->find();
    }
    /**
     * 根据schedulingid 查询课节信息
     * @php yr
     * @return [type] [description]
     */
    public function getLessonsInfo($schedulingid,$unitid){
        $info = Db::table($this->table)
            ->field('unitid,periodname,periodsort')
            ->where('schedulingid','eq',$schedulingid)
            ->where('unitid','eq',$unitid)
            ->find();
        return $info;
    }
    /**
     * 根据unitid查询课节信息
     * @php yr
     * @return [type] [description]
     */
    public function getLessonsByUnitid($unitid,$schedulingid){
        $info = Db::table($this->table)
            ->field('id as lessonsid,periodname,periodsort')
            ->where('unitid','eq',$unitid)
            ->where('delflag','eq',1)
            ->where('schedulingid','eq',$schedulingid)
            ->order('periodsort')
            ->select();
        return $info;
    }
    /**
     * 根据schedulingid查询课节信息
     * @php yr
     * @return [type] [description]
     */
    public function getLessonsByscheduid($schedulingid){
        $info = Db::table($this->table)
            ->field('id as lessonsid,periodname,periodsort,unitid')
            ->where('delflag','eq',1)
            ->where('schedulingid','eq',$schedulingid)
            ->order('unitid,periodsort')
            ->select();
        return $info;
    }
    /**
     * 根据条件查询指定课节信息
     * @php yr
     * @return [type] [description]
     */
    public function getLessonsFind($where,$field){
        $info = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
        return $info;
    }
    /**
     * 根据条件查询所有lessonsid
     * @php yr
     * @return [type] [description]
     */
    public function getLessonids($where){
        $info = Db::table($this->table)
            ->where($where)
            ->column('id');
        return $info;
    }
    /**
     * 查询某个字段的集合
     * @Author yr
     * @return mixed
     */
    public function getOldschedulingids($where,$field){
        $result = Db::table($this->table)
            ->where($where)
            ->column($field);
        return $result;
    }
}







