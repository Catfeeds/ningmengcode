<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 参数配置表
 * @ yr
*/
class Regionconfig extends Model{
    protected $table = 'nm_regionconfig';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getClassNum 获取大小班可报名人数]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getClassNum(){
        $lists =Db::table($this->table)
            ->field('maxclass,minclass')
            ->find();
        return  $lists;
    }

}







