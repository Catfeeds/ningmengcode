<?php
namespace app\teacher\model;
use think\Db;
use think\Model;
//操作订单管理表

class OrderManage extends Model
{
    protected $pk = 'teacherid';
    protected $table = 'nm_ordermanage';
    public function __construct(){
        //$this->organid = 1;
    }



    /*
     * [getStudentOrder 根据学生id和机构id来获取订单列表]
     * @Author wanwy
     * @DateTime 2018-04-20T14:28:00+0800
     * @param    [int]     $organid   [description]
     * @param    [int]     $studentid [description]
     * @return   [array]              [学生的订单已经成功付款过]
     *
     */
    public function getStudentOrder($studentid,$pagenum,$pagesize){
        $field = 't.imageurl,c.coursename,c.classname,c.orderstatus,s.teachername,s.nickname,c.paytype,c.originprice,c.discount,c.amount,c.ordernum,c.ordertime' ;
        $list['data'] = Db::table($this->table)
            ->alias('c')
            ->join('nm_teacherinfo s','c.teacherid = s.teacherid ','LEFT')
            ->join('nm_scheduling t','t.id=c.schedulingid','LEFT')
            ->where('c.studentid','eq',$studentid)
            ->where('c.orderstatus','EGT',0)
            ->where('coursetype','eq',2)
            ->page($pagenum,$pagesize)
            ->field($field)
            ->select() ;
        $list['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=> $this->getStudentOrdercount($studentid));
        return $list;
        //print_r(Db::table($this->table)->getlastsql());
    }
    public function getStudentOrdercount($studentid){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_teacherinfo s','c.teacherid = s.teacherid ','LEFT')
            ->join('nm_scheduling t','t.id=c.schedulingid','LEFT')
            ->where('c.studentid','eq',$studentid)
            ->where('c.orderstatus','EGT',0)
            ->where('coursetype','eq',2)
            ->count();
    }



    /*
     * [getOrderStu 根据教师id，机构id，课程id获取学生id，并在nm_studentinfo中查询数据]
     * @Author wanwy
     * @DateTime 2018-04-20T14:28:00+0800
     * @param    [int]     $organid   [description]
     * @param    [int]     $studentid [description]
     * @param    [int]     $curriculumid [description]
     * @return   [array]              [学生的订单已经成功付款过]
     *
     */
    public function getOrderStu($teacherid,$organid,$curriculumid){
        $field = 's.nickname,s.imageurl' ;
        return Db::table($this->table)->alias('c')
            ->join('nm_studentinfo s','c.studentid = s.id ')
            ->where('c.teacherid','eq',$teacherid)
            ->where('c.orderstatus','GT',1)
            ->where('c.organid','eq',$organid)
            ->where('c.curriculumid','eq',$curriculumid)
            ->where('coursetype','eq',2)
            ->field($field)
            ->find();
        //print_r(Db::table($this->table)->getlastsql());
    }



    /**
     * [getPayOrderCount 查询对应的班多少人参加了]
     * @Author jcr
     * @DateTime 2018-04-21T09:54:41+0800
     * @param    [int]     $curriculumid   [description]
     * @return   [array]        [购买次数]
     */
    public function getPaySchedulingCount($schedulingid){
        return Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->where('orderstatus','gt',1)
            ->where('coursetype','eq',2)
            ->count();

    }




    /**
     * [getPayOrderCount 获取课程已购买次数]
     * @Author jcr
     * @DateTime 2018-04-21T09:54:41+0800
     * @param    [int]     $schedulingid   [开课表id]
     * @return   [array]        [购买次数]
     */
    public function getSchedulingIdCount($schedulingid){
        return Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->where('orderstatus','gt',1)
            ->where('coursetype','eq',2)
            ->count();
    }



    /*
     * [getOrderStu 根据教师id，机构id，课程id获取学生id，并在nm_studentinfo中查询数据]
     * @Author wanwy
     * @DateTime 2018-04-20T14:28:00+0800
     * @param    [int]     $organid   [description]
     * @param    [int]     $studentid [description]
     * @param    [int]     $curriculumid [description]
     * @return   [array]              [学生的订单已经成功付款过]
     *
     */
    public function getstudentlist($teacherid,$limitstr){
        $field = 'c.ordertime,c.coursename,s.nickname';
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_studentinfo s','c.studentid = s.id ','LEFT')
            ->where('c.teacherid','eq',$teacherid)
            ->where('c.orderstatus','eq',20)
            ->where('coursetype','eq',2)
            ->field($field)
            ->limit($limitstr)
            ->select();
        //print_r(Db::table($this->table)->getlastsql());

    }


    /**
     * [getOrderList 获取订单列表]
     * @Author wyx
     * @DateTime 2018-04-21T09:54:41+0800
     * @return   [array]        [返回订单列表数组]
     */
    public function getOrderList($where,$limitstr){
      return Db::table($this->table)
          ->where($where)
          ->field('ordernum,organid,curriculumid,orderstatus,ordertime,studentid,paytype,amount,coursename,teacherid')
          ->limit($limitstr)
          ->order('teacherid','asc')
          ->select();
    }
    /**
     * [getOrderListCount 获取订单列表总行数]
     * @Author wyx
     * @DateTime 2018-04-21T09:54:41+0800
     * @return   [array]        [返回订单列表数组]
     */
    public function getOrderListCount($where){
      return Db::table($this->table)->where($where)->count();
    }


   /**
    * 获取该教师的所有订单学生id
    */
   public function getStudentnum($teacherid){
       return Db::table($this->table)
                   ->where('teacherid','eq',$teacherid)
                   ->column('studentid');
   }

   /**
    * 获取该教师该课程的所有订单学生id
    */
   public function getStudenAlllist($where){
       return Db::table($this->table)
                   ->where($where)
                   ->field('studentid')
                   ->select();
   }


}
