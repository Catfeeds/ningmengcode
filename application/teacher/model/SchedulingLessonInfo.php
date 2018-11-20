<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class SchedulingLessonInfo extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_schedulinglessoninfo';
    //

    /**所有班级作业信息列表
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param $schedulingid
     * @param $curriculumid
     * @param null $lessonsid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lessonHomeworkList($teacherid,$pagenum,$pagesize,$schedulingid,$curriculumid,$lessonsidarr = null){
        $where = ['teacherid'=>$teacherid,'classid'=>$schedulingid,'courseid'=>$curriculumid];
        if(!empty($lessonsidarr)){
            $where['lessonid'] = ['in',$lessonsidarr];
        }
        $data['data'] = Db::table($this->table)
            ->where($where)
            ->page($pagenum,$pagesize)
            ->field('id,lessonid,starttime,reviewstatus,endtime,avgscore,classid,courseid,addtime')
            ->select();
        //return Db::table($this->table)->getLastSql();
        $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getClassarrcount($where));
        return $data;
    }

    /**
     * @param $teacherid
     * @param $curriculumid
     * @param $schedulingid
     * @param null $lessonsid
     * @return int|string
     */
    public function getWorknum($teacherid,$curriculumid,$schedulingid,$lessonsid =null){
        if(isset($lessonsid)){
            $where = ['teacherid'=>$teacherid,'courseid'=>$curriculumid,'classid'=>$schedulingid,'lessonid'=>$lessonsid];
        }else{
            $where = ['teacherid'=>$teacherid,'courseid'=>$curriculumid,'classid'=>$schedulingid];
        }
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /**
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param $curriculumid
     * @param $schedulingid
     * @return array
     */
    public function getClassarr($teacherid,$pagenum,$pagesize,$curriculumidarr,$schedulingidarr){
        if(empty($curriculumidarr) && empty($schedulingidarr)){
            $where = ['teacherid'=>$teacherid];
        }elseif(!empty($curriculumidarr) && empty($schedulingidarr)){
            $where = ['teacherid'=>$teacherid,'courseid'=>['in',$curriculumidarr]];
        }elseif(empty($curriculumidarr) && !empty($schedulingidarr)){
            $where = ['teacherid'=>$teacherid,'classid'=>['in',$schedulingidarr]];
        }
        //return $where;
        $data['data'] = Db::table($this->table)
            ->where($where)
            ->page($pagenum,$pagesize)
            ->order('addtime','DESC')
            ->column('id,classid,courseid,lessonid,addtime','classid');
        $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getClassarrcount($where));
        return $data;
    }
    /**
     * @param $whe
     * @return int|string
     */
    public function getClassarrcount($where){
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /** 作业全部提交的前提下，调用该接口
     * @param $lessonid
     * @param $teacherid
     * @param $avgscore
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateScheinfo($lessonid,$teacherid,$avgscore,$reviewstatus=0){
        $where = ['lessonid'=>$lessonid,'teacherid'=>$teacherid];
        return Db::table($this->table)
            ->where($where)
            ->update(['avgscore'=>$avgscore,'reviewstatus'=>$reviewstatus]);
    }
    /**
     * @param $where
     * @param $field
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function upHomework($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->update($field);
    }

    /**
     * @param $data
     * @return int|string
     */
    public function addHomework($data){
        return Db::table($this->table)
            ->insert($data);
    }

    /**
     * @param $where
     * @param $field
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllfind($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->field($field)
            ->find();
    }

}
