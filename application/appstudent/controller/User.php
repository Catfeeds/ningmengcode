<?php
/**
 * 机构学生登录 业务逻辑层
 * 
 * 
 */
namespace app\appstudent\controller;
use app\student\business\UserManage;
use login\Authorize;
use think\Controller;
use app\appstudent\business\AppUserManage;
use app\appstudent\business\OfficalAppUserManage;
use think\Request;
class User extends Authorize
//class User extends Authorize
    {
    //定义上传目录
    protected $dstfolder = 'student/1/headimg';
    public function _initialize()
    {
        parent::_initialize();
        //获取登录后的organid
//        $this->organid = $this->userInfo['info']['organid'];
        //获取登录后的学生id
         $this->userid = $this->userInfo['info']['uid'];
//        $this->nickname = $this->userInfo['info']['nickname'];

    }
    /**
     * 查询学生个人资料
     * @Author yr
     * @param  userid  学生id
     * appstudent/User/getStudentInfo
     * @return array();
     */
    public function getStudentInfo(){
        $studentid = $this->request->param('studentid');
        $userobj = new AppUserManage;
        $res = $userobj->getStudentInfo($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 学生上传头像
     * @Author yr
     * @DateTime 2018-05-10T20:11:19+0800
     * form表单提交上传
     * @return   array();
     * URL:/appstudent/User/uploadHeadimg
     */
    public function uploadHeadimg(){
        $data['files'] = $_FILES;
        $data['dstfolder'] = $this->dstfolder;//默认文件夹
        $data['id'] = $this->request->param('studentid');//默认文件夹
        $userobj = new AppUserManage;
        $res = $userobj->uploadHeadimg($data);
        $this->ajaxReturn($res);

    }
    /**
     * 学生修改昵称，性别，生日，地区，个性签名统一调用
     * @Author yr
     * @DateTime 2018-05-10T20:11:19+0800
     * @return   array();
     * URL:/appstudent/User/uploadAppuserInfo
     */
    public function updateAppuserInfo(){
        $data = Request::instance()->POST(false);
        $data['id'] = $this->userid;//默认文件夹
        $userobj = new AppUserManage;
        $res = $userobj->updateAppuserInfo($data);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生个人资金流水
     * @Author yr
     * @param  userid  学生id
     * student/User/getStudentPaylog
     * @return array();
     */
    public function getStudentPaylog(){
        $userid = $this->request->param('studentid');
        $pagenum = $this->request->param('pagenum');
        /*$limit= $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_studentpaylog'];
        $userobj = new AppUserManage;
        $res = $userobj->getStudentPaylog($userid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 修改个人资料
     * @Author yr
     * student/User/updateStudentInfo
     * @return array();
     */
    public function updateStudentInfo(){
        $data = Request::instance()->POST(false);
        $manageobj = new AppUserManage;
        $userlist  = $manageobj->updateStudentInfo($data);
        $this->ajaxReturn($userlist);
    }
    /**
     * 随机返回4位验证码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param
     * @return   array();
     * URL:/student/User/randomCode
     */
    public function randomCode(){
        $userobj = new AppUserManage;
        $res = $userobj->getCaptcha();
        $this->ajaxReturn($res);
    }
    /**
     * 学生找回密码 发送短信
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                   organid 机构id
     * @return   array();
     * URL:/student/User/sendMobileMsg
     */
    public function sendMobileMsg(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $uniqid = $this->request->param('uniqid');
        $organid = $this->request->param('organid');
        $userobj = new AppUserManage;
        $res = $userobj->sendMsg($mobile,$code,$uniqid,$organid);
        $this->ajaxReturn($res);
    }
    /**
     * 学生修改密码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return   array();
     * URL:/student/User/updatePass
     */
    public function updatePass(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $organid = $this->request->param('organid');
        $newpass = $this->request->param('newpass');
        $userobj = new AppUserManage;
        $res = $userobj->updatePassword($mobile,$code,$organid,$newpass);
        $this->ajaxReturn($res);
    }
    /**
     * 学生发送短信
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @param   [int]                   organid 机构id
     * @return   array();
     * URL:/student/User/sendUpdateMobileMsg
     */
    public function sendUpdateMobileMsg(){
        $newmobile = $this->request->param('mobile');
        $prphone = $this->request->param('prphone');
        $userobj = new AppUserManage();
        $res = $userobj->sendUpdatemobileMsg($newmobile,$prphone);
        $this->ajaxReturn($res);
    }
    /**
     * 学生修改手机号
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              oldmobile  必填原有手机号
     * @param    [string]              code     验证码
     * @param   [int]                  organid 机构id
     * @param   [int]               newmobile  手机号
     * @param   [int]               studentid   用户Id
     * @return   array();
     * URL:/student/User/updateMobile
     */
    public function updateMobile(){
        $oldmobile = $this->request->param('oldmobile');
        $newmobile = $this->request->param('newmobile');
        $code = $this->request->param('code');
        $studentid = $this->userid;
        $prphone = $this->request->param('prphone');
        $userobj = new AppUserManage();
        $res = $userobj->updateMobile($oldmobile,$newmobile,$code,$studentid,$prphone);
        $this->ajaxReturn($res);
    }
    /**
     * 学生充值接口
     * @Author yr
     * @param  userid  学生id
     * @param  amount  充值金额
     * student/User/studentRecharge
     * @return array();
     */
    /**
     * 学生充值接口
     * @Author yr
     * @param  userid  学生id
     * @param  amount  充值金额
     * student/User/studentRecharge
     * @return array();
     */
    public function studentRecharge(){
        $studentid = $this->userid;
        $amount =  $this->request->param('amount');
        $paytype =  $this->request->param('paytype');
        $source=  $this->request->param('source');
        $type =  1;//app充值type为1
        $userobj = new UserManage;
        $res = $userobj->studentRecharge($studentid,$amount,$paytype,$source,$type);
        $this->ajaxReturn($res);
    }
    /**
     * 学生修改密码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return   array();
     * URL:/student/User/updateUserPass
     */
    public function updateUserPass(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $newpass = $this->request->param('newpass');
        $userobj = new AppUserManage();
        $res = $userobj->updatePassword($mobile,$code,$newpass);
        $this->ajaxReturn($res);
    }
    /**
     * 学生收藏机构
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/organCollect
     */
    public function organCollect(){
        $studentid  = $this->request->post('studentid');
        $organid = $this->request->post('organid');
        $userobj = new OfficalAppUserManage;
        $res = $userobj->organCollect($organid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 学生取消收藏机构
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/cancelOrganCollect
     */
    public function cancelOrganCollect(){
        $studentid  = $this->request->post('studentid');
        $organid = $this->request->post('organid');
        $userobj = new OfficalAppUserManage;
        $res = $userobj->cancelOrganCollect($organid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 收藏老师
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/microsite/User/teacherCollect
     */
    public function teacherCollect(){
        $teacherid = $this->request->param('teacherid');
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->teacherCollect($teacherid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 取消收藏老师
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/cancelTeacherCollect
     */
    public function cancelTeacherCollect(){
        $teacherid = $this->request->param('teacherid');
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->cancelTeacherCollect($teacherid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 收藏班级
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               schedulingid 班级id
     * @return   array();
     * URL:/microsite/User/classCollect
     */
    public function classCollect(){
        $courseid= $this->request->param('courseid');
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->classCollect($courseid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 取消收藏班级课程
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/microsite/User/cancelClassCollect
     */
    public function cancelClassCollect(){
        $courseid= $this->request->param('courseid');
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->cancelClassCollect($courseid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     *查询学生收藏的老师
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/microsite/User/teacherCollectList
     */
    public function teacherCollectList(){
        $pagenum = $this->request->param('pagenum');
        $studentid = $this->request->param('studentid');
        //$limit = $this->request->param('limit');
        $limit = config('param.pagesize')['student_teachercollect'];
        //$studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->teacherCollectList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生收藏的班级
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/microsite/User/classCollectList
     */
    public function classCollectList(){
        $pagenum = $this->request->param('pagenum');
        $studentid = $this->request->param('studentid');
        /*   $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_classcollect'];
        // $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->classCollectList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生消息列表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              pagenum  页码数
     * @return   array();
     * URL:/student/User/messageList
     */
    public function messageList(){
        $pagenum = $this->request->param('pagenum');
        $type = $this->request->param('type');
        /*   $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_messagelist'];
        $studentid  = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->messageList($studentid,$pagenum,$limit,$type);
        $this->ajaxReturn($res);
    }
    /**
     * 更改消息状态 变为已查看
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              latelytime 最新一条数据的 添加时间
     * @return   array();
     * URL:/student/User/updateMsgStatus
     */
    public function updateMsgStatus(){
        $latelytime = $this->request->param('latelytime');
        $studentid = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->updateMsgStatus($latelytime,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 删除消息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              messageids  消息id拼接的字符串 如1 或者1,2
     * @param    [int]              studentid  学生id
     * @return   array();
     * URL:/student/User/deleteMsg
     */
    public function deleteMsg(){
        $messageids = $this->request->param('messageids');
        /*   $limit = $this->request->param('limit');*/
        $userobj = new UserManage;
        $res = $userobj->deleteMsg($messageids);
        $this->ajaxReturn($res);
    }
    /**
     * 查看是否有最新消息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              messageids  消息id拼接的字符串 如1 或者1,2
     * @param    [int]              studentid  学生id
     * @return   array();
     * URL:/student/User/deleteMsg
     */
    public function getNewMsg(){
        $studentid = $this->userid;
        /*   $limit = $this->request->param('limit');*/
        $userobj = new UserManage;
        $res = $userobj->getNewMsg($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生收藏的班级
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/classCollectList
     */
//    public function classCollectList(){
//        $pagenum = $this->request->param('pagenum');
//        $limit = $this->request->param('limit');
//        $studentid  = $this->userid;
//        $organid = $this->organid;
//        if($organid == 1){
//            $userobj = new OfficalAppUserManage;
//        }else{
//            $userobj = new AppUserManage;
//        }
//        $res = $userobj->classCollectList($studentid,$pagenum,$limit);
//        $this->ajaxReturn($res);
//    }
    /**
     * 新增收货地址表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                 studentid       学生id
     * @return   array();
     * URL:/student/User/getAddressList
     */
    public function getAddressList(){
        $studentid = $this->userid;
        $userobj = new AppUserManage();
        $res = $userobj->getAddressList( $studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 新增收货地址表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                 pid       省id关联nm_city
     * @param    [int]                 cityid      城市id 关联nm_city
     * @param    [int]                 areaid  区域id
     * @param    [vachar]                zipcode 邮编
     * @param    [vachar]              address  详细地址
     * @param    [vachar]              linkman 收货人姓名
     * @param    [int]                 mobile  手机号码
     * @return   array();
     * URL:/microsite/User/addOrUpdateAddress
     */
    public function addOrUpdateAddress(){
        $data['studentid'] = $this->userid;
        /*   $limit = $this->request->param('limit');*/
        $data['pid']= $this->request->post('pid');
        $data['cityid'] = $this->request->post('cityid');
        $data['areaid'] = $this->request->post('areaid');
        $data['address'] = $this->request->post('address');
        $data['zipcode']= $this->request->post('zipcode');
        $data['linkman'] = $this->request->post('linkman');
        $data['mobile'] = $this->request->post('mobile');
        $data['isdefault'] = $this->request->post('isdefault');
        $data['id'] = $this->request->post('id');
        $userobj = new AppUserManage;
        $res = $userobj->addOrUpdateAddress($data);
        $this->ajaxReturn($res);
    }
    /**
     * 删除收货地址表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                 studentid       学生id
     * @return   array();
     * URL:/student/User/deleteAddress
     */
    public function deleteAddress(){
        $addressid  = $this->request->post('id');
        $userobj = new AppUserManage();
        $res = $userobj->deleteAddress($addressid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的套餐使用列表
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param status int 0待使用 1已使用 2已过期
     * @param pagenum int 分页页数
     * @return   array();
     * URL:/student/Package/packageUseList
     */
    public function packageUseList()
    {
        $status = $this->request->post('status');
        $pagenum = $this->request->post('pagenum');
        //$studentid = $this->request->post('studentid');
        $studentid = $this->userid;
        $limit = config('param.pagesize')['student_packageuselist'];
        $orderobj = new AppUserManage;
        $res = $orderobj->packageUseList($studentid,$pagenum,$status,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 签到首页
     * @Author lc
     * @DateTime 2018-04-17T13:11:19+0800
     * @return   [type]                   [description]
     */
    public function signinHome()
    {
        $studentid = $this->userid;
        //$studentid = $this->request->param('studentid');
        $userobj = new AppUserManage;
        $list = $userobj->getSigninHomeData($studentid);
        $this->ajaxReturn($list);
        return $list;
    }
    /**
     * 签到
     * @Author lc
     * @return   array();
     */
    public function signin(){
        $studentid = $this->userid;
        //$studentid = $this->request->param('studentid');
        $knowledgeid = $this->request->param('knowledgeid');
        $obj = new AppUserManage;
        $res = $obj->signin($studentid, $knowledgeid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的历史签到
     * @Author lc
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              pagenum  页码数
     * @return   array();
     * URL:/microsite/User/mySigninList
     */
    public function mySigninList(){
        $pagenum = $this->request->param('pagenum');
        //$studentid = $this->request->param('studentid');
        $limit = $this->request->param('limit');
        //$limit = config('param.pagesize')['student_messagelist'];
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->mySigninList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 背景图列表
     * @Author lc
     * @DateTime 2018-04-17T13:11:19+0800
     * @return   [type]                   [description]
     */
    public function getSigninbgiList()
    {
        $manageobj = new AppUserManage;
        $list = $manageobj->getSigninbgiList();
        $this->ajaxReturn($list);
        return $list;
    }
    /**
     * 更换签到背景
     * @Author lc
     * @return   array();
     */
    public function changeSigninImage(){
        $signinimageid = $this->request->param('signinimageid');
        $studentid = $this->userid;
        $obj = new AppUserManage;
        $res = $obj ->changeSigninImage($signinimageid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的点评列表
     * @Author lc
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              pagenum  页码数
     * @return   array();
     * URL:/microsite/User/myCommentList
     */
    public function myCommentList(){
        $pagenum = $this->request->param('pagenum');
        //$studentid = $this->request->param('studentid');
        $limit = $this->request->param('limit');
        //$limit = config('param.pagesize')['student_messagelist'];
        $studentid  = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->myCommentList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的点评详情
     * @Author lc
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              pagenum  页码数
     * @return   array();
     * URL:/microsite/User/myCommentList
     */
    public function myCommentMsg(){
        $id = $this->request->param('id');
        $userobj = new AppUserManage;
        $res = $userobj->myCommentMsg($id);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的作业列表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              pagenum  分页页数
     * @param    [int]              status 0未完成 1已完成 2.已批阅
     * @return   array();
     * URL:/student/User/getHomeworkList
     */
    public function getHomeworkList(){
        $pagenum = $this->request->param('pagenum');
        $status = $this->request->param('status');
        $studentid = $this->userid;
        $limit = config('param.pagesize')['student_homework_list'];
        $userobj = new UserManage();
        $res = $userobj->getHomeworkList($studentid,$status,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 按课时名称搜索我的作业列表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              pagenum  分页页数
     * @param    [int]              status 0未完成 1已完成 2.已批阅
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/searchHomework
     */
    public function searchHomework(){
        $search = $this->request->param('search');
        $pagenum = $this->request->param('pagenum');
        $status = $this->request->param('status');
        $studentid = $this->userid;
        $limit = config('param.pagesize')['student_homework_list'];
        $userobj = new UserManage;
        $res = $userobj->searchHomework($studentid,$search,$status,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 写作业 查询我的题库
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/getQuestionList
     */
    public function getQuestionList(){
        $lessonsid = $this->request->param('lessonid');
        $classid = $this->request->param('classid');
        $studentid = $this->userid;
        $obj = new UserManage;
        $res = $obj ->getQuestionList($studentid,$lessonsid,$classid);
        $this->ajaxReturn($res);
    }
    /**
     * 写作业 提交作业
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              lessonid 课时id
     * @param    [int]              classid 班级id
     * @param    [array]            answers 答案
     * @return   array();
     * URL:/student/User/submitQuestions
     */
    public function submitQuestions(){
        $result = $this->request->post(false);
        $classid = $result['classid'];
        $lessonid = $result['lessonid'];
        $homeworkid = $result['homeworkid'];
        $studentid = $this->userid;
        $answers = $result['answers'];
        $obj = new UserManage;
        $res = $obj ->submitQuestions($studentid,$classid,$lessonid,$homeworkid,$answers);
        $this->ajaxReturn($res);
    }
    /**
     * 已完成的作业 展示修改
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @param    [array]            answers 答案
     * @return   array();
     * URL:/student/User/showUpdateQuestions
     */
    public function showUpdateQuestions(){
        $lessonid= $this->request->param('lessonid');
        $classid = $this->request->param('classid');
        $studentid = $this->userid;
        $obj = new UserManage;
        $res = $obj ->showUpdateQuestions($studentid,$lessonid,$classid);
        $this->ajaxReturn($res);
    }
    /**
     * 已完成的作业 提交修改
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @param    [array]            answers 答案
     * @return   array();
     * URL:/student/User/updateQuestions
     */
    public function updateQuestions(){
        $result = $this->request->post(false);
        $classid = $result['classid'];
        $lessonid = $result['lessonid'];
        $homeworkid = $result['homeworkid'];
        $studentid = $this->userid;
        $answers = $result['answers'];
        $obj = new UserManage;
        $res = $obj ->submitUpdateQuestions($studentid,$classid,$lessonid,$homeworkid,$answers);
        $this->ajaxReturn($res);
    }
    /**
     * 查询已批阅的作业详情
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/getCompleteQuestions
     */
    public function getCompleteQuestionList(){
        $lessonid = $this->request->param('lessonid');
        $classid = $this->request->param('classid');
        $studentid = $this->userid;
        $obj = new UserManage;
        $res = $obj ->getCompleteQuestions($studentid,$lessonid,$classid);
        $this->ajaxReturn($res);
    }
    /**
     * 已完成的作业发送消息提醒
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              homeworkid  学生作业表id
     * @return   array();
     * URL:/student/User/sendHomeworkMessage
     */
    public function sendHomeworkMessage(){
        $homeworkid = $this->request->param('homeworkid');
        $studentid = $this->userid;
        $obj = new UserManage;
        $res = $obj ->sendHomeworkMessage($studentid,$homeworkid);
        $this->ajaxReturn($res);
    }
    /**
     * 选择学生喜欢的分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/getUserFavorCategory
     */
    public function getUserFavorCategory(){
        $obj = new AppUserManage();
        $res = $obj ->getUserFavorCategory();
        $this->ajaxReturn($res);
    }
    /**
     * 添加学生喜欢的分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/getUserFavorCategory
     */
    public function addUserFavorCategory(){
        $categorystr = $this->request->param('categoryid');
        $studentid = $this->userid;
        $obj = new AppUserManage();
        $res = $obj ->addUserFavorCategory($categorystr,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 选择学生的标签
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @return   array();
     * URL:/student/User/getUserTag
     */
    public function getUserTag(){
        $obj = new UserManage;
        $res = $obj ->getUserTag();
        $this->ajaxReturn($res);
    }
    /**
     * 添加学生喜欢的分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/addUserTag
     */
    public function addUserTag(){
        $fatherid = $this->request->param('tagid');
        $tagids = $this->request->param('childtags');
        $studentid = $this->userid;
        $obj = new UserManage;
        $res = $obj ->addUserTag($fatherid,$tagids,$studentid);
        $this->ajaxReturn($res);
    }

	/**
	 * 给登陆用户绑定对应极光标签
	 */
	public function bindingUser(){
		$registrationid = Request::instance()->post('registrationid');
		if(!$registrationid) $this->ajaxReturn(return_format('',10240,lang('param_error')));
		$user = new \app\index\business\UserLogin();
		$returnDate = $user->setPush($this->userInfo['info']['uid'],$this->userInfo['type'],$registrationid);
		if($returnDate){
			$this->ajaxReturn(return_format('',0,lang('10227')));
		}else{
			$this->ajaxReturn(return_format('','10226',lang('10226')));
		}
	}

}
