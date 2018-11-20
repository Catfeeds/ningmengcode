<?php

namespace app\teacher\controller;

use think\Controller;
use think\Request;
use app\teacher\business\MessageManager;
use login\Authorize;

class Message extends Authorize
{
    public $teacherid;
    public function _initialize()
    {
        parent::_initialize();
        //header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');

        $this->teacherid = $this->userInfo['info']['uid'];
    }
    /*
     *  教师消息列表
     * @Author wangwy
     */
    public function msgList(){
        $data = Request::instance()->post(false);
        $pagenum = $data['pagenum'];
        $teacherid = $this->teacherid;
        $msgobj = new MessageManager;
        $msg = $msgobj->getMessageList($pagenum,$teacherid);
        $this->ajaxReturn($msg);
    }
    /*
     *  点击改变为已查看状态
     *  @Author wangwy
     */
    public function setMessage(){
        $data = Request::instance()->post(false);
        $msgobj = new MessageManager();
        $msg = $msgobj->setMessage($data['idarr']);
        $this->ajaxReturn($msg);
    }
    /*
     * 教师登陆后的机构邀请列表
     *  @Author wangwy
     */
    public function invitationList(){
        $data = Request::instance()->post(false);
        $msgobj = new MessageManager();
        $msg = $msgobj->invitationList($data['teacherid']);
        $this->ajaxReturn($msg);
    }
    /*
     * 删除该teacherinvite的该信息
     *  @Author wangwy
     */
    public function delInvite(){
        $data = Request::instance()->post(false);
        $msgobj = new \app\teacher\business\TeacherManage();
        $msg = $msgobj->delInvite($data['idarr']);
        $this->ajaxReturn($msg);
    }
    /*
     * 标记已读该teacherinvite的该信息
     *  @Author wangwy
     */
    public function viewInvite(){
        $data = Request::instance()->post(false);
        $msgobj = new \app\teacher\business\TeacherManage();
        $msg = $msgobj->viewInvite($data['idarr']);
        $this->ajaxReturn($msg);
    }
    /*
 * 批量删除消息
 * @Author wangwy
 */
    public function delMsglist(){
        $data = Request::instance()->post(false);
        $arr = $data['idarr'];
        $mc = new MessageManager();
        $mm = $mc->delMsglist($arr);
        $this->ajaxReturn($mm);
    }

    /**
     *  群发消息
     */
    public function noticeAll(){
        $data = Request::instance()->post(false);
        $mc = new MessageManager();
        $mm = $mc->noticeAll($data['studentarr'],$data['title'],$data['content']);
        $this->ajaxReturn($mm);
    }
}
