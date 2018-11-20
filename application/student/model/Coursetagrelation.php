<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 课程Model
 * @ yr
*/
class Coursetagrelation extends Model{

    protected $table = 'coursetagrelation';
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }

    public function __construct(){
        $this->pagenum = config('paginate.list_rows');
    }

    /**
     * 获取对应id的所有标签
     * @ yr
     * @return [type] [description]
     */
    public function getArrId($id){
        $list = Db::name($this->table)
                    ->alias('c')
                    ->join('coursetags t','c.tagid = t.id ')
                    ->where('c.courseid','eq',$id)
                    ->field('t.id,t.tagname')->select();
        return $list?$list:[];
    }

    




}
