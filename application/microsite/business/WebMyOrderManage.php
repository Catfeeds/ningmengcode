<?php
namespace app\student\business;
use app\student\model\Ordermanage;
use app\student\model\Studentfunds;
use app\student\model\Scheduling;
use app\student\model\Schedulingdeputy;
use Order;
use think\db;
use alipay\Alipaydeal;
use wxpay\Wxpay;
class WebMyOrderManage
{
    protected $foo;
    protected $str;
    protected $cancelstatus = 10;
    //定义大班课报名人数
    protected $maxapply = 1000;
    //定义小班课报名人数
    protected $minapply = 12;
    protected $paytype = [
        '0','1','2','3'
    ];
    //支付组合类型
    protected $paymethod = [
        '1','2','3','4','1,2','1,3','1,4'
    ];
    protected $wxnotifyurl = '/admin/ServerNotice/wxCourseNotify';
    protected $alinotifyurl = '/admin/ServerNotice/aliCourseNotify';
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
     * @param  int  organid  机构id
     * @return   array();
     */
    public function gotoOrder($studentid,$schedulingid,$amount,$ordersource,$organid,$originprice){
        if(!is_intnum($schedulingid) || !is_intnum($studentid) || !is_intnum($organid) || !is_intnum($ordersource) || empty($amount)){
            return return_format($this->str,34000,'参数异常');
        }
        //生成订单号
        $ordernum = getOrderNum();
        //根据排课id查询课程相关信息
        $schedumodel = new Schedulingdeputy;
        $data = $schedumodel->getCourserOne($schedulingid);
        $organid = $data['organid'];
        if(empty($data)){
            return return_format($this->str,34001,'没有此班级信息');
        }
        //如果是小班型和大班型 不能重复购买
        if($data['type'] !== 1){
            //查看班级状态 班级状态 0 未招生 1已招生 2已成班 3已满员 4授课中 5已结束(已取消)
            if($data['classstatus'] == 3){
                return return_format('',34100,'报名结束，人数已满');
            }elseif($data['classstatus'] == 4){
                return return_format('',34101,'报名结束，授课中');
            }elseif($data['classstatus'] == 5){
                return return_format('',34101,'报名结束');
            }
            $ordermodel  = new Ordermanage;
            $count = $ordermodel->getOrderBySchedu($studentid,$schedulingid);
            if($count>0){
                return return_format($this->str,34201,'请勿重复购买');
            }
        }
        //拼装订单信息
        $data['originprice'] = $originprice;
        //暂且不做优惠券
        $data['amount'] = $amount;
        $data['discount'] = $originprice-$amount;
        $data['ordersource'] = $ordersource;
        $data['ordernum'] = $ordernum;
        $data['ordertime'] = time();
        $data['studentid'] = $studentid;
        $data['vip'] = '0';
        $data['schedulingid'] = $schedulingid;
        $ordermodel = new Ordermanage;
        //满员人数
        $fullpeople =   $data['fullpeople'];
        //根据课程类型下单时如果是大小班课，需要对排课的班级状态修改
        switch ($data['type']){
            case 1:
                //一对一课程直接下单
                $insertid = $ordermodel->gotoOrder($data);
                if(is_numeric($insertid)||$insertid>0){
                    $data = $ordermodel->getOrderdata($insertid);
                    return return_format($data,0,'下单成功');
                }elseif(!is_numeric($insertid)){
                    return return_format('',34002,$insertid);
                }else{
                    return return_format($data,0,'下单失败');
                }
                break;
            case 2:
                //小班课
                    $result = $this->dealApplyStatus($data,$schedulingid,$organid,$fullpeople);
                    return $result;
                break;
            case 3:
                //大班课
                    $result = $this->dealApplyStatus($data,$schedulingid,$organid,$fullpeople);
                    return $result;
                break;
            default:
                 return return_format('',34003,'参数错误');
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
            return return_format('',34004,'参数错误');
        }
        $ordermodel = new Ordermanage;
        $orderinfo = $ordermodel->getOrderInfo($ordernum);
        if(empty($orderinfo)){
            return return_format('',34005,'没有此订单信息');
        }else{
            return return_format($orderinfo,0,'查询成功');
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
    public function gotoPay($studentid,$ordernum,$amount,$usablemoney,$paytype,$coursename,$classtype,$gradename){
        if(!in_array($paytype,$this->paymethod)){
            return return_format('',34142,'请选择正确的支付方式');
        }
        if(!is_intnum($studentid)){
            return return_format('',34143,'参数错误');
        }
        $ordemodel = new Ordermanage;
        $orderinfo = $ordemodel->getUnpaidOrderInfo($studentid,$ordernum);
        if(empty($orderinfo)){
            return return_format('',34042,'没有此订单信息');
        }
        if($orderinfo['classstatus'] == 4){
            return return_format('',34200,'该课程已在授课中,不能支付');
        }
        if($orderinfo['classstatus'] == 5){
            return return_format('',34200,'该课程已经结束,不能支付');
        }
        if($orderinfo['orderstatus'] !==0){
            return return_format('',34142,'订单状态有误');
        }
        //查看此订单是否是混合支付.如果是混合支付
        if($orderinfo['balance'] !== '0.00'){
            //该笔第三方支付的价钱是订单实际价格-余额支付的部分
            $amounts = $orderinfo['amount'] *100;
            $usablemoneys = $orderinfo['balance']*100;
            $amount  = (float)($amounts - $usablemoneys)/100;
        }else{
            $amount = $orderinfo['amount'];
        }
        //根据type跳到不同的支付方式
        $paytype = explode(',',$paytype);
        $length = count($paytype);
        //如果length为1是单一支付方式
        $subject = $coursename;
        $body = $this->classtype[$classtype].''.$gradename ;

        if($length == 1){
            $paytype = intval($paytype[0]);
            if(!in_array($paytype,$this->paytype)){
                return return_format('',34006,'请选择正确的支付方式');
            }
            switch ($paytype){
                case 1:
                    //余额支付 直接扣款
                    $orderclass = new Order;
                    $result = $orderclass->balancePay($studentid,$ordernum,$amount,$usablemoney,$paytype);
                    return $result;
                    break;
                case 2:
                    //微信支付
                    $wxpayobj = new Wxpay;
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;
                    $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$amount,$body,$notifyurl);
                    if($result['result_code'] !== 'SUCCESS'){
                        return return_format('',34026,'系统繁忙请稍后再试');
                    }
                    $url = $result['code_url'];
                    $image = get_base64_qrcode($url);
                    $data['type'] = 1;
                    $data['codeurl'] = $image;
                    return  return_format($data,0,'成功');
                    break;
                case 3:
                    //支付宝支付
                    $alipayobj = new Alipaydeal;
                    $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->createPayRequest($ordernum,$subject,$amount,$body,$returnurl,$notifyurl);
                    $data['data'] = $res;
                    return return_format($data,0,'成功');
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                default:
                    return return_format('',34007,'请选择正确的支付方式');
                    break;
            }
        }elseif($length == 2){
            //查看该余额是否正常
            $fundsmodel  =  new Studentfunds;
            $fundsinfo = $fundsmodel->getUserBalance($studentid);
            if($usablemoney !== '0.00'){
                $usablemoney = $fundsinfo['usablemoney'];
                if($fundsinfo['usablemoney'] !== $usablemoney){
                    return return_format('',34030,'账户异常');
                }
            }else{
                $usablemoney = $fundsinfo['frozenmoney'];
            }
            if($fundsinfo['usablemoney']>=$amount){
                return return_format('',34130,'余额足以支付,请选择单一付款方式');
            }
            //查看账户余额里是否有冻结的金额,如果没有
            if($usablemoney !== '0.00' && $fundsinfo['frozenmoney'] == '0.00'){
                //如果是混合支付,修改订单表balance,把余额支付的钱，放入冻结金额
                $ordermodel = new Order;
                $result = $ordermodel->delFreeze($studentid,$ordernum,$usablemoney);
                if($result !=1){
                    return $result;
                }
            }
            $mixtype = $paytype[1];
            switch($mixtype){
                case 2:
                    //微信支付
                    $wxpayobj = new Wxpay;
                    $amounts = $amount *100;
                    $usablemoneys = $usablemoney*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;
                    $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$price,$body,$notifyurl);
                    if($result['result_code'] !== 'SUCCESS'){
                        return return_format('',34026,'系统繁忙请稍后再试');
                    }
                    $url = $result['code_url'];
                    $image = get_base64_qrcode($url);
                    $data['type'] = 1;
                    $data['codeurl'] = $image;
                    return  return_format($data,0,'成功');
                    break;

                case 3:
                    //支付宝支付
                    $amounts = $amount *100;
                    $usablemoneys = $usablemoney*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $alipayobj = new Alipaydeal;
                    $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->createPayRequest($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                    $data['data'] = $res;
                    return return_format($data,0,'成功');
                    break;
                case 4:
                    //银联支付
                    echo "this is bankpay";
                    break;
                default:
                    return return_format('',34008,'请选择正确的支付方式');
                    break;
            }

        }else{
            return return_format('',34009,'参数异常');
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
            return return_format($this->str,34010,"参数错误");
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
        $result = $ordermodel->getStudentOrder($userid,$limitstr);
        $total = $ordermodel->studentOrderCount($userid);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $result;
        if(empty($result)){
            return return_format($alllist,0,'没有数据');
        }else{
            return return_format($alllist,0,"请求成功");
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
            return return_format($this->str,340113,"参数错误");
        }
        //实例化订单表
        $ordemodel = new Ordermanage;
        $result = $ordemodel->getUnpaidOrderInfo($userid,$ordernum);
        if(empty($result)){
            return return_format($result,34014,'没有此订单信息');
        }elseif($result['orderstatus'] !== 0){
            return return_format($this->str,34015,'订单状态有误');
        }else{
            //实例化学生账户表
            $studentfund = new Studentfunds;
            $account = $studentfund->getUserBalance($userid);
            $data['orderlist'] = $result;
            $data['account'] = $account;
            return return_format($data,0,'查询成功');
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
            return return_format($this->str,34016,"参数错误");
        }
        //实例化订单表
        $ordermodel = new Ordermanage;
        $result = $ordermodel->getOrderInfo($ordernum);
        if(empty($result)){
            return return_format($result,34017,'没有此订单信息');
        }elseif($result['orderstatus'] !== 0){
            return return_format($this->str,34018,'订单状态有误');
        }else{
            $fundsmodel  = new Studentfunds;
            $fundinfo = $fundsmodel->getUserBalance($result['studentid']);
            if($result['balance'] !=='0.00'){
                //查看是否是混合支付
                if($result['balance'] !== $fundinfo['frozenmoney']){
                    return return_format('',34027,'账户异常');
                }
            }
            $fundinfo['balance'] = $result['balance'];
            $fullpeople = $result['fullpeople'];
            //根据课程类型下单时如果是大小班课，需要对排课的班级状态修改 如果是混合支付 查看是否有冻结金额 如果有回滚账户余额
            switch ($result['type']){
                case 1:
                    //一对一课程直接修改订单状态
                    //修改订单
                    $where = [
                        'ordernum' => $ordernum,
                    ];
                    $data = [
                        'orderstatus'=> $this->cancelstatus
                    ];
                    Db::startTrans();
                    $fundswhere['studentid'] = $result['studentid'];
                    $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$fundinfo['balance']);
                    $res = $ordermodel->updateBalanceInfo($where,$data);
                    if($res&&$updatefunds_res>=0){
                        Db::commit();
                        return return_format('',0,'取消成功');
                    }else{
                        Db::rollback();
                        return return_format('',34019,'修改失败');
                    }
                    break;
                case 2:
                    //小班课 修改班级状态
                    $result = $this->delCancerOrder($result, $fullpeople,$fundinfo);
                    return $result;
                    break;
                case 3:
                    //大班课
                    $result = $this->delCancerOrder($result, $fullpeople,$fundinfo);
                    return $result;
                    break;
                default:
                    return return_format('',34020,'参数错误');
            }


        }
    }
    /**
     * 大小班下单统一调用方法
     * @param $data 订单数据
     * @param $schedulingid 排课表id
     * @param $organid 机构id
     * @param $fullpeople 满员人数
     * @Author yr
     * @param ordernum int 订单号
     * @return array
     *
     */
    public function dealApplyStatus($data,$schedulingid,$organid,$fullpeople){
        //实例化模型
        $ordermodel = new Ordermanage;
        $schedumodel = new Scheduling;
        //查询订单表该课时报名人数
        $applynum = $ordermodel->getApplyPeople($schedulingid,$organid);
        //小班课满员人数12人 大班课1000人
            $totalpeople = $applynum+1;
            if($totalpeople == 1 && $fullpeople !==1){
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
                return return_format('',34021,'报名结束，人数已满');
            }else{
                //直接插入订单表
                $insertid = $ordermodel->gotoOrder($data);
                if($insertid>0){
                    $data = $ordermodel->getOrderdata($insertid);
                    return return_format($data,0,'下单成功');
                }else{
                    return return_format('',34022,'下单失败');
                }
            }
            //事务开始
            Db::startTrans();
            $update_result = $schedumodel->updateClassStatus($where,$update_data);
            $insert_result = $ordermodel->gotoOrder($data);
            if($update_result>=0 && $insert_result){
                Db::commit();
                return return_format($data,0,'下单成功');
            }else{
                Db::rollback();
                return return_format('',34023,'下单失败');
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
            return return_format('',34200,'订单号参数异常');
        }
        $orderobj = new Wxpay;
        $result = $orderobj->orderQuery($ordernum);
        if(empty($result)){
            $result['trade_state'] = 'PAYERROR';
        }
        $data['trade_state'] = $result['trade_state'];
        return return_format($data,0,'查询成功');
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
            return return_format('',34200,'订单号参数异常');
        }
        $orderobj = new Ordermanage;
        $result = $orderobj->getOrderInfo($ordernum);
        if(empty($result)){
            return return_format('',34300,'没有此订单信息');
        }
        return return_format($result,0,'查询成功');
    }
    /**
     * 大小班取消订单统一修改订单状态方法
     * @param $result 订单数据
     * @param $fullpeople 满员人数
     * @Author yr
     * @return array
     *
     */
    private function delCancerOrder($result,$fullpeople,$fundinfo){
        //实例化模型
        $ordermodel = new Ordermanage;
        $schedumodel = new Scheduling;
        $fundsmodel = new Studentfunds;
        //查询订单表该课时报名人数
        $applynum = $ordermodel->getApplyPeople($result['schedulingid'],$result['organid']);
        //小班课满员人数12人 大班课1000人
        $totalpeople = $applynum - 1;
        //拼装修改订单表条件
        $order_where = [
            'ordernum' => $result['ordernum'],
        ];
        $order_data = [
            'orderstatus'=> $this->cancelstatus
        ];
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
            Db::startTrans();
            $fundswhere['studentid'] = $result['studentid'];
            $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$fundinfo['balance']);
            if($res&&$updatefunds_res>=0){
                Db::commit();
                return return_format('',0,'取消成功');
            }else{
                Db::rollback();
                return return_format('',34024,'修改失败');
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
        //账户资金回滚
        $fundswhere['studentid'] = $result['studentid'];
        $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$fundinfo['balance']);
        if($order_result && $schedu_result>=0&&$updatefunds_res>=0){
            Db::commit();
            return return_format('',0,'操作成功');
        }else{
            Db::rollback();
            return return_format('',34025,'操作失败');
        }

    }
}
