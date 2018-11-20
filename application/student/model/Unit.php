<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课程单元Model
 * @ yr
*/
class Unit extends Model{
    protected $table = 'nm_unit';
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
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getUnitList($curriculumid){
        $lists =Db::table($this->table)
            ->where('delflag','eq','1')
            ->where('curriculumid',$curriculumid)
            ->field('id as unitid,curriculumid,unitname,unitsort')
            ->order('unitsort')
            ->select();
        return  $lists;
    }

}







