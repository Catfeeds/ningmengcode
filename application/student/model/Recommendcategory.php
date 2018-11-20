<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课程分类推荐表
 * @ yr
*/
class Recommendcategory extends Model{
    protected $table = 'nm_recommendcategory';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getCategoryList 获取推荐课程列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        parent_id[课程id]
     * @return   array
     */
    public function getCategoryList($where){
        $lists = Db::table($this->table)
            ->field('categoryname,curriculumids')
            ->where($where)
            ->order('categorysort desc')
            ->select();
        return  $lists;
    }

}







