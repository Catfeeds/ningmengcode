<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
//对课程评价表进行操作

class Coursecomment extends Model
{
    //
    protected $pk    = 'allaccountid';
	  protected $table = 'nm_coursecomment';
    protected $pagenum;//第几页
    //protected $id;

    /*
	 * 根据teacherid，教师的课程评价信息content,
	 * @Author wyx
     * @param  $allacountid   教师所在表的 id
     * @param $limitstr string      必填
	 * @return   array                   [description]
	 */
    public function getCommentList($allaccountid,$limitstr)
    {
        return Db::table($this->table)
                  ->where('allaccountid','eq',$allaccountid)
                  ->field('content,addtime,nickname,score')
                  ->limit($limitstr)->order('addtime', 'desc')->select();
    }


    /**
    * 根据teacherid，获取评价和排课表的班级名称,
    * @Author wangwy
    * @where  $where   查询条件
    * @param $limitstr string      必填
    * @return   array                   [description]
    */
    public function getCommentListb($where,$pagenum,$pagesize)
    {
        $pagenum = $pagenum?$pagenum:$this->pagenum;
        $id = $where['c.allaccountid'];
        $data['data'] = Db::table($this->table)
                  ->alias('c')
                  ->join('nm_scheduling t','c.schedulingid = t.id','LEFT')
                  ->join('nm_studentinfo s','c.studentid =s.id','LEFT')
                  ->join('nm_curriculum m','c.curriculumid = m.id','LEFT')
                  ->where($where)
                  ->page($pagenum,$pagesize)
                  ->field('c.content,c.addtime,c.nickname,c.score,s.imageurl,m.coursename,t.gradename,c.classtype')
                  ->order('c.addtime', 'desc')->select();
         // print_r(Db::table($this->table)->getlastsql());
         $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getListCount($where));
         return $data;
    }

       /**
    * 根据teacherid，获取所有评分
    * @Author wangwy
    * @where  $where   查询条件
    * @param $limitstr string      必填
    * @return   array                   [description]
    */
    public function getComScore($where)
    {
        $data = Db::table($this->table)
                  ->alias('c')
                  ->join('nm_scheduling t','c.schedulingid = t.id')
                  ->join('nm_studentinfo s','c.studentid =s.id')
                  ->join('nm_curriculum m','c.curriculumid = m.id')
                  ->where($where)
                  ->column('c.score');
         return $data;
    }




   /**
    * getId 查询对应课程评论数量
    * @ jcr
    * @param $where 查询条件
    * @return array();
    */
   public function getListCount($where){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_scheduling t','c.schedulingid = t.id','LEFT')
            ->join('nm_studentinfo s','c.studentid =s.id','LEFT')
            ->join('nm_curriculum m','c.curriculumid = m.id','LEFT')
            ->where($where)
            ->count();
   }
   /**
    * getId 查询对应课程评论数量
    * @ wangwy
    * @param $where 查询条件
    * @return array();
    */
   public function getcommentCount($where){
        return Db::table($this->table)
                            ->where($where)
                            ->count();
   }


   /*
    * 根据teacherid，教师的课程评价信息,
    * @Author wyx
    * @param  $allacountid   教师所在表的 id
    * @param $limitstr string      必填
    * @return   array                   [description]
    **/
   public function getComment($allaccountid)
   {
       return Db::table($this->table)
                  ->where('allaccountid','eq',$allaccountid)
                  ->field('studentid,allaccountid,curriculumid,score')
                  ->select();
   }


  /*
   * 根据teacherid，教师的课程评价信息,
   * @Author wangwy
   * @param  $where   查询条件
   * @param $paenum   当前第几页 必填
   * @param $pagesize 每页多少行 必填
   * @return   array                   [description]
   */
   public function getperComment($where,$pagenum,$pagesize)
   {
       $pagenum = $pagenum?$pagenum:$this->pagenum;
       //$id = $where['c.allaccountid'];
       $data['data'] = Db::table($this->table)->alias('c')
                  ->join('nm_studentinfo t','c.studentid = t.id')
                  ->where($where)
                  ->field('t.imageurl,t.nickname,c.studentid,c.allaccountid,c.content,c.score,c.addtime')
                  ->page($pagenum,$pagesize)
                  ->select();
       $data['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getpercommentCount($where));
        return $data;
   }

   /*
    * getId 查询对应课程评论数量
    * @ WangWY
    * @param $where 查询条件
    * @return array();
    */
    public function getpercommentCount($where){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_studentinfo t','c.studentid = t.id')
            ->where($where)
            ->count();
    }


   /*
    * 根据teacherid，教师的课程评价信息content,
    * @Author wyx
    * @param  $allacountid   教师所在表的 id
    * @param $limitstr string      必填
    * @return   array                   [description]
    */
    public function getappCommentList($allaccountid,$organid,$limitstr)
    {
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_studentinfo t','c.studentid = t.id')
            ->where('c.allaccountid','eq',$allaccountid)
            ->where('c.organid','eq',$organid)
            ->field('c.content,c.addtime,c.nickname,c.score,t.imageurl')
            ->limit($limitstr)
            ->order('c.addtime', 'desc')
            ->select();
    }




}
