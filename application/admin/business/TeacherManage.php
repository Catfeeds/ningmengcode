<?php
/**
 * 机构端教师管理 业务逻辑层
 *
 *
 */
namespace app\admin\business;
use app\admin\model\Teacherinfo;
use app\admin\model\Teachertime;
use app\admin\model\Scheduling;
use app\admin\model\Allaccount;
use app\admin\model\Accessroleuser;
class TeacherManage{
	/**
	 * 获取教师列表
	 * @Author wyx
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
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
		$where['delflag'] = 1;

		$techmodel = new Teacherinfo;
		$field = 'teacherid,prphone,mobile,nickname,addtime,accountstatus' ;

		$return = $techmodel->getTeacherList($where,$field,$limitstr);
		$total  = $techmodel->getTeacherListCount($where);

		if( !empty($return) ){//如果不为空 获取到 id
			//$teachids = array_column($return, 'teacherid') ;
			//$logintime = $techmodel->getLoginTime($teachids) ;

			//将最后登陆时间合并到查询结果中
			foreach ($return as $key => &$val) {
				//$val['lastlogin'] = isset($logintime[$val['teacherid']]) ? date('Y-m-d H:i:s',$logintime[$val['teacherid']]) : '' ;
				$val['addtime'] = date("Y-m-d H:i:s", $val['addtime']);
			}
		}
		//处理整合教师的最后登录时间
		if( empty($return) ){//没有符合条件的数据
			return return_format([],40088) ;
		}else{
			$result = [
				'data'=>$return,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($result,0) ;
		}
	}
	
	/**
	 * 获取教师详细信息
	 * @Author wyx
	 * @param $teachid 教师id
	 * @param $allaccountid 机构人员总表id 从session 中获取
	 * @return object
	 *
	 */
	public function getTeachInfo($teachid){
		$techmodel = new Teacherinfo;
		//返回教师基本信息
		$field = 'teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus,addtime,sex,country,province,city,profile,birth,school,grade,class' ;
		$baseinfo = $techmodel->getTeacherData($field,$teachid);

		//!empty($baseinfo['addtime']) &&	$baseinfo['addtime'] = date('Y-m-d H:i:s',$baseinfo['addtime']);
		//!empty($baseinfo['birth']) &&	$baseinfo['birth'] = date('Y-m-d',$baseinfo['birth']);

		if( empty($baseinfo) ){//没有符合条件的教师数据
			return return_format([],40089) ;
		}else{
			//根据教师id 获取总表的id
			//$allaccount = new Allaccount;
			//$retarr = $allaccount->getTeacherAccount($teachid);
			//$allaccountid = $retarr['id'] ;
			//获取教师最近登陆时间
			//$allaccountid 从session 中取
			//$basetime = $techmodel->getLoginTime($allaccountid);
			//$baseinfo['logintime'] = isset($basetime[0]) ? date('Y-m-d H:i:s',$basetime[0]) : '' ;
			
			$accessmodel = new Accessroleuser;
			$where = [
				'uid' => $teachid,
				'usertype' => 1
			];
			$roleidarr = $accessmodel->getRoleidByCondition($where);
			$roleidstr = '';
			$ar = [];
			foreach($roleidarr as $val) $ar[] = $val == 2 ? 1 : 2;
			$return = ['baseinfo'=>$baseinfo, 'roleid'=>implode(',', $ar)];
			return return_format($return,0);
		}

	}
	
