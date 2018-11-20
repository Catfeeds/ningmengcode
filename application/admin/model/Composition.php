<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use think\Session;

class Composition extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_composition';
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
	 * @Author wyx
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getCompositionList($where,$limitstr,$order)
    {
        return Db::table($this->table. ' c')
				->join('nm_hjxappstudentinfo hs', 'c.studentid=hs.id', 'LEFT')
				->join('nm_studentcategory sc', 'hs.categoryid=sc.id', 'LEFT')
				->join('nm_compositioncomment cc', 'c.id=cc.compositionid and cc.type=1', 'LEFT')
				->join('nm_compositioncomment ccc', 'c.id=ccc.compositionid and ccc.type=2', 'LEFT')
				->where($where)
				->field('c.id,c.type,c.title,c.addtime,c.studentid,c.reviewstatus,hs.school,sc.name as grade,hs.class,hs.nickname,cc.reviewtime,ccc.reviewscore as studentreviewscore')
				->limit($limitstr)->order($order)->select();
    }
	
    /**
     * 从数据库获取 学生列表的符合条件的总记录数
     * @Author wyx
     * @param $where    array       必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getCompositionListCount($where)
    {
        return Db::table($this->table. ' c')
				->join('nm_hjxappstudentinfo hs', 'c.studentid=hs.id', 'LEFT')
				->join('nm_compositioncomment cc', 'c.id=cc.compositionid and cc.type=1', 'LEFT')
				->join('nm_compositioncomment ccc', 'c.id=ccc.compositionid and ccc.type=2', 'LEFT')
				->where($where)
				->count('distinct c.id');
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getCompositionData($compositionid)
    {
        return Db::table($this->table. ' c')
			->join('nm_hjxappstudentinfo hs', 'c.studentid=hs.id', 'LEFT')
			->where('c.id', $compositionid)
			->field('c.title,c.content,c.imgurl,c.videourl,c.studentid,hs.nickname as studentname,FROM_UNIXTIME(c.addtime, "%Y/%m/%d %H:%i") as addtime,c.reviewstatus')
			->find();
    }
	
	/**
	 * 获取上次批阅信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getPreCompositionData($compositionid, $studentid)
    {
        return Db::table($this->table)
			->where('id', '<', $compositionid)
			->where('studentid', 'EQ', $studentid)
			->field('id')
			->order('id desc')
			->find();
    }
	
	/**
     * [delComposition 删除]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [id]
     * @return   [type]               [description]
     */
    public function delComposition($id){
		if(!$this->checkCompositionExsit($id)) return return_format('',90020);
		$commentmodel = new Compositioncomment;
		$where['id'] = $id;
		Db::startTrans();
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if(!$r){
			Db::rollback();
            return array('code'=>10020,'info'=>lang('error'));
		}
		
		//删除该作文的评论
		if($commentmodel->checkCommentById($id)){
			$r1 = $commentmodel->delCommentsById($id);
			if(!$r1){
				Db::rollback();
				return array('code'=>10020,'info'=>lang('error'));
			}
		}
		
		Db::commit();
        return array('code'=>0,'info'=>lang('success'));	
    }
	
	/**
	 * checkCompositionExsit 检查作文是否存在
	 * @param  id
	 * @return [bool]
	 */
	public function checkCompositionExsit($id){
		return Db::table($this->table)->where(['id'=>$id, 'delflag'=>0])->field('id')->find();
	}
}
