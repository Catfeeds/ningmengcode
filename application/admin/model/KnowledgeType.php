<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use app\admin\model\Knowledge;
class KnowledgeType extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_knowledgetype';
	protected $rule = [
			'name' => 'require|max:30',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'name.require'   => lang('80002'),
            'name.max'       => lang('80003'),
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
    public function getKnowledgeTypeList($where,$field,$limitstr,$order='id asc')
    {
        // var_dump($order);
        return Db::table($this->table)->where($where)->field($field)->limit($limitstr)->order($order)->select();
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
    public function getKnowledgeTypeListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 知识类型的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getKnowledgeTypeData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->field($field)->find();
    }
   
    /**
     * [addKnowledgeType 添加知识类型数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addKnowledgeType($data){
  //   	$validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		if( empty($data['name'])){
			return return_format('',80099);
		}else{
				$alldata = [
					'name' => $data['name'] ,
					'addtime' => time() ,
				];
				$logflag = Db::table($this->table)->insert($alldata);
                if($logflag){
                    return return_format('',0);
                }else{
				    return return_format('',40101);
                }
		}
    }
	
    /**
     * [updateKnowledgeType 更新知识类型数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateKnowledgeType($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',40110,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkKnowledgeTypeExsit($data['id'])) return return_format('',70103);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'name'=> $data['name'],
                ] ;

                Db::table($this->table)->where($where)->update($allaccountdata) ;
				return return_format('',0);
			}else{
				return return_format('',80004);
			}
		}
    }
	
    /**
     * [delKnowledgeType 删除知识类型信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [知识类型id]
     * @return   [type]               [description]
     */
    public function delKnowledgeType($id){
		if(!$this->checkKnowledgeTypeExsit($id)) return return_format('',70103);
		$knowledgemodel = new Knowledge;
		$where['id'] = $id;
		Db::startTrans();
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if(!$r){
			Db::rollback();
            return array('code'=>10020,'info'=>lang('error'));
		}
		
		//删除该类型下面的知识
		if($knowledgemodel->checkKnowledgeByTypeid($id)){
			$r1 = $knowledgemodel->delKnowledgesByTypeid($id);
			if(!$r1){
				Db::rollback();
				return array('code'=>10020,'info'=>lang('error'));
			}
		}
		
		Db::commit();
        return array('code'=>0,'info'=>lang('success'));	
    }
	
	/**
	 * checkCategoryExsit 检查分类是否存在
	 * @param tag id
	 * @return [bool]
	 */
	public function checkKnowledgeTypeExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->where('delflag',0)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}
	
	/**
	 * 获取所有知识类型
	 * 
	 */
	public function getAllKnowledgeTypeList()
    {
        return Db::table($this->table)->where(['delflag' => 0])->field('id,name')->order('id asc')->select();
    }
	
	/**
	 * 根据name获取知识类型数据
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getFieldByName($name, $field)
    {
        return Db::table($this->table)
        ->where('name',$name)
		->where('delflag', 0)
        ->field($field)->find();
    }

}