    /**
     * 保存教师的空余时间设置
     * @Author wyx
     * @param $week 7天的空闲设置
     * @param $teachid 教师id
     * @return 
     * URL:/admin/teacher/updateWeekIdle
     */
    /* public function updateWeekIdle($week,$teachid){
    	if( count($week) == 7 && $teachid>0){
    		$flagarr = [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7] ;
    		foreach ($week as $val) {
    			unset($flagarr[$val['weekday']]);
    		}
    		if(!empty($flagarr)) return return_format('',40094) ;
	        $weekobj = new Teachertime;
	        //更新教师拥有的标签
	        return $weekobj->updateWeekIdle($week,$teachid);
    		
    	}else{
    		return return_format('',40093) ;
    	}
    } */
	/**
	 * 编辑教师资料时获取教师的信息
	 * @Author wyx
	 * @param $teachid 教师id
	 * @return object
	 *
	 */
	public function getTeachMsg($teachid){
		$techmodel = new Teacherinfo;
		//返回教师基本信息
		$field = 'teacherid,imageurl,prphone,mobile,teachername,nickname,accountstatus,sex,profile,birth,country,province,city,school,grade,class' ;
		$baseinfo = $techmodel->getTeacherData($field,$teachid);

		//!empty($baseinfo['birth']) &&	$baseinfo['birth'] = date('Y-m-d',$baseinfo['birth']);

		if( empty($baseinfo) ){//没有符合条件的教师数据
			return return_format([],40096) ;
		}else{
			$accessmodel = new Accessroleuser;
			$where = [
				'uid' => $teachid,
				'usertype' => 1
			];
			$roleidarr = $accessmodel->getRoleidByCondition($where);
			$roleidstr = '';
			$ar = [];
			foreach($roleidarr as $val) $ar[] = $val == 2 ? 1 : 2;
			$return = ['baseinfo'=>$baseinfo, 'roleid'=>implode(',', $ar)];
			return return_format($return,0);
		}

	}
	/**
	 * [dealArray 将数组合并分层]
	 * @Author wyx
	 * @DateTime 2018-04-19T14:08:12+0800
	 * @param    [array]    $arr    [description]
	 * @param    [string]   $father [description]
	 * @return   [array]            [description]
	 */
	public function dealArray($arr,$father){
		if(empty($arr)) return [] ;

		$temp  = [] ;
		$child = [] ;
		foreach ($arr as $key => $val) {
			if($val[$father]==0){
				$temp[$val['id']] = ['name'=>$val['tagname']];
			}else{
				//删除不需要的字段
				unset($val['addtime']) ;
				unset($val['status']) ;

				$child[$val[$father]][] = $val ;
			}
		}
		foreach ($temp as $key => &$val) {
			$val['list'] =  isset($child[$key]) ? $child[$key] : [] ;
		}
		return $temp ;
	}
	/**
	 * [addTeacherMsg 添加教师数据]
	 * @Author wyx
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [发送过来的数据]
	 */
	public function addTeacherMsg($data){
		if( empty($data['roleid']) ) return return_format('',40110);
		if( empty($data['password']) ) return return_format('',40098);
		if( strpos($data['nickname'],'#') !== false ) return return_format('',40097);
		if( !empty($data['password']) && $data['password'] == $data['repassword'] ){
	        $allowfield = ['imageurl','mobile','nickname','sex','country','province','city','birth','profile','password','teacherid','prphone','school','grade','class','roleid'];
	        // $allowfield = ['imageurl','mobile','nickname','teachername','sex','country','province','city','birth','profile','password','status','teacherid','prphone'];
	        //过滤 多余的字段
	        $newdata = where_filter($data,$allowfield) ;
	        $newdata['addtime'] = time();

			//创建验证器规则
			$teachobj = new Teacherinfo ;
			$return = $teachobj->addTeacher($newdata);
			// var_dump($return) ;
			return $return ;
			
		}else{
			return return_format('',40097);
		}

	}
	/**
	 * [updateTeacherMsg 更新教师数据]
	 * @Author wyx
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data [发送过来的数据]
	 */
	public function updateTeacherMsg($data){
		if( empty($data['roleid']) ) return return_format('',40110);
		$allowfield = ['imageurl','nickname','sex','country','province','city','birth','profile','teacherid','school','grade','class','roleid','mobile','prphone'];
		//过滤 多余的字段
		$newdata = where_filter($data,$allowfield) ;
		//if( isset($data['truename']) ) $newdata['teachername'] = $data['truename'] ;// 教师表对应的字段名字

		$teachobj = new Teacherinfo ;
		$return = $teachobj->updateTeacher($newdata);
		return $return ;

	}
	
