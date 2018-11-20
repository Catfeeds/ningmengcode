<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\RecommendManage;
use think\Session;
use login\Authorize;
class Recommend extends Authorize
{
    /**
     *  
     *
     */
    public function __construct(){
        parent::__construct();
    }
    /**
     * [getCourseList 获取课程推荐列表]
     * @Author wyx
     * @DateTime 2018-04-21T15:58:48+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/getCourseList
     */
    public function getCourseList()
    {
        $coursename = $this->request->param('coursename') ;
        // $coursename = '' ;
        $pagenum    = $this->request->param('pagenum') ;
    	$limit = config('param.pagesize')['adminrecomm_courselist'] ;

    	$manageobj = new RecommendManage;
    	//获取教师列表信息,默认分页为5条
    	$teachlist = $manageobj->getCourseList($coursename,$pagenum,$limit);
    	
    	// var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist;
    }
    /**
     * [exchangeCoursePos 交换课程的推荐位置]
     * @Author wyx
     * @DateTime 2018-04-21T17:51:32+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/exchangeCoursePos
     */
    public function exchangeCoursePos(){
        $idx1   = $this->request->param('courseid1');
        $idx2   = $this->request->param('courseid2');

        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $changeflag = $manageobj->exchangeCoursePos($idx1,$idx2);
        // var_dump($changeflag);
        $this->ajaxReturn($changeflag);
        return $changeflag;
    }
    /**
     * [switchCourseStatus 切换课程的推荐状态]
     * @Author wyx
     * @DateTime 2018-04-21T17:52:40+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/switchCourseStatus
     */
    public function switchCourseStatus(){

        $courseid = $this->request->param('courseid');
        // $courseid = 2 ;
        $status    = $this->request->param('status');
        // $status    = 1;
        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $changestatus = $manageobj->setCourseFlag($courseid,$status);
        // var_dump($changestatus);
        $this->ajaxReturn($changestatus);
        return $changestatus ;
    }
    /**
     * [getTeacherList 获取老师推荐列表]
     * @Author wyx
     * @DateTime 2018-04-21T15:58:48+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/getTeacherList
     */
    public function getTeacherList()
    {   
        $teachername = $this->request->param('teachername') ;
        // $teachername = '' ;
        $pagenum  = $this->request->param('pagenum') ;
        //机构 标识id
        $limit = config('param.pagesize')['adminrecomm_teacherlist'] ;

        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->getTeacherList($teachername,$pagenum,$limit);
        
        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist;
    }
    /**
     * [exchangeTeacherPos 交换老师的推荐位置]
     * @Author wyx
     * @DateTime 2018-04-21T17:51:32+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/exchangeTeacherPos
     */
    public function exchangeTeacherPos(){
        $idx1   = $this->request->param('teacherid1');
        $idx2   = $this->request->param('teacherid2');
        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->exchangeTeacherPos($idx1,$idx2);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     * [switchTeacherStatus 切换老师的推荐状态]
     * @Author wyx
     * @DateTime 2018-04-21T17:52:40+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/switchTeacherStatus
     */
    public function switchTeacherStatus(){
        $teacherid = $this->request->param('teacherid');
        // $teacherid = 5 ;
        $status    = $this->request->param('status');
        // $status    = 1;
        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $recommendflag = $manageobj->setTeacherFlag($teacherid,$status);
        // var_dump($recommendflag);
        $this->ajaxReturn($recommendflag);
        return $recommendflag;
    }
    /**
     * [addTeacherImage 设置推荐老师照片和标语]
     * @Author wyx
     * @DateTime 2018-04-21T17:52:40+0800
     * @return   [type]                   [description]
     * URL:/admin/recommend/addTeacherImage
     */
    public function addTeacherImage(){
        $data      = Request::instance()->post();

        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $recommendflag = $manageobj->addTeacherImage($data);
        // var_dump($recommendflag);
        $this->ajaxReturn($recommendflag);
    }
    /**
     *  [getOrganSlide 获取机构轮播图]
     *  @return   [type]                   [description]
     *  URL:/admin/recommend/getOrganSlide
     *
     */
    public function getOrganSlide(){

        $manageobj = new RecommendManage;
        //获取机构的轮播图
        $slideimg = $manageobj->getOrganSlide();
        $this->ajaxReturn($slideimg);
    }
    /**
     *  [addSlideImage 添加机构轮播图]
     *  @return   [type]                   [description]
     *  URL:/admin/recommend/addSlideImage
     *
     */
    public function addSlideImage(){
        $data      = Request::instance()->post();
        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->addSlideImage($data);

        $this->ajaxReturn($slideimg);
    }
    /**
     *  [addSlideImage 编辑机构轮播图]
     *  @return   [type]                   [description]
     *  URL:/admin/recommend/editSlideImage
     */
    public function editSlideImage(){
        $data      = Request::instance()->post();
        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->editSlideImage($data);
        $this->ajaxReturn($slideimg);
    }
    /**
     *  [delSlideImage 删除机构轮播图]
     *  @return   [type]                   [description]
     *  URL:/admin/recommend/delSlideImage
     *
     */
    public function delSlideImage(){
        $id = $this->request->param('id');
        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->delSlideImage($id);

        $this->ajaxReturn($slideimg);
    }
   
}
