<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use think\Session;

class Studentinfo extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_studentinfo';
	protected $rule = [
			'mobile'   => 'require|max:25',
			'nickname' => 'require|max:30',
			'sex'      => 'number|between:0,2',
			'country'  => 'number',
			'province' => 'number',
            'city'     => 'number',
			'status'   => 'number|between:0,1',
		];
	protected $message = [];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'mobile.require'   => lang('40072'),
            'mobile.max'       => lang('40073'),
            'nickname.require' => lang('40074'),
            'nickname.max'     => lang('40075'),
            'sex.number'       => lang('40076'),
            'sex.between'      => lang('40077'),
            'country.number'   => lang('40078'),
            'province.number'  => lang('40079'),
            'city.number'      => lang('40080'),
            'status.number'    => lang('40081'),
            'status.between'   => lang('40082'),
        ];
    }
	/**
	 * 从数据库获取
	 * @Author wyx
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getUserList($where,$limitstr)
    {
        return Db::table($this->table)
				->alias(['nm_studentcategory'=>'c', 'nm_studentinfo'=>'s', 'nm_studenttag'=>'t'])
				->join('nm_studentcategory','s.categoryid=c.id','LEFT')
				->join('nm_studenttag','s.tag=t.id','LEFT')
				->where($where)
				->field('s.id,s.sex,s.prphone,s.mobile,s.nickname,s.status,s.logintime,c.name as categoryname,t.name as tagname,s.childtag')
				->limit($limitstr)->order('s.id', 'asc')->select();
    }
    /**
     * 从数据库获取 学生列表的符合条件的总记录数
     * @Author wyx
     * @param $where    array       必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     */
    public function getUserListCount($where)
    {
        return Db::table($this->table)->alias(['nm_studentinfo'=>'s'])->where($where)->count();
    }

	/**
	 * [getTeachCount 获取机构对应老师总行数]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function getStudentsCount($where){
		return Db::table($this->table)->where($where)->count();
	}


	/**
	 * [getTeachlist 查询老师列表]
	 * @param  [type] $data    [查询条件]
	 * @param  [type] $pagenum [第几页]
	 * @param  [type] $limit   [一页几条]
	 * @return [type]          [description]
	 */
	public function getStudenslist($where,$pagenum,$limit,$order = ''){
		$field = 'id,nickname,mobile';
		return Db::table($this->table)->where($where)->page($pagenum,$limit)->field($field)->order($order)->select();
	}


    /**
     * [getUserDetail 获取学生的详细信息]
     * @Author
     * @DateTime 2018-04-20T13:48:56+0800
     * @param    [int]      $studentid [学生标识]
     * @return   [array]               [description]
     */
    public function getUserDetail($studentid)
    {
    	$field = 'id,imageurl,prphone,mobile,birth,sex,logintime,country,province,city,profile,username,nickname,status,addtime' ;
        return Db::table($this->table)->where('id','=',$studentid)
                                      ->field($field)->find();
    }
    /**
     * [addStudent 添加学生信息]
     * @Author wyx
     * @DateTime 2018-04-20T16:34:59+0800
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
    /* public function addStudent($data){
        //数据验证
    	$validate = new Validate($this->rule, $this->message);
		$result   = $validate->check($data);

		if(!$validate->check($data)){
			return return_format('',40070,$validate->getError());
		}else{
			$arr = explode('-',$data['birth']) ;
			$data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
            Db::startTrans();
            try{

                // 密码处理
                $cryptdeal = new Authorize;
                // 生成随机密码
                $password = $cryptdeal->getRandString(6);
                $cryptarr = $cryptdeal->createUserMark($password);

                $data['password'] = $cryptarr['password'] ;
                $data['mix']      = $cryptarr['mix'] ;

                //插入学生信息
    			$return = $this->allowField(true)->save($data);
                $id = $this->id;

                //添加财务空 记录
                $cashinfo = Db::table('nm_studentfunds')->insert(['studentid'=>$id]);
                if($return && $cashinfo){
                    Db::commit();
                    //新增新增新增 用户成功后 给用户添加默认角色 学生的用户类型为3
                    $cryptdeal->addUserDefaultAcl($id,3);
                    //给用户 发送密码
                    $sendmsg = new \Messages ;
                    $prphone = isset($data['prphone']) ? $data['prphone'] : '86' ;
                    $sendmsg->sendMeg($data['mobile'],3,[$password],$prphone) ;

    			    return return_format($return,0);
                    
                }else{

                }
            }catch(\Exception $e){
                // 回滚事务
                Db::rollback();
                return return_format($return,40071);
            }

		}
    } */
    /**
     * [updateStudent 更新学生数据]
     * @Author wyx
     * @DateTime 2018-04-20T21:02:32+0800
     * @param    [array]       $data    [需要更新的字段]
     * @return   [array]                [返回数据]
     */
    /* public function updateStudent($data){
    	$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',40084,$validate->getError());
		}else{
			$arr = explode('-',$data['birth']) ;
			$data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;

			$where = ['id'=>$data['id']] ;
			if($data['id']>0){
                // 更新学生信息
				$return = $this->allowField(true)->save($data,$where);
				return return_format($return,0);

			}else{
				return return_format('',40083);
			}
		}
    } */
    /**
     * [changeUserStatus 更改学生状态]
     * @Author wyx
     * @DateTime 2018-04-20T20:53:36+0800
     * @param    [int]         $userid    [需要更新的学生id]
     * @param    [int]         $flag      [机构标记id]
     * @return   [array]                
     */
    public function changeUserStatus($userid,$flag){
        $where = ['id'=>$userid] ;
        $data  = ['status'=>$flag] ;
        Db::table($this->table)->where($where)->update($data) ;
        return  return_format('',0);
    }
    /**
     * [delStudent 删除信息]
     * @Author wyx
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $userid    [学生id]
     * @return   [type]               [description]
     */
    /* public function delStudent($userid){
    	$data  = ['delflag'=>1] ;
    	$where = ['id'=>$userid] ;
    	return $this->save($data,$where);
    } */
    /**
     * [getStudentnameById 通过学生id来获取学生的名字]
     * @Author wyx
     * @DateTime 2018-04-21T11:55:09+0800
     * @param    [int]        $userid  [学生id]
     * @return   [array]               [查询结果]
     */
    public function getStudentnameById($userid){
    	return $this->get(['id'=>$userid]);
    }
    /**
     * [getStudentnameByIds 订单表分页后获取教师名称]
     * @Author wyx
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [array]          $arr [学生ids]
     * @return   [type]               [description]
     */
    public function getStudentnameByIds($arr){
        return Db::table($this->table)->where('id','IN',$arr)
        ->column('id,username');
    }

	/**
	 * [getStudentnameByIds 订单表分页后获取教师名称]
	 * @Author wyx
	 * @DateTime 2018-04-21T14:25:18+0800
	 * @param    [array]          $arr [学生ids]
	 * @return   [type]               [description]
	 */
	public function getStudentMebileByIds($arr){
		return Db::table($this->table)->where('id','IN',$arr)
			->field('id,mobile,prphone')
			->select();
	}

    /**
     *  获取机构 的学生总数
     *  @author wyx
     *
     */
    public function getStudentAllAccount(){
        return Db::table($this->table)
        ->count();
    }
    /**
     *  获取当月截至到今天的 每天的注册学生数
     *  @author wyx
     *  @param  $monthstart   获取的开始时间
     *
     *
     */
    public function getAllMonthData($monthstart){
        return Db::table($this->table)
        ->where('addtime','GT',$monthstart)
        ->field('from_unixtime(addtime,"%Y-%m-%d") formatdate,count(id) num')
        ->group('formatdate')
        ->select();
    }


    /**
     * 根据老师id 获取学生对应详细信息
     * @php jcr
     * $id 学生id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getStudentId($id,$field){
        return Db::table($this->table)->where('id','eq',$id)->field($field)->find();
    }

	/**
     * [根据childtag获取子标签名称]
     * @Author wyx
     * @DateTime 2018-04-21T14:25:18+0800
     * @param    [string]   $childtag
     * @return   [type]     [description]
     */
    public function getnameBychildtag($childtag){
		$map[]=["exp", "FIND_IN_SET(id,$childtag)"];
        return Db::table('nm_studentchildtag')
				->where("find_in_set(id, '$childtag')")
				->column('name');
    }
	
	/**
     * 根据categoryid获取学生
     * @ lc
     * @para $categoryid
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getStudentBycategoryid($categoryid){
        return Db::table($this->table)->where('categoryid','eq',$categoryid)->field('id')->find();
    }
}
