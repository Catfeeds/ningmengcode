<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 题库Model
 * @ yr
*/
class Exercisesubject extends Model{
    protected $table = 'nm_exercisesubject';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getSubjectList 根据指定条件查询作业列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getSubjectList($where){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' e')
            ->field('e.id as subjectid,e.type,e.courseid,e.periodid,e.name,e.imageurl,e.analysis,e.score,l.periodname')
            ->join('nm_period l','l.id=e.periodid','LEFT')
            ->where($where)
            ->where('e.delflag','eq','0')
            ->where('e.status','eq','1')
            ->select();
        return  $lists;
    }
    /**
     * [getSubjectInfo根据指定条件查询习题信息]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getSubjectInfo($where){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $info = Db::table($this->table.' e')
            ->field('correctanswer')
            ->where($where)
            ->where('e.delflag','eq','0')
            ->where('e.status','eq','1')
            ->find();
        return  $info;
    }
}







