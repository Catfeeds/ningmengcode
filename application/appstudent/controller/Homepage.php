<?php
/**
 * 首页分类课程筛选 业务逻辑层
 *
 *
 */
namespace app\appstudent\controller;
use app\admin\model\Category;
use app\admin\model\Teacherinfo;
use app\student\business\HomepageManage;
use app\student\business\ScheduManage;
use app\student\business\TeacherManage;
use login\Authorize;
use think\Controller;
use app\appstudent\business\AppScheduManage;
use app\appstudent\business\AppHomepageManage;
use app\appstudent\business\AppTeacherManage;
use app\appstudent\business\AppLoginManage;
use app\appstudent\business\AppUserManage;
use app\appstudent\business\AppMyCourseManage;
use app\appstudent\business\OfficalAppHomepageManage;
use app\appstudent\business\OfficalAppScheduManage;
use app\appstudent\business\OfficalAppTeacherManage;
use app\appstudent\business\OfficalAppLoginManage;
use app\appstudent\business\OfficalAppUserManage;
use app\appstudent\business\OfficalAppMyCourseManage;
use app\index\business\UserLogin;
use think\Request;

//class Homepage extends Authorize
class Homepage extends Authorize
{
    protected  $logintype = 3;
    public function _initialize()
    {
        parent::_initialize();
        //获取登录后的organid
//        $this->organid = $this->userInfo['info']['organid'];
//        //获取登录后的学生id
        $this->userid = $this->userInfo['info']['uid'];
//        $this->nickname = $this->userInfo['info']['nickname'];

    }
    /**
     * 学生注册
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone  国家区号
     * @param    [string]              code     验证码
     * @param   [int]                  organid 机构id
     * @param   [string]               password   密码
     * @return   array();
     * URL:/appstudent/Home/register
     */
    public function register()
    {
        $post  = $this->request->post(false);
        $loginobj = new AppLoginManage();
        $res = $loginobj->register($post);
        $this->ajaxReturn($res);

    }
    /**
     * 检测用户登录信息
     * @Author yr
     * @DateTime 2018-04-20T13:11:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/login
     */

