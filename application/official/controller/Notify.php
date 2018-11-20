<?php
/**
**官方机构后台批量转账控制器
**/
namespace app\official\controller;
use think\Controller;
use think\Session;
use think\Request;
use alibatchtrans\AlipayBatch;
use think\Log;

class Notify extends Controller{
	
    /**支付宝批量转账异步的通知接口
     * [manageWithDrawResAsync]
     * @Author zzq
     * @DateTime 2018-06-11
     * @param str int           []   
     * @return   [array]                   [description]
     */
	public function manageWithDrawResAsync(){
		
        //测试
        /*$_POST = [
            'success_details'=>'Zfp2_4809584144816567^18739798667^赵志强^0.01^S^null^201805048427067^20180504070809|Zfp2_4709584144816833^18235102743^余瑞^0.01^S^null^201805058427067^20180505070809|',
            'fail_details'=>'Zfp2_4688584144816833^18235102743^余瑞^0.50^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180501248427065^20180501143651|Zfp2_5709584141216833^18739798667^赵志强^0.20^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^20180502248427065^20180502143651|'

        ];*/
        //打印$_POST的信息
        Log::write('------支付宝批量转账回调POST信息开始------');
        Log::write($_POST);
        Log::write('------支付宝批量转账回调POST信息结束------');
        $obj = new AlipayBatch();
        $res = $obj->batchNotifyUrlPost($_POST);

	}	
}