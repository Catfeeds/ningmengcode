<?php
namespace app\microsite\model;
use think\Model;
use think\Db;
/*
 * 出勤表 Studentattendance
 * @ lc
*/
class Knowledge extends Model{
    protected $table = 'nm_knowledge';
    
	/**
	 * 根据条件获取知识
	 * @param $where
	 * @return array
	 */
	public function getFieldByWhere($where){
		return Db::table($this->table.' k')
			->join('nm_knowledgetype kt','k.typeid=kt.id','LEFT')
            ->field('k.id,k.content,k.answer,kt.name as typename')
            ->where($where)
            ->find();
	}
	
	/**
	 * 随机获取知识
	 * @param $where 过滤条件
	 * @return array
	 */
	public function getRandKnowledgeData($where){
		$lists =Db::table($this->table.' k')
			->join('nm_knowledgetype kt','k.typeid=kt.id','LEFT')
            ->field('k.id,k.content,k.answer,kt.name as typename')
            ->where($where)
			->orderRaw('rand()')
            ->limit(1)
            ->select();
        return  $lists;
	}
}







