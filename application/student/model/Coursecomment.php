<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课程评论Model
 * @ yr
*/
class Coursecomment extends Model{
    protected $table = 'nm_coursecomment';
    protected $rule = [
        'score'   => 'require',
        'content' => 'require',
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
          parent::initialize();
          $this->message = [
                'score.require' => lang('33033'),
                'content.require' => lang('33036'),
            ];
    }
    /**
     * [add 插入评论数据]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           nickname  必填用户昵称
     * @param    [int]              curriculumid  必填课程id
     * @param    [int]              type 必填课程类型
     * @param    [string]           content  必填评价内容
     * @param    [int]              studentid  必填学生id
     * @param    [int]              teacherid  必填老师id
     * @param    [int]              score  必填分数
     * @param    [int]              schedulingid 排课id
     * @param   [int]              lessonsid 机构id
     * @return   array
     */
    public function add($data){
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return $validate->getError();
        }else{
            $data['addtime'] = time();
            $data = where_filter($data,array('curriculumid','nickname','classtype','studentid','content','allaccountid','score','schedulingid','addtime','lessonsid','toteachid'));
            $id = Db::table($this->table)->insertGetId($data);
        }
            return $id?$id:0;
    }

    /**
     * [getCommentScore 获取老师的平均评分]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getCommentScore($teacherid){
        $lists =Db::table($this->table)
            ->where('allaccountid','eq',$teacherid)
            ->where('delflag','eq','1')
            ->avg('score');
        return  $lists;
    }
    /**
     * [getCommentList 获取排课老师的评论]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getCommentList($teacherid,$limitstr){
        $lists = Db::table($this->table.' c')
            ->field('u.imageurl,u.nickname,FROM_UNIXTIME(c.addtime) as addtime,c.score,c.content,c.id as commentid')
            ->join('nm_studentinfo u','c.studentid=u.id','LEFT')
            ->where('c.allaccountid','eq',$teacherid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->order('c.addtime','desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getCommentCount 获取排课老师的评论数量]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getCommentCount($teacherid){
        $lists = Db::table($this->table.' c')
            ->where('c.allaccountid','eq',$teacherid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->count();
        return  $lists;
    }
    /**
     * [getCommentList 获取课程的评论]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getCommentListBycid($cid,$limitstr){
        $lists = Db::table($this->table.' c')
            ->field('u.imageurl,u.nickname,FROM_UNIXTIME(c.addtime) as addtime,c.score,c.content,c.id as commentid')
            ->join('nm_studentinfo u','c.studentid=u.id','LEFT')
            ->where('c.curriculumid','eq',$cid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->order('c.addtime','desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getCommentCount 获取排课老师的评论数量]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getCommentCountBycid($cid){
        $lists = Db::table($this->table.' c')
            ->where('c.curriculumid','eq',$cid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->count();
        return  $lists;
    }
    /**
     * [getCommentBylessonid 获取评论信息]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $lessonsid [课节id]
     * @return   array
     */
    public function getCommentBylessonid($toteachid){
        $lists = Db::table($this->table)
            ->field('id')
            ->where('toteachid','eq',$toteachid)
            ->where('delflag','eq','1')
            ->find();
        return  $lists;
    }
    /**
     * [getLessonsCommentCount 获取排课老师的评论数量]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getLessonsCommentCount($studentid,$lessonsid){
        $lists = Db::table($this->table)
           ->where('studentid','eq',$studentid)
           ->where('lessonsid','eq',$lessonsid)
           ->where('classtype','eq','1')
            ->count();
        return  $lists;
    }
    /**
     * [getLessonsComment 获取录播课的课时评论]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getLessonsComment($lessonsid,$limitstr){
        $lists = Db::table($this->table.' c')
            ->field('u.imageurl,u.nickname,FROM_UNIXTIME(c.addtime) as addtime,c.score,c.content,c.id as commentid')
            ->join('nm_studentinfo u','c.studentid=u.id','LEFT')
            ->where('c.lessonsid','eq',$lessonsid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->where('c.classtype','eq','1')
            ->order('c.addtime','desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getLessonsCount 获取录播课的课时评论数量]
     * @Author yr
     * @DateTime 2018-04-25T13:58:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getLessonsCount($lessonsid){
        $lists = Db::table($this->table.' c')
            ->where('c.lessonsid','eq',$lessonsid)
            ->where('c.delflag','eq','1')
            ->where('c.status','eq','1')
            ->where('c.classtype','eq','1')
            ->count();
        return  $lists;
    }
}