    public function login ()
    {
        $mobile = $this->request->post('mobile');
        $password = $this->request->post('password');
        $domain = $this->request->post('domain');
        $type = $this->request->post('type');
        /* $mobile = '18235102743';
         $password = '';
         $domain = 'http://test.menke.com';*/
        if($type == 1){
            $loginobj = new OfficalAppLoginManage;
        }else{
            $loginobj = new AppLoginManage;
        }
        $res = $loginobj->login($mobile, $password ,$domain);
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
     * URL:/student/Homepage/updatePass
     */
    public function updatePass(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $newpass = $this->request->param('newpass');
        $userobj = new AppUserManage();
        $res = $userobj->updatePassword($mobile,$code,$newpass);
        $this->ajaxReturn($res);
    }
    /**
     * 首页获取机构相关信息接口
     * @Author yr
     * @DateTime 2018-05-5T16:20:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getOrganInfo
     */
    public function getOrganInfo()
    {
        $homepageobj  = new AppHomepageManage;
        $res = $homepageobj->getOrganInfo();
        $this->ajaxReturn($res);
    }
    /**
     * 首页轮播接口
     * @Author yr
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/appstudent/Homepage/getSlideList
     */
    public function getSlideList()
    {
        $class = new AppHomepageManage;
        $res = $class->getSlideList();
        $this->ajaxReturn($res);
    }
    /**
     * APP首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/appstudent/Homepage/getThreeCategroyList
     */
    public function getThreeCategroyList()
    {
        $obj = new AppHomepageManage();
        $res = $obj->getThreeCategroyList();
        $this->ajaxReturn($res);
    }
    /**
     * APP首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/appstudent/Homepage/getThreeCourseList
     */
    public function getThreeCourseList()
    {
        $categoryid = $this->request->post('categoryid');
        $obj = new AppHomepageManage();
        $res = $obj->getThreeCourseList($categoryid);
        $this->ajaxReturn($res);
    }
    /**
     * 柠檬教育查询推荐课程列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/appstudent/HomePage/getRecommendCourser
     */
    public function getRecommendCourser()
    {
        $scheduobj = new HomepageManage();
        $res = $scheduobj ->getRecommendCourser();
        $this->ajaxReturn($res);
    }
    /**
    /**
     * 首页查询所有的一级分类及其课程
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getCategoryList
     */
    public function getCategoryList()
    {
        //机构id
        $class = new AppHomepageManage;
        $res = $class->getCategoryList();
        $this->ajaxReturn($res);
    }
//    /**
//     * 查询推荐排课课程列表
//     * @Author yr
//     * @DateTime 2018-04-21T14:11:19+0800
//     * @return   array();
//     * URL:/appstudent/HomePage/getRecommendCourser
//     */
//    public function getRecommendCourser()
//    {
//        $organid = $this->organid;
//        $scheduobj = new AppScheduManage;
//        $res = $scheduobj ->getScheduList($organid);
//        $this->ajaxReturn($res);
//    }
    /**
     * 查询首页二级分类及排课列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/appstudent/HomePage/getChildOrList
     */
    public function getChildOrList()
    {
        $organid = $this->organid;
        $organid = $this->request->post('organid');
        if($organid == 1){
            $class = new OfficalAppScheduManage;
        }else{
            $class = new AppScheduManage;
        }
        $res = $class ->getCategoryOrList($organid);
        $this->ajaxReturn($res);
    }
    /**
     * 名师推荐
     * @Author yr
     * @DateTime 2018-04-21T13:11:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getRecommendTeacher
     */
    public function getRecommendTeacher()
    {
        $class = new AppTeacherManage;
        $res = $class ->getTeacherList();
        $this->ajaxReturn($res);
    }
    /**
     * 名师堂
     * @Author ZQY
     * @DateTime 2018-09-26 15:42:13
     * @return   array();
     * URL:/appstudent/Homepage/teacherRecommend
     */
    public function teacherRecommend()
    {
        $class = new AppTeacherManage;
        $res = $class ->getRecommendTeacherList();
        $this->ajaxReturn($res);
    }
    /**
     * 分类查询
     * @Author yr
     * @param tagid str
     * 标签id集合  可选
     * @param category_id   str 分类id集合  可选
     * @param pagenum   int 分页页码     必填
     * @param tagid    string  标签集合 可选
     * @param limit   string 取出多少条记录    必填
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getFilterCourserList
     */
    public function getFilterCourserList()
    {

        //分类id
        $category_id = $this->request->param('category_id');
        //是否查询免费课程
        $isfree = $this->request->param('is_free');
        //直播和录播标识 coursetype  1 录播课 2直播课
        $coursetype  = $this->request->param('coursetype');
        //分页页数
        $pagenum = $this->request->param('pagenum');
        //配置文件中获取分页条目数
        $limit = config('param.pagesize')['student_courserlist'];
        $class = new ScheduManage();
        $res = $class->getFilterCourserList($category_id,$isfree,$pagenum,$limit,$coursetype);
        $this->ajaxReturn($res);
    }
    /**
     * 课程名称关键字搜索
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    organid  int   机构id
     * @param    category_id int   当前选中的id，全部默认0；
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/searchCourseByCname
     */
    public function searchCourseByCname()
    {
        $organid = $this->request->param('organid');
        $search = $this->request->param('search');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
        /*   $organid = 1;
           $pagenum = 1;
           $limit = 10;
           $search = 'frb';*/
        if($organid == 1){
            $class = new OfficalAppMyCourseManage;
        }else{
            $class = new AppMyCourseManage;
        }
        $res =  $class ->searchCourseByCname($organid,$search,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * 查询城市列表
     * @Author yr
     * @param parentid int 可选
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/appstudent/Homepage/getCityList
     */
    public function getCityList()
    {
        //城市id
        $parentid = $this->request->param('parentid');
        $class = new AppHomepageManage;
        $res = $class->getCityList($parentid);
        $this->ajaxReturn($res);
    }
    /**
     * 随机返回4位验证码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param
     * @return   array();
     * URL:/appstudent/Homepage/randomCode
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
     * @param   [str]                   domain string
     * @return   array();
     * URL:/appstudent/Homepage/sendMobileMsg
     */
    public function sendMobileMsg(){
        $mobile = $this->request->param('mobile');
        $prphone = $this->request->param('prphone');
        $type = $this->request->param('type');

        /*    $mobile = '18235102743';
            $domain = 'http://test.menke.com';*/
        $class = new AppUserManage;
        $res = $class->sendMsg($mobile, $prphone,$type);
        $this->ajaxReturn($res);
    }

    /**
     * 获取感兴趣分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                  studentid  学生id
     * @param   [int]                  organid 机构id
     * @return   array();
     * URL:/appstudent/Homepage/getFavorCategory
     */
    public function getFavorCategory(){
        $organid = $this->request->post('organid');
        $studentid =  $this->request->post('studentid');
        /*   $organid = 1;
           $studentid = 1;*/
        if($organid == 1){
            $class = new OfficalAppUserManage;
        }else{
            $class = new AppUserManage;
        }
        $res =  $class->getFavorCategory($organid,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 添加感兴趣的分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                  studentid  学生id
     * @param   [int]                  organid 机构id
     * @return   array();
     * URL:/appstudent/Homepage/favorCategoryAdd
     */
    public function favorCategoryAdd(){
        $ids = $this->request->param('ids');
        $studentid = $this->userid;
        $userobj = new AppUserManage;
        $res = $userobj->favorCategoryAdd($ids,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 官方首页课程搜索and机构搜索
     * @Author yr
     * @param keywords 关键字搜索
     * @param organid 机构id
     * @param searchtype 搜索类型 1 课程搜索 2机构搜索
     * @param pagenum 分页数量
     * @param pagenum 分页记录条数
     * @DateTime 2018-05-25T16:20:19+0800
     * @return   array();
     * URL:/student/Homepage/searchOrgainOrCourse
     */
    public function searchOrgainOrCourse()
    {
        //机构id
        $organid = $this->request->param('organid');
        $searchtype = $this->request->param('searchtype');
        $keywords = $this->request->param('keywords');
        $pagenum = $this->request->param('pagenum');
        $limit = $this->request->param('limit');
        $class = new AppHomepageManage;
        $organid = isset($organid)?$organid:0;
        $res = $class->searchOrgainOrCourse($organid,$keywords,$pagenum,$limit,$searchtype);
        $this->ajaxReturn($res);
    }
    /**
     * APP端我的课程 查询所有的一级分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                  studentid  学生id
     * @param   [int]                  organid 机构id
     * @return   array();
     * URL:/appstudent/Homepage/getTopCategory
     */
    public function getTopCategory(){
        $obj = new AppHomepageManage();
        $result = $obj ->getTopCategory();
        $this->ajaxReturn($result);
    }
    /**
     * APP端我的课程 筛选一级分类下的所有分类集合
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                  studentid  学生id
     * @param   [int]                  organid 机构id
     * @return   array();
     * URL:/appstudent/Homepage/getTopCategoryChild
     */
    public function getTopCategoryChild(){
        $categoryid = $this->request->post('categoryid');
        $obj = new AppHomepageManage();
        $result = $obj ->getTopCategoryChild($categoryid);
        $this->ajaxReturn($result);
    }
    /**
     * 官方首页机构下的老师分页
     * @Author yr
     * @DateTime 2018-05-25T16:20:19+0800
     * @param limit 推荐条数
     * @return   array();
     * URL:/student/Homepage/getOrganTeacherList
     */
    public function getOrganTeacherList(){
        $pagenum = $this->request->post('pagenum');
        $limit = $this->request->post('limit');
        $organid = $this->request->post('organid');
        if($organid == 1){
            $class = new OfficalAppHomepageManage;
        }else{
            $class = new AppHomepageManage;
        }
        $res = $class->getOrganTeacherList($organid,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 首页-名师堂-查询所有老师信息
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    pagenum int   分页页数
     * @return   array();
     * URL:/student/Homepage/getAllTeacherList
     */
    public function getAllTeacherList()
    {
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $scheduobj = new TeacherManage();
        $res =  $scheduobj ->getAllTeacherList($pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 课程分类
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    organid  int   机构id
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/appstudent/Mycourse/getCategoryArr
     */
    public function getCategoryArr()
    {
        $organid = $this->request->param('organid');
        if($organid == 1){
            $courseobj = new OfficalAppMyCourseManage;
        }else{
            $courseobj = new AppMyCourseManage;
        }
        $res =  $courseobj ->getCategoryArr($organid);
        $this->ajaxReturn($res);

    }
    /**
     * 课程名称关键字搜索
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    searchtype int   1课程搜索2老师搜索
     * @param    search str     搜索内容
     * @param    pagenum int   每页页数
     * @return   array();
     * URL:/student/Mycourse/searchCourseOrTeacher
     */
    public function searchCourseOrTeacher()
    {
        $search = $this->request->param('search');
        $searchtype = $this->request->param('searchtype');
        $pagenum = $this->request->param('pagenum');
        /* $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_searchcourse'];
        /*   $organid = 1;
           $pagenum = 1;
           $limit = 10;
           $search = 'frb';*/
        $class = new AppHomepageManage();
        $res =  $class ->searchCourseOrTeacher($search,$searchtype,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 添加学生喜欢的分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]
     * @return   array();
     * URL:/student/User/getUserFavorCategory
     */
    public function addUserFavorCategory(){
        $categorystr = $this->request->param('categoryid');
        $studentid = $this->userid;
//        $studentid = $this->userid;
        $obj = new AppHomepageManage;
        $res = $obj ->addUserFavorCategory($categorystr,$studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     * URL:/student/Homepage/getPackageDetail
     */
    public function getPackageDetail()
    {
        $packageid = $this->request->param('packageid');
        $packageobj = new AppHomepageManage;
        $res =  $packageobj->getPackageDetail($packageid);
        $this->ajaxReturn($res);

    }
    /**
     * 查询推荐排课详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid  int   课程id
     * @param    teacherid  int   老师id 可选参数 如果是从老师课程入口进入 需要传入teacherid
     * @param   classtypes  int [机构id]  1 录播课 2直播课 录播课没有班级,直接查询
     * @param   date  日期  2018-08-07
     * @param   fullpeople 日期  4或6
     * @return   array();
     * URL:/student/Curriculumdetail/getCurriculumInfo
     */
    public function getCurriculumInfo()
    {
        $courseid= $this->request->param('courseid');
        $teacherid = $this->request->param('teacherid');
        $date = $this->request->param('date');
        $fullpeople = $this->request->param('fullpeople');
        $scheduobj = new ScheduManage();
        $res =  $scheduobj ->getCurriculumInfo($courseid,$teacherid,$date,$fullpeople);
        $this->ajaxReturn($res);

    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 如: 1 或 1,2
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     * URL:/student/Myorder/gotoPay
     */
    public function gotoPay()
    {
        $studentid = $this->userid;
        $ordernum = $this->request->param('ordernum');
        $paytype = $this->request->param('paytype');
        $orderobj = new AppHomepageManage;
        /*  $studentid = 1;
          $ordernum = '201805261741194972438610';
          $amount = '200.00';
          $usablemoney = '99998404.00';
          $paytype = '2';
          $coursename = 'php精讲';
          $classtype = '2';
          $gradename = '班级名称';*/
        $res = $orderobj->gotoPay($studentid,$ordernum,$paytype);
        $this->ajaxReturn($res);

    }

}
