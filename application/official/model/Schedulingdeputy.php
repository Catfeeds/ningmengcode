<?php
/***
*班级表附表的模型
*
*
**/
namespace app\official\model;
use think\Model;
use think\Db;

class Schedulingdeputy extends Model
{
	protected $pk    = 'id';
	protected $table = 'nm_schedulingdeputy';

	//获取班级列表
    /**
	 * PC 后台获取 开课列表
	 * @Author jcr
	 * @param $data    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array            [description]
	 */
    public function getClassesList($where,$orderbys,$pagenum,$pernum)
    {

    	$where['a.delflag'] = ['EQ',"1"];
    	$field = 'a.id,a.curriculumid,a.status,a.type,b.coursename,b.imageurl,a.totalprice,b.categorystr,c.organname';

    	if(empty($orderbys)){
    		$orderbys = 'a.id desc';
    	}else{
	        if($orderbys == 'id desc'){
	        	$orderbys = 'a.id desc';
	        }elseif($orderbys == 'id asc'){
	        	$orderbys = 'a.id asc';
	        }    		
    	}

        $pagenum = $pagenum?$pagenum:$this->pagenum;
		$lists = Db::table($this->table)->alias('a')->join('nm_curriculum b','a.curriculumid=b.id','LEFT')->join('nm_organ c','a.organid=c.id','LEFT')->page($pagenum,$pernum)->order($orderbys)->where($where)->field($field)->select();

		$count = Db::table($this->table)->alias('a')->join('nm_curriculum b','a.curriculumid=b.id','LEFT')->where($where)->count();
		
		$ret = [];
		$ret['lists'] = $lists;
		$ret['count'] = $count;
        $showPagenum = ceil($count / $pernum);
        $ret['pagenum'] = $showPagenum;
        $ret['pernum'] = $pernum;
		return $ret;

    }


    /**
	 * PC 后台获取 开课列表总行数
	 * @Author jcr
	 * @param $where    array       必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array            [description]
	 */
    public function getClassesListCount($where){

		$count = Db::table($this->table)->where($where)->count();
		return $count;
    }



	//修改班级上下架
	public function doOnOrOffClass($id,$status){

		try{
			$res = $this->save(['status'=>$status],['id'=>$id]);
			return return_format($res,0,lang('success'));			
		}catch(\Exception $e){
			return return_format($e->getMessage(),50004,lang('50004'));			
		}

	}

	//查看是否被删除
	public function isDelClassById($id){
		$res = Db::table($this->table)->where('id','=',$id)->where('delflag','<>','0')->find();
		if($res){
			return true;
		}else{
			return false;
		}	
	}

}	