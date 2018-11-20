<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 学生标签子表
 * @ yr
*/
class Studentchildtag extends Model{

    protected $table = 'nm_studentchildtag';

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
    public function getChildTag($where){
        $info  = Db::table($this->table)
            ->field('id as childtagid,name as childname')
            ->where('delflag','eq','0')
            ->where($where)
            ->select();
        return $info;
    }

}







