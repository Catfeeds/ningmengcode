<?php

namespace app\teacher\model;
use think\Db;
use think\Model;

class TeacherLable extends Model
{
    //
    	protected $pk    = 'id';
	protected $table = 'nm_teacherlable';
	/**
	 * 教师标签列表 一层的数据获取
	 * @Author wyx
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $organid 机构id
	 * @param  $start   开始取位置
	 * @param  $offset  获取数量
	 * @return   array                   [description]
	 */
    public function getLableList($organid,$start=0,$offset=5)
    {
    	//分页兼容
    	$limit = $start>0 ? $start.','.$offset : $offset ;
    	//获取字段
    	$field = 'id,tagname,addtime,fatherid,status' ;
        return Db::table($this->table)->where(['organid'=>$organid,'fatherid'=>0,'delflag'=>0])->field($field)->limit($limit)->select();
    }
    /**
	 * 根据机构id 统计标签数量
	 * @Author wyx
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $idarr   分页后的父级标签id数组
	 * @return   array                   [description]
	 */
    public function getValueNum($idarr)
    {
    	$field = 'count(fatherid) num,fatherid' ;
    	return Db::table($this->table)->where(['fatherid'=>['in',$idarr]])->field($field)->group('fatherid')->select();
    }
    /**
	 * 添加 标签
	 * @Author wyx
	 * @param $organid 机构id从session 中获取
	 * @param $tagname 标签名称
	 * @return array
	 * 
	 */
	public function addLable($organid,$tagname){
		$connobj = Db::table($this->table) ;
		//教师标签
		$data = [
				'tagname'=>$tagname ,
				'organid'=>$organid ,
				'addtime'=>time() 
			] ;
		$return = $connobj->insert($data) ;
		if($return){
			//更新sort 值
			$returnid = $connobj->getLastInsID() ;
			// 
			Db::table($this->table)->where(['id'=>$returnid])->update(['sort'=>$returnid]) ;
			//返回结果
			return return_format('',0,'添加成功') ;
		}else{
			return return_format('',-1,'添加标签错误')
			 ;
		}
		
	}
	/**
	 * 更新 标签 以及标签值
	 * @Author wyx
	 * @param $where 更新条件
	 * @param $data  更新内容
	 * @return array
	 * 
	 */
	public function updateLable($where,$data){
		$return = Db::table($this->table)->where($where)->update($data) ;

		//返回结果
		return return_format('',0,'更新成功') ;
	}
	/**
	 * 查询标签 是否已经被使用
	 * @Author wyx
	 * @param $organid   所属机构id
	 * @param $lableid   要被更新的标签的id
	 * @return array
	 * 
	 */
	public function findLableUsing($organid,$lableid){
		$where  = [
					'id'     => $lableid ,
					'organid'=> $organid ,
				] ;
		return Db::table($this->table)->field('id')->where($where)->find() ;
		
	}
	/**
	 * 删除教师 标签 假删除 将delflag 设置为1 且将子级设置为1
	 * @Author wyx
	 * @param $organid   所属机构id
	 * @param $lableid   要被更新的标签的id
	 * @return array
	 * 
	 */
	public function deleteLable($organid,$lableid){
		
		$where['organid']     = $organid;
		$where['id|fatherid'] = $lableid;
		
		return Db::table($this->table)
		->where($where)
		// ->fetchSql(true)
		->update(['delflag'=>1 ]) ;
		
	}
	/**
	 * [getValueList 获取标签值列表]
	 * @Author wyx
	 * @DateTime 2018-04-18T16:30:12+0800
	 * @param  $lableid  根据标签id 获取他的子级
     * @param  $organid  机构类别id
     * @param  $start    开始取位置
	 * @param  $offset   获取数量
	 * @return   [type]                   [description]
	 */
	public function getValueList($organid,$lableid,$start=0,$offset=5)
    {
    	//分页兼容
    	$limit = $start>0 ? $start.','.$offset : $offset ;
    	//获取字段
    	$field = 'id,tagname,addtime,fatherid,status' ;
        return Db::table($this->table)->where(['organid'=>$organid,'fatherid'=>$lableid,'delflag'=>0])->field($field)->limit($limit)->select();
       // print_r(Db::table($this->table)->getlastsql());

	}
	/**
	 * [exchangeSort 根据两个id来交换sort值]
	 * @Author wyx
	 * @DateTime 2018-04-18T16:30:12+0800
	 * @param  $organid   机构类别id
	 * @param  $ids       要交换的id数组
	 * @return   [type]                   [description]
	 */
	public function exchangeSort($organid,$ids)
    {	
    	$where['id'] = ['in',$ids] ;
    	$arr = Db::table($this->table)->field('id,sort')->where($where)->select();
    	if(count($arr)==2){
    		
	    	Db::table($this->table)->where(['organid'=>$organid,'id'=>$arr[0]['id']])->update(['sort'=>$arr[1]['sort']]);
	    	Db::table($this->table)->where(['organid'=>$organid,'id'=>$arr[1]['id']])->update(['sort'=>$arr[0]['sort']]);
    		return return_format('',0,'移动成功');
    	}else{
    		return return_format('',-1,'数据匹配失败');
    	}
	}
	/**
	 * 删除教师 标签值 假删除 将delflag 设置为1
	 * @Author wyx
	 * @param $organid   所属机构id
	 * @param $lableid   要被更新的标签的id
	 * @return array
	 * 
	 */
	public function deleteLableValue($organid,$lableid){
		$where  = [
					'id'=> $lableid,
					'organid'=> $organid,
				] ;
		
		return Db::table($this->table)->where($where)->update(['delflag'=>1 ]) ;
	}
}
