<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 选择题选项model
 * @ yr
*/
class Exercisesubjectoptions extends Model{
    protected $table = 'nm_exercisesubjectoptions';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [getSubjectOptions 根据习题id获取选择题选项]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getSubjectOptions($where){
        //课程名称 班级名称 习题名称 老师 截止时间 作业状态
        $lists = Db::table($this->table)
            ->where($where)
            ->column('optionname');
        return  $lists;
    }

}







