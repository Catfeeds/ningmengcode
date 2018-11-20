<?php

namespace app\student\model;

use think\Model;
use think\Db;

class Teachertime extends Model
{
    //
    protected $pk    = 'id';
	protected $table = 'nm_teachertime';
	/**
	 * [findWeekMark 获取一对一时间掩码]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
	public function findWeekMark($teacherid){
		$where  = [
					'teacherid' => $teacherid ,
				] ;
		$field = 'id,week,mark' ;
		return Db::table($this->table)->field($field)->where($where)->select() ;
	}
    /**
     * [findWeekMark 获取一对一某天的时间掩码]
     * @Author wyx
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
    public function findWeekdayMark($teacherid,$week){
        $where  = [
            'teacherid' => $teacherid ,
            'week'   => $week ,
        ] ;
        $field = 'id,week,mark' ;
        return Db::table($this->table)->field($field)->where($where)->find() ;
    }
}
