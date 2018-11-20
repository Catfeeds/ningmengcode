<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class StudentHomeworkAnswer extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_studenthomeworkanswer';
    //

    /** 學生答案
     * @param $schedulingid
     * @param $lessonsid
     * @param $subjectid
     * @param $studentid
     * @return mixed
     */
    public function showStuAnswer($schedulingid,$lessonsid,$subjectidarr,$studentid){
        $where = ['classid'=>$schedulingid,'lessonid'=>$lessonsid,'subjectid'=>['in',$subjectidarr],'studentid'=>$studentid];
        return Db::table($this->table)
            ->where($where)
            ->column('id,answer,score,comment','subjectid');
    }

    /**
     * @param $id
     * @param $score
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateStuScore($id,$score){
        $where = ['id'=>$id];
        return Db::table($this->table)
            ->where($where)
            ->update(['score'=>$score['score'],'comment'=>$score['comment']]);
        //return Db::table($this->table)->getLastSql();
    }
    public function getHomeworkCount($where){
        return Db::table($this->table)
            ->where($where)
            ->count();
    }
}
