<?php
namespace app\student\model;
use think\Db;
class Curriculum
{
    protected $table = 'nm_curriculum';
    /**
 * 获取
 * @param $tid
 * @return mixed
 */
    public function getCurriculum($tid)
    {

        return Db::table('nm_curriculum a, nm_coursetagrelation b')
            ->field(' id as cid ,coursename,imageurl')
            ->where('b.courseid = a.id')
            ->where(['b.tagid'=>['in',$tid]])
            ->select();
    }
    /**
     * 获取
     * @param cid
     * @return mixed
     */
    public function getCurriculumInfo($cid)
    {

        return Db::table($this->table)
            ->field(' id as cid ,coursename,subhead')
            ->where('id','eq',$cid)
            ->find();
    }
    /**
     * 模糊搜索课程
     * @return mixed
     */
    public function getCurriculumByCname($search,$limitstr)
    {
        return Db::table('nm_curriculum')
            ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,classtypes,giftdescribe,classnum')
            ->where(function ($query) use($search) {
                $query->where('status','eq','1')
                    ->where('delflag','eq','1')
                    ->where('classtypes','eq','2')
                    ->where('coursename','like',"%$search%")
                    ->where('classnum','neq','0');
            })->whereOr(function ($query) use($search) {
                $query->where('status','eq',1)
                    ->where('delflag','eq','1')
                    ->where('coursename','like',"%$search%")
                    ->where('classtypes','eq','1');
            })
            ->order('id desc')
            ->limit($limitstr)
            ->select();
    }
    /**
     * 模糊搜索课程
     * @return mixed
     */
    public function getCurriculumByCnameCount($search)
    {
        return Db::table('nm_curriculum')
            ->where(function ($query) use($search) {
                $query->where('status','eq','1')
                    ->where('delflag','eq','1')
                    ->where('classtypes','eq','2')
                    ->where('coursename','like',"%$search%")
                    ->where('classnum','neq','0');
            })->whereOr(function ($query) use($search) {
                $query->where('status','eq',1)
                    ->where('delflag','eq','1')
                    ->where('coursename','like',"%$search%")
                    ->where('classtypes','eq','1');
            })
            ->count();
    }
    /**
     * @return mixed
     */
    public function getAll()
    {
        return Db::table('nm_curriculum')
            ->field('id as cid ,coursename,imageurl')
            ->select();
    }

    /**获取当前层级所有的ID
     * @param $id
     * @return mixed
     */

