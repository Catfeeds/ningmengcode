<?php

namespace app\teacher\model;
use think\Db;
use think\Model;
use think\Validate;
use app\admin\model\DbModel;
use app\admin\model\Coursetagrelation;
use app\admin\model\Unit;
use app\admin\model\Period;
//对课程表进行操作


class Curriculum extends Model
{
    protected $pk = 'teacherid';
    protected $table = 'nm_curriculum';
    protected $organid;
    protected $pagenum; //每页显示行数
    protected $field = true;

      // 课程添加验证规则
    // public $rule = [
    //     'categorystr' => 'require',
    //     'coursename'  => 'require|max:20',
    //     'subhead' => 'require|max:100',
    //     'imageurl' => 'require',
    //     // 'describe' => 'require|max:100',
    //     'generalize' => 'require|max:600',
    // ];

    // // 课程添加验证规则
    // public $rule1 = [
    //     'price' => 'number',
    //     'classtypes' => 'require'
    // ];
    // protected $message = [];
    // protected $message1 = [];
    // protected function initialize(){
    //     parent::initialize();
    //     $this->message = [
    //         'categorystr.require' => lang('23012'),
    //         'coursename.require' => '课程名称不能为空',
    //         'coursename.max'     => '课程名称最多不能超过20个汉字',
    //         'subhead.require' => '副标题不能为空',
    //         'subhead.max'     => '副标题最多不能超过100个汉字',
    //         'imageurl.require' => '请上传课程封页',
    //         // 'describe.require' => '适用人群不能为空',
    //         // 'describe.max'     => '适用人群最多不能超过100个汉字',
    //         'generalize.require' => '课程概述不能为空',
    //         'generalize.max'     => '课程概述最多不能超过100个汉字'
    //     ];
    //     $this->message1 = [
    //         'price.number' => '基准价格必须是数字',
    //         'classtypes.require' => '请选择开班类型',
    //     ];
    // }
    // public $message  = [
    //     'categorystr.require' => '请选择分类',
    //     'coursename.require' => '课程名称不能为空',
    //     'coursename.max'     => '课程名称最多不能超过20个汉字',
    //     'subhead.require' => '副标题不能为空',
    //     'subhead.max'     => '副标题最多不能超过100个汉字',
    //     'imageurl.require' => '请上传课程封页',
    //     // 'describe.require' => '适用人群不能为空',
    //     // 'describe.max'     => '适用人群最多不能超过100个汉字',
    //     'generalize.require' => '课程概述不能为空',
    //     'generalize.max'     => '课程概述最多不能超过100个汉字'
    // ];
    // public $message1  = [
    //     'price.number' => '基准价格必须是数字',
    //     'classtypes.require' => '请选择开班类型',
    // ];

    public function __construct(){
            $this->organid = 1;
            $this->pagenum = config('paginate.list_rows');
    }

    /*
     * getId 根据课程id 查询课程详情
     * @ jcr
     * @param $id 课程id
     * @param $field 查询内容 默认不传全部
     * @return array();
     */
     public function getId($id,$field){
         if (!$id) return false;
         return Db::table($this->table)->where(array('id'=>$id))->field($field)->find();
     }


    /**
     * getId 基础查询教师的课程
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @return array();
     */
     protected function getCurriculumList($where,$field,$orderbys='',$pagenum = 1,$pagesize,$findIn=false){
         $pagesize = $pagesize?$pagesize:$this->pagesize;
         $inDb = Db::table($this->table)->page($pagenum,$pagesize)->order($orderbys)->where($where)->field($field);
         if($findIn){
            // 兼容find_in_set
            $inDb = $inDb->where($findIn);
         }
         $lists = $inDb->select();
         return $lists;
     }


    /*
     * getId 根据课程id 查询学习人数
     * @ wangwy
     * @param $id 课程id
     * @param $field 查询内容 默认不传全部
     * @return array();
     */
    public function getStudypeople($where){
      return Db::table($this->table)->where($where)->field('studypeople')->select();
    }



