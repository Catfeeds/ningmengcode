<?php
namespace app\official\business;
use login\Authorize;
use think\Session;
use app\official\model\Schedulingdeputy;
use app\admin\model\Category; //获取多级分类名称

class CourseManage{



	//获取班级列表
	public function getClasseslist($data){

		$where = [];
        if(!empty($data['coursename'])){
            $data['coursename'] = $this->filterKeywords($data['coursename']);
        	$coursename = $data['coursename'];
        	//模糊查询
            $where['coursename']  = ['like',"%".$coursename."%"];
        }
		$obj = new Schedulingdeputy();
		$ret =  $obj->getClasseslist($where,$data['orderbys'],$data['pagenum'],$data['pernum']);
		foreach($ret['lists'] as $k => $v){
			$category = new Category();
			$ret['lists'][$k]['categoryname'] = $category->getCategoryName(explode('-',$v['categorystr']));
			$ret['lists'][$k]['classtype'] = $this->getClassType($v['type']);
		}
		
		return $ret;
	}


	//获取班级附表中的班级的总数目(没有筛选)
	public function getClasseslistTotalCount(){

		$where = [];
		$where['delflag'] = ['EQ',"1"];
		$obj = new Schedulingdeputy();
		$count = $obj->getClassesListCount($where);
		$data = [];
		$data['count'] = $count;
		return return_format($data,0,lang('success'));	
	}


	//强制上下架班级
	public function doOnOrOffClass($id,$status){

		if(!isset($id) || !isset($status)){
			return return_format('',50000,lang('50000'));
		}
		$obj = new Schedulingdeputy();
		$res = $obj->isDelClassById($id);
		if(!$res){
			return return_format('',50040,lang('50040'));
		}
		$arr = [0,1];
		if(!in_array($status,$arr)){
			return return_format('',50048,lang('50048'));
		}
		$ret =  $obj->doOnOrOffClass($id,$status);
		return $ret;
	}

    /**
     * //过滤搜索字符串
     * @Author zzq
     * @param $str   string  [搜索字符串]
     * @return string  [返回信息]
     * 
     */
    public function filterKeywords($str){
        $str = strip_tags($str);   //过滤html标签
        $str = htmlspecialchars($str);   //将字符内容转化为html实体
        $str = addslashes($str);

        return $str;
    }

    //根据type获取班级类型
    public function getClassType($type){
        $str = '';
        switch ($type) {
            case 1:
                $str = "1对1";
                break;
            case 2:
                $str = "小班课";
                break;
            case 3:
                $str = "直播课";
                break;         
            default:
                $str = "未知";
                break;
        }
        return $str;    	
    }
}