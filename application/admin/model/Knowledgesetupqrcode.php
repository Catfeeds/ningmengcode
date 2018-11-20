<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Knowledgesetupqrcode extends Model
{	
	protected $table = 'nm_knowledgesetupqrcode';

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
	 * 获取二维码列表
	 */
	public function getQrList($where, $field, $order){
		return Db::table($this->table)->where($where)->field($field)->order($order)->select();
	}
	
	/**
     * []
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [学生分类id]
     * @return   [type]               [description]
     */
   /*  public function delQrcode($id){
    	$flag1 = Db::table($this->table)->where(['id'=>$id])->update(['delflag'=>1]);
		if(!$flag1){
			return return_format('',40101);
		}
		return array('code'=>0,'info'=>lang('success'));
    } */

	/**
	 * [addFile 新建文件夹]
	 * @php lc
	 * @param [type] $data [description]
	 * @return array()
	 */
	public function updateFile($data){
			$validate = new Validate($this->rule, $this->message);
	        if(!$validate->check($data)){
	            return array('code'=>40030,'info'=>$validate->getError());
	        }

			$updatedata = where_filter($data,array('imageurl'));
			$updatedata['updatetime'] = time();
			$id = Db::table($this->table)->where('id','>',0)->update($updatedata);
		return $id?array('code'=>0,'info'=>$id):array('code'=>70107,'info'=>lang('error'));
	}
}
