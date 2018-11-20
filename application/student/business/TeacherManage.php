<?php
namespace app\student\business;
use app\student\model\Curriculum;
use app\student\model\Organ;
use app\student\model\Teacherinfo;
use app\student\model\Ordermanage;
use app\student\model\TeacherLable;
use app\student\model\Teachertagrelate;
use app\student\model\Coursecomment;
use app\student\model\Scheduling;
use app\student\model\Teachercollection;
use Login;
use app\student\controller\Loginbase;
class TeacherManage
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
     * @return array
     *
     */
    public function getTeacherList()
    {

        $teachermodel = new Teacherinfo;
        $teacherlist = $teachermodel->getRecommendList();
        if(empty($teacherlist)){
            return return_format($teacherlist,0,lang('success'));
        }else{
            $ordermanage = new Ordermanage;
            foreach($teacherlist as $k=>$v){
                $teacherlist[$k]['student_num'] = $ordermanage->getOrderStudentNum($v['teacherid']);
            }
            return return_format($teacherlist,0,'success');
        }
    }
    /**
     * 获取老师详情
     * @Author yr
     * @return array
     *
     */
    public function getTeacherData($teacherid)
    {
        if(!is_intnum($teacherid) || empty($teacherid)){
            return return_format($this->str,36001,lang('param_error'));
        }
        $techmodel = new Teacherinfo;
        //返回教师基本信息
        $baseinfo = $techmodel->getTeacherData($teacherid);
        if(empty($baseinfo)){
            return return_format([],39000,lang('36002'));
        }
            //查看是否收藏
            //查看是否登录
            $loginobj = new Loginbase;
            $userinfo = $loginobj->checkUserLogin();
            $studentid = $userinfo['uid'];
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
        //计算年龄
        $birthmonth = date('Y',$baseinfo['birth']);
        if(empty($birthmonth)){
            $baseinfo['age'] = 0;
        }else{
            $nowtimemonth  = date('Y',time());
            $year =  $nowtimemonth - $birthmonth;
            if($year >0){
                $baseinfo['age'] = $year;
            }else{
                $baseinfo['age'] = 1;
            }
        }
        //查询老师的开课数量
        $schedumodel = new Scheduling;
        $baseinfo['classnum'] = $schedumodel->getOpenClassCount($teacherid);
        //查询学生数量
        $ordermodel = new Ordermanage;
        $student = $ordermodel->getOrderStudentNum($teacherid);
        $baseinfo['student'] = $student;
        //计算评分
        $commentmodel = new Coursecomment;
        $score = $commentmodel->getCommentScore($teacherid);
        $score = sprintf("%.1f",$score);
        $baseinfo['score'] = $score;
        return return_format($baseinfo,0,lang('success'));
    }
    /**
     * 获取老师的班级
     * @Author yr
     * @param    teacherid int   老师id
     * @param    type int   classtype 课程类型  0免费课程1在售课程
     * @return array
     *
     */
    public function getTeacherClass($teacherid,$classtype)
    {
        if(!is_intnum($teacherid) || empty($teacherid)){
            return return_format($this->str,36003,lang('param_error'));
        }
        //课程信息
        $classobj = new Curriculum;
        switch ($classtype){
            case 0:
                //0免费课程
                $where = [
                    'price' => '0.00',
                ];
                break;
            case 1:
                //1在售课程
                $where = [
                    'price' =>['neq','0.00'],
                ];
                break;
            case 2:
                $where = [];
                break;
            default:
                return return_format('',36100,lang('36100'));
        }
        $classinfo['data'] = $classobj->getTeacherList($where,$teacherid);
        $classinfo['classnum'] = count($classinfo['data']);
        if( $classinfo['classnum'] == 0){
            return return_format($classinfo,0,lang('success'));
        }else{
            return return_format($classinfo,0,lang('success'));
        }
    }
    /**
     * 查询课程评论
     * @Author yr
     * @param $teacherid   int [老师id]
     * @return array
     *
     */
    public function getCommentData($teacherid,$pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($teacherid) || !is_intnum($limit)){
            return return_format($this->str,36004,lang('param_error'));
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
        $organinfo = $teachermodel->getTeacherId($teacherid);
        $commentinfo['data'] = $commentmodel->getCommentList($teacherid,$limitstr);
        $total = $commentmodel->getCommentCount($teacherid);
        $commentinfo['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        if(empty($commentinfo)){
            return return_format($commentinfo,0,lang('success'));
        }else{
            return return_format($commentinfo,0,lang('success'));
        }

    }
    /**
     * [getAllTeacherList 获取所有老师列表]
     * @Author yr
     * @DateTime 2018-04-19T14:08:12+0800
     * @param    [array]    $pagenum   分页页数
     * @param    [string]   $limit 每页条目数
     * @return   [array]            [description]
     */
    public function getAllTeacherList($pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($pagenum) || !is_intnum($limit)){
            return return_format($this->str,36004,lang('param_error'));
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
        $teachermodel = new Teacherinfo;
        $teacher['data'] = $teachermodel->getAllTeacherList($limitstr);
        $total = $teachermodel->getAllTeacherCount();
        $teacher['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        if(empty( $teacher)){
            return return_format($teacher,0,lang('success'));
        }else{
            return return_format($teacher,0,lang('success'));
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
