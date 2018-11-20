<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 开课副表
 * @ jcr
*/
class Schedulingdeputy extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_schedulingdeputy';//表名称

	/**
     * 将签名验证成功的推单数据 插入数据表
     * @author JCR
     * @param $data 添加数据源
     */
    public function addEdit($data,$id = false){
        if($id){
			return Db::table($this->table)->where('id',$id)->update($data);
		}else{
			return Db::table($this->table)->insert($data);

		}
    }



}
