<?php
namespace app\apphjx\model;

use think\Model;
use think\Validate;
use think\Db;

class HomepageDb extends Model
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
    public function getCompositionDb($studentid,$status)
    {
        return Db::table($this->table)
            ->where(['type'=>"$status",'studentid'=>"$studentid"])
            ->field('id,title,imgurl,label,addtime,reviewstatus,submit')
            ->select();
    }
    /**
     * [getLable 获取作文标签数据]
     * @Author ZQY
     * @DateTime 2018-10-16 14:58:00
     * @return   [array]  [获取标签作文数据]
     */
    public function getLable($where)
    {
        return Db::table($this->lable_table)
            ->where($where)
            ->field('lablename')
            ->select();
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
            ->field('com.title,com.addtime,stu.nickname,com.content,com.imgurl')
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
        return Db::table($this->table)
            ->alias('com')
            ->join('nm_hjxappstudentinfo stu','stu.id=com.studentid')
            ->field('com.title,com.addtime,stu.nickname,com.content,com.imgurl')
            ->where(['com.id'=>"$compositionid"])
            ->select();
    }

}