<?php
/**
*官方后台机构控制器
**/
namespace app\official\controller;
use app\official\controller\Base;
use app\official\business\OrganManage;
use app\official\business\FinanceManage;
use think\Session;
use think\Request;
use app\admin\business\TimingTask;
class Organ extends Base
{	

    /**
     * [getOrganList //获取机构列表]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param organname string 机构名（搜索关键字） 
     * @param auditstatus int 审核状态 
     * @param $orderbys str 排序方式
     * @param $pagenum int 页码数
     * @param $pernum int 一页几条
     * @return array 机构列表
     */
    public function getOrganList(){
        $data = [];
        $auditstatus = Request::instance()->post('auditstatus');
        $organname = Request::instance()->post('organname');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'auditstatus'=>$auditstatus ? $auditstatus : 0,
            'organname'=>$organname ? $organname : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_organ_list'],
        ];
        $organobj = new OrganManage();
        $res = $organobj->getOrganList($data);
        $this->ajaxReturn($res);
        return $res;     
    }

    
    /**
     * [getAllOrganListCount //获取某类机构（包括搜索的）数目]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param 无参数
     * @return array 机构列表的数目（包括未认证，待审核，已拒绝，已通过）
     */
    public function getAllOrganListCount(){
        $data = [];
        $organ = new OrganManage();
        $res = $organ->getAllOrganListCount();
        $this->ajaxReturn($res);
        return $res;  
        
    }

    /**
     * [getOrganBaseInfo //官方后台 展示基本信息]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param organid int 该机构的id
     * @return array 机构的基本信息
     */
    public function getOrganBaseInfo(){
        $organid = Request::instance()->post('organid','');
        $organ = new OrganManage();
        $res = $organ->getOrganBaseInfo($organid);
        $this->ajaxReturn($res);
        return $res;  
    }
    
    /**
     * [getOrganConfirmInfo //机构后台 展示认证信息]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 返回信息
     */
    public function getOrganConfirmInfo(){
        $organid = Request::instance()->post('organid');
        $organ = new OrganManage();
        $res = $organ->getOrganConfirmInfo($organid);
        $this->ajaxReturn($res);
        return $res; 
    }
    
    /**
     * [getOrganRegisterInfo //获取企业的注册信息]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 返回信息
     */
    public function getOrganRegisterInfo(){
        $organid = Request::instance()->post('organid');
        $organ = new OrganManage();
        $res = $organ->getOrganRegisterInfo($organid);
        $this->ajaxReturn($res);
        return $res; 
    }

    /**
     * [doAudit 审核某机构]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param auditstatus int    
     * @param refuseinfo string  审核信息通过的时候为空
     * @param organid int  机构id
     * @return array 操作是否成功的信息
     */
    public function doAudit(){
        $data = [];
        $organid = Request::instance()->post('organid');
        $auditstatus = Request::instance()->post('auditstatus');
        $refuseinfo = Request::instance()->post('refuseinfo','');
        $data = [
            'organid' => $organid,
            'auditstatus'=>$auditstatus,
            'refuseinfo'=>$refuseinfo,
        ];
        $organ = new OrganManage();
        $res = $organ->doAudit($data);
        $this->ajaxReturn($res);
        return $res;     
    }

    /**
     * [getOrganAuditResById //官方后台 展示审核结果 某机构]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 获取机构审核的结果
     */
    public function getOrganAuditResById(){
        $organid = Request::instance()->post('organid');
        $organ = new OrganManage();
        $res = $organ->getOrganAuditResById($organid);
        $this->ajaxReturn($res);
        return $res;  
    }

    
    /**
     * [setOrganOnOrOff //设置通过认证后的企业 启用或者禁用]
     * @Author zzq
     * @DateTime 2018-05-07
     * @param organid int  机构id
     * @param auditstatus int  当前的机构是启用还是禁用
     * @return array 返回信息
     */
    public function setOrganOnOrOff(){
        $organid = Request::instance()->post('organid','');
        $auditstatus = Request::instance()->post('auditstatus','');
        $organManage = new OrganManage();
        $res = $organManage->setOrganOnOrOff($organid,$auditstatus);
        $this->ajaxReturn($res);
        return $res; 
    }


    /**
     * [getOrganPayAuditBillTotalSum //获取机构认证缴费总额]
     * @Author zzq
     * @DateTime 2018-05-15 
     * @param 无参数         []   ]   
     * @return   [array]                   [description]
     */
    public function getOrganPayAuditBillTotalSum(){
        $financeManage = new FinanceManage();
        $res = $financeManage->getOrganPayAuditBillTotalSum();
        $this->ajaxReturn($res);
        return $res; 
    }


    /**
     * [getOrganPayAuditBillList //机构付款明细列表]
     * @Author zzq
     * @DateTime 2018-06-09
     * @param fromdate string           [开始日期]     
     * @param enddate string           [截止日期]     
     * @param domain string               [机构与名]     
     * @param orderbys string           [排序方式]        
     * @param pagenum int           [每页数目]     
     * @param pernum int           [页码数]   ]   
     * @return   [array]                   [description]
     */
    public function getOrganPayAuditBillList(){
        //获取订单余额
        $data = [];
        $fromdate = Request::instance()->post('fromdate');
        $enddate = Request::instance()->post('enddate');
        $domain = Request::instance()->post('domain');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'fromdate'=>$fromdate ? $fromdate : '',  
            'enddate'=>$enddate ? $enddate : '',
            'domain'=>$domain ? $domain : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_organ_pay_audit_bill_list'],
        ];
        // var_dump($data);
        // die;
        $financeManage = new FinanceManage();
        $res = $financeManage->getOrganPayAuditBillList($data);
        $this->ajaxReturn($res);
        return $res;  
    }
    
    /**
     * [getApplyVipOrganList //获取申请vip的机构的列表]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param organname string 机构名（搜索关键字） 
     * @param $orderbys str 排序方式
     * @param $pagenum int 页码数
     * @param $pernum int 一页几条
     * @return array 机构列表
     */
    public function getApplyVipOrganList(){
        $data = [];

        $organname = Request::instance()->post('organname');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'organname'=>$organname ? $organname : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_apply_vip_organ_list'],
        ];
        $organobj = new OrganManage();
        $res = $organobj->getApplyVipOrganList($data);
        $this->ajaxReturn($res);
        return $res;     
    }

    
    //将原有机构的机构信息copy到新机构
    //复制机构信息 生成新的机构id  nm_organ
    //复制超级管理员信息nm_allaccount nm_adminmember
    //复制机构的基本信息 nm_organbaseinfo
    //复制机构的认证信息 nm_organauthinfo 
    /**
     * [copyFromOldOrganToNewOrgan //批准机构申请vip,生成新机构]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param $oldOrganid int 原有的免费的机构的id 
     * @return array 返回生成新的vip机构的机构主键id
     */
    public function copyFromOldOrganToNewOrgan($oldOrganid){
        
        $oldOrganid = Request::instance()->post('oldOrganid');
        $oldOrganid = $oldOrganid ? $oldOrganid : '';

        $organobj = new OrganManage();
        $res = $organobj->copyFromOldOrganToNewOrgan($oldOrganid);
        //$this->ajaxReturn($res);
        //return $res;

        //复制其他表的数据
        $newOrganid = $res['data']['newOrganid'];
        $timingTask  =  new TimingTask();
        $timingTask->copyOrgan($oldOrganid,$newOrganid);

        //复制基本信息成功
        if($res['code'] == 0){
            $organ = new OrganManage();
            $organ->updateOrganToHasVip($oldOrganid);            
        }

    }

    
    /**
     * [addRedisCopyList //将要复制机构id投入redis list队列]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param $organid int 原有的免费的机构的id 
     * @return array 
     */
    public function addRedisCopyList(){
        $organid = Request::instance()->post('organid');
        $organobj = new OrganManage();
        $res = $organobj->addRedisCopyList($organid);
        $this->ajaxReturn($res);
        return $res;   
    }

    
    /**
     * [timeTaskCopy //定时任务执行复制机构的数据]
     * @Author zzq
     * @DateTime 2018-05-26
     * @param [] 
     * @return array 
     */
    public function timeTaskCopy(){
        $organobj = new OrganManage();
        $res = $organobj->timeTaskCopy();   
    }


}
