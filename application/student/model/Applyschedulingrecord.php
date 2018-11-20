<?php
namespace app\student\model;
use think\Db;
/*
 * 申请调班表Model
 * @ yr
*/
class Applyschedulingrecord
{
    protected $table = 'nm_applyschedulingrecord';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * 查询调班记录下的课程数量
     * @Author yr
     * @return mixed
     */
    public function getCourseCount($where)
    {
        $result = Db::table($this->table)
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * 插入调班记录
     * @Author why
     * @return mixed
     */
    public function insertData($data)
    {
        $result = Db::table($this->table)->insert($data);
        return $result;
    }
    /**
     * 查找数据
     * @Author why
     * @return mixed
     */
    public function findData($where,$field){
        $result = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
        return $result;
    }
    /**
     * 查询旧班级集合
     * @Author why
     * @return mixed
     */
    public function getColumnIds($where,$field){
        $result = Db::table($this->table)
            ->where($where)
            ->column($field);
        return $result;
    }
    /**
     * 查询指定课程下新班级的集合
     * @Author why
     * @return mixed
     */
    public function getNewLessons($where){
        $lists = Db::table($this->table. ' o')
            ->field('o.newschedulingid as schedulingid,s.gradename,c.periodnum,o.curriculumid')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_scheduling s','o.newschedulingid=s.id','LEFT')
            ->where($where)
            ->select();
        return $lists;
    }
}
