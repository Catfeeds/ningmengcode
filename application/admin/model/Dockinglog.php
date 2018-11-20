<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 课程单元Model
 * @ jcr
*/
class Dockinglog extends Model{

    protected $table = 'dockinglog';

    //自定义初始化
    protected function initialize(){
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
    }


    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data){
        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','unitname','unitsort','delflag'));
            $ids = Db::name($this->table)->where(['id'=>$data['id']])->update($data);
        }else{
            //添加
            $data = where_filter($data,array('id','dockingurl','content','code'));
            $ids = Db::name($this->table)->insertGetId($data);
        }
        return $ids;
    }





}
