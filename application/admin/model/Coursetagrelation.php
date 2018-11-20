<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 课程Model
 * @ jcr
*/
class Coursetagrelation extends Model{

    protected $table = 'coursetagrelation';
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
    }

    /**
     * getId 基础查询机构课程
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @return array();
     */
     protected function getCurriculumList($where,$field,$orderbys='',$limit = 1,$pagenum){
         $pagenum = $pagenum?$pagenum:$this->pagenum;
         $lists = Db::name($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->field($field)->select();
         return $lists;
     }


    /**
     * 获取对应id的所有标签
     * @ jcr
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

    /**
     * 获取对应id的所有标签 id
     * @ jcr
     * @return [type] [description]
     */
    public function getArrListId($id){
        $list = Db::name($this->table)
                    ->where('courseid','eq',$id)
                    ->field('courseid,tagid')->select();
        return $list?$list:[];
    }
    
    
   
    /**
     * 课程标签关联表批量数据插入
     * @ jcr
     * @param $data 添加数据源
     * return int 返回受影响行数
     */
    public function addAll($data){
        if(!$data) return false;
        $ids = Db::name($this->table)->insertAll($data);
        return $ids;
    }

    
    /**
     * 删除标签
     */
    public function deleteIds($where){
        return Db::name($this->table)->where($where)->delete();
    }




}
