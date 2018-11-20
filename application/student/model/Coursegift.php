<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课程赠品Model
 * @ yr
*/
class Coursegift extends Model{
    protected $table = 'nm_coursegift';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getField 获取指定字段的名称]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        parent_id[课程id]
     * @return   array
     */
    public function getField($where,$field){
        $lists =Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
        return  $lists;
    }
}







