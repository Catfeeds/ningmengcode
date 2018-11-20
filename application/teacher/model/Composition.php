<?php
namespace app\teacher\model;

use think\Db;
use think\Model;

class Composition extends Model
{
    protected $table = 'nm_composition';
    protected $commom_table = 'nm_compositioncomment';
    //自定义初始化
//    protected function initialize()
//    {
//        parent::initialize();
//    }

    /**
     * [getCompositionData 获取批阅数据]
     * @Author ZQY
     * @DateTime 2018-10-15 20:56:23
     * @return   [array]  [获取批阅数据]
     */
    public function getZoroCompositionData($where,$limitstr)
    {
        return Db::table($this->table)
            ->alias('com')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->join('nm_studentcategory cate','cate.id=stu.categoryid')
            ->field('com.id,stu.nickname,com.title titles,com.type,com.addtime,com.studentid,stu.school,stu.class,cate.name')
            ->where($where)
            ->order('com.addtime','desc')
            ->limit($limitstr)
            ->select();
    }
    /**
     * [getLastCompositionData 获取上篇作文]
     * @Author ZQY
     * @DateTime 2018-10-18 19:30:23
     * @return   [array]  [获取上篇作文]
     */
    public function getLastCompositionData($where)
    {
        return Db::table($this->table)
            ->field('')
            ->where($where)
            ->order('id desc')
            ->limit(1)
            ->select();
    }
    /**
     * [ifLastComposition 获取上篇作文]
     * @Author ZQY
     * @DateTime 2018-10-18 10:56:23
     * @return   [array]  [获取上篇作文]
     */
    public function ifLastComposition($composition)
    {
        return Db::table($this->commom_table)
            ->alias('comm')
            ->join('nm_teacherinfo tea','tea.teacherid=comm.userid')
            ->where(['comm.compositionid'=>$composition,'comm.type'=>1])
            ->field('comm.reviewtime,tea.nickname')
            ->select();
    }
    /**
     * [getCompositionDataCount 获取批阅数据Count]
     * @Author ZQY
     * @DateTime 2018-10-16 10:42:23
     * @return   [array]  [获取批阅数据Num]
     */
    public function getZeroCompositionDataCount($where)
    {
        return Db::table($this->table)
            ->alias('com')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->field('com.id,stu.nickname,com.title,com.type')
            ->where($where)
            ->count();
    }
    /**
     * [getOneCompositionData 获取我的批阅数据]
     * @Author ZQY
     * @DateTime 2018-10-16 11:00:23
     * @return   [array]  [获取我的批阅数据]
     */
    public function getOneCompositionData($where,$limitstr)
    {
        return Db::table($this->commom_table)
            ->alias('comm')
            ->join('nm_composition com','comm.compositionid=com.id and comm.type=1')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->field('com.id,stu.nickname,com.title titles,com.type,comm.reviewscore,com.addtime,comm.reviewtime')
            ->where($where)
            ->order('comm.reviewtime','desc')
            ->limit($limitstr)
            ->select();
    }
    /**
     * [getOneCompositionDataCount 获取批阅数据Count]
     * @Author ZQY
     * @DateTime 2018-10-16 11:00:00
     * @return   [array]  [获取批阅数据Num]
     */
    public function getOneCompositionDataCount($where)
    {
        return Db::table($this->commom_table)
            ->alias('comm')
            ->join('nm_composition com','comm.compositionid=com.id and comm.type=1')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->field('com.id,stu.nickname,com.title,com.type')
            ->where($where)
            ->count();
    }
    /**
     * [updateReviewStatus 修改该作文为批阅中状态]
     * @Author ZQY
     * @DateTime 2018-10-16 14:49:00
     * @return   [array]  [修改该作文为批阅中状态]
     */
    public function updateReviewStatus($compositionid)
    {
        return Db::table($this->table)->where(['id'=>"$compositionid"])->update(['reviewstatus'=>'1']);
    }
    /**
     * [updateReviewStatus 修改该作文为批阅中状态]
     * @Author ZQY
     * @DateTime 2018-10-16 14:49:00
     * @return   [array]  [修改该作文为批阅中状态]
     */
    public function checkReviewStatus($compositionid)
    {
        return Db::table($this->table)->where(['id'=>"$compositionid"])->select();
    }
    /**
     * [getCompositionData 获取作文数据]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取作文数据]
     */
    public function getCompositionData($compositionid)
    {
        return Db::table($this->table)
            ->alias('com')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->field('com.title titles,com.addtime,stu.nickname,com.content contents,com.imgurl')
            ->where(['com.id'=>"$compositionid"])
            ->select();
    }
    /**
     * [addCompositionData 作文批改]
     * @Author ZQY
     * @DateTime 2018-10-16 16:41:00
     * @return   [array]  [作文批改]
     */
    public function addCompositionData($data,$compositionid)
    {
        //开启事物
        Db::startTrans();
        $update = Db::table($this->table)->where(['id'=>$compositionid])->update(['reviewstatus'=>2]);
        $add = Db::table($this->commom_table)->insert($data);
        if($update&&$add){
            Db::commit();
            return TRUE;
        }else{
            Db::rollback();
            return false;
        }
    }
    /**
     * [testingReview 获取作文评语]
     * @Author ZQY
     * @DateTime 2018-11-13 19:53:36
     * @return   [array]  [获取作文评语]
     */
    public function testingReview($where)
    {
        return Db::table($this->commom_table)->where($where)->select();
    }
    /**
     * [UpdateCompositionData 修改作文批改]
     * @Author ZQY
     * @DateTime 2018-10-16 16:41:00
     * @return   [array]  [修改作文批改]
     */
    public function UpdateCompositionData($data,$compositionid,$status)
    {
        return Db::table($this->commom_table)->where(['compositionid'=>$compositionid,'type'=>$status])->update($data);
    }
    /**
     * [getTeacherData 获取老师的评语]
     * @Author ZQY
     * @DateTime 2018-10-16 16:41:00
     * @return   [array]  [获取老师的评语]
     */
    public function getTeacherData($compositionid)
    {
        return Db::table($this->commom_table)->where(['compositionid'=>"$compositionid",'type'=>'1'])->field('reviewscore,commentcontent,reviewtime')->select();
    }
    /**
     * [getStudentData 获取学生的评语]
     * @Author ZQY
     * @DateTime 2018-10-16 16:41:00
     * @return   [array]  [获取学生的评语]
     */
    public function getStudentData($compositionid)
    {
        return Db::table($this->commom_table)->where(['compositionid'=>"$compositionid",'type'=>'2'])->field('reviewscore,commentcontent,reviewtime')->select();
    }
    /**
     * [getStudentData 获取学生的评语]
     * @Author ZQY
     * @DateTime 2018-10-16 16:41:00
     * @return   [array]  [获取学生的评语]
     */
    public function compositionRegressesStatus($compositionid)
    {
        return Db::table($this->table)->where(['id'=>"$compositionid"])->update(['reviewstatus'=>0]);
    }

}