<?php
/**
 * 机构学生登录 业务逻辑层
 * 
 * 
 */
namespace app\student\controller;
use think\Controller;
use app\student\business\UserManage;
use app\teacher\business\UploadFiles;
use think\Request;
use app\student\business\WebUserManage;
use login\Authorize;
use think\Session;
class User extends Authorize
    {
   /* public function __construct(Request $request = null)
    {
        $this->checktokens(1);
    }*/
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin: *');
        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
    }
    /**
     * 查询学生个人资料
     * @Author yr
     * @param  userid  学生id
     * student/User/getStudentInfo
     * @return array();
     */
    public function getStudentInfo(){
        $studentid = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->getStudentInfo($studentid);
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
        $userid = $this->userid;
        $pagenum = $this->request->param('pagenum');
        //$limit= $this->request->param('limit');
        $limit = config('param.pagesize')['student_studentpaylog'];
        $userobj = new UserManage;
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
        $manageobj = new UserManage;
        $userlist  = $manageobj->updateStudentInfo($data);
        $this->ajaxReturn($userlist);
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
        $userobj = new UserManage;
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
        $userobj = new UserManage;
        $res = $userobj->updateMobile($oldmobile,$newmobile,$code,$studentid,$prphone);
        $this->ajaxReturn($res);
    }
    /**
     * 学生上传头像
     * @Author yr
     * @DateTime 2018-05-10T20:11:19+0800
     * form表单提交上传
     * @return   array();
     * URL:/student/User/uploadHeadimg
     */
    public function uploadHeadimg(){
        header('Access-Control-Allow-Origin: *');
        $data['files'] = $_FILES;
        if(empty($_FILES)){
            $data = [
                'code' => 39037,
                'info' => '没有上传的内容',
                'data' => ''
            ];
            $this->ajaxReturn($data);
        }
        $organid = $this->organid;
        if(!is_intnum($organid)){
            $data = [
                'code' => 39038,
                'info' => '机构id为空',
                'data' => ''
            ];
            $this->ajaxReturn($data);
        }
        $data['dstfolder'] = "student/{$organid}/headimg";//默认文件夹
        $allfile = new UploadFiles;
        $allfiles = $allfile->getUploadFiles($data,1);
        if($allfiles['code'] == 0){
            $data = [
                'code' => 0,
                'info' => '上传成功',
                'data' => [
                    'source_url' => $allfiles['data']['data']['source_url'],
                ]
            ];
        }else{
            $data = [
                'code' => 39036,
                'info' => $allfiles['info'],
                'data' => ''
            ];
        }
        $this->ajaxReturn($data);
    }
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
        $userobj = new UserManage;
        $res = $userobj->studentRecharge($studentid,$amount,$paytype,$source);
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
        $userobj = new UserManage;
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
        $userobj = new WebUserManage;
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
        $userobj = new UserManage;
        $res = $userobj->cancelOrganCollect($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 收藏老师
     * @Author yr
     * @DateTime 2018-05-28T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/teacherCollect
     */
    public function teacherCollect(){
        $teacherid = $this->request->param('teacherid');
        $studentid  = $this->userid;
        $userobj = new UserManage;
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
        $userobj = new UserManage;
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
     * URL:/student/User/classCollect
     */
    public function classCollect(){
        $courseid= $this->request->param('courseid');
        $studentid  = $this->userid;
        //$studentid = $this->request->param('studentid');
        $userobj = new UserManage;
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
     * URL:/student/User/cancelClassCollect
     */
    public function cancelClassCollect(){
        $courseid= $this->request->param('courseid');
        $studentid  = $this->userid;
        $userobj = new UserManage;
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
     * URL:/student/User/teacherCollectList
     */
    public function teacherCollectList(){
        $pagenum = $this->request->param('pagenum');
        //$limit = $this->request->param('limit');
        $limit = config('param.pagesize')['student_teachercollect'];
        $studentid  = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->teacherCollectList($studentid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生收藏的机构
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param   [int]               organid 机构id
     * @return   array();
     * URL:/student/User/organCollectList
     */
    public function organCollectList(){
        $pagenum = $this->request->param('pagenum');
        //$limit = $this->request->param('limit');
        $limit = config('param.pagesize')['student_organcollect'];
        $studentid  = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->organCollectList($studentid,$pagenum,$limit);
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
    public function classCollectList(){
        $pagenum = $this->request->param('pagenum');
     /*   $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_classcollect'];
        $studentid  = $this->userid;
        $userobj = new UserManage;
        $res = $userobj->classCollectList($studentid,$pagenum,$limit);
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
        $userobj = new UserManage;
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
        $obj = new UserManage;
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
        $obj = new UserManage;
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
        $studentid = $this->request->param('studentid');
        /*   $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_messagelist'];
        /*$studentid  = $this->userid;*/
        $userobj = new UserManage;
        $res = $userobj->messageList($studentid,$pagenum,$limit);
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
        $studentid = $this->request->param('studentid');
        /*   $limit = $this->request->param('limit');*/
        $userobj = new UserManage;
        $res = $userobj->getNewMsg($studentid);
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
     * URL:/student/User/addOrUpdateAddress
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
        $userobj = new UserManage;
        $res = $userobj->addOrUpdateAddress($data);
        $this->ajaxReturn($res);
    }
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
        $userobj = new UserManage;
        $res = $userobj->getAddressList( $studentid);
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
        $studentid = $this->userid;
        $addressid  = $this->request->post('id');
        $userobj = new UserManage;
        $res = $userobj->deleteAddress($addressid);
        $this->ajaxReturn($res);
    }
    /**
     * 获取学生所有的分析反馈信息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                 studentid       学生id
     * @return   array();
     * URL:/student/User/getFeedbackList
     */
    public function getFeedbackList(){
        $studentid = $this->userid;
        $pagenum = $this->request->post('pagenum');
        $search = $this->request->post('search');
        $search = isset($search)?$search:'';
        $userobj = new UserManage;
        $limit = config('param.pagesize')['student_messagelist'];
        $res = $userobj->getFeedbackList($studentid,$pagenum,$limit,$search);
        $this->ajaxReturn($res);
    }
}
