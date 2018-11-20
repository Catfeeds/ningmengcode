<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Signinbackgroundimage extends Model
{	
	protected $table = 'nm_signinbackgroundimage';

    // 课程添加验证规则
    protected $rule = [
        'imageurl'  => 'require'
    ];

    protected $message  = [];
	//自定义初始化
	protected function initialize(){
		parent::initialize();
		$this->message = [
			'imageurl.require' => lang('70104')
		];
	}
	
	/**
	 * 获取背景图列表
	 */
	public function getSigninbgiList($where, $field, $order = 'id asc'){
		return Db::table($this->table)->where($where)->field($field)->order($order)->select();
	}
	
	/**
     * []
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [学生分类id]
     * @return   [type]               [description]
     */
    public function delSigninbgi($id){
    	$flag1 = Db::table($this->table)->where(['id'=>$id])->update(['delflag'=>1]);
		if(!$flag1){
			return return_format('',40101);
		}
		return array('code'=>0,'info'=>lang('success'));
    }

	/**
	 * [addFile 新建文件夹]
	 * @php lc
	 * @param [type] $data [description]
	 * @return array()
	 */
	public function addFile($data){
			$validate = new Validate($this->rule, $this->message);
	        if(!$validate->check($data)){
	            return array('code'=>40030,'info'=>$validate->getError());
	        }

			$adddata = where_filter($data,array('imageurl'));
			$adddata['addtime'] = time();
			$id = Db::table($this->table)->insert($adddata);
		return $id?array('code'=>0,'info'=>$id):array('code'=>70105,'info'=>lang('error'));
	}
}
