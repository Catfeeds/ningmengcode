<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class ApplyschedulingRecord extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_applyschedulingrecord';
    //

    /** 调班学生数量
     * @param $data
     * @return int|string
     */
    public function getAlschedulingCount($data){
        return Db::table($this->table)
            ->where($data)
            ->count();
    }

    /**
     * @param $where
     * @param $field
     * @return array
     */
    public function getAlschedulinglist($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
}
