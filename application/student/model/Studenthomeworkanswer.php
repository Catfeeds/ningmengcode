<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 学生作业表Model
 * @ yr
*/
class Studenthomeworkanswer extends Model{
    protected $table = 'nm_studenthomeworkanswer';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [submitQuestions 插入作业答案]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function submitQuestions($data){
        $result = Db::table($this->table)->insertAll($data);
        return $result;
    }
    /**
     * [updateQuestions 修改]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function updateQuestions($where,$data){
        $result = Db::table($this->table)->where($where)->update($data);
        return $result;
    }
    /**
     * [getAnswers 查询答案]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getAnswers($where){
        $result = Db::table($this->table)
            ->field('answer,score,comment')
            ->where($where)
            ->find();
        return $result;
    }
}







