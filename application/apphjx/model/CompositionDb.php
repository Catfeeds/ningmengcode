<?php
namespace app\apphjx\model;

use think\Model;
use think\Validate;
use think\Db;

class CompositionDb extends Model
{
    protected $table = 'nm_composition';
    protected $lable_table = 'nm_hjxapplable';
    protected $comment_table = 'nm_compositioncomment';


    /**
     * [getCompositionDb 获取首页作文列表数据]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取首页作文列表数据]
     */
    public function getCompositionDb($studentid,$status,$limitstr)
    {
        return Db::table($this->table)
            ->where(['type'=>"$status",'studentid'=>"$studentid"])
            ->field('id,title,imgurl,label,addtime,reviewstatus')
            ->limit($limitstr)
            ->order('addtime','desc')
            ->select();
    }
    /**
     * [getCompositionDbCount 获取首页作文列表数据Count]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取首页作文列表数据Count]
     */
    public function getCompositionDbCount($studentid,$status)
    {
        return Db::table($this->table)->where(['type'=>"$status",'studentid'=>"$studentid"])->count();
    }
    /**
     * [getLable 获取作文标签数据]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取标签作文数据]
     */
    public function getLable($where)
    {
        return Db::table($this->lable_table)->where($where)->field('lablename')->select();
    }
    /**
     * [getCompositionData 获取作文详情数据]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取作文详情数据]
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
     * [getTeacherData 获取老师的评语]
     * @Author ZQY
     * @DateTime 2018-10-17 16:53:00
     * @return   [array]  [获取老师的评语]
     */
    public function getTeacherData($compositionid)
    {
        return Db::table($this->comment_table)
            ->alias('comm')
            ->join('nm_teacherinfo tea','tea.teacherid=comm.userid')
            ->field('tea.nickname,comm.reviewscore,comm.commentcontent,comm.reviewtime,tea.imageurl')
            ->where(['comm.compositionid'=>"$compositionid",'type'=>'1'])
            ->select();
    }
    /**
     * [getCommentStatus 查询学生评论数据]
     * @Author ZQY
     * @DateTime 2018-10-17 16:53:00
     * @return   [array] 查询学生评论数据]
     */
    public function getCommentStatus($compositionid)
    {
        return Db::table($this->comment_table)->where(['compositionid'=>"$compositionid",'type'=>'2'])->select();
    }

    /**
     * [getCommentInfo 获取星级标签]
     * @Author cy
     * @DateTime 2018-11-19
     * @return   array
     */
    public function getCommentidsInfo()
    {
        $lists = Db::table('nm_hjxappcommentlabel')
            ->field('id,star,content')
            ->where('delflag',0)
            ->order('star')
            ->select();
        return $lists;
    }

    /**
     * [getCommentData 查询学生评论数据]
     * @Author ZQY
     * @DateTime 2018-10-17 20:23:00
     * @return   [array] 查询学生评论数据]
     */
    public function getCommentData($compositionid)
    {
        return Db::table($this->comment_table)
            ->alias('comm')
            ->join('nm_hjxappstudentinfo stu','stu.id = comm.userid')
            ->field('stu.nickname,stu.imageurl,comm.reviewscore,comm.commentcontent,comm.reviewtime,comm.commentlabelids')
            ->where(['compositionid'=>"$compositionid",'type'=>'2'])
            ->select();
    }
    /**
     * [addComment 添加学生评价]
     * @Author ZQY
     * @DateTime 2018-10-18 10:54:00
     * @return   [array] 添加学生评价]
     */
    public function addComment($data)
    {
        return Db::table($this->comment_table)->insert($data);
    }
    /**
     * [updateComment 修改学生评价]
     * @Author ZQY
     * @DateTime 2018-10-18 10:55:00
     * @return   [array] 修改学生评价]
     */
    public function updateComment($data,$compositionid)
    {
        return Db::table($this->comment_table)->where(['compositionid'=>"$compositionid",'type'=>'2'])->update($data);
    }
    /**
     * [CompositionInsert 添加学生作文]
     * @Author ZQY
     * @DateTime 2018-10-18 16:16:00
     * @return   [array] 添加学生作文]
     */
    public function CompositionInsert($data)
    {
        return Db::table($this->table)->insert($data);
    }
    /**
     * [CompositionUpdate 修改作文]
     * @Author ZQY
     * @DateTime 2018-10-18 16:19:00
     * @return   [array] 修改作文]
     */
    public function CompositionUpdate($data,$compositionid)
    {
         return Db::table($this->table)->where(['id'=>"$compositionid"])->update($data);
    }
    /**
     * [testingSubmit 获取作文的提交状态]
     * @Author ZQY
     * @DateTime 2018-10-29 10:11:00
     * @return   [array] 获取作文的提交状态]
     */
    public function testingSubmit($compositionid)
    {
        return Db::table($this->table)->where(['id'=>"$compositionid"])->field('studentid')->select();
    }
}