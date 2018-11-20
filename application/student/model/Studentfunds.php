<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 学生用户资金表Model
 * @ yr
*/
class Studentfunds extends Model{

    protected $table = 'nm_studentfunds';

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }

    public function __construct(){
    }

    /**
     * [getUserBalance 获取学生账户余额]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function getUserBalance($userid){
        $info  = Db::table($this->table)
            ->field('studentid,usablemoney,totalpay,frozenmoney')
            ->where('studentid','eq',$userid)
            ->find();
        return $info;
    }
    /**
     * [updateData 修改学生资金表信息]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function updateData($userid,$data){
        $where = ['studentid'=>$userid];
        $res = Db::table('nm_studentfunds')->where($where)->update($data);
        return $res;
    }
    /**
     * [delFreezeMoney(混合支付冻结余额]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function delFreezeMoney($data,$where){
        $res = Db::table('nm_studentfunds')->where($where)->update($data);
        return $res;
    }
    /**
     * [delFreezeMoney(混合支付冻结余额]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [userid]        $userid  [学生id]
     * @return   array
     */
    public function updateFrozenOrUsable($where,$price){
        $res = Db::table('nm_studentfunds')->where($where)
            ->exp('usablemoney','usablemoney + '.$price)
            ->exp('frozenmoney','frozenmoney - '.$price)
            ->update();
        return $res;
    }
}







