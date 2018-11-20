<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use app\admin\model\Hjxappexamplesentence;
class Hjxappexamplesentencetype extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_hjxappexamplesentencetype';
	protected $rule = [
			'name' => 'require|max:30',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'name.require'   => lang('90022'),
            'name.max'       => lang('90022'),
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
    public function getExampleTypeList($where,$field,$limitstr,$order='id asc')
    {
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
    public function getExampleTypeListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 例句类型的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getExampleTypeData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->where('delflag',0)
        ->field($field)->find();
    }
   
    /**
     * [addExampleType 添加例句类型数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addExampleType($data){
  //   	$validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		if( empty($data['name'])){
			return return_format('',90022);
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
     * [updateExampleType 更新例句类型数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateExampleType($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',90022,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkExampleTypeExsit($data['id'])) return return_format('',90020);
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
     * [delExampleType 删除例句类型信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [例句类型id]
     * @return   [type]               [description]
     */
    public function delExampleType($id){
		if(!$this->checkExampleTypeExsit($id)) return return_format('',90020);
		$examplemodel = new Hjxappexamplesentence;
		$where['id'] = $id;
		Db::startTrans();
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if(!$r){
			Db::rollback();
            return array('code'=>10020,'info'=>lang('error'));
		}
		
		//删除该类型下面的例句
		if($examplemodel->checkExampleByType($id)){
			$r1 = $examplemodel->delExamplesByType($id);
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
	public function checkExampleTypeExsit($id){
		return Db::table($this->table)->where(['id'=>$id, 'delflag'=>0])->field('id')->find();
	}
	
	/**
	 * 获取所有例句类型
	 * 
	 */
	public function getAllExampleTypeList()
    {
        return Db::table($this->table)->where(['delflag' => 0])->field('id,name')->order('id asc')->select();
    }
	
	/**
	 * 根据name获取例句类型数据
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
