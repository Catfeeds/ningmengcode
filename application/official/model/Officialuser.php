<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use login\Authorize;
use app\official\model\Officialuseroperate;
class Officialuser extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_officialuser';

	
	 // 添加验证规则
    protected $rule = [
        'username'  => 'require|max:50',
        'realname'  => 'require|max:50',
        'mobile'  => 'require|max:11',
        'password'  => 'max:16',
        'repassword'  => 'confirm:password',
    ];

    protected $message  = [];
    protected function initialize() {
        parent::initialize();
        $this->message = [
            'username.require' => lang('50267'),
            'username.max' => lang('50268'),
            'realname.require' => lang('50269'),
            'realname.max' => lang('50270'),
            'mobile.require' => lang('50271'),
            'mobile.max' => lang('50272'),
            'password.max' => lang('50273'),
            'repassword.confirm'=> lang('50274'),
        ];
    }

    /**
     * [getOfficialUserList 获取用户列表]
     * @Author zzq
     * @DateTime 
     * @param    没有参数
     * @return   [array]                              [description]
     */
    public function getOfficialUserList($where,$orderbys,$pagenum,$pernum){
        // var_dump($where);
        // die;
        $orderbys = $orderbys?$orderbys : 'id desc';
        $field = 'id,username,realname,mobile,addtime,logintime,status';
        $pagenum = $pagenum?$pagenum:$this->pagenum;
        try{
            $lists = Db::table($this->table)->page($pagenum,$pernum)->order($orderbys)->where($where)->field($field)->select();
            if(!$lists){
                $lists = [];
            }
            if($lists){
                foreach($lists as $k => $v){
                    $lists[$k]['addtime'] = Date('Y-m-d H:i:s',$lists[$k]['addtime']);
                    $lists[$k]['logintime'] = Date('Y-m-d H:i:s',$lists[$k]['logintime']); 
                }
            }
            $count = $this->getOfficialUserListCount($where);
            $pagenum = ceil($count/$pernum);
            $ret = [];
            $ret['lists'] = $lists;
            $ret['count'] = $count;
            $ret['pagenum'] = $pagenum;
            $ret['pernum'] = $pernum;
            //获取页码数
            return return_format($ret,0,lang('success')) ;
        }catch(\Excpetion $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

        return $ret;
    }

    /**
     * [getOrganListCount 获取管理员数目]
     * @Author
     * @DateTime 2018-05-03
     * @param    [array]            $where    [筛选条件]
     * @return   [int]              $count        [查询的数目]
     */
    public function getOfficialUserListCount($where){

        try{
            $count = Db::table($this->table)->where($where)->count();
        }catch(\Excpetion $e){
            $count = 0;
        }

        return $count;
    }


    /**
     * [getOfficialUserById 获取单个用户的详情]
     * @Author zzq
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $id   [管理员id]
     * @return   [array]                              [description]
     */
    public function getOfficialUserById($id){
        
        $field = 'id,username,realname,mobile,info,status,logintime,lastlogintime' ;
        try{
            $res = Db::table($this->table)
            ->field($field)
            ->where('id','EQ',$id)
            ->find();
            if($res){
                return return_format($res,0,lang('success'));
            }else{
                return return_format('',50086,lang('50086')) ;
            }
             
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
    /**
     * [addOfficialUser 添加用户]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function addOfficialUser($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',50010,$validate->getError()) ;
        }
        if($this->hasOfficialUser('username',$data['username'])){
            return return_format('',50078,lang('50078')) ;
        }
        $cryptstr = new Authorize;
        $cryptarr = $cryptstr->createUserMark($data['password']);

        $data['password'] = $cryptarr['password'];
        $data['mix'] = $cryptarr['mix'];

        $data['status'] = 1;
        $time = time();
        $data['addtime'] = $time;
        $data['logintime'] = $time;
        $data['lastlogintime'] = $time;
        $data['organid'] = 1;
        unset($data['repassword']);
        // var_dump($data);
        // die;
        try{
            Db::startTrans() ;
            try{
                $flag = $this->insertGetId($data);

                $roleData = [
                    'roleid'=>4,
                    'uid'=>$flag,
                    'usertype'=>5
                ];
                Db::table('nm_accessroleuser')->insert($roleData);
                Db::commit();
            }catch (\Exception $e) {
                //回滚事务
                Db::rollback();
                return return_format($e->getMessage(),50007,lang('50007'));
            }


            if($flag){
            	//添加操作日志
            	$obj = new Officialuseroperate();
                $obj->addOperateRecord('添加了管理员'); 
            	return return_format('',0) ;
            }else{
            	return return_format('',50004,lang('50004')) ;
            }
            
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

    }
    /**
     * [editOfficialUser 更新用户]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function editOfficialUser($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',50010,$validate->getError()) ;
        }
        //构造入库数据
        if(!$this->hasOfficialUser('id',$data['id'])){
            return return_format('',50079,lang('50079')) ;
        }
        //当密码都为空的时候表示不修改密码
        if(empty($data['password']) && empty($data['repassword']) ){
            unset($data['password']);
            unset($data['repassword']);
        }else{
            unset($data['repassword']);
            //$cryptstr = new Authorize;
            //$cryptarr = $cryptstr->createUserMark($data['password']);
            //$data['password'] = md5($data['password']);
            //$data['mix'] = sha1($data['password']);

            $cryptstr = new Authorize;
            $cryptarr = $cryptstr->createUserMark($data['password']);

            $data['password'] = $cryptarr['password'];
            $data['mix'] = $cryptarr['mix'];
        }
        // var_dump($data);
        // die;
        try{
            $flag = $this->save($data,['id'=>$data['id']]);
            //添加操作日志
            $obj = new Officialuseroperate();
            $obj->addOperateRecord('编辑了管理员'); 
            return return_format('',0,lang('success')) ;

            
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
	/**
	 * [setOfficialUserOnOrOff] 
	 * @Author zzq
	 * @param    [array]        $data   数组
	 * @return   [array]            [description]
	 */
	public function setOfficialUserOnOrOff($id,$status){

        if(!$this->hasOfficialUser('id',$id)){
            return return_format('',50079,lang('50079')) ;
        }
        //判断当前的状态值是否匹配
        $arr = $this->getOfficialUserById($id);
        $prestatus = $arr['data']['status'];
        $prestatus = intval($prestatus);
        $status = intval($status);
        if($prestatus != $status){
            return return_format('',50077,lang('50077')) ;
        }
        if($status == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        $ret = [
                'status'      => $status
            ] ;

        try{
            $flag = $this->save($ret,['id'=>$id]);
            //添加操作日志
            $obj = new Officialuseroperate();
            $obj->addOperateRecord('禁用了管理员'); 
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

	}

    /**
     * [delOfficialUser] 
     * @Author zzq
     * @param    [int]        $id   查询参数
     * @return   [array]            [description]
     */
    public function delOfficialUser($id){
        $where = [
                'id'      => $id,
            ] ;

        try{
            $flag = Db::table($this->table)
                ->where($where)
                ->delete();
            if($flag){
                //添加操作日志
                $obj = new Officialuseroperate();
                $obj->addOperateRecord('删除了管理员'); 
                return return_format('',0,lang('success')) ;
            }else{
                return return_format('',50004,lang('50004')) ;
            }
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

    }
    /**
     * [hasOfficialUser] 
     * @Author zzq
     * @param    [string]        $field   字段名
     * @param    [string]        $value   字段值
     * @return   [array]            [description]
     */
    public function hasOfficialUser($field,$value){
        $where = [
                $field      => $value,
            ] ;
        $res = Db::table($this->table)
                ->where($where)
                ->find();
        if($res){
            return $res;
        }else{
            return false;
        }        
    }

    /**
     * [checkUserAndPass] 判断用户名密码是否正确 
     * @Author zzq
     * @param    [string]        $username   用户名
     * @param    [string]        $password   密码 
     * @return   [array]            [description]
     */
    public function checkUserAndPass($username,$password){

        //判断管理员是否存在
        $res = $this->hasOfficialUser('username',$username);
        if(!$res){
            return false;
        }
        //获取mix password
        $mix = $res['mix'];
        $sign = $res['password'];

        $auth = new Authorize;
        $flag = $auth->checkUserMark($password,$mix,$sign);

        if($flag){
            return true;
        }else{
            return false;
        }
    }

    /**
     * [changeOfficialUserField] 修改用户的状态 
     * @Author zzq
     * @param    [array]        $data   
     * @param    [int]        $id   用户id 
     * @return   [array]            [description]
     */
    public function changeOfficialUserField($data,$id){

        $res = $this->save($data,['id'=>$id]);
        return $res;      
    }

}
