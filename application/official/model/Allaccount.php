<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use login\Authorize;
class Allaccount extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_allaccount';

	protected $rule = [
			//'username' => 'require|max:30',
			'mobile'   => 'require|max:25',
			'useraccount'=> 'require|max:30' ,
			'password'   => 'require|min:6|max:16',
			//'repassword' => 'require|confirm:password',
		];
	protected $message = [];
	protected $modifyPassRule = [
			//'username' => 'require|max:30',
			'mobile'   => 'require|max:25',
			'password'   => 'require|min:6|max:50',
		];
	protected $modifyPassMessage = [];
	protected function initialize() {
		parent::initialize();
		$this->message = [
			'useraccount.require' => lang('50200'),
			'useraccount.max' => lang('50201'),
			'mobile.require' => lang('50202'),
			'mobile.max' => lang('50203'),
			'password.require' => lang('50204'),
			'password.min' => lang('50205'),
			'password.max' => lang('50206'),
		];
		$this->modifyPassMessage=[
			'mobile.require' => lang('50202'),
			'mobile.max' => lang('50203'),
			'password.require' => lang('50204'),
			'password.min' => lang('50205'),
			'password.max' => lang('50206'),
		];
	}
	/**
	 * [addAdminUser 添加机构管理账号]
	 * @Author wyx
	 * @DateTime 2018-04-24T14:42:41+0800
	 * @param    [array]                 $data    [要添加的信息]
	 * @param    [int]                   $organid [组织机构标识id]
	 */
	public function addAdminUser($data,$organid,$usertype=2){

		$validate = new Validate($this->rule, $this->message);
		if( !$validate->check($data) ){
			// var_dump($validate->check($data));
			// die;
			return return_format('',-1,$validate->getError());
		}else{
			//var_dump($data);exit();
			//die;
			Db::startTrans() ;
			try{
				//获取 当前最大id
				$currenttime = time() ;
				$admindata['useraccount'] = $data['useraccount'] ;
				$admindata['adminname']   = $data['username'] ;
				$admindata['mobile']      = $data['mobile'] ;
				$admindata['organid']     = $organid ;
				$admindata['logintime']   = $currenttime ;
				!empty($data['info']) ? $admindata['info'] = $data['info'] : '' ;
				$uid = Db::table('nm_adminmember')->insertGetId($admindata);

				$accountdata['uid'] = $uid ;
				$accountdata['usertype'] = $usertype ;
				$accountdata['username'] = $data['useraccount'] ;
				$accountdata['mobile']   = $data['mobile'] ;
				$accountdata['addtime']  = $currenttime ;
				$accountdata['organid']  = $organid ;

				//
				$cryptstr = new Authorize;
				$cryptarr = $cryptstr->createUserMark($data['password']);
				$accountdata['password']  = $cryptarr['password'] ;
				$accountdata['mix']       = $cryptarr['mix'] ;

				Db::table($this->table)->insert($accountdata);
				// 提交事务
				Db::commit();
				return return_format(['adminid'=>$uid],0,lang('success'));
			} catch (\Exception $e) {
				//回滚事务
				Db::rollback();
				return return_format($e->getMessage(),50007,lang('50007'));
			}

		}
	}

	/**
	 * [getOrganAddTime 获取注册时间]
	 * @Author  zzq
	 * @DateTime 2018-05-04
	 * @param    [int]                   $organid [及机构标识id]
	 * @return   [int]                   时间戳        [description]
	 */
	public function getOrganAddTime($organid){
		$res = Db::table($this->table)
		->where('organid','EQ',$organid)
		->find();
		return $res['addtime'];
	}

	/**
	 * [getOrganUserInfo 获取该机构的注册人信息]
	 * @Author  zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $organid [及机构标识id]
	 * @return   [array]                           [获取该机构的注册人信息]
	 */
	public function getOrganUserInfo($organid){
		$res = Db::table($this->table)
		->where('organid','EQ',$organid)
		->find();
		return $res;
	}

	/**
	 * [hasUseraccount 判断这个机构的账户是否存在]
	 * @Author  zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $useraccount [账户名]
	 * @return   [array]                           [获取该机构的注册人信息]
	 */
	//判断填写的useraccout的是否已经存在
	public function hasUseraccount($useraccount){
		$res = Db::table($this->table)
		->where('username','EQ',$useraccount)
		->find();
		if($res){
			return $res;
		}else{
			return false;
		}		
	}

	/**
	 * [hasMobile 判断这个机构的手机号是否存在]
	 * @Author  zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $mobile [账户名]
	 * @return   [array]                           [获取该机构的注册人信息]
	 */
	public function hasMobile($mobile,$isVip){
		$res = Db::table($this->table)
		->alias('a')
		->join('nm_organ b','a.organid = b.id')
		->field('a.id,a.mobile,a.organid,b.vip')
		->where('a.mobile','EQ',$mobile)
		//->where('b.vip','EQ',0)
		->select();
		// var_dump($res);
		// var_dump($this->getLastSql());
		// die;

		$data = [];
		foreach ($res as $k => $v) {
			if($v['vip'] == $isVip){
				$flag = true;
				$data = $v;
				break;  
			}
			
		}
		if($data){
			return $data;
		}else{
			return false;
		}		
	}

	
	/**
	 * [modifyPassByMobile //根据手机号修改密码]
	 * @Author  zzq
	 * @DateTime 2018-05-07
	 * @param    [string]                   $mobile [手机号]
	 * @param    [string]                   $newPassword [新密码]
	 * @return   [array]                           [返回信息]
	 */
	public function modifyPassByMobile($mobile,$newPassword,$isVip){
		$ret = [];
		$ret = [
			'mobile'=>$mobile,
			'password'=>$newPassword
		];
		//查询mobile对应的主键id
		$res = $this->hasMobile($mobile,$isVip);
		$id = $res['id'];
		$validate = new Validate($this->modifyPassRule, $this->modifyPassMessage);
		if( !$validate->check($ret) ){
			return return_format('',50010,$validate->getError());
		}else{
			$cryptstr = new Authorize;
			$cryptarr = $cryptstr->createUserMark($newPassword);
			$data = [];
			$data['password']  = $cryptarr['password'] ;
			$data['mix']       = $cryptarr['mix'] ;
			try{
				$this->save($data,['id'=>$id]);
				return return_format('',0,lang('success')); 
			}catch(\Exception $e){
				return return_format($e->getMessage(),50028,lang('50028'));
			}
		}
	}

}
