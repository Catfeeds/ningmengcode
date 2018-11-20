<?php
namespace app\admin\model;
use think\Db;
use think\Model;

/*
 * 对应的开课视频
 * @ jcr
 */
class Playback extends Model {

	protected $table = 'playback';
	protected $pagenum; //每页显示行数

	//自定义初始化
	protected function initialize() {
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
	}

	public function getList($serial) {
		$field = 'id,playpath';
		return Db::name($this->table)->where('serial', $serial)->field($field)->select();
	}

	/**
	 * 课程回放视频
	 * @ jcr
	 * @param $data 添加数据源
	 */
	public function addEdit($data, $id) {
//        Db::startTrans();
		$ids = Db::name($this->table)->insertAll($data);
//        if(!$ids){
		//            Db::rollback();
		//            return false;
		//        }

		$classroom = new Classroom();
		// 更新视频已生成状态
		$info = $classroom->addRoom(['id' => $id, 'videostatus' => 1]);
//        if($info['code']!=0){
		//            Db::rollback();
		//            return false;
		//        }

//        Db::commit();
		return $ids;
	}

}
