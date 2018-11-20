<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use login\Authorize;
use Messages;
class Teacherinfo extends Model
{	

	protected $pk    = 'teacherid';
	protected $table = 'nm_teacherinfo';
    protected $organid;
	protected $rule = [
			'nickname' => 'require|max:30',
			'sex' => 'number|between:0,2',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'mobile.require'   => lang('40103'),
            'mobile.max'       => lang('40104'),
            'nickname.require' => lang('40105'),
            'nickname.max'     => lang('40106'),
            'sex.number'       => lang('40107'),
            'sex.between'      => lang('40108'),
        ];
    }
	/**
	 * 从数据库获取
	 * @Author wyx
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getTeacherList($where,$field,$limitstr,$order='teacherid asc')
    {
        // var_dump($order);
        return Db::table($this->table)->where($where)->field($field)->limit($limitstr)->order($order)->select();
    }
    /**
     * @Author wyx
     * @param $where    array       必填
     * @param $order    string      必填
     * @param $limitstr string      必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     *
     */   
    public function getTeacherListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 教师的详细信息
	 * @Author wyx
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $teachid 教师表teacherid
	 * @param    [int]     $organid   [机构标识]
	 * @return   array                [description]
	 */
    public function getTeacherData($field,$teachid)
    {
        return Db::table($this->table)
        ->where('teacherid',$teachid)
        ->field($field)->find();
    }
	
    /**
	 * 根据teacherid获取 教师最近登陆时间
	 * @Author wyx
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $allacountid 教师所在总表的 id 整型 或 数组
	 * @return   array                   [description]
	 */
    /* public function getLoginTime($allacountid)
    {   
        if( is_array($allacountid) ){//如果是一个数组批量查询
           return Db::table('nm_organlogin')->where('teachid','IN',$allacountid)->column('allacountid,logintime');
        }else{// 
    	   return Db::table('nm_organlogin')->where(['allacountid'=>$allacountid])->column('logintime');

        }
    } */
    /**
     * [addTeacher 添加教师数据]
     * @Author wyx
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addTeacher($data){
  //   	$validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		if( empty($data['nickname']) || empty($data['mobile'])  ){
			return return_format('',40099);
		}else{
            // 检测手机号是否已经存在
            $checkflag = Db::table('nm_teacherinfo')->where([ 'mobile'=>$data['mobile'], 'delflag'=>1 ])->field('teacherid')->find();
            if( !empty($checkflag) ) return return_format('',40100);
			// 启动事务
			Db::startTrans();
			try{
				//获取 当前最大id
				$maxid = $this->max('teacherid');
				$maxid = empty($maxid) ? 1 : ++$maxid ;
				$data['sortnum'] = $maxid ;
				//生日转换为 时间戳
				// $arr = explode('-',$data['birth']) ;
				// $data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;

				// 生成首字母
				$data['initials'] = get_initial($data['nickname']);

				//入库
				$return = $this->allowField(true)->save($data);
				//新增的教师id
				$teacherid = $this->teacherid ;

                // 密码处理
                $cryptdeal = new Authorize;
                $cryptarr = $cryptdeal->createUserMark($data['password']);

				$alldata = [
						'uid' => $teacherid ,
						'usertype' => 1 ,
						//'username' => $data['teachername'] ,
						'mobile'   => $data['mobile'] ,//手机号为空
						'addtime'  => time() ,
                        'password' => $cryptarr['password'] ,
                        'mix'      => $cryptarr['mix'] ,
                        'prephone'      => $data['prphone'] ,
					] ;
				$logflag = Db::table('nm_allaccount')->insert($alldata);
				
				// 提交事务
                if($return>0 && $logflag){
				    Db::commit();
                    // 发送短信 通知密码
                    //$organinfo = Db::table('nm_organ')->where(['id'=>$data['organid']])->field('organname')->find();
                   // $organname = empty($organinfo) ? '' : $organinfo['organname'] ;

                    // $obj = new Messages();
                    // $res = $obj->sendMeg($data['mobile'],1,$params = [$organname,$data['password']]);// 模板为 1 机构名称  初始密码
                    //新增新增新增 用户成功后 给用户添加默认角色
					foreach(explode(',', $data['roleid']) as $v){
						$roleid = $v == 1 ? 1 : 5;
						$cryptdeal->addUserDefaultAcl($teacherid,$roleid);
					}
                    return return_format($return,0);
                }else{
                    Db::rollback();
				    return return_format($return,40101);
                }


			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return return_format([],40102);
			}
		}
    }
    /**
     * [updateTeacher 添加教师数据]
     * @Author wyx
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateTeacher($data){
        // 检测账号 在机构下是否已经存在
        if( !empty($data['mobile']) ){// 如果更新老师账号名字  则判断是否已经存在,'teacherid'=>$data['teacherid']
            $checkflag = Db::table('nm_teacherinfo')->where([ 'mobile'=>$data['mobile'], 'delflag'=>1 ])
                ->where('teacherid','neq',$data['teacherid'])->field('teacherid')
                ->find();
            if( !empty($checkflag) ) return return_format('',40109);
        }

    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',40110,$validate->getError());
		}else{
            if($data['teacherid']>0){
                Db::startTrans();
                try{
                    // 生成首字母
                    $data['initials'] = get_initial($data['nickname']);

				    $return = $this->allowField(true)->save($data,['teacherid'=>$data['teacherid']]);
					$teacherid = $this->teacherid ;
                    
                    $where = ['uid'=>$teacherid ,'usertype'=>1];
                    $allaccountdata  = [
						'mobile' => $data['mobile'],
						'prephone' => $data['prphone'],
                    ] ;

                    Db::table('nm_allaccount')->where($where)->update($allaccountdata);
					
					//删除原有角色组
					Db::table('nm_accessroleuser')->where(['uid'=>$data['teacherid'], 'usertype'=>1])->delete();
					
					//添加新的角色组
					$cryptdeal = new Authorize;
					foreach(explode(',', $data['roleid']) as $v){
						$roleid = $v == 1 ? 1 : 5;
						$cryptdeal->addUserDefaultAcl($data['teacherid'],$roleid);
					}
					
                    // 提交事务
                     Db::commit();
				    return return_format($return,0);
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return return_format($return,40111);
                }
			}else{
				return return_format('',40112);
			}
		}
    }
	
    /**
     * [switchTeachStatus 修改教师的可用状态]
     * @Author wyx
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]       $teacherid [教师id]
     * @param    [int]       $dataflag  [要修改的标记状态]
     * @return   [array]                [返回数组]
     */
    public function switchTeachStatus($teacherid,$dataflag){
        
        $allwhere['uid'] = $teacherid ;
        $allwhere['usertype'] = 1 ;
        $alldata['status'] = $dataflag ;
        Db::startTrans();
        try{
            // 修改登录中的状态
            Db::table('nm_allaccount')->where($allwhere)->update($alldata) ;

            $data['accountstatus']= $dataflag ;
            if( $dataflag==1 ) $data['recommend'] = 0 ;// 如果将老师禁用 则关闭推荐
            $where = ['teacherid'=>$teacherid] ;

            Db::table($this->table)->where($where)->update($data) ;
            Db::commit();
            return return_format('',0);
        }catch(\Exception $e){
            Db::rollback();
            return return_format('',40114);
        }


    }
    /**
     * [delTeacher 删除教师信息]
     * @Author wyx
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $teacherid [教师id]
     * @return   [type]               [description]
     */
    public function delTeacher($teacherid){
    	$data  = ['delflag'=>0 ,'recommend'=> 0 ] ;
    	$where = ['teacherid'=>$teacherid] ;
        
        Db::startTrans();
        try{
            Db::table($this->table)->where($where)->update($data) ;

            //同时 修改allaccount 表中用户的禁用标志，将禁止登陆
            $allwhere = ['uid'=>$teacherid ,'usertype'=>1 ];//类型1 为老师
            $allaccountdata  = [
                        'status'  => 2,// 删除老师后 登录表 改为2 状态代表删除
                    ] ;
            Db::table('nm_allaccount')->where($allwhere)->update($allaccountdata) ;
            // 提交事务
            Db::commit();
            return return_format('',0);
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            return return_format('',40116);
        }
    }
    /**
     * [getTeachernameByIds 订单表分页后获取教师名称]
     * @Author
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [array]          $arr [教师ids]
     * @return   [type]               [description]
     */
    public function getTeachernameByIds($arr){
    	return Db::table($this->table)->where('teacherid','IN',$arr)
    	->column('teacherid,nickname');
    }
	/**
	 * [exchangeSort 根据两个id来交换sortnum值]
	 * @Author wyx
	 * @DateTime 2018-04-21T19:00:12+0800
	 * @param  $ids       要交换的id数组
	 * @return   [type]       [description]
	 */
	public function exchangeSort($ids)
    {	
    	$where['teacherid'] = ['in',$ids] ;
    	$arr = Db::table($this->table)->field('teacherid,sortnum')->where($where)->select();
        if(count($arr)==2){
	    	Db::table($this->table)->where(['teacherid'=>$arr[0]['teacherid']])->update(['sortnum'=>$arr[1]['sortnum']]);
	    	Db::table($this->table)->where(['teacherid'=>$arr[1]['teacherid']])->update(['sortnum'=>$arr[0]['sortnum']]);
    		return return_format('',0);
    	}else{
    		return return_format('',40052);
    	}
       
	}
    /**
     * [setRecommendFlag 设置教师推荐位]
     * @Author
     * @DateTime 2018-04-21T19:57:10+0800
     * @param    [int]          $organid   [机构标记id]
     * @param    [int]          $teacherid [教师标记id]
     * @param    [int]          $status    [设置标记值]
     */
    public function setRecommendFlag($teacherid,$status){
    	$data  = ['recommend'=>$status] ;
    	$where = ['teacherid'=>$teacherid] ;
    	return $this->allowField(true)->save($data,$where);
        // var_dump($where);
    }
    /**
     * [addTeacherImage 设置教师推荐位]
     * @Author wyx
     * @DateTime 2018-05-08T20:12:10+0800
     * @param    [array]         $data   [添加数据]
     * @param    [int]           $organid [机构标记]
     */
    public function addTeacherImage($data){

        $updatedata  = [
                    'identphoto'=>$data['image'],
                    'slogan'    =>$data['profile'],
                    ] ;
        $where = ['teacherid'=>$data['teacherid']] ;
        return Db::table($this->table)->where($where)->update($updatedata);
    }
    /**
     * [getTeacherAllAccount 获取机构的教师总数]
     * @Author wyx
     * @param    [int]           $organid [机构标记]
     */
    public function getTeacherAllAccount(){

        return Db::table($this->table)
        ->where('delflag','EQ',1)
        ->count();
    }
    


    /**
     * 根据老师id 获取老师对应详细信息
     * @php jcr
     * $id 教师id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getTeacherId($id,$field){
        return Db::table($this->table)->where('teacherid','eq',$id)->field($field)->find();
    }


    /**
     * [getTeachlist 查询老师列表]
     * @param  [type] $data    [查询条件]
     * @param  [type] $pagenum [第几页]
     * @param  [type] $limit   [一页几条]
     * @return [type]          [description]
     */
    public function getTeachlist($where,$pagenum,$limit,$order = ''){
        $field = 'teacherid,nickname,mobile,teachername';
        return Db::table($this->table)->where($where)->page($pagenum,$limit)->field($field)->order($order)->select();
    }



    /**
     * [getTeachCount 获取机构对应老师总行数]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getTeachCount($where){
        return Db::table($this->table)->where($where)->count();
    }


    /**
     * 获取 开课 老师列表
     * @Author jcr
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getLists($ids)
    {
    	$where = ['accountstatus'=>0,'delflag'=>1];
		if($ids){
			$where['teacherid'] = ['in',$ids];
		}

        return Db::table($this->table)->where($where)
                                      ->field('teacherid,teachername,nickname,accountstatus')
                                      ->limit(1000)->select();
    }


	/**
	 * [getOrganTeachList 获取对应机构下未删除的所有老师]
	 * @Author JCR
	 * @param  $organid
	 * @return array()
	 */
    public function getOrganTeachList($organid){
		$field = 't.*,a.usertype,a.username,a.mobile,a.status,a.password,a.mix';
		$list = Db::table($this->table)->alias('t')
									   ->join('nm_allaccount a','t.teacherid = a.uid and a.usertype = 1')
									   ->where(['t.organid'=>$organid,'t.delflag'=>1])
									   ->field($field)->select();
		return $list;
	}


	/**
	 * [getOrganTeachList 查询新插的老师数据做插入比对]
	 * @Author JCR
	 * @param  $organid
	 * @return array()
	 */
	public function getOrganTeachId($organid){
		return Db::table($this->table)
				->where(['organid'=>$organid])
				->field('teacherid,copyid')->select();
	}




	/**
	 * [allAddTeacher 复制老师 添加]
	 * @author JCR
	 * @param $teachArr	 处理teacherinfo
	 * @param $allTeach  处理nm_allaccount
	 * @param $timelist  可预约时间
	 * @param $teachertime
	 * @return bool;
	 */
	public function allAddTeacher($teachArr,$allTeach,$timelist,$teachertime){
		$teacher = new Teachertime();
		Db::startTrans();

		$id = Db::table($this->table)->insertGetId($teachArr);
		// 添加teacherinfo失败回滚
		if(!$id){
			Db::rollback();
			return false;
		}

		$allTeach['uid'] = $id;
		$allid = Db::table('nm_allaccount')->insert($allTeach);
		// 添加allaccount失败回滚
		if(!$allid) {
			Db::rollback();
			return false;
		}

		if($timelist){
			foreach ($timelist as $key => &$val){
				$val['organid'] = $teachArr['organid'];
				$val['teacherid'] = $id;
				unset($val['id']);
			}
			if(!$teachertime->addAll($timelist)){
				Db::rollback();
				return false;
			}
		}

		Db::commit();
		return true;
	}


    /*
    * 根据机构id获取个体教师id
    * @Author wangwy
    *
    */
    public function getSingleid($organid){
       return  Db::table($this->table)
               ->where('organid','eq',$organid)
               ->field('teacherid')
               ->find();
    }
	
	/**
	 * [ImportTeachers 批量导入老师]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $datas    [传递过来的excel数据]
	 * @return   [array]
	 */
	public function importTeachers($datas){
		    Db::startTrans();
			try{
				$cryptdeal = new Authorize;
				$c = 0;
				foreach($datas as $data){
					$checkflag = Db::table('nm_teacherinfo')->where([ 'mobile'=>$data['mobile'], 'delflag'=>1 ])->field('teacherid')->find();
					if( !empty($checkflag) ) continue;
					
					$maxid = $this->max('teacherid');
					$maxid = empty($maxid) ? 1 : ++$maxid ;
					$data['sortnum'] = $maxid ;

					$data['initials'] = get_initial($data['nickname']);
					
					$tdata = [
							'nickname' => $data['nickname'],
							'initials' => $data['initials'],
							'mobile'   => $data['mobile'],
							'addtime'  => $data['addtime'],
							'school' => $data['school'],
							'grade'      => $data['grade'],
							'class'      => $data['class'],
							'prphone'      => $data['prphone'],
							'sortnum'      => $data['sortnum'],
						];
					$teacherid = Db::table($this->table)->insertGetId($tdata);
					$cryptarr = $cryptdeal->createUserMark($data['password']);
					$alldata = [
							'uid' => $teacherid ,
							'usertype' => 1 ,
							'mobile'   => $data['mobile'] ,
							'addtime'  => $data['addtime'] ,
							'password' => $cryptarr['password'] ,
							'mix'      => $cryptarr['mix'] ,
							'prephone'      => $data['prphone'] ,
						] ;
					$logflag = Db::table('nm_allaccount')->insert($alldata);
					
					// 提交事务
					if($teacherid>0 && $logflag){
						Db::commit();
						foreach(explode(',', $data['roleid']) as $v){
							$roleid = $v == 1 ? 1 : 5;
							$cryptdeal->addUserDefaultAcl($teacherid,$roleid);
						}
						$c++;	
					}else{
						Db::rollback();
						//return return_format($return,40101);
					}
				}
				return return_format($c, 0);
			} catch (\Exception $e) {
				Db::rollback();
				return return_format([],40102);
			}
    }
}
