<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class Hjxappexamplesentence extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_hjxappexamplesentence';
	protected $rule = [
			//'id' => 'require|number',
			'type' => 'require|number',
			'content' => 'require',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
       // $this->pagenum = config('paginate.list_rows');
        $this->message = [
            //'id.require'   => lang('70204'),
            'type.require'   => lang('90023'),
            'type.number'   => lang('90024'),
            'content.require'   => lang('90025'),
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
    public function getExampleList($where,$field,$limitstr,$order='id asc')
    {
        return Db::table($this->table . ' e')
				->join('nm_hjxappexamplesentencetype et','e.type=et.id','LEFT')
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
    public function getExampleListCount($where){
        return Db::table($this->table . ' e')
				->where($where)
				->count();
    }
	
    /**
	 * 根据teacherid获取 例句的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getExampleData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->where('delflag',0)
        ->field($field)->find();
    }
   
    /**
     * [addExample 添加例句数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addExample($data){
		unset($this->rule['id']);
     	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',70202,$validate->getError());
		}
		
		$alldata = [
			'type' => $data['type'],
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
     * [updateExample 更新例句数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateExample($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',70202,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkExampleExsit($data['id'])) return return_format('',90020);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'type' => $data['type'],
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
     * [delExample 删除例句信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [例句id]
     * @return   [type]               [description]
     */
    public function delExample($id){
		if(!$this->checkExampleExsit($id)) return return_format('',90020);
		$where['id'] = $id;
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if($r){
			return return_format('',0);
		} else {
			return return_format('',22014);
		}
    }
	
	/**
	 * checkExampleExsit 根据id检查例句是否存在
	 * @param tag id
	 * @return [bool]
	 */
	public function checkExampleExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->where('delflag', 0)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}

	/**
	 * ImportExamples 批量导入例句
	 * @param $exampleRet
	 * @return [int]
	 */
	public function ImportExamples($exampleRet){
		return Db::table($this->table)->insertAll($exampleRet);
	}
	
	/**
	 * checkExampleByType 根据type检查是否有例句存在
	 * @param $type
	 * @return [bool]
	 */
	public function checkExampleByType($type){
		return Db::table($this->table)->where(['type'=>$type, 'delflag'=>0])->field('id')->find();
	}
	
	/**
     * [delExamplesByType 根据type删除例句]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $type
     * @return   [int]               [description]
     */
    public function delExamplesByType($type){
    	return Db::table($this->table)->where(['type'=>$type])->update(['delflag'=>1]);
    }
}
