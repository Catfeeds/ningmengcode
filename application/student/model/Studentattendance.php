<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*老师对学生评价表
 * @ yr
*/
class Studentattendance extends Model{
    protected $table = 'nm_studentattendance';
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getFeedbackList 获取学生反馈信息]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $where [查询条件]
     * @return   [array]
     */
    public function getFeedbackList($where,$lmitstr,$search)
    {
        $field = 'l.periodname,s.curriculumname,s.gradename,a.comment,a.score,a.addtime,t.nickname as teachername';
        if(empty($search)){
            $result = Db::table($this->table.' a')
                ->field($field)
                ->join('nm_lessons l','l.id=a.lessonsid','left')
                ->join('nm_scheduling s','s.id=a.schedulingid','left')
                ->join('nm_teacherinfo t','t.teacherid=a.teacherid','left')
                ->where($where)
                ->where('a.status','eq',1)
                ->order('a.addtime desc')
                ->limit($lmitstr)
                ->select();
        }else{
            $result = Db::table($this->table.' a')
                ->field($field)
                ->join('nm_lessons l','l.id=a.lessonsid','left')
                ->join('nm_scheduling s','s.id=a.schedulingid','left')
                ->join('nm_teacherinfo t','t.teacherid=a.teacherid','left')
                ->where($where)
                ->where('a.status','eq',1)
                ->where('lessonsid','in',function ($query) use($search) {
                    $query->table('nm_lessons')->where('periodname','like',"%$search%")->where('delflag','eq',1)->field('id');
                })
                ->order('a.addtime desc')
                ->limit($lmitstr)
                ->select();
        }
        return $result;

    }
    /**
     * [getFeedbackCount 获取学生反馈数量]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $where [查询条件]
     * @return   [array]
     */
    public function getFeedbackCount($where,$search)
    {
        if(!empty($search)){
            $result = Db::table($this->table.' a')
                ->where($where)
                ->where('a.status','eq',1)
                ->where('lessonsid','in',function ($query) use($search) {
                    $query->table('nm_lessons')->where('periodname','like',"%$search%")->where('delflag','eq',1)->field('id');
                })
                ->count();
        }else{
            $result = Db::table($this->table.' a')
                ->where($where)
                ->where('a.status','eq',1)
                ->count();
        }
        return $result;

    }
    /**
     * [getFindInfo 根据where条件查询单条数据]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $where [查询条件]
     * @return   [array]
     */
    public function getFindInfo($where,$field)
    {
        $result = Db::table($this->table.' a')
            ->field($field)
            ->join('nm_lessons l','l.id=a.lessonsid','left')
            ->join('nm_scheduling s','s.id=a.schedulingid','left')
            ->join('nm_teacherinfo t','t.teacherid=a.teacherid','left')
            ->where($where)
            ->select();
        return $result;

    }
	
	/**
     * [查看学生某课时的点评信息]
     * @Author lc
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $where [查询条件]
     * @return   [array]
     */
    public function getFeedbackOne($where,$field)
    {
        return Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
    }
}








