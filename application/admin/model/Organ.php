<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Organ extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organ';
	protected $rule = [
		'organname' => 'require|max:200',
		'summary'	=> 'require|max:500',
		'imageurl'	=> 'require|max:500',
		'hotline'	=> 'require|max:50',
		'email'		=> 'require|max:50',
		'contactname'=> 'require|max:30',
		'contactphone' => 'require|max:30',
		'contactemail' => 'require|email',
	];

	protected $message = [];
	protected function initialize() {
		parent::initialize();
		$this->message = [
			'organname.require' => lang('50253'),
			'organname.max' => lang('50254'),
//			'profile.require' => lang('50255'),
//			'profile.max' => lang('50256'),
			'summary.require' => lang('50257'),
			'summary.max' => lang('50258'),
			'imageurl.require' => lang('50259'),
			'imageurl.max' => lang('50260'),
			'hotline.require' => lang('50261'),
			'hotline.max' => lang('50262'),
			'email.require' => lang('50263'),
			'email.max' => lang('50264'),
			'contactname.require' => lang('50243'),
			'contactname.max' => lang('50244'),
			'contactphone.require' => lang('50245'),
			'contactphone.max' => lang('50246'),
			'contactemail.require' => lang('50247'),
			'contactemail.email' => lang('50248'),
		];
	}
	/**
	 * [getOrganmsgById 根据机构id获取机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @param  $orderinfo  [array]                   $orderinfo [包含机构id 和 套餐时长]
	 * @param  $mealid  [array]                   套餐id
	 * @return   int
	 */
	public function upVipTime($orderinfo,$mealid){

        $mealinfo = Db::table('nm_organauditbill')->where('id','eq',$mealid)->field('ontrial')->find();
        $info     = Db::table($this->table)->where('id','eq',$orderinfo['organid'])->field('validtime,usetrial')->find();

        $data = [] ;
        if(empty($info) || (isset($info['validtime']) && $info['validtime'] < time() ) )
        {
            $time = time() + $orderinfo['during'] ;
            if(isset($info['validtime']) && $info['validtime'] ==0 ) $data['usetrial'] = $mealinfo['ontrial']==1 ? 3 : 1 ;// 第一次购买 分为体验 改为1 如果飞体验 标记为 3 不可体验
        }else{
            if( isset($info['validtime']) && $info['validtime'] > 0 ) $data['usetrial'] = $info['usetrial']==1 ? 2 : $info['usetrial'] ;
            $time = $orderinfo['during'] ;
        }
        return Db::table($this->table)
                        ->where('id','eq',$orderinfo['organid'])
                        ->exp('validtime','validtime + '.$time)
                        ->update($data) ;

	}
	/**
	 * [getOrganmsgById 根据机构id获取机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @param    [type]                   $organid [description]
	 * @return   [type]                            [description]
	 */
	public function getOrganmsgById(){
		$field = 'id,organname,profile,summary,imageurl,hotline,email,contactname,contactphone,contactemail,classhours' ;
		return Db::table($this->table)
				->field($field)
				->where('id','eq',1)
				->find() ;
	}

	/**
	 * [getOrganmsgById 根据机构id获取机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @param    [type]                   $organid [description]
	 * @return   [type]                            [description]
	 */
	public function getOrganid(){
		$field = 'id,organname,roomkey,classhours' ;
		return Db::table($this->table)
			->field($field)
			->where('id','eq',1)
			->find() ;
	}

	/**
	 * [updateOrganMsg 更新机构信息]
	 * @Author
	 * @DateTime 2018-04-23T12:08:01+0800
	 * @param    [array]            $data    [需要更新的数据]
	 * @param    [int]              $organid [机构标识id]
	 * @return   [int]                       [更新结果标记]
	 */
	public function updateOrganMsg($data){
		$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',40024,$validate->getError());
		}else{
			//入库
			$return = $this->allowField(true)->save($data,['id'=>1]);
			return return_format($return,0);
		}
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
	public function updateOrgan($data,$id){
		$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{
			try{
				$res = $this->save($data,['id'=>$id]);
				return return_format($res,0,lang('success'));			
			}catch(\Exception $e){
				return return_format($e->getMessage(),50004,lang('50004'));			
			}
		}
	}


	/**
	 * 更新关于我们
	 * @param $aboutus
	 * @param $id
	 * @return int|string
	 */
	public function updateOrgans($aboutus,$id){
		return Db::table($this->table)->where('id',$id)->update(['aboutus'=>$aboutus]);
	}

	/**
	 * [getOrganListCount 获取机构数目]
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
	 * [updateOrganStatus//更改organ状态]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [array]            $where    [筛选条件]
	 * @return   [int]              $count        [查询的数目]
	 */
	public function updateOrganStatus($auditstatus,$organid){
		try{
			$res = $this->save(['auditstatus'=>$auditstatus],['id'=>$organid]);
			return return_format($res,0,lang('success'));			
		}catch(\Exception $e){
			return return_format($e->getMessage(),50004,lang('50004'));			
		}
	}



	/**
	 * [ 申请vip机构]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [int]            $organid    [organid]
	 * @return   [bool] /[array]          
	 */
	public function applyVipOrgan($organid){

		//该机构是认证过的auditstatus = 3|4 免费机构 vip=0 appvip=0
		$where = [];
		$where['id'] = ['EQ',$organid];

 		$res =  Db::table($this->table)
		->where($where)
		->find();
		// var_dump($res);
		// die;
		if(!$res){  
			return return_format('',50047,lang('50047'));
		}

		//更新机构的appvip状态为1 并添加appviptime为当前时间
		$data = [
			'applyvip' => '1',
			'applyviptime' => time()
		];
		try{
			$this->save($data,['id'=>$organid]);
			return return_format('',0,lang('success'));
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
	public function getOrganById($organid,$field = '*'){

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
	 * [getOrganAuditstatusById 查看机构是否存在]
	 * @Author
	 * @DateTime 2018-05-03
	 * @param    [int]            $organid    [organid]
	 * @return   [bool] /[array]          
	 */
	public function getOrganAuditstatusById($organid){
		
		$field = 'id,auditstatus' ;
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
	 * //获取机构试用套餐剩余时间
	 * @Author zzq
	 * @param [status] []
	 * @return array  [返回信息以天计算]
	 *
	 */
	public function getOrganTrialBillLastTime($organid){

		$field = 'usetrial,validtime' ;
		$res =  Db::table($this->table)
				->field($field)
				->where('id','eq',$organid)
				->where('usetrial','eq','1') //试用套餐
				->find();
		$data = [];
		if($res){
			//判断试用版用户是否过期
			if($res['validtime'] <= time() ){
				$data['lastdays'] = 0;
				return return_format($data,0,lang('success'));
			}else{
				$lastdays = ceil( ($res['validtime']-time() )/(3600*24));
				$data['lastdays'] = $lastdays;
				return return_format($data,0,lang('success'));
			}
		}else{
			return return_format('',50090,lang('50090'));
		}
	}


    /**
    * 根据机构id判断该该机构属于教师个人还是企业
    * @Author WangWY
    *
    **/
    public function getrestype($organid){
    	return Db::table($this->table)
    	       ->where('id','eq',$organid)
    	       ->field('restype')
    	       ->find();
    }
	
	/**
	 * 更新下载json
	 * @param $downloadjson
	 * @param $id
	 * @return int|string
	 */
	public function updateOrgansDownloadJson($downloadjson,$id){
		return Db::table($this->table)->where('id',$id)->update(['downloadjson'=>$downloadjson]);
	}
}
