<?php
/**
 * @author JCR
 * 此业务专为定时函数而提供
 */
namespace app\admin\business;
use app\admin\model\Category;
use app\admin\model\Curriculum;
use app\admin\model\Filemanage;
use app\admin\model\Organconfig;
use app\admin\model\Period;
use app\admin\model\Teacherinfo;
use app\admin\model\Teachertime;
use app\admin\model\Unit;
use app\admin\model\Teacherlable;
use app\admin\model\Teachertagrelate;
use app\admin\model\Messagesign;
use app\admin\model\Ordermanage;
use app\admin\model\Organpaylog;
use app\admin\model\Toteachtime;
use app\admin\model\Scheduling;
use think\Log;
use login\Rsa;
use Order;
use Messages;

class TimingTask {

	function __construct() {
	}

	/**
	 * [refreshSign 短信签名定时任务 刷新签名状态]
	 * @author JCR
	 */
	public function refreshSign(){
		$message = new \Messages();
		$sign = new Messagesign();

		// 获取对应的审核中的签名
		$list = $sign->getSignList(['status'=>1,'delflag'=>1]);
		if($list){
			$signId = array_column($list,'signid');
			// 刷新签名状态
			$message->refreshSign($signId);
		}
	}


	/**
	 * [setOrderTime 已完成订单去做结算]
	 * @author JCR
	 */
	public function setOrderTime(){
		// 查询·7天前的时间戳
		$timedate = mktime(00,00,00,date('m'),date('d')-7,date('Y'));
		$order = new Ordermanage();
		$where = ['closingstatus'=>1,'orderstatus'=>20,'finishtime'=>array('lt',$timedate)];
		// 7天前已完成订单，进行数据处理
		$list = $order->getOrderList($where,5000);
		if($list){
			$organpay = new Organpaylog();
			foreach ($list as $k => $v) {
				$organpay->addPayLog($v,$v['organid']);
			}
		}
	}


	/**
	 * [copyOrgan 复制机构信息]
	 * @author JCR
	 * @param $getOrganid
	 * @param $addOrganid
	 */
	public function copyOrgan($getOrganid,$addOrganid){
		// 复制分类
//		$this->copyCatefory($addOrganid);
//
//		// 测试复制课件
//		$this->copyFilemanage($getOrganid,$addOrganid);
//
//		// 复制老师
//		$this->copyTeacher($getOrganid,$addOrganid);
//
//		// 复制课程 复制课时单元 复制课时
//		$this->copyCurriculum($getOrganid,$addOrganid);
//
//		// 复制 机构配置项
////		$this->copyOrganConfig($getOrganid,$addOrganid);
//
//		// 复制老师标签
//		$this->copyTeacherLable($getOrganid,$addOrganid);
//
//		// 复制老师标签 中间关系表
//		$this->copyTeacherTag($getOrganid,$addOrganid);
	}



	/**
	 * [getTimeStatus 获取符合条件的开课信息 去走定时任务]
	 * @author JCR
	 * @return [type] [description]
	 */
	static function getTimeStatus(){
		log::write(date('Y-m-d H:i:s',time()).'开始','log',TRUE);
		$toteach = new Toteachtime();
		$schedule = new Scheduling();

		// 获取即将开课或已开课的数据集
//		$list = $toteach->getDatalist(1);
//		// 解析数组
//		foreach ($list as $k => $v) {
//			// 获取开课时间的时间戳
//			$strtime = strtotime($v['intime'].' '.get_time_key(explode(',',$v['timekey'])[0]));
//			// 匹配时间戳
//			if(time()>=$strtime){
//				// 已到开课时间 执行函数去更新对应的开课状态 为授课中
//				$indata = ['toteachtimeid'=> $v['id'],
//					'id'           => $v['schedulingid'],
//					'classstatus'  => 4,
//					'type'         => $v['type'],
//					'ordernum'	  => $v['ordernum']
//				];
//				$schedule->automateEdit($indata,$toteach);
//			}
//		}
		// 获取对应开课的 对应课时最后一条
		$endlist = $toteach->getDatalist(2);

		foreach ($endlist as $k => $v) {
			$timekeyarr = explode(',',$v['timekey']);
//			$strtime = strtotime($v['intime'].' '.get_time_key($timekeyarr[count($timekeyarr)-1]));
			if(time()>=$v['endtime']){
				// 最近一节课上完更新 开课对应状态
				$indata = ['toteachtimeid'=> $v['id'],
					'id'           => $v['schedulingid'],
					'classstatus'  => 5,
					'type'         => $v['type'],
					'schedulingid' => $v['schedulingid'],
					'studentid'    => $v['studentid'],
					'ordernum'	  => $v['ordernum']
				];
				$schedule->automateEdit($indata,$toteach);
			}
		}
		log::write('结束-----------------','log',TRUE);
	}



