<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class ExercisesubjectOptions extends Model
{
    //
    protected $pk = 'id';
    protected $table = 'nm_exercisesubjectoptions';
    public function showOptionlist($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
}
