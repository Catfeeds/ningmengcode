<?php
namespace app\student\business;
use app\student\model\Scheduling;
use app\student\model\Teacherinfo;
use app\student\model\Ordermanage;
use app\student\model\Unit;
use app\student\model\Category;
use app\official\model\Category as OffCate;
use app\student\model\Coursetags;
use app\student\model\Lessons;
use app\student\model\Coursecomment;
use app\student\model\Regionconfig;
use app\student\model\Unitdeputy;
use app\student\model\Coursetagrelation;
use app\student\model\Organ;
use app\student\model\Classcollection;
use Login;
use app\student\model\Schedulingdeputy;
use app\student\controller\Loginbase;
use app\official\model\RecommendManage;
class WebScheduManage
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
            return return_format($this->str,35001,'参数错误');
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
            return return_format('',35100,'没有此课程信息');
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
                //如果是小班课 查看是否暂停招生 否则都是可以报名状态
                case 1:
                    if ($schedustatus['status'] == 0) {
                        $courserinfo['applystatus'] = 0;
                    } else {
                        $courserinfo['applystatus'] = 1;
                    }

                    break;
                default:
                    //如果是小班课或者大班课 查看是否暂停招生 查看招生状态是否为未招生 和已招生状态
                    if ($schedustatus['status'] == 1 && ($schedustatus['classstatus'] == 0 || $schedustatus['classstatus'] == 1)) {
                        $courserinfo['applystatus'] = 1;
                    } else {
                        $courserinfo['applystatus'] = 0;
                    }
                    break;
            }
        //返回结果集
        $courserinfo['unit'] = $unitlist;
        if(empty($courserinfo)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($courserinfo,0,'查询成功');
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
            return return_format($this->str,35003,'参数错误');
        }
        $categorymodel = new Category;
        //查询推荐的分类
        $list  = $categorymodel->getRecommendCid($organid);
        //拼装分类列表
        $schedumodel  = new Schedulingdeputy;
        foreach($list as $k=>$v){
            $list[$k]['child'] = $categorymodel->getChildList($organid,$v['category_id']);
            if(!empty($list[$k]['child'] )){
                foreach($list[$k]['child'] as $key=>$value){
                    if($key == 0){
                        $list[$k]['child'][$key]['showfilter'] = true;
                    }else{
                        $list[$k]['child'][$key]['showfilter'] = false;
                    }
                    $list[$k]['child'][$key]['data'] = $schedumodel->getCateCourserList($value['category_id']);
                }
            }else{
                $list[$k]['data'] = $schedumodel->getCateCourserList($v['category_id']);
            }
        }
        if(empty($list)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($list,0,'查询成功');
        }
    }
    /**
     * 获取排课推荐列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getScheduList($organid)
    {
        if(!is_numeric($organid) || empty($organid)){
            return return_format($this->str,35003,'参数错误');
        }
        $schedeumodel = new Scheduling;
        $courserlist = $schedeumodel->getCourserList($organid);
        if(empty($courserlist)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($courserlist,0,'查询成功');
        }
    }
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
        $scheduobj = new Schedulingdeputy;
        $result = $scheduobj->getCateCourserList($categoryid);
        if(empty($result)){
            return return_format('',0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
        }
    }
    /**
     * 按分类查询课程列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getFilterCourserList ($organid,$category_id,$pagenum,$limit)
    {
        if(!is_intnum($organid) || !is_intnum($limit)){
            return return_format($this->str,35005,'参数错误');
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $category_id= isset($category_id)?$category_id:0;
        $coursermodel = new Schedulingdeputy;
        $categorymodel = new Category;
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
        if(empty($category_id)){
            $data['schedulist'] = $coursermodel->getCourserAllList($limitstr);
          /*  $data['categorylist'] = $categorymodel->getTopList($organid);*/
            $total  = $coursermodel->getCourserAllCount($organid);
        }else{
            //如果有分类id 查询是第几级分类
            /*$data['categorylist'] = $categorymodel->getChildList($organid,$categoryids);*/
            $data['schedulist'] = $coursermodel->getWebFilterCourserList($categoryids,$limitstr);
            $total = $coursermodel->getWebFilterCourserCount($categoryids);
        }
        //分页信息
        $data['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
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
            return return_format($data,0,'没有数据');
        }else{
            return return_format($data,0,'查询成功');
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
            return return_format($this->str,36007,'参数类型错误');
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
            return return_format([],0,'没有数据');
        }else{
            return return_format($commentinfo,0,'查询成功');
        }

    }
    /**
     * 查询机构老师的信息
     * @Author yr
     * @param $organid   int [机构id]
     * @param $teacherid   int [老师id]
     * @return array
     *
     */
    public function getTeacherData($organid,$teacherid){
        //判断参数是否合法
        if(!is_intnum($organid) || !is_intnum($teacherid)){
            return return_format($this->str,35009,'参数类型错误');
        }
        //实例化模型
        $teachermodel = new Teacherinfo;
        $schedumodel = new ScheduManage;
        //查询老师信息
        $teacherinfo = $teachermodel->getTeacherData($teacherid,$organid);

    }
    /**
     *  获取点击分类的导航
     *  首页 获取信息的头部 分类导航
     *  @author wyx
     *  @param   $cateid  点击的分类id   需要获取其父级及 相邻子级（如果存在） 导航路径
     *
     *
     */
    public function getCateLeader($cateid,$department=' > '){
        $organid = 1 ;// 官方机构id 

        $infinty = [
                'id' => 0  ,
                'title' => '不限' ,
                'rank' => 0 ,
                'fid' => 0 ,
                'children'=> [],
                'selected'=> false ,
            ] ;
        // 获取机构的 分类
        $coursemodel = new OffCate;
        //交换教师 排位序号
        $resultarr =  $coursemodel->getCategoryList($organid);
        $floor1 = [] ;
        $floor2 = [] ;
        $floor3 = [] ;
        //目前处理三级 分类
        foreach ($resultarr as $val) {
            if($val['prerank']==1){// 包含 1-2 级分类
                $floor1[$val['preid']] = ['id'=>$val['preid'],'title'=>$val['prename'],'rank'=>$val['prerank'],'fid'=>$val['prefid'],'children'=>[]] ;
                if( $val['id']>0 && $val['delflag']==1 && $val['status']==1  ){ // 为删除的 显示的
                    $floor2[$val['id']]    = ['id'=>$val['id'],'title'=>$val['name'],'rank'=>$val['rank'],'fid'=>$val['fid'],'children'=>[]] ;
                }
            }else{// 2-3 级分类
                if( $val['id']>0 && $val['delflag']==1 && $val['status']==1  ){ // 为删除的 显示的
                    $floor3[$val['id']]    = ['id'=>$val['id'],'title'=>$val['name'],'rank'=>$val['rank'],'fid'=>$val['fid']] ;
                }
            }
        }
        //做一次查询 
        $catemodel = new Category;
        $linecate = $catemodel->getCategoryBycid($cateid);

        if( !empty($linecate['rank']) ){
            $cateids = empty($linecate['path']) ? [] : explode( '-',$linecate['path'] ) ;
            if( isset($linecate['rank']) && $linecate['rank'] == 1 ){//选中一级情况
                //选中 一级分类 并将二级设置为不限
                $floor1[$cateid]['selected'] = true ;
                $leader = $floor1[$cateid]['title'] ;

                //插入不限且选中
                $infinty['selected'] = true ;
                $infinty['id'] = $cateid ;
                array_push($floor1[$cateid]['children'],$infinty);

                // 将指定二级 插入
                foreach ($floor2 as $val) {
                    if( isset($floor1[$val['fid']]) && $cateid==$val['fid'] ){
                        array_push($floor1[$cateid]['children'],$val);
                    }
                }
                //如果合并后 数据仅1条 则清空
                if( count($floor1[$cateid]['children']) == 1 ) $floor1[$cateid]['children'] = [] ;


                $floor1 = $this->dealArrayStruct($floor1);

                return return_format(['catetree'=>$floor1,'leader'=>$leader],0,'OK') ;

            }elseif( isset($linecate['rank']) && $linecate['rank'] == 2 ){//选中二级情况
                $cateid1 = $cateids[0] ;//一层 选中的id

                $floor1[$cateid1]['selected'] = true ;
                $leader[1] = $floor1[$cateid1]['title'] ;


                //插入三级不限 将指定三级并入 插入不限且选中
                $infinty['selected'] = true ;
                $infinty['id'] = $cateid ;
                array_push($floor2[$cateid]['children'],$infinty);
                //将 数组三合并到 2
                foreach ($floor3 as $val) {
                    if( isset($floor2[$val['fid']]) && $cateid==$val['fid'] ){
                        array_push($floor2[$cateid]['children'],$val);
                    }
                }
                //如果合并后 数据仅1条 则清空
                if( count($floor2[$cateid]['children']) == 1 ) $floor2[$cateid]['children'] = [] ;
                //插入二级不限 将指定二级并入 插入不限
                $infinty['selected'] = false ;
                $infinty['id'] = $cateid1 ;
                array_push($floor1[$cateid1]['children'],$infinty);
                foreach ($floor2 as $val) {
                    if( isset($floor1[$val['fid']]) && $cateid1==$val['fid'] ){
                        if($cateid==$val['id']){
                            $val['selected'] = true ;//选中二级分类
                            $leader[2] = $val['title'] ;// 导航拼接
                        } 
                        array_push($floor1[$cateid1]['children'],$val);
                    }
                }
                ksort($leader);
                $floor1 = $this->dealArrayStruct($floor1);
                
                return return_format(['catetree'=>$floor1,'leader'=>implode($department, $leader)],0,'OK') ;
            }else{// 选中三级情况
                $cateid1 = $cateids[0] ;//一层 选中的id
                $cateid2 = $cateids[1] ;//二层 选中的id

                $floor1[$cateid1]['selected'] = true ;
                $leader[1] = $floor1[$cateid1]['title'] ;

                //插入三级不限 将指定三级并入 插入不限且选中
                $infinty['selected'] = false ;
                $infinty['id'] = $cateid2 ;
                array_push($floor2[$cateid2]['children'],$infinty);
                //将 数组三合并到 2
                foreach ($floor3 as $val) {
                    if( isset($floor2[$val['fid']]) && $cateid2==$val['fid'] ){
                        if($cateid==$val['id']){
                            $val['selected'] = true ;//选中二级分类
                            $leader[3] = $val['title'] ;// 导航拼接
                        } 
                        array_push($floor2[$cateid2]['children'],$val);
                    }
                }
                //如果合并后 数据仅1条 则清空
                if( count($floor2[$cateid2]['children']) == 1 ) $floor2[$cateid]['children'] = [] ;
                //插入二级不限 将指定二级并入 插入不限
                $infinty['selected'] = false ;
                $infinty['id'] = $cateid1 ;
                array_push($floor1[$cateid1]['children'],$infinty);
                foreach ($floor2 as $val) {
                    if( isset($floor1[$val['fid']]) && $cateid1==$val['fid'] ){
                        if($cateid2==$val['id']){
                            $val['selected'] = true ;//选中二级分类
                            $leader[2] = $val['title'] ;// 导航拼接
                        } 
                        array_push($floor1[$cateid1]['children'],$val);
                    }
                }
                
                ksort($leader);
                $floor1 = $this->dealArrayStruct($floor1);
                return return_format(['catetree'=>$floor1,'leader'=>implode($department, $leader)],0,'查询成功') ;
            }

        }else{
            $floor1 = $this->dealArrayStruct($floor1);
            return return_format(['catetree'=>$floor1,'leader'=>''],0,'分类不存在') ;
        }
       

    }
    /**
     *  将数据改为数字下标连续
     *
     *
     */
    public function dealArrayStruct($floor1){
        $floor1 = array_values($floor1);
        foreach ($floor1 as &$value) {
            if(!empty($value['children'])){
                $value['children'] = array_values($value['children']);
                foreach ($value['children'] as &$val) {
                    if(!empty($val['children'])){
                        $val['children'] = array_values($val['children']);
                    }
                }
            }
        }
        return $floor1;
    }
    
}
