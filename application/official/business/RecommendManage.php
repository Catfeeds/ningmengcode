<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\official\business;
use app\official\model\Category;
use app\official\model\Organ;
use app\official\model\Officialuseroperate;
class RecommendManage{
	/**
	 * [getCategoryRecomm description]
	 * @author wyx
	 * @DateTime 2018-04-21T20:19:58+0800
	 * @param    [int]                   $organid    [机构id]
	 * @return   [type]                         [description]
	 */
	public function getCategoryRecomm($organid){
		if($organid>0){

			$coursemodel = new Category;
			$data  = $coursemodel->getCategoryRecomm($organid);
			
			return return_format($data,0) ;

		}else{
			return  return_format('',40128) ;
		}
	}
	/**
	 * [getCategoryTree 获取机构有效的分类标签]
	 * @author wyx
	 * @DateTime 2018-05-26
	 * @param    [int]         $organid [机构标记id]
	 * @return   [array]                [description]
	 */
	public function getCategoryTree($organid){
			
		$coursemodel = new Category;
		//交换教师 排位序号
		$resultarr =  $coursemodel->getCategoryList($organid);
		$floor1 = [] ;
		$floor2 = [] ;
		$floor3 = [] ;
		//目前处理三级 分类
		foreach ($resultarr as $val) {
			if($val['prerank']==1){// 包含 1-2 级分类
				$floor1[$val['preid']] = ['id'=>$val['preid'],'title'=>$val['prename'],'rank'=>$val['prerank'],'fid'=>$val['prefid'],'children'=>[]] ;
				$floor2[$val['id']]    = ['id'=>$val['id'],'title'=>$val['name'],'rank'=>$val['rank'],'fid'=>$val['fid'],'children'=>[]] ;
			}else{// 2-3 级分类
				$floor3[$val['id']]    = ['id'=>$val['id'],'title'=>$val['name'],'rank'=>$val['rank'],'fid'=>$val['fid']] ;
			}
		}
		//将 数组三合并到 2
		foreach ($floor3 as $val) {
			if( isset($floor2[$val['fid']]) ){
				array_push($floor2[$val['fid']]['children'],$val);
			}
		}
		//将 数组三合并到 2
		foreach ($floor2 as $val) {
			if( isset($floor1[$val['fid']]) ){
				array_push($floor1[$val['fid']]['children'],$val);
			}
		}
		return return_format($floor1,0) ;
		
	}
	/**
	 * [updateCateRecomm 设置推荐状态]
	 * @author wyx
	 * @DateTime 2018-05-26
	 * @param    [int]           $organid   [机构标记]
	 * @param    [int]           $status    [教师推荐标记0，1]
	 */
	public function updateCateRecomm($organid,$ids){
		//入库数据 处理
		$idsarr = array_unique(explode('^',trim($ids,'^') ) );
		if( !empty($idsarr) ){
			$catemodel = new Category;
			// 获取已经 推荐的ids 
			$oldcomm = $catemodel->getCategoryRecomm($organid);
			$count = count($oldcomm) ;
			$oldids = array_column($oldcomm, 'id') ;
			//新增 推荐的ids
			$outer  = array_diff($idsarr, $oldids);
			$outernum= count($outer) ;

			if($count+$outernum<=10){
				$flag = $catemodel->updateCateRecomm($outer,$organid);
				//添加操作日志
				$operateobj = new Officialuseroperate();
				$operateFlag = $operateobj->addOperateRecord('添加了推荐分类');
				return  return_format($flag,0) ;
			}else{
				return  return_format($count+$outernum ,40129) ;
			}
		}else{
			return  return_format('',4013) ;
		}
	}
	/**
	 * [exchangeTeacherPos 交换两个id对应的序列]
	 * @author wyx
	 * @DateTime 2018-04-21T19:12:05+0800
	 * @param    [int]         $organid [机构标记id]
	 * @param    [int]         $id1     [推荐分类id]
	 * @param    [int]         $id2     [推荐分类id]
	 * @return   [array]                [description]
	 */
	public function exchangeCatePos($organid,$id1,$id2){
		if($id1>0 && $id2>0){
			//添加操作日志
			$operateobj = new Officialuseroperate();
			$operateFlag = $operateobj->addOperateRecord('交换了推荐分类');
			$techmodel = new Category;
			//交换教师 排位序号
			return  $techmodel->exchangeSort($organid,[$id1,$id2]);
		}else{
			return  return_format('',40133) ;
		}
		
	}
    /**
     *  [delRecomm 删除官方推荐分类]
     *	@author wyx
     *	@param    int     $organid        机构标识id
     *	@param    int     $id             要删除的分类id
     *  @return   [type]                   [description]
     *  
     *
     */
    public function delRecomm($organid,$id){
        if($organid>0 && $id> 0){
	        $cateobj = new Category;
	        
	        //更新数据
	        $flag = $cateobj->
            delRecomm($id,$organid);
	        if($flag){
				//添加操作日志
				$operateobj = new Officialuseroperate();
				$operateFlag = $operateobj->addOperateRecord('删除了推荐分类');
				return  return_format($flag,0) ;
			}else{
				return  return_format($flag ,40131) ;
			}
        }else{
        	return  return_format('',40132) ;
        }
    }
    /**
     *	[getRecommOrgan 获取所有的免费机构]
     *	@author wyx
     *	
     *
     */
    public function getRecommOrgan(){
    	$organobj = new Organ;
	        
        //获取所有的免费机构 
        $recommdata = $organobj->getRecommOrgan();

        return return_format($recommdata,0) ;

    }
    /**
     *	[getFreeOrgan 获取所有的免费机构]
     *	@author wyx
     *	@param  $name      根据机构名字或者id搜索机构
     *	@param  $pagenum   当前页码
     *	@param  $limit     每页显示条数
     *	
     *
     */
    public function getFreeOrgan($name,$pagenum,$limit){
	    
    	$pagenum = $pagenum>0 ? $pagenum : 1 ;
    	$offset = ($pagenum-1)* $limit ;

    	$organobj = new Organ;
        //获取所有的免费机构 
        $freeorgan = $organobj->getFreeOrgan($name,$limit,$offset);
        $total     = $organobj->getFreeOrganCount($name);

        //返回数组组装
		$result = [
			 	'data'=>$freeorgan,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=>$limit ,// 每页多少条记录
			 		'pagenum' =>$pagenum ,//当前页码
			 		'total'   => $total // 符合条件总的记录数
			 	]
			] ;

