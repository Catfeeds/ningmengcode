<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Coursepackagegift extends Model
{
    protected $table = 'nm_coursepackagegift';
    /**
     * [getPackageList 获取所有的套餐列表]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $toteachtimeid[description]
     * @return   [type]                            [description]
     */
    public function getPackageList($limitstr){
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid';
        $result = Db::table($this->table.' p')
            ->join('nm_coursepackagegift g','p.id=g.packageid','left')
            ->field($field)
            ->where('p.delflag','eq','1')
            ->order('p.id')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     * [getPackageCount 获取所有的套餐数量]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function getPackageCount(){
        return Db::table($this->table)
            ->where('delflag','eq','1')
            ->count();
    }
    /**
     * [getPackageDetail 查询套餐详情]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function getPackageDetail($packageid){
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid';
        $result = Db::table($this->table.' p')
            ->join('nm_coursepackagegift g','p.id=g.packageid','left')
            ->field($field)
            ->where('p.delflag','eq','1')
            ->where('p.id','eq',$packageid)
            ->find();
        return $result;
    }
}
