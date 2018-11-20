<?php
namespace app\teacher\model;
use think\Model;
use think\Db;
use think\Validate;
class Filemanage extends Model
{
	protected $table = 'nm_filemanage';
    protected $organid;
    protected $pagenum; //每页显示行数
    public function __construct(){
        $this->pagenum = config('paginate.list_rows');
    }

    // 课程添加验证规则
    protected $rule = [
        'showname'  => 'require',
    ];
    protected $message = [];
    //自定义初始化
    protected function initialize(){
    	parent::initialize();
    	$this->message = [
    		'showname.require' => lang('23012')
    	];
    }
    


	/*
	 * [findWeekMark 获取对应的资源列表]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $data     数据源
	 * @param    [int]        $limit    第几页
	 * @return   [type]       $pagenum  一页几条
	 */
	public function getFileList($data,$limit,$pagenum){
		$data = where_filter($data,['fatherid','teacherid','showname','filetype','usetype']);
 		$pagenum = $pagenum?$pagenum:$this->pagenum;
        //$cc = Db::raw('select * from nm_filemanage where teacherid = '.$data['teacherid'].' or teacherid is null');
        if($data['filetype'] == 0){
            //共有
            $where = ['fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
                'delflag'  => 1,
                'filetype' => 0,
                'usetype'=>$data['usetype']];
        }elseif($data['filetype'] == 1){
            //私有
            $where  = [
                'fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
                'delflag'  => 1,
                'teacherid' => [['eq',$data['teacherid']],['eq',0],'or'],
                //'teacherid' => ['in',[0,$data['teacherid']]],
                'filetype' => 1,
                'usetype'=>$data['usetype']
            ] ;
        }else{
            //显示当前老师和共有的所有课件
            $where  = [
                'fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
                'delflag'  => 1,
                'teacherid' => [['eq',$data['teacherid']],['eq',0],'or'],
                'usetype' => $data['usetype']
            ] ;
        }
// 		$where  = [
// 					'fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
// 					'delflag'  => 1,
//					'teacherid' => [['eq',$data['teacherid']],['eq',0],'or'],
//                    'filetype' => $data['filetype']
// 				] ;
 		if(isset($data['showname'])){
 			 	$where['showname'] = array('like','%'.$data['showname'].'%');
 		}
 		$field = 'fileid,fileurl,showname,fatherid,addtime,sizes,usetype,relateid,cosurl' ;

 		$info['data'] = Db::table($this->table)->field($field)->where($where)->page($limit,$pagenum)->select();
        //return Db::table($this->table)->getLastSql();
 		$info['pageinfo'] = array('pagesize'=>$pagenum,'pagenum'=>$limit,'total'=>$this->getFileCount($where));
 		return $info;
 	}


	/**
	 * [findWeekMark 获取对应的资源列表]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   [机构id]
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
	public function getFileCount($where){
	 	return Db::table($this->table)->where($where)->count();
	 	//return Db::table($this->table)->getLastSql();
	}


	/*
	 * [addFile 新建文件夹]
	 * @param [type] $data [description]
	 */
	public function addFile($data){
		if(isset($data['fileid'])){
			// 编辑
			$list = where_filter($data,array('delflag','filename','teacherid'));
			//$id = Db::table($this->table)->where('fileid','eq',$data['fileid'])->where('teacherid','eq',$data['teacherid'])->update($data);
			//删除文件夹时自动删除文件夹下的课件
            $where = $data['filetype']==1?['filetype'=>1,'teacherid'=>$data['teacherid']]:['filetype'=>0];
            //$where = [['filetype'=>1,'teacherid'=>$data['teacherid']],['filetype'=>0],'or'];
			$id = Db::table($this->table)
                ->where('fatherid|fileid','eq',$data['fileid'])
                ->where($where)
                ->update($list);
            //return(print_r(Db::table($this->table)->getlastsql()));

			if($id){
				return return_format('',0,lang('success'));
			}else{
				return return_format('',20019,lang('20019'));
			}			
			//print_r(Db::table($this->table)->getlastsql());
		}else{
			// 添加模块
			$validate = new Validate($this->rule, $this->message);
	        if(!$validate->check($data)){
	            return array('code'=>20005,'info'=>$validate->getError());
	        }

			$adddata = where_filter($data,array('showname','fatherid','fileurl','sizes','relateid','filetype','usetype'));
			$adddata['addtime'] = time();
			$adddata['teacherid'] = $data['teacherid'];
			$adddata['filetype'] = $data['filetype'];
            if(!isset($adddata['fatherid'])){
                $adddata['usetype'] = 0;
            }
			$id = Db::table($this->table)->insert($adddata);
			if($id){
				return return_format('',0,lang('success'));
			}else{
				return return_format('',20004,lang('20004'));
			}
		}

	}

	// /**
	// *删除文件夹
	// */
	// public function delfold($data){
	// 	// 编辑
	// 		$data = where_filter($data,array('fatherid','delflag','filename','teacherid'));
	// 		$id = Db::table($this->table)
	// 		          ->where(['fatherid'=>$data['fileid'],'fileid'=>$data['fileid']],'or')
	// 		          ->where('teacherid','eq',$data['teacherid'])
	// 		          ->update($data);
	// 		// print_r(Db::table($this->table)->getlastsql());
	// 		// exit();
	// 		return $id?array('code'=>0,'info'=>$id):array('code'=>20088,'info'=>'操作失败');

	// }



	/*
	 * [getWarefile 获取该课时的课件列表]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $data     数据源
	 * @param    [int]        $limit    第几页
	 * @return   [type]       $pagenum  一页几条
	 */
	public function getWarefile($data){
		//$pagenum = $pagenum?$pagenum:$this->pagenum;
		$where  = [
			        'fileid' => ['in',$data['courseware']],
					'delflag'   => 1
				] ;
		//如果传入fatherid则加入该条件否则
		if (isset($data['fatherid'])){
		    $where['fatherid'] = $data['fatherid'];
        }else{
		    $where['fatherid'] = ['neq',0];
        }
		$field = 'fileid,fileurl,showname,fatherid,addtime,sizes' ;
		$info['data'] = Db::table($this->table)->field($field)->where($where)->select();
		$info['pageinfo'] = $this->getFileCount($where);
		//print_r(Db::table($this->table)->getlastsql());
		return $info;
	}



}