    public function getOne($id)
    {
        return Db::table('nm_curriculum')
            ->where(['categoryid'=>['in',$id]])
            ->field('id as cid ,coursename,imageurl')
            ->select();

    }
    /**
     * 返回详情只定的字段
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getSelectId($id){
        return $this->getId($id,'id,coursename,addtime,subhead,imageurl,price,status,generalize,identifying,categoryid,classtypes,categorystr,periodnum,schedule,delflag');
    }
    /**
     * getId 根据课程id 查询课程详情
     * @ jcr
     * @param $id 课程id
     * @param $field 查询内容 默认不传全部
     * @return array();
     */
    public function getId($id,$field){
        if (!$id) return false;
        $res =  Db::table($this->table)->field($field)->find();
        return $res;
    }
    /**
     * getRecommendList 查询课程推荐列表
     * @ yr
     * @return array();
     */
    public function getRecommendList(){
        $result = Db::table($this->table)
            ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
            ->where(function ($query)  {
                $query->where('recommend','eq','1')
                    ->where('status','eq','1')
                    ->where('delflag','eq','1')
                    ->where('classtypes','eq','2')
                    ->where('classnum','neq','0');
            })->whereOr(function ($query) {
                $query->where('status','eq',1)
                    ->where('recommend','eq','1')
                    ->where('status','eq','1')
                    ->where('delflag','eq','1')
                    ->where('classtypes','eq','1');
            })
            ->order('currsort')
            ->limit(4)
            ->select();
        return $result;
    }
    /**
     * getCourserById 查询课程详情
     * @ yr
     * @return array();
     */
    public function getCourserById($where){
        $result = Db::table($this->table.' c')
            ->field('c.id as courseid,c.coursename,c.subhead,c.imageurl,c.price,c.maxprice,c.classtypes,c.describe,c.generalize,c.identifying,c.classhour,c.teacherid,c.categoryid,c.giftdescribe,t.nickname as teachername,c.classnum,c.categorystr,c.periodnum,c.giftstatus,giftjson')
            ->join('nm_teacherinfo t','c.teacherid=t.teacherid','LEFT')
            ->where($where)
            ->find();
        return $result;
    }
    /**
     * getCourserById 查询课程详情
     * @ yr
     * @return array();
     */
    public function getRecommendCourserById($where){
        $result = Db::table($this->table.' c')
            ->field('c.id as courseid,c.coursename,c.subhead,c.imageurl,c.price,c.maxprice,c.classtypes,c.describe,c.identifying,c.classhour,c.teacherid,c.categoryid,t.nickname as teachername,c.classnum,c.categorystr,c.periodnum')
            ->join('nm_teacherinfo t','c.teacherid=t.teacherid','LEFT')
            ->where($where)
            ->order('currsort desc')
            ->select();
        return $result;
    }
    /**
     * getSelectInfo查询课程详情
     * @ yr
     * @return array();
     */
    public function getSelectData($where){
        $result = Db::table($this->table.' c')
            ->field('c.id as courseid,c.coursename,c.subhead,c.imageurl,c.price,c.maxprice,c.classtypes,c.describe,c.generalize,c.identifying,c.classhour,c.teacherid,c.categoryid,c.giftdescribe,t.nickname as teachername,c.classnum,c.categorystr,c.periodnum,c.giftstatus,giftjson')
            ->join('nm_teacherinfo t','c.teacherid=t.teacherid','LEFT')
            ->where($where)
            ->order('id desc')
            ->select();
        return $result;
    }
    /**
     * [getCourserAllList 获取所有课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllList($isfree,$limitstr,$coursetype){
        $where = [];
        if($isfree == 1){
            $where['delflag'] = 1;
            $where['status'] = 1;
            $where['price'] = '0.00';
        }else{
            $where['delflag'] = 1;
            $where['status'] = 1;
        }
        if(!empty($coursetype)){
            $where['classtypes'] = $coursetype;
            if($coursetype == 1){
                $where['classnum'] = 0;
            }else{
                $where['classnum'] = ['neq',0];
            }
            $lists = Db::table($this->table)
                ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
                ->where($where)
                ->order('id desc')
                ->limit($limitstr)
                ->select();

        }else{
            $lists = Db::table($this->table)
                ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
                ->where(function ($query) use($where)  {
                    $query->where('classnum','neq','0')
                        ->where('classtypes','eq',2)
                        ->where($where);

                })->whereOr(function ($query) use($where){
                    $query->where($where)
                        ->where('classnum','eq','0')
                        ->where('classtypes','eq',1);
                    /*   ->where('classtypes','eq','1');*/
                })
                ->order('id desc')
                ->limit($limitstr)
                ->select();
        }
        return  $lists;
    }
    /**
     * [getCourserAllList 获取全部课程的数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr  [分页]
     * @return   array
     */
    public function getCourserAllCount($isfree,$coursetype){
        $where = [];
        if($isfree == 1){
            $where['delflag'] = 1;
            $where['status'] = 1;
            $where['price'] = '0.00';
        }else{
            $where['delflag'] = 1;
            $where['status'] = 1;
        }
        if(!empty($coursetype)){
            $where['classtypes'] = $coursetype;
            if($coursetype == 1){
                $where['classnum'] = 0;
            }else{
                $where['classnum'] = ['neq',0];
            }
            $lists = Db::table($this->table)
                ->where($where)
                ->count();

        }else{
            $lists =Db::table($this->table)
                ->where(function ($query) use($where)  {
                    $query->where('classnum','neq','0')
                        ->where('classtypes','eq',2)
                        ->where($where);
                    /* ->where('classtypes','eq','2');*/
                })->whereOr(function ($query) use($where){
                    $query->where($where)
                        ->where('classnum','eq','0')
                        ->where('classtypes','eq',1);
                    /*->where('classtypes','eq','1');*/
                })
                ->count();
        }
        return  $lists;
    }
    /**
     * [getFilterCourserList 按分类筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserList($isfree,$categoryid,$limitstr,$coursetype){
        $where = [];
        if($isfree == 1){
            $where['delflag'] = 1;
            $where['status'] = 1;
            $where['price'] = '0.00';
        }else{
            $where['delflag'] = 1;
            $where['status'] = 1;
        }
        if(!empty($coursetype)){
            $where['classtypes'] = $coursetype;
            if($coursetype == 1){
                $where['classnum'] = 0;
            }else{
                $where['classnum'] = ['neq',0];
            }
            $lists = Db::table($this->table)
                ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
                ->where($where)
                ->where('categoryid','in',$categoryid)
                ->order('id desc')
                ->limit($limitstr)
                ->select();

        }else{
            $lists = Db::table($this->table)
                ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
                ->where(function ($query) use($where,$categoryid)  {
                    $query->where('classnum','neq','0')
                        ->where($where)
                        ->where('classtypes','eq',2)
                        ->where('categoryid','in',$categoryid);
                })->whereOr(function ($query) use($where,$categoryid){
                    $query->where($where)
                        ->where('classnum','eq','0')
                        ->where('classtypes','eq',1)
                        ->where('categoryid','in',$categoryid);
                })
                ->order('id desc')
                ->limit($limitstr)
                ->select();
        }
        return  $lists;
    }
    /**
     * [getFilterCourserList 按分类筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserCount($isfree,$categoryid,$coursetype){
        $where = [];
        if($isfree == 1){
            $where['delflag'] = 1;
            $where['status'] = 1;
            $where['price'] = '0.00';
        }else{
            $where['delflag'] = 1;
            $where['status'] = 1;
        }
        if(!empty($coursetype)){
            $where['classtypes'] = $coursetype;
            if($coursetype == 1){
                $where['classnum'] = 0;
            }else{
                $where['classnum'] = ['neq',0];
            }
            $lists = Db::table($this->table)
                ->where('categoryid','in',$categoryid)
                ->where($where)
                ->count();

        }else{
            $lists = Db::table($this->table)
                ->where(function ($query) use($where,$categoryid)  {
                    $query->where('classnum','neq','0')
                        ->where($where)
                        ->where('classtypes','eq',2)
                        ->where('categoryid','in',$categoryid);
                })->whereOr(function ($query) use($where,$categoryid){
                    $query->where($where)
                        ->where('categoryid','in',$categoryid)
                        ->where('classtypes','eq',1)
                        ->where('classnum','eq','0');
                })
                ->count();
        }
        return  $lists;
    }
    /**
     * [getTeacherList 获取指定老师课程List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getTeacherList($where,$teacherid){
        $lists = Db::table($this->table)
                ->field('id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum')
                ->where(function ($query) use($where,$teacherid) {
                   $query->where('classtypes','eq','1')
                       ->where('status','eq',1)
                       ->where('delflag','eq',1)
                       ->where('teacherid','eq',$teacherid)
                       ->where($where);
               })->whereOr(function ($query) use($where,$teacherid) {
                     $query->where('status','eq',1)
                    ->where('classnum','neq','0')
                    ->where('delflag','eq',1)
                     ->where('classtypes','eq','2')
                    ->where($where)
                    ->where('id','IN',function($query) use($teacherid){
                    $query->table('nm_scheduling')->where('teacherid','EQ',$teacherid)->field('curriculumid');
                });
                })
                ->order('id desc')
                ->select();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getTeacherCount 获取指定老师课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getTeacherCount($where,$teacherid){
        $lists = Db::table($this->table)
            ->where(function ($query) use($where,$teacherid) {
                $query->where('classtypes','eq','1')
                    ->where('status','eq',1)
                    ->where('delflag','eq',1)
                    ->where('teacherid','eq',$teacherid)
                    ->where($where);
            })->whereOr(function ($query) use($where,$teacherid) {
                $query->where('status','eq',1)
                    ->where('classnum','neq','0')
                    ->where('delflag','eq',1)
                    ->where('classtypes','eq','2')
                    ->where($where)
                    ->where('id','IN',function($query) use($teacherid){
                        $query->table('nm_scheduling')->where('teacherid','EQ',$teacherid)->field('curriculumid');
                    });
            })
            ->count();
        return  $lists;
    }
    /**
     * [getSelectInfo 根据where条件查询]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getSelectInfo($where,$field){
        $lists = Db::table($this->table)
                ->field($field)
              ->where(function ($query) use($where) {
                $query->where('classtypes','eq','1')
                    ->where('status','eq',1)
                    ->where('delflag','eq',1)
                    ->where($where);
             })->whereOr(function ($query) use($where) {
                $query->where('status', 'eq', 1)
                    ->where('classnum', 'neq', '0')
                    ->where('delflag', 'eq', 1)
                    ->where('classtypes', 'eq', '2')
                    ->where($where);
           })
            ->order('id asc')
            ->select();
        return  $lists;
    }
    /**
     * [getSelectInfo 根据where条件查询]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function getSelectInfos($where,$field){
        $lists = Db::table($this->table)
            ->field($field)
            ->where('status', 'eq', 1)
            ->where('classnum', 'neq', '0')
            ->where('delflag', 'eq', 1)
            ->where('classtypes', 'eq', '2')
            ->where($where)
            ->order('id asc')
            ->limit('0,5')
            ->select();
        return  $lists;
    }
    /**
     * [isdelflag 查看指定id的数据是否删除]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $teacher  [老师id]
     * @return   array
     */
    public function isdelflag($where){
        $result = Db::table($this->table)->where($where)->find();
        return $result;
    }
}
