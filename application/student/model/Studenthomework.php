<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 学生作业表Model
 * @ yr
*/
class Studenthomework extends Model{
    protected $table = 'nm_studenthomework';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getHomeworkList 根据指定条件查询作业列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getHomeworkList($where,$limitstr){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' h')
            ->field('h.id as homeworkid,h.courseid,h.classid,h.lessonid,h.studentid,h.submittime,h.score,c.coursename,s.gradename,l.periodname,s.teacherid,k.endtime,h.sendstatus,k.starttime')
            ->join('nm_curriculum c','c.id=h.courseid','left')
            ->join('nm_scheduling s','s.id=h.classid','left')
            ->join('nm_lessons l','l.id=h.lessonid','left')
            ->join('nm_schedulinglessoninfo k','k.lessonid=h.lessonid','left')
            ->where($where)
            ->order('h.id desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getHomeworkCount 根据指定条件查询作业条数
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getHomeworkCount($where){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' h')
            ->where($where)
            ->count();
        return  $lists;
    }
    /**
     * [searchHomework 根据指定条件查询作业列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function searchHomework($where,$limitstr,$search){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' h')
            ->field('h.id as homeworkid,h.courseid,h.lessonid,h.score,h.studentid,h.submittime,h.score,c.coursename,s.gradename,l.periodname,k.endtime,s.teacherid')
            ->join('nm_curriculum c','c.id=h.courseid','LEFT')
            ->join('nm_scheduling s','s.id=h.classid','LEFT')
            ->join('nm_lessons l','l.id=h.lessonid','LEFT')
            ->join('nm_schedulinglessoninfo k','k.lessonid=h.lessonid','LEFT')
            ->where($where)
            ->where('h.lessonid','IN',function($query) use($search){
                $query->table('nm_lessons')
                    ->where('periodname','LIKE',"%$search%")
                    ->field('id');
            })
            ->order('h.id desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [searchHomeworkCount 根据指定条件查询作业列表数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function searchHomeworkCount($where,$search){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' h')
            ->where($where)
            ->where('h.lessonid','IN',function($query) use($search){
                $query->table('nm_lessons')
                    ->where('periodname','like',"%$search%")
                    ->field('id');
            })
            ->count();
        return  $lists;
    }
    /**
     * [updateData 更新作业表字段]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function updateData($where,$fieldarr){
        $result = Db::table($this->table)->where($where)->update($fieldarr);
        return $result;
    }
    /**
     * [updateData 更新作业表字段]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getHomeworkInfo($where,$fieldarr){
        $result = Db::table($this->table)->where($where)->field($fieldarr)->find();
        return $result;
    }
    /**
     * [getFindHomework 根据指定条件查询作业列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getFindHomework($where){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table.' h')
            ->field('h.issubmited,h.reviewstatus,h.id as homeworkid,h.courseid,h.classid,h.lessonid,h.studentid,h.submittime,h.score,c.coursename,s.gradename,l.periodname,s.teacherid,k.endtime,h.sendstatus')
            ->join('nm_curriculum c','c.id=h.courseid','left')
            ->join('nm_scheduling s','s.id=h.classid','left')
            ->join('nm_lessons l','l.id=h.lessonid','left')
            ->join('nm_schedulinglessoninfo k','k.lessonid=h.lessonid','left')
            ->where($where)
            ->find();
        return  $lists;
    }
}







