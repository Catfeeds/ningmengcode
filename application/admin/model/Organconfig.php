<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Organconfig extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organconfig';
	protected $organid;


	protected $rule = [	
			'toonetime' => 'require|number',
			'smallclasstime' => 'require|number',
			'bigclasstime' => 'require|number',
			'maxclass'=>'require|number',
			'minclass'=>'require|number',
		];
	protected $message = [];
	protected function initialize() {
		parent::initialize();
		$this->organid = 1;
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
		];
	}
	/**
	 * 获取对应的
	 * @Author jcr
	 * @param $organid   所属机构id
	 * @param $field     查询内容
	 * @return array
	 * 
	 */
	public function getOrganid($id,$field = 'id,organname,profile,imageurl,roomkey,classhours,contactname,contactphone'){
		$where  = [
					'id' => $id ,
				] ;
		$res = Db::table($this->table)->field($field)->where($where)->find() ;
		return $res;
		
	}

	/**
	 * 添加
	 * @Author zzq
	 * @param $organid   所属机构id  [官方机构后台organid为0]
	 * @return array
	 * 
	 */
	public function setOrganConfig($organid,$data){

		$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',50010,$validate->getError());
		}else{			
			//修改
			try{
				$flag = $this->getOrganid(1);
				if(!$flag){
					$data['organid'] = $organid;
					$this->save($data);
				}else{
					$this->save($data,['organid'=>$organid]);
				}
				return return_format('',0,lang('success'));
			}catch(\Exception $e){
				return return_format($e->getMessage(),40029);
			}				
		}
	}

	/**
     * 机构配置编辑
     * @author jcr
     * @param $data 添加数据源
	 * @return int
     */
    public function addEdit($data){
        //修改
        $data = where_filter($data,array('toonetime','smallclasstime','bigclasstime','regionprefix','maxclass','minclass','roomkey'));
        $ids = Db::table($this->table)->where('organid','eq',1)->update($data);
        return $ids;
    }


	/**
	 * [addOrgan 添加机构配置]
	 * @author JCR
	 * @param $date
	 */
    public function addOrgan($date){
		if(!$date) return false;
		return Db::table($this->table)->insertGetId($date);
	}
	
	

}
