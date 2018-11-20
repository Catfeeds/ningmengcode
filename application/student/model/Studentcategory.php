<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 学生喜欢分类表
 * @ yr
*/
class Studentcategory extends Model{

    protected $table = 'nm_studentcategory';

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getUserFavorCategory 获取学生喜欢的分类]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function getUserFavorCategory(){
        $info  = Db::table($this->table)
            ->field('id as categoryid,name')
            ->where('delflag','eq','0')
            ->where('status','eq',1)
            ->select();
        return $info;
    }

}







