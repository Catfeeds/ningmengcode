<?php
namespace app\microsite\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 出勤表 Studentattendance
 * @ lc
*/
class Studentattendance extends Model{
    protected $table = 'nm_studentattendance';
    //自定义初始化
    /**
     * [getCommentList  获取点评列表]
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getCommentList($userid,$limitstr){
        $lists =Db::table($this->table.' sa')
			->join('nm_scheduling s','s.id=sa.schedulingid','LEFT')
			->join('nm_lessons l','l.id=sa.lessonsid','LEFT')
            ->field('sa.id,sa.score,s.gradename,l.periodname')
            ->where('sa.studentid','eq',$userid)
            ->where('sa.status','eq',1)
            ->order('sa.id asc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
	
    /**
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getCommentCount($userid){
        $lists = Db::table($this->table)
            ->field('id')
            ->where('studentid','eq',$userid)
            ->where('status','eq',1)
            ->count();
        return  $lists;
    }
    
	/**
	 * 查看点评详情
	 * @param $id  出勤表主键ID
	 * @return array
	 */
	public function getCommentMsg($id){
		$lists =Db::table($this->table.' sa')
			->join('nm_teacherinfo t','t.teacherid=sa.teacherid','LEFT')
			->join('nm_lessons l','l.id=sa.lessonsid','LEFT')
            ->field('sa.id,sa.score,sa.comment,sa.addtime,l.periodname,t.nickname')
            ->where('sa.id','eq',$id)
            ->find();
        return  $lists;
	}
}







