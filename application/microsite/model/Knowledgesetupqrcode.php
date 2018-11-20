<?php
namespace app\microsite\model;
use think\Model;
use think\Db;
/*
 * 出勤表 knowledgesetupqrcode
 * @ lc
*/
class Knowledgesetupqrcode extends Model{
    protected $table = 'nm_knowledgesetupqrcode';
    
	/**
	 * 获取知识设置二维码url
	 * @return array
	 */
	public function getQrcode(){
		return Db::table($this->table)->field('imageurl')->where('delflag', 'EQ', 0)->find();
	}
}







