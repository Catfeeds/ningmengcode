<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 官方轮播Model
 * @ yr
*/
class Officialslideimg extends Model{
    protected $table = 'nm_officialslideimg';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getSlideList 获取轮播列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        parent_id[课程id]
     * @return   array
     */
    public function getSlideList(){
        $lists =Db::table($this->table)
            ->field('id,remark,imagepath,sortid')
            ->order('sortid asc')
            ->select();
        return  $lists;
    }

}







