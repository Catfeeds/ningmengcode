<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 机构收藏Model
 * @ yr
*/
class Organcollection extends Model{
    protected $table = 'nm_orgaincollection';
    protected $rule = [
        'studentid' => 'require',
        'organid'   => 'require',
    ];
    protected $message = [];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'studentid.require' => lang('37106'),
            'organid.require' => lang('37107'),
        ];
    }
    /**
     * [add 插入收藏数据]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           studentid  学生id
     * @param    [int]              organid  机构id
     * @return   array
     */
    public function add($data){
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',-1,$validate->getError());
        }else{
            $data['addtime'] = time();
            $data = where_filter($data,array('studentid','organid','addtime'));
            $id = Db::table($this->table)->insertGetId($data);
        }
        return $id?$id:0;
    }
    /**
     * [delete 删除数据]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           studentid  学生id
     * @param    [int]              organid  机构id
     * @return   array
     */
    public function deleteData($where){
        $result = Db::table($this->table)
            ->where($where)
            ->update(['delflag'=>0]);
        return $result;
    }
    /**
     * [add 获取收藏数据]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           studentid  学生id
     * @param    [int]              organid  机构id
     * @return   array
     */
    public function getDataInfo($where,$field){
       $result = Db::table($this->table)
           ->field($field)
           ->where($where)
           ->where('delflag','eq',1)
           ->find();
        return $result;
    }
    /**
     * [getCollectList 获取收藏的数据]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           studentid  学生id
     * @param    [int]              organid  机构id
     * @return   array
     */
    public function getCollectList($studentid,$limitstr){
        $result = Db::table($this->table.' c')
            ->field('c.organid,o.organname,o.imageurl,b.summary')
            ->join('nm_organ o','c.organid=o.id','LEFT')
            ->join('nm_organbaseinfo b','c.organid=b.organid','LEFT')
            ->where('c.studentid','eq',$studentid)
            ->where('c.delflag','eq','1')
            ->order('c.addtime')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     * [getCollectCount 获取收藏的总记录数]
     * @Author yr
     * @DateTime 2018-04-27T13:58:56+0800
     * @param    [string]           studentid  学生id
     * @param    [int]              organid  机构id
     * @return   array
     */
    public function getCollectCount($studentid){
        $result = Db::table($this->table.' c')
            ->where('c.studentid','eq',$studentid)
            ->where('c.delflag','eq','1')
            ->count();
        return $result;
    }
}