	static function savelog(){

		$str = 'J+4d1QUv7avM42/GdXBffdZIZQl75YZV2XdQ3Y6GjYlAjcE6sQtheVFh5WXPOywZpTp69CgUedq7s6TQmdMkYOCYHmeSr8XniwNYZQIvk5jN1mKY/4msB5obO3TptDQMJDSLCvDcSNxz7/BZgQEC+vYxXvhLRuROiObuXqIVbP6KLegKb6BGymLdSlOyGlIWRU6oEhs2xQPlYlG6ACxFe8PxRVfpFYmGQ54eL57wFNn5F2QRXyKQQZuwf4HRCJFR3jHsh21V6W3mEfjOTcvKx1+kFSuXsCSc8wepJgXOIBz6J/yfimvP+qLU5P2RfENy1SbMOj+/QiaLqvki3EE4wA==';

		$ret = new Rsa ;// 1加密
		$data = json_decode($ret->rsaDecryptorign($str),true);

		dump($data);
	}


	/**
	 * 定时任务 批量上传文件
	 */
	static function updateFile(){
//        log::write(date('Y-m-d H:i:s',time()).'上传文件开始','log',TRUE);
		$docking = new Docking();
		$docking->updateTk();
//        log::write(date('Y-m-d H:i:s',time()).'上传文件结束','log',TRUE);
	}


	/**
	 * 定时任务预约教室 5分钟一趟
	 */
	static function makeRoom(){
		log::write(date('Y-m-d H:i:s',time()).'开始llllll','log',TRUE);
		$docking = new Docking();
		$docking->addClassRoom();
		log::write(date('Y-m-d H:i:s',time()).'结束了llllll','log',TRUE);
	}
    /**
     * [cancelOrder 取消订单]
     * @author yr
     * @return [type] [description]
     */
    static function cancelOrder(){
        $orderobj = new Order;
        $orderobj->batchCancelOrders();
    }
    /*
       * 定时任务，上课前30分钟统计发送名单
       * @Author wangwy
       *  @return [type]
       */
    static function rcMobile(){
        log::write(date('Y-m-d H:i:s',time()).'开始统计','log',TRUE);
        $obj = new \app\teacher\business\CurriculumModule();
        $obj->rcMobile();
        log::write(date('Y-m-d H:i:s',time()).'结束统计','log',TRUE);
    }
    /*
     * 定时任务，上课前15分钟向老师和学生发短信
     * @Author wangwy
     *  @return [type]
     */
    static function RemindMessage(){
        log::write(date('Y-m-d H:i:s',time()).'开始发送短信','log',TRUE);
        $obj = new \app\teacher\business\CurriculumModule();
        $obj->RemindMessage();
        log::write(date('Y-m-d H:i:s',time()).'结束发送短信','log',TRUE);
    }
    /**
     * [cancelOrder 取消套餐订单]
     * @author yr
     * @return [type] [description]
     */
    static function cancelPackageOrder(){
        $orderobj = new Order;
        $orderobj->batchCancelPackageOrders();
    }
    /**
     * [cancelPackageStatus 更改套餐使用状态]
     * @author yr
     * @return [type] [description]
     */
    static function cancelPackageStatus(){
        $orderobj = new Order;
        $orderobj->dealwithPackageUse();
    }

}
