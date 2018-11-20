<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Coursepackageuse extends Model
{
    protected $table = 'nm_coursepackageuse';
    /**
     * [addData 添加数据]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function addData($data){
      $result = Db::table($this->table)->insertAll($data);
      return $result;
    }
    /**
     * [getDataByStatus 根据状态查询列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     */
    public function getDataByStatus($where,$limitstr){
        $field = 'u.id as packageuseid,p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid,u.usetime,u.type,u.surplus,u.total,u.ifuse';
        $result = Db::table($this->table.' u')
            ->join('nm_coursepackagegift g','g.id=u.packagegiftid','left')
            ->join('nm_coursepackage p','p.id=u.packageid','left')
            ->field($field)
            ->where($where)
            ->order('u.id')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     * [getDataByStatusCount 根据状态查询列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     */
    public function getDataByStatusCount($where){
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid,u.usetime,u.type,u.surplus,u.total';
        $result = Db::table($this->table.' u')
            ->field($field)
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * [getDataByStatusCount 根据状态查询列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     */
    public function getStudentPackage($where,$field){
        $result = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->select();
        return $result;
    }
    /**
     * [update 根据状态查询列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     */
    public function updateData($where,$field){
        $result = Db::table($this->table)->where($where)->update($field);
        $sql = Db::table($this->table)->getLastSql();
        file_put_contents('order.txt',print_r("查看SQL$sql" ,true),FILE_APPEND) ;
        return $result;
    }
    /**
     * [findStudentPackage 根据状态查询列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     */
    public function findStudentPackage($where,$field){
        $result = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->find();
        return $result;
    }
}
