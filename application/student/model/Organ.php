<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Organ extends Model
{	
	protected $table = 'nm_organ';
	/**
	 * [getOrganmsgById 根据机构id获取机构信息]
	 * @Author yr
	 * @DateTime 2018-04-23T11:38:01+0800
	 * @return   [type]                            [description]
	 */
	public function getOrganmsgByDomain(){
		$res =  Db::table($this->table.' o')
            ->field('o.imageurl,o.id as organid,o.organname,b.summary,o.hotline,o.email')
            ->join('nm_organbaseinfo b','o.id=b.organid','LEFT')
            ->find();
		return $res;
	}
    /**
     * [searchOrganByName 根据机构名称模糊搜索机构]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function searchOrganByName($keywords,$limitstr){
        if(empty($keywords)){
            $where = '';
        }else{
            $where['organname'] = ['like',"%$keywords%"];
        }
        $result =  Db::table($this->table.' o')
            ->field('o.imageurl,o.organname,b.summary')
            ->join('nm_organbaseinfo b','o.id=b.organid','left')
            ->where('o.auditstatus','eq','3')
            ->where('o.vip','eq','0')
            ->where('o.id','gt','1')
            ->where($where)
            ->order('o.sort')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     * [searchOrganCount 根据机构名称模糊搜索机构]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function searchOrganCount($keywords){
        if(empty($keywords)){
            $where = '';
        }else{
            $where['organname'] = ['like',"%$keywords%"];
        }
        $result =  Db::table($this->table)
            ->where('auditstatus','eq','3')
            ->where('vip','eq','0')
            ->where('id','gt','1')
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * [getRecommendOrgan 获取推荐机构]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function getRecommendOrgan($limit){
        $result =  Db::table($this->table.' o')
            ->field('o.imageurl,o.id as organid,o.organname,b.summary')
            ->join('nm_organbaseinfo b','o.id=b.organid','LEFT')
            ->where('auditstatus','eq','3')
            ->where('vip','eq','0')
            ->where('recommend','eq','1')
            ->where('o.id','GT','1')
            ->limit($limit)
            ->order('sort')
            ->select();
        return $result;
    }
    /**
     * [getArrByid 通过机构id获取机构详情]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function getArrByid(){
        $result = Db::table($this->table.' o')
            ->field('o.imageurl,o.organname,b.summary,b.phone,b.email,o.aboutus')
            ->join('nm_organbaseinfo b','o.id=b.organid','LEFT')
            ->find();
        return $result;
    }
    /**
     * [getConfigData 通过机构id获取机构详情]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function getConfigData(){
        $result = Db::table($this->table)
            ->field('downloadjson')
            ->select();
        return $result;
    }
    public function getOrganid($str){
        $result = Db::table($this->table)
            ->where('vip','eq','1')
            ->where('auditstatus','eq','3')
            ->where('id|domain','eq',$str)
            ->find();
        return $result;
    }
    public function getRoomkey(){
        $result = Db::table($this->table)
            ->field('roomkey')
            ->where('id','eq','1')
            ->find();
        return $result;
    }
}
