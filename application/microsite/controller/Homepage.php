<?php
/**
 * 首页分类课程筛选 业务逻辑层
 *
 *
 */
namespace app\microsite\controller;
use app\student\model\Curriculum;
use think\Controller;
use app\student\business\ScheduManage;
use app\microsite\business\MicroScheduManage;
use app\microsite\business\MicroHomepageManage;
use app\microsite\business\MicroTeacherManage;
use app\microsite\business\MicroUserManage;
use app\microsite\business\MicroLoginManage;
use app\microsite\business\MicroWebScheduManage;
use app\microsite\business\MicroWebLoginManage;
use app\microsite\business\MicroPackageManage;
use Verifyhelper;
class Homepage extends \Base
{
    //发送短信业务类型 1找回密码2手机注册
    protected  $sendtype = [1,2];
    //机构和官网的轮播类型 1机构2官网
    protected   $slidetype = [1,2];
    public function __construct(){
        // 必须先调用父类的构造函数
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin:*');
        parent::__construct();
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
     * URL:/microsite/Home/updateUserPass
     */
    public function register()
    {
        $post  = $this->request->post(false);
        $loginobj = new MicroLoginManage;
        $res = $loginobj->register($post);
        $this->ajaxReturn($res);

    }

    /**
     * 检测用户登录信息
     * @Author yr
     * @DateTime 2018-04-20T13:11:19+0800
     * @return   array();
     * URL:/microsite/Homepage/login
     */
    public function login ()
    {

        $mobile = input('mobile');
        $password = input('password');
        $organid = input('organid');
        if($organid == 1){
            $loginobj= new WebLoginManage;
        }else{
            $loginobj = new MicroLoginManage;
        }
        $res = $loginobj->login($mobile,$password,$organid);
        $this->ajaxReturn($res);

    }
    /**
     * 首页获取机构相关信息接口
     * @Author yr
     * @DateTime 2018-05-5T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getOrganInfo
     */
    public function getOrganInfo()
    {
        $homepageobj  = new MicroHomepageManage;
        $res = $homepageobj->getOrganInfo();
        $this->ajaxReturn($res);
    }
    /**
     * 首页轮播接口
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getSlideList
     */
    public function getSlideList()
    {
        $class = new MicroHomepageManage;
        $res = $class->getSlideList();
        $this->ajaxReturn($res);
    }
	
	/**
     * 微网站首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/microsite/Homepage/getThreeCategroyList
     */
    public function getThreeCategroyList()
    {
        $obj = new MicroHomepageManage();
        $res = $obj->getThreeCategroyList();
        $this->ajaxReturn($res);
    }
    /**
     * 微网站首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/microsite/Homepage/getThreeCourseList
     */
    public function getThreeCourseList()
    {
        $categoryid = $this->request->post('categoryid');
        $obj = new MicroHomepageManage();
        $res = $obj->getThreeCourseList($categoryid);
        $this->ajaxReturn($res);
    }
	
    /**
     * 柠檬教育全部课程分类接口
     * @Author yr
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getCategoryList
     */
    public function getCategoryList()
    {
        $class = new MicroHomepageManage;
        $res = $class->getCategoryList();
        $this->ajaxReturn($res);
    }
    /**
     * 首页分类接口
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getChildList
     */
    public function getChildList()
    {
        $categoryid = $this->request->param('categoryid');
        $class = new MicroHomepageManage;
        $res = $class->getChildList($categoryid);
        $this->ajaxReturn($res);
    }
    /**
     * 柠檬教育首页直播课程分类及课程
     * @Author yr
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getLiveList
     *
     */
    public function getLiveList()
    {
        $class = new MicroHomepageManage;
        $res = $class->getLiveList();
        $this->ajaxReturn($res);
    }
    /**
     * 柠檬教育查询推荐课程列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/microsite/HomePage/getRecommendCourser
     */
    public function getRecommendCourser()
    {
        $scheduobj = new MicroHomepageManage;
        $res = $scheduobj ->getRecommendCourser();
        $this->ajaxReturn($res);
    }
	
	/**
     * 获取各一级分类最新5个课程
     * @Author lc
     * @return   array();
     * URL:/microsite/HomePage/getTopCategoryCourser
     */
    public function getTopCategoryCourser()
    {
        $scheduobj = new MicroHomepageManage;
        $res = $scheduobj ->getTopCategoryCourser();
        $this->ajaxReturn($res);
    }
	
    /**
     * 查询首页二级分类及排课列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/microsite/HomePage/getChildOrList
     */
    /* public function getChildOrList()
    {
        $scheduobj = new MicroScheduManage;
        $res = $scheduobj ->getCategoryOrList();
        $this->ajaxReturn($res);
    } */
    /**
     * 查询指定分类下的排课列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/microsite/HomePage/getCategorySchedu
     */
    public function getCategorySchedu()
    {
        $categoryid = $this->request->param('categoryid');
        $scheduobj = new MicroScheduManage;
        $res = $scheduobj ->getCategorySchedu($categoryid);
        $this->ajaxReturn($res);
    }
    /**
     * 名师推荐
     * @Author yr
     * @DateTime 2018-04-21T13:11:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getRecommendTeacher
     */
    public function getRecommendTeacher()
    {
        $teacherobj = new MicroTeacherManage;
        $res =  $teacherobj ->getTeacherList();
        $this->ajaxReturn($res);
    }
	
	/**
     * 微网站我的课程 查询所有的一级分类
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getTopCategory
     */
    public function getTopCategory(){
        $obj = new MicroHomepageManage();
        $result = $obj ->getTopCategory();
        $this->ajaxReturn($result);
    }
	
    /**
     * 微网站我的课程 筛选一级分类下的所有分类集合
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getTopCategoryChild
     */
    public function getTopCategoryChild(){
        $categoryid = $this->request->post('categoryid');
        $obj = new MicroHomepageManage();
        $result = $obj ->getTopCategoryChild($categoryid);
        $this->ajaxReturn($result);
    }
	
    /**
     * 分类查询
     * @Author yr
     * @param tagid str
     * 标签id集合  可选
     * @param is_free  int 0查看全部课程 1 免费
     * @param category_id   str 分类id集合  可选
     * @param pagenum   int 分页页码     必填
     * @param limit   string 取出多少条记录    必填
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getFilterCourserList
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
        $class = new ScheduManage;
        $res = $class->getFilterCourserList($category_id,$isfree,$pagenum,$limit,$coursetype);
        $this->ajaxReturn($res);
    }
    
    /**
     * 查询城市列表
     * @Author yr
     * @param parentid int 可选
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getCityList
     */
    public function getCityList()
    {
        //城市id
        $parentid = $this->request->param('parentid');
        $class = new MicroHomepageManage;
        $res = $class->getCityList($parentid);
        $this->ajaxReturn($res);
    }
    /**
     * 随机返回图形验证码
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param
     * @return   array();
     * URL:/microsite/Homepage/randomCode
     */
    public function randomCode(){
        $userobj = new MicroUserManage;
        $res = $userobj->getCaptcha();
        $this->ajaxReturn($res);
    }
    /**
     * 发送短信业务类型 1找回密码2手机注册
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [int]                   organid 机构id
     * @return   array();
     * URL:/microsite/Homepage/sendMobileMsg
     */
    public function sendMobileMsg(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $sessionid = $this->request->param('sessionid');
        $type = $this->request->param('type');
        $prphone = $this->request->param('prphone');
     /*   $mobile = '18235102743';
        $code = 'mv6ja';
        $organid = 1;
        $sessionid = 'c08a51b5059772bb5e6213e770f3fc62';*/
        $userobj = new MicroUserManage;
        $res = $userobj->sendMsg($mobile,$code,$sessionid,$type,$prphone);
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
     * URL:/microsite/Homepage/updatePass
     */
    public function updatePass(){
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
        $newpass = $this->request->param('newpass');
        $userobj = new MicroUserManage;
        $res = $userobj->updatePassword($mobile,$code,$newpass);
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
     * URL:/microsite/Mycourse/searchCourseOrTeacher
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
        $class = new MicroHomepageManage;
        $res =  $class ->searchCourseOrTeacher($search,$searchtype,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * 官方首页课程搜索and机构搜索
     * @Author yr
     * @param keywords 关键字搜索
     * @param searchtype 搜索类型 1 课程搜索 2机构搜索
     * @param pagenum 分页数量
     * @param pagenum 分页记录条数
     * @DateTime 2018-05-25T16:20:19+0800
     * @return   array();
     * URL:/microsite/Homepage/searchOrgainOrCourse
     */
    public function searchOrgainOrCourse()
    {
        $searchtype = $this->request->param('searchtype');
        $keywords = $this->request->param('keywords');
        $pagenum = $this->request->param('pagenum');
      /*  $limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_searchall'];
        $class = new MicroHomepageManage;
        $res = $class->searchOrgainOrCourse($keywords,$pagenum,$limit,$searchtype);
        $this->ajaxReturn($res);
    }
    /**
     * 官方首页推荐机构
     * @Author yr
     * @DateTime 2018-04-21T16:20:19+0800
     * @param limit 推荐条数
     * @return   array();
     * URL:/microsite/Homepage/getRecommendOrgan
     */
    public function getRecommendOrgan()
    {
        //城市id
        $limit = $this->request->post('limit');
        $class = new MicroHomepageManage;
        $res = $class->getRecommendOrgan($limit);
        $this->ajaxReturn($res);
    }
    /**
     * 官方首页机构详情
     * @Author yr
     * @DateTime 2018-05-25T16:20:19+0800
     * @param limit 推荐条数
     * @return   array();
     * URL:/microsite/Homepage/getOrganDetail
     */
    public function getOrganDetail()
    {
        $class = new MicroHomepageManage;
        $res = $class->getOrganDetail();
        $this->ajaxReturn($res);
    }
    /**
     * 官方首页机构下的课程分页
     * @Author yr
     * @DateTime 2018-05-25T16:20:19+0800
     * @param limit 推荐条数
     * @return   array();
     * URL:/microsite/Homepage/getOrganCourseList
     */
    public function getOrganCourseList()
    {
        $pagenum = $this->request->post('pagenum');
       /* $limit = $this->request->post('limit');*/
        $limit = config('param.pagesize')['student_getorgancourse'];
        $class = new MicroHomepageManage;
        $res = $class->getOrganCourseList($pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 官方首页机构下的老师分页
     * @Author yr
     * @DateTime 2018-05-25T16:20:19+0800
     * @param limit 推荐条数
     * @return   array();
     * URL:/microsite/Homepage/getOrganTeacherList
     */
    public function getOrganTeacherList(){
        $pagenum = $this->request->post('pagenum');
       /* $limit = $this->request->post('limit');*/
        $limit = config('param.pagesize')['student_commentlist'];
        $class = new MicroHomepageManage;
        $res = $class->getOrganTeacherList($pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 首页-名师堂-查询所有老师信息
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    pagenum int   分页页数
     * @return   array();
     * URL:/microsite/Homepage/getAllTeacherList
     */
    public function getAllTeacherList()
    {
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_orderlist'];
        $scheduobj = new MicroTeacherManage;
        $res =  $scheduobj ->getAllTeacherList($pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 发送短信
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @return   array();
     * URL:/microsite/Homepage/sendUpdateMobileMsg
    */
    public function sendUpdateMobileMsg(){
        $newmobile = $this->request->param('mobile');
        $prphone = $this->request->param('prphone');
        $userobj = new MicroUserManage;
        $res = $userobj->sendUpdatemobileMsg($newmobile,$prphone,$type=1);
        $this->ajaxReturn($res);
    }
    /**
     * 赠送免费课程
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @param    [int]                 code   手机号验证码
     * @return   array();
     * URL:/microsite/Homepage/addReceiveInfo
     */
    public function addReceiveInfo(){
        $mobile = $this->request->post('mobile');
        $prphone = $this->request->post('prphone');
        $code = $this->request->post('code');
        $userobj = new MicroHomepageManage;
        $res = $userobj->addReceiveInfo($mobile,$prphone,$code);
        $this->ajaxReturn($res);
    }
	
	/**
     * 查询微网站/app首页套餐
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @return   array();
     * URL:/microsite/Homepage/getHomePackageList
     */
    public function getHomePackageList()
    {
        $packageobj = new MicroPackageManage();
        $res =  $packageobj->getHomePackageList();
        $this->ajaxReturn($res);

    }
	
	/**
     * 查询我的套餐列表
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    pagenum int   分页页数
     * @return   array();
     * URL:/microsite/Homepage/getPackageList
     */
    public function getPackageList()
    {
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['student_packagelist'];
        $packageobj = new MicroPackageManage();
        $res =  $packageobj->getPackageList($pagenum,$limit);
        $this->ajaxReturn($res);

    }
    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     * URL:/microsite/Homepage/getPackageDetail
     */
    public function getPackageDetail()
    {
        $packageid = $this->request->param('packageid');
        $packageobj = new MicroPackageManage;
        $res =  $packageobj->getPackageDetail($packageid);
        $this->ajaxReturn($res);

    }
}