        return return_format($result,0) ;

    }
    /**
	 * [updateFreeOrgan 设置机构推荐状态]
	 * @author wyx
	 * @DateTime 2018-05-26
	 * @param    [int]           $ids    [新增的推荐的机构的id 多个id使用^ 隔开]
	 */
	public function updateFreeOrgan($ids){
		//入库数据 处理
		$idsarr = array_unique(explode('^',trim($ids,'^') ) );
		if( !empty($idsarr) ){
			$freeorgan = new Organ;
			// 获取已经 推荐的ids 
			$oldcomm = $freeorgan->getRecommOrgan();
			$count = count($oldcomm) ;
			$oldids = array_column($oldcomm, 'id') ;
			//新增 推荐的ids
			$outer  = array_diff($idsarr, $oldids);
			$outernum= count($outer) ;

			if($count+$outernum<=10){
				$flag = $freeorgan->updateFreeOrgan($outer);
				//添加操作日志
				$operateobj = new Officialuseroperate();
				$operateobj->addOperateRecord('添加了推荐机构');
				return  return_format($flag,0) ;
			}else{
				return  return_format($count+$outernum ,40135) ;
			}
		}else{
			return  return_format('',40136) ;
		}
	}
	/**
	 * [exchangeOrganPos 交换两个id对应的序列]
	 * @author wyx
	 * @DateTime 2018-05-28
	 * @param    [int]         $id1     [推荐分类id]
	 * @param    [int]         $id2     [推荐分类id]
	 * @return   [array]                [description]
	 */
	public function exchangeOrganPos($id1,$id2){
		if($id1>0 && $id2>0){
			//添加操作日志
			$operateobj = new Officialuseroperate();
			$operateFlag = $operateobj->addOperateRecord('交换了推荐机构次序');
			$techmodel = new Organ;
			//交换教师 排位序号
			return  $techmodel->exchangeSort([$id1,$id2]);
		}else{
			return  return_format('',40137) ;
		}
		
	}
    /**
	 * [delCommOrgan 交换两个id对应的序列]
	 * @author wyx
	 * @DateTime 2018-05-28
	 * @param    [int]         $id1     [推荐分类id]
	 * @param    [int]         $id2     [推荐分类id]
	 * @return   [array]                [description]
	 */
	public function delCommOrgan($organid){
		if($organid > 1){
			//添加操作日志
			$operateobj = new Officialuseroperate();
			$operateFlag = $operateobj->addOperateRecord('删除了推荐机构');
			$techmodel = new Organ;
			//交换教师 排位序号
			return  $techmodel->delCommOrgan($organid);
		}else{
			return  return_format('',40140) ;
		}
		
	}
    
	
	
}



?>