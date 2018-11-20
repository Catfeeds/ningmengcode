<?php

namespace app\appteacher\model;

use think\Model;
use think\Validate;
use think\Db;
use login\Authorize;
class TeacherInfo extends Model
{

    protected $pk    = 'teacherid';
	protected $table = 'nm_teacherinfo';
	protected $rule = [
            'nickname' => 'max:30',
            'sex' => 'number|between:0,2',
            'profile'=>'max:300'
        ];
    protected $message = [];
    protected function initialize() {
        parent::initialize();
        $this->message = [
            'nickname.max' => lang('20529'),
            'sex.number' => lang('20530'),
            'sex.between' => lang('20531'),
            'profile.max' => lang('20532')
        ];
    }
    // protected $message = [
    //         //'nickname.require' => '昵称必须填写',
    //         'nick.max' => '昵称不能超过30个字符',
    //         'sex.number' => '性别必须是数字',
    //         'sex.between' => '性别只能在0-2之间',
    //         //'profile.require' => '简介必须填写',
    //         'profile.max' => '简介最多不能超过300个字符',
    //     ];
    public function __construct(){
        $this->organid = 1;
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
        return Db::table($this->table)->where($where)->field('teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus')->limit($limitstr)->order($order)->select();
    }


    /**
	 * 根据teacherid获取 教师的详细信息
	 * @Author wangwy
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $teachid 教师表teacherid
	 * @param    [int]     $organid   [机构标识]
	 * @return   array                [description]
	 */
    public function getTeacherData($field,$teachid,$organid)
    {
        return Db::table($this->table)
        ->where('teacherid','eq',$teachid)
        ->where('organid','eq',$organid)
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
     * @Author wyx
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateTeacher($data){
        $validate = new Validate($this->rule, $this->message);
        // $result = $validate->check($data);
        if(!$validate->check($data)){
            return return_format('',-1,$validate->getError());
        }else{
            if(isset($data['birth'])){
                $arr = explode('-',$data['birth']) ;
                $data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
            }

            if($data['teacherid']>0){
              //$whe = where_filter($data,array('teacherid','username','country','province','city','profile','teachername','birth','sex','nickname','imageurl'));
                $whe = ['teacherid'=>$data['teacherid'],'organid'=>$data['organid']];

              // 过滤post数组中的非数据表字段数据
                $return = $this->allowField(true)->save($data,$whe);
                    //构造allaccount 更新需要的数据和条件
                    //$where = ['uid'=>$data['teacherid'] ,'usertype'=>1 ];//类型1 为老师
                    // $allaccountdata  = [
                    //             'username'=> $data['truename']
                    //         ] ;

                    //Db::table('nm_allaccount')->where($where)->update($allaccountdata) ;
                    // 提交事务

                return $return;

            }else{
                return return_format('',22003,lang('22003'));
            }
        }
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
                                      ->limit(1000)->select();
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
    	->column('teacherid,teachername');
    }


    /**
     * 根据老师id 获取老师对应详细信息
     * @php jcr
     * $id 教师id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getTeacherId($id,$field){
        return Db::table($this->table)->where('teacherid','eq',$id)->field($field)->find();
    }
    /**
     * [checkLogin 手机获取教师用户信息]
     * @Author yr
     * @DateTime 2018-04-20T19:31:56+0800
     * @param    [vachar]        $mobile  [手机号]
     * @return   array
     */
    public function checkLogin($mobile,$organid){
        $where['mobile'] =  $mobile;
        $where['organid'] = $organid;
        $where['delflag'] = 1;
        $lists = Db::table($this->table)->where($where)->field('teacherid,teachername,prphone,mobile,addtime,organid,delflag')->find();
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
    public function updateMobile($teacherid,$organid,$newmobile,$prphone){
        $where = ['id'=>$teacherid,'organid'=>$organid];
        $data['mobile'] = $newmobile;
        $data['prphone'] = $prphone;
        $res = $this->allowField(true)->save($data,$where);
        return $res;
    }

}
