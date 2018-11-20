<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use app\official\model\Officialuseroperate;
class Organconfig extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organconfig';

	protected $rule = [	
			'toonetime' => 'require|number',
			'smallclasstime' => 'require|number',
			'bigclasstime' => 'require|number',
			'maxclass'=>'require|number',
			'minclass'=>'require|number',
			'organid'=>'require|number',
			'roomkey'=>'require'
		];
	protected $message = [];
	protected function initialize() {
		parent::initialize();
		$this->message = [
	        'toonetime.require' => lang('50207'),
			'toonetime.number' => lang('50208'),
	        'smallclasstime.require' => lang('50209'),
			'smallclasstime.number' => lang('50210'),
	        'bigclasstime.require' => lang('50211'),
			'bigclasstime.number' => lang('50212'),
	        'maxclass.require' => lang('50213'),
			'maxclass.number' => lang('50214'),
	        'minclass.require' => lang('50215'),
			'minclass.number' => lang('50216'),
			'organid.require' => lang('50241'),
			'organid.number' => lang('50242'),
			'roomkey.require' => lang('50287'),
		];
	}

	/**
	 * 获取官方后台 课堂配置
	 * @Author zzq
	 * @param $organid   所属机构id  [官方机构后台organid为0]
	 * @return array
	 * 
	 */
	public function getOrganClassConfig($organid){
		$where  = [
					'organid' => $organid ,
				] ;
		try{
			$res =  Db::table($this->table)->field('id,toonetime,smallclasstime,bigclasstime,maxclass,minclass,roomkey')->where($where)->find();
				return  return_format($res,0,lang('success'));
		}catch(\Exception $e){
			return  return_format($e->getMessage(),50004,lang('50004'));
		}	
	}

	/**
	 * 查看官方后台 课堂配置 是否存在
	 * @Author zzq
	 * @param $organid   所属机构id  [官方机构后台organid为0]
	 * @return bool
	 * 
	 */
	public function hasGetOrganClassConfig($organid = 0){
		$where  = [
					'organid' => $organid ,
				] ;
	
		$res =  Db::table($this->table)->field('id')->where($where)->find();
		if($res){
			return true;
		}else{
			return false;
		}	
	}

	/**
	 * 添加
	 * @Author zzq
	 * @param $organid   所属机构id  [官方机构后台organid为0]
	 * @return array
	 * 
	 */
	public function changeOrganClassConfig($data){

		$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{
			$res = $this->hasGetOrganClassConfig($data['organid']);
			// var_dump($res);
			// die;
			if($res){
				//修改
				try{
					$this->save($data,['organid'=>$data['organid']]);
	            	//添加操作日志
	            	$obj = new Officialuseroperate();
	                $obj->addOperateRecord('修改了课堂配置'); 

					return return_format('',0,lang('success'));
				}catch(\Exception $e){
					return return_format($e->getMessage(),50004,lang('50004'));
				}				
			}else{
				//添加
				try{
					$this->save($data);
	            	//添加操作日志
	            	$obj = new Officialuseroperate();
	                $obj->addOperateRecord('添加了课堂配置'); 
					return return_format('',0,lang('success'));
				}catch(\Exception $e){
					$error = $e->getMessage();
					return return_format($e->getMessage(),50004,lang('50004'));
				}				
			}

		}
	}

	//
	/**
	 * 生成新机构以后，自动插入一条记录
	 * @Author zzq
	 * @param $organid   int [机构id]
	 * @return array 返回信息
	 * 
	 */
	public function AfterAddOrganChangeClassConfig($organid){
		//读取参数的配置
		$res = $this->hasGetOrganClassConfig(0);
		if(!$res){
			//如果没有数据
			return false;

		}else{
			$arr = $this->getOrganClassConfig(0);
			$ret  = $arr['data'];  //数组里边
			$data = [];
			$data['toonetime'] = $ret['toonetime'];
			$data['smallclasstime'] = $ret['smallclasstime'];
			$data['bigclasstime'] = $ret['bigclasstime'];
			$data['regionprefix'] = '';
			$data['maxclass'] = $ret['maxclass'];
			$data['minclass'] = $ret['minclass'];
			$data['organid'] = $organid;
			$data['roomkey'] = '';  //如何生成的
			$this->save($data);			
		}

	}	

}
