<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;

class Messagesign extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_messagesign';

	/**
	 * [getMessagesignById 根据机构id获取机构签名]
	 * @Author JCR
	 * @param    [type]   $organid [description]
	 * @return   [type]            [description]
	 */
	public function getSignById($organid){
		$field = 'id,signname,signid,status' ;
		return Db::table($this->table)->field($field)->where(['organid'=>$organid,'delflag'=>1])->find();
	}


	/**
	 * 签名列表
	 * @param array $where
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function getSignList($where = []){
		$field = 'id,signname,signid,status' ;
		return Db::table($this->table)->field($field)->where($where)->select();
	}



	/**
	 * [updateOrganMsg 更新短信模版]
	 * @Author  JCR
	 * @param    [array]     $data    [需要更新的数据]
	 * @param    [int]       $organid [机构标识id]
	 * @return   [int]       [更新结果标记]
	 */
	public function updateSign($data,$where = []){
		if($where){
			return $this->allowField(true)->where($where)->save($data);
		}else{
			$this->save($data);
			return $this->id;
		}
	}
	



}
