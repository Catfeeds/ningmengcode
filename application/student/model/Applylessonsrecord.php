<?php
namespace app\student\model;
use think\Db;
/*
 * 申请调课表Model
 * @ yr
*/
class Applylessonsrecord
{
    protected $table = 'nm_applylessonsrecord';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * 查询数量
     * @Author yr
     * @return mixed
     */
    public function getCount($where)
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
}
