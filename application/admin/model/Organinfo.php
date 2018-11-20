<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\Organ;
class Organinfo extends Model
{

	protected $table = 'nm_organbaseinfo';
	protected $rule = [
			'organid'=>'require|number',
			'contactname' => 'require|max:30',
			'contactphone' => 'require|max:30',
			'contactemail' => 'require|email',
			'summary' => 'require|max:500',
			'phone' => 'require|max:30',
			'email' => 'require|email',
		];
	protected $message = [];
	protected $partRule = [
			'organid'=>'require|number',
			'contactname' => 'require|max:30',
			'contactphone' => 'require|max:30',
			'contactemail' => 'require|email',
			'phone' => 'require|max:30',
			'email' => 'require|email',
		];
	protected $partMessage = [];
	protected function initialize() {
		parent::initialize();
		$this->message = [
	        'organid.require' => '机构id必须填写',
			'organid.number' => '机构id不是整数',
			'contactname.require' => '联系人姓名必须填写',
			'contactname.max' => '联系人姓名最多不能超过30个字符',
			'contactphone.require' => '联系人电话必须填写',
			'contactphone.max' => '联系人电话最多不能超过30个字符',
			'contactemail.require' => '联系人邮箱必填',
			'contactemail.email' => '联系人邮箱格式不正确',
			'summary.require' => '机构概述必须填写',
			'summary.max' => '机构概述最多不能超过要求',
			'phone.require' => '客服热线必须填写',
			'phone.max' => '客服热线最多不能超过30个字符',
			'email.require' => '客服邮箱必填',
			'email.email' => '客服邮箱格式不正确',
		];
		$this->partMessage=[
	        'organid.require' => lang('50241'),
			'organid.number' => lang('50242'),
			'contactname.require' => lang('50243'),
			'contactname.max' => lang('50244'),
			'contactphone.require' => lang('50245'),
			'contactphone.max' => lang('50246'),
			'contactemail.require' => lang('50247'),
			'contactemail.email' => lang('50248'),
			'phone.require' => lang('50249'),
			'phone.max' => lang('50250'),
			'email.require' => lang('50251'),
			'email.email' => lang('50252'),
		];
	}
	/**
	 * [changeOrganInfo 修改基本信息]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param    [array]            $data    [需要的数据]
	 * @return   [array]                       [返回的处理信息]
	 */
	public function changeOrganInfo($data){
		$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{
			//进行判断
			$organinfo = new Organinfo();
			$flag = $organinfo->hasOrganInfoById($data['organid']);
			//开启事务修改企业名和logo
	    	Db::startTrans();
	    	try{
	    		//修改logo 域名 机构名
				$organ = new Organ();
				if($flag){
					//修改
					$res = $organ->updateOrganLogoAndName($data['organid'],$data['organname'],$data['imageurl']);
				}else{
					//添加
					$res = $organ->updateOrganLogoAndNameAndDomain($data['organid'],$data['organname'],$data['imageurl'],$data['domain']);		
				}

				unset($data['imageurl']);
				unset($data['organname']);
				unset($data['domain']);

				if($flag){
					//表示修改
					$this->save($data,['organid'=>$data['organid']]);
				}else{
					//表示新增
					$this->save($data);
				}
	    		Db::commit();
				return return_format('',0,lang('success'));
	    	}catch(\Exception $e) { 		
			    // 回滚事务
				Db::rollback();
				return return_format($e->getMessage(),50007,lang('50007'));
			}
		}
	}

    /**
    *  setOrganPartBaseInfo  //修改机构的基本的部分信息
    * @ zzq  2018-05-03
    * @param array $data 信息 
    * @return array 返回信息  ;
   */ 
	public function setOrganPartBaseInfo($data){
		$validate = new Validate($this->partRule, $this->partMessage);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{
			try{
				$res = $this->save($data,['organid'=>$data['organid']]);
				return return_format('',0,lang('success'));
			}catch(\Exception $e){
				return return_format($e->getMessage(),50004,lang('50004'));
			}			
		}
	}


	//获取基本信息
	/**
	 * [getOrganInfoById 根据机构id获取机构的基本信息]
	 * @Author zzq
	 * @DateTime 2018-05-05
	 * @param    [int]                   $organid [机构id]
	 * @return   [array]                 机构基本信息          [description]
	 */
	public function getOrganInfoById($organid){
		
		$res = Db::table($this->table)->where('organid','eq',$organid)->find();
		return $res;
	}
	/**
	 * [hasOrganInfoById 判断是否有有该机构的基本信息的记录]
	 * @Author zzq
	 * @DateTime 2018-05-05
	 * @param    [int]                   $organid [机构id]
	 * @return   [bool]                 是否有该机构的基本信息          [description]
	 */
	public function hasOrganInfoById($organid){
		
		$res = Db::table($this->table)->field('id')->where('organid','eq',$organid)->find();
		if($res){
			$flag = true;
		}else{
			$flag = false;
		}
		return $flag;
	}
}	