<?php

namespace app\teacher\controller;
use app\teacher\business\AllteacherInfo;
use app\teacher\business\TeacherComposition;
use app\teacher\business\TeacherManage;
use app\admin\business\Docking;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use login\Authorize;

// 该控制器关于个人主页部分
class Teacher extends Authorize
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $this->teacherid = $this->userInfo['info']['uid'];
    }

  /**
   * 教师首页个人信息接口（头部部分）
   * @Author wangwwy
   * @param 使用$teacherid 做查询
   * @return   [type]                   [description]
   * URL:/teacher/Teacher/getPersoninfo
   */
    public function getPersoninfo(){
        //$data = Request::instance()->post(false);
        $teacherid = $this->teacherid ;
        $limita = config('param.pagesize')['person_info_listA'];
        $limitb = config('param.pagesize')['person_info_listB'];
        $limitc = config('param.pagesize')['person_info_listC'];
        $manageobj = new AllteacherInfo;
        //获取学生
        $userinfo  = $manageobj->getPersoninfo($teacherid,$limita,$limitb,$limitc) ;
        // var_dump($userinfo);
        $this->ajaxReturn($userinfo);
    }




    /**
     *获取该教师所有的课程评价（加入班级名称）
     *@Author wangwy
     * @param 使用$allaccountid 做查询
     * @return  [type]                   [description]
     * URL:/teacher/Teacher/getAllComment
     */
    public function getAllComment(){
        $data = Request::instance()->post(false);
        $allaccountid = $this->teacherid;
        // $keyword  = $this->request->param('keyword') ;//关键词暂时不做
        $pagesize = config('param.pagesize')['teacher_comment_list'];
        $motest = new AllteacherInfo;
        //获取学生信息，默认分页5条
        $studentlist = $motest->getCommentLists($data['pagenum'],$pagesize,$allaccountid,$data['allcommit'] );
        $this->ajaxReturn($studentlist);
    }




    /**
    *获取教师课程评价总览(app部分加入头像)
    *@Author wangwy
    * @param 使用$allaccountid 做查询
    * @return  [type]                   [description]
    * URL:/teacher/Teacher/getappCommentList
    */
    public function getappCommentList()
    {
        $data = Request::instance()->post(false);
        //教师示id
        // $data['allaccountid'] = 1;
        //默认分页为5条
        // $data['limitstr'] =5 ;
        // $data['organid'] = 1;
        $motest = new AllteacherInfo;
        //获取学生信息，默认分页5条
        $studentlist = $motest->getappCommentList($data['allaccountid'],$data['pagesize']='');
        $this->ajaxReturn($studentlist);
    }

    /**
    * 教师修改密码
    * @Author wangwy
    * @DateTime 2018-04-23T20:11:19+0800
    * @param    [string]              mobile  必填手机号
    * @param    [string]              code     验证码
    * @param   [string]               uniqid    tokenid
    * @param   [int]                  organid 机构id
    * @param   [string]               newpass   新密码
    * @return   array();
    * URL:/teacher/Teacher/updatePass
    */
    public function updatePass(){
        $data = Request::instance()->post(false);
        // $uniqid = $this->request->param('uniqid');
        // $mobile = $this->request->param('mobile');
        // $code = $this->request->param('code');
        // $organid = $this->request->param('organid');
        // $newpass = $this->request->param('newpass');
        $userobj = new TeacherManage;
        $res = $userobj->updatePassword($data['mobile'],$data['code'],$data['newpass'],$data['repass']);
        $this->ajaxReturn($res);
    }
    /**
    * 教师修改手机号发送短信
    * @Author wangwy
    * @DateTime 2018-04-23T20:11:19+0800
    * @param    [string]              mobile  必填手机号
    * @param    [string]              prphone    手机号前缀
    * @param   [int]                   organid 机构id
    * @return   array();
    * URL:/teacher/Teacher/sendUpdateMobileMsg
    */
    public function sendUpdateMobileMsg(){
        $data = Request::instance()->post(false);
        // $newmobile = $this->request->param('mobile');
        // $organid = $this->request->param('organid');
        // $prphone = $this->request->param('prphone');
        //$newmobile = '18235102743';
        //$prphone = '86';
        $userobj = new TeacherManage;
        $res = $userobj->sendUpdatemobileMsg($data['newmobile'],$data['prphone']);
        $this->ajaxReturn($res);
    }
    /**
    * 教师修改密码发送短信
    * @Author wangwy
    * @DateTime 2018-04-23T20:11:19+0800
    * @param    [string]              mobile  必填手机号
    * @param    [string]              prphone    手机号前缀
    * @param   [int]                   organid 机构id
    * @return   array();
    * URL:/teacher/Teacher/sendUpdateMobileMsg
    */
    public function sendUpdatePassMsg(){
        $data = Request::instance()->post(false);
        // $newmobile = $this->request->param('mobile');
        // $organid = $this->request->param('organid');
        // $prphone = $this->request->param('prphone');
        //$newmobile = '18235102743';
        //$prphone = '86';
        $userobj = new TeacherManage;
        $res = $userobj->sendUpdatePassMsg($data['mobile'],$data['prphone']);
        $this->ajaxReturn($res);
    }

    /**
    * 教师修改手机号
    * @Author wangwy
    * @DateTime 2018-04-23T20:11:19+0800
    * @param    [string]              oldmobile  必填原有手机号
    * @param    [string]              code     验证码
    * @param   [int]                  organid 机构id
    * @param   [int]               newmobile  手机号
    * @param   [int]               studentid   用户Id
    * @return   array();
    * URL:/teacher/Teacher/updateMobile
    */
    public function updateMobile(){
        $data = Request::instance()->post(false);
        $teacherid = $this->teacherid;
        // $studentid = 1;
        // $prphone = '78';
        $userobj = new TeacherManage;
        $res = $userobj->updateMobile($data['oldmobile'],$data['newmobile'],$data['code'] ,$teacherid,$data['prphone']);
        $this->ajaxReturn($res);
    }
    /**
    * 保存教师的空余时间设置
    * @Author wyx
    * @param 使用teacherid 做查询
    * @return
    * URL:/teacher/teacher/updateWeekIdle
    */
    public function updateWeekIdle(){
        $week = Request::instance()->post(false);
        $week = $week['week'];
        $teachid = $this->teacherid;
        $manageobj = new TeacherManage;
        //更新教师拥有的标签
        $teachlist = $manageobj->updateWeekIdle($week,$teachid);
        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);

    }

    /**
    * 注册机构教师
    * Author WangWY
    * @param 
    */
    public function registerTeacher(){
        $data = Request::instance()->post(false);
        $teacherid = $this->teacherid;
        $regisobj = new TeacherManage;
        $register = $regisobj->registerTeacher($data,$teacherid);
        $this->ajaxReturn($register);
    }
  
    /**
    * 登陆后，返回logo和机构名称
    * @Author wangwy
    */
    public function getLogo(){
        //$data = Request::instance()->post(false);
        $organid = 1;
        // print_r('123456');
        // exit();
        $organobj = new AllteacherInfo;
        $logo = $organobj->getlogo($organid);
        $this->ajaxReturn($logo);
    }
    /**
     * 教师端-作文批改-列表
     * @Author ZQY
     * URL:/teacher/Teacher/getCompositionList
     */
    public function getCompositionList(){
        $reviewstatus = $this->request->param('reviewstatus');
        $teacherid = $this->teacherid;
        $studentname =$this->request->param('studentname');
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['Teacher_composition_list'];
        $composition = new TeacherComposition();
        $composition_list = $composition->handleCompositionList($reviewstatus,$studentname,$pagenum,$limit,$teacherid);
        $this->ajaxReturn($composition_list);
        return $composition_list;
    }
    /**
     * 教师端-作文批改-获取作文数据
     * @Author ZQY
     * URL:/teacher/Teacher/getCompositionData
     */
    public function getCompositionData(){
        $compositionid = $this->request->param('compositionid');
        $composition = new TeacherComposition();
        $composition_data = $composition->getCompositionData($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }
    /**
     * 教师端-作文批改-检测该作文是否正在批阅
     * @Author ZQY
     * URL:/teacher/Teacher/checksCompositionData
     */
    public function checksCompositionData(){
        $compositionid = $this->request->param('compositionid');
        $composition = new TeacherComposition();
        $composition_data = $composition->checkCompositionData($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }
    /**
     * 教师端-作文批改
     * @Author ZQY
     * URL:/teacher/Teacher/reviewComposition
     */
    public function reviewComposition(){
        $compositionid = $this->request->param('compositionid');
        $reviewscore = $this->request->param('reviewscore');
        $commentcontent = $this->request->param('commentcontent');
        $teacherid = $this->teacherid;
        $composition = new TeacherComposition();
        $composition_data = $composition->reviewComposition($compositionid,$reviewscore,$commentcontent,$teacherid);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }
    /**
     * 教师端-修改作文批改
     * @Author ZQY
     * URL:/teacher/Teacher/UpdateReviewComposition
     */
    public function UpdateReviewComposition(){
        $compositionid = $this->request->param('compositionid');
        $reviewscore = $this->request->param('reviewscore');
        $commentcontent = $this->request->param('commentcontent');
        $composition = new TeacherComposition();
        $composition_data = $composition->UpdateReviewComposition($compositionid,$reviewscore,$commentcontent);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }
    /**
     * 教师端 - 我的批阅 - 查看
     * @Author ZQY
     * URL:/teacher/Teacher/seeReviewComposition
     */
    public function seeReviewComposition(){
        $compositionid = $this->request->param('compositionid');
//        $teacherid = $this->teacherid;
        $composition = new TeacherComposition();
        $composition_data = $composition->seeCompositionData($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }
    /**
     * 教师端 - 修改批阅状态
     * @Author ZQY
     * URL:/teacher/Teacher/compositionRegresses
     */
    public function compositionRegresses(){
        $compositionid = $this->request->param('compositionid');
        $composition = new TeacherComposition();
        $composition_data = $composition->compositionRegresses($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;
    }



}
