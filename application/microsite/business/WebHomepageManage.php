<?php
namespace app\student\business;
use app\student\model\Organcollection;
use app\student\model\Organslideimg;
use app\student\model\Scheduling;
use app\student\model\City;
use app\student\model\Category;
use app\student\model\Officialslideimg;
use app\student\model\Schedulingdeputy;
use app\student\model\Organ;
use app\student\model\Teacherinfo;
use app\student\model\Teachertagrelate;
use Login;

class WebHomepageManage
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
            return return_format($info,0,'查询成功');
        }else{
            return return_format([],0,'没有数据');
        }
    }
    /**
     * 获取首页分类信息
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getCategoryList($organid){
        $catemodel  = new Category;
        $result = $catemodel->getCategory($organid);
        //拼装树状结构
        $list = generateTree($result,'category_id');
        if(empty($list)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($list,0,'查询成功');
        }

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
            return return_format([],0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
        }

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
            return return_format($this->str,31002,'参数错误');
        }
        $coursermodel = new Scheduling;
        $teacherlist = $coursermodel->getCourserList($organid);
        if(empty($teacherlist)){
            return return_format([],0,'没有数据');
        }else{
            foreach($teacherlist as $k=>$v){
                $teacherlist[$k]['typename'] = $this->array[$v['type']];
            }
            return return_format($teacherlist,0,'查询成功');
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
        return return_format($res,0,'查询成功');
    }
    /**
     * 获取首页轮播列表
     * @Author yr
     * @param parentid int 可选
     * @DateTime 2018-04-21T16:20:19+0800
     * @return   array();
     */
    public function getSlideList($organid)
    {
        if(!is_intnum($organid)){
            return return_format('',31004,'参数错误');
        }
        $class = new Organslideimg;
        $res = $class->getSlideList($organid);
        if(empty($res)){
            return return_format([],0,'没有数据');
        }
            return return_format($res,0,'查询成功');
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
            return return_format('',33105,'参数错误');
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
                return return_format('',33108,'参数错误');
        }
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $data['arr'] = $coursearr;
        if(empty($coursearr)){
            return return_format($data,0,'没有数据');
        }else{
            return return_format($data,0,'查询成功');
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
            return return_format('',33109,'参数错误');
        }
        $result = $organobj->getRecommendOrgan($limit);
        if(empty($result)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
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
            return return_format('',33112,'参数错误');
        }
        $organobj  = new Organ;
        $result = $organobj->getArrByid($organid);
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
            return return_format([],0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
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
            return return_format('',33113,'参数错误');
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
            return return_format($data,0,'没有数据');
        }else{
            return return_format($data,0,'查询成功');
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
            return return_format('',33113,'参数错误');
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
            return return_format($data,0,'没有数据');
        }else{
            return return_format($data,0,'查询成功');
        }
    }
}
