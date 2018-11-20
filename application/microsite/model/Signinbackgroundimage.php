<?php
namespace app\microsite\model;
use think\Model;
use think\Db;
/*
 *
 * @ lc
*/
class Signinbackgroundimage extends Model{
    protected $table = 'nm_signinbackgroundimage';
    
	/**
	 *  getFieldByid
	 * @return array
	 */
	public function getFieldByid($id, $field){
		return Db::table($this->table)->field($field)->where('id', $id)->where('delflag', 'EQ', 0)->find();
	}
}







