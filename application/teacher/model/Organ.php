<?php

namespace app\teacher\model;
use think\Db;
use think\Model;

class Organ extends Model
{
    //
    protected $table = 'nm_organ';
    public function getlogo($organid){
    	return Db::table($this->table)
    	           ->field('imageurl,organname')
    	           ->where('id','eq',$organid)
    	           ->find();
    	//print_r(Db::table($this->table)->getlastsql());
    }

    /**
     * 获取对应的
     * @Author jcr
     * @param $organid   所属机构id
     * @return array
     *
     */
    public function getOrganid($organid=1){
        $where  = [
            'id' => $organid ,
        ] ;
        return Db::table($this->table)->field('id,organname,roomkey,classhours')->where($where)->find() ;

    }


    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data,$organid){
        //修改
        $data = where_filter($data,array('toonetime','smallclasstime','bigclasstime','regionprefix','maxclass','minclass','roomkey'));
        $ids = Db::table($this->table)->where('organid','eq',$this->organid)->update($data);
        return $ids;
    }

    /**
     * [getOrganmsgById 根据机构id获取机构信息]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @param    [type]                   $organid [description]
     * @return   [type]                            [description]
     */
    public function getRoomkey($organid = 1){
        $field = 'roomkey' ;
        return Db::table($this->table)
            ->field($field)
            ->where('id','eq',$organid)
            ->find() ;
    }
    /*
     * 批量获取机构名称
     * @Author WangWY
     */
    public function getOrganname($arr){
        return Db::table($this->table)
            ->where('id','in',$arr)
            ->column('organname','id');
    }
}
