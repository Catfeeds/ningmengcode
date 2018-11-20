<?php
namespace app\admin\controller;
use app\admin\business\EducationalHandle;
use login\Authorize;
use app\admin\business\TransferHandle;
use think\Controller;
class Educational extends Authorize
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * [transferClassList 教务-调班列表]
     * @Author ZQY
     * @DateTime 2018-09-14 11:18:25
     * @return   [array]        [教务-调班列表]
     * URL:/admin/Proadmin/transferClassList
     */
    public function transferClassList()
    {
        $status = $this->request->param('status');//处理状态
        $pagenum = $this->request->param('pagenum');//分页页码
        $studentname = $this->request->param('studentname');//学生名字
        $limit = config('param.pagesize')['admin_transferclass_list'];
        $category = new EducationalHandle();
        $category_list = $category->TransferList($status,$studentname,$pagenum,$limit);
        $this->ajaxReturn($category_list);
        return $category_list;
    }
    /**
     * [TransferClassApply 教务-调班列表-同意、拒绝]
     * @Author ZQY
     * @DateTime 2018-09-14 11:18:25
     * @return   [array]        [教务-调班列表-同意、拒绝]
     * URL:/admin/Proadmin/TransferClassApply
     */
    public function TransferClassApply()
    {
        $status = $this->request->param('status');//处理状态
        $tranid = $this->request->param('tranid');
        $transfer = new EducationalHandle();
        $transfer_list = $transfer->TransferApply($status,$tranid);
        $this->ajaxReturn($transfer_list);
        return $transfer_list;
    }
    /**
     * [TransferLessonApply 教务-调课列表]
     * @Author ZQY
     * @DateTime 2018-09-17 16:03:25
     * @return   [array]        [教务-调课列表]
     * URL:/admin/Proadmin/TransferLessonApply
     */
    public function TransferLessonList()
    {
        $status = $this->request->param('status');//处理状态
        $pagenum = $this->request->param('pagenum');//分页页码
        $studentname = $this->request->param('studentname');//学生名字
        $limit = config('param.pagesize')['admin_transferclass_list'];
        $transfer = new EducationalHandle();
        $transfer_list = $transfer->TransferLesson($status,$studentname,$pagenum,$limit);
        $this->ajaxReturn($transfer_list);
        return $transfer_list;
    }
    /**
     * [TransferClassApply 教务-调课列表-同意、拒绝]
     * @Author ZQY
     * @DateTime 2018-09-17 11:18:25
     * @return   [array]        [教务-调课列表-同意、拒绝]
     * URL:/admin/Proadmin/TransferClassApply
     */
    public function TransferLessApply()
    {
        $status = $this->request->param('status');//处理状态
        $tranid = $this->request->param('tranid');
        $transfer = new EducationalHandle();
        $transfer_list = $transfer->TransferLessonApply($status,$tranid);
        $this->ajaxReturn($transfer_list);
        return $transfer_list;
    }
    /**
     * [TransferClassApply 教务-调课列表-同意、拒绝-检测此课时是否已经开班]
     * @Author ZQY
     * @DateTime 2018-09-17 11:18:25
     * @return   [array]        [教务-调课列表-同意、拒绝-检测此课时是否已经开班]
     * URL:/admin/Proadmin/TransferClassApply
     */
    public function testingLessApply()
    {
        $tranid = $this->request->param('tranid');
        $transfer = new EducationalHandle();
        $transfer_list = $transfer->testingLessonApply($tranid);
        $this->ajaxReturn($transfer_list);
        return $transfer_list;
    }
    /**
     * [influenceClass 调班列表->检测调班是否会对学生造成影响]
     * @Author ZQY
     * @DateTime 2018-11-02 15:06:26
     * @return   [array]        [调班列表->检测调班是否会对学生造成影响]
     * URL:/admin/Proadmin/influenceClass
     */
    public function influenceClass()
    {
        $tranid = $this->request->param('tranid');
        $transfer = new EducationalHandle();
        $transfer_list = $transfer->testingInfluence($tranid);
        $this->ajaxReturn($transfer_list);
        return $transfer_list;
    }

}