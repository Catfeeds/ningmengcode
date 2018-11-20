<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class StudentHomework extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_studenthomework';
    //

    /**
     * @param $teacherid
     * @param $curriculumid
     * @param $schedulingid
     * @param null $lessonsid
     * @return int|string
     */
    public function getWorknum($teacherid,$curriculumid,$schedulingid,$lessonsid =null){
        if(isset($lessonsid)){
            $where = ['teacherid'=>$teacherid,'courseid'=>$curriculumid,'classid'=>$schedulingid,'issubmited'=>1,'lessonid'=>$lessonsid];
        }else{
            $where = ['teacherid'=>$teacherid,'courseid'=>$curriculumid,'issubmited'=>1,'classid'=>$schedulingid];
        }
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /**
     * @param $where
     * @return int|string
     */
    public function getViewnum($where){
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /** 已经提交并被批阅的
     * @param $teacherid
     * @param $curriculumid
     * @param $schedulingid
     * @param $lessonsid
     * @return int|string
     */
    public function getmarkWorknum($teacherid,$schedulingid,$lessonsid){
        if(isset($lessonsid)){
            $where = ['teacherid'=>$teacherid,'classid'=>$schedulingid,'issubmited'=>1,'lessonid'=>$lessonsid,'reviewstatus'=>1];
        }else{
            $where = ['teacherid'=>$teacherid,'issubmited'=>1,'classid'=>$schedulingid,'reviewstatus'=>1];
        }
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /**
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param $reviewstatus
     * @param null $curriculumid
     * @param null $schedulingid
     * @param null $lessonsid
     * @param null $studentid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function homeworkList($teacherid,$pagenum,$pagesize,$reviewstatus,$curriculumid = null,$schedulingid=null,$lessonsid =null,$studentid = null){
        $where = ['teacherid'=>$teacherid,'courseid'=>$curriculumid,'classid'=>$schedulingid,'lessonid'=>$lessonsid,'reviewstatus'=>$reviewstatus];
        if(!empty($studentid)){
            $where['studentid']=$studentid;
        }
        $data['data'] = Db::table($this->table)
            ->where($where)
            ->page($pagenum,$pagesize)
            ->field('studentid,issubmited,submittime,reviewstatus,score')
            ->select();
        $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->homeworkListCount($where));
        return $data;
    }

    /**
     * @return int|string
     */
    public function homeworkListCount($where){
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /** 为学生作业批阅分数
     * @param $lessonsid
     * @param $studentid
     * @param $score
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateMarking($lessonsid,$studentid,$score){
        $where = ['lessonid'=>$lessonsid,'studentid'=>$studentid,'issubmited'=>1];
        return Db::table($this->table)
            ->where($where)
            ->update(['reviewstatus'=>1,'score'=>$score]);
    }

    /**
     * @param $where
     * @return array
     */
    public function getAllscore($where){
        return Db::table($this->table)
            ->where($where)
            ->column('score');
    }
    public function addHomework($data){
        return Db::table($this->table)
            ->insert($data);
    }


}
