<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;

class Message extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_message';


	// 分类添加验证规则
	public $rule = [
		'ids'	=> 'require',
		'title'	=> 'require|max:14',
		'content'=>'require|max:50',
	];
	public $message = [];



	//自定义初始化
	protected function initialize(){
		parent::initialize();
		$this->message = [
			'ids.require' => lang('11020'),
			'title.require' => lang('11021'),
			'title.max' => lang('11023'),
			'content.require' => lang('11022'),
			'content.max' => lang('11024'),
		];

	}


	/**
	 * [getMessageById 根据机构id获取机构签名]
	 * @Author JCR
	 * @param    [type]   $organid [description]
	 * @return   [type]            [description]
	 */
	public function getMessageById($organid){
		$field = 'id,signname,signid,status' ;
		return Db::table($this->table)->field($field)->where(['organid'=>$organid,'delflag'=>1])->find();
	}


	/**
	 * [getMessageList 消息列表]
	 * @Author JCR
	 * @param  array $where
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function getMessageList($where = [],$pagenum,$limit){
		$field = 'id,content,type,addtime,userid,usertype,istoview';
		return Db::table($this->table)->field($field)->where($where)->page($pagenum,$limit)->order('addtime','desc')->select();
	}


	/**
	 * [getMessageList 消息count]
	 * @Author JCR
	 * @param  array $where
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function getMessageCount($where = []){
		return Db::table($this->table)->where($where)->count();
	}


	/**
	 * [updateMessage 更新消息]
	 * @Author  JCR
	 * @param   [array]     $data    [需要更新的数据]
	 * @param   [int]       $organid [机构标识id]
	 * @return  [int]       [更新结果标记]
	 */
	public function updateMessage($data,$where = []){
		if($where){
			return $this->allowField(true)->where($where)->update($data);
		}else{
			$this->save($data);
			return $this->id;
		}
	}

	/*
	 * [updateMessage 批量更新消息]
	 * @author wangwy
	 */
	public function addMessage($arr){
	    return $this->saveAll($arr);
    }
	



}
