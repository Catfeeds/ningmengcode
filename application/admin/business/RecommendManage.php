<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\admin\business;
use app\admin\model\Teacherinfo;
use app\admin\model\Scheduling;
use app\admin\model\Organslideimg;
class RecommendManage{
	/**
	 * [getCourseList description]
	 * @Author
	 * @DateTime 2018-04-21T20:19:58+0800
	 * @param    [string]                $coursename [课程名字]
	 * @param    [int]                   $pagenum    [分页序号]
	 * @param    [int]                   $organid    [机构id]
	 * @param    [int]                   $limit      [description]
	 * @return   [type]                             [description]
	 */
	public function getCourseList($coursename,$pagenum,$limit){

		$where = [] ;
		!empty($coursename) && $where['cm.coursename'] = ['like',$coursename.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		// 开始 2018-06
		$where['sg.delflag'] = 1;
		$where['sg.status'] = 1;
		$where['sg.classstatus'] = ['in','0,1,2,3'];
		// 解释

		$order = 'recommend desc,sortnum desc' ;

		$coursemodel = new Scheduling;
		$data  = $coursemodel->getCourseList($where,$limitstr,$order);
		if($data){
			$teach = new Teacherinfo();
			foreach ($data as $k => $v){
				$data[$k]['teachname'] = $teach->getTeacherId($v['teacherid'],'teachername')['teachername'];
			}
		}
		$total = $coursemodel->getCourseListCount($where);
		//返回数组组装
		$result = [
				'data'=>$data,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
		return return_format($result,0) ;


	}
	/**
	 * [exchangeCoursePos 交换课程两个id对应的序列]
	 * @Author
	 * @DateTime 2018-04-21T19:12:05+0800
	 * @param    [int]         $organid [机构标记id]
	 * @param    [int]         $id1     [开课表id]
	 * @param    [int]         $id2     [开课表id]
	 * @return   [array]                [description]
	 */
	public function exchangeCoursePos($id1,$id2){
		if($id1>0 && $id2>0){
			
			$coursemodel = new Scheduling;
			//交换教师 排位序号
			return  $coursemodel->exchangeSort([$id1,$id2]);
		}else{
			return  return_format('',40046) ;
		}
		
	}
	/**
	 * [setCourseFlag 设置课程推荐状态]
	 * @Author
	 * @DateTime 2018-04-21T19:51:42+0800
	 * @param    [int]           $organid   [机构标记]
	 * @param    [int]           $courseid  [开课表id]
	 * @param    [int]           $status    [课程推荐标记0，1]
	 */
	public function setCourseFlag($courseid,$status){
		if($courseid>0 && in_array($status,[0,1]) ){
			
			$coursemodel = new Scheduling;
			//设置教师推荐 标记
			$flag = $coursemodel->setRecommendFlag($courseid,$status);
			if($flag){
				return  return_format($flag,0) ;
			}else{
				return  return_format($flag,40049) ;
			}
		}else{
			return  return_format('',40048) ;
		}
	}
	/**
	 * 获取教师列表
	 * @Author wyx
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $organid 机构标记id      必填
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getTeacherList($nickname,$pagenum,$limit){

		$where = [] ;
		!empty($nickname) && $where['nickname'] = ['like',$nickname.'%'] ;
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['delflag'] = 1;// 删除标识 0 删除 1未删除
		//拉取字段
		$field = 'teacherid,teachername,nickname,recommend,identphoto,slogan' ;
		//排序方式
		$order = 'recommend desc,sortnum desc' ;

		$techmodel = new Teacherinfo;
		$listarr = $techmodel->getTeacherList($where,$field,$limitstr,$order);
		//总记录数
		$total = $techmodel->getTeacherListCount($where);
		//返回数组组装
		$result = [
				'data'=>$listarr,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
		return return_format($result,0) ;


	}
	/**
	 * [exchangeTeacherPos 交换两个id对应的序列]
	 * @Author
	 * @DateTime 2018-04-21T19:12:05+0800
	 * @param    [int]         $organid [机构标记id]
	 * @param    [int]         $id1     [教师id]
	 * @param    [int]         $id2     [教师id]
	 * @return   [array]                [description]
	 */
	public function exchangeTeacherPos($id1,$id2){
		if($id1>0 && $id2>0){
			$techmodel = new Teacherinfo;
			//交换教师 排位序号
			return  $techmodel->exchangeSort([$id1,$id2]);
		}else{
			return  return_format('',40051) ;
		}
		
	}
	/**
	 * [setTeacherFlag 设置推荐状态]
	 * @Author
	 * @DateTime 2018-04-21T19:51:42+0800
	 * @param    [int]           $organid   [机构标记]
	 * @param    [int]           $teacherid [教师id]
	 * @param    [int]           $status    [教师推荐标记0，1]
	 */
	public function setTeacherFlag($teacherid,$status){
		if($teacherid>0 && in_array($status,[0,1]) ){
			$techmodel = new Teacherinfo;
			//设置教师推荐 标记
			$flag = $techmodel->setRecommendFlag($teacherid,$status);
			if($flag){
				return  return_format($flag,0) ;
			}else{
				return  return_format($flag,40054) ;
			}
		}else{
			return  return_format('',40053) ;
		}
	}
	/**
	 * [addTeacherImage 设置推荐老师照片和标语]
	 * @Author wyx
	 * @DateTime 2018-05-8
	 * @param    [array]         $data   [添加数据]
	 * @param    [int]           $organid [机构标记]
	 */
	public function addTeacherImage($data){
		if(isset($data['teacherid']) && $data['teacherid']> 0 && !empty($data['image']) && !empty($data['profile'])){
			$techmodel = new Teacherinfo;
			//设置教师推荐 标记
			$flag = $techmodel->addTeacherImage($data);
			if($flag){
				return  return_format($flag,0) ;
			}else{
				return  return_format($flag,40055) ;
			}
		}else{
			return  return_format('',40056) ;
		}
	}
	/**
	 * [getOrganSlide 设置推荐状态]
	 * @Author wyx
	 * @DateTime 2018-04-21T19:51:42+0800
	 * @param    [int]           $organid   [机构标记]
	 * @param    [int]           $teacherid [教师id]
	 * @param    [int]           $status    [教师推荐标记0，1]
	 */
	public function getOrganSlide(){

			$slideimgmodel = new Organslideimg;
			//设置教师推荐 标记
			$flag = $slideimgmodel->getOrganSlide();
			// if($flag){
				return  return_format($flag,0) ;
			// }else{
				// return  return_format($flag,-1,'设置失败') ;
			// }

	}
	/**
     *  [addSlideImage 添加机构轮播图]
     *	@param    array   $data           要入库的数据
     *	@param    int     $organid        机构标识id
     *  @return   [type]                   [description]
     *  
     *
     */
    public function addSlideImage($data){

		$slideimgobj = new Organslideimg;
		//获取机构的轮播图数量
		$result = $slideimgobj->getOrganSlide();
		$count = count($result);
		//轮播图不能超过 5个
		if($count>=5){
			return return_format('',40059) ;
		}elseif($count==0){
			$sortnum = 1 ;
		}else{
			$sortnum = 1 ;
			foreach ($result as $val) {
				$sortnum = $val['sortid'] > $sortnum ? $val['sortid'] : $sortnum ;
			}
			$sortnum++ ;
		}
		//添加数据
		return $slideimgobj->addSlideImage($sortnum,$data);


    }
    /**
     *  [editSlideImage 添加机构轮播图]
     *	@param    array   $data           要入库的数据
     *	@param    int     $organid        机构标识id
     *  @return   [type]                   [description]
     *  
     *
     */
    public function editSlideImage($data){
        if(isset($data['id']) && $data['id']> 0){
	        $slideimgobj = new Organslideimg;
	        
	        //更新数据
	        return $slideimgobj->editSlideImage($data);

        }else{
        	return  return_format('',40063) ;
        }
    }
    /**
     *  [delSlideImage 添加机构轮播图]
     *	@param    int     $id             要删除的图片的id
     *	@param    int     $organid        机构标识id
     *  @return   [type]                   [description]
     *  
     *
     */
    public function delSlideImage($id){
        if($id> 0){
	        $slideimgobj = new Organslideimg;
	        
	        //更新数据
	        return $slideimgobj->delSlideImage($id);

        }else{
        	return  return_format('',40066) ;
        }
    }
	
	
}



?>