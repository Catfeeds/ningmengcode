<?php
namespace app\microsite\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 签到表 Studentsignin
 * @ lc
*/
class Studentsignin extends Model{
    protected $table = 'nm_studentsignin';
    protected $rule = [
        'studentid'   => 'require',
        //'knowledgeid' => 'require',
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
          parent::initialize();
          $this->message = [
                'studentid.require' => lang('33033'),
                //'knowledgeid.require' => lang('33036'),
            ];
    }
	
    /**
     * [getSigninList  ]
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getSigninList($userid,$limitstr){
        $lists =Db::table($this->table.' ss')
			->join('nm_knowledge k','ss.knowledgeid=k.id','LEFT')
            ->field('ss.id,ss.signdate,k.content')
            ->where('ss.studentid','eq',$userid)
            ->where('ss.delflag','eq',0)
            ->order('ss.id asc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
	
    /**
	 *
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getSigninCount($userid){
        $lists = Db::table($this->table)
            ->field('id')
            ->where('studentid','eq',$userid)
            ->where('delflag','eq',0)
            ->count();
        return  $lists;
    }
	
	/**
	 * 添加签到记录
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function addSignin($data){
		$validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return $validate->getError();
		}	
        $data['signdate'] = date("Y-m-d");
        $data = where_filter($data, array('studentid', 'knowledgeid', 'signdate'));
        $id = Db::table($this->table)->insertGetId($data);
        
        return $id?$id:0;
    }
	
	/**
	 * 根据where获取签到数据 
	 * @param $where
	 * @return array
	 */
	public function getSigninByCondition($where, $field){
		return Db::table($this->table)->field($field)->where($where)->find();
	}
	
	/**
     * [getAllSigninList  获取学生所有签到数据]
     * @Author lc
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getAllSigninList($userid, $field, $order='id desc'){
        $lists =Db::table($this->table)
            ->field($field)
            ->where('studentid','eq',$userid)
            ->where('delflag','eq',0)
            ->order($order)
            ->select();
        return  $lists;
    }
}







