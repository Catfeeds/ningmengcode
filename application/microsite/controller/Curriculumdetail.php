<?php
/**
 * 获取课程详情列表 业务逻辑层
 *
 *
 */
namespace app\microsite\controller;
use think\Controller;
use app\student\business\ScheduManage;
use app\microsite\business\MicroScheduManage;
class Curriculumdetail extends \Base
{
    public function __construct(){
        // 必须先调用父类的构造函数
        parent::__construct();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        header('Access-Control-Allow-Origin:*');

    }
	
	/**
     * 查课程详情 课程选择
     * @Author lc
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid
     * @return   array();
     * URL:/microsite/Curriculumdetail/chooseAllList
     */
    public function chooseAllList()
    {
        //$courseid = 186;
		$courseid= $this->request->param('courseid');
        $scheduobj = new ScheduManage;
        $res =  $scheduobj ->chooseAllList($courseid);
        $this->ajaxReturn($res);
    }
	
    /**
     * 查询推荐排课详情
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid  int   课程id
     * @param    teacherid  int   老师id 可选参数 如果是从老师课程入口进入 需要传入teacherid
     * @param   classtypes  int [机构id]  1 录播课 2直播课 录播课没有班级,直接查询
     * @param   date  日期  2018-08-07
     * @param   fullpeople 日期  4或6
     * @return   array();
     * URL:/microsite/Curriculumdetail/getCurriculumInfo
     */
    public function getCurriculumInfo()
    {
        $courseid= $this->request->param('courseid');
        $teacherid = $this->request->param('teacherid');
        $date = $this->request->param('date');
        $fullpeople = $this->request->param('fullpeople');
        $scheduobj = new MicroScheduManage;
        $res =  $scheduobj ->getCurriculumInfo($courseid,$teacherid,$date,$fullpeople);
        $this->ajaxReturn($res);

    }
    /**
     * 查课程详情 查询日期
     * @Author yr
     * @DateTime 2018-04-23T14:11:19+0800
     * @param    courseid
     * @return   array();
     * URL:/microsite/Curriculumdetail/getCurriculumDateList
     */
    public function getCurriculumDateList()
    {
        $courseid= $this->request->param('courseid');
        $scheduobj = new MicroScheduManage;
        $res =  $scheduobj ->getCurriculumDateList($courseid);
        $this->ajaxReturn($res);
    }
}