    /*
     * getId 根查询课程表的
     * @ wangwy
     * @param $where 查询条件
     * @return array();
     */
    public function getCurriinfo($where){
      return Db::table($this->table)->where($where)->field('imageurl,coursename,subhead,generalize')->find();
    }


    /**
     * 返回详情只定的字段
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getSelectId($id,$organid){
        return $this->getId($id,$organid,'id,coursename,addtime,subhead,imageurl,price,status,generalize,categoryid,classtypes,categorystr,periodnum,schedule,delflag');
    }


    /**
     * getId 基础查询教师所属的课程（配合getAdminCurriculumList使用）
     * @ wangwy
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @return array();
     */
     // protected function getCurriculumLists($where,$whe,$field,$orderbys='',$limit = 1,$pagenum){
     //     $pagenum = $pagenum?$pagenum:$this->pagenum;
     //     $lists = Db::table($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->where($whe)->field($field)->select();
     //     return $lists;
     // }


      /**
    * getId 查询机构课程列表 数据组装
    * @ jcr
    * @param $where 查询条件
    * @return array();
    */
    public function getAdminCurriculumList($data,$pagesize){
        if (!$data) $data = [];
        $findin = isset($data['classtypes'])?' FIND_IN_SET('.$data['classtypes'].',classtypes) ':'';
        // 过滤数组中的空值 和 没定义的字段
        $where = where_filter($data,array('status','coursename'));
        $field = 'id,imageurl,coursename,price,status,categoryid,categorystr,periodnum';
        $where['delflag'] = 1; //未删除数据
        // 查询列表
        $indata['data'] = $this->getCurriculumList($where,$field,'',$data['pagenum'],$pagesize,$findin);
        // 列表对应总行数
        $indata['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$data['pagenum'],'total'=>count($indata['data'])?$this->getCurriculumCount($where):0);
        return $indata;
        //echo $this->getlastsql();
        //exit();
    }


    /**
     * getId 查询机构课程列表总行数
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @return int;
     */
    public function getCurriculumCount($where){
        $counts = Db::table($this->table)->where($where)->count();
        return $counts;
    }
    /**
     *  @author wyx
     *  @param  $data array 课程id数组
     *
     *
     */
    public function getCurriculumImageById($data){
        return Db::table($this->table)
        ->where('id','IN',$data)
        ->column('id,imageurl');
    }
    


    /*
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserApp($organid,$categoryid,$limitstr){
        $lists = Db::table($this->table)
            ->field('id,imageurl,coursename,subhead')
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->where('organid','eq',$organid)
            ->where('categoryid','IN',$categoryid)
            ->order('id')
            ->limit($limitstr)
            ->select();
        //$sql = db::table($this->table)->getLastSql();
        return  $lists;
    }     
    /**
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserCount($organid,$categoryid){
        $lists = Db::table($this->table)
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->where('organid','eq',$organid)
            ->where('categoryid','IN',$categoryid)
            ->count();
        //$sql = db::table($this->table)->getLastSql();
        return  $lists;
    }



    /*
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAll($type,$organid,$categoryid,$tagids,$limitstr){
        $lists = Db::table($this->table)
            ->field('id,imageurl,coursename,subhead')
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->where('organid','eq',$organid)
            ->where('categoryid','IN',$categoryid)
            ->where($tagids)
            ->where('find_in_set(:type,classtypes)',['type'=>$type])
            ->limit($limitstr)
            ->order('id')
            ->select();
        // $sql = db::table($this->table)->getLastSql();
        // print_r($sql);
        return  $lists;
    } 

    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAllCount($type,$organid,$categoryid,$tagids){
        $lists = Db::table($this->table)
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->where('organid','eq',$organid)
            ->where('categoryid','IN',$categoryid)
            ->where($tagids)
            ->where('find_in_set(:type,classtypes)',['type'=>$type])
            ->count();
        //$sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    public function getAllist($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
   







}
