<?php
namespace app\appstudent\business;
use app\student\model\Organslideimg;
use app\student\model\Coursepackage;
use app\student\model\Scheduling;
use app\student\model\City;
use app\student\model\Category;
use app\student\model\Studentinfo;
use app\student\model\Studentaddress;
use app\student\model\Schedulingdeputy;
use app\student\model\Organ;
use app\student\model\Teacherinfo;
use app\student\model\Teachertagrelate;
use app\student\model\Organcollection;
use app\student\model\Curriculum;
use app\student\model\Studentfunds;
use app\student\model\Period;
use app\student\model\Ordermanage;
use app\student\model\Unit;
use app\student\model\Lessons;
use app\student\model\Coursecomment;
use app\student\model\Unitdeputy;
use app\student\model\Classcollection;
use app\student\controller\Loginbase;
use alipay\Alipaydeal;
use wxpay\Wxpay;
use Order;
use Login;
class AppHomepageManage
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
        //获取二级域名
        $domain = getSecondDomain();
        $organmodel = new Organ;
        $info = $organmodel->getOrganmsgByDomain($domain);
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
     * APP首页展示所有的三级分类
     * @Author yr
     * @param organ_id  int 机构id
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     *
     * URL:/appstudent/Homepage/getThreeCourseList
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
    /**
     * 获取所有三级分类
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getTopCategroyList(){
        $categorymdoel = new Category();
        $where = [
            'delflag'=>1,
            'status'=>1,
            'rank'=>3,
        ];
        $field = 'id as category_id ,categoryname,rank,fatherid';
        $result = $categorymdoel->getSelectInfo($where,$field);
        //对数据分组 5条数据一组
        $result = array_chunk($result,5);
        return return_format($result,0,lang('success'));
    }
    /**
     * 获取分类
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getCategoryList(){
        //查询所有的一级分类及课程
        $coursemodel = new Curriculum();
        $categorymodel = new Category();
        $categorylist =   $categorymodel->getTopList();
        foreach($categorylist as $k=>$v){
            $categorystr = $categorymodel->get_category($v['category_id']);
            $categoryids = rtrim($categorystr,',');
            $categoryidarr = explode(',',$categoryids);
            $where = [
                'categoryid' => ['in',$categoryidarr]
            ];
            $field = 'id as courseid,coursename,subhead,imageurl,price,maxprice,giftdescribe,classtypes,classnum';
            $categorylist[$k]['courselist'] = $coursemodel->getSelectInfos($where,$field);
            /*if(empty( $categorylist[$k]['courselist'])){
                unset($categorylist[$k]);
            }*/
        }
        //$categorylist = array_values($categorylist);
        return return_format($categorylist,0,lang('success'));
    }
    /**
     * 获取老师推荐列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getScheduList ($organid)
    {
        if(!is_numeric($organid) || empty($organid)){
            return return_format($this->str,30001,lang('param_error'));
        }
        $coursermodel = new Scheduling;
        $teacherlist = $coursermodel->getCourserList($organid);
        if(empty($teacherlist)){
            return return_format($teacherlist,0,lang('success'));
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
     * 获取首页分类信息
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getChildList($organid,$categoryid){
        $catemodel  = new Category;
        $result = $catemodel->getChildList($organid,$categoryid);
        foreach($result as $k=>$v){
            $result[$k]['son'] = $catemodel->getChildList($organid,$v['category_id']);
        }
        //拼装树状结构
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }

    }

    /**
     * 官方首页课程搜索and机构搜索
     * @Author yr
     * @param keywords 关键字搜索
     * @param organid 机构id
     * @return   array();
     */
    public function searchOrgainOrCourse($organid,$keywords,$pagenum,$limit,$searchtype)
    {
        if($organid !== '1'){
            return return_format('',30003,lang('param_error'));
        }
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
                return return_format('',30004,lang('param_error'));
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
            return return_format('',30005,lang('param_error'));
        }
        $result = $organobj->getRecommendOrgan($limit);
        if(empty($result)){
            return return_format('',0,lang('success'));
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
    public function getOrganDetail($organid){
        if(!is_intnum($organid)){
            return return_format('',30006,lang('param_error'));
        }
        $organobj  = new Organ;
        $result = $organobj->getArrByid($organid);
        if(empty($result)){
            return return_format('',30007,lang('30007'));
        }
        //查看是否登录
        $loginobj = new Login;
        $studentid = $loginobj->checkIsLogin(1);
        if($studentid == false){
            $result['is_collect'] = 0;
        }else{
            $organcollectmodel = new Organcollection;
            $where['organid'] = $organid;
            $where['studentid'] = $studentid;
            $field = 'id';
            $cid = $organcollectmodel->getDataInfo($where,$field);
            $result['is_collect'] = empty($cid)?0:1;
        }
        if(empty($result)){
            return return_format('',0,lang('success'));
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
    public function getOrganCourseList($organid,$pagenum,$limit){
        if(!is_intnum($organid)|| !is_intnum($limit)){
            return return_format('',30008,lang('param_error'));
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
        $result = $schudeobj->getOrgainClassList($organid,$limitstr);
        $total = $schudeobj->getOrgainClassCount($organid);
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['data'] = $result;
        if(empty($result)){
            return return_format([],0,lang('success'));
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
    public function getOrganTeacherList($organid,$pagenum,$limit){
        if(!is_intnum($organid)|| !is_intnum($limit)){
            return return_format('',30009,lang('param_error'));
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
        $result = $schudeobj->getOrganTeacherList($organid,$limitstr);
        if(!empty($result)){
            $tagmodel = new Teachertagrelate;
            foreach($result as $k=>$v){
                $result[$k]['taglist'] = $tagmodel->getTeacherLable($v['teacherid'],$organid);
            }
        }
        $total = $schudeobj->getOrganTeacherCount($organid);
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['data'] = $result;
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
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
                foreach ($data['arr'] as $k=>$v){
                    unset($data['arr'][$k]['maxprice']);
                    unset($data['arr'][$k]['giftdescribe']);
                    unset($data['arr'][$k]['classnum']);
                }
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
     * 添加用户喜欢的分类
     * @Author yr
     * @return array
     *
     */
    public function addUserFavorCategory($categoryid,$studentid){
        if(empty($categoryid)){
            return return_format('',-37204,lang('-37204'));
        }
        if(!is_intnum($studentid)){
            return return_format('',-37200,lang('-37200'));
        }
        $usermodel = new Studentinfo;
        $result = $usermodel->favorAdd($categoryid,$studentid);
        if($result>=0){
            return return_format($result,0,lang('success'));
        }

    }
    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     *
     */
    public function getPackageDetail($packageid){
        if(!is_intnum($packageid)){
            return return_format('',38001,lang('param_error'));
        }
        $packetmodel = new Coursepackage();
        $result = $packetmodel->getPackageDetail($packageid);
        if($result['trialtype'] == 2){
            //如果可使用课程类型为指定分类
            $categoryarr = explode(',',$result['categoryids']);
            foreach($categoryarr as $k=>$v){

            }
        }
        return return_format($result,0,lang('success'));
    }
    /**
     * 查询录播课程相关信息
     * @Author yr
     * @param $teacherid   int [老师id]
     * @return array
     *
     */
    public function getRecordeInfo($courseid){
        //实例化model
        $coursemodel = new Curriculum;
        $teachermodel = new Teacherinfo;
        $ordermodel = new Ordermanage;
        $unitmodel = new Unit;
        $lessonsmodel = new Period;
        $schedumodel = new Scheduling;
        $categorymodel = new Category;
        //获取课程相关信息
        $where = [
            'id' => $courseid
        ];
        $courserinfo['courser'] = $coursemodel->getCourserById($where);
        $courserinfo['courser']['totalprice'] = $courserinfo['courser']['price'];
        $data = $courserinfo['courser'];
        //查看是否登录
        $loginobj = new Loginbase;
        $userinfo = $loginobj->checkUserLogin();
        $studentid = $userinfo['uid'];
        if($studentid == false){
            $data['is_collect'] = 0;
            $data['applystatus'] = 1;
        }else{
            $organcollectmodel = new Classcollection;
            $collectwhere['courseid'] = $courseid;
            $collectwhere['studentid'] = $studentid;
            $field = 'id';
            $cid = $organcollectmodel->getDataInfo($collectwhere,$field);
            $data['is_collect'] = empty($cid)?0:1;
            //查看学生是否购买过此课程
            $isbuy  = $ordermodel->isBuyCourse($courseid,$studentid);
            if($isbuy >0){
                $data['applystatus'] = 2;
                $data['ordernum'] = $ordermodel->getCourseOrdernum($courseid,$studentid)['ordernum'];
            }else{
                $data['applystatus'] = 1;
            }
        }
        $list = array();
        if(!empty($courserinfo['courser']['categoryid'])){
            //获取课程分类
            $categorystr = $categorymodel->get_parent_id($courserinfo['courser']['categoryid']);
            if(!empty($categorystr)){
                $categorystr = ltrim($categorystr,',').','.$courserinfo['courser']['categoryid'];
                $category = explode(',',$categorystr);
            }else{
                $category = [$courserinfo['courser']['categoryid']];
            }
            foreach($category as $key=>$value){
                $list[$key] = $categorymodel->getCategoryname($value);
            }
            $data['category'] = $list;
        }else{
            $data['category'] = [];
        }

        //获取课程单元和课时
        $unitlist = $unitmodel->getUnitList($courseid);
        $unitlist_num = count($unitlist);
        //如果班级类型是一对一 不需要查询预约时间
        $lessons_num = 0;
        foreach($unitlist as $k=>$v){
            $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid']);
            $lessons_num += count($unitlist[$k]['period']);
        }
        $courserinfo['unitlist_num'] = $unitlist_num;
        $courserinfo['lessons_num'] = $lessons_num;

        //获取老师相关信息
        $teacherinfo = $teachermodel->getTeacherData($courserinfo['courser']['teacherid']);

        if(!empty($teacherinfo)){
            $teacherinfo['classnum'] = $schedumodel->getOpenClassCount( $teacherinfo['teacherid']);
            $teacherinfo['studentnum'] = $ordermodel->getOrderStudentNum($courserinfo['courser']['teacherid']);
            //计算评分
            $commentmodel = new Coursecomment;
            $score = $commentmodel->getCommentScore($teacherinfo['teacherid']);
            $teacherinfo['score']= sprintf("%.1f",$score);
        }
        $data['teacher'] = $teacherinfo;
        //返回结果集
        $data['unitlist'] = $unitlist;
        return $data;
    }
    /**
     * 查询直播课程相关信息
     * @Author yr
     * @param $courseid  int [老师id]
     * @return array
     *
     */
    public function getLiveInfo($courseid,$teacherid,$date='',$fullpeople=''){
        //实例化model
        $coursemodel = new Curriculum;
        $teachermodel = new Teacherinfo;
        $ordermodel = new Ordermanage;
        $unitmodel = new Unitdeputy;
        $lessonsmodel = new Lessons;
        $schedumodel = new Scheduling;
        $categorymodel = new Category;
        //获取课程相关信息
        $where = [
            'id' => $courseid
        ];
        $courserinfo['courser'] = $coursemodel->getCourserById($where);
        //查看是否登录
        $loginobj = new Loginbase;
        $userinfo = $loginobj->checkUserLogin();
        $studentid = $userinfo['uid'];
        /*------------------如果登录 查询是否收藏过此课程----------*/
        if($studentid == false){
            $courserinfo['is_collect'] = 0;
        }else{
            $organcollectmodel = new Classcollection;
            $collectwhere['courseid'] = $courseid;
            $collectwhere['studentid'] = $studentid;
            $field = 'id';
            $cid = $organcollectmodel->getDataInfo($collectwhere,$field);
            $courserinfo['is_collect'] = empty($cid)?0:1;
        }
        /*-----------------拼装分类导航---------------*/
        $list = array();
        if(!empty($courserinfo['courser']['categoryid'])){
            //获取课程分类
            $categorystr = $categorymodel->get_parent_id($courserinfo['courser']['categoryid']);
            if(!empty($categorystr)){
                $categorystr = ltrim($categorystr,',').','.$courserinfo['courser']['categoryid'];
                $category = explode(',',$categorystr);
            }else{
                $category = [$courserinfo['courser']['categoryid']];
            }
            foreach($category as $key=>$value){
                $list[$key] = $categorymodel->getCategoryname($value);
            }
            $courserinfo['courser']['category'] = $list;
        }else{
            $courserinfo['courser']['category'] = [];
        }
        /*--------------查询该课程下的班级信息-------------*/
        if(empty($teacherid)){
            $where = [
                's.curriculumid' => $courseid,
            ];
        }else{
            $where = [
                's.curriculumid' => $courseid,
                's.teacherid' => $teacherid
            ];
        }
        if(!empty($date)){
            $where['starttime'] = $date;
        }
        if(!empty($fullpeople)){
            $where['fullpeople'] = $fullpeople;
        }
        $scheduarr = $schedumodel->getClassByCourseid($where);
        $orderobj = new Ordermanage;
        foreach($scheduarr as $k=>$v){
            //第一条数据默认展示
            if($k==0){
                $scheduarr[$k]['isdefault'] = 1;
            }else{
                $scheduarr[$k]['isdefault'] = 0;
            }
            //招生中状态 未招生和招生中都列为以招生
            if($v['classstatus'] == 0){

                $scheduarr[$k]['classstatus'] = 1;
            }
            $scheduarr[$k]['remainpeople'] = $v['fullpeople'] - $v['realnum'];
            $scheduarr[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            $scheduarr[$k]['classtypes'] = $courserinfo['courser']['classtypes'];
            $scheduarr[$k]['unitlist'] = $unitmodel->getUnitList($v['scheduid']);
            $unitlist_num = count($scheduarr[$k]['unitlist']);
            //如果班级类型是一对一 不需要查询预约时间
            $lessons_num = 0;
            foreach($scheduarr[$k]['unitlist'] as $key=>$value){
                $scheduarr[$k]['unitlist'][$key]['period'] = $lessonsmodel->getLessonsList($value['unitid']);
                $lessons_num += count($scheduarr[$k]['unitlist'][$key]['period']);
            }
            $scheduarr[$k]['unitlist_num'] = $unitlist_num;
            $scheduarr[$k]['lessons_num'] = $lessons_num;
            //获取老师相关信息
            $teacherinfo = $teachermodel->getTeacherData($v['teacherid']);

            if(!empty($teacherinfo)){
                $teacherinfo['classnum'] = $schedumodel->getOpenClassCount( $v['teacherid']);
                $teacherinfo['studentnum'] = $ordermodel->getOrderStudentNum($v['teacherid']);
                //计算评分
                $commentmodel = new Coursecomment;
                $score = $commentmodel->getCommentScore($v['teacherid']);
                $teacherinfo['score']= sprintf("%.1f",$score);
            }
            $scheduarr[$k]['teacher'] = $teacherinfo;
            $schedustatus  = $schedumodel->getApplyStatus($v['scheduid']);
            //如果是小班课或者大班课 查看是否暂停招生 查看招生状态是否为未招生 和已招生状态
            if ($schedustatus['status'] == 1 && ($schedustatus['classstatus'] == 0 || $schedustatus['classstatus'] == 1)) {
                $count = 0;
                if($studentid !==false){

                    $count = $orderobj->isBuy($v['scheduid'],$studentid);
                }
                if($count >0){

                    $scheduarr[$k]['applystatus'] = 2;
                    $scheduarr[$k]['ordernum'] = $orderobj->getClassOrdernum($v['scheduid'],$studentid)['ordernum'];
                }else{
                    $scheduarr[$k]['applystatus'] = 1;
                }
            } else {
                //判断是否登陆 如果登陆查看是否购买过
                $scheduarr[$k]['applystatus'] = 0;
            }
        }
        //查询该班是否可以报名 applystatus 1代表可报名




        $result['classinfo'] = $scheduarr;
        $result['nav'] = $courserinfo['courser']['category'];
        $result['is_collect'] =  $courserinfo['is_collect'];
        return  $result;
    }
    /**
     * 查询课程详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid  int   课程id
     * @param   classtypes  int [机构id]  1 录播课 2直播课 录播课没有班级,直接查询
     * @return   array();
     */
    public function getCurriculumInfo($courseid,$teacherid,$date,$fullpeople)
    {
        if(!is_intnum($courseid)){
            return return_format($this->str,35000,lang('35000'));
        }
        $coursemodel = new Curriculum;
        $condition = [
            'id' => $courseid
        ];
        $courseinfo = $coursemodel->getCourserById($condition);
        if($courseinfo['classtypes'] == 1){
            //如果是录播课 查询课程相关信息
            $recorderesult = $this->getRecordeInfo($courseid);
            return return_format($recorderesult,0,lang('success'));
        }else{
            //如果是直播课 查询直播课程相关信息
            $liveresult = $this->getLiveInfo($courseid,$teacherid,$date,$fullpeople);
            /*  $date = '2018-09-05';
              $datearr = explode('-',$date);
              $cal = new \Calendar($datearr[0],$datearr[1],$datearr[2]);
              $starttime = date('Y-m-d',$cal->starttime) ;
              $endtime   = date('Y-m-d',$cal->endtime) ;
              //获取指定月的星期 和 日期数组
              $calendar = $cal->array ;
              dump($calendar);die();*/
            return return_format($liveresult,0,lang('success'));
        }
    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 1,2 余额支付在前，其他支付在后面
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     */
    public function gotoPay($studentid,$ordernum,$paytype){
        if(!in_array($paytype,$this->paymethod)){
            return return_format('',34026,lang('34026'));
        }
        if(!is_intnum($studentid)){
            return return_format('',34027,lang('param_error'));
        }
        $ordemodel = new Ordermanage;
        $orderinfo = $ordemodel->getUnpaidOrderInfo($studentid,$ordernum);
        //如果该班级已经在授课中或已经结束不能支付
        if($orderinfo['classstatus'] == 4){
            return return_format('',34028,lang('34028'));
        }
        if($orderinfo['classstatus'] == 5){
            return return_format('',34029,lang('34029'));
        }
        if($orderinfo['classstatus'] == 6){
            return return_format('',34130,lang('34130'));
        }
        if(empty($orderinfo)){
            return return_format('',34030,lang('34030'));
        }
        if($orderinfo['orderstatus'] ==10){
            return return_format('',34031,lang('34031'));
        }
        if($orderinfo['orderstatus'] ==20){
            return return_format('',34032,lang('34032'));
        }
        $amount = $orderinfo['amount'];
        $subject = $orderinfo['coursename'];
        $body = $orderinfo['coursename'];
        $studentfundmodel = new Studentfunds();
        $usablemoney = $studentfundmodel->getUserBalance($studentid)['usablemoney'];
        //根据type跳到不同的支付方式
        if(!in_array($paytype,$this->paytype)){
            return return_format('',34033,lang('34026'));
        }
        $paytype = explode(',',$paytype);
        $length = count($paytype);
        //如果length为1是单一支付方式
        if($length == 1){
            //查看此订单是否是混合支付.如果是混合支付
            if($orderinfo['balance'] !== '0.00'){
                //该笔第三方支付的价钱是订单实际价格-余额支付的部分
                $amounts = $orderinfo['amount'] *100;
                $usablemoneys = $orderinfo['balance']*100;
                $amount  = (float)($amounts - $usablemoneys)/100;
            }
            $paytype = intval($paytype[0]);
            if(!in_array($paytype,$this->paytype)){
                return return_format('',34033,lang('34026'));
            }
            switch ($paytype){
                case 1:
                    //余额支付 直接扣款
                    $orderclass = new Order;
                    $result = $orderclass->balancePay($studentid,$ordernum,$amount,$usablemoney,$paytype,$orderinfo['coursetype'],$orderinfo['usepackage']);
                    return $result;
                    break;
                case 2:
                    //微信支付
                    $wxpayobj = new Wxpay;
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;
                    $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$amount,$body,$notifyurl);
                    if($result['result_code'] !== 'SUCCESS'){
                        return return_format('',34034,lang('33031'));
                    }
                    $url = $result['code_url'];
                    $image = get_base64_qrcode($url);
                    $data['type'] = 1;
                    $data['codeurl'] = $image;
                    return  return_format($data,0,lang('success'));
                    break;
                case 3:
                    //支付宝支付
                    $alipayobj = new Alipaydeal;
                    $returnurl = "/web#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->createPayRequest($ordernum,$subject,$amount,$body,$returnurl,$notifyurl);
                    $data['data'] = $res;
                    return return_format($data,0,lang('success'));
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                case 5:
                    /*$paypalobj = new Paypal;
                    $returnurl = "/web#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $res =  $paypalobj->pcpaypal($ordernum,$subject,$amount,$body,$returnurl);
                    return return_format($res,0,lang('success'));*/
                    break;
                default:
                    return return_format('',34035,lang('34026'));
                    break;
            }
        }elseif($length == 2){
            //查看该余额是否正常
            $fundsmodel  =  new Studentfunds;
            $fundsinfo = $fundsmodel->getUserBalance($studentid);
            if($usablemoney !== '0.00'){
                $usablemoney = $fundsinfo['usablemoney'];
                if($fundsinfo['usablemoney'] !== $usablemoney){
                    return return_format('',34036,lang('34036'));
                }
            }else{
                $usablemoney = $fundsinfo['frozenmoney'];
            }
            if($fundsinfo['usablemoney']>=$amount){
                return return_format('',34037,lang('34037'));
            }
            //查看账户余额里是否有冻结的金额,如果没有
            if($usablemoney !== '0.00' && $fundsinfo['frozenmoney'] == '0.00'){
                //如果是混合支付,修改订单表balance,把余额支付的钱，放入冻结金额
                $ordermodel = new Order;
                $result = $ordermodel->delFreeze($studentid,$ordernum,$usablemoney,$type=1);
                if($result !=1){
                    return $result;
                }
            }
            $mixtype = $paytype[1];
            switch($mixtype){
                case 2:
                    //微信支付
                    $wxpayobj = new Wxpay;
                    $amounts = $amount *100;
                    $usablemoneys = $usablemoney*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;
                    $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$price,$body,$notifyurl);
                    if($result['result_code'] !== 'SUCCESS'){
                        return return_format('',34038,lang('33031'));
                    }
                    $url = $result['code_url'];
                    $image = get_base64_qrcode($url);
                    $data['type'] = 1;
                    $data['codeurl'] = $image;
                    return  return_format($data,0,lang('success'));
                    break;

                case 3:
                    //支付宝支付
                    $amounts = $amount *100;
                    $usablemoneys = $usablemoney*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $alipayobj = new Alipaydeal;
                    $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->createPayRequest($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                    $data['data'] = $res;
                    return return_format($data,0,lang('success'));
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                case 5:
                    $paypalobj = new Paypal;
                    $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $res =  $paypalobj->pcpaypal($ordernum,$subject,$amount,$body,$returnurl);
                    return return_format($res,0,lang('success'));
                default:
                    return return_format('',34039,lang('34026'));
                    break;
            }

        }else{
            return return_format('',34040,lang('param_error'));
        }

    }
    /**
     * 获取所有学生的收货地址
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [array]       studentid       学生id
     * @return   array();
     * URL:/student/User/getAddressList
     */
    public function getAddressList($studentid){
        if(!is_intnum($studentid)){
            return return_format('',39401,lang('参数studentid错误'));
        }
        $addressmodel = new Studentaddress;
        $result = $addressmodel->getAddressList($studentid);
        $citymodel = new City();
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['pname'] = $citymodel->getName($v['pid']);//省名称
                $result[$k]['cname'] = $citymodel->getName($v['cityid']);//城市名称
                $result[$k]['aname'] = $citymodel->getName($v['areaid']);//区域名称
            }
        }
        return return_format($result,0,lang('success'));

    }

}