	/**
	 * [switchTeachStatus 切换教师的启用状态标记]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $teacherid [教师id]
	 * @param    [int]       $dataflag  [要修改的标记状态]
	 * @return   [array]                [返回数组]
	 */
	public function switchTeachStatus($teacherid,$dataflag){
		if($teacherid>0 && in_array($dataflag, [0,1]) ){
			$teachobj = new Teacherinfo ;
			return $teachobj->switchTeachStatus($teacherid,$dataflag);
		}else{
			return return_format('',40113);
		}
	}
	
	/**
	 * [checkTeacherHaveCourse]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $teacherid [description]
	 * @return   [type]                 [description]
	 */
	public function checkTeacherHaveCourse($teacherid){
		if($teacherid>0){
			$sdobj = new Scheduling ;
			$flag = $sdobj->checkSchedulExist($teacherid) ;
			if(!empty($flag)){
				return return_format('',40145);
			}else{
				return return_format('',0);
			}
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * [delTeacher 伪删除教师信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $teacherid [description]
	 * @return   [type]                 [description]
	 */
	public function delTeacher($teacherid){
		if($teacherid>0){
			//检测如果 老师有课 禁止删除
			$sdobj = new Scheduling ;
			$flag = $sdobj->checkSchedulExist($teacherid);
			if(!empty($flag)){
				return return_format('',40145) ;
			}

			$teachobj = new Teacherinfo ;
			return $teachobj->delTeacher($teacherid);
		}else{
			return return_format('',40115);
		}
	}
	
	/**
	 * 重置密码
	 * @Author jcr
	 * @param $data['teachid','password','reppassword']
	 */
	public function editTeachPass($data){
		if(!isset($data['teachid']) || !$data['teachid']){
			return return_format('',-40200,'请传输要修改的老师') ;
		}
		$password = isset($data['password'])?trim($data['password']):'';
		$reppassword = isset($data['reppassword'])?trim($data['reppassword']):'';

		if(!$password || !$reppassword){
			return return_format('',-40201,'密码或重复密码不能为空') ;
		}else if(!verifyPassword($password)){
			return return_format('',-40202,'密码必须为6-16位的数字和字母');
		}else if($password != $reppassword){
			return return_format('',-40203,'密码和重复密码不一致') ;
		}

		$allaccount = new Allaccount();
		if($allaccount->updatePassword($password,$data['teachid'])){
			return return_format('',0,'修改成功') ;
		}else{
			return return_format('',-40204,'修改失败') ;
		}
	}
	
	/**
	 * [ImportTeachers 批量导入老师]
	 * @Author lc
	 * @DateTime 2018-04-19T18:43:14+0800
	 * @param    [array]  $data    [传递过来的excel数据]
	 * @return   [int]   [返回导入的数据条数]
	 */
	public function ImportTeachers($data){
		$teachobj = new Teacherinfo;
		$roleidarr = [1=>'课程', 2=>'作文'];
		foreach($data as $k=>$v){
			if(empty($v['A']) || empty($v['B']) || empty($v['C']) || empty($v['D']) || empty($v['H']) || empty($v['I'])){
				continue;
			}
			
			if(strpos($v['B'],'#') !== false) continue;
			if($v['H'] != $v['I']) continue;
			$roleid = [];
			foreach(explode(',', $v['A']) as $val){
				$roleid[] = current(array_keys($roleidarr, $val));
			}
			$newdata[$k]['roleid'] = join(',', $roleid);
			$newdata[$k]['nickname'] = $v['B'];
			$newdata[$k]['prphone'] = $v['C'];
			$newdata[$k]['mobile'] = $v['D'];
			$newdata[$k]['school'] = $v['E'];
			$newdata[$k]['grade'] = $v['F'];
			$newdata[$k]['class'] = $v['G'];
			$newdata[$k]['password'] = $v['H'];
			$newdata[$k]['repassword'] = $v['I'];
			$newdata[$k]['addtime'] = time();
		}
		if($newdata){
			$return = $teachobj->importTeachers(array_values($newdata));
			$c = ($return['code'] == 0) ? $return['data'] : 0;
		}
		return $c;
	}
}

