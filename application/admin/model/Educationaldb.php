<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Educationaldb extends Model
{
    protected $transfer_class_table = 'nm_applyschedulingrecord';//调班表
    protected $transfer_lesson_table = 'nm_applylessonsrecord';//调课表
    protected $curriculum_table = 'nm_curriculum';//调课表
    protected $scheduling_table = 'nm_scheduling';//排课、排班表
    protected $teacher_table = 'nm_teacherinfo';//老师表
    protected $student_table = 'nm_studentinfo';//学生表
    protected $lessons = 'nm_lessons';//课时表 副表
    protected $classroom = 'nm_classroom';//开启教室表
    protected $toteachtime = 'nm_toteachtime';//课程章节时间安排表


    //自定义初始化
//    protected function initialize()
//    {
//        parent::initialize();
//    }

    /**
     * [transferClassList 教务-调班列表]
     * @Author ZQY
     * @DateTime 2018-09-16 19:36:41+0800
     * @return   [array]  [教务-调班列表]
     */
    public function transferClassList($where,$limitstr)
    {
        return Db::table($this->student_table)
            ->alias('stu')
            ->join('nm_applyschedulingrecord tran','stu.id = tran.studentid')
            ->join('nm_curriculum cur','tran.curriculumid = cur.id')
            ->join('nm_teacherinfo old_tea','tran.oldteacherid = old_tea.teacherid')
            ->join('nm_teacherinfo new_tea','tran.newteacherid = new_tea.teacherid')
            ->join('nm_scheduling old_class','tran.oldschedulingid = old_class.id')
            ->join('nm_scheduling new_class','tran.newschedulingid = new_class.id')
            ->field('tran.id,stu.nickname studentname,cur.coursename,old_tea.nickname oldteacher,new_tea.nickname newteacher,old_class.gradename oldclass,new_class.gradename newclass,tran.status,tran.applytime,tran.updatetime')
            ->where($where)
            ->limit($limitstr)
            ->select();

    }
    /**
     * [transferClassList 教务-调班列表Count]
     * @Author ZQY
     * @DateTime 2018-09-16 19:36:41+0800
     * @return   [array]  [教务-调班列表]
     */
    public function transferClassListCount($where)
    {
        return Db::table($this->student_table)
            ->alias('stu')
            ->join('nm_applyschedulingrecord tran','stu.id = tran.studentid')
            ->join('nm_curriculum cur','tran.curriculumid = cur.id')
            ->join('nm_teacherinfo old_tea','tran.oldteacherid = old_tea.teacherid')
            ->join('nm_teacherinfo new_tea','tran.newteacherid = new_tea.teacherid')
            ->join('nm_scheduling old_class','tran.oldschedulingid = old_class.id')
            ->join('nm_scheduling new_class','tran.newschedulingid = new_class.id')
            ->where($where)
            ->count();
    }
    /**
     * [getOneMobile 获取老师、学生的手机号]
     * $tranid  调班表id
     * @Author ZQY
     * @DateTime 2018-09-17 11:54:41+0800
     * @return   [array]  [获取老师、学生的手机号]
     */
    public function getSuccess($tranid)
    {
        return Db::table($this->transfer_class_table)
            ->alias('tran')
            ->join('nm_teacherinfo tea','tran.newteacherid = tea.teacherid')
            ->join('nm_teacherinfo teas','tran.oldteacherid = teas.teacherid')
            ->join('nm_studentinfo stu','tran.studentid = stu.id')
            ->join('nm_scheduling class','tran.newschedulingid = class.id')
            ->field('teas.nickname old_teachername,teas.prphone old_tea_prphone,teas.mobile old_teacher_mobile,tea.nickname teachername,tea.prphone tea_prphone,tea.mobile teacher_mobile,stu.nickname student_name,tea.prphone stu_prphone,stu.mobile student_mobile,class.gradename class')
            ->where(['tran.id'=>"$tranid"])
            ->select();
    }
    /**
     * [getOneMobile 获取学生的手机号]
     * @Author ZQY
     * $tranid 调班表id
     * @DateTime 2018-09-17 12:23:41+0800
     * @return   [array]  [学生的手机号]
     */
    public function getFail($tranid)
    {
        return Db::table($this->transfer_class_table)
            ->alias('tran')
            ->join('nm_studentinfo stu','tran.studentid = stu.id')
            ->join('nm_scheduling class','tran.newschedulingid = class.id')
            ->field('stu.nickname student_name,stu.prphone,stu.mobile student_mobile')
            ->where(['tran.id'=>"$tranid"])
            ->select();
    }
    /**
     * [TranferUpdate 调班同意申请]
     * @Author ZQY
     * $tranid 调班表id
     * @DateTime 2018-09-17 14:52:41+0800
     * @return   [array]  [调班同意申请]
     */
    public function TranferUpdate($tranid,$status,$update_time)
    {
        return Db::table($this->transfer_class_table)
            ->where(['id'=>"$tranid"])
            ->update([
                'status'  => "$status",
                'updatetime'  => "$update_time",
            ]);
    }
    /**
     * [transferClassList 教务-调课列表]
     * @Author ZQY
     * @DateTime 2018-09-16 19:36:41+0800
     * @return   [array]  [教务-调班列表]
     */
    public function transferLessonList($where,$limitstr)
    {
        return Db::table($this->student_table)
            ->alias('stu')
            ->join('nm_applylessonsrecord tran','stu.id = tran.studentid')
            ->join('nm_curriculum cur','cur.id = tran.curriculumid')
            ->join('nm_teacherinfo tea','tea.teacherid = tran.oldteacherid')
            ->join('nm_teacherinfo teas','teas.teacherid = tran.newteacherid')
            ->join('nm_lessons les','les.id = tran.oldlessonsid')
            ->join('nm_lessons less','less.id = tran.newlessonsid')
            ->field('tran.id,stu.nickname student_name,cur.coursename,tea.nickname old_teacher,teas.nickname new_teacher,les.periodsort oldsort,les.periodname ondlesson,less.periodsort newsort,less.periodname newlesson,tran.updatetime,les.schedulingid old_class,less.schedulingid new_class,tran.applytime,tran.status')
            ->where($where)
            ->limit($limitstr)
            ->select();
    }
    /**
     * [transferLessonListCount 教务Count]
     * @Author ZQY
     * @DateTime 2018-09-16 19:36:41+0800
     * @return   [array]  [教务-教务Count]
     */
    public function transferLessonListCount($where)
    {
        return Db::table($this->student_table)
            ->alias('stu')
            ->join('nm_applylessonsrecord tran','stu.id = tran.studentid')
            ->join('nm_curriculum cur','cur.id = tran.curriculumid')
            ->join('nm_teacherinfo tea','tea.teacherid = tran.oldteacherid')
            ->join('nm_teacherinfo teas','teas.teacherid = tran.newteacherid')
            ->join('nm_lessons les','les.id = tran.oldlessonsid')
            ->join('nm_lessons less','less.id = tran.newlessonsid')
            ->field('tran.id,stu.username student_name,cur.coursename,tea.teachername old_teacher,teas.teachername new_teacher,les.periodsort oldsort,les.periodname ondlesson,less.periodsort newsort,less.periodname newlesson,tran.updatetime,les.schedulingid old_class,less.schedulingid new_class')
            ->where($where)
            ->count();
    }
    /**
     * [getToteachId 获取课时开课时间]
     * @Author ZQY
     * @DateTime 2018-09-30 13:33:41+0800
     * @return
     */
    public function getToteachId($tranid)
    {
        return Db::table($this->transfer_lesson_table)
            ->alias('lesson')
            ->join('nm_toteachtime to','lesson.oldlessonsid = to.lessonsid')
            ->where(['lesson.id'=>"$tranid"])
            ->field('to.id')
            ->select();
    }
    /**
     * [getToteachId 获取课时开课时间]
     * @Author ZQY
     * @DateTime 2018-09-30 13:33:41+0800
     * @return
     */
    public function testingRomm($to_teacher_id){
        return Db::table($this->classroom)->where(['toteachtimeid'=>$to_teacher_id])->field('')->select();
    }
    /**
     * [$tranid 获取班级id]
     * @Author ZQY
     * @DateTime 2018-11-02 15:28:41+0800
     * @return
     */
    public function getSchedulingId($tranid){
        return Db::table($this->transfer_class_table)
            ->alias('tran')
            ->join('nm_studentinfo stu','stu.id=tran.studentid')
            ->where(['tran.id'=>$tranid])
            ->field('tran.oldschedulingid,stu.nickname')
            ->select();
    }
    /**
     * [$tranid 获取班级id]
     * @Author ZQY
     * @DateTime 2018-11-02 15:28:41+0800
     * @return
     */
    public function getLessonTime($schedulingid){
        return Db::table($this->toteachtime)
            ->where(['schedulingid'=>$schedulingid])
            ->field('starttime')->limit(1)
            ->order('starttime','asc')
            ->select();
    }
    /**
     * [transferClassList 教务-获取班级名称]
     * @Author ZQY
     * @DateTime 2018-09-17 19:46:41+0800
     * @return   [array]  [教务-获取班级名称]
     */
    public function getClassName($oldid,$newid)
    {
        $old = Db::table($this->scheduling_table)->where(['id'=>$oldid])->field('gradename')->select();
        $new = Db::table($this->scheduling_table)->where(['id'=>$newid])->field('gradename')->select();
        $resurt = [];
        $resurt['old_class'] = $old[0]['gradename'];
        $resurt['new_class'] = $new[0]['gradename'];
        return $resurt;
    }
    /**
     * [getOneMobile 获取老师、学生的手机号]
     * $tranid  调课表id
     * @Author ZQY
     * @DateTime 2018-09-17 20:17:41+0800
     * @return   [array]  [获取老师、学生的手机号]
     */
    public function getLessSuccess($tranid)
    {
        return Db::table($this->transfer_lesson_table)
            ->alias('tran')
            ->join('nm_teacherinfo tea','tran.newteacherid = tea.teacherid')
            ->join('nm_teacherinfo teas','tran.oldteacherid = teas.teacherid')
            ->join('nm_curriculum cur','cur.id = tran.curriculumid')
            ->join('nm_lessons less','less.id = tran.newlessonsid')
            ->join('nm_lessons lesss','lesss.id = tran.oldlessonsid')
            ->join('nm_studentinfo stu','tran.studentid = stu.id')
            ->join('nm_toteachtime to','tran.newlessonsid = to.lessonsid')
            ->field('lesss.periodname oldlesson,lesss.periodsort oldsort,teas.nickname old_teachername,teas.prphone old_tea_prphone,teas.mobile old_tea_mobile,tea.nickname teachername,tea.prphone tea_prphone,tea.mobile tea_mobile,cur.coursename,less.periodname newlesson,less.periodsort newsort,stu.nickname username,to.starttime,stu.prphone stu_prphone,stu.mobile stu_mobile')
            ->where(['tran.id'=>"$tranid"])
            ->select();
    }
    /**
     * [TranferUpdate 调课同意申请]
     * @Author ZQY
     * $tranid 调班表id
     * @DateTime 2018-09-17 14:52:41+0800
     * @return   [array]  [调课同意申请]
     */
    public function TranferLessonUpdate($tranid,$status,$update_time)
    {
        return Db::table($this->transfer_lesson_table)
            ->where(['id'=>"$tranid"])
            ->update([
                'status'  => "$status",
                'updatetime'  => "$update_time",
            ]);
    }
    /**
     * [getOneMobile 获取生的手机号]
     * $tranid  调课表id
     * @Author ZQY
     * @DateTime 2018-09-17 20:17:41+0800
     * @return   [array]  [获取学生的手机号]
     */
    public function getLessonFail($tranid)
    {
        return Db::table($this->transfer_lesson_table)
            ->alias('tran')
            ->join('nm_curriculum cur','cur.id = tran.curriculumid')
            ->join('nm_studentinfo stu','tran.studentid = stu.id')
            ->field('cur.coursename,stu.nickname username,stu.prphone stu_prphone,stu.mobile stu_mobile')
            ->where(['tran.id'=>"$tranid"])
            ->select();
    }
}