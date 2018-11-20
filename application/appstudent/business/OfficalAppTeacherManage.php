<?php
namespace app\appstudent\business;
use app\student\model\Organ;
use app\student\model\Schedulingdeputy;
use app\student\model\Teacherinfo;
use app\student\model\Ordermanage;
use app\student\model\TeacherLable;
use app\student\model\Teachertagrelate;
use app\student\model\Coursecomment;
use app\student\model\Scheduling;
use app\student\model\Teachercollection;
use Login;
class OfficalAppTeacherManage
{
    protected  $foo;
    protected  $str;
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
    }
    /**
     * 获取老师推荐列表
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getTeacherList($organid)
    {

        if(!is_numeric($organid) || empty($organid)){
            return return_format($this->str,36000,'参数错误');
        }
        $teachermodel = new Teacherinfo;
        $teacherlist = $teachermodel->getRecommendList($organid);
        if(empty($teacherlist)){
            return return_format([],0,'没有数据');
        }else{
            $ordermanage = new Ordermanage;
            foreach($teacherlist as $k=>$v){
                $teacherlist[$k]['student_num'] = $ordermanage->getOrderStudentNum($v['teacherid']);
            }
            return return_format($teacherlist,0,'查询成功');
        }
    }
    /**
     * 获取老师详情
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getTeacherData($teacherid)
    {
        if(!is_intnum($teacherid) || empty($teacherid)){
            return return_format($this->str,36002,'参数错误');
        }
        $techmodel = new Teacherinfo;
        //返回教师基本信息
        $baseinfo = $techmodel->getTeacherData($teacherid);
        if(empty($baseinfo)){
            return return_format([],0,'没有数据');
        }
        $organid = $baseinfo['organid'];
        //查看是否是官方机构请求接口
        $organmodel = new Organ;
        $organinfo = $organmodel->getArrByid($organid);
        if($organinfo['vip'] == '0'){
            //查询机构信息
           /* $baseinfo['organinfo'] = $organinfo;*/
            //查看是否收藏
            $loginobj = new Login;
            $studentid = $loginobj->checkIsLogin(1);
            if($studentid == false){
                $baseinfo['is_collect'] = 0;
            }else{
                $collectmodel = new Teachercollection;
                $where['teacherid'] = $teacherid;
                $where['studentid'] = $studentid;
                $field = 'id';
                $cid =  $collectmodel->getDataInfo($where,$field);
                $baseinfo['is_collect'] = empty($cid)?0:1;
            }
        }
        //计算年龄
        $birthmonth = date('Y',$baseinfo['birth']);
        $nowtimemonth  = date('Y',time());
        $year =  $nowtimemonth - $birthmonth;
        if($year >0){
            $baseinfo['age'] = $year;
        }else{
            $baseinfo['age'] = 1;
        }
        //查询老师的开课数量
        $schedumodel = new Scheduling;
        $baseinfo['classnum'] = $schedumodel->getOpenClassCount($teacherid,$organid);
        //查询学生数量
        $ordermodel = new Ordermanage;
        $student = $ordermodel->getOrderStudentNum($teacherid);
        $baseinfo['student'] = $student;
        //计算评分
        $commentmodel = new Coursecomment;
        $score = $commentmodel->getCommentScore($teacherid,$organid);
        $score = sprintf("%.1f",$score);
        $baseinfo['score'] = $score;
        //获取教师拥有的标签
        $lablerelate = new Teachertagrelate;
        $lablearr = $lablerelate->getTeacherLable($teacherid,$organid);
        $baseinfo['lable'] = $lablearr;
        return return_format($baseinfo,0,'查询成功');
    }
    /**
     * 获取老师的班级
     * @Author yr
     * @param $organid   int [机构id]
     * @return array
     *
     */
    public function getTeacherClass($teacherid)
    {
        if(!is_intnum($teacherid) || empty($teacherid)){
            return return_format($this->str,36004,'参数错误');
        }
        //获取老师所属机构
        $teachermodel = new Teacherinfo;
        $field = 'organid';
        $organinfo = $teachermodel->getTeacherId($teacherid,$field);
        //课程信息
        $classobj = new Schedulingdeputy;
        $classinfo['data'] = $classobj->getTeacherList($teacherid,$organinfo['organid']);
        $classinfo['classnum'] = count($classinfo['data']);
        if( $classinfo['classnum'] == 0){
            return return_format([],0,'没有数据');
        }else{
            return return_format($classinfo,0,'查询成功');
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
    public function getCommentData($teacherid,$pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($teacherid) || !is_intnum($limit)){
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
        //获取老师所属机构
        $teachermodel = new Teacherinfo;
        $field = 'organid';
        $organinfo = $teachermodel->getTeacherId($teacherid,$field);
        $organid = $organinfo['organid'];
        $commentinfo['data'] = $commentmodel->getCommentList($teacherid,$organid,$limitstr);
        $total = $commentmodel->getCommentCount($teacherid,$organid);
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
     * [dealArray 将数组合并分层]
     * @Author wangwy
     * @DateTime 2018-04-19T14:08:12+0800
     * @param    [array]    $arr    [description]
     * @param    [string]   $father [description]
     * @return   [array]            [description]
     */
    public function dealArray($arr,$father){
        if(empty($arr)) return [] ;

        $temp  = [] ;
        $child = [] ;
        foreach ($arr as $key => $val) {
            if($val[$father]==0){
                $temp[$val['id']] = ['name'=>$val['tagname']];
            }else{
                $child[$val[$father]][] = $val ;
            }
        }
        foreach ($temp as $key => &$val) {
            $val['list'] =  isset($child[$key]) ? $child[$key] : [] ;
        }
        return $temp ;
    }
}
