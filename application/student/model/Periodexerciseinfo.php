<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 题库课时信息Model
 * @ yr
*/
class Periodexerciseinfo extends Model{
    protected $table = 'nm_periodexerciseinfo';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getSubjectCount 查询作业题目数量以及题目名称]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getSubjectCount($keshi_id){
        $lists = Db::table($this->table.' k')
            ->join('nm_period l','l.id=k.periodid','LEFT')
            ->field('k.subjectcount,l.periodname')
            ->where('k.periodid','eq',$keshi_id)
            ->where('k.delflag','eq',0)
            ->find();
        return  $lists;
    }

}







