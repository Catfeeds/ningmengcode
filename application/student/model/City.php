<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 城市列表Model
 * @ yr
*/
class City extends Model{
    protected $table = 'nm_city';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getCityList 获取分类]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        parent_id[课程id]
     * @return   array
     */
    public function getCityList($parent_id){
        $lists =Db::table($this->table)
            ->field('id,name,en_name,is_child')
            ->where('parent_id','eq',$parent_id)
            ->select();
        return  $lists;
    }
    /**
     * [getAllList]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getAllList(){
        $lists =Db::table($this->table)
            ->field('id,name,parent_id,en_name,is_child')
            ->select();
        return  $lists;
    }
    /**
     * [getName 获取名称]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getName($id){
        $lists =Db::table($this->table)
            ->field('name')
            ->where('id','eq',$id)
            ->find();
        return  $lists['name'];
    }
}







