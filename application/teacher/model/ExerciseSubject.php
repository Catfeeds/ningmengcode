<?php
//题库表
namespace app\teacher\model;

use think\Model;
use think\Db;
class ExerciseSubject extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_exercisesubject';
    //

    /**
     * @param $curriculumid
     * @param $lessonid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function showExerciselist($curriculumid,$lessonsid){
        $where = ['c.courseid'=>$curriculumid,'u.id'=>$lessonsid,'c.delflag'=>0,'c.status'=>1];
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_lessons u','c.periodid=u.periodid','left')
            ->where($where)
            ->field('c.id,c.type,c.name,c.imageurl,c.analysis,c.correctanswer,c.score')
            ->select();
    }

    /**
     * @param $data
     * @param $field
     * @return array
     */
    public function showSubjectlist($data,$field){
        $where = ['periodid'=>$data['periodid'],'delflag'=>0,'status'=>1];
        if($data['type']){
            if(isset($data['courseid'])){
                $where['courseid'] = $data['courseid'];
            }
            $where['type'] = ['in',$data['type']];
            //$where = ['courseid'=>$data['courseid'],'periodid'=>$data['periodid'],'delflag'=>0,'status'=>1,'type'=>['in',$data['type']]];
        }else{
            if(isset($data['courseid'])){
                $where['courseid'] = $data['courseid'];
            }
            //$where = ['courseid'=>$data['courseid'],'periodid'=>$data['periodid'],'delflag'=>0,'status'=>1];
        }
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
    public function showSubjectoptlist($data,$field){
        $where = ['c.periodid'=>$data['periodid'],'c.delflag'=>0,'s.delflag'=>0,'c.status'=>1];
        if($data['type']){
            $where['c.type'] = ['in',$data['type']];
        }
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_exercisesubjectoptions s','s.subjectid=c.id','LEFT')
            ->where($where)
            ->column($field);
    }

}
