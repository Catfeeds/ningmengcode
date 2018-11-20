<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Classroom extends Model
{
    protected $table = 'nm_classroom';
    /**
     * [getClassInfo 根据机构id获取机构信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function getClassInfo($toteachtimeid){
        $field = 'addtime,shuttime,classroomno,confuserpwd,passwordrequired' ;
        return Db::table($this->table)
            ->field($field)
            ->where('toteachtimeid','eq',$toteachtimeid)
            ->find() ;
    }

}
