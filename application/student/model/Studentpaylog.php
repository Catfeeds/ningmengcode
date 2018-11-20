<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 学生资金流水Model
 * @ yr
*/
class Studentpaylog extends Model{
    protected $table = 'nm_studentpaylog';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getstudentFunds 获取学生资金流水]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [int]        $userid  [学生用户id]
     * @param    [string]     $limitstr     [分页条件]
     * @return   array
     */
    public function getStudentPaylog($userid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->join('nm_curriculum c','s.courseid=c.id','LEFT')
            ->field('c.coursename,s.studentid,s.paynum,s.paytype,s.paystatus,FROM_UNIXTIME(s.paytime) as paytime')
            ->where('studentid','eq',$userid)
            ->where(function ($query) {
                   $query->where('paystatus', 'eq', '2')->whereOr('paytype', 'eq', '1');
                })
                ->order('s.paytime desc')
            ->limit($limitstr)
            ->select();
        return $lists;
    }
    /**
     * [getstudentFunds 获取学生资金流水]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [int]        $userid  [学生用户id]
     * @param    [string]     $limitstr     [分页条件]
     * @return   array
     */
    public function studentPaylogCount($userid){
        $lists = Db::table($this->table)
            ->where('studentid','eq',$userid)
            ->where(function ($query) {
                $query->where('paystatus', 'eq', '2')->whereOr('paytype', 'eq', '1');
            })
            ->count();
        return $lists;
    }
    /**
     * [insert 插入学生资金流水]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    $data
     * @return   array
     */
    public function insert($data){
        $id = Db::table($this->table)->insert($data);
        return $id;
    }
}







