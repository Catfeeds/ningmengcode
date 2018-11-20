<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class Hjxappcommentlabel extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_hjxappcommentlabel';
	protected $rule = [
			'id' => 'require|number',
			'star' => 'require|number',
			'content' => 'require',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
       // $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'id.require'   => lang('70204'),
            'star.require'   => lang('90021'),
            'content.require'   => lang('90021'),
        ];
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getCommentlabelList($where,$field,$limitstr,$order='id asc')
    {
        return Db::table($this->table)
				->where($where)
				->field($field)
				->limit($limitstr)
				->order($order)
				->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @param $order    string      必填
     * @param $limitstr string      必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     *
     */   
    public function getCommentlabelListCount($where){
        return Db::table($this->table)
				->where($where)
				->count();
    }
	
    /**
	 * 根据teacherid获取 标签的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getCommentlabelData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->where('delflag',0)
        ->field($field)->find();
    }
   
    /**
     * [addCommentlabel 添加标签数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addCommentlabel($data){
		unset($this->rule['id']);
     	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',90021,$validate->getError());
		}
		
		$alldata = [
			'star' => $data['star'],
			'content' => $data['content'],
			'addtime' => time(),
		];
		$logflag = Db::table($this->table)->insert($alldata);
        if($logflag){
            return return_format('',0);
        }else{
			return return_format('',40101);
        }
    }
	
    /**
     * [updateCommentlabel 更新标签数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateCommentlabel($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',90021,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkCommentlabelExsit($data['id'])) return return_format('',90020);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'star' => $data['star'],
					'content' => $data['content'],
                ] ;

                Db::table($this->table)->where($where)->update($allaccountdata) ;
				return return_format('',0);
			}else{
				return return_format('',80004);
			}
		}
    }
	
    /**
     * [delCommentlabel 删除标签信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [标签id]
     * @return   [type]               [description]
     */
    public function delCommentlabel($id){
		if(!$this->checkCommentlabelExsit($id)) return return_format('',90020);
		$where['id'] = $id;
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if($r){
			return return_format('',0);
		} else {
			return return_format('',22014);
		}
    }
	
	/**
	 * checkCommentlabelExsit 根据id检查标签是否存在
	 * @param tag id
	 * @return [bool]
	 */
	public function checkCommentlabelExsit($id){
		return Db::table($this->table)->where(['id'=>$id, 'delflag'=>0])->field('id')->find();
	}
	
	/**
	 * 根据ids获取标签内容
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getLabelNamesByIds($ids)
    {
        return Db::table($this->table)
        ->where('id', 'in', $ids)
        ->column('content');
    }
}
