<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
class Lessons extends Model
{
    protected $table = 'nm_lessons';
    protected $organid;
    protected $pagenum; //每页显示行数

    // 课程添加验证规则
    protected $rule = [
        'periodname'  => 'require',
        'courseware'  => 'require',

    ];
    protected $message = [];
    protected function initialize() {
        parent::initialize();
        $this->message = [
            'periodname.require' => lang('23013'),
            'courseware.require' => lang('23014')
        ]; 
    }

    public function __construct(){
        //$this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
    }

    /**
     * getId 根据课时id
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
     * getId 基础查询课时
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @return array();
     */
     protected function getCurriculumList($where,$field,$orderbys='',$limit = 1,$pagenum){
         $pagenum = $pagenum?$pagenum:$this->pagenum;
         $lists = Db::table($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->field($field)->select();
         return $lists;
     }


     /**
     * getLists 根据课时单元查询课时
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getLists($data){
        $where = where_or($data,'unitid','eq');
        $lists = Db::table($this->table)
                            ->where($where)
                            ->where('organid','eq',$data['organid'])
                            ->where('delflag','eq',1)
                            ->order('periodsort desc')
                            ->field('id,periodname,periodsort,courseware,unitid')
                            ->select();
        return $lists;
     }



     /**
     * getLists 根据课时单元查询课时 和课时对应时间
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getInLists($id,$teacherid){
        $lists = Db::table($this->table)->alias('u')
                            ->join('nm_toteachtime t',' u.id = t.lessonsid ','LEFT')
                            ->where('u.schedulingid','eq',$id)
                            ->where('t.type','neq',1)
                            ->where('u.teacherid','eq',$teacherid)
                            ->where('u.delflag','eq',1)
                            ->order('u.periodsort asc')
                            ->field('u.id,u.periodname,u.periodsort,u.courseware,u.unitid,t.intime,t.timekey,t.teacherid')->select();
        //print_r(Db::table($this->table)->getlastsql());
        return $lists;
     }


     /**
     * getIdsLists 根据课程查询课时
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getIdsLists($id,$organid){
        $where['curriculumid'] = $id;
        $lists = Db::table($this->table)
                            ->where($where)
                            ->where('organid','eq',$organid)
                            ->where('delflag','eq',1)
                            ->order('periodsort desc')
                            ->field('id,periodname,periodsort,courseware,unitid')->select();
        return $lists;
     }


    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
     public function addEdit($data,$type=''){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)&&$type!='all'){
            return array('code'=>10050,'info'=>$validate->getError());
        }

        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','periodname','periodsort','courseware','delflag'));
            $ids = Db::table($this->table)->where('id','eq',$data['id'])->update($data);
        }else{
            //添加
            if($type=='all'){
                //批量插入
                $ids = Db::table($this->table)->insertAll($data);
            }else{
                //$data['organid'] = $this->organid;
                $data = where_filter($data,array('id','periodname','periodsort','courseware','curriculumid','unitid','organid','schedulingid','teacherid'));
                $ids = Db::table($this->table)->insertGetId($data);
            }
        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10051,'info'=>'添加课程单元异常');
     }


    /**
     * 删除课时
     */
     public function deleteIds($where){
        return Db::table($this->table)->where($where)->update(['delflag'=>0]);
     }

     /**
     * [获取该课时对应的courseware]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
     public function getCourseware($whe){
        return Db::table($this->table)
                   ->where($whe)
                   ->field('courseware')
                   ->find();
         //print_r(Db::table($this->table)->getLastSql());
     }


     /**
     * [删除或者添加该课时对应的courseware添加相应的fileid]
     * @Author wangwy
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $organid   [机构id]
     * @param    [int]        $teacherid [教师id]
     * @return   [type]                  [description]
     */
     public function upCourseware($whe,$courseware){
        return Db::table($this->table)
                   ->where($whe)
                   ->setField('courseware',$courseware);
         //print_r(Db::table($this->table)->getlastsql());

     }
     /**
      * 根据课节id 获取课节指定字段
      * @php yr
      * $id learnsid
      * @$field 查询字段
      * @return [type] [description]
      */
     public function getFieldName($id,$field){
         return Db::table($this->table)->where('id','eq',$id)->field($field)->find();
         //print_r(Db::table($this->table)->getLastSql());
     }
    /*
    * getLists 根据课时单元查询课时 和课时对应时间
    * @ jcr
    * @param $where 查询
    * @return array();
    */
    public function getAllInLists($id,$teacherid){
        $lists = Db::table($this->table)->alias('u')
            ->where('schedulingid','eq',$id)
            //->where('t.type','eq',1)
            ->where('teacherid','eq',$teacherid)
            ->where('delflag','eq',1)
            ->order('periodsort asc')
            ->field('id,periodname,periodsort,courseware,unitid')
            ->select();
        //print_r(Db::table($this->table)->getlastsql());
        return $lists;
    }

    /**
     * @param $where
     * @param $field
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllfind($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->field($field)
            ->find();
    }
    public function getAllcolumn($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
    public function getPeriod($where,$field){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_period u','c.periodid = u.id')
            ->where($where)
            ->column($field);
    }

    
}
