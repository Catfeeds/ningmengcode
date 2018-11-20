<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use app\official\model\Organ;
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
	protected function initialize() {
		parent::initialize();
		$this->message = [
	        'organid.require' => lang('50241'),
			'organid.number' => lang('50242'),
			'contactname.require' => lang('50243'),
			'contactname.max' => lang('50244'),
			'contactphone.require' => lang('50245'),
			'contactphone.max' => lang('50246'),
			'contactemail.require' => lang('50247'),
			'contactemail.email' => lang('50248'),
			'summary.require' => lang('50257'),
			'summary.max' => lang('50258'),
			'phone.require' => lang('50261'),
			'phone.max' => lang('50262'),
			'email.require' => lang('50263'),
			'email.email' => lang('50264'),
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