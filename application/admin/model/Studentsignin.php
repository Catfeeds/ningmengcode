<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class Studentsignin extends Model
{
	protected $pk = 'id';
	protected $table = 'nm_studentsignin';

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getSigninList($where,$field,$limitstr,$order='s.id asc')
    {
        return Db::table('nm_studentinfo s')
			->field($field)
			->join($this->table .' ss', 's.id=ss.studentid', 'INNER')
			->where($where)
			->group('ss.studentid')
			->limit($limitstr)
			->order($order)
			->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @param $order    string      必填
     * @param $limitstr string      必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     *
     */   
    public function getSigninListCount($where){
        return Db::table('nm_studentinfo s')
			->field('ss.studentid')
			->join($this->table .' ss', 's.id=ss.studentid', 'INNER')
			->where($where)
			->group('ss.studentid')
			->count();
    }
	
	/**
     * [getAllSigninList  根据$userid获取所有签到数据]
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
