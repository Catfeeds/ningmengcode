<?php

namespace app\teacher\model;
use think\Db;
use think\User;
use think\Model;
//对学生流水表进行操作

class StudentPaylog extends Model
{
    //
    protected $pk = 'studentid';
    protected $table = 'nm_studentpaylog';

    /**
   * 从数据库获取
   * @Author wangwwy
   * @param $where    array       必填
   * @param $limitstr string      必填
   * @return   array                   [description]
   */
   public function getStudentList($where,$limit){
     return Db::table($this->table)->where($where)->field('paytime')->limit($limit)->order('paytime','desc')->select();
   }

   /**
   * 从数据库获取支付时间
   * @Author wangwwy
   * @param $where    array       必填
   * @param $limitstr string      必填
   * @return   array                   [description]
   */
   public function getStudentpaytime($ordnum){
     return Db::table($this->table)->where('out_trade_no','eq',$ordnum)->field('paytime')->find();
   }




}
