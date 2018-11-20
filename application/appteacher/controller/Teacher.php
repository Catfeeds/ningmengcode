<?php

namespace app\appteacher\controller;
use app\appteacher\business\AllteacherInfo;
use app\appteacher\business\TeacherManage;
use app\admin\business\Docking;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use login\Authorize;

// 该控制器关于个人主页部分




class Teacher extends Authorize
{
  public $organid;
  public $teacherid;
  public function _initialize()
    {
        parent::_initialize();
    
        $this->organid = $this->userInfo['info']['organid'];
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
      $data = Request::instance()->post(false);
      // $data['teacherid']  = $this->request->param('userid') ;
      // $data['teacherid']  = 1 ;
      //机构 标识id
      //$organid = Session::get('organid');
      $teacherid = $this->teacherid;
      $organid = $this->organid;
      $limita=5;
      $limitb=5;
      $limitc=5;
      $manageobj = new AllteacherInfo;
      //获取学生
      $userinfo  = $manageobj->getPersoninfo($teacherid,$organid,$limita,
                  $limitb,$limitc) ;
     // var_dump($userinfo);
      $this->ajaxReturn($userinfo);
   }

   /**
   * 获取课程总览(教师主页部分)
   * @Author wangwwy
   * @param 使用$teacherid 做查询
   * @return   [type]                   [description]
   * URL:/teacher/Teacher/getCurriList
   */
  public function getCurriList()
  {
    //$teacherid = $this->request->param('teacherid') ;
    $data = Request::instance()->post(false);
    //教师示id
    // $data['teacherid'] = 1;
    // $data['limitstr'] =6 ;
    // $data['organid'] =1;
    $motest = new AllteacherInfo;
    //获取学生信息，默认分页5条
    $CurriculumList = $motest->getCurriculumList($data['teacherid'],$data['organid'],$data['limitstr']);

    //var_dump($CurriculumList);
    $this->ajaxReturn($CurriculumList);
  }


    /**
   *获取学生总览(教师主页部分)
   *@Author wangwy
   * @param 使用$teacherid 做查询
   * @return  [type]                   [description]
   * URL:/teacher/Teacher/getStudentList
   */
    public function getStudentList()
    {
        //$allaccountid = $this->request->param('teacherid') ;
        $data = Request::instance()->post(false);
        //机构表示id
        // $data['teacherid'] = 1;
        // $data['limitstr'] =5 ;
        // $data['organid'] =1;
        $motest = new AllteacherInfo;
        //获取学生信息，默认分页5条
        $studentlist = $motest->getStudentList($data['teacherid'],$data['organid'],$data['limitstr']);

       // var_dump($studentlist);
        $this->ajaxReturn($studentlist);
    }


     /**
     *获取教师课程评价总览(教师主页部分)
     *@Author wangwy
     * @param 使用$allaccountid 做查询
     * @return  [type]                   [description]
     * URL:/teacher/Teacher/getCommentList
     */
    // public function getCommentList()
    // {
    //   $data = Request::instance()->post(false);
    //   //教师示id
    //   // $data['allaccountid'] =$this->request->param('allaccountid');
    //   // $data['allaccountid'] = 1;
    //   //默认分页为5条
    //   // $data['limitstr'] =5 ;
    //   $data['organid'] = $this->organid;
    //   $motest = new AllteacherInfo;
    //   //获取学生信息，默认分页5条
    //   $studentlist = $motest->getCommentList($data['allaccountid'],$data['organid'],$data['pagesize']);
    //
    //   //var_dump($studentlist);
    //   $this->ajaxReturn($studentlist);
    // }


