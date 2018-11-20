<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use app\official\model\Organ;
/**
*组织的身份认证信息
**/
class Organconfirm extends Model
{
	protected $table = 'nm_organauthinfo';
	protected $personalRule = [	
			'idname' => 'require|max:30',
			'idnum' => 'require|max:20',
			'frontphoto' => 'require|max:255',
			'backphoto' => 'require|max:255',
			'confirmtype'=>'require|in:1,2',
			'organid'=>'require|number',
		];
	protected $personalMessage = [];
	protected $organRule = [	
			'organname' => 'require|max:100',
			'organnum' => 'require|max:100',
			'frontphoto' => 'require|max:255',
			'backphoto' => 'require|max:255',
			'organphoto' => 'require|max:255',
			'confirmtype'=>'require|in:1,2',
			'organid'=>'require|number',
		];
	protected $organMessage = [];
	protected function initialize() {
		parent::initialize();
		$this->personalMessage = [
	        'idname.require' => lang('50217'),
			'idname.max' => lang('50218'),
	        'idnum.require' => lang('50219'),
			'idnum.max' => lang('50220'),
	        'frontphoto.require' => lang('50221'),
			'frontphoto.max' => lang('50222'),
	        'backphoto.require' => lang('50223'),
			'backphoto.max' => lang('50224'),
	        'confirmtype.require' => lang('50225'),
			'confirmtype.in' => lang('50226'),
	        'organid.require' => lang('50227'),
			'organid.number' => lang('50228'),
		];
		$this->organMessage=[
	        'organname.require' => lang('50229'),
			'organname.max' => lang('50230'),
	        'organnum.require' => lang('50231'),
			'organnum.max' => lang('50232'),
	        'frontphoto.require' => lang('50233'),
			'frontphoto.max' => lang('50234'),
	        'backphoto.require' => lang('50235'),
			'backphoto.max' => lang('50236'),
	        'organphoto.require' => lang('50237'),
			'organphoto.max' => lang('50238'),
	        'confirmtype.require' => lang('50239'),
			'confirmtype.in' => lang('50240'),
	        'organid.require' => lang('50241'),
			'organid.number' => lang('50242'),
		];
	}

	/**
	 * [changeOrganConfirmInfo 修改认证信息]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [array]            $data    [需要的数据]
	 * @return   [int]                      [返回的处理信息]
	 */
	public function changeOrganConfirmInfo($data){
		//根据认证类型的不同进行验证
		if($data['confirmtype'] == 1){
			//表示的是个人认证
			$validate = new Validate($this->personalRule, $this->personalMessage);
		}else{
			//表示的是企业认证
			$validate = new Validate($this->organRule, $this->organMessage);
		}

		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{

			//开启事务修改机构认证的信息   修改完成以后将状态从为未认证改为待审
	    	Db::startTrans();
	    	try{
				//进行判断是否已经填写过认证信息
			    $flag = $this->hasOrganConfirmInfoById($data['organid']);
				if($flag){
					//表示修改
					try{
						$this->save($data,['organid'=>$data['organid']]);
						//更改机构的状态未认证到待审
						$organ = new Organ();
						$organ->updateOrganStatus(1,$data['organid']);
						$error = '';
					}catch(\Exception $e){
						$error = $e->getMessage();
					}

				}else{
					//表示新增
					try{
						$this->save($data);
						//更改机构的状态未认证到待审
						$organ = new Organ();
						$organ->updateOrganStatus(1,$data['organid']);
						$error = '';
					}catch(\Exception $e){
						$error = $e->getMessage();
					}

				}
	    		Db::commit();
	    		if($error != ''){
	    			return return_format($error,50004,lang('50004'));
	    		}
	    		return return_format($error,0,lang('success'));
	    	}catch(\Exception $e) { 		
			    // 回滚事务
				Db::rollback();
				return return_format($e->getMessage(),50007,lang('50007'));
			}

			
		}
	}

	/**
	 * [getOrganConfirmInfoById 判断是否有有该机构的认证信息的记录]
	 * @Author zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                           [是否有该机构的认证信息]
	 */
	public function getOrganConfirmInfoById($organid){
		
		try{
		    $res = Db::table($this->table)->where('organid','eq',$organid)->find();
		    if($res){
		    	return return_format($res,0,lang('success'));
		    }else{
		    	return return_format('',50024,lang('50024'));
		    }
		}catch(\Exception $e){
			return return_format($e->getMessage(),50081,lang('50081'));
		}
	}
	/**
	 * [hasOrganConfirmInfoById 判断是否有有该机构的认证信息的记录]
	 * @Author zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $organid [机构id]
	 * @return   [bool]                           [是否有该机构的认证信息]
	 */
	public function hasOrganConfirmInfoById($organid){
		
		$res = Db::table($this->table)->field('id')->where('organid','eq',$organid)->find();
		if($res){
			$flag = $res;
		}else{
			$flag = false;
		}
		return $flag;
	}


	/**
	 * [getOrganAuthInfoById 机构的认证信息的记录]
	 * @Author zzq
	 * @DateTime 2018-05-07
	 * @param    [int]                   $organid [机构id]
	 * @return   [bool]                           [是否有该机构的认证信息]
	 */
	public function getOrganAuthInfoById($organid){
		
		$res = Db::table($this->table)->where('organid','eq',$organid)->find();
		if($res){
			$flag = $res;
		}else{
			$flag = false;
		}
		return $flag;
	}
}