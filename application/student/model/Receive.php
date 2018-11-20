<?php

namespace app\student\model;
/*
 * 收集赠送免费课程信息 model
 * @ yr
*/
use think\Model;
use think\Db;

class Receive extends Model
{
    //
    protected $table = 'nm_receive';
    /**
     * [isMobileExist 查询该手机号是否存在]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $toteachid [toteachid]
     * @return   [type]                  [description]
     */
    public function isMobileExist($mobile,$prphone){
        $res = Db::table($this->table)
            ->where('mobile','eq',$mobile)
            ->where('prphone','eq',$prphone)
            ->count();
        return $res;
    }
    /**
     * [getData 查询该手机号是否存在]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $toteachid [toteachid]
     * @return   [type]                  [description]
     */
    public function addData($data){
        $field = 'mobile,receivetime,prphone,name';
        $res = Db::table($this->table)->field($field)->insert($data);
        return $res;
    }
}
