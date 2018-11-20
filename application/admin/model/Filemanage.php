<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Filemanage extends Model
{	
	protected $table = 'nm_filemanage';
    protected $pagenum; //每页显示行数

    // 课程添加验证规则
    protected $rule = [
        'showname'  => 'require'
    ];

    protected $message  = [];
	//自定义初始化
	protected function initialize(){
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			'showname.require' => lang('10507')
		];
	}


	/**
	 * [getFileList 获取对应的资源列表]
	 * @Author jcr
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $data     数据源
	 * @param    [int]        $limit    第几页
	 * @return   [type]       $pagenum  一页几条
	 */
	public function getFileList($data,$limit,$pagenum){
		$pagenum = $pagenum?$pagenum:$this->pagenum;
		$where  = [
					'fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
					'delflag'  => 1 ,
				];
		if(isset($data['teacherid'])){
			$where['teacherid'] = $data['teacherid'];
		}

		if(isset($data['filetype'])){
			$where['filetype'] = $data['filetype'];
			if($data['filetype']==0 && isset($where['teacherid'])){
				unset($where['teacherid']);
			}
		}
		if($where['fatherid'] != 0 && isset($data['usetype'])){
			$where['usetype'] = $data['usetype'];
		}

		if(isset($data['showname'])){
			$where['showname'] = array('like',$data['showname'].'%');
		}
		$field = 'fileid,fileurl,showname,fatherid,addtime,sizes,usetype,teacherid,cosurl,relateid' ;
		$info['data'] = Db::table($this->table)->field($field)->where($where)->page($limit,$pagenum)->select();
		$info['pageinfo'] = array('pagesize'=>$pagenum,'pagenum'=>$limit,'total'=>$this->getFileCount($where));
		return $info;
	}


	/**
	 * [getFileCount 获取对应的资源列表]
	 * @Author jcr
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   [机构id]
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
	public function getFileCount($where){
		return Db::table($this->table)->where($where)->count();
	}



	/**
	 * [addFile 新建文件夹]
	 * @php jcr
	 * @param [type] $data [description]
	 * @return array()
	 */
	public function addFile($data){
		if(isset($data['fileid'])){
			// 编辑
            $sdata = where_filter($data,array('delflag','fileurl','relateid'));
            $id = Db::table($this->table)->where('fileid',$data['fileid'])->update($sdata);
		}else{
			// 添加模块
			$validate = new Validate($this->rule, $this->message);
	        if(!$validate->check($data)){
	            return array('code'=>10075,'info'=>$validate->getError());
	        }

			$adddata = where_filter($data,array('showname','fatherid','fileurl','sizes','relateid','teacherid','filetype','usetype','cosurl'));
			$adddata['addtime'] = time();
			$adddata['filetype'] = isset($adddata['filetype'])?$adddata['filetype']:0;
			if(!isset($adddata['fatherid'])){
			    $adddata['usetype'] = 0;
            }
            //dump($adddata);
			$id = Db::table($this->table)->insert($adddata);
		}
		return $id?array('code'=>0,'info'=>$id):array('code'=>10109,'info'=>lang('error'));
	}


	/**
	 * [addAllFile 批量添加对应的课件]
	 * @author JCR
	 * @param $data
	 * @return bool
	 */
	public function addAllFile($data){
		if(!$data) return false;
		$typenum =  Db::table($this->table)->insertAll($data);
		return $typenum;
	}





	/**
	 * [getIdIn 根据id获取对应资源的 网课端id]
	 * @php jcr
	 * @param  [type] $ids [description]
	 * @return array()
	 */
	public function getIdIn($ids){
		return Db::table($this->table)->where('fileid','in',$ids)->field('fileid,relateid')->select();
	}


	/**
	 * [getIdIn 根据id获取对应资源的]
	 * @php jcr
	 * @param  [type] $ids [description]
	 * @return array()
	 */
	public function getById($id){
		return Db::table($this->table)->where('fileid',$id)->field('fileid,filetype')->find();
	}


	/**
	 * [getIdInField 根据id获取对应资源id和名称]
	 * @php jcr
	 * @param  [type] $ids [description]
	 * @return array()
	 */
	public function getIdInField($ids){
		return Db::table($this->table)->where('fileid','in',$ids)->where('delflag',1)->field('fileid as id,showname as name')->select();
	}


	public function getListAll(){
		$where = [
			'fatherid'	=>['neq',0],
			'delflag'	=>1,
			'relateid'	=> NULL,
			'usetype'	=>2,
		];
		return Db::table($this->table)->where($where)->field('fileid,fileurl')->select();
	}





	
	

}
