<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class StudentAttendance extends Model
{
    protected $table = 'nm_studentattendance';
    protected $pk = 'id';
    //

    /**
     * @param $lessonsid
     * @param $teacherid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttendance($lessonsid,$teacherid,$pagenum,$pagesize){
        $where = ['lessonsid'=>$lessonsid,'teacherid'=>$teacherid];
        $data['data'] = Db::table($this->table)
            ->where($where)
            ->field('id,studentid,attendancestatus,score,comment,status')
            ->page($pagenum,$pagesize)
            ->select();
        $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getAttendanceCount($lessonsid,$teacherid));
        return $data;
    }

    /** 出勤表学生总数
     * @param $lessonsid
     * @param $teacherid
     * @return int|string
     */
    public function getAttendanceCount($lessonsid,$teacherid){
        $where = ['lessonsid'=>$lessonsid,'teacherid'=>$teacherid];
        return Db::table($this->table)
            ->where($where)
            ->count();
    }

    /**
     * @param $id
     * @param $attendancestatus
     * @param $score
     * @param $comment
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function upAttendance($studentid,$lesssonsid,$data){
        $data = where_filter($data,array('attendancestatus','score','comment','addtime','status','addtime'));
        //['attendancestatus'=>$attendancestatus,'score'=>$score,'comment'=>$comment,'status'=>1,'addtime'=>$addtime]
        //$where = ['id'=>$id];
        $where = ['studentid'=>$studentid,'lessonsid'=>$lesssonsid];//根据当前课时
        return  Db::table($this->table)
            ->where($where)
            ->update($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function addAttendance($data){
//        $this->allowField(true)->save($data);
//        $id = $this->id;
//        return $id;
        return Db::table($this->table)
            ->insert($data);
    }
}
