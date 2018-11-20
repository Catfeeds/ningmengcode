<?php
namespace app\apphjx\model;
use think\Model;
use think\Db;
use think\Validate;
use app\student\model\Accessroleuser;
/*
 * 学生用户Model
 * @ yr
*/
class Hjxappstudentinfo extends Model{
    protected $table = 'nm_hjxappstudentinfo';
    protected $rule = [
        'nickname' => 'require|max:30',
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'nickname.require' => lang('37004'),
            'nickname.max' => lang('37005'),
            'imageurl.require'       => lang('37007'),
        ];
    }
    /**
     * [checkLogin 手机获取学生用户信息]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [vachar]        $mobile  [手机号]
     * @return   array
     */
    public function checkLogin($mobile){
        $where['mobile'] =  $mobile;
        $lists = Db::table($this->table)->where($where)->where('delflag','eq',1)->field('id,mix,username,prphone,password,mobile,addtime,status,delflag,imageurl')->find();
        return $lists;
    }
    /**
     * [getStudentInfo userid获取学生用户信息]
     * @Author cy
     * @DateTime 2018-10-16
     * @param    [int]        $userid  [用户id]
     * @return   array
     */
    public function getStudentInfo($userid){
        $lists = Db::table($this->table.' s')
            ->field('s.id,s.imageurl,s.username,s.nickname,s.categoryid,s.school,c.name as gradename,s.sex,s.class')
            ->join('nm_studentcategory c','s.categoryid=c.id','LEFT')
            ->where('s.id','eq',$userid)
            ->find();
        return $lists;
    }
    /**
     * [getLabelInfo userid获取学生标签信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $userid  [用户id]
     * @return   array
     */
    public function getLabelInfo($userid)
    {
        $lists = Db::table('nm_hjxapplable')
                    ->field('id,lablename')
                    ->where('studentid',$userid)
                    ->order('id','desc')
                    ->select();
        return $lists;

    }
    /*getLabelist  获取用户标签个数
     *
     * */
    public function getLabelist($studentid)
    {
        $lists = Db::table('nm_hjxapplable')
                ->where('studentid',$studentid)
                ->count();
        return $lists;
    }
    /**
     * [createLabelInfo userid 新增学生标签信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $studentid  [用户id]
     * @param    [int]        $label      [标签名]
     * @return   array
     */
    public function createLabelInfo($data)
    {
        $res   = Db::table('nm_hjxapplable')
                ->insert($data);
        return $res;
    }
    /**
     * [deleteLabelInfo userid 删除学生标签信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $userid  [用户id]
     * @param    [int]        $labelid [标签id]
     * @return   array
     */
    public function deleteLabelInfo($where)
    {
        $res  = Db::table('nm_hjxapplable')
                ->where($where)
                ->delete();
        return $res;
    }

    /**
     * [updateLabelInfo studentid 更改学生标签信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $where  [筛选条件]
     * @param    [int]        $data   [更新的标签名]
     * @return   array
     */
    public function updateLabelInfo($where,$data)
    {
        $res = Db::table('nm_hjxapplable')
                ->where($where)
                ->update($data);
        return $res;
    }
    /**
     * [searchLabelInfo $where 搜索标签信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $where  [筛选条件]
     * @return   array
     */
    public function searchLabelInfo($where,$limitstr,$labelid)
    {
        $res = Db::table('nm_composition')
                ->where($where)
                ->where('find_in_set('.$labelid.',label)')
                ->field('id,type,title,imgurl,label,addtime,reviewstatus')
                ->limit($limitstr)
                ->select();
        return $res;
    }
    /**
     * [searchArticleInfo $where 搜索文章标题信息]
     * @Author cy
     * @DateTime 2018-10-18
     * @param    [int]        $where  [筛选条件]
     * @return   array
     */
    public function searchArticleInfo($where,$limitstr)
    {
        $res  = Db::table('nm_composition')
                ->where($where)
                ->field('id,type,title,imgurl,label,addtime,reviewstatus')
                ->limit($limitstr)
                ->select();
        return $res;
    }
    /*
     *统计标签搜索的条数
     */
    public function searchLabelInfoCount($where,$labelid)
    {
        $res  = Db::table('nm_composition')
            ->where($where)
            ->where('find_in_set('.$labelid.',label)')
            ->count();
        return $res;
    }
    /**
     * 统计文章关键字条数
     */
    public function searchArticleInfoCount($where)
    {
        $res  = Db::table('nm_composition')
            ->where($where)
            ->count();
        return $res;
    }
    /**
     * [getStudentInfo userid获取学生班级信息]
     * @Author cy
     * @DateTime 2018-10-16
     * @return   array
     */
    public function getClassCategory()
    {
       $classes = Db::table('nm_studentcategory')
                ->where('status',1)
                ->field(['id','name'])
                ->select();
       return $classes;
    }

    /**
     * [getEquipmentInfo userid获取学生设备信息]
     * @Author cy
     * @DateTime 2018-10-19
     * @return   array
     */
    public function getEquipmentInfo($studentid)
    {
        $lists = Db::table($this->table)
            ->field('equipment')
            ->where('id',$studentid)
            ->find();
        return $lists;
    }
    /**
     * [updateAppuserInfo]
     * @Author cy
     * @DateTime 2018-10-17
     * @param    where                  用户id
     * @param    field                  修改字段
     * @return   [array]                [返回数据]
     */
    public function updateAppuserInfo($where,$field){
        $field = where_filter($field,array('imageurl','nickname','sex','school','categoryid','class','equipment'));
        $res = Db::table($this->table)->where($where)->update($field);
        return $res;
    }

	/**
     * [addStudent 添加好迹星学生]
     * @Author lc
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]      
     * @return   [array]
     */
    public function addStudent($data)
    {

        Db::startTrans();
        try{

            
            $return = $this->allowField(true)->save($data);
            $id = $this->id;
            $nickname = getRandNickname($id);
            $info['nickname'] = $nickname;
            $info['id'] = $id;
            $this->allowField(true)->save($info);
            
           // Db::table('nm_studentfunds')->insert(['studentid'=>$id]);
 
            Db::commit();
            
            $accessroleusermodel = new Accessroleuser;
            $accessroleusermodel->addUserDefaultAcl($id,4);

            return $info['id'];
        }catch(\Exception $e){
            
            Db::rollback();
            return false;
        }

    }

}








