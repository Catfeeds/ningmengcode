<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
// use app\admin\model\Teacherinfo;
// use app\official\model\Teacherinfo;
use app\admin\model\Organslideimg;
use app\admin\model\Organconfig;
use login\Authorize;
use app\official\model\Organslideimg as Officialslideimg;
use app\official\model\Organconfig as Officialconfig;
class Allaccount extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_allaccount';

	protected $rule = [
			'username' => 'require|max:30',
			'mobile'   => 'require|max:25',
			'groupids'	=> 'require',
			'useraccount'=> 'require' ,
			'password'   => 'min:6',
			'repassword' => 'confirm:password',
		];
	protected $message = [
			'username.require' => '员工姓名必须填写',
			'username.max' => '员工姓名不能超过30个字符',
			'mobile.require' => '手机号必须填写',
			'mobile.max' => '名称最多不能超过25个字符',
			'groupids.require' => '请选择分组',
			'useraccount.require' => '账户名必须填写',
			'password.min' => '密码长度不能少于6个',
			'repassword.confirm' => '两次密码必须一致',
		];

	protected $addRule = [
			//'username' => 'require|max:30',
			'mobile'   => 'require|max:25',
			'useraccount'=> 'require|max:16' ,
			'password'   => 'require|min:6|max:16',
			//'repassword' => 'require|confirm:password',
		];
	protected $addMessage = [];
	protected $modifyPassRule = [
			//'username' => 'require|max:30',
			'mobile'   => 'require|max:25',
			'password'   => 'require|min:6|max:16',
		];
	protected $modifyPassMessage = [];
	protected function initialize() {
		parent::initialize();
		$this->addMessage = [
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
	 * [getAdminList 获取机构添加的管理员列表]
	 * @Author wyx
	 * @DateTime 2018-04-23T20:39:48+0800
	 * @param    [array]                   $where    [过滤条件]
	 * @param    [string]                  $limitstr [分页信息]
	 * @return   [array]                             [结果集]
	 */
	public function getAdminList($where,$limitstr){
		$field = 'aa.uid,aa.username,aa.mobile,aa.addtime,aa.status,am.adminname,am.logintime,am.info,am.groupids' ;
		
		return Db::table($this->table)
				->alias(['nm_allaccount'=>'aa','nm_adminmember'=>'am'])
				->join('nm_adminmember','aa.uid=am.id','LEFT')
				->field($field)
				->where($where)
				->limit($limitstr)
				->select() ;
	}
	/**
	 * [getAdminList 获取机构添加的管理员列表]
	 * @Author wyx
	 * @DateTime 2018-04-23T20:39:48+0800
	 * @param    [array]                   $where    [过滤条件]
	 * @param    [string]                  $limitstr [分页信息]
	 * @return   [array]                             [结果集]
	 */
	public function getAdminListCount($where){
		
		return Db::table($this->table)
				->alias(['nm_allaccount'=>'aa','nm_adminmember'=>'am'])
				->join('nm_adminmember','aa.uid=am.id','LEFT')
				->where($where)
				->count() ;
	}
	/**
	 * [getAdminUser 获取机构添加的管理员信息]
	 * @Author wyx
	 * @DateTime 2018-04-24T09:49:48+0800
	 * @param    [int]                   $adminid [机构管理员id]
	 * @param    [int]                   $organid [组织识别码]
	 * @return   [array]                          [结果集]
	 */
	public function getAdminUser($adminid){
		$field = 'aa.uid,aa.username,aa.mobile,aa.addtime,aa.status,am.adminname,am.logintime,am.userimage,am.info,am.groupids' ;

		$info = Db::table($this->table)
				->alias(['nm_allaccount'=>'aa','nm_adminmember'=>'am'])
				->join('nm_adminmember','aa.uid=am.id','LEFT')
				->field($field)
				->where('aa.uid','EQ',$adminid)
				->where('aa.usertype','EQ',2)// 代表机构添加的管理员
				->find() ;
		return $info;

	}
	/**
	 * [updatePass 获取机构添加的管理员信息]
	 * @Author wyx
	 * @DateTime 2018-04-24T09:49:48+0800
	 * @param    [string]                $pass [组织识别码]
	 * @param    [int]                   $uid [当前登录用户的uid]
	 * @param    [int]                   $organid [组织识别码]
	 * @return   [array]                          [结果集]
	 */
	public function updatePass($pass,$uid){

		$cryptstr = new Authorize;
		$cryptarr = $cryptstr->createUserMark($pass);
		$data['password']  = $cryptarr['password'] ;
		$data['mix']  = $cryptarr['mix'] ;

		return Db::table($this->table)
				->where('uid','EQ',$uid)
				->update($data) ;
	}
	/**
	 * [updateAdminUser 更新添加的管理员信息]
	 * @Author wyx
	 * @DateTime 2018-05-22
	 * @param    [int]                   $uid [当前登录用户]
	 * @param    [string]                $oldphone [旧手机号]
	 * @param    [string]                $newphone [新手机号]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function changeMobile($uid,$oldphone,$newphone){
		
		Db::startTrans() ;
		try{
			$admindata['mobile']      = $newphone ;

			$mkadmin = Db::table('nm_adminmember')
			->where(['id'=>$uid,'mobile'=>$oldphone])
			->update($admindata);

			$accountdata['mobile']   = $newphone ;

			$mkall = Db::table($this->table)
			->where(['uid'=>$uid,'mobile'=>$oldphone])
			->update($accountdata);
			// 提交事务
			if($mkadmin>0 && $mkall>0 ){
				Db::commit();
				return return_format('',0);
			}else{
				Db::rollback();
				return return_format('',40020);
			}
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return return_format('',40021);
		}

	}
	/**
	 * [updateAdminUser 更新添加的管理员信息]
	 * @Author wyx
	 * @DateTime 2018-04-24T11:17:55+0800
	 * @param    [array]                 $data [需要更新的数据]
	 * @param    [int]                   $adminid [要更新的管理员id]
	 * @param    [int]                   $organid [机构标识id]
	 * @return   [array]                          [description]
	 */
	public function updateAdminUser($data,$adminid){
		$validate = new Validate($this->rule, $this->message);
		if( !$validate->check($data) ){
			return return_format('',-1,$validate->getError());
		}else{
			Db::startTrans() ;
			try{
				//获取 当前最大id
				$currenttime = time() ;
				$admindata['useraccount'] = $data['useraccount'] ;
				$admindata['adminname']   = $data['username'] ;
				$admindata['mobile']      = $data['mobile'] ;
				$admindata['groupids']	= $data['groupids'];
				!empty($data['info']) ? $admindata['info'] = $data['info'] : '' ;

				Db::table('nm_adminmember')
				->where(['id'=>$adminid])
				->update($admindata);

				$accountdata['username'] = $data['useraccount'] ;
				$accountdata['mobile']   = $data['mobile'] ;
				//密码处理
				if(!empty($data['password'])){
					$cryptstr = new Authorize;
					$cryptarr = $cryptstr->createUserMark($data['password']);
					$accountdata['password']  = $cryptarr['password'] ;
					$accountdata['mix']  = $cryptarr['mix'] ;
				} 

				Db::table($this->table)
				->where(['uid'=>$adminid,'usertype'=>array('in','0,2')])
				->update($accountdata);
				// 提交事务
				Db::commit();
				return return_format('',0);
			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return return_format('',40032);
			}

		}
	}
	/**
	 * [addAdminUser 添加机构管理账号]
	 * @Author wyx
	 * @DateTime 2018-04-24T14:42:41+0800
	 * @param    [array]                 $data    [要添加的信息]
	 * @param    [int]                   $organid [组织机构标识id]
	 */
	public function addAdminUser($data,$usertype=2){
		$validate = new Validate($this->rule, $this->message);
		if( !$validate->check($data) ){
			return return_format('',40034,$validate->getError());
		}else{
			if(!preg_match('/^[A-Za-z0-9]+$/', $data['useraccount'])){
				return return_format([],40500,lang('40500'));
			}
			$adminmerder = new Adminmember();
			$counts = $adminmerder->getCount(['useraccount'=>$data['useraccount']]);
			if($counts>0){
				return return_format([],40501,lang('40501'));
			}

			Db::startTrans() ;
			try{
				//获取 当前最大id
				$currenttime = time() ;
				$admindata['useraccount'] = $data['useraccount'] ;
				$admindata['adminname']   = $data['username'] ;
				$admindata['mobile']      = $data['mobile'] ;
				$admindata['logintime']   = $currenttime ;
				$admindata['groupids'] = $data['groupids'];
				!empty($data['info']) ? $admindata['info'] = $data['info'] : '' ;

				$uid = Db::table('nm_adminmember')->insertGetId($admindata);

				$accountdata['uid'] = $uid ;
				$accountdata['usertype'] = $usertype ;
				$accountdata['username'] = $data['useraccount'] ;
				$accountdata['mobile']   = $data['mobile'] ;
				$accountdata['addtime']  = $currenttime ;

				//
				$cryptstr = new Authorize;
				$cryptarr = $cryptstr->createUserMark($data['password']);
				$accountdata['password']  = $cryptarr['password'] ;
				$accountdata['mix']       = $cryptarr['mix'] ;

				Db::table($this->table)->insert($accountdata);
				// 提交事务
				Db::commit();
				//新增新增新增 用户成功后 给用户添加默认角色
				$cryptstr->addUserDefaultAcl($uid,$usertype);

				return return_format([],0);
			} catch (\Exception $e) {
				//回滚事务
				Db::rollback();
				return return_format([],40035);
			}

		}
	}
	/**
	 * [delAdminUser 删除机构添加的管理员]
	 * @Author wyx  不能删除超管 不能删除自己
	 * @DateTime 2018-04-24T15:41:21+0800
	 * @param    [int]                   $adminid [管理员id]
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                          [description]
	 */
	public function delAdminUser($adminid){
		Db::startTrans() ;
		try{
			$delflagmsg = Db::table('nm_adminmember')
			->where(['id'=>$adminid])
			->delete();

			$delflag = Db::table($this->table)
			->where(['uid'=>$adminid,'usertype'=>['eq',2] ] )
			->delete();
			// 提交事务
			if($delflag && $delflagmsg){
				Db::commit();
				return return_format([],0);
			}else{
				Db::rollback();
				return return_format([],40038);
			}
		} catch (\Exception $e) {
			//回滚事务
			Db::rollback();
			return return_format([],40039);
		}
	}
	/**
	 * [switchAdminFlag 切换用户的可用状态]
	 * @Author wyx
	 * @DateTime 2018-04-24T15:59:23+0800
	 * @param    [int]                   $flag    [转换为的标记]
	 * @param    [int]                   $adminid [要改变的管理员的id]
	 * @param    [int]                   $organid [及机构标识id]
	 * @return   [array]                            [description]
	 */
	public function switchAdminFlag($flag,$adminid){
		$flag = Db::table($this->table)
		->where('uid','EQ',$adminid)
		->where('usertype','EQ',2)
		->update(['status'=>$flag]);
		return return_format($flag,0);
	}


	/**
	 * [resOrganAddAdminUser 注册机构添加超级管理员]
	 * @Author wyx
	 * @DateTime 2018-04-24T14:42:41+0800
	 * @param    [array]                 $data    [要添加的信息]
	 * @param    [int]                   $organid [组织机构标识id]
	 */
	public function resOrganAddAdminUser($data,$usertype=2){
		// var_dump($data);
		// die;

		$validate = new Validate($this->addRule, $this->addMessage);
		if( !$validate->check($data) ){
			// var_dump($validate->check($data));
			// die;
			return return_format('',50010,$validate->getError());
		}else{
			//var_dump($data);exit();
			//die;
			Db::startTrans() ;
			try{
				//查询最大的organ表的主键
				$maxId = Db::table('nm_organ')->max('id');
				//添加排序sort
				$sort = (int)$maxId + 1;
				//先生成机构
		        $organData = [
		            'organname'=>'',
		            'profile'=>'',
		            'imageurl'=>'',
		            'hotline'=>'',
		            'email'=>'',
		            'info'=>'',
		            'domain'=>'',
		            'sort'=>$sort,
		            'restype'=>$data['restype'],
		            'vip'=>$data['vip']
		        ];
		        $organid = Db::table('nm_organ')->insertGetId($organData);
		        if(!$organid){
		        	return return_format('',50061,lang('50061'));
		        }
		        // var_dump($res);
		        // die;

				//获取 当前最大id
				$currenttime = time() ;
				$admindata['useraccount'] = $data['useraccount'] ;
				$admindata['adminname']   = $data['useraccount'] ;
				$admindata['mobile']      = $data['mobile'] ;
				$admindata['logintime']   = $currenttime ;
				!empty($data['info']) ? $admindata['info'] = $data['info'] : '' ;
				$uid = Db::table('nm_adminmember')->insertGetId($admindata);

		        if(!$uid){
		        	return return_format('',50062,lang('50062'));
		        }

				$accountdata['uid'] = $uid ;
				$accountdata['usertype'] = $usertype ;
				$accountdata['username'] = $data['useraccount'] ;
				$accountdata['mobile']   = $data['mobile'] ;
				$accountdata['addtime']  = $currenttime ;
				$accountdata['vip']  = $data['vip'] ;

				//
				$cryptstr = new Authorize;
				$cryptarr = $cryptstr->createUserMark($data['password']);
				$accountdata['password']  = $cryptarr['password'] ;
				$accountdata['mix']       = $cryptarr['mix'] ;

				$accountid = Db::table($this->table)->insertGetId($accountdata);

		        if(!$accountid){
		        	return return_format('',50062,lang('50062'));
		        }

				//插入角色表
				$roleData = [
					'roleid'=>1,
					'uid'=>$uid,
					'usertype'=>0
				];
				$roleUserId = Db::table('nm_accessroleuser')->insertGetId($roleData);
				if(!$roleUserId){
					return return_format('',50065,lang('50065'));
				}
				if($data['restype'] == 1){
					//添加教师
					
					$myTeacher = [];
					$myTeacher['mobile'] = $data['mobile'];
					$myTeacher['prphone'] = '+86';
					$myTeacher['addtime'] = time();
					$myTeacher['teachername'] = $data['useraccount'];
					$myTeacher['nickname'] = $data['useraccount'];
					$myTeacher['accountstatus'] = 1;
					$myTeacher['recommend'] = 0;
					$myTeacher['delflag'] = 1;
					// var_dump($myTeacher);
					// die;
					$teacherRes = Db::table('nm_teacherinfo')->insertGetId($myTeacher);
			        if(!$teacherRes){
			        	return return_format('',50063,lang('50063'));
			        }
				}

				//机构账户表自动添加一个记录
				$organAccount = [
					'tradeflow'=>'0.00',
					'usablemoney'=>'0.00',
					'frozenmoney'=>'0.00',
					'organid'=>$organid,
				];

				$organAccountId =  Db::table('nm_organaccount')->insertGetId($organAccount);
				if(!$organAccountId){
					return return_format('',50064,lang('50064'));
				}

				$returnData = [
					'organid'=>$organid,
					'adminid'=>$uid,
					'accountid'=>$accountid,
					'restype'=>$data['restype'],
					'usertype'=>0,
					'logintime'=>$currenttime,
					'useraccount'=>$data['useraccount'],
					'adminname'=>'',
					'userimage'=>'',
					'mobile'=>$data['mobile']
					];
				if($data['restype'] == 1){
					$returnData['teachid'] = $teacherRes;
				}

				//复制轮播图
				$slideobj = new Officialslideimg();
				$officialRes = $slideobj->getOrganSlideImgList();
				//判断官方有没有配置
				if(!$officialRes){
					return return_format('',50066,lang('50066'));
				}
		        $officialRet = [];
		        // var_dump($officialRes['data']);
		        // die;
		        if($officialRes['code'] == 0){
			        foreach($officialRes['data'] as $k => $v){
			            $officialRet[$k]['remark'] = $v['remark']; 
			            $officialRet[$k]['imagepath'] = $v['imagepath']; 
			            $officialRet[$k]['sortid'] = $v['sortid'];
			            $officialRet[$k]['addtime'] = time(); 
			            $officialRet[$k]['organid'] = $organid;  
			        }
			        // var_dump($officialRet);
			        // die;
			        //循环插入
	                Db::table('nm_organslideimg')->insertAll($officialRet);		        	
		        }

				//复制课堂配置
				$configobj = new Officialconfig();
				$configArr = $configobj->getOrganClassConfig(0);
				//判断官方有没有配置
				if(!$configArr){
					return return_format('',50067,lang('50067'));
				}
				if($configArr['code'] == 0){
					$configRet  = $configArr['data'];  //数组里边
					$configData = [];
					if($configRet){
						$configData['toonetime'] = $configRet['toonetime'];
						$configData['smallclasstime'] = $configRet['smallclasstime'];
						$configData['bigclasstime'] = $configRet['bigclasstime'];
						$configData['regionprefix'] = '';
						$configData['maxclass'] = $configRet['maxclass'];
						$configData['minclass'] = $configRet['minclass'];
						$configData['organid'] = $organid;
						$configData['roomkey'] = $configRet['roomkey'];  //如何生成的
						Db::table('nm_organconfig')->insert($configData);	
					}					
				}
//				$slideobj = new Officialslideimg();
//				$officialRes = $slideobj->getOrganSlideImgList();
//				//判断官方有没有配置
//				if(!$officialRes){
//					return return_format('',50066,lang('50066'));
//				}
//		        $officialRet = [];
//		        // var_dump($officialRes['data']);
//		        // die;
//		        if($officialRes['code'] == 0){
//			        foreach($officialRes['data'] as $k => $v){
//			            $officialRet[$k]['remark'] = $v['remark'];
//			            $officialRet[$k]['imagepath'] = $v['imagepath'];
//			            $officialRet[$k]['sortid'] = $v['sortid'];
//			            $officialRet[$k]['addtime'] = time();
//			            $officialRet[$k]['organid'] = $organid;
//			        }
//			        // var_dump($officialRet);
//			        // die;
//			        //循环插入
//	                Db::table('nm_organslideimg')->insertAll($officialRet);
//		        }

				//复制课堂配置
//				$configobj = new Officialconfig();
//				$configArr = $configobj->getOrganClassConfig(0);
//				//判断官方有没有配置
//				if(!$configArr){
//					return return_format('',50067,lang('50067'));
//				}
//				if($configArr['code'] == 0){
//					$configRet  = $configArr['data'];  //数组里边
//					$configData = [];
//					if($configRet){
//						$configData['toonetime'] = $configRet['toonetime'];
//						$configData['smallclasstime'] = $configRet['smallclasstime'];
//						$configData['bigclasstime'] = $configRet['bigclasstime'];
//						$configData['regionprefix'] = '';
//						$configData['maxclass'] = $configRet['maxclass'];
//						$configData['minclass'] = $configRet['minclass'];
//						$configData['organid'] = $organid;
//						$configData['roomkey'] = $configRet['roomkey'];  //如何生成的
//						Db::table('nm_organconfig')->insert($configData);
//					}
//				}
				// 提交事务
				Db::commit();
				return return_format($returnData,0,lang('success'));
			} catch (\Exception $e) {
				//回滚事务
				Db::rollback();
				return return_format($e->getMessage(),50038,lang('50038'));
			}

		}
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
	 * @param    [int]                   $isVip [0表示免费 1表示vip]
	 * @return   [array]                           [获取该机构的注册人信息]
	 */
	public function hasMobile($mobile,$isVip){
		$res = Db::table($this->table)
		->alias('a')
		->join('nm_organ b','a.organid = b.id')
		->field('a.id,a.mobile,a.organid,b.vip')
		->where('a.mobile','EQ',$mobile)
		->where('b.vip','EQ',$isVip)
		->find();
		if($res){
			return $res;
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
	 * @param    [int]                   $isVip [0表示免费,1表示付费]
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

	/**
	 * [updatePassword 机构修改老师密码]
	 * @param $password 密码
	 * @param $organid  机构ID
	 * @param $uid		老师ID
	 */
	public function updatePassword($password,$uid){
		// 密码处理
		$cryptdeal = new Authorize;
		$cryptarr = $cryptdeal->createUserMark($password);
		$where = [
			'uid'	  =>$uid,
			'usertype'=>1
		];
		return Db::table($this->table)->where($where)->update(['password'=>$cryptarr['password'],'mix'=>$cryptarr['mix']]);

	}
}
