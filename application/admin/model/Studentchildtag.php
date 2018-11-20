<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Validate;

/*
 * 分类Model
 * @ lc
 */
class Studentchildtag extends Model {

	protected $table = 'nm_studentchildtag';

	//自定义初始化
	protected function initialize() {
		parent::initialize();
	}
	
	/** 
	 * 批量插入子标签数据
	 * @param $childtagRet array
	 * @return true or false
	 */
	public function insertChildtagArr($childtagRet){
		return Db::table($this->table)->insertAll($childtagRet);
	}
	
	/**
	 * get childtag name by fatherid
	 * @param fatherid
	 * @return [bool]
	 */
	public function getchildTagByfatherid($fatherid){
		$result = Db::table($this->table)
        ->where('fatherid',$fatherid)
        ->where('delflag',0)
        ->column('name');
		return $result;
	}
	
	/**
     * [根据fatherid 删除childtag]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [array]    $fatherid 
     * @return   [type]     [description]
     */
    public function delChildtagByfatherid($fatherid){
    	$result = Db::table($this->table)->where(['fatherid'=>$fatherid])->update(['delflag'=>1]);
		return $result;
    }
}
