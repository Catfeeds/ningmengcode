<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use think\Session;

class Hjxappstudentinfo extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_hjxappstudentinfo';
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
        return Db::table($this->table.' s')
				->join('nm_studentcategory c', 's.categoryid=c.id', 'LEFT')
				->where($where)
				->field('s.id,s.mobile,s.nickname,s.sex,s.school,c.name as grade,s.class,s.logintime,s.status')
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
        return Db::table($this->table.' s')
				->join('nm_studentcategory c', 's.categoryid=c.id', 'LEFT')
				->where($where)
				->count();
    }
	
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
}
