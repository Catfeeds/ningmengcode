<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 学生喜欢分类表
 * @ yr
*/
class Studenttag extends Model{

    protected $table = 'nm_studenttag';

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getUserTag  获取学生的标签]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function getUserTag(){
        $info  = Db::table($this->table)
            ->field('id as tagid,name as fathername')
            ->where('delflag','eq','0')
            ->where('status','eq',1)
            ->find();
        return $info;
    }

}







