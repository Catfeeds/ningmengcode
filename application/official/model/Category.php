<?php
namespace app\official\model;
use think\Db;
use think\Model;
/**
 * 分类Model
 * @ wyx
 */
class Category extends Model {

	protected $pk    = 'id';
	protected $table = 'nm_category';
	/**
	 * getCategoryRecomm 根据官方机构id  获取官方的分类推荐
	 * @author wyx
	 * @param $organid 官方机构id
	 * @return array();
	 */
	public function getCategoryRecomm($organid) {

		$field = 'id,categoryname,rank' ;
		$where = [
			'organid'   => $organid ,
			'recommend' => 1 ,// 1表示推荐
			'delflag'   => 1 ,// 未删除

			] ;
		return Db::table($this->table)->field($field)->where($where)->order('sort')->select();
	}
	/**
	 * getCategoryList 查询分类列表
	 * @author wyx
	 * @param $where 查询条件
	 * @param $field 查询内容 默认不传全部
	 * @param $limit 查询页数
	 * @param $pagenum 一页几条
	 * @return array();
	 */

	public function getCategoryList() {

		$where['t1.delflag'] = 1 ;// 未删除的 分类
		$where['t1.status'] = 1 ;// 1 显示
		$field = 't1.id preid,t1.categoryname prename,t1.rank prerank,t1.fatherid prefid,t1.delflag predelflag,t1.status prestatus,t2.id,t2.categoryname name,t2.rank,t2.fatherid fid,t2.delflag,t2.status' ;
		$lists = Db::table($this->table.' t1')
				->join($this->table.' t2','t1.id=t2.fatherid','LEFT')
				->where($where)
				->field($field)
                ->order('t1.rank,t1.sort,t1.id asc')
				->select();
		return $lists;
	}


	/**
	 * updateCateRecomm 增加官方分类推荐
	 * @author wyx
	 * @param $organid
	 * @param $ids     新增推荐
	 */
	public function updateCateRecomm($ids,$organid){
		return Db::table($this->table)->where( ['organid'=>$organid,'id'=>['IN',$ids]] )->update( [ 'recommend' => 1 ] );
	}
	/**
	 * 课程列表分类处理
	 *  @author wyx
     *	@param    int     $id             要删除的分类id
	 *	@param    int     $organid        机构标识id
	 */
	public function delRecomm($id,$organid) {
		// 遍历生成对应的或关系查询
		$where['id']      = $id;
		$where['organid'] = $organid;
		$data = ['recommend'=> 0 ] ;

		return Db::table($this->table)->where($where)->update($data);
	}

	/**
	 * exchangeSort 查询移动上下项
	 * @author wyx
	 * @param $organid 机构id
	 * @param $ids 需要交换位置的两个类别
	 * @return 
	 */
	public function exchangeSort($organid , $ids) {
		$where['id'] = ['IN',$ids] ;
    	$arr = Db::table($this->table)->field('id,sort')->where($where)->select();
        if(count($arr)==2){
    		
	    	Db::table($this->table)->where(['organid'=>$organid,'id'=>$arr[0]['id']])->update(['sort'=>$arr[1]['sort']]);
	    	Db::table($this->table)->where(['organid'=>$organid,'id'=>$arr[1]['id']])->update(['sort'=>$arr[0]['sort']]);
    		return return_format('',0);
    	}else{
    		return return_format('',40134);
    	}
	}

	/**
	 * getId 查询机构分类列表总行数
	 * @ jcr
	 * @param $where 查询条件
	 * @param $organid 所属机构id
	 * @return int;
	 */
	public function getCategoryCount($where, $organid) {
		if (!$where) {
			$where = [];
		}

		$where['organid'] = $organid;
		$counts = Db::name($this->table)->where($where)->count();
		return $counts;
	}

	/**
	 * 分类编辑/添加
	 * @ jcr
	 * @ $data 添加数据源
	 * @ $affairs 添加回调开启事务更新排序值
	 */
	public function editAdd($data, $affairs = false, $organid) {
		$validate = new Validate($this->rule, $this->message);
		if (!isset($data['id']) || (isset($data['categoryname']) && isset($data['id']))) {
			// 添加时验证 和 编辑类名时验证
			if (!$validate->check($data)) {
				return array('code' => 500, 'info' => $validate->getError());
			}
		}

		if (isset($data['id'])) {
			// 允许传输的编辑字段
			$data = where_filter($data, array('id', 'status', 'sort', 'delflag', 'categoryname', 'organid'));
			$info = Db::name($this->table)->where(['id' => $data['id'], 'organid' => $organid])->update($data);
			if ($info && $affairs) {
				return true;
			} else if ($affairs) {
				return false;
			}
		} else {
			//添加模块
			$data = where_filter($data, array('id', 'status', 'sort', 'delflag', 'categoryname', 'rank', 'fatherid', 'path', 'organid','copyid'));
			$data['organid'] = $organid;
			$data['addtime'] = time();
			//开启事务
			Db::name($this->table)->startTrans();
			$info = Db::name($this->table)->insertGetId($data);
			if ($info) {
				//回调 更新sort 同步主键
				$editstatus = $this->editAdd(['id' => $info, 'sort' => $info, 'organid' => $organid], true, $organid);
				//var_dump($editstatus);
				if ($editstatus) {
					Db::name($this->table)->commit();
				} else {
					Db::name($this->table)->rollback();
					return array('code' => 500, 'info' => '添加失败');
				}
			}
		}
		return array('code' => 0, 'info' => isset($data['id']) ? '修改成功' : '添加成功');
	}

	/**
	 * 特殊需求，上下移动
	 * @ jcr
	 * @ data 修改数据源
	 **/
	public function editSoct($data, $organid) {
		// 开启事务 交换两个id 对应的排序值
		Db::name($this->table)->startTrans();
		$onein = $this->editAdd($data[0], false, $organid);
		$twoin = $this->editAdd($data[1], false, $organid);
		if ($onein && $twoin) {
			Db::name($this->table)->commit();
			return true;
		} else {
			Db::name($this->table)->rollback();
			return false;
		}
	}

}
