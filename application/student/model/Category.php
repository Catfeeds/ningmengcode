<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 课程分类Model
 * @ yr
*/
class Category extends Model{
    protected $table = 'nm_category';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getCategoryByUid 获取3条排序在前的分类]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getCategoryByUid($studentid=null){
        //如果studentid为空 按排序获取前三条一级分类
        if(empty($studentid)){
            $lists = Db::table($this->table)
                ->field('id as category_id,categoryname,rank,sort')
                ->where('delflag','eq','1')
                ->where('status','eq','1')
                ->order('sort asc')
                ->limit(3)
                ->select();
        }else{
            $lists = Db::table($this->table)
                ->field('id as category_id,categoryname,rank,fatherid,sort')
                ->where('delflag','eq','1')
                ->where('status','eq','1')
                ->order('sort asc')
                ->select();
        }

        $sql = Db::table($this->table)->getLastSql();
        return $lists;
    }
    /**
     * [getCategory 获取分类列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getCategoryBycid($categoryid){
        $lists = Db::table($this->table)
            ->field('id as category_id,categoryname,rank,fatherid,sort,path')
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->where('id','eq',$categoryid)
            ->order('rank,sort asc')
            ->find();
        $sql = Db::table($this->table)->getLastSql();
        return $lists;
    }
    /**
     * [getCategory 获取分类列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getCategory(){
        $lists = Db::table($this->table)
            ->field('id as category_id,categoryname,rank,fatherid,sort,icos,icostwo')
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->order('rank,sort,id asc')
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $lists;
    }
    /**
     * [getTopList 查询一级分类]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getTopList(){
        $lists = Db::table($this->table)
            ->field('id as category_id,categoryname,rank,fatherid,sort,imgs,describe,icos')
            ->where('rank','eq','1')
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->order('sort asc')
            ->select();
        return $lists;
    }
    /**
     * [getTopAndChildList 查询一二级分类]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getTopAndChildList(){
        $lists = Db::table($this->table)
            ->field('id as category_id,categoryname,rank,fatherid,sort')
            ->where('rank','in','1,2')
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->order('sort asc')
            ->select();
        return $lists;
    }
    /**
     * [getChildList 获取下级分类list]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getChildList($category_id){
        $lists = Db::table($this->table)
            ->field('id as category_id ,categoryname,rank,fatherid,sort')
            ->where('fatherid','eq', $category_id)
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->order('sort asc')
            ->select();
        return $lists;
    }
    /**
     * [getRank获取分类等级]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getRank($category_id){
        $lists = Db::table($this->table)
            ->field('rank,fatherid,id')
            ->where('id','eq', $category_id)
            ->where('delflag','eq','1')
            ->where('status','eq','1')
           ->find();
        return $lists;
    }
    /**
     * [get_parent_id 递归查询父级id]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    function get_child_id($cid){
        $pids = '';
        $parentarray =  Db::table($this->table)
            ->where('fatherid','eq',$cid)
            ->where('delflag','eq',1)
            ->where('status','eq',1)
            ->field('id,categoryname,sort')
            ->order('sort')
            ->select();
        $parent_id = $parentarray['id'];
        if( $parent_id != '' ){
            $pids = $parent_id;
            $npids = $this->get_parent_id( $parent_id );
            if(isset($npids))
                $pids = $npids.','.$pids;
        }
        return $pids;
    }
    /**
     * [get_parent_id 递归查询下级id]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    function get_category($cid){
        $category_ids = $cid.",";
        $ids =  Db::table($this->table)->where('fatherid','eq',$cid)->where('delflag','eq','1')->where('status','eq','1')->field('id')->select();
        foreach( $ids as $key => $val )
            $category_ids .= $this->get_category( $val['id'] );
        return $category_ids;
    }
    /**
     * [get_parent_id 递归查询下级分类]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    function getSubs($id, $includeSelf = true,&$ids=[]){
        if($includeSelf) {
            if(!in_array($id, $ids)) {
                array_push($ids, $id);
            }
        }
        $subIds = Db::table($this->table)->where('fatherid','eq',$id)->where('status','eq',1)->where('delflag','eq',1)->column('id');
        $ids = array_unique(array_merge($ids, $subIds));
        foreach($subIds as $sub_id) {
            $this->getSubs($sub_id, $includeSelf,$ids);
        }
        return $ids;
    }

    /**
     * [get_parent_id 递归查询父级id]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    function get_parent_id($cid){
        $pids = '';
        $parentarray =  Db::table($this->table)->where('id','eq',$cid)->field('fatherid')->where('delflag','eq','1')->find();
        $parent_id = $parentarray['fatherid'];
        if($parent_id != '' ){
            $pids = $parent_id;
            $npids = $this->get_parent_id( $parent_id );
            if(isset($npids))
                $pids = $npids.','.$pids;
        }
        return $pids;
    }
    /**
     * 获取对应id的分类名称
     * @ yr
     * @return [type] [description]
     */
    public function getArrId($id){
        $list = Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('id','eq',$id)
            ->field('categoryname,id as category_id')
            ->find();
        return $list?$list:[];
    }
    /**
     * 获取对应id的分类名称
     * @ yr
     * @return [type] [description]
     */
    public function getCategoryname($id){
        $list = Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('id','eq',$id)
            ->field('categoryname,id as category_id')
            ->find();
        return $list?$list:[];
    }
    /**
 * 获取对应id的分类名称
 * @ yr
 * @return [type] [description]
 */
    public function getArrIds($ids){
        $list = Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('id','in',$ids)
            ->select();
        return $list?$list:[];
    }
    /**
     * 获取推荐的分类
     * @ yr
     * @return [type] [description]
     */
    public function getRecommendCid(){
        $list = Db::table($this->table)
            ->field('id as category_id ,categoryname,rank,fatherid,sort')
            ->where('delflag','eq',1)
            ->where('recommend','eq','1')
            ->where('status','eq',1)
            ->order('sort')
            ->select();
        return $list?$list:[];
    }
    /**
     * 根据where条件查找指定字段
     * @ yr
     * @return [type] [description]
     */
    public function getSelectInfo($where,$field){
        $list = Db::table($this->table)
            ->field($field)
            ->where($where)
            ->order('sort asc')
            ->select();
        return $list?$list:[];
    }
}





