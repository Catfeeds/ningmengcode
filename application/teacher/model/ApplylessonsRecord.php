<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class ApplylessonsRecord extends Model
{
    protected $pk = 'id';
    protected $table = 'nm_applylessonsrecord';

    /**
     * @param $data
     * @return int|string
     */
    public function getAltlessonsCount($data){
        return Db::table($this->table)
            ->where($data)
            ->count();
    }

    /**
     * @param $where
     * @param $field
     * @return array
     */
    public function getAltlessonslist($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
}
