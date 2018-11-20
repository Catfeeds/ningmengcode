<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课程单元Model
 * @ yr
*/
class Unitdeputy extends Model{
    protected $table = 'nm_unitdeputy';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getCourserList 获取指定课程单元的List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $排课id]
     * @return   array
     */
    public function getUnitList($schedulingid){
        $lists =Db::table($this->table)
            ->field('id as unitid,curriculumid,unitname,unitsort')
            ->where('delflag','eq','1')
            ->where('schedulingid','eq',$schedulingid)
            ->order('unitsort')
            ->select();
        return  $lists;
    }

}







