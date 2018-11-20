<?php

namespace app\teacher\model;

use think\Model;
use think\Db;

class Studentfunds extends Model
{
	protected $table = 'nm_studentfunds';
    //添加学生信息
    public function addStudentinfo($studentid){
       return Db::table($this->table)->insert(['studentid' => $studentid]);
    }

    //删除学生信息
    public function delStudentinfo($studentid){
    	return Db::table($this->table)->delete($studentid);
    }
}
