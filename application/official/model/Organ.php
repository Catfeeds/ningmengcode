<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
class Organ extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organ';
	protected $rule = [
			'organname' => 'require|max:200',
			'profile' => 'require|max:30',
			'info' => 'require|max:500',
			'imageurl' => 'require|max:500',
			'hotline' => 'require|max:50',
			'email' => 'require|max:50',
		];
	protected $message = [];
	protected $addOrUpdateRule = [
			'organname' => 'require|max:200',
			'domain' => 'require|max:50',
		];
	protected $addOrUpdateMessage = [];
	protected function initialize() {
		parent::initialize();
		$this->message = [
			'organname.require' => lang('50253'),
			'organname.max' => lang('50254'),
			'profile.require' => lang('50255'),
			'profile.max' => lang('50256'),
			'info.require' => lang('50257'),
			'info.max' => lang('50258'),
			'imageurl.require' => lang('50259'),
			'imageurl.max' => lang('50260'),
			'hotline.require' => lang('50261'),
			'hotline.max' => lang('50262'),
			'email.require' => lang('50263'),
			'email.max' => lang('50264'),
		];
		$this->addOrUpdateMessage = [
			'organname.require' => lang('50253'),
			'organname.max' => lang('50254'),
			'domain.require' => lang('50265'),
			'domain.max' => lang('50266'),
		];
	}
	/**
	 * [getOrganList 获取机构列表]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [int]                   $organid [description]
	 * @return   [array]                            [description]
	 */
	public function getOrganList($where,$orderbys,$pagenum,$pernum){
		// var_dump($where);
		// die;
        $orderbys = $orderbys?$orderbys : 'id desc';
        $field = 'id,organname,imageurl,domain,auditstatus,passtime';
        $pagenum = $pagenum?$pagenum:$this->pagenum;
		$lists = Db::table($this->table)->page($pagenum,$pernum)->order($orderbys)->where($where)->field($field)->select();
		// var_dump($this->getLastSql());
		// die;
		$count = $this->getOrganListCount($where);
		
		// var_dump($lists);
		// var_dump($count);
		$ret = [];
		$ret['lists'] = $lists;
		$ret['count'] = $count;
        $showPagenum = ceil($count / $pernum);
        $ret['pagenum'] = $showPagenum;
        $ret['pernum'] = $pernum;
		// var_dump($ret);
		// die;
		return $ret;
	}

	/**
	 * [getOrganList 获取机构列表]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [int]                   $organid [description]
	 * @return   [array]                            [description]
	 */
	public function getApplyVipOrganList($where,$orderbys,$pagenum,$pernum){
		// var_dump($where);
		// die;
        //获取的是其他机构的列表 organid>1代表的是注册的其他机构
        $selectWhere = [];
		if(!empty($where['organname'])){
			$selectWhere['a.organname'] = $where['organname'];
		}

		if($where['id']){
			$selectWhere['a.id'] = $where['id'];
		}

		if($where['vip']){
			$selectWhere['a.vip'] = $where['vip'];
		}

		if($where['applyvip']){
			$selectWhere['a.applyvip'] = $where['applyvip'];
		}

        $orderbys = $orderbys?$orderbys : 'id desc';
        $field = 'a.id,a.organname,b.contactname,b.contactphone,b.contactemail,a.applyviptime';
        $pagenum = $pagenum?$pagenum:$this->pagenum;
		$lists = Db::table($this->table)->alias('a')->field($field)->join('nm_organbaseinfo b','a.id=b.organid','LEFT')->where($selectWhere)->page($pagenum,$pernum)->order($orderbys)->select();
		// var_dump($this->getLastSql());
		// die;
		$count = Db::table($this->table)->alias('a')->field($field)->join('nm_organbaseinfo b','a.id=b.organid','LEFT')->where($selectWhere)->count();
		
		$ret = [];
		$ret['lists'] = $lists;
		$ret['count'] = $count;
        $showPagenum = ceil($count / $pernum);
        $ret['pagenum'] = $showPagenum;
        $ret['pernum'] = $pernum;
		// var_dump($ret);
		// die;
		return $ret;
	}


	/**
	 * [addOrgan 添加]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [array]            $data    [需要的数据]
	 * @return   [int]              $id         [机构id]
	 */
	public function addOrgan($data){
		$validate = new Validate($this->addOrUpdateRule, $this->addOrUpdateMessage);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{
			
	    	$data['auditstatus'] = 0;
            $this->save($data); 
            return $this->id;
		}
	}

	/**
	 * [updateOrgan 更新机构域名和名称]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            $data    [需要的数据]
	 * @param    [array]            $organid    [需要的id]
	 * @return   [int]              $id         [机构id]
	 */
	public function updateOrgan($data,$organid){
		$res = $this->where('id',$organid)->find();
		if(!$res){
			return return_format('',50006,lang('50006'));
		}
		$validate = new Validate($this->addOrUpdateRule, $this->addOrUpdateMessage);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',-1,$validate->getError());
		}else{

			try{
				$res = $this->save($data,['id'=>$organid]);
				return return_format($res,0,lang('success'));			
			}catch(\Exception $e){
				return return_format($e->getMessage(),50004,lang('50004'));			
			}
		}
	}

	/**
	 * [getOrganListCount 获取有条件的机构数目]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            $where    [筛选条件]
	 * @return   [int]              $count        [查询的数目]
	 */
	public function getOrganListCount($where){
		$count = Db::table($this->table)->where($where)->count();
		return $count;
	}

	/**
	 * [getAllOrganListCount 获取所有的机构数目]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            $where    [筛选条件]
	 * @return   [int]              $count        [查询的数目]
	 */
	public function getAllOrganListCount(){
		//获取未认证
		$unAuditedCount = Db::table($this->table)->where('auditstatus','=','0')->where('vip','=','1')->count();
		//获取待审核
		$inAuditedCount = Db::table($this->table)->where('auditstatus','=','1')->where('vip','=','1')->count();
		//获取已拒绝
		$refusedCount = Db::table($this->table)->where('auditstatus','=','2')->where('vip','=','1')->count();
		//获取已通过(启用和禁用)
		//并且这些机构必须是免费的
		$where = [];
		$where['auditstatus'] = ['IN',['3','4']];
		$where['vip'] = ['EQ','1'];
		$passCount = Db::table($this->table)->where($where)->count();
		$data = [];
		$data['unAuditedCount'] = $unAuditedCount ? $unAuditedCount : 0; 
		$data['inAuditedCount'] = $inAuditedCount ? $inAuditedCount : 0; 
		$data['refusedCount'] = $refusedCount ? $refusedCount : 0; 
		$data['passCount'] = $passCount ? $passCount : 0; 
		return $data;
	}


	/**
	 * [updateOrganStatus//更改organ状态]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            $where    [筛选条件]
	 * @return   [int]              $count        [查询的数目]
	 */
	public function updateOrganStatus($auditstatus,$organid){
		//如果要修改成成功的时候，要记录通过时间
		if($auditstatus == 3){
			$arr = ['auditstatus'=>$auditstatus,'passtime'=>time()];
		}else{
			$arr = ['auditstatus'=>$auditstatus];
		}
		try{
			$res = $this->save($arr,['id'=>$organid]);
			return return_format($res,0,lang('success'));			
		}catch(\Exception $e){
			return return_format($e->getMessage(),50004,lang('50004'));			
		}
	}

	/**
	 * [getOrganById 查看机构是否存在]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [int]            $organid    [organid]
	 * @return   [bool] /[array]          
	 */
	public function getOrganById($organid){
		
		$field = '*' ;
		$res =  Db::table($this->table)
				->field($field)
				->where('id','eq',$organid)
				->find();
		if($res){
			return $res;
		}else{
			return false;
		}
	}

	/**
	 * [getOrganByDomain 查看机构域名是否存在]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [string]            $domain 
	 * @return   [bool] /[array]          
	 */
	public function getOrganByDomain($domain){
		
		$field = 'id,domain' ;
		$res =  Db::table($this->table)
				->field($field)
				->where('domain','eq',$domain)
				->find();

		if($res){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * [updateOrganLogoAndName //修改机构的机构名和logo]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [int]            $organid    [机构id]
	 * @param    [string]            $organname    [机构名]
	 * @param    [string]            $imageurl    [logo地址]
	 * @return   [bool] /[array]          
	 */
	public function updateOrganLogoAndName($organid,$organname,$imageurl){
		$data = [
			'organname'=>$organname,
			'imageurl'=>$imageurl
		];
		$this->save($data,['id'=>$organid]);
	}

	/**
	 * [updateOrganLogoAndName //修改机构的机构名和logo]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [int]            $organid    [机构id]
	 * @param    [string]            $organname    [机构名]
	 * @param    [string]            $imageurl    [logo地址]
	 * @param    [string]            $domain    [域名]
	 * @return   [bool] /[array]          
	 */
	public function updateOrganLogoAndNameAndDomain($organid,$organname,$imageurl,$domain){
		$data = [
			'organname'=>$organname,
			'imageurl'=>$imageurl,
			'domain'=>$domain
		];
		$this->save($data,['id'=>$organid]);
	}


	/**
	 * [getAllMonthData 获取平台月初到现在已经激活过的所有的机构]
	 * @Author wyx
	 * @param    $monthstart 当月 月初时间戳
	 * @return   [int]  -1代表未填写域名和机构名称,0代表未认证,1代表待审核,2代表已拒绝,3代表已通过并且启用,4代表已通过但是被禁用
	 */
	public function getAllMonthData($monthstart){
		return Db::table($this->table)
		->field('from_unixtime(passtime,"%Y-%m-%d") formatdate,count(id) num')
		->where('passtime','GT',$monthstart)
		->where('auditstatus','GT',2)
		->group('formatdate') //根据日期分组
		->select();
	}
	/**
	 * [getValidOrganCount 获取平台已经激活过的所有的机构总数]
	 * @Author wyx
	 * @param    无
	 * @return   [int]  -1代表未填写域名和机构名称,0代表未认证,1代表待审核,2代表已拒绝,3代表已通过并且启用,4代表已通过但是被禁用
	 */
	public function getValidOrganCount(){
		return Db::table($this->table)
		->where('auditstatus','GT',2)
		->count();
	}


	//复制到新的vip机构信息
    /**
     * [copyFromOldOrganToNewOrgan //批准机构申请vip,生成新机构]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param $oldOrganid int 原有的免费的机构的id 
     * @param $newOrganData array vip机构organ信息
     * @param $newAllccountData array  vip机构管理员信息
     * @param $newAdminmemberData array vip机构管理员信息
     * @param $newOrganBaseData array vip机构基本信息 
     * @param $newOrganAuthData array vip机构认证信息  
     * @return array 返回生成新的vip机构的机构主键id
     */
	public function copyFromOldOrganToNewOrgan($oldOrganid,$newOrganData,$newAllccountData,$newAdminmemberData,$newOrganBaseData,$newOrganAuthData,$newOrganImgData){

		Db::startTrans() ;
		try{
			//插入新的机构信息 获得主键id
			$newOrganId = Db::table('nm_organ')->insertGetId($newOrganData);

			//插入新的机构对应的超级管理员信息(一)
			$newAdminmemberData['organid'] = $newOrganId;
			$uid = Db::table('nm_adminmember')->insertGetId($newAdminmemberData);

			//插入角色用户表
			$roleData = [
				'roleid'=>1,
				'uid'=>$uid,
				'usertype'=>0
			];
			Db::table('nm_accessroleuser')->insert($roleData);

			//插入新的机构对应的超级管理员信息(二)
			$newAllccountData['uid'] = $uid;
			$newAllccountData['organid'] = $newOrganId;
			Db::table('nm_allaccount')->insert($newAllccountData);

			//插入新的机构的基本信息
			$newOrganBaseData['organid'] = $newOrganId;
			Db::table('nm_organbaseinfo')->insert($newOrganBaseData);

			//插入新的机构的认证信息
			$newOrganAuthData['organid'] = $newOrganId;
			Db::table('nm_organauthinfo')->insert($newOrganAuthData);

			//机构账户表自动添加一个记录
			$organAccount = [
				'tradeflow'=>'0.00',
				'usablemoney'=>'0.00',
				'frozenmoney'=>'0.00',
				'organid'=>$newOrganId,
			];

			Db::table('nm_organaccount')->insert($organAccount);

			//添加默认的轮播图
			foreach ($newOrganImgData as $k => $va) {
				$newOrganImgData[$k]['organid'] = $newOrganId;
			}
			Db::table('nm_organslideimg')->insertAll($newOrganImgData);

			// 提交事务
			Db::commit();
			return return_format(['newOrganid'=>$newOrganId],0,lang('success'));
		} catch (\Exception $e) {
			//回滚事务
			Db::rollback();
			return return_format($e->getMessage(),50045,lang('50045'));
		}		
	}

	public function updateOrganToHasVip($oldOrganid){
		//原机构的申请vip状态结束 appvip = 2
		$arr = [
			'applyvip'=>2
		];
		$res = $this->save($arr,['id'=>$oldOrganid]);
		return $res;
	}
	/**
     *	[getRecommOrgan 获取推荐机构]
     *	@author wyx
     *	
     *
     */
    public function getRecommOrgan(){
        //获取所有的免费机构 
        $where = [
    			'id'          => ['GT',1 ],// 1 为 官方id
    			'vip'         => 0,// 非vip
    			'recommend'   => 1,// 推荐类型
    			'auditstatus' => 3,//已通过并且启用
    		] ;
    	$field = 'id,organname,imageurl' ;
    	$order = 'sort desc' ;
        
        return Db::table($this->table)->where($where)->field($field)->order($order)->select() ;
    }
    /**
     *	[getRecommOrgan 获取所有的免费机构]
     *	@author wyx
     *	@param  $name   机构名字或者id
     *	@param  $limit   每页多少个
     *	@param  $offset  从哪里开始
     *
     */
    public function getFreeOrgan($name,$limit,$offset){
        //获取所有的免费机构 
        $where = [
    			'id'          => ['GT',1 ],// 1 为 官方id
    			'vip'         => 0,// 非vip
    			'auditstatus' => 3,//已通过并且启用
    		] ;
    	//是否需要检索
    	if(!empty($name)){
    		//$where['id|organname'] = $name ;//  此处将覆盖 id > 1 条件
    		$where['organname'] = $name ;//  此处将覆盖 id > 1 条件
    	}

    	$field = 'id,organname' ;
    	$order = 'recommend desc,sort desc' ;
        
        return Db::table($this->table)->where($where)->field($field)->order($order)->limit($offset.','.$limit)->select() ;

    }
    /**
     *	[getFreeOrganCount 获取所有的免费机构的总数用于分页]
     *	@author wyx
     *
     */
    public function getFreeOrganCount($name){
        //获取所有的免费机构 
        $where = [
    			'id'          => ['GT',1 ],// 1 为 官方id
    			'vip'         => 0,// 非vip
    			'auditstatus' => 3,//已通过并且启用
    		] ;
    	//是否需要检索
    	if(!empty($name)){
    		//$where['id|organname'] = $name ;//  此处将覆盖 id > 1 条件
    		$where['organname'] = $name ;//  此处将覆盖 id > 1 条件
    	}

        return Db::table($this->table)->where($where)->count() ;

    }
    /**
	 * updateFreeOrgan 增加免费机构的推荐
	 * @author wyx
	 * @param $organid
	 * @param $ids     新增推荐免费机构
	 */
	public function updateFreeOrgan($ids){
		return Db::table($this->table)->where( ['vip'=>0,'id'=>['IN',$ids]] )->update( [ 'recommend' => 1 ] );
	}
	/**
	 * updateFreeOrgan 交换两个推荐机构的位置
	 * @author wyx
	 * @param $ids    要交换的两个机构的id
	 */
	public function exchangeSort($ids){

		$where['vip'] = 0 ;
		$where['id']  = ['IN',$ids] ;
    	$arr = Db::table($this->table)->field('id,sort')->where($where)->select();
        if(count($arr)==2){
    		
	    	Db::table($this->table)->where(['id'=>$arr[0]['id']])->update(['sort'=>$arr[1]['sort']]);
	    	Db::table($this->table)->where(['id'=>$arr[1]['id']])->update(['sort'=>$arr[0]['sort']]);
    		return return_format('',0);
    	}else{
    		return return_format('',40138);
    	}
	}
	/**
	 * delCommOrgan 交换两个推荐机构的位置
	 * @author wyx
	 * @param $organid    要移除推荐的机构id
	 */
	public function delCommOrgan($organid){

		$where['id']  = $organid ;
    	$flag = Db::table($this->table)->where($where)->update(['recommend'=>0]);
        if($flag){
    		return return_format($flag,0);
    	}else{
    		return return_format($flag,40139);
    	}
	}


}
