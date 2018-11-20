<?php
namespace app\student\business;
use app\student\model\Category;
use app\student\model\Coursepackage;
use app\student\model\Coursepackageorder;
use app\student\model\Coursepackageuse;
use app\student\model\CoursepackageUseDetail;
use app\student\model\Curriculum;
use app\student\model\Ordermanage;
use app\student\model\Studentfunds;
use app\student\model\Scheduling;
use app\student\model\Studentinfo;
use Order;
use think\Cache;
use think\db;
use alipay\Alipaydeal;
use wxpay\Wxpay;
class MyOrderManage
{
    protected $foo;
    protected $str;
    protected $cancelstatus = 10;
    protected $completestatus = 20;
    //定义大班课报名人数
    protected $maxapply = 1000;
    //定义小班课报名人数
    protected $minapply = 12;
    protected $paytype = [
        '0','1','2','3','1,2','1,3','1,4'
    ];
    //支付组合类型
    protected $paymethod = [
        '1','2','3','4','1,2','1,3','1,4'
    ];
    protected $wxnotifyurl = '/admin/ServerNotice/wxCourseNotify';
    protected $alinotifyurl = '/admin/ServerNotice/aliCourseNotify';
    protected $wxappCourseurl = '/admin/ServerNotice/wxappCourseNotify';//微信app买课支付回调
    protected $wxappPackageyurl = '/admin/ServerNotice/wxappPackageNotify';//微信app套餐支付支付回调
    protected $wxappRechargeurl = '/admin/ServerNotice/wxappRechargeNotify';//微信app充值支付回调
    protected  $classtype;
    //班级状态 0 未招生 1已招生 2已成班 3已满员 4授课中 5已结束(已取消)
    protected  $classstatus = [0,1,2,3,4,5];
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
        //定义班级类型
        $this->classtype = [
          '1'=> '一对一',
          '2'=> '小班课',
          '3'=> '大班课',
        ];
    }
    /**
     * 学生统一下单
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param studentid int 学生用户id
     * @param schedulingid int 排课id
     * @param  int  float 订单金额
     * @param  originprice float  课程原价
     * @param  int  ordersource    下单渠道 1web 2app
     * @return   array();
     */
    public function gotoOrder($studentid,$schedulingid,$amount,$ordersource,$originprice,$courseid,$addressid,$usestatus,$type,$packageid,$packagegiftid,$packageuseid){
        if(!is_intnum($studentid) || !is_intnum($ordersource) || empty($amount)){
            return return_format($this->str,34000,lang('param_error'));
        }
        $coursemodel = new Curriculum;
        $where = [
            'id' =>$courseid
        ];
        $courseinfo = $coursemodel->getCourserById($where);
        if(empty($courseinfo)){
            return return_format('',35100,'没有此课程信息');
        }
        $ordernum = getOrderNum();
        if($courseinfo['classtypes'] == 1){//录播课
            $periodnum = $courseinfo['periodnum'];//课时数量
            $unit_price = $courseinfo['price'];//单价
        }else{//直播课 班级
            if(!is_intnum($schedulingid)){
                return return_format('',35101,'参数schedulingid错误');
            }
            $schedumodel = new Scheduling();
            $scheduinfo = $schedumodel->getCourserById($schedulingid);
            if(empty($scheduinfo)){
                return return_format('',35102,'没有此班级信息');
            }
            $periodnum = $scheduinfo['periodnum'];//课时数量
            $unit_price = $scheduinfo['price'];//单价

        }
        $originprice = $unit_price * $periodnum ;//原价
        //计算价格
        if($usestatus == 1) {
            if($originprice == '0.00'){
                return return_format('',39102,'0元课程不能使用优惠券');
            }
            //使用优惠券
            $packageusemodel = new Coursepackageuse();
            $packagewhere = ['id'=>$packageuseid];
            $packagefield = 'surplus';
            $totalpackagenum = $packageusemodel->findStudentPackage($packagewhere,$packagefield)['surplus'];
            if($totalpackagenum>= $periodnum){
                $amount = '0.00';//实付金额
            }else{
                $pricenum = $periodnum -$totalpackagenum;
                $amount = $pricenum * $unit_price;
            }

        }else{
            //不适用套餐 实付金额等于课程的原价
            $amount =  $originprice;
        }
        //拼装订单信息
        $data['originprice'] = $originprice;
        $data['coursename'] = $courseinfo['coursename'];
        $data['curriculumid'] = $courseinfo['courseid'];
        //暂且不做优惠券
        $data['amount'] = $amount;
        $data['discount'] = $originprice-$amount;
        $data['ordersource'] = $ordersource;
        $data['ordernum'] = $ordernum;
        $data['ordertime'] = time();
        $data['studentid'] = $studentid;
        $data['schedulingid'] = $schedulingid;
        $data['coursetype'] = $courseinfo['classtypes'];
        $data['teacherid'] = $courseinfo['teacherid'];
        $data['addressid'] = $addressid;
        $data['usepackage'] = $usestatus;
        $data['usestatus'] = $usestatus;
        $data['type'] = $type;
        $data['packageid'] = $packageid;
        $data['packagegiftid'] = $packagegiftid;
        $data['packageuseid'] = $packageuseid;
      /*  //0元课程 直接购买
        if($amount == '0.00'){
            $data['orderstatus'] =  20;
            $data['paytime'] = time();
        }*/
            switch ($courseinfo['classtypes']){
                case 1:
                    //录播课购买 课程购买
                    $ordermodel  = new Ordermanage;
                    $count = $ordermodel->getOrderByCourseid($studentid,$courseid);
                    if($count>0){
                        return return_format($this->str,34002,lang('34002'));
                    }
                    $result = $this->dealFreeOrder($data);
                    return $result;
                case 2:
                    //直播课购买 班级购买
                    //根据排课id查询课程相关信息
                    $schedumodel = new Scheduling;
                    $scheduinfo = $schedumodel->getCourserOne($schedulingid);
                    $data['classname'] = $scheduinfo['gradename'];
                    $data['teacherid'] = $scheduinfo['teacherid'];
                        //查看班级状态 班级状态 0 未招生 1已招生 2已成班 3已满员 4授课中 5已结束(已取消)
                        if($scheduinfo['classstatus'] == 3){
                            return return_format('',34023,lang('34023'));
                        }elseif($scheduinfo['classstatus'] == 4){
                            return return_format('',34024,lang('34024'));
                        }elseif($scheduinfo['classstatus'] == 5){
                            return return_format('',34025,lang('34025'));
                        }
                        $ordermodel  = new Ordermanage;
                        $count = $ordermodel->getOrderBySchedu($studentid,$schedulingid);
                        if($count>0){
                            return return_format($this->str,34002,lang('34002'));
                        }

                    //满员人数
                    $fullpeople = $scheduinfo['fullpeople'];
                    $result = $this->dealApplyStatus($data,$schedulingid,$fullpeople);
                    return $result;
            }

    }
    /**
     * 学生显示订单详情和账户余额信息
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     */
    public function showOrderDetail($ordernum){
        if(empty($ordernum)){
            return return_format('',34021,lang('param_error'));
        }
        $ordermodel = new Ordermanage;
        $orderinfo = $ordermodel->getOrderInfo($ordernum);
        if(empty($orderinfo)){
            return return_format('',34022,lang('34022'));
        }else{
            return return_format($orderinfo,0,lang('success'));
        }

    }
    /**
     * 学生显示订单详情和账户余额信息
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 课程订单号
     * @return   array();
     */
    public function getUserPackage($studentid,$curriculumid,$amount){
        if(empty($curriculumid) || !is_intnum($studentid)){
            return return_format('',34021,lang('param_error'));
        }
        //查询出该课程id对应的分类id
        $courseusemodel = new Curriculum();
        $where = [
            'id'=> $curriculumid
        ];
        $categoryid = $courseusemodel->getCourserById($where)['categoryid'];
        $classtypes = $courseusemodel->getCourserById($where)['classtypes'];//课程类型
        //根据课程id 查询学生满足条件的优惠券列表
        $courseusemodel = new Coursepackageuse();
        $where = [
            'studentid' => $studentid,
            'ifuse' => ['neq',2],
            'delflag' => 1
        ];
        $field = 'packageid,packagegiftid,type,surplus,total,endtime,id as packageuseid';
        $result = $courseusemodel->getStudentPackage($where,$field);
        $packagemodel = new Coursepackage;
        foreach($result as $k=>$v){
            $result[$k]['setmeal'] =   $packagemodel->getPackageDetail($v['packageid'])['setmeal'];
            $result[$k]['threshold'] =   $packagemodel->getPackageDetail($v['packageid'])['threshold'];
            $result[$k]['trialtype'] = $packagemodel->getPackageDetail($v['packageid'])['trialtype'];
            $result[$k]['categoryids'] = $packagemodel->getPackageDetail($v['packageid'])['categoryids'];
            $result[$k]['curriculumids'] = $packagemodel->getPackageDetail($v['packageid'])['curriculumids'];
            $result[$k]['gifttrialtype'] = $packagemodel->getPackageDetail($v['packageid'])['gifttrialtype'];
            $result[$k]['giftcategoryids'] = $packagemodel->getPackageDetail($v['packageid'])['giftcategoryids'];
            $result[$k]['giftcurriculumids'] = $packagemodel->getPackageDetail($v['packageid'])['giftcurriculumids'];
            $result[$k]['giftthreshold'] = $packagemodel->getPackageDetail($v['packageid'])['giftthreshold'];
            $result[$k]['sendlive'] = $packagemodel->getPackageDetail($v['packageid'])['sendlive'];
            $result[$k]['sendvideo'] = $packagemodel->getPackageDetail($v['packageid'])['sendvideo'];
        }
        foreach($result as $key=>$value){

            //判断当前的优惠券的类型
            if($result[$key]['surplus'] == 0){
                unset($result[$key]);
                continue;
            }
            if($result[$key]['type'] == 1){
                //如果使用门槛有限制 并且小于课程价格
               if($value['threshold']>0&&$value['threshold']<$amount){
                    unset($result[$key]);
                    continue;
               }
               if(!empty($value['categoryids'])){
                   $catearr = explode(',',$value['categoryids']);

               }
               if($value['trialtype'] == 2&&!in_array($categoryid,$catearr)){
                    unset($result[$key]);
                    continue;
               }
               if(!empty($value['curriculumids'])){
                   $courseidarr = explode(',',$value['curriculumids']);
               }
               if($value['trialtype'] == 3&&!in_array($curriculumid,$courseidarr)){
                   unset($result[$key]);
                   continue;
               }
            }else{
                //如果使用门槛有限制 并且小于课程价格
                if($value['giftthreshold']>0&&$value['giftthreshold']<$amount){
                    unset($result[$key]);
                    continue;
                }
                if(!empty($value['giftcategoryids'])){
                    $catearr = explode(',',$value['giftcategoryids']);
                }
                if($value['gifttrialtype'] == 2&&!in_array($categoryid,$catearr)){
                    unset($result[$key]);
                    continue;
                }
                if(!empty($value['giftcurriculumids'])){
                    $courseidarr = explode(',',$value['giftcurriculumids']);
                }
                if($value['gifttrialtype'] == 3&&!in_array($curriculumid,$courseidarr)){
                    unset($result[$key]);
                    continue;
                }
                if($classtypes == 1 && $value['sendvideo'] == 0){
                    unset($result[$key]);
                    continue;
                }
                if($classtypes == 2 && $value['sendlive'] == 0){
                    unset($result[$key]);
                    continue;
                }
            }
        }

        $result  = array_values($result);
        if(empty($result)){
            return return_format($result,0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }

    }
    /**
     * 学生显示订单详情和账户余额信息
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @param type int  类型 1购买课程2购买套餐
     * @return   array();
     */
    public function queryOrderStatus($ordernum,$type){
        if(empty($ordernum)){
            return return_format('',34021,lang('param_error'));
        }
        if($type== 1){
            $model = new Ordermanage;
            $orderinfo = $model->getOrderInfo($ordernum);
        }else{
            $model = new Coursepackageorder;
            $orderinfo = $model->getPackageOrderInfo($ordernum);
        }
        if($orderinfo['orderstatus'] ==10){
            return return_format('',34231,lang('34031'));
        }
        if($orderinfo['orderstatus'] ==20){
            return return_format('',34232,lang('34032'));
        }
        if(empty($orderinfo)){
            return return_format('',34234,lang('34022'));
        }else{
            return return_format('',0,lang('success'));
        }

    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 1,2 余额支付在前，其他支付在后面
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     */
    public function gotoPay($studentid,$ordernum,$paytype,$source=2){
        if(!in_array($paytype,$this->paymethod)){
            return return_format('',34026,lang('34026'));
        }
        if(!is_intnum($studentid)){
            return return_format('',34027,lang('param_error'));
        }
        $ordemodel = new Ordermanage;
        $orderinfo = $ordemodel->getUnpaidOrderInfo($studentid,$ordernum);
        //如果该班级已经在授课中或已经结束不能支付
        if($orderinfo['classstatus'] == 4){
            return return_format('',34028,lang('34028'));
        }
        if($orderinfo['classstatus'] == 5){
            return return_format('',34029,lang('34029'));
        }
        if($orderinfo['classstatus'] == 6){
            return return_format('',34130,lang('34130'));
        }
        if(empty($orderinfo)){
            return return_format('',34030,lang('34030'));
        }
        if($orderinfo['orderstatus'] ==10){
            return return_format('',34031,lang('34031'));
        }
        if($orderinfo['orderstatus'] ==20){
            return return_format('',34032,lang('34032'));
        }
        $studentinfomodel = new Studentinfo();
        $mobile = $studentinfomodel->getStudentInfo($studentid)['mobile'];
        $openid = Cache::get($mobile.'-'.'openid');//获取openid
        if($orderinfo['usepackage'] == 1){
            //如果使用套餐 查看套餐是否可用
            $coursepackagemodel = new CoursepackageUseDetail();
            $detailwhere = ['d.ordernum'=>$ordernum];
            $detailfield = 'd.packageuseid,d.usenum,u.surplus';
            $result = $coursepackagemodel->joinFindInfo($detailwhere,$detailfield);
            if($result['usenum']>$result['surplus']){
                return return_format('',34132,'该笔订单可抵扣的套餐课时数量不足,请重新下单');
            }
        }
        $amount = $orderinfo['amount'];
        $subject = $orderinfo['coursename'];
        $body = $orderinfo['coursename'];
        $studentfundmodel = new Studentfunds();
        $usablemoney = $studentfundmodel->getUserBalance($studentid)['usablemoney'];
        //根据type跳到不同的支付方式
        if(!in_array($paytype,$this->paytype)){
            return return_format('',34033,lang('34026'));
        }
        $paytype = explode(',',$paytype);
        $length = count($paytype);
        //如果length为1是单一支付方式
        if($length == 1){
            //查看此订单是否是混合支付.如果是混合支付
            if($orderinfo['balance'] !== '0.00'){
                //该笔第三方支付的价钱是订单实际价格-余额支付的部分
                $amounts = $orderinfo['amount'] *100;
                $usablemoneys = $orderinfo['balance']*100;
                $amount  = (float)($amounts - $usablemoneys)/100;
            }
            $paytype = intval($paytype[0]);
            if(!in_array($paytype,$this->paytype)){
                return return_format('',34033,lang('34026'));
            }
            switch ($paytype){
                case 1:
                    //余额支付 直接扣款
                    $orderclass = new Order;
                    $result = $orderclass->balancePay($studentid,$ordernum,$amount,$usablemoney,$paytype,$orderinfo['coursetype'],$orderinfo['usepackage']);
                    return $result;
                    break;
                case 2:
                    $wxpayobj = new Wxpay;
                    $notifyurl = config('param.server_url') . $this->wxnotifyurl;//异步回调地址 扫码和公众号支付使用
                    if($source == 1){//app支付
                        $appnotifurl = config('param.server_url').$this->wxappCourseurl;
                        $result = $wxpayobj->appWxpay($ordernum, $subject, $amount, $body, $appnotifurl);
                        if ($result['result_code'] !== 'SUCCESS') {
                            return return_format('', 34034, lang('33031'));
                        }
                        $data['data'] = $result;
                        return return_format($data, 0, lang('success'));
                    }elseif($source == 2){//扫码支付
                        $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$amount,$body,$notifyurl);
                        if($result['result_code'] !== 'SUCCESS'){
                            return return_format('',34034,lang('33031'));
                        }
                        $url = $result['code_url'];
                        $image = get_base64_qrcode($url);
                        $data['type'] = 1;
                        $data['codeurl'] = $image;
                        return  return_format($data,0,lang('success'));
                    }else{//微信公众号支付
                        if(!is_weixin()){
                            return return_format('',38000,'请在微信客户端打开链接完成支付');
                        }
                        if(empty($openid)){
                            return return_format('',-1,'未获取到openid');
                        }
                        $result = $wxpayobj->jsapiWxpay($ordernum,$subject,$amount,$body,$notifyurl,$openid);
                        if(empty($result)){
                            return return_format('',34070,'支付失败');
                        }
                        $data = json_decode($result,true);
                        return return_format($data,0,lang('success'));
                    }
                    break;
                case 3:
                    $alipayobj = new Alipaydeal;
                    if($source == 1){//app支付
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=2";
                        $returnurl = config('param.http_name') . $_SERVER['HTTP_HOST'] . $returnurl;
                        $notifyurl = config('param.server_url') . $this->alinotifyurl;
                        $res = $alipayobj->appAlipay($ordernum,$subject,$amount,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data, 0, lang('success'));
                    }else{//扫码支付
                        $alipayobj = new Alipaydeal;
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=2";
                        $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                        $notifyurl = config('param.server_url').$this->alinotifyurl;
                        $res =  $alipayobj->createPayRequest($ordernum,$subject,$amount,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data,0,lang('success'));
                    }
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                case 5:
                    /*$paypalobj = new Paypal;
                    $returnurl = "/web#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $res =  $paypalobj->pcpaypal($ordernum,$subject,$amount,$body,$returnurl);
                    return return_format($res,0,lang('success'));*/
                    break;
                default:
                    return return_format('',34035,lang('34026'));
                    break;
            }
        }elseif($length == 2){
            //查看该余额是否正常
            $fundsmodel  =  new Studentfunds;
            $fundsinfo = $fundsmodel->getUserBalance($studentid);
            $usablemoney = $fundsinfo['usablemoney'];
            if($fundsinfo['usablemoney']>=$amount){
                return return_format('',34037,lang('34037'));
            }
            //查看账户余额里是否有冻结的金额,如果没有
            if($orderinfo['balance'] == '0.00'){
                //如果是混合支付,修改订单表balance,把余额支付的钱，放入冻结金额
                $ordermodel = new \Order;
                $result = $ordermodel->delFreeze($studentid,$ordernum,$usablemoney);
                if($result !=1){
                    return $result;
                }
                $orderinfo['balance'] = $usablemoney;//如果修改数据库成功，重新赋值
            }
            $mixtype = $paytype[1];
            $amounts = $amount * 100;
            $usablemoneys = $orderinfo['balance'] * 100;
            $price = (float)($amounts - $usablemoneys) / 100;
            switch($mixtype){
                case 2:
                    $wxpayobj = new Wxpay;
                    $notifyurl = config('param.server_url') . $this->wxnotifyurl;//异步回调地址 扫码和公众号支付使用
                    if($source == 1){//app支付
                        $appnotifurl = config('param.server_url').$this->wxappCourseurl;
                        $result = $wxpayobj->appWxpay($ordernum, $subject, $price, $body, $appnotifurl);
                        if ($result['result_code'] !== 'SUCCESS') {
                            return return_format('', 34034, lang('33031'));
                        }
                        $data['data'] = $result;
                        return return_format($data, 0, lang('success'));
                    }elseif($source == 2){//扫码支付

                        $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$price,$body,$notifyurl);
                        if($result['result_code'] !== 'SUCCESS'){
                            return return_format('',34034,lang('33031'));
                        }
                        $url = $result['code_url'];
                        $image = get_base64_qrcode($url);
                        $data['type'] = 1;
                        $data['codeurl'] = $image;
                        return  return_format($data,0,lang('success'));
                    }else{
                        if(!is_weixin()){
                            return return_format('',38000,'请在微信客户端打开链接完成支付');
                        }
                        if(empty($openid)){
                            return return_format('',-1,'未获取到openid');
                        }
                        $result = $wxpayobj->jsapiWxpay($ordernum,$subject,$amount,$body,$notifyurl,$openid);
                        if(empty($result)){
                            return return_format('',34070,'支付失败');
                        }
                        $data = json_decode($result,true);
                        return return_format($data,0,lang('success'));
                    }
                    break;

                case 3:
                    $alipayobj = new Alipaydeal;
                    $notifyurl = config('param.server_url') . $this->alinotifyurl;
                    if($source == 1){//app支付
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=2";
                        $returnurl = config('param.http_name') . $_SERVER['HTTP_HOST'] . $returnurl;
                        $res = $alipayobj->appAlipay($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data, 0, lang('success'));
                    }else{//扫码支付
                        $alipayobj = new Alipaydeal;
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=2";
                        $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                        $res =  $alipayobj->createPayRequest($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data,0,lang('success'));
                    }
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                case 5:
                    $paypalobj = new Paypal;
                    $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $res =  $paypalobj->pcpaypal($ordernum,$subject,$amount,$body,$returnurl);
                    return return_format($res,0,lang('success'));
                default:
                    return return_format('',34039,lang('34026'));
                    break;
            }

        }else{
            return return_format('',34040,lang('param_error'));
        }

    }
    /**
     * 查询我的订单列表
     * @Author yr
     * @param $userid  int  用户id
     * @return array
     *
     */
    public function myOrderList($userid,$pagenum,$limit)
    {
        //先判断用户id和分页页数是否合法
        if(!is_intnum($userid) || !is_intnum($limit) ){
            return return_format($this->str,34041,lang('param_error'));
        }
        //判断分页页数
        if(is_intnum($pagenum)>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }

        //实例化模型
        $ordermodel = new Ordermanage;
        $result = $ordermodel->getStudentOrder($userid,$limitstr,$orderstatus=null,$where=[]);
        $total = $ordermodel->studentOrderCount($userid,$orderstatus=null,$where=[]);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $result;
        if(empty($result)){
            return return_format($alllist,0,lang('success'));
        }else{
            return return_format($alllist,0,lang('success'));
        }
    }
    /**
     * 查询一条订单信息
     * @Author yr
     * @param orderid int 订单Id
     * @param ordernum int 订单号
     * @param userid int 用户id 用户ID
     * @return array
     *
     */
    public function getUnpaidOrder($userid,$ordernum){
        //先判断用户id是否合法
        if(!is_intnum($userid) ||  !is_numeric($ordernum) ){
            return return_format($this->str,34042,lang('param_error'));
        }
        //实例化订单表
        $ordemodel = new Ordermanage;
        $result = $ordemodel->getUnpaidOrderInfo($userid,$ordernum);
        if(empty($result)){
            return return_format($result,34043,lang('34043'));
        }elseif($result['orderstatus'] !== 0){
            return return_format($this->str,34044,lang('34044'));
        }else{
            //实例化学生账户表
            $studentfund = new Studentfunds;
            $account = $studentfund->getUserBalance($userid);
            $data['orderlist'] = $result;
            $data['account'] = $account;
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 取消订单
     * @Author yr
     * @param ordernum int 订单号
     * @return array
     *
     */
    public function cancelOrder($ordernum){
        //先判断订单号是否合法
        if(!is_numeric($ordernum) ){
            return return_format($this->str,34045,lang('param_error'));
        }
        //实例化订单表
        $ordermodel = new Ordermanage;
        $result = $ordermodel->getOrderInfo($ordernum);
        //班级满员人数
        $fullpeople = $result['fullpeople'];
        if(empty($result)){
            return return_format($result,34046,lang('34046'));
        }elseif($result['orderstatus'] !== 0){
            return return_format($this->str,34047,lang('34047'));
        }else{
            //直播课和录播课
            switch ($result['coursetype']){
                case 1://录播课
                    $where = [
                        'ordernum' => $ordernum,
                    ];
                    $data = [
                        'orderstatus'=> $this->cancelstatus
                    ];
                    Db::startTrans();
                    $res = $ordermodel->updateBalanceInfo($where,$data);
                    $fundsmodel = new Studentfunds();
                    $fundswhere['studentid'] = $result['studentid'];
                    if($result['balance'] > 0){
                        $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
                    }else{
                        $updatefunds_res = 1;
                    }
                    if($res && $updatefunds_res>=0){
                        Db::commit();
                        return return_format('',0,lang('success'));
                    }else{
                        Db::rollback();
                        return return_format('',34049,lang('error'));
                    }
                    break;
                case 2://直播课
                    $result = $this->delCancerOrder($result,$fullpeople);
                    return $result;
                    break;
                default:
                    return return_format('',34050,lang('param_error'));
            }


        }
    }
    /**
     * 大小班下单统一调用方法
     * @param $data 订单数据
     * @param $schedulingid 排课表id
     * @param $fullpeople 满员人数
     * @Author yr
     * @param ordernum int 订单号
     * @return array
     *
     */
    public function dealApplyStatus($data,$schedulingid,$fullpeople){
        //实例化模型
        $ordermodel = new Ordermanage;
        $schedumodel = new Scheduling;
        //查询订单表该课时报名人数
        $applynum = $ordermodel->getApplyPeople($schedulingid);
        //小班课满员人
            $totalpeople = $applynum+1;
            if($totalpeople == 1&& $fullpeople !==1){
                //把班级状态修改为已招生
                $update_data = [
                    'classstatus' => $this->classstatus[1]
                ];
                $where = [
                    'id' => $schedulingid
                ];
            }elseif($totalpeople == $fullpeople){
                //把班级状态修改为已满员
                $update_data = [
                    'classstatus' => $this->classstatus[3]
                ];
                $where = [
                    'id' => $schedulingid
                ];
            }elseif($totalpeople > $fullpeople){
                return return_format('',34051,lang('34051'));
            }else{
                //事务开始
                Db::startTrans();
                //直接插入订单表
                $insertid = $ordermodel->gotoOrder($data);
                //拼装套餐使用详情
                if($data['usestatus'] == 1){
                    //计算套餐使用数量
                    $unitprice  = $schedumodel->getCourserById($schedulingid)['price'];
                    $discountprice = $data['originprice'] - $data['amount'];
                    $usenum = intval($discountprice / $unitprice);
                    $detailmodel = new CoursepackageUseDetail;
                    $info['ordernum'] = $data['ordernum'];
                    $info['packageid'] = $data['packageid'];
                    $info['packagegiftid'] = $data['packagegiftid'];
                    $info['type'] = $data['type'];
                    $info['usenum'] =  $usenum;
                    $info['addtime'] = time();
                    $info['packageuseid'] = $data['packageuseid'];
                    $detail_result = $detailmodel->insertData($info);
                    if(!$detail_result){
                        Db::rollback();
                        return return_format('',34053,'error');
                    }
                }else{
                    $detail_result = 1;
                }
                if($insertid>0 &&$detail_result){
                    Db::commit();
                    $data = $ordermodel->getOrderdata($insertid);
                    return return_format($data,0,lang('success'));
                }else{
                    Db::rollback();
                    return return_format('',34052,'error');
                }
            }
            //事务开始
            Db::startTrans();
            $update_result = $schedumodel->updateClassStatus($where,$update_data);
            $insert_result = $ordermodel->gotoOrder($data);
            //拼装套餐使用详情
            if($data['usestatus'] == 1){
                //计算套餐使用数量
                $unitprice  = $schedumodel->getCourserById($schedulingid)['price'];
                $discountprice = $data['originprice'] - $data['amount'];
                $usenum = intval($discountprice / $unitprice);
                $detailmodel = new CoursepackageUseDetail;
                $info['ordernum'] = $data['ordernum'];
                $info['packageid'] = $data['packageid'];
                $info['packagegiftid'] = $data['packagegiftid'];
                $info['type'] = $data['type'];
                $info['usenum'] =  $usenum;
                $info['addtime'] = time();
                $info['packageuseid'] = $data['packageuseid'];
                $detail_result = $detailmodel->insertData($info);
                if(!$detail_result){
                    Db::rollback();
                    return return_format('',34053,'error');
                }
            }else{
                $detail_result = 1;
            }
            if($update_result>=0 && $insert_result &&$detail_result){
                Db::commit();
                return return_format($data,0,lang('success'));
            }else{
                Db::rollback();
                return return_format('',34053,'error');
            }

    }
    /**
     * 微信查询订单状态
     * @param $data 订单数据
     * @param $ordernum 排课表id
     * @Author yr
     * @return array
     *
     */
    public function queryOrder($ordernum){
        if(empty($ordernum)){
            return return_format('',34054,lang('34054'));
        }
        $orderobj = new Wxpay;
        $result = $orderobj->orderQuery($ordernum);
        $data['trade_state'] = $result['trade_state'];
        return return_format($data,0,lang('success'));
    }
    /**
     * 查询订单详情和支付状态
     * @param $data 订单数据
     * @param $ordernum 排课表id
     * @Author yr
     * @return array
     *
     */
    public function orderSuccess($ordernum){
        if(empty($ordernum)){
            return return_format('',34055,lang('34055'));
        }
        $orderobj = new Ordermanage;
        $result = $orderobj->getOrderInfo($ordernum);
        if(empty($result)){
            return return_format('',34056,lang('34056'));
        }
        return return_format($result,0,lang('success'));
    }
    /**
     * 大小班取消订单统一修改订单状态方法
     * @param $result 订单数据
     * @param $fullpeople 满员人数
     * @Author yr
     * @return array
     *
     */
    private function delCancerOrder($result,$fullpeople){
        //实例化模型
        $ordermodel = new Ordermanage;
        $schedumodel = new Scheduling;
        $fundsmodel = new Studentfunds;

        //查询订单表该课时报名人数
        $applynum = $ordermodel->getApplyPeople($result['schedulingid']);
        //小班课满员人数12人 大班课1000人
        $totalpeople = $applynum - 1;
        //拼装修改订单表条件
        $order_where = [
            'ordernum' => $result['ordernum'],
        ];
        $order_data = [
            'orderstatus'=> $this->cancelstatus
        ];

        //查询该班级是否为授课中和已经结束 4授课中 5已结束(已取消)
        if($result['classstatus'] == 4 ||$result['classstatus'] == 5){

            $res = $ordermodel->updateBalanceInfo($order_where,$order_data);
            $fundswhere['studentid'] = $result['studentid'];
            if($result['balance'] > 0){
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
            }else{
                $updatefunds_res = 1;
            }
            if($res&&$updatefunds_res>=0){
                return return_format('',0,lang('success'));
            }else{
                return return_format('',34057,lang('error'));
            }
        }
        if($totalpeople == 0){
            //把班级状态修改为未招生
            $update_data = [
                'classstatus' => $this->classstatus[0]
            ];

        }elseif($totalpeople < $fullpeople){
            //把班级状态修改为已招生
            $update_data = [
                'classstatus' => $this->classstatus[1]
            ];
        }else{
            $res = $ordermodel->updateBalanceInfo($order_where,$order_data);
            $fundswhere['studentid'] = $result['studentid'];
            if($result['balance'] > 0){
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
            }else{
                $updatefunds_res = 1;
            }
            if($res&&$updatefunds_res>=0){
                return return_format('',0,lang('success'));
            }else{
                return return_format('',34058,lang('error'));
            }
        }
        //事务开始
        Db::startTrans();
        //修改订单表状态
        $order_result = $ordermodel->updateBalanceInfo($order_where,$order_data);
        $where = [
            'id' => $result['schedulingid']
        ];
        //修改班级状态
        $schedu_result = $schedumodel->updateClassStatus($where,$update_data);
        $fundswhere['studentid'] = $result['studentid'];
        if($result['balance'] > 0){
            $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
        }else{
            $updatefunds_res = 1;
        }
        if($order_result && $schedu_result>=0 &&$updatefunds_res>=0){
            Db::commit();
            return return_format('',0,lang('success'));
        }else{
            Db::rollback();
            return return_format('',34059,lang('error'));
        }

    }
    /**
     * 录播课课程购买
     * @param $data 课程数据
     * @Author yr
     * @return array
     *
     */
        public function dealFreeOrder($data){
        //实例化模型
        $ordermodel = new Ordermanage;
        //计算套餐使用数量
        $coursemodel = new Curriculum;

        //事务开始
        Db::startTrans();
        $insert_result = $ordermodel->gotoOrder($data);
        if($data['usestatus'] == 1){
            $where = ['id'=>$data['curriculumid']];
            $unitprice  = $coursemodel->getCourserById($where)['price'];
            $discountprice = $data['originprice'] - $data['amount'];
            $usenum = intval($discountprice/$unitprice);
            $detailmodel = new CoursepackageUseDetail;
            $info['ordernum'] = $data['ordernum'];
            $info['packageid'] = $data['packageid'];
            $info['packagegiftid'] = $data['packagegiftid'];
            $info['type'] = $data['type'];
            $info['usenum'] = $usenum;
            $info['addtime'] = time();
            $info['packageuseid'] = $data['packageuseid'];
            $detail_result = $detailmodel->insertData($info);
            if(!$detail_result){
                Db::rollback();
                return return_format('',34053,'error');
            }
        }else{
            $detail_result = 1;
        }
        if( $insert_result&&$detail_result){
            Db::commit();
            return return_format($data,0,lang('success'));
        }else{
            Db::rollback();
            return return_format('',34053,'error');
        }

    }


}
