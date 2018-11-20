<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 老师信息Model
 * @ yr
*/
class Teacherinfo extends Model{
    protected $table = 'nm_teacherinfo';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * 根据老师id 获取老师对应详细信息
     * @php yr
     * $id 教师id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getTeacherId($id){
        return Db::table($this->table)->where('teacherid','eq',$id)->find();
    }
    /**
     * [getTeachernameByIds 订单表分页后获取教师名称]
     * @Author
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [array]          $arr [教师ids]
     * @return   [type]               [description]
     */
    public function getTeachernameByIds($arr){
        return Db::table($this->table)->where('teacherid','IN',$arr)
            ->column('teacherid,nickname as teachername');
    }
    /**
     * [getCourserList 获取机构推荐老师List]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @return   array
     */
    public function getRecommendList(){
        $lists =Db::table($this->table)
            ->where('recommend','eq','1')
            ->where('delflag','eq',1)
            ->where('accountstatus','eq',0)
            ->field('nickname as teachername,profile,classesnum,imageurl,recommend,teacherid,identphoto,slogan')
            ->order('sortnum desc')
            ->select();
        return  $lists;
    }
    /**
     * 根据teacherid获取 教师的详细信息
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $teachid 教师表teacherid
     * @return   array                [description]
     */
    public function getTeacherData($teachid)
    {
        $field = 'teacherid,imageurl,prphone,mobile,nickname as teachername,addtime,sex,country,province,city,profile,birth,identphoto' ;
        return Db::table($this->table)
            ->field($field)
            ->where('teacherid','eq',$teachid)
            ->where('delflag','eq','1')
            ->find();
    }
    /**
     *  获取所有的老师信息
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $teachid 教师表teacherid
     * @return   array                [description]
     */
    public function getOrganTeacherList($lmitstr)
    {

        $field = 'teacherid,imageurl,prphone,mobile,nickname as teachername,accountstatus,addtime,sex,country,province,city,profile,birth,identphoto,slogan' ;
         $result = Db::table($this->table)
            ->field($field)
            ->where('delflag','eq',1)
            ->order('sortnum')
            ->limit($lmitstr)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $result;

    }
    /**
 *  获取所有的老师总数
 * @Author yr
 * @DateTime 2018-04-25T11:32:53+0800
 * @param  $teachid 教师表teacherid
 * @return   array                [description]
 */
    public function getOrganTeacherCount()
    {
        return Db::table($this->table)
            ->where('delflag','eq',1)
            ->count();
    }
    /**
     *  获取所有的老师总数
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $teachid 教师表teacherid
     * @return   array                [description]
     */
    public function getTeacherName($teacherid)
    {
        $result = Db::table($this->table)
            ->field('nickname')
            ->where('teacherid','eq',$teacherid)
            ->where('delflag','eq',1)
            ->find();
        return $result['nickname'];
    }
    /**
     *  获取所有老师列表
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $limitstr 分页
     * @return   array                [description]
     */
    public function getAllTeacherList($limitstr){
        $field = 'teacherid,imageurl,prphone,mobile,nickname as teachername,addtime,sex,country,province,city,profile,birth,identphoto';
        $result = Db::table($this->table)
            ->field($field)
            ->where('delflag','eq',1)
            ->order('sortnum desc')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     *  获取所有老师数量
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $limitstr 分页
     * @return   array                [description]
     */
    public function getAllTeacherCount(){
        $result = Db::table($this->table)
            ->where('delflag','eq',1)
            ->count();
        return $result;
    }
    /**
     *  模糊搜索老师
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $limitstr 分页
     * @return   array                [description]
     */
    public function searchTeacherList($search,$limitstr){
        $result = Db::table($this->table)
            ->field('teacherid,imageurl,prphone,mobile,nickname as teachername,addtime,sex,country,province,city,profile,birth,identphoto')
            ->where('delflag','eq',1)
            ->where('nickname','like',"%$search%")
            ->order('sortnum desc')
            ->limit($limitstr)
            ->select();
        return $result;
    }
    /**
     *  模糊搜索老师数量
     * @Author yr
     * @DateTime 2018-04-25T11:32:53+0800
     * @param  $limitstr 分页
     * @return   array                [description]
     */
    public function searchTeacherCount($search){
        $result = Db::table($this->table)
            ->field('teacherid,imageurl,prphone,mobile,nickname as teachername,addtime,sex,country,province,city,profile,birth,identphoto')
            ->where('delflag','eq',1)
            ->where('nickname','like',"%$search%")
            ->count();
        return $result;
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









