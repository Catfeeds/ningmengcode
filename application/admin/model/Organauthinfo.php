<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 机构认证信息表
 * @ jcr
*/
class Organauthinfo extends Model{

    protected $table = 'nm_organauthinfo';
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
    }

   
    /**
     * getOrganAccountId 获取对应机构的基本认证信息
     * @ jcr
     * @return array();
     */
    public function getOrganauthinfoId(){
        return Db::table($this->table)->where('organid',1)
									  ->field('idname,organname,confirmtype')->find();
    }



}
