<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Adminmember extends Model
{
	protected $pk    = 'id';
	protected $table = 'nm_adminmember';

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
    /**
     * [getOrganIdByAdminId //根据机构登录人员id获取对应的机构id]
     * @Author zzq
     * @DateTime 2018-05-09
     * @param adminid int  机构管理员id
     */
    public function getOrganIdByAdminId($adminid){
    	if(empty($adminid)){
    		return return_format('',50000,'adminid为空');
    	}
		$res = Db::table($this->table)
		->where('id','EQ',$adminid)
		->find();
		if($res){
			return return_format([],0,'获取机构id成功');
		}else{
			return return_format('',50086,'暂无此管理员');
		}      
    }


	/**
	 * [getOrganUser //获取指定机构注册用户的在该表的信息]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [int]                   $id   [组织id]
	 * @return   [array]                 $res       [注册该组织的用户的信息]
	 */
	public function getOrganUserField($id){
		$res = Db::table($this->table)
			->where('id','EQ',$id)
			->field('useraccount,adminname,mobile,userimage,logintime')
			->find();
		return $res;
	}


	/**
	 * 获取符合条件的数量
	 * @param $where
	 * @return int|string
	 */
	public function getCount($where){
		return Db::table($this->table)->where($where)->count();
	}

}	