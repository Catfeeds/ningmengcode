<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Organconfig extends Model
{
    protected $table = 'nm_organ';
    /**
     * [getOrganmsgById 根据机构id获取机构信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function getRoomkey(){

        $field = 'roomkey' ;
        return Db::table($this->table)
            ->field($field)
            ->find() ;
    }

}
