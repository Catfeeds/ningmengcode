<?php
/**
 * 机构学生登录 业务逻辑层
 * 
 * 
 */
namespace app\apphjx\controller;
use login\Authorize;
use think\Controller;
use app\apphjx\business\ApphjxUserManage;
use think\Request;
class User extends Authorize
    {
    //定义上传目录
    protected $dstfolder = 'student/1/headimg';
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $this->userid = $this->userInfo['info']['uid'];
    }
    /**
     * 查询学生个人资料
     * @Author cy
     * @param  userid  学生id
     * apphjx/User/getStudentInfo
     * @return array();
     */
    public function getStudentInfo(){
		$studentid = $this->userid;
        $userobj = new ApphjxUserManage;
        $res = $userobj->getStudentInfo($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 学生修改昵称，性别，学校，年级，班级，设备名称，统一调用
     * @Author cy
     * @DateTime 2018-10-17
     * @return   array()
     * URL:/apphjx/User/uploadAppuserInfo
     */
    public function updateAppuserInfo(){
        $data = Request::instance()->POST(false);
        $data['id'] = $this->userid;
        $userobj = new ApphjxUserManage;
        $res = $userobj->updateAppuserInfo($data);
        $this->ajaxReturn($res);
    }
    /**
     * 查询班级个人资料
     * @Author cy
     * apphjx/User/getClassInfo
     * @return array();
     */
    public function getClassInfo(){
        $userobj = new ApphjxUserManage;
        $res = $userobj->getClassInfo();
        $this->ajaxReturn($res);
    }
    /**
     * 查询学生标签资料
     * @Author cy
     * @param  userid  学生id
     * apphjx/User/getLabelInfo
     * @return array();
     */
    public function getLabelInfo()
    {
        $studentid = $this->userid;
        $userobj = new ApphjxUserManage;
        $res = $userobj->getLabelInfo($studentid);
        $this->ajaxReturn($res);
    }
    /**
     * 新增学生标签资料
     * @Author cy
     * @param  studentid  学生id
     * @param  label      标签名
     * @url    apphjx/User/createLabelInfo
     * @return array();
     */
    public function createLabelInfo()
    {
        $studentid = $this->userid;
        $labelinfo = $this->request->param('label');
        $userobj = new ApphjxUserManage;
        $res = $userobj->createLabelInfo($studentid,$labelinfo);
        $this->ajaxReturn($res);
    }
    /**
     * 删除学生标签资料
     * @Author cy
     * @param  studentid  学生id
     * @param  labelid      标签id
     * @url    apphjx/User/deleteLabelInfo
     * @return array();
     */
    public function deleteLabelInfo()
    {
        $studentid = $this->userid;
        $labelid = $this->request->param('labelid');
        $userobj = new ApphjxUserManage;
        $res = $userobj->deleteLabelInfo($studentid,$labelid);
        $this->ajaxReturn($res);
    }
    /**
     * 修改学生标签资料
     * @Author cy
     * @param  studentid    学生id
     * @param  labelid      标签id
     * @param  label        标签名
     * @url    apphjx/User/updateLabelInfo
     * @return array();
     */
    public function updateLabelInfo()
    {
        $studentid = $this->userid;
        $labelid   = $this->request->param('labelid');
        $label     = $this->request->param('label');
        $userobj = new ApphjxUserManage;
        $res = $userobj->updateLabelInfo($studentid,$labelid,$label);
        $this->ajaxReturn($res);
    }
    /**
    * 首页点击搜索，进行跳转
     * @Author cy
     *apphjx/User/searchInfo
     * return array
     **/
    public function searchInfo()
    {
        return $this->getLabelInfo();
    }
    /**
     * 搜索页，标签搜索
     * @Author cy
     * @url    apphjx/User/searchLabelInfo
     * @param  studentid 学生id
     * @param  labelid   标签id
     * return array
     **/
    public function searchLabelInfo()
    {
        $studentid = $this->userid;
        $labelid   = $this->request->param('labelid');
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['app_composition_list'];
        $userobj   = new ApphjxUserManage();
        $articlelist = $userobj->searchLabelInfo($studentid,$labelid,$pagenum,$limit);
        $this->ajaxReturn($articlelist);
    }
    /**
     * 搜索页，文章标题关键字搜索
     * @Author cy
     * @url    apphjx/User/searchArticleInfo
     * @param  studentid 学生id
     * @param  labelid   标签id
     * return array
     **/
    public function searchArticleInfo()
    {
        $studentid = $this->userid;
        $keywords   = $this->request->param('keywords');
        $pagenum = $this->request->param('pagenum');
        $limit = config('param.pagesize')['app_composition_list'];
        $userobj   = new ApphjxUserManage();
        $articlelist = $userobj->searchArticleInfo($studentid,$keywords,$pagenum,$limit);
        $this->ajaxReturn($articlelist);
    }
    /**
     * 设置页，获取设备名称
     * @Author cy
     * @url    apphjx/User/getEquipmentInfo
     * @param  studentid 学生id
     * return array
     **/
    public function getEquipmentInfo()
    {
        $studentid   = $this->userid;
        $userobj   = new ApphjxUserManage();
        $articlelist = $userobj->getEquipmentInfo($studentid);
        $this->ajaxReturn($articlelist);
    }

}