    /**
     *获取该教师所有的课程评价（加入班级名称）
     *@Author wangwy
     * @param 使用$allaccountid 做查询
     * @return  [type]                   [description]
     * URL:/teacher/Teacher/getAllComment
     */
     public function getAllComment(){
       $data = Request::instance()->post(false);
       //教师示id
       // $data['allaccountid'] =$this->request->param('allaccountid');
       $allaccountid = $this->teacherid;
       // $keyword  = $this->request->param('keyword') ;//关键词暂时不做
       // $keyword = '1';
       //默认分页为5条
       //$data['pagesize'] = $this->request->param('pagesize');//获取数
       $pagesize = $data['limit'] ;
       $organid = $this->organid;
       $motest = new AllteacherInfo;
       //获取学生信息，默认分页5条
       $studentlist = $motest->getCommentLists($data['pagenum'],$pagesize,$allaccountid,$organid,$data['allcommit'] );

       //var_dump($studentlist);
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
     // $data['allaccountid'] =$this->request->param('allaccountid');
     // $data['allaccountid'] = 1;
     //默认分页为5条
     // $data['limitstr'] =5 ;
     // $data['organid'] = 1;
     $motest = new AllteacherInfo;
     //获取学生信息，默认分页5条
     $studentlist = $motest->getappCommentList($data['allaccountid'],$data['organid'],$data['pagesize']='');

     //var_dump($studentlist);
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
        $organid = $this->organid;
        $userobj = new TeacherManage;
        $res = $userobj->updatePassword($data['mobile'],$data['code'],$organid,$data['newpass'],$data['repass']);
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
        $organid = $this->organid;
        //$prphone = '86';
        $userobj = new TeacherManage;
        $res = $userobj->sendUpdatemobileMsg($data['newmobile'],$data['prphone'],$organid);
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
        $organid = $this->organid;
        //$prphone = '86';
        $userobj = new TeacherManage;
        $res = $userobj->sendUpdatePassMsg($data['mobile'],$data['prphone'],$organid);
        $this->ajaxReturn($res);
    }
    /**
     * (未登录情况下)教师忘记密码发送短信
     * @Author wangwy
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @param   [int]                   organid 机构id
     * @return   array();
     * URL:/teacher/Teacher/sendUpdateMobileMsg
     */
    public function sendUpdatePassMsgNlog(){
        $data = Request::instance()->post(false);
        // $newmobile = $this->request->param('mobile');
        // $organid = $this->request->param('organid');s
        // $prphone = $this->request->param('prphone');
        //$newmobile = '18235102743';
        $organid = $this->organid;
        //$prphone = '86';
        $userobj = new TeacherManage;
        $res = $userobj->sendUpdatePassMsg($data['mobile'],$data['prphone'],$organid);
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
        // $oldmobile = $this->request->param('oldmobile');
        // $newmobile = $this->request->param('newmobile');
        // $code = $this->request->param('code');
        // $organid = $this->request->param('organid');
        // $studentid = $this->request->param('studentid');
        // $prphone = $this->request->param('prphone');
        // $oldmobile = '18235102742';
        // $newmobile = '18235102743';
        // $code = '695640';
         $organid = $this->organid;
         $teacherid = $this->teacherid;
        // $studentid = 1;
        // $prphone = '78';
        $userobj = new TeacherManage;
        $res = $userobj->updateMobile($data['oldmobile'],$data['newmobile'],$data['code'] ,$organid,$teacherid,$data['prphone']);
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
        $week    = $_POST['week'] ;
        // var_dump($vids);exit();
        //$teachid = $this->request->param('teachid') ;
        $teachid = $this->teacherid;
        //$organid = Session::get('organid');
        $organid = $this->organid;

        $manageobj = new \app\teacher\business\TeacherManage;
        //更新教师拥有的标签
        $teachlist = $manageobj->updateWeekIdle($week,$teachid,$organid);
        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        //return $teachlist ;
    }

    /**
     *在未登录的情况下，重新设置密码
     * @Author wangwy
     * @param 使用teacherid 做查询
     * @return
     * URL:/teacher/teacher/updateWeekIdle
     */
    public function resetTeacherPass(){
      $data = Request::instance()->post(false);
      $userobj = new TeacherManage;
      $res = $userobj->updateTeacherPass($data['mobile'],$data['code'],$data['domain'],$data['newpass']);
      $this->ajaxReturn($res);
    }






}
