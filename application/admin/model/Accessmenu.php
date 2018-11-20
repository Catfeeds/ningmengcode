<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Accessmenu extends Model
{


	protected $pk    = 'id';
	protected $table = 'nm_accessmenu';


	/**
	 * 查询菜单列表
	 * @param $where
	 * @return false|\PDOStatement|string|\think\Collection
	 */
	public function getList($where){
		$field = 'id,name,url,fatherid,icon,linkname';
		return Db::table($this->table)->where($where)->field($field)->select();
	}

	/**
	 * [updateUserMsg   更新用户的头像和昵称]
	 * @Author wyx
	 * @DateTime 2018-05-22
	 * @return   [string]                $userimg   [用户头像的url地址]
	 * @return   [string]                $username  [用户的名字或者昵称]
	 * @param    [int]                   $uid       [登陆用户id]
	 */
	public function updateUserMsg($userimg,$username,$uid){
		$data = [
					'adminname'=>$username,
					'userimage'=>$userimg
				] ;
		$res = Db::table($this->table)
		->where('id','EQ',$uid)
		->update($data);
		return $res;
	}



}	