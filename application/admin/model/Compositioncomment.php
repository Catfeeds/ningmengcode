<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use think\Session;

class Compositioncomment extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_compositioncomment';
	/* protected $rule = [
			'mobile'   => 'require|max:25',
			'nickname' => 'require|max:30',
			'sex'      => 'number|between:0,2',
			'country'  => 'number',
			'province' => 'number',
            'city'     => 'number',
			'status'   => 'number|between:0,1',
		]; */
	//protected $message = [];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        /* $this->message = [
            'mobile.require'   => lang('40072'),
            'mobile.max'       => lang('40073'),
            'nickname.require' => lang('40074'),
            'nickname.max'     => lang('40075'),
            'sex.number'       => lang('40076'),
            'sex.between'      => lang('40077'),
            'country.number'   => lang('40078'),
            'province.number'  => lang('40079'),
            'city.number'      => lang('40080'),
            'status.number'    => lang('40081'),
            'status.between'   => lang('40082'),
        ]; */
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getReviewList($where,$limitstr)
    {
        return Db::table($this->table. ' cc')
				->join('nm_teacherinfo t', 'cc.userid=t.teacherid', 'LEFT')
				->where($where)
				->field('cc.userid as teacherid,t.nickname as teachername,count(cc.userid) as reviewcount')
				->limit($limitstr)
				->group('cc.userid')
				->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getReviewListCount($where)
    {
        return Db::table($this->table. ' cc')
				->join('nm_teacherinfo t', 'cc.userid=t.teacherid', 'LEFT')
				->where($where)
				->group('cc.userid')
				->count();
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getTeacherReviewList($where,$limitstr)
    {
        return Db::table($this->table. ' cc')
				->join('nm_composition c', 'cc.compositionid=c.id', 'LEFT')
				->join('nm_hjxappstudentinfo hs', 'c.studentid=hs.id', 'LEFT')
				->where($where)
				->field('cc.compositionid,hs.nickname as studentname,c.title,cc.reviewtime')
				->order('cc.reviewtime desc')
				->limit($limitstr)
				->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getTeacherReviewListCount($where)
    {
        return Db::table($this->table. ' cc')
				->join('nm_composition c', 'cc.compositionid=c.id', 'LEFT')
				->join('nm_hjxappstudentinfo hs', 'c.studentid=hs.id', 'LEFT')
				->where($where)
				->count();
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getTeacherComment($compositionid)
    {
        return Db::table($this->table. ' cc')
				->join('nm_teacherinfo t', 'cc.userid=t.teacherid', 'LEFT')
				->where('cc.compositionid', $compositionid)
				->where('cc.type', 1)
				->field('cc.reviewscore,cc.commentcontent,FROM_UNIXTIME(cc.reviewtime, "%Y/%m/%d %H:%i") as reviewtime,t.nickname as teachername')
				->find();
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getStudentComment($compositionid)
    {
        return Db::table($this->table)
				->where('compositionid', $compositionid)
				->where('type', 2)
				->field('reviewscore,commentcontent,FROM_UNIXTIME(reviewtime, "%Y/%m/%d %H:%i") as reviewtime,commentlabelids')
				->find();
    }
	
	/**
	 * checkCommentById 检查是否存在作文评论
	 * @param  id
	 * @return [array]
	 */
	public function checkCommentById($id){
		return Db::table($this->table)->where(['compositionid'=>$id, 'delflag'=>0])->field('id')->find();
	}
	
	/**
	 * delCommentsById 假删除作文评论
	 * @param  id
	 * @return [int]
	 */
	public function delCommentsById($id){
		return Db::table($this->table)->where(['compositionid'=>$id, 'delflag'=>0])->update(['delflag'=>1]);
	}
}
