<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 学生地址表用户Model
 * @ yr
*/
class Studentaddress extends Model{
    protected $table = 'nm_studentaddress';
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    /**
     * [addUserAddress 添加学生信息]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
    public function addOrUpdateAddress($data)
    {
        if($data['isdefault'] == 1){
            $update_data = [
                'isdefault' => '0',
            ];
            Db::table($this->table)->where('studentid','eq',$data['studentid'])->update($update_data);
        }
        if(empty($data['id'])){
            //插入学生信息
            $result =  Db::table($this->table)->insert($data);
        }else{
            //修改学生信息
            $result = Db::table($this->table)->update($data);
        }

        return $result;

    }
    /**
     * [getAddressList 查询用户收货地址列表]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $studentid [要入库的用户信息]
     * @return   [array]
     */
    public function getAddressList($studentid)
    {
        $field = 'studentid,pid,cityid,areaid,address,zipcode,linkman,mobile,isdefault,id';
        $result =  Db::table($this->table)
            ->field($field)
            ->where('studentid','eq',$studentid)
            ->where('delflag','eq','0')
            ->select();
        return $result;

    }
    /**
     * [getAddressCount 查询用户添加收货地址的数量]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
    public function getAddressCount($studentid)
    {
        //插入学生信息
        $result =  Db::table($this->table)
            ->where('studentid','eq',$studentid)
            ->where('delflag','eq','0')
            ->count();
        return $result;

    }
    /**
     * [deleteAddress 删除地址]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @return   [array]
     */
    public function deleteAddress($id)
    {
        $data['delflag'] = 1;
        $result =  Db::table($this->table)->where('id','eq',$id)->update($data);
        return $result;

    }
}








