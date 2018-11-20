<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\admin\business;
use app\admin\model\Studentinfo;
use app\admin\model\Hjxappstudentinfo;
use app\admin\model\Ordermanage;
use \Messages;
class StudentManage{
	/**
	 * [getUserList description]
	 * @Author wyx
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getUserList($mobil,$nickname,$pagenum,$limit){
		$where = [] ;
		!empty($mobil) && $where['s.mobile'] = ['like', '%'.$mobil.'%'];
		!empty($nickname) && $where['s.nickname'] = ['like', '%'.$nickname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['s.delflag'] = 1;

		$usermodel = new Studentinfo;
		$resultarr = $usermodel->getUserList($where,$limitstr);
		
		foreach ($resultarr as &$val) {
			$val['childtagname'] = !empty($val['childtag']) ? join(',', $usermodel->getnameBychildtag($val['childtag'])): '';
			$val['logintime'] = date('Y-m-d H:i:s',$val['logintime']);
		}
		
		$total = $usermodel->getUserListCount($where);
		//返回数组组装
		$result = [
				'data'=>$resultarr,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;

		return return_format($result,0) ;
	}
	/**
	 * [getUserDetail 获取学员的详细信息]
	 * @Author wyx
	 * @DateTime 2018-04-20T13:56:36+0800
	 * @param    [int]        $studentid [学生标识id]
	 * @return   [array]                 [description]
	 */
	public function getUserDetail($studentid){
		if($studentid>0){
			$usermodel = new Studentinfo;
			$data = $usermodel->getUserDetail($studentid);
			if(!empty($data)){
				$data['birthday']  = date('Y-m-d',$data['birth']);
				$data['logintime'] = $data['logintime']?date('Y-m-d H:i:s',$data['logintime']):'-';
				$data['addtime'] = date('Y-m-d H:i:s',$data['addtime']);

				//获取学生购买过的课程
				$orderobj  = new Ordermanage;
		        $orderlist = $orderobj->getStudentOrder($studentid) ;
				return return_format(['data'=>$data,'courselist'=>$orderlist],0) ;
			}else{
				return return_format([],0) ;
			}
		}else{
			return return_format([],40069) ;
		}
	}
	/**
	 * 机构后台 不再可添加学生  2018-06-08
	 * [addStudentInfo 新增学员]
	 * @Author
	 * @DateTime 2018-04-20T16:07:34+0800
	 * @param    [array]      $data    [用户需要入库数据，必须规定可更新字段]
	 * @return []
	 */
	/* public function addStudentInfo($data){
		$allowfield = ['imageurl','mobile','nickname','username','sex','country','province','city','birth','profile','prphone','status'];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield) ;

        $usermodel = new Studentinfo;
        $ret = $usermodel->addStudent($newdata); 
        return $ret ;

	} */
	/**
	 * [updateStudent 更新学生信息]
	 * @Author wyx
	 * @DateTime 2018-04-20T20:53:36+0800
	 * @param    [array]       $data    [需要更新的字段]
	 * @return   [array]                
	 */
	/* public function updateStudentInfo($data){
		$allowfield = ['id','imageurl','mobile','nickname','username','sex','country','province','city','birth','profile','prphone','status'];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield) ;

        $usermodel = new Studentinfo;
        $ret = $usermodel->updateStudent($newdata); 

        return $ret ;

	} */
	/**
	 * [changeUserStatus 更改学生状态]
	 * @Author wyx
	 * @DateTime 2018-04-20T20:53:36+0800
	 * @param    [int]         $userid    [需要更新的学生id]
	 * @param    [int]         $flag      [机构标记id]
	 * @return   [array]                
	 */
	public function changeUserStatus($userid,$flag){
		if($userid>0 && in_array($flag, [0,1]) ){
			$usermodel = new Studentinfo;
        	$ret = $usermodel->changeUserStatus($userid,$flag); 
        	return $ret ;
		}else{
			return return_format([],40085) ;
		}	
	}
	/**
	 * [delStudent 删除学生信息，伪删除]
	 * @Author wyx
	 * @DateTime 2018-04-20T21:15:39+0800
	 * @param    [int]                   $userid  [要删除的学生id]
	 * @return   [array]                          [操作结果]
	 */
	/* public function delStudent($userid){
		if($userid > 0){
			$usermodel = new Studentinfo;
	        $ret = $usermodel->delStudent($userid); 
	        if($ret){
	        	return return_format($ret,0) ;
	        }else{
	        	return return_format('',40087) ;
	        }
		}else{
			return return_format([],40086) ;
		}
	} */
	
	/*
     * app推送
     * @Author lc
     */
    public function sendStudentMessage($data){
		if(!is_array($data['studentids'])){
			 return return_format('', 33000, lang('param_error'));
		}
		if(empty($data['title']) || empty($data['content'])){
			 return return_format('', 33000, lang('param_error'));
		}
		
        foreach($data['studentids'] as $k => $v){
            $contentarr[$k]['content'] = $data['content'];
            $contentarr[$k]['userid'] = $v;
            $contentarr[$k]['title'] = $data['title'];
            $contentarr[$k]['usertype'] = 3;//学生
           // $contentarr[$k]['externalid'] = $schedulingid;//班级id
        }
        $msgobj = new Messages();
        $type = 11;
        $msg = $msgobj->addMessagearr($contentarr,$type);
        return return_format($msg,0,lang('success'));
    }
	
	/**
	 * [getHjxUserList description]
	 * @Author lc
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getHjxUserList($mobil,$nickname,$pagenum,$limit){
		$where = [] ;
		!empty($mobil) && $where['s.mobile'] = ['like', '%'.$mobil.'%'];
		!empty($nickname) && $where['s.nickname'] = ['like', '%'.$nickname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		$where['s.delflag'] = 1;

		$usermodel = new Hjxappstudentinfo;
		$resultarr = $usermodel->getUserList($where,$limitstr);
		
		foreach ($resultarr as &$val) {
			$val['logintime'] = date('Y-m-d H:i:s',$val['logintime']);
		}
		
		$total = $usermodel->getUserListCount($where);
		//返回数组组装
		$result = [
				'data'=>$resultarr,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;

		return return_format($result,0) ;
	}
	
	/**
	 * [changeHjxUserStatus 更改学生状态]
	 * @Author wyx
	 * @DateTime 2018-04-20T20:53:36+0800
	 * @param    [int]         $userid    [需要更新的学生id]
	 * @param    [int]         $flag      [机构标记id]
	 * @return   [array]                
	 */
	public function changeHjxUserStatus($userid,$flag){
		if($userid>0 && in_array($flag, [0,1]) ){
			$usermodel = new Hjxappstudentinfo;
        	$ret = $usermodel->changeUserStatus($userid,$flag); 
        	return $ret ;
		}else{
			return return_format([],40085);
		}
	}
}
