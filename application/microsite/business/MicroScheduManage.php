<?php
namespace app\microsite\business;
use app\student\model\Coursegift;
use app\student\model\Period;
use app\student\model\Scheduling;
use app\student\model\Studentaddress;
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
use app\student\controller\Loginbase;
use app\official\model\Category as OffCate;
use app\student\model\Curriculum;
use Login;
class MicroScheduManage
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
        if(empty($courserinfo['courser'])){
            return return_format('',35001,lang('35001'));
        }
        //查看该班级详情的机构id是否是免费的
        $organinfo = $organmodel->getArrByid();

        if($organinfo['vip'] == '0'){
            $courserinfo['organinfo'] = $organinfo;
        }else {
            //获取课程标签
            $courserinfo['courser']['tags'] = $tagsmodel->getArrId($courserinfo['courser']['curriculumid']);
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
                $list[$key] = $categorymodel->getCategoryname($value);
            }
            $courserinfo['courser']['category'] = $list;
        }else{
            $courserinfo['courser']['category'] = [];
        }

        //获取课程单元和课时
        $unitlist = $unitmodel->getUnitList($scheduid);
        $unitlist_num = count($unitlist);
        //如果班级类型是一对一 不需要查询预约时间
        $lessons_num = 0;
        foreach($unitlist as $k=>$v){
            $unitlist[$k]['period'] = $lessonsmodel->getLessonsList($v['unitid'],$courserinfo['courser']['type']);
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
                $teacherinfo['classnum'] = $schedumodel->getOpenClassCount( $teacherinfo['teacherid']);
                $teacherinfo['studentnum'] = $ordermodel->getOrderStudentNum($courserinfo['courser']['teacherid']);
                //计算评分
                $commentmodel = new Coursecomment;
                $score = $commentmodel->getCommentScore($teacherinfo['teacherid']);
                $teacherinfo['score']= sprintf("%.1f",$score);
            }else{
                $teacherinfo = [];
            }

            $courserinfo['teacher'] = $teacherinfo;
            //查询该班是否可以报名 applystatus 1代表可报名
            $schedumodel = new Scheduling;
            $schedustatus  = $schedumodel->getApplyStatus($scheduid);
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
     * @return array
     *
     */
    public function getCategoryOrList()
    {

        $categorymodel = new Category;
        $result  = $categorymodel->getTopAndChildList();
        //拼装分类列表
        $list =  generateTree($result,'category_id');
        $schedumodel  = new Scheduling;
        //查询出所有的一二级分类和二级分类下的课程数量
        foreach($list as $k=>$v){
            if(!empty($list[$k]['child'] )){
                foreach($list[$k]['child'] as $key=>$value){
                    if($key == 0){
                        $list[$k]['child'][$key]['showfilter'] = true;
                    }else{
                        $list[$k]['child'][$key]['showfilter'] = false;
                    }
                    $categorylist = $categorymodel->get_category($value['category_id']);
                    $categoryids = rtrim($categorylist,',');
                    $list[$k]['child'][$key]['length'] = $schedumodel->getCateCourserCount($categoryids);
                }
                //对数组进行排序
                array_multisort(array_column($list[$k]['child'],'length'),SORT_DESC,$list[$k]['child']);
            }
        }
        //对课程数量排序 取出三个最多分类
        foreach($list as $k=>$v){
            if(!empty($list[$k]['child'] )){
                foreach($list[$k]['child'] as $key=>$value){
                    if($key >2){
                        unset($list[$k]['child'][$key]);
                    }
                }
            }
        }
        if(empty($list)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($list,0,lang('success'));
        }
    }
    /**
     * 获取排课推荐列表
     * @Author yr
     * @return array
     *
     */
    public function getScheduList()
    {

        $schedeumodel = new Scheduling;
        $courserlist = $schedeumodel->getCourserList();
        if(empty($courserlist)){
            return return_format($courserlist,0,lang('success'));
        }else{
            return return_format($courserlist,0,lang('success'));
        }
    }
    /**
     * 获取指定分类下排课推荐列表
     * @Author yr
     * @return array
     *
     */
    public function getCategorySchedu($categoryid){

        $categorymodel = new Category;
        $categorylist = $categorymodel->get_category($categoryid);
        if(empty($categorylist)){
            $categoryids = $categoryid;
        }else{
            $categoryids = rtrim($categorylist,',');
        }
        $scheduobj = new Scheduling;
        $result = $scheduobj->getCateCourserList($categoryids);
        if(empty($result)){
            return return_format('',0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 按分类和标签查询课程列表
     * @Author yr
     * @return array
     *
     */
    public function getFilterCourserList ($category_id,$is_free,$pagenum,$limit,$coursetype)
    {
        if( !is_intnum($limit)){
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
        $coursermodel = new Curriculum();
        $categorymodel = new Category;
        //如果分类id为空，并且标签id为空,默认显示全部
        //拆分分类id
        $array = explode(',',$category_id);
        if(count($array)>1){
            $categoryids = $category_id;
            $categoryarr = $categorymodel->getRank($array[0]);
            $category_id = $categoryarr['fatherid'];
        }else{
            $categorylist = $categorymodel->get_category($category_id);
            $categoryids = rtrim($categorylist,',');
        }
        if(empty($category_id)){
            $data['schedulist'] = $coursermodel->getCourserAllList($is_free,$limitstr,$coursetype);
            $total  = $coursermodel->getCourserAllCount($is_free,$coursetype);
        }else{
            //如果有分类id 查询是第几级分类
            $data['schedulist'] = $coursermodel->getFilterCourserList($is_free,$categoryids,$limitstr,$coursetype);
            $total = $coursermodel->getFilterCourserCount($is_free,$categoryids,$coursetype);
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
            $category[$key] = $categorymodel->getArrId($value);
        }
        $data['nav_category'] = $category;
        if(empty($data['schedulist'])){
            return return_format($data,0,'没有数据');
        }else{
            return return_format($data,0,'查询成功');
        }
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
            return return_format($liveresult,0,lang('success'));
        }
    }
    /**
     * 查询课程详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid  int   课程id
     * @return   array();
     */
    public function getCurriculumDateList($courseid)
    {
        if(!is_intnum($courseid)){
            return return_format($this->str,35000,lang('35000'));
        }
        $model = new Scheduling();
        $where = [
            's.curriculumid' => $courseid
        ];
        $result = $model->getClassByCourseGroup($where);
        $datearray = [];
        foreach($result as $k=>$v){
            $datearray[$v['starttime']][] = $v;
        }
        $newarray = [];

        foreach($datearray as $k=>$v){
            $newarray[$k]['date'] = $k;
            $wherearr = [
                's.curriculumid' => $courseid,
                's.starttime' => $k,
            ];
            $newarray[$k]['class'] = $model->getClassGroup($wherearr);
        }
        $arr = array_values($newarray);
        return return_format($arr,0,lang('success'));
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

                    $count = $orderobj->isStudentBuy($v['scheduid'],$studentid);
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
        $coursemodel = new Coursegift;
        if($courserinfo['courser']['giftstatus'] == 0){
            $giftdescribe = json_decode($courserinfo['courser']['giftjson'],true);
            foreach($giftdescribe as $k=>$v){
                $where = ['id'=>$v['id']];
                $field = ['name'];
                $giftdescribe[$k]['name'] =  $coursemodel->getField($where,$field)['name'];
            }
            $result['giftdescribe'] =  $giftdescribe;
        }
        $result['giftstatus'] = $courserinfo['courser']['giftstatus'];
        $result['classinfo'] = $scheduarr;
        $result['nav'] = $courserinfo['courser']['category'];
        $result['is_collect'] =  $courserinfo['is_collect'];
        return  $result;
    }
	
    /**
     * 查询课程评论
     * @Author yr
     * @param $teacherid   int [老师id]
     * @return array
     *
     */
    public function getCommentData($curriculumid,$pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($curriculumid) || !is_intnum($limit)){
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
        $commentinfo['data'] = $commentmodel->getCommentListBycid($curriculumid,$limitstr);
        $total = $commentmodel->getCommentCountBycid($curriculumid);
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
