<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Refuseorgan extends Model
{
	protected $table = 'nm_organrefuseinfo';
	/**
	 * [getRefuseOrganInfoById 获取机构被拒绝审核的理由]
	 * @Author zzq
	 * @DateTime 2018-05-3
	 * @param    [int]                   $organid [description]
	 * @return   [str]                   $refuseinfo         [description]
	 */
	public function getRefuseOrganInfoById($organid){
		//获取最新的被拒绝的理由
		$field = 'refuseinfo' ;
		$res = Db::table($this->table)
				->where('organid','eq',$organid)
				->field($field)
				->order(['id'=>'desc'])
				->limit(1)
				->select() ;
		if($res){
	    	$refuseinfo = $res[0]['refuseinfo'];
		}else{
			$refuseinfo = '';
		}

		return $refuseinfo;
	}

	
	/**
	 * [getRefuseOrganInfoById //添加机构审核被拒绝的理由]
	 * @Author zzq
	 * @DateTime 2018-05-3
	 * @param    [int]                   $organid [组织id]
	 * @param    [int]                   $refuseinfo [拒绝理由]
	 * @return   [array]                   操作结果信息[description]
	 */
	public function addRefuseInfo($organid,$refuseinfo){
		$data = [
			'refuseinfo'=>$refuseinfo,
			'organid'=>$organid,
			'addtime'=>time()
		];
		try{
			$res = Db::table($this->table)->insert($data);
            return return_format($res,0,lang('success'));
		}catch(\Exception $e){
			return return_format($e->getMessage(),50004,lang('50004'));
		}

	}

}