<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * @ jcr
*/
class City extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_city';//表名称

	/**
     * 将签名验证成功的推单数据 插入数据表
     * @author JCR
     * @param $data 添加数据源
     */
    public function getInOrder($ids,$order = 'id asc'){
    	return Db::table($this->table)->where('id','in',$ids)->order($order)->column('name','id');
    }



}
