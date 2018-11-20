<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
class Adminmember extends Model
{
	protected $table = 'nm_adminmember';

	
	/**
	 * [getOrganUser //获取指定机构注册用户的在该表的信息]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [int]                   $organid   [组织id]
	 * @return   [array]                 $res       [注册该组织的用户的信息]
	 */
	public function getOrganUser($organid){
		$res = Db::table($this->table)
		->where('organid','EQ',$organid)
		->find();
		return $res;
	}
}	