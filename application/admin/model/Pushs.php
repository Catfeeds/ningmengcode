<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;

class Pushs extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_pushs';

	/**
	 * [getPushsId 根据指定条件推送信息]
	 * @Author JCR
	 * @param    [type]   $organid [description]
	 * @return   [type]            [description]
	 */
	public function getPushsId($userid,$usertype){
		return Db::table($this->table)
						->where('userid',$userid)
						->where('usertype',$usertype)->find();
	}


	/**
	 * [getPushList ]
	 * @Author JCR
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function getPushList($where){
		return Db::table($this->table)->where($where)->field('userid,usertype,logintype')->select();
	}


	/**
	 * [deletePush 删除用户对应唯一标识]
	 * @Author JCR
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function deletePush($id){
		return Db::table($this->table)->where('id',$id)->delete();
	}

	/**
	 * [addPush 添加编辑推送唯一标识]
	 * @Author  JCR
	 * @param   [array]     $teacherid   [老师ID]
	 * @param   [int]       $organid	 [机构标识id]
	 * @return  [int]       [更新结果标记]
	 */
	public function addPush($data){
		if(isset($data['id'])){
			return Db::table($this->table)->where('id',$data['id'])->update($data);
		}else{
			$data['logintype'] = 1;
			return Db::table($this->table)->insert($data);
		}
	}


	/**
	 * [addPush 编辑推送唯一标识]
	 * @Author  JCR
	 * @param   [array]     $teacherid   [老师ID]
	 * @param   [int]       $organid	 [机构标识id]
	 * @return  [int]       [更新结果标记]
	 */
	public function exidPush($where,$type){
		return Db::table($this->table)->where($where)->update(['logintype'=>$type]);
	}



}
