<?php

namespace app\teacher\model;

use think\Model;
use think\Validate;
use think\Db;
use login\Authorize;
class TeacherInfo extends Model
{

    protected $pk    = 'teacherid';
	protected $table = 'nm_teacherinfo';
    protected $rule = [
        'nickname' => 'require|max:30',
        'sex' => 'number|between:0,2',
        'profile'=>'max:300'
    ];
    protected $message = [];
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'nickname.require' => lang('28008'),
            'nick.max' => lang('28009'),
            'sex.number' => lang('28010'),
            'sex.between' => lang('28011'),
            //'profile.require' => lang('28012'),
            'profile.max' => lang('28013')
        ];
    }
    public function __construct(){
        //$this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
    }
	/**
	 * 从数据库获取
	 * @Author wangwy
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getTeacherList($where,$limitstr,$order='teacherid desc')
    {
        return Db::table($this->table)
            ->where($where)
            ->field('teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus')
            ->limit($limitstr)
            ->order($order)
            ->select();
    }


    /*
	 * 根据teacherid获取 教师的详细信息
	 * @Author wangwy
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $teachid 教师表teacherid
	 * @param    [int]     $organid   [机构标识]
	 * @return   array                [description]
	 */
    public function getTeacherData($field,$teachid)
    {
        return Db::table($this->table)
            ->where('teacherid','eq',$teachid)
            ->field($field)->find();
    }

    /**
	 * 根据teacherid获取 教师最近登陆时间
	 * @Author wangwy
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $allacountid 教师所在总表的 id
	 * @return   array                   [description]
	 */
    public function getLoginTime($allacountid)
    {
        if( is_array($allacountid) ){//如果是一个数组批量查询
            return Db::table('nm_organlogin')->where('teachid','IN',$allacountid)->column('allacountid,logintime');
        }else{//
            return Db::table('nm_organlogin')->where(['allacountid'=>$allacountid])->column('logintime');

        }
    }

    /*
     * [updateTeacher 更新教师数据（除了密码和手机号）]
     * @Author wangwy
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateTeacher($data){
        $validate = new Validate($this->rule, $this->message);
        // $result = $validate->check($data);
        if(!$validate->check($data)){
            return return_format('',-1,$validate->getError());
        }else{
            $arr = explode('-',$data['birth']) ;
            $data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
            if($data['teacherid']>0){
                Db::startTrans();
                try{
                    // 过滤post数组中的非数据表字段数据
                    $return = $this->allowField(true)->save($data,['teacherid'=>$data['teacherid']]);
                    //构造allaccount 更新需要的数据和条件
//                    $where = ['uid'=>$data['teacherid'] ,'usertype'=>1 ];//类型1 为老师
//                    $allaccountdata  = [
//                                'username'=> $data['truename']
//                            ] ;
//                    Db::table('nm_allaccount')->where($where)->update($allaccountdata) ;
                    // 提交事务
                    Db::commit();
                    return return_format($return,0,lang('success'));
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return return_format('',22002,lang('22002'));
                }


            }else{
                return return_format('',22003,lang('22003'));
            }
        }
    }
    /*
    * 更改教师头像imageurl
    * @Author WangWY
    * @dateTime
    **/
    public function updateTeacherimg($data){
        $where['teacherid'] = $data['teacherid'];
        $where['organid'] = $data['organid'];
        $image = ['imageurl'=>$data['imageurl']];
        $res = $this->allowField(true)->save($image,$where);
        return $res;
    } 

    /**
     * 获取 开课 老师列表
     * @Author jcr
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getLists($organid)
    {
        return Db::table($this->table)->where('organid','eq',$organid)
            ->where('delflag','eq',1)
            ->where('accountstatus','eq',0)
            ->field('teacherid,teachername,nickname,accountstatus')
            ->limit(1000)
            ->select();
    }

    /**
     * [getTeachernameByIds 订单表分页后获取教师名称]
     * @Author
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [array]          $arr [教师ids]
     * @return   [type]               [description]
     */
    public function getTeachernameByIds($arr){
    	return Db::table($this->table)
            ->where('teacherid','IN',$arr)
    	    ->column('teacherid,teachername');
    }

    /**
     * [getTeachernameById 订单表分页后获取教师名称]
     * @Author
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [array]          $arr [教师ids]
     * @return   [type]               [description]
     */
    public function getTeachernameById($arr){
        return Db::table($this->table)->where('teacherid','IN',$arr)
            ->field('teacherid,teachername')
            ->find();
    }


    /**
     * 根据老师id 获取老师对应详细信息
     * @php jcr
     * $id 教师id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getTeacherId($id,$field){
        return Db::table($this->table)
            ->where('teacherid','eq',$id)
            ->field($field)
            ->find();
    }
    /**
     * [checkLogin 手机获取教师用户信息]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [vachar]        $mobile  [手机号]
     * @return   array
     */
    public function checkLogin($mobile){
        $where['mobile'] =  $mobile;
        //$where['organid'] = $organid;
        $where['delflag'] = 1;
        $lists = Db::table($this->table)->where($where)
            ->field('teacherid,teachername,prphone,mobile,addtime,delflag')
            ->find();
        //print_r(Db::table($this->table)->getlastsql());
        return $lists;
    }
    /**
     * [updateStudentPass 修改教师手机号]
     * @Author yr
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [教师手机号]
     * @param    [array]         $newpass [新密码]
     * @param    [int]         $organid[机构id]
     * @return   [array]                [返回数据]
     */
    public function updateMobile($teacherid,$newmobile,$prphone){
        $where = ['teacherid'=>$teacherid];
        $data['mobile'] = $newmobile;
        $data['prphone'] = $prphone;
        $res = $this->allowField(true)->save($data,$where);
        //print_r(Db::table($this->table)->getlastsql());
        return $res;
    }

    /**
     * [updateTeacherPass 修改教师密码]
     * @Author WangWY
     * @DateTime 2018-04-24T21:02:32+0800
     * @param    [int]         $mobile [学生手机号]
     * @param    [array]         $newpass [新密码]
     * @param    [int]         $organid[机构id]
     * @return   [array]                [返回数据]
     */
    public function updateTeacherPass($newpass,$mobile,$mix){
        $where = ['mobile'=>$mobile];
        $data['password'] = $newpass;
        $data['mix'] = $mix;
        $res = $this->allowField(true)->save($data,$where);
        return $res;
    }
   /*
    * 
    * @Author WangWY
    * @param
    */
    public function registerTeacher($mobile){
        $fo = ['mobile'=>$mobile];
        return Db::table($this->table)->insert($fo);
    }
   /*
    * 
    * @Author WangWY
    * @param
    */
    public function getuid($mobile){
        return Db::table($this->table)
                   ->field('teacherid')
                   ->where('mobile','eq',$mobile)
                   ->find();
    }

    /**
    *
    * 获取教师昵称
    */
    public function getNick($teacherid){
        return Db::table($this->table)
                   ->field('nickname')
                   ->where('teacherid','eq',$teacherid)
                   ->find();
    }
    /*
     *  获取教师手机号
     *
     */
    public function getTeacherMobile($idarr){
        return Db::table($this->table)
            ->where('teacherid','IN',$idarr)
            ->column('prphone,mobile','teacherid');
    }

}
