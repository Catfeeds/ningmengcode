<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use app\admin\model\Studentchildtag;
class StudentTag extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_studenttag';
	protected $rule = [
			'id' => 'require|number',
			'name' => 'require|max:30',
            'childname' => 'require',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'id.require'   => lang('80010'),
            'id.number'   => lang('80011'),
            'name.require'   => lang('80002'),
            'name.max'       => lang('80003'),
            'childname.require' => lang('80006'),
        ];
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
    public function getTagList($where,$field,$limitstr,$order='id asc')
    {
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
    public function getTagListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 学生标签的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getTagData($field,$id)
    {
		$where['id'] = $id;
		$where['delflag'] = 0;
        return Db::table($this->table)
        ->where($where)
        ->field($field)->find();
    }
   
    /**
     * [addTag 添加学生标签数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addTag($data){
		if(empty($data['name']) || empty($data['childname'])){
			return return_format('',80090);
		}else{
				$alldata = [
					'name' => $data['name'],
				];
				//开启事务
				Db::startTrans();
				$tagid = Db::table($this->table)->insertGetId($alldata);
                if(!$tagid){
					Db::rollback();
                    return return_format('',40101);
                }
				
				//插入子标签
				$childtagModel = new Studentchildtag;
				foreach(explode(',', $data['childname']) as $k=>$v){
					$childtagRet[$k]['fatherid'] = $tagid;
					$childtagRet[$k]['name'] = $v;
				}
				$childtag = $childtagModel->insertChildtagArr($childtagRet);
				if(!$childtag){
					Db::rollback();
					return return_format('',40101);
				}
				
			    //处理完成 提交事务
				Db::commit();
				return array('code'=>0,'info'=>lang('success'));
		}
    }
	
    /**
     * [updateTag 更新学生标签数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateTag($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',80090,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkTagExsit($data['id'])) return return_format('',80005);
                $where = ['id' => $data['id']];
                $tagdata  = [
                    'name'=> $data['name'],
                ];
				//开启事务
				Db::startTrans();
                $r1 = Db::table($this->table)->where($where)->update($tagdata);
				if($r1 === false){
					Db::rollback();
					return return_format('',40101);
				}
				
				$childtagModel = new Studentchildtag;
				$r2 = $childtagModel->delChildtagByfatherid($data['id']);
				if(!$r2){
					Db::rollback();
					return return_format('',40101);
				}
				
				//插入子标签
				foreach(explode(',', $data['childname']) as $k=>$v){
					$childtagRet[$k]['fatherid'] = $data['id'];
					$childtagRet[$k]['name'] = $v;
				}
				$childtag = $childtagModel->insertChildtagArr($childtagRet);
				if(!$childtag){
					Db::rollback();
					return return_format('',40101);
				}
				
				//return return_format('',0);
				
				//处理完成 提交事务
				Db::commit();
				return array('code'=>0,'info'=>lang('success'));
			}else{
				return return_format('',80004);
			}
		}
    }
	
    /**
     * [switchTeachStatus 修改学生标签的可用状态]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]       $id [学生标签id]
     * @param    [int]       $dataflag  [要修改的标记状态]
     * @return   [array]                [返回数组]
     */
    public function switchTagStatus($id,$status){
		if(!$this->checkTagExsit($id)) return return_format('',80005);
        $data['status']= $status ;
        $where = ['id'=>$id] ;
		
		//开启事务
		Db::startTrans();
        if(!Db::table($this->table)->where($where)->update($data)){
			Db::rollback();
			return return_format('',40101);
		}
		
		//保证只有一个标签被启用
		if($status == 1){
			if(Db::table($this->table)->where('status', 1)->where('id', 'neq', $id)->field('id')->find()){
				if(!Db::table($this->table)->where('id', 'neq', $id)->update(['status' => 0])){
					Db::rollback();
					return return_format('',40101);
				}
			}
		}
		
		//处理完成 提交事务
		Db::commit();
		return array('code'=>0,'info'=>lang('success'));
    }
    /**
     * [delTag 删除学生标签信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [学生标签id]
     * @return   [type]               [description]
     */
    public function delTag($id){
		//开启事务
		Db::startTrans();
    	$flag1 = Db::table($this->table)->where(['id'=>$id])->update(['delflag'=>1]);
		if(!$flag1){
			Db::rollback();
			return return_format('',40101);
		}
		
		$childtagModel = new Studentchildtag;
		$flag2 = $childtagModel->delChildtagByfatherid($id);
		if(!$flag2){
			Db::rollback();
			return return_format('',40101);
		}
		
		//处理完成 提交事务
		Db::commit();
		return array('code'=>0,'info'=>lang('success'));
		
    }
	
	/**
	 * checkCategoryExsit 检查标签是否存在
	 * @param tag id
	 * @return [bool]
	 */
	public function checkTagExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}
}
