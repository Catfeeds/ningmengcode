<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 机构轮播Model
 * @ yr
*/
class Organslideimg extends Model{
    protected $table = 'nm_organslideimg';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getSlideList 获取轮播列表]
     * @Author yr
     * @DateTime 2018-09-18T11:59:56+0800
     * @param    [int]        parent_id[课程id]
     * @return   array
     */
    public function getSlideList(){
        $lists =Db::table($this->table)
            ->field('id,remark,imagepath,sortid,urltype,courseid,teacherid,url')
            ->order('id desc')
            ->select();
        return  $lists;
    }

}







