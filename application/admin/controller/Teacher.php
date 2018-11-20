<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\TeacherManage;
use app\teacher\business\TeacherManage as TM;
use think\Request;
use login\Authorize;
class Teacher extends Authorize
{	

    /**
     *  
     *
     */
    public function __construct(){
        parent::__construct();
		header('Access-Control-Allow-Origin: *');
    }
	/**
	 * 获取教师列表
	 * @Author wyx
	 * @DateTime 2018-04-17T13:11:19+0800
	 * @return   [type]                   [description]
	 * URL:/admin/teacher/getTeachList
	 */
    public function getTeachList()
    {	
        //$mobil    = $this->request->param('mobile') ;
        $nickname = $this->request->param('nickname') ;
        $pagenum  = $this->request->param('pagenum') ;
    	$limit = config('param.pagesize')['adminteach_teachlist'] ;

    	$manageobj = new TeacherManage;
    	//获取教师列表信息,默认分页为5条
    	$teachlist = $manageobj->getTeacherList($nickname,$pagenum,$limit);
    	// var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
        // return json_encode($teachlist) ;

    }
    /**
     * 教师详情
     * @Author wyx
     * @param 使用teacherid 做查询
     * @return 
     * URL:/admin/teacher/teachInfo
	 */
    public function teachInfo(){
    	$teachid = $this->request->param('teachid') ;
    	
    	$manageobj = new TeacherManage;
    	//获取教师列表信息,默认分页为5条
    	$teachlist = $manageobj->getTeachInfo($teachid);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
    }
	
    /**
     * 保存教师的空余时间设置
     * @Author wyx
     * @param 使用teacherid 做查询
     * @return 
     * URL:/admin/teacher/updateWeekIdle
     */
    /* public function updateWeekIdle(){
        $data = Request::instance()->post();
        $week = $data['week'] ;
        // var_dump($vids);exit();
        // $teachid = $this->request->param('teachid') ;
        $teachid = $data['teachid'] ;
        $manageobj = new TeacherManage;
        //更新教师拥有的标签
        $teachlist = $manageobj->updateWeekIdle($week,$teachid);
        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
    } */
	
    /**
     * 编辑教师资料时获取教师的信息
     * @Author wyx
     * @param 使用teacherid 做查询
     * @return 
     * URL:/admin/teacher/getTeachMsg
     */
    public function getTeachMsg(){
        $teachid = $this->request->param('teachid') ;

        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->getTeachMsg($teachid);
        $this->ajaxReturn($teachlist);
        return $teachlist ;
    }
    /**
     * [addTeacherMsg 添加教师信息]
     * @Author wyx 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/teacher/addTeacherMsg
     */
    public function addTeacherMsg(){
        //机构 标识id
        $data = Request::instance()->post();

        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->addTeacherMsg($data);
        $this->ajaxReturn($teachlist);
        return $teachlist;

    }
	
    /**
     * [updateTeacherMsg 修改教师信息]
     * @Author wyx 
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/admin/teacher/updateTeacherMsg
     */
    public function updateTeacherMsg(){
        $data = Request::instance()->post();

        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->updateTeacherMsg($data);
        $this->ajaxReturn($teachlist);
        return $teachlist ;

    }
	
    /**
     * 教师修改密码
     * @Author wangwy copy by wyx 180620
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [string]               newpass   新密码
     * @return   array();
     * URL:/admin/teacher/updatePass
     */
    /* public function updatePass(){
        $data = Request::instance()->post(false);
        $userobj = new TM;
        $res = $userobj->updatePassword($data['mobile'],$data['code'],$data['newpass'],$data['repass']);
        $this->ajaxReturn($res);
    } */
	
    /**
     * 教师修改手机号发送短信
     * @Author wangwy  copy by wyx 180620
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @return   array();
     * URL:/admin/teacher/sendUpdateMobileMsg
     */
    /* public function sendUpdateMobileMsg(){
        $data = Request::instance()->post(false);
        $userobj = new TM;
        $res = $userobj->sendUpdatemobileMsg($data['newmobile'],$data['prphone']);
        $this->ajaxReturn($res);
    } */
	
    /**
     * 教师修改密码发送短信
     * @Author wangwy  copy by wyx 180620
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @return   array();
     * URL:/admin/teacher/sendUpdatePassMsg
     */
    /* public function sendUpdatePassMsg(){
        $data = Request::instance()->post(false);

        $userobj = new TM;
        $res = $userobj->sendUpdatePassMsg($data['mobile'],$data['prphone']);
        $this->ajaxReturn($res);
    } */
	
    /**
     * 教师修改手机号
     * @Author wangwy  copy by wyx 180620
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              oldmobile  必填原有手机号
     * @param    [string]              code     验证码
     * @param   [int]               newmobile  手机号
     * @param   [int]               studentid   用户Id
     * @return   array();
     * URL:/admin/teacher/updateMobile
     */
    /* public function updateMobile(){
        $data = Request::instance()->post(false);
      
        $userobj = new TM;
        $res = $userobj->updateMobile($data['oldmobile'],$data['newmobile'],$data['code'],$data['teacherid'],$data['prphone']);
        $this->ajaxReturn($res);
    } */
	
    /**
     * 切换教师的启用状态标记
     * @Author wyx
     * @param 使用organid 做查询
     * @return 
     * URL:/admin/teacher/switchTeachStatus
     */
    public function switchTeachStatus(){
        $teacherid = $this->request->param('teacherid');
        // $teacherid =  5;
        $dataflag  = $this->request->param('dataflag');
        // $dataflag =  5;

        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->switchTeachStatus($teacherid,$dataflag);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
	
    /**
     * [删除前判断老师是否有课]
     * @Author lc
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/teacher/checkTeacherHaveCourse
     */
    public function checkTeacherHaveCourse(){
        $teacherid = $this->request->param('teacherid');
		
        $manageobj = new TeacherManage;
        $checkflag = $manageobj->checkTeacherHaveCourse($teacherid);
        $this->ajaxReturn($checkflag);
        return $checkflag ;

    }
	
	/**
     * [deleteTeacher 删除教师]
     * @Author wyx
     * @DateTime 2018-04-20T09:52:59+0800
     * @return   [type]      [description]
     * URL:/admin/teacher/deleteTeacher
     */
    public function deleteTeacher(){
        $teacherid = $this->request->param('teacherid');
        // $teacherid =  5;

        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $delflag = $manageobj->delTeacher($teacherid);
        // var_dump($lablelist);
        $this->ajaxReturn($delflag);
        return $delflag ;

    }
	
    /**
     * 修改老师密码
     * @author    jcr
     * @param    teachid   老师ID
     * @param    password  密码
     * @param    reppassword 重复密码
     */
    public function editTeachPass(){
        $data = Request::instance()->post(false);
        $teacher = new TeacherManage();
        $dataReturn = $teacher->editTeachPass($data);
        $this->ajaxReturn($dataReturn);
    }

}
