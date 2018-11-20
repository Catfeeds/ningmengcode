<?php
namespace app\appstudent\business;
use app\student\model\Scheduling;
use app\student\model\Teacherinfo;
use app\student\model\Ordermanage;
use app\student\model\Unit;
use app\student\model\Category;
use app\student\model\Coursetags;
use app\student\model\Lessons;
use app\student\model\Coursecomment;
use app\student\model\Regionconfig;
use app\student\model\Unitdeputy;
use app\student\model\Coursetagrelation;
use app\student\model\Organ;
use app\student\model\Classcollection;
use Login;
use app\student\controller\Loginbase;
class AppScheduManage
{
    protected $str;
    protected $foo;
    protected $array;
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
        //初始化课程类型
        $this->array = [1=>'一对一',2=>'小课班',3=>'大课班'];
    }
    /**
     * 获取指定id的排课详情
     * @Author yr
     * @param scheduid   int [排课id]  必选
     * @param organid  int [机构id]  必选
     * @param studentid int [学生id]  必选
     * @param type int [类型id]  判断是个人中心的排课详情还是首页的 可选
     * @return array
     *
     */
    public function getScheduOne($scheduid)
    {
        if(!is_intnum($scheduid)){
            return return_format($this->str,35000,lang('35000'));
        }
        //实例化model
        $coursermodel = new Scheduling;
        $teachermodel = new Teacherinfo;
        $ordermodel = new Ordermanage;
        $unitmodel = new Unitdeputy;
        $lessonsmodel = new Lessons;
        $schedumodel = new Scheduling;
        $tagsmodel = new Coursetagrelation;
        $categorymodel = new Category;
        $organmodel = new Organ;
        //获取课程相关信息
        $courserinfo['courser'] = $coursermodel->getCourserById($scheduid);
        $organid = $courserinfo['courser']['organid'];
        if(empty($courserinfo['courser'])){
            return return_format('',35001,lang('35001'));
        }
        //查看该班级详情的机构id是否是免费的
        $organinfo = $organmodel->getArrByid($organid);

        if($organinfo['vip'] == '0'){
            $courserinfo['organinfo'] = $organinfo;
        }else {
            //获取课程标签
            $courserinfo['courser']['tags'] = $tagsmodel->getArrId($courserinfo['courser']['curriculumid'], $organid);
        }
        //查看是否登录
        $loginobj = new Loginbase;
        $userinfo = $loginobj->checkUserLogin();
        $studentid = $userinfo['uid'];
        if($studentid == false){
            $courserinfo['is_collect'] = 0;
        }else{
            $organcollectmodel = new Classcollection;
            $where['schedulingid'] = $scheduid;
            $where['studentid'] = $studentid;
            $field = 'id';
            $cid = $organcollectmodel->getDataInfo($where,$field);
            $courserinfo['is_collect'] = empty($cid)?0:1;
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
                $list[$key] = $categorymodel->getCategoryname($value,$organid);
            }
            $courserinfo['courser']['category'] = $list;
        }else{
            $courserinfo['courser']['category'] = [];
        }

        //获取课程单元和课时
        $unitlist = $unitmodel->getUnitList($scheduid,$organid);
        $unitlist_num = count($unitlist);
        //如果班级类型是一对一 不需要查询预约时间
        $lessons_num = 0;
        foreach($unitlist as $k=>$v){
            $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid'],$organid,$courserinfo['courser']['type']);
            $lessons_num += count($unitlist[$k]['period']);
            if($courserinfo['courser']['type'] !==1){
                foreach( $unitlist[$k]['period'] as $key=>$value){
                    if(!empty($unitlist[$k]['period'][$key]['timekey'])){
                        $unitlist[$k]['period'][$key]['timekey']  = explode(',',$value['timekey']);
                        $unitlist[$k]['period'][$key]['time'] = get_time_key($unitlist[$k]['period'][$key]['timekey'][0]);
                    }else{
                        $unitlist[$k]['period'][$key]['time'] = '';
                    }

                }
            }
        }
        $courserinfo['unitlist_num'] = $unitlist_num;
        $courserinfo['lessons_num'] = $lessons_num;
        //获取老师相关信息
        $teacherinfo = $teachermodel->getTeacherData($courserinfo['courser']['teacherid']);
        if(!empty($teacherinfo)){
            $teacherinfo['classnum'] = $schedumodel->getOpenClassCount( $teacherinfo['teacherid'],$organid);
            $teacherinfo['studentnum'] = $ordermodel->getOrderStudentNum($courserinfo['courser']['teacherid'],$organid);
            //计算评分
            $commentmodel = new Coursecomment;
            $score = $commentmodel->getCommentScore($teacherinfo['teacherid'],$organid);
            $teacherinfo['score']= sprintf("%.1f",$score);
        }else{
            $teacherinfo = [];
        }

        $courserinfo['teacher'] = $teacherinfo;
        //查询该班是否可以报名 applystatus 1代表可报名
        $schedumodel = new Scheduling;
        $schedustatus  = $schedumodel->getApplyStatus($scheduid,$organid);
        switch($courserinfo['courser']['type']) {
            //如果是小班课 查看是否暂停招生 否则都是可以报名状态 报名状态applystatus：0暂停报名1立即报名2已报名
            case 1:
                //一对一课程
                if ($schedustatus['status'] == 0) {
                    $courserinfo['applystatus'] = 0;
                } else {
                    $courserinfo['applystatus'] = 1;
                }

                break;
            default:
                //如果是小班课或者大班课 查看是否暂停招生 查看招生状态是否为未招生 和已招生状态
                if ($schedustatus['status'] == 1 && ($schedustatus['classstatus'] == 0 || $schedustatus['classstatus'] == 1)) {
                    $count = 0;
                    if($studentid !==false){
                        $orderobj = new Ordermanage;
                        $count = $orderobj->isBuy($scheduid,$studentid);
                    }
                    if($count >0){
                        $courserinfo['applystatus'] = 2;
                    }else{
                        $courserinfo['applystatus'] = 1;
                    }
                } else {
                    //判断是否登陆 如果登陆查看是否购买过
                    $courserinfo['applystatus'] = 0;
                }
                break;
        }
        //返回结果集
        $courserinfo['unit'] = $unitlist;
        if(empty($courserinfo)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($courserinfo,0,lang('success'));
        }
    }
    /**
     * 获取一，二级分类及列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getCategoryOrList($organid)
    {
        if(!is_numeric($organid) || empty($organid)){
            return return_format($this->str,35002,lang('35002'));
        }
        $categorymodel = new Category;
        $result  = $categorymodel->getTopAndChildList($organid);
        //拼装分类列表
        $list =  generateTree($result,'category_id');
        $schedumodel  = new Scheduling;
        foreach($list as $k=>$v){
            if(!empty($list[$k]['child'] )){
                foreach($list[$k]['child'] as $key=>$value){
                    if($key == 0){
                        $list[$k]['child'][$key]['showfilter'] = true;
                    }else{
                        $list[$k]['child'][$key]['showfilter'] = false;
                    }
                    $list[$k]['child'][$key]['data'] = $schedumodel->getCateCourserList($organid,$value['category_id']);
                }
            }else{
                $list[$k]['data'] = $schedumodel->getCateCourserList($organid,$v['category_id']);
            }
        }
        if(empty($list)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($list,0,lang('success'));
        }
    }
//    /**
//     * 获取排课推荐列表
//     * @Author yr
//     * @param $organid   int [机构id]
//     * @return array
//     *
//     */
//    public function getScheduList($organid)
//    {
//        if(!is_numeric($organid) || empty($organid)){
//            return return_format($this->str,35003,lang('param_error'));
//        }
//        $schedeumodel = new Scheduling;
//        $courserlist = $schedeumodel->getCourserList($organid);
//        if(empty($courserlist)){
//            return return_format($courserlist,0,lang('success'));
//        }else{
//            return return_format($courserlist,0,lang('success'));
//        }
//    }
    /**
     * 获取指定分类下排课推荐列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getCategorySchedu($organid,$categoryid){
        if(!is_intnum($organid) || !is_intnum($categoryid)){
            return return_format($this->str,35005,'参数错误');
        }
        $scheduobj = new Scheduling;
        $result = $scheduobj->getCateCourserList($organid,$categoryid);
        if(empty($result)){
            return return_format('',0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
        }
    }
    /**
     * 按分类和标签查询课程列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getFilterCourserList ($organid,$category_id,$tagid,$pagenum,$limit)
    {
        if(!is_intnum($organid) || !is_intnum($limit)){
            return return_format($this->str,35004,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $tagid = isset($tagid)?$tagid:0;
        $tagid = rtrim($tagid,',');
        $category_id= isset($category_id)?$category_id:0;
        $coursermodel = new Scheduling;
        $categorymodel = new Category;
        $tagmodel = new Coursetags;
        //如果分类id为空，并且标签id为空,默认显示全部
        //拆分分类id
        $array = explode(',',$category_id);
        if(count($array)>1){
            $categoryids = $category_id;
            $categoryarr = $categorymodel->getRank($organid,$array[0]);
            $category_id = $categoryarr['fatherid'];
        }else{
            $categorylist = $categorymodel->get_category($category_id);
            $categoryids = rtrim($categorylist,',');
        }
        //查询出该分类等级
        $info = $categorymodel->getRank($organid,$categoryids);
        if(empty($categoryids) && empty($tagid)){
            $data['schedulist'] = $coursermodel->getCourserAllList($organid,$limitstr);
            $data['categorylist'] = $categorymodel->getTopList($organid);
            $total  = $coursermodel->getCourserAllCount($organid);
        }elseif($categoryids){
            //如果有分类id 查询是第几级分类
            $data['categorylist'] = $categorymodel->getChildList($organid,$categoryids);
            if(empty($data['categorylist'])){
                //如果是一级分类 不展示
                if($info['rank'] == 1){
                    //如果是一级分类 且没有下级 设置flag
                    $flag = 1;
                }else{
                    $data['categorylist'] = $categorymodel->getChildList($organid,$info['fatherid']);
                }
            }
            $data['schedulist'] = $coursermodel->getFilterCourserList($organid,$categoryids,$tagid,$limitstr);
            $total = $coursermodel->getFilterCourserCount($organid,$categoryids,$tagid);
        }else{
            //如果没有分类id
            $data['categorylist'] = $categorymodel->getTopList($organid);
            $data['schedulist'] = $coursermodel->getFilterCourserList($organid,$categoryids,$tagid,$limitstr);
            $total = $coursermodel->getFilterCourserCount($organid,$categoryids,$tagid);
        }
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        //拼接分类不限
        $catestr = '';
        $flag = isset($flag)?$flag:0;
        if($flag == 1){
            $catestr = $category_id;
            $data['categorylist'] = [];
        }else{
            foreach($data['categorylist'] as $key=>$value){

            $catestr.=$value['category_id'].',';
            }
        }
        $catearr = [
            'category_id' => rtrim($catestr,','),
            'categoryname' => '不限'
        ];
        array_unshift($data['categorylist'],$catearr);
        //获取标签
        $data['taglist'] = $tagmodel->getTags($organid);
        $data['taglist'] = generateTree($data['taglist']);
        foreach($data['taglist'] as $k=>$v){
            if(empty($v['child'])){
                unset($data['taglist'][$k]);
            }

        }
        //拼接标签不限
        $data['taglist'] = array_values($data['taglist']);
        $newstr = '';
        $newarr = [];
        foreach($data['taglist'] as $key=>$value){
            foreach($value['child'] as $k=>$v){
                $newstr.=$v['tagid'].',';
                $newarr = [
                    'tagid'=> $newstr,
                    'tagname' => '不限'
                ];
            }
            $newstr = '';
            array_unshift($data['taglist'][$key]['child'],$newarr);
        }
        //获取导航分类
        $categorystr = $categorymodel->get_parent_id($category_id);
        if(empty($categorystr)){
            $categorystr = $category_id;
        }else{
            $categorystr =ltrim($categorystr,',').','.$category_id;
        }
        $category = explode(',',ltrim($categorystr,','));
        foreach($category as $key=>$value){
            $category[$key] = $categorymodel->getArrId($value,$organid);
        }
        $data['nav_category'] = $category;
        if(empty($data['schedulist'])){
            return return_format($data,0,lang('success'));
        }else{
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 查询课程评论
     * @Author yr
     * @param $organid   int [机构id]
     * @param $teacherid   int [老师id]
     * @return array
     *
     */
    public function getCommentData($curriculumid,$organid,$pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($organid) || !is_intnum($curriculumid) || !is_intnum($limit)){
            return return_format($this->str,35008,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        //实例化模型
        $commentmodel = new Coursecomment;
        //查询评论信息
        $commentinfo['data'] = $commentmodel->getCommentListBycid($curriculumid,$organid,$limitstr);
        $total = $commentmodel->getCommentCountBycid($curriculumid,$organid);
        $commentinfo['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        if(empty($commentinfo)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($commentinfo,0,lang('success'));
        }

    }
}
