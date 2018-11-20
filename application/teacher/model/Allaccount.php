<?php

namespace app\teacher\model;
use think\Model;
use think\Db;
//对用户表进行操作

//直接使用Db类操作数据库，返回password

class Allaccount extends Model
{
    protected $table = 'nm_allaccount';
	/**
	 * [checkLogin 手机获取教师用户信息]
	 * @Author yr
	 * @DateTime 2018-04-20T19:31:56+0800
	 * @param    [vachar]        $mobile  [手机号]
	 * @return   array
	 */
	public function checkLogin($mobile){
			$where['mobile'] =  $mobile;
        //$where['organid'] = $organid;
			$where['usertype'] =1;
			$lists = Db::table($this->table)
			         ->where($where)
							 ->field('password,mix,status')
							 ->find();
			return $lists;
	}
	/**
	 * [updateStudentPass 修改教师手机号]
	 * @Author yr
	 * @DateTime 2018-04-24T21:02:32+0800
	 * @param    [int]         $mobile [教师手机号]
	 * @param    [array]         $newpass [新密码]
	 * @param    [int]         $organid[机构id]
	 * @return   [array]                [返回数据]
	 */
    public function updateMobile($teacherid,$newmobile,$prphone){
        $where = ['uid'=>$teacherid,'usertype'=>1];
        $data['mobile'] = $newmobile;
        $res = $this->allowField(true)->save($data,$where);
        return $res;
    }

	/**
	 * [updateStudentPass 修改教师密码]
	 * @Author yr
	 * @DateTime 2018-04-24T21:02:32+0800
	 * @param    [int]         $mobile [学生手机号]
	 * @param    [array]         $newpass [新密码]
	 * @param    [int]         $organid[机构id]
	 * @return   [array]                [返回数据]
	 */
    public function updateTeacherPass($newpass,$mobile){
        $where = ['mobile'=>$mobile,'usertype'=>1];
        $data['password'] = $newpass['password'];
        $data['mix'] = $newpass['mix'];
        $res = $this->allowField(true)->save($data,$where);
        return $res;
    }

	/**
	 * [getTeacherAccount 根据教师的id获总表id]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @param    [int]                   $teachid [机构标识id]
	 * @return   [array]                          [返回查询结果]
	 */
	public function getTeacherAccount($teachid){
		$field = 'id' ;
		return Db::table($this->table)
				->field($field)
				->where('uid','eq',$teachid)
				->find() ;
	}

	/**
    * 
    * @Author WangWY
    * @param
    */
    public function registerTeacher($data,$uid){
        $fo = ['uid'=>$uid,'mobile'=>$data['mobile'],'password'=>$data['password'],'mix'=>$data['mix']];
        return Db::table($this->table)->insert($fo);
    }
}
