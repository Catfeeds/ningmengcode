<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 学生用户Model
 * @ yr
*/
class Studentinfo extends Model{
    protected $table = 'nm_studentinfo';
    protected $rule = [
        'nickname' => 'require|max:30',
     /*   'sex'      => 'number|between:0,2',
        'country'  => 'number',
        'province' => 'number',
        'city'     => 'number',*/
      /*  'profile'     => 'require|max:300',*/
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'nickname.require' => lang('37004'),
            'nickname.max' => lang('37005'),
          /*  'profile.require' => lang('37100'),
            'profile.max'     => lang('37006'),*/
            'imageurl.require'       => lang('37007'),
            /*  'sex.number'       => '性别必须是数字',
              'sex.between'      => '性别只能在0-2之间',
              'country.number'   => '必须是数字',
              'province.number'  => '必须是数字',
              'city.number'      => '必须是数字',*/
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
     * @Author yr
     * @DateTime 2018-04-24T19:31:56+0800
     * @param    [int]        $userid  [用户id]
     * @return   array
     */
    public function getStudentInfo($userid){
        $lists = Db::table($this->table.' s')
            ->field('f.usablemoney, s.birth,s.id,s.imageurl,s.username,s.password,s.prphone,s.mobile,s.nickname,s.sex,s.country,s.province,s.city,s.profile,s.favorcategory,s.categoryid,c.name as gradename,s.tag,s.childtag')
            ->join('nm_studentfunds f','s.id=f.studentid','LEFT')
            ->join('nm_studentcategory c','s.categoryid=c.id','LEFT')
            ->where('s.id','eq',$userid)
            ->find();
        return $lists;
    }
    /**
     * [getFieldByiduserid获取学生用户信息]
     * @Author yr
     * @DateTime 2018-04-24T19:31:56+0800
     * @param    [int]        $userid  [用户id]
     * @return   array
     */
    public function getFieldByid($where,$filed){
        $lists = Db::table($this->table.' s')
            ->field($filed)
            ->where($where)
            ->find();
        return $lists;
    }
    /**
     * [updateStudentInfo 更新学生数据]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [array]       $data    [需要更新的字段]
     * @param    [int]         $userid [学生用户id]
     * @return   [array]                [返回数据]
     */
    public function updateStudentInfo($userid,$data){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)){
            return return_format('',37002,$validate->getError());
        }else{
            if(!empty($data['birth'])){
                $data['birth'] = strtotime($data['birth']);
            }else{
                $data['birth']  = '';
            }
            $where = ['id'=>$userid] ;
            $return =  Db::table($this->table)->where($where)->update($data);
            if($return>=0){
                return return_format($return,0,lang('success'));
            }else{
                return return_format('',37003,lang('error'));
            }
        }
    }
    /**
     * [updateStudentPass 修改学生密码]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [学生手机号]
     * @param    [array]         $newpass [新密码]
     * @return   [array]                [返回数据]
     */
    public function updateStudentPass($newpass,$mobile,$mix){
        $where = ['mobile'=>$mobile];
        $data['password'] = $newpass;
        $data['mix'] = $mix;
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [updateStudentPass 修改学生密码]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [学生手机号]
     * @param    [array]         $newpass [新密码]
     * @return   [array]                [返回数据]
     */
    public function updateMobile($studentid,$newmobile,$prphone){
        $where = ['id'=>$studentid];
        $data['mobile'] = $newmobile;
        $data['prphone'] = $prphone;
        $res =Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [favorAdd 添加用户喜欢的分类]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [学生手机号]
     * @param    [array]         $newpass [新密码]
     * @return   [array]                [返回数据]
     */
    public function favorAdd($ids,$studentid){
        $where = ['id'=>$studentid];
        $data['categoryid'] = $ids;
        $res =Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [updateAppuserInfo]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [学生手机号]
     * @param    [array]         $newpass [新密码]
     * @return   [array]                [返回数据]
     */
    public function updateAppuserInfo($where,$field){
        $field = where_filter($field,array('imageurl','nickname','sex','country','province','city','birth','profile','tag','childtag'));
        $res = Db::table($this->table)->where($where)->update($field);
        return $res;
    }
    /**
     * [addStudent 添加学生信息]
     * @Author yr
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
    public function addStudent($data)
    {

        Db::startTrans();
        try{

            //插入学生信息
            $return = $this->allowField(true)->save($data);
            $id = $this->id;
            $nickname = getRandNickname($id);
            $info['nickname'] = $nickname;
            $info['id'] = $id;
            $this->allowField(true)->save($info);
            //添加财务空 记录
            Db::table('nm_studentfunds')->insert(['studentid'=>$id]);

            Db::commit();
            //新增新增新增 用户成功后 给用户添加默认角色 学生的用户类型为3
            $accessroleusermodel = new Accessroleuser;
            $accessroleusermodel->addUserDefaultAcl($id,3);

            return $info['id'];
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            return false;
        }

    }
	
	/**
     * [updateMicrouserInfo]
     * @param $where 更新条件
	 * @param $field 更新字段
     * @return   [bool]  [返回数据]
     */
    public function updateMicrouserInfo($where,$field){
        $field = where_filter($field,array('imageurl','nickname','sex','country','province','city','birth','profile','tag','childtag','signinimageid'));
        $res = Db::table($this->table)->where($where)->update($field);
        return $res;
    }
}








