<?php

namespace app\student\model;

use think\Model;
use think\Db;

class Teachertagrelate extends Model
{
    //
    protected $pk    = 'id';
	protected $table = 'nm_teachertagrelate';
	/**
	 * 查询标签 是否已经被使用
	 * @Author wyx
	 * @param $lableid   要被更新的标签的id
	 * @return array
	 * 
	 */
	public function findLableUsing($lableid){
		$where  = [
					'lableid' => $lableid ,
				] ;
		return Db::table($this->table)->field('teacherid')->where($where)->find() ;
		
	}
    /**
     * [getBindLable 获取教师已经绑定的标签]
     * @Author   yr
     * @DateTime 2018-04-18T21:35:06+0800
     * @param    [int]  $teachid [description]
     * @return   [type]   [description]
     */
    public function getTeacherLable($teachid){
        $res = Db::table($this->table.' g')
            ->field('l.tagname')
            ->join('nm_teacherlable l','g.lableid=l.id','LEFT')
            ->where('g.teacherid','eq',$teachid)
            ->select();
        return $res;
    }
	/**
	 * [getBindLable 获取教师已经绑定的标签]
	 * @Author
	 * @DateTime 2018-04-18T21:35:06+0800
	 * @param    [int]  $teachid [description]
	 * @return   [type]   [description]
	 */
	public function getBindLable($teachid){
		$where = ['teacherid'=>$teachid] ;
		$arr   = Db::table($this->table)->where($where)->column('path');
		if(!empty($arr)){
			$field = 'id,tagname,addtime,fatherid,status' ;
			$res = Db::table('nm_teacherlable')->field($field)->where(['delflag'=>0])->where(function($query) use($arr){
				$query->where('id', 'in',$arr)->whereOr('fatherid', 'in',$arr);
			})->select() ;
            return $res;
		}else{
			return [] ;
		}
	}
}
