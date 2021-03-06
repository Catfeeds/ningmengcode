<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use app\teacher\business\TeacherManage;
use think\Session;
use login\Authorize;
class Personal extends Authorize
//class Personal extends Controller
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        //$this->organid = $this->userInfo['info']['organid'];
        $this->teacherid = $this->userInfo['info']['uid'];
    }


    /**
     * 教师详情
     * @Author wyx
     * @param 使用teacherid 做查询
     * @return
     * URL:/teacher/Personal/teachInfo
     */
    public function teachInfo(){
      $teachid = $this->teacherid ;
      $manageobj = new TeacherManage;
      //获取教师列表信息,默认分页为5条
      $teachlist = $manageobj->getTeachInfo($teachid);

      // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        //return $teachlist ;
    }


    /**
     * 编辑教师资料时获取教师的信息
     * @Author wyx
     * @param 使用teacherid 做查询
     * @return
     * URL:/teacher/Personal/getTeachMsg
     */
    public function getTeachMsg(){
        $data = Request::instance()->POST(false);
        $teachid = $this->teacherid ;
        //$allaccountid = 1 ;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->getTeachMsg($teachid);

        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        //return $teachlist ;
    }






    /**
     * [updateTeacherMsg 修改教师信息]
     * @Author wyx
     * @DateTime 2018-04-19T17:20:04+0800
     * URL:/teacher/Personal/updateTeacherMsg
     */
    public function updateTeacherMsg(){
         $data = Request::instance()->POST(false);
        $data['teacherid'] = $this->teacherid;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->updateTeacherMsg($data);
        $this->ajaxReturn($teachlist);
        //return $teachlist ;

    }













    /**
     * 更新一级标签
     * @Author wangwy
     * @param 需要lableid
     * @param 需要lablename
     * @return
     * URL:/teacher/Personal/saveTeachLable
     */
    public function saveTeachLable(){
        //$organid = Session::get('organid');
        $lableid   = $this->request->param('lableid');
        $lablename = $this->request->param('lablename');
        $lableid   = 19 ;
        $lablename = 'testname12' ;
        $manageobj    = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->updateLable($lablename,$lableid);
        $this->ajaxReturn($lablelist);
    }
    /**
     * 删除教师标签
     * @Author wangwy
     * @param 需要lableid 要删除的标签id
     * @param 需要delflag 是否强制删除
     * @return
     * URL:/teacher/Personal/delTeachLable
     */
    public function delTeachLable(){
        //$organid = Session::get('organid');
        $organid   = 2 ;
        $lableid   = $this->request->param('lableid');
        $lableid   = 19 ;
        $delflag   = $this->request->param('delflag');
        $delflag   = 0;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->deleteLable($organid,$lableid,$delflag);
        $this->ajaxReturn($lablelist);
    }
    /**
     * 获取标签值列表
     * @Author wangwy
     * @param 需要lableid  根据标签id 获取他的子级
     * @param 需要organid  机构类别id
     * @return
     * URL:/teacher/Personal/getValueList
     */
    public function getValueList(){
        //$organid = Session::get('organid');
        $organid = 2 ;
        $lableid = $this->request->param('lableid');
        $lableid = 1 ;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->getValueList($organid,$lableid);
        $this->ajaxReturn($lablelist);
    }
    /**
     * 交换两个标签的位置
     * @Author wangwy
     * @param idx1  要交换的标签的id
     * @param idx2  要交换的标签的id
     * @return
     * URL:/teacher/Personal/exchangePos
     */
    public function exchangePos(){
        //$organid = Session::get('organid');
        $organid = 2 ;
        $idx1 = $this->request->param('idx1');
        $idx2 = $this->request->param('idx2');
        $idx1 = 2 ;
        $idx2 = 3 ;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->exchangeSort($organid,$idx1,$idx2);
        var_dump($lablelist);
        return '';
    }
    /**
     * 更改标签的值
     * @Author wangwy
     * @param lableid    要交换的标签的id
     * @param lablename  要交换的标签的id
     * @return
     * URL:/teacher/Personal/updateTagVal
     */
    public function updateTagVal(){
        //$organid = Session::get('organid');
        $organid = 2 ;
        $lableid   = $this->request->param('lableid');
        $lablename = $this->request->param('lablename');
        $lableid   = 21 ;
        $lablename = 'cctv' ;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->updateLable($lablename,$lableid);
        var_dump($lablelist);
        return '';
    }
    /**
     * 更改标签的值
     * @Author wangwy
     * @param lableid    要交换的标签的id
     * @return
     * URL:/teacher/Personal/removeVal
     */
    public function removeVal(){
        //$organid = Session::get('organid');
        $organid = 2 ;
        $lableid   = $this->request->param('lableid');
        $lableid   = 21 ;
        $manageobj = new TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->removeLableVal($organid,$lableid);
        var_dump($lablelist);
        return '';
    }
    /**
     * 切换教师标签的启用状态标记
     * @Author wyx
     * @param 使用organid 做查询
     * @return
     * URL:/admin/teacher/switchLabelStatus
     */
    public function switchLabelStatus(){
        // $organid = Session::get('organid');
        $organid = 2 ;
        $lableid = $this->request->param('lableid');
        $dataflag  = $this->request->param('dataflag');

        $manageobj = new \app\admin\business\TeacherManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->switchLabelStatus($lableid,$dataflag,$organid);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }


}
