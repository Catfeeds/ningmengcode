<?php
namespace app\microsite\business;
use app\student\model\Curriculum;
use app\student\model\Recommendcategory;
use app\student\model\Organcollection;
use app\student\model\Organslideimg;
use app\student\model\Receive;
use app\student\model\Scheduling;
use app\student\model\City;
use app\student\model\Category;
use app\student\model\Officialslideimg;
use app\student\model\Schedulingdeputy;
use app\student\model\Organ;
use app\student\model\Teacherinfo;
use app\student\model\Teachertagrelate;
use Login;
use app\student\controller\Loginbase;
use think\Cache;

class MicroHomepageManage
{
    protected $foo;
    protected $str;
    protected $array;
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
        //初始化课程类型
        $this->array = ['一对一','小课班','大课班'];
    }
    /**
     * 获取机构信息
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getOrganInfo(){
        $organmodel = new Organ;
        $info = $organmodel->getOrganmsgByDomain();
        //获取当前域名
        $url = $_SERVER['SERVER_NAME'];
        $info['url'] = $url;
        if(!empty($info)){
            return return_format($info,0,lang('success'));
        }else{
            return return_format([],0,lang('success'));
        }
    }
    /**
     * 根据课程名称搜索课程
     * @Author yr
     * @param    studentid int   学生id
     * @param    search  str   搜索内容
     * @return array
     */
    public function searchCourseOrTeacher($search,$searchtype,$pagenum,$limit){
        if(!is_intnum($searchtype)){
            return return_format('',33105,lang('param_error'));
        }
        if(empty($search)){
            return return_format('',33107,lang('33107'));
        }
        //防止脚本攻击和SQL注入
        if (isset($search)){
            $str = trim($search);  //清理空格
            $str = strip_tags($str);   //过滤html标签
            $str = htmlspecialchars($str);   //将字符内容转化为html实体
            $str = addslashes($str);
        }
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        switch ($searchtype){
            case 1:
                $coursemodel = new Curriculum;
                $data['arr'] = $coursemodel->getCurriculumByCname($str,$limitstr);
                $total = $coursemodel->getCurriculumByCnameCount($str);
                $data['pageinfo'] = [
                    'pagesize'=>$limit ,// 每页多少条记录
                    'pagenum' =>$pagenum ,//当前页码
                    'total'   => $total // 符合条件总的记录数
                ];
                //搜索课程
                break;
            case 2:
                //搜索老师
                $teachermodel = new Teacherinfo;
                $data['arr'] = $teachermodel->searchTeacherList($str,$limitstr);
                $total =  $teachermodel->searchTeacherCount($str);
                $data['pageinfo'] = [
                    'pagesize'=>$limit ,// 每页多少条记录
                    'pagenum' =>$pagenum ,//当前页码
                    'total'   => $total // 符合条件总的记录数
                ];
        }

        //分页信息

        return return_format($data,0,lang('success'));
    }
	
	/**
     * 获取所有三级分类
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getThreeCategroyList(){
        $categorymdoel = new Category();
        $where = [
            'delflag'=>1,
            'status'=>1,
            'rank'=>3,
        ];
        $field = 'id as category_id ,categoryname,rank,fatherid,imgs';
        $result = $categorymdoel->getSelectInfo($where,$field);
        $grouped = [];
        foreach ($result as $value) {
            $grouped[$value['categoryname']][] = $value;
        }
        $grouped  = array_values($grouped);
        $newlist = [];
        foreach($grouped as $k=>$value){
            $newlist[$k]['categoryname'] = $grouped[$k][0]['categoryname'];
            $newlist[$k]['rank'] = $grouped[$k][0]['rank'];
            $categoryids = implode(',',array_column($grouped[$k],'category_id'));
            $newlist[$k]['category_id'] = $categoryids;
            $newlist[$k]['imgs'] = $grouped[$k][0]['imgs'];
        }
        //对数据分组 5条数据一组
        $result = array_chunk($newlist,5);
        return return_format($result,0,lang('success'));
    }
	
    /**
     * 首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/microsite/Homepage/getThreeCourseList
     */
    public function getThreeCourseList($categoryids)
    {
        if(empty($categoryids)){
            return return_format('',37100,'参数categoryid错误');
        }
        $model = new Curriculum();
        $field = 'id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum';
        $where = ['categoryid'=>['in',$categoryids]];
        $res = $model->getSelectInfo($where,$field);
        return return_format($res,0,lang('success'));
    }
	
    /**
     * 首页推荐课程列表
     * @Author yr
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/microsite/HomePage/getRecommendCourser
     */
    public function getRecommendCourser()
    {

      $model = new Recommendcategory;
      $where = [
          'delflag' => 1,
          'curriculumids' => ['neq',''],
      ];
      $result = $model->getCategoryList($where);
      $coursemodel = new Curriculum();
      if(!empty($result)){
          foreach ($result as $k=>$v){
              $idarr = explode(',',$v['curriculumids']);
              foreach($idarr as $key=>$value){
                  $wherearr = [
                      'id' => $value
                  ];
                  $result[$k]['coursearr'][$key] = $coursemodel->getCourserById($wherearr);
              }
          }
      }
      return return_format($result,0,lang('success'));
    }
	
	/**
     * 获取各一级分类最新5个课程
     * @Author lc
     * @DateTime 2018-04-21T14:11:19+0800
     * @return   array();
     * URL:/microsite/HomePage/getTopCategoryCourser
     */
    public function getTopCategoryCourser()
    {
		$categorymodel = new Category;
		$coursemodel = new Curriculum;
		$topids = array_column($categorymodel->getTopList(), 'category_id');
	  
	    foreach($topids as $k=>$id){
			$result[$k] = $categorymodel->getCategoryname($id);
			$categorylist = $categorymodel->get_category($id);
            $categoryids = rtrim($categorylist,',');
			$result[$k]['courselist'] = $coursemodel->getFilterCourserList(0,$categoryids,'0,5',2);
	    }
        return return_format($result,0,lang('success'));
    }
	
    /**
     * 获取首页分类信息
     * @Author yr
     * @return array
     *
     */
    public function getCategoryList(){
        $catemodel  = new Category;
        $result = $catemodel->getCategory();
        //拼装树状结构
        $list = generateTree($result,'category_id');
        if(empty($list)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($list,0,lang('success'));
        }

    }
    /**
     * 获取首页分类信息
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getChildList($categoryid){
        $catemodel  = new Category;
        $result = $catemodel->getChildList($categoryid);
        foreach($result as $k=>$v){
            $result[$k]['son'] = $catemodel->getChildList($v['category_id']);
        }
        //拼装树状结构
        if(empty($result)){
            return return_format($result,0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }

    }
    /**
     * 获取首页直播课的课程及其分类
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getLiveList(){
        $catemodel  = new Category;
        $result = $catemodel->getChildList();
        foreach($result as $k=>$v){
            $result[$k]['son'] = $catemodel->getChildList($v['category_id']);
        }
        //拼装树状结构
        if(empty($result)){
            return return_format($result,0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }

    }
    /**
     * 获取老师推荐列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getScheduList ()
    {
        $coursermodel = new Scheduling;
        $teacherlist = $coursermodel->getCourserList();
        if(empty($teacherlist)){
            return return_format([],0,lang('success'));
        }else{
            foreach($teacherlist as $k=>$v){
                $teacherlist[$k]['typename'] = $this->array[$v['type']];
            }
            return return_format($teacherlist,0,lang('success'));
        }
    }
    /**
     * 查询城市列表
     * @Author yr
     * @param parentid int 可选
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     */
    public function getCityList($parentid=0)
    {
        $parentid = isset($parentid)?$parentid:0;
        //城市id
        $class = new City;
        $res = $class->getCityList($parentid);
        return return_format($res,0,lang('success'));
    }
    /**
     * 获取首页轮播列表
     * @Author yr
     * @param parentid int 可选
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     */
    public function getSlideList()
    {

        //机构id
        $class = new Organslideimg;
        $res = $class->getSlideList();
        $coursemodel = new Curriculum();
        foreach($res as $k=>$v){
            if($v['urltype'] == 1){
                $where = ['id'=>$v['courseid']];
                $res[$k]['coursetype'] = $coursemodel->getCourserById($where)['classtypes'];
            }
        }
        if(empty($res)){
            return return_format([],0,lang('success'));
        }
            return return_format($res,0,lang('success'));
    }
    /**
     * 官方首页课程搜索and机构搜索
     * @Author yr
     * @param keywords 关键字搜索
     * @param organid 机构id
     * @return   array();
     */
    public function searchOrgainOrCourse($keywords,$pagenum,$limit,$searchtype)
    {
        //防止脚本攻击和SQL注入
        if (isset($keywords)){
            $str = trim($keywords);  //清理空格
            $str = strip_tags($str);   //过滤html标签
            $str = htmlspecialchars($str);   //将字符内容转化为html实体
            $keywords = addslashes($str);
        }
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        switch($searchtype){
            case 1:
                $schedumodel = new Schedulingdeputy;
                $coursearr = $schedumodel->searchOfficialByCname($keywords,$limitstr);
                $total = $schedumodel->searchOfficialCount($keywords);
                break;
            case 2:
                $organmodel = new Organ;
                $coursearr = $organmodel->searchOrganByName($keywords,$limitstr);
                $total  = $organmodel->searchOrganCount($keywords);
                break;
            default:
                return return_format('',33108,lang('param_error'));
        }
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['arr'] = $coursearr;
        if(empty($coursearr)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 官方首页推荐机构
     * @Author yr
     * @return   array();
     */
    public function getRecommendOrgan($limit){
        $organobj = new Organ;
        if(!is_intnum($limit)){
            return return_format('',33109,lang('param_error'));
        }
        $result = $organobj->getRecommendOrgan($limit);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 官方首页机构详情
     * @Author yr
     * @param organid 机构id
     * @return   array();
     */
    public function getOrganDetail(){

        $organobj  = new Organ;
        $result = $organobj->getArrByid();
        //查看是否登录
        $loginobj = new Loginbase;
        $userinfo = $loginobj->checkUserLogin();
        $studentid = $userinfo['uid'];
        if($studentid == false){
            $result['is_collect'] = 0;
        }else{
            $organcollectmodel = new Organcollection;
            $where['studentid'] = $studentid;
            $field = 'id';
            $cid = $organcollectmodel->getDataInfo($where,$field);
            $result['is_collect'] = empty($cid)?0:1;
        }
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 官方首页机构下的课程分页
     * @Author yr
     * @param organid 机构id
     * @return   array();
     */
    public function getOrganCourseList($pagenum,$limit){
        if(!is_intnum($limit)){
            return return_format('',33113,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{

            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $schudeobj = new Schedulingdeputy;
        $result = $schudeobj->getOrgainClassList($limitstr);
        $total = $schudeobj->getOrgainClassCount();
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['data'] = $result;
        if(empty($result)){
            return return_format($data,0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
        }

    }
    /**
     * 官方首页机构下的老师分页
     * @Author yr
     * @param organid 机构id
     * @return   array();
     */
    public function getOrganTeacherList($pagenum,$limit){
        if(!is_intnum($limit)){
            return return_format('',33113,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{

            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $schudeobj = new Teacherinfo;
        $result = $schudeobj->getOrganTeacherList($limitstr);
        if(!empty($result)){
            $tagmodel = new Teachertagrelate;
           foreach($result as $k=>$v){
               $result[$k]['taglist'] = $tagmodel->getTeacherLable($v['teacherid']);
           }
        }
        $total = $schudeobj->getOrganTeacherCount();
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['data'] = $result;
        if(empty($result)){
            return return_format($data,0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 赠送免费课程
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [string]              mobile  必填手机号
     * @param    [string]              prphone    手机号前缀
     * @param    [int]                 code   手机号验证码
     * @return   array();
     */
    public function addReceiveInfo($mobile,$prphone,$code){
        if( empty($mobile) || empty($code) || empty($prphone)){
            return return_format($this->str,33200,lang('param_error'));
        }
        if(strlen($mobile)>12 || strlen($mobile)<6){
            return return_format($this->str,33201,lang('37037'));
        }
        $cachedata = Cache::get('mobile'.$mobile);
        if(empty($cachedata)){
            return return_format($this->str,33202,lang('37038'));
        }
        //验证验证码是否正确并且验证次数
        if(trim($cachedata) !== trim($code)){
            if(!verifyErrorCodeNum($mobile)){
                return return_format($this->str,33203,lang('37039'));
            }
            return return_format($this->str,33204,lang('37040'));
        }
        $data['mobile'] = $mobile;
        $data['prphone'] = $prphone;
        $data['receivetime'] = time();
        $receivemodel = new Receive;
        $result = $receivemodel->addData($data);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',37041,lang('error'));
        }
    }
	
	/**
     * 获取所有的一级分类
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getTopCategory(){
        $categorymodel = new Category();
        $result = $categorymodel->getTopList();
        return return_format($result,0,lang('success'));
    }
	
    /**
     * APP端我的课程 筛选一级分类下的所有分类集合
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                  categoryid 分类id
     * @return   array();
     * URL:/appstudent/Homepage/getTopCategoryChild
     */
    public function getTopCategoryChild($categoryid){
        if(!is_intnum($categoryid)){
            return return_format('',37000,'参数分类id错误');
        }
        $categorymodel = new Category();
        $list  = $categorymodel->getChildList($categoryid);
        foreach($list as $k=>$v){
            $list[$k]['child'] = $categorymodel->getChildList($v['category_id']);
        }
        return return_format($list,0,lang('success'));
    }
}
