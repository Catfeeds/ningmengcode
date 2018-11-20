<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class Reward extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_reward';
	/* protected $rule = [
			'name' => 'require|max:30',
			'condition1'    => 'require',
			'condition2'    => 'require',
			'type'          => 'require',
			'value'    => 'require',
			'mixamount'    => 'require',
			'expiretype'   => 'require',
			'expirevalue'  => 'require',
			'note'      => 'require',
		]; */
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        /* $this->message = [
            'name.require'   => lang('80002'),
            'name.max'       => lang('80003'),
            'condition1'    => lang('70302'),
			'condition2'    => lang('70302'),
			'type'          => lang('70302'),
			'value'    => lang('70302'),
			'mixamount'    => lang('70302'),
			'expiretype'   => lang('70302'),
			'expirevalue'  => lang('70302'),
			'note'      => lang('70302'),

        ]; */
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
    public function getRewardList($where,$field,$limitstr,$order='id asc')
    {
        // var_dump($order);
        return Db::table($this->table)->where($where)->field($field)->limit($limitstr)->order($order)->select();
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
    public function getRewardListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 奖品的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getRewardData($where,$field,$id)
    {
		$where['id'] = $id;
        return Db::table($this->table)
        ->where($where)
        ->field($field)->find();
    }
   
    /**
     * [addReward 添加奖品数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]    $data [提交的数据]
     */
    public function addReward($data){
  //   	$validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		if( empty($data['name']) || empty($data['type']) || empty($data['value']) || empty($data['mixamount']) || empty($data['expiretype']) || empty($data['expirevalue']) || empty($data['note'])){
			return return_format('',70302);
		}else{
				$alldata = [
					 'name' => $data['name'],
					'condition1' => $data['condition1'],
					'condition2' => $data['condition2'],
					'type' => $data['type'],
					'value' => $data['value'],
					'mixamount' => $data['mixamount'],
					'expiretype' => $data['expiretype'],
					'expirevalue' => $data['expirevalue'],
					'forcoursetype' => $data['forcoursetype'],
					'forcoursevalue' => $data['forcoursevalue'],
					'note' => $data['note'],
					'addtime' => time(),
				];
				$logflag = Db::table($this->table)->insert($alldata);
                if($logflag){
                    return return_format('',0);
                }else{
				    return return_format('',40101);
                }
		}
    }
	
    /**
     * [updateReward 更新奖品数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]    $data [提交的数据]
     */
    public function updateReward($data){
    	//$validate = new Validate($this->rule, $this->message);
		//if(!$validate->check($data)){
		//	return return_format('',40110,$validate->getError());
		//}else{
            if($data['id']>0){
				if(!$this->checkRewardExsit($data['id'])) return return_format('',70301);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'name' => $data['name'],
					'condition1' => $data['condition1'],
					'condition2' => $data['condition2'],
					'type' => $data['type'],
					'value' => $data['value'],
					'mixamount' => $data['mixamount'],
					'expiretype' => $data['expiretype'],
					'expirevalue' => $data['expirevalue'],
					'forcoursetype' => $data['forcoursetype'],
					'forcoursevalue' => $data['forcoursevalue'],
					'note' => $data['note'],
                ];

                Db::table($this->table)->where($where)->update($allaccountdata) ;
				return return_format('',0);
			}else{
				return return_format('',80004);
			}
		//}
    }
	
    /**
     * [switchTeachStatus 修改奖品的可用状态]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]      $id [奖品id]
     * @param    [int]      $dataflag  [要修改的标记状态]
     * @return   [array]               [返回数组]
     */
    public function switchRewardStatus($id,$status){
		if(!$this->checkRewardExsit($id)) return return_format('',70301);
        $data['status']= $status ;
        $where = ['id'=>$id];

         Db::table($this->table)->where($where)->update($data) ;
         return return_format('',0);
    }
    /**
     * [delReward 删除奖品信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]    $id [奖品id]
     * @return   [type]              [description]
     */
    public function delReward($id){
		if(!$this->checkRewardExsit($id)) return return_format('',70301);
		$where['id']= $id;
    	$r = Db::table($this->table)->where($where)->update(['delflag'=>1]);
		if($r){
			return return_format('',0);
		} else {
			return return_format('',22014);
		}
    }
	
	/**
	 * checkCategoryExsit 检查标签是否存在
	 * @param Reward id
	 * @return [bool]
	 */
	public function checkRewardExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}

}
