<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class Knowledge extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_knowledge';
	protected $rule = [
			'id' => 'require|number',
			'typeid' => 'require',
			'content' => 'require',
            //'answer' => 'require|max:30',
            'forstudenttype' => 'require',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
       // $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'id.require'   => lang('70204'),
            'typeid.require'   => lang('70205'),
            'content.require'   => lang('70206'),
            //'answer.require'   => lang('70207'),
            //'answer.max'       => lang('70208'),
            'forstudenttype.require' => lang('70209'),
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
    public function getKnowledgeList($where,$field,$limitstr,$order='id asc')
    {
        return Db::table($this->table)
				->alias(['nm_knowledge'=>'k','nm_knowledgetype'=>'kt', 'nm_studentcategory'=>'sc'])
				->join('nm_knowledgetype','k.typeid=kt.id','LEFT')
				->join('nm_studentcategory','k.forstudenttype=sc.id','LEFT')
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
    public function getKnowledgeListCount($where){
        return Db::table($this->table)
				->alias(['nm_knowledge'=>'k'])
				->where($where)
				->count();
    }
	
    /**
	 * 根据teacherid获取 知识的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getKnowledgeData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->where('delflag',0)
        ->field($field)->find();
    }
   
    /**
     * [addKnowledge 添加知识数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addKnowledge($data){
		unset($this->rule['id']);
     	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',70202,$validate->getError());
		}
		
		$alldata = [
			'typeid' => $data['typeid'],
			'content' => $data['content'],
			'answer' => isset($data['answer']) ? $data['answer'] : '',
			'forstudenttype' => $data['forstudenttype'],
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
     * [updateKnowledge 更新知识数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateKnowledge($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',70202,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkKnowledgeExsit($data['id'])) return return_format('',70203);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'typeid' => $data['typeid'],
					'content' => $data['content'],
					'answer' => isset($data['answer']) ? $data['answer'] : '',
					'forstudenttype' => $data['forstudenttype'],
                ] ;

                Db::table($this->table)->where($where)->update($allaccountdata) ;
				return return_format('',0);
			}else{
				return return_format('',80004);
			}
		}
    }
	
    /**
     * [delKnowledge 删除知识信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [知识id]
     * @return   [type]               [description]
     */
    public function delKnowledge($id){
		if(!$this->checkKnowledgeExsit($id)) return return_format('',70203);
		$where['id'] = $id;
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if($r){
			return return_format('',0);
		} else {
			return return_format('',22014);
		}
    }
	
	/**
	 * checkKnowledgeExsit 根据id检查知识是否存在
	 * @param tag id
	 * @return [bool]
	 */
	public function checkKnowledgeExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->where('delflag', 0)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}

	/**
	 * ImportKnowledges 批量导入知识
	 * @param tag id
	 * @return [bool]
	 */
	public function ImportKnowledges($knowledgeRet){
		return Db::table($this->table)->insertAll($knowledgeRet);
	}
	
	/**
	 * checkKnowledgeByTypeid 根据typeid检查是否有知识存在
	 * @param $typeid
	 * @return [bool]
	 */
	public function checkKnowledgeByTypeid($typeid){
		$result = Db::table($this->table)
        ->where('typeid',$typeid)
		->where('delflag', 0)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}
	
	/**
     * [delKnowledgesByTypeid 根据typeid删除知识]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $typeid
     * @return   [int]               [description]
     */
    public function delKnowledgesByTypeid($typeid){
    	return Db::table($this->table)->where(['typeid'=>$typeid])->update(['delflag'=>1]);
    }
	
	/**
	 * checkKnowledgeByStuCate 根据forstudenttype检查是否有知识存在
	 * @param $forstudenttype
	 * @return [array]
	 */
	public function checkKnowledgeByStuCate($forstudenttype){
		return Db::table($this->table)
        ->where('forstudenttype',$forstudenttype)
		->where('delflag', 0)
        ->field('id')->find();
	}
	
	/**
     * [updateKnowledgesByStuCate 根据forstudenttype更新知识]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $forstudenttype
     * @return   [int]     [description]
     */
    public function updateKnowledgesByStuCate($forstudenttype){
    	return Db::table($this->table)->where(['forstudenttype'=>$forstudenttype, 'delflag'=>0])->update(['forstudenttype'=>null]);
    }
}
