<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class CoursepackageUseDetail extends Model
{
    protected $table = 'nm_coursepackageusedetail';
    /**
     * [insertData 插入套餐列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function insertData($data){
        $result = Db::table($this->table)->insertGetId($data);
        return $result;

    }
    /**
     * [getInfo 获取套餐信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function getInfo($where,$field){
        $result = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
        return $result;

    }
    /**
     * [getInfo 获取套餐信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function joinFindInfo($where,$field){
        $result = Db::table($this->table.' d')
            ->join('nm_coursepackageuse u','u.id = d.packageuseid','left')
            ->field($field)
            ->where($where)
            ->find();
        return $result;

    }
}
