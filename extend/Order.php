<?php
use think\Controller;
use think\Session;
use app\student\model\Ordermanage;
use app\student\model\Studentfunds;
use app\student\model\Studentpaylog;
use app\student\model\Recharge;
use app\student\model\Scheduling;
use app\student\model\Organaccount;
use think\Db;
use think\log;
use app\student\model\Organconfig;
/**
 * 处理订单
 * 根据不用的支付方式选择不同的订单方式
 *
 */

class Order{
    //订单完成状态
    protected $completestatus = 20;
    //订单取消状态
    protected $cancelstatus = 10;
    //已经下单状态
    protected $orderstatus = 0;
    //订单类型 下单
    protected $paystatus = [1,2,3];//类型 1下单2充值3.购买套餐
    //支付类型
    protected $paytype = 1;
    //充值成功状态
    protected $rechargestatus = 1;
    //定义大班课报名人数
    protected $maxapply = 1000;
    //定义小班课报名人数
    protected $minapply = 12;
    //班级状态 0 未招生 1已招生 2已成班 3已满员 4授课中 5已结束(已取消)
    protected  $classstatus = [0,1,2,3,4,5];
    /**
     * 初始化操作
     * @access protected
     */
    protected function _initialize()
    {
        parent::_initialize();

    }
    /**
     * [混合支付第一步,可用余额放入冻结金额里面]
     * @Author yr
     * @DateTime 2018-04-29T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function delFreeze($studentid,$ordernum,$usablemoney,$type=0){

        //查看该学生的余额是否足以支付
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($studentid);
        if($fundsinfo['usablemoney'] !== $usablemoney){
            return return_format('',34201,lang('34201'));
        }
        $data = [
            'usablemoney'=>$fundsinfo['usablemoney'] - $usablemoney,
            'frozenmoney'=>$usablemoney+$fundsinfo['frozenmoney'],
        ];
        $where['studentid'] = $studentid;
        //事务开启
        Db::startTrans();
        //冻结余额,同时修改订单balance字段
        $freeze_res = $fundsmodel->delFreezeMoney($data,$where);
        $balancedata = [
            'balance' => $usablemoney
        ];
        $balancewhere['ordernum'] = $ordernum;
        //先查询订单信息
        if($type == 1){
            $ordermodel = new \app\student\model\Coursepackageorder();
            $balance_res = $ordermodel->updateData($balancewhere,$balancedata);
        }else{
            $ordermodel = new Ordermanage;
            $balance_res = $ordermodel->updateBalanceInfo($balancewhere,$balancedata);
        }

        if($freeze_res>=0 && $balance_res>=0){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return return_format('',34202,lang('34202'));
        }
    }
    /**
     * [套餐余额支付 balancePay]
     * @Author yr
     * @DateTime 2018-04-27T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function packageBalancePay($studentid,$orderinfo,$amount,$usablemoney,$paytype){
        //先查询订单信息
        $ordemodel = new \app\student\model\Coursepackageorder();
        if(empty($orderinfo) ||$orderinfo['orderstatus'] !== $this->orderstatus){
            return return_format('',34203,lang('34203'));
        }
        if($orderinfo['amount'] !== $amount){
            return return_format('',34204,lang('34204'));
        }
        $amount = $orderinfo['amount'];
        //查看该学生的余额是否足以支付
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($studentid);
        if($fundsinfo['usablemoney'] < $amount){
            return return_format('',34206,lang('34206'));
        }
        //开启事务
        Db::startTrans();
        //修改学生资金表 余额减 消费加
        $totalmoney = $fundsinfo['totalpay'];
        $totalmoney += $amount;
        $balance = $fundsinfo['usablemoney'] - $amount;
        $fundsdata = [
            'totalpay'=> $totalmoney,
            'usablemoney'=> $balance,
        ];
        //1.修改学生账户表
        $studentfunds_res  = $fundsmodel->updateData($studentid,$fundsdata);
        //2.修改订单表状态和支付方式
        $condition = [
            'ordernum' => $orderinfo['ordernum']
        ];
        $orderdata = [
            'paytype' => 1,
            'orderstatus'=> $this->completestatus
        ];
        $order_res =  $ordemodel->updateData($condition,$orderdata);
        //3. 插入资金流水表
        $paylog['studentid'] = $studentid;
        $paylog['paynum'] = $amount;
        $paylog['paytype'] = $paytype;
        $paylog['paytime'] = time();
        $paylog['out_trade_no'] = $orderinfo['ordernum'];
        $paylog['paystatus'] = 3;
        $paylogmodel = new Studentpaylog;
        $paylog_res =  $paylogmodel->insert($paylog);
        //4.增加机构账户金额
        $organaccmodel = new Organaccount;
        $account_res = $organaccmodel->updateTradeflow($amount);
        //$paylog_res =  $paylogmodel->insert($paylog);
        //5套餐使用表里添加记录
        //计算套餐到期时间
        if($orderinfo['efftype'] ==1){
            $packageendtime = $orderinfo['effendtime'];
        }else{
            $nowtime = time();
            $packageendtime = $nowtime + 86400*$orderinfo['efftime'];
        }
        if(empty($orderinfo['packagegiftid'])){
            $data[0] = [
                'packageid' => $orderinfo['packageid'],
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 1,
                'surplus' => $orderinfo['bughour'],
                'total' => $orderinfo['bughour'],
                'endtime' => $packageendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
        }else{
            //如果该套餐赠送课时 同时插入两条信息
            $data[0] = [
                'packageid' => $orderinfo['packageid'],
                'packagegiftid' => 0,
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 1,
                'surplus' => $orderinfo['bughour'],
                'total' => $orderinfo['bughour'],
                'endtime' => $packageendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
            //课时总数
            if(!empty($orderinfo['sendvideo'])){
                    $num = $orderinfo['sendvideo'];
            }else{
                    $num = $orderinfo['sendlive'];
            }
            //计算套餐赠送到期时间
            if($orderinfo['giftefftype'] ==1){
                $packagegiftendtime = $orderinfo['gifteffendtime'];
            }else{
                $nowtime = time();
                $packagegiftendtime = $nowtime + 86400*$orderinfo['giftefftime'];
            }
            $data[1] = [
                'packageid' => $orderinfo['packageid'],
                'packagegiftid' => $orderinfo['packagegiftid'],
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 2,
                'surplus' => $num,
                'total' => $num,
                'endtime' => $packagegiftendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
        }
        $packageusemodel  = new \app\student\model\Coursepackageuse();
        $packageuse_res = $packageusemodel->addData($data);
        if($studentfunds_res>=0&&$order_res&&$paylog_res&&$account_res>=0&&$packageuse_res>0)
        {
            Db::commit();
            $data['paytype'] = 1;
            return return_format($data,0,lang('success'));
        }
        else
        {
            Log::write('修改套餐订单状态时看哪一步出错'."1:".$studentfunds_res."2:".$order_res."3:".$paylog_res,'error'.'error'."5:".$account_res,'error');
            Db::rollback();
            return return_format('',34207,lang('34207'));
        }
    }
    /**
     * [余额支付 balancePay]
     * @Author yr
     * @DateTime 2018-04-27T19:29:24+0800
     * @return   [type]                   [description]
     */
    public function balancePay($studentid,$ordernum,$amount,$usablemoney,$paytype,$coursetype,$usepackage){
        //先查询订单信息
        $ordemodel = new Ordermanage;
        $orderinfo = $ordemodel->getUnpaidOrderInfo($studentid,$ordernum);
        if(empty($orderinfo) ||$orderinfo['orderstatus'] !== $this->orderstatus){
            return return_format('',34203,lang('34203'));
        }
        if($orderinfo['amount'] !== $amount){
            return return_format('',34204,lang('34204'));
        }
        $amount = $orderinfo['amount'];
        //查看该学生的余额是否足以支付
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($studentid);
        if($fundsinfo['usablemoney'] !== $usablemoney){
            return return_format('',34205,lang('34205'));
        }
        if($fundsinfo['usablemoney'] < $amount){
            return return_format('',34206,lang('34206'));
        }
        //开启事务
        Db::startTrans();
        //修改学生资金表 余额减 消费加
        $totalmoney = $fundsinfo['totalpay'];
        $totalmoney += $amount;
        $balance = $fundsinfo['usablemoney'] - $amount;
        $fundsdata = [
            'totalpay'=> $totalmoney,
            'usablemoney'=> $balance,
        ];
        $studentfunds_res  = $fundsmodel->updateData($studentid,$fundsdata);
        //修改订单表状态和支付方式
        $orderstatus = $this->completestatus;
        $order_res =  $ordemodel->updateOrderInfo($ordernum,$paytype,$orderstatus);
        // 插入资金流水表
        $paylog['studentid'] = $studentid;
        $paylog['courseid'] = $orderinfo['curriculumid'];
        $paylog['paynum'] = $amount;
        $paylog['paytype'] = $paytype;
        $paylog['paytime'] = time();
        $paylog['out_trade_no'] = $orderinfo['ordernum'];
        $paylog['paystatus'] = 1;
        $paylogmodel = new Studentpaylog;
        $paylog_res =  $paylogmodel->insert($paylog);
        if($coursetype == 1){
            $schedu_res = 1;
        }else{
            //修改班级表实际支付人数
            $schedumodel = new Scheduling;
            $schedu_res  = $schedumodel->setRealnumSum($orderinfo['schedulingid']);
        }
        //增加机构账户金额
        $organaccmodel = new Organaccount;
        $account_res = $organaccmodel->updateTradeflow($amount);
        //查看是否使用套餐支付
        if($usepackage){
            $detailmodel = new \app\student\model\CoursepackageUseDetail;
            $where = [
                'ordernum'=> $ordernum
            ];
            $field = 'packageid,packagegiftid,type,usenum,packageuseid';
            $useinfo = $detailmodel->getInfo($where,$field);
            $packageusemodel = new \app\student\model\Coursepackageuse();
            //批量修改学生套餐状态
                $where = ['id'=>$useinfo['packageuseid']];
                $usedata = [
                    'ifuse'=> 1,
                    'usetime'=> time(),
                    'surplus' => Db::raw('surplus-'. $useinfo['usenum'])
                ];
            $update_use_res = $packageusemodel->updateData($where,$usedata);
        }else{
            $update_use_res = 1;
        }
        if($studentfunds_res>=0&&$order_res&&$paylog_res&&$schedu_res&&$account_res>=0&&$update_use_res)
        {

            Db::commit();
            $data['paytype'] = 1;
            return return_format($data,0,lang('success'));
        }
        else
        {
            Log::write('修改订单状态时看哪一步出错'."1:".$studentfunds_res."2:".$order_res."3:".$paylog_res.'error'."4:".$schedu_res.'error'."5:".$account_res.'6:'.$update_use_res,'error');
            file_put_contents('order.txt',print_r("修改订单状态时看哪一步出错'.\"1:\".$studentfunds_res.\"2:\".$order_res.\"3:\".$paylog_res,'error'.\"4:\".$schedu_res,'error'.\"5:\".$account_res.'6:'.$update_use_res" ,true),FILE_APPEND) ;
            Db::rollback();
            return return_format('',34207,lang('34207'));
        }
    }
    /**
     * 异步回调调用订单处理
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param out_trade_no 订单号
     * @param  trade_no  交易凭证号
     * @param  buyer_logon_id 买家支付宝账号或者微信openid
     * @param  amount 订单金额
     * @param  paytype 1余额支付2微信支付 3支付宝支付 4银联支付
     * @return   array();
     * URL:/student/Myorder/delOrder
     */
    public function dealwithOrder($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype)
    {
        //先查询订单信息
        $ordemodel = new Ordermanage;
        $orderinfo = $ordemodel->getOrderInfo($out_trade_no);
        if(empty($orderinfo) ||$orderinfo['orderstatus'] !== $this->orderstatus){
            log::write('没有此订单信息39001');
            return false;
        }
        //查询学生资金表状态
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($orderinfo['studentid']);
        // 拼装第三方支付插入资金流水表数据
        $paylog = [
            'studentid' =>  $orderinfo['studentid'],
            'courseid' =>  $orderinfo['curriculumid'],
            'paynum' =>  $amount,
            'paytype' =>  $paytype,
            'paystatus' =>  $this->paystatus[0],
            'paytime' =>  time(),
            'out_trade_no' =>  $orderinfo['ordernum'],
            'trade_no' =>  $trade_no,
            'buyer_logon_id' =>  $buyer_logon_id,
        ];
            //开启事务
            Db::startTrans();
            //修改订单状态
            $update_order_res = $ordemodel->updateOrderInfo($out_trade_no,$paytype,$this->completestatus);
            //添加用户流水表状态
            $paylogmodel = new Studentpaylog;
            $paylog_res =  $paylogmodel->insert($paylog);
            //修改资金表状态
            if($orderinfo['balance'] >0){
                //如果是余额支付
                $frozenmoney = $fundsinfo['frozenmoney'] - $orderinfo['balance'];
                $fundsdata = [
                    'totalpay' => $fundsinfo['totalpay']+$orderinfo['amount'],
                    'frozenmoney' => $frozenmoney,
                ];
                $balancedata = [
                    'studentid' =>  $orderinfo['studentid'],
                    'courseid' =>  $orderinfo['curriculumid'],
                    'paynum' =>  $orderinfo['balance'],
                    'paytype' =>  1,
                    'paystatus' =>  $this->paystatus[0],
                    'paytime' =>  time(),
                    'out_trade_no' =>  $orderinfo['ordernum'],
                ];
                $paylog_res2 =  $paylogmodel->insert($balancedata);
            }else{
                //消费总额 第三方支付总额+余额支付
                $fundsdata = [
                    'totalpay' => $fundsinfo['totalpay']+$orderinfo['amount'],
                ];
                $paylog_res2 = 1;
            }
            $studentfunds_res  = $fundsmodel->updateData($orderinfo['studentid'],$fundsdata);

            //如果余额支付 资金流水表里插入一条消息

            //修改班级表实际支付人数 录播课不需要修改班级人数
            if($orderinfo['coursetype'] == 2){
                $schedumodel = new Scheduling;
                $schedu_res  = $schedumodel->setRealnumSum($orderinfo['schedulingid']);
            }else{
                $schedu_res = true;
            }
            //增加机构账户金额
            $organaccmodel = new Organaccount;
            $where = ['organid'=>1];
            $account_res = $organaccmodel->updateFlowOrUsable($where,$orderinfo['amount'],$amount);
        //查看是否使用套餐支付
        if($orderinfo['usepackage']){
            $detailmodel = new \app\student\model\CoursepackageUseDetail;
            $where = [
                'ordernum'=> $out_trade_no
            ];
            $field = 'packageid,packagegiftid,type,usenum,packageuseid';
            $useinfo = $detailmodel->getInfo($where,$field);
            $packageusemodel = new \app\student\model\Coursepackageuse();
                $where = [
                    'id' => $useinfo['packageuseid'],
                ];
                $usedata = [
                    'ifuse'=> 1,
                    'usetime'=> time(),
                    'surplus' => Db::raw('surplus-'. $useinfo['usenum'])
                ];

            $update_use_res = $packageusemodel->updateData($where,$usedata);
        }else{
            $update_use_res = 1;
        }
            if($studentfunds_res>=0&&$update_order_res&&$paylog_res&&$schedu_res&&$account_res&& $update_use_res&&$paylog_res2)
            {
                Db::commit();
                return true;
            }
            else
            {
                file_put_contents('coursebuy.txt',print_r('支付回调修改订单状态时看哪一步出错'."$out_trade_no".":1:".$studentfunds_res."2:".$update_order_res."3:".$paylog_res."4:".$schedu_res."5:".$account_res."6:".$update_use_res."7:".$paylog_res2.'error',true),FILE_APPEND) ;
                Db::rollback();
                return false;
            }
        }
    /**
     * 充值异步回调处理调用
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param out_trade_no 订单号
     * @param  trade_no  交易凭证号
     * @param  buyer_logon_id 买家支付宝账号或者微信openid
     * @param  amount 订单金额
     * @param  paytype 1余额支付2微信支付 3支付宝支付 4银联支付
     * @return   array();
     * URL:/student/Myorder/delOrder
     */
    public function dealwithRecharge($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype)
    {
        file_put_contents('recharge.txt',print_r('进入充值回调',true),FILE_APPEND) ;
        //先查询充值信息
        $rechargemodel = new Recharge;
        $rechargeinfo =  $rechargemodel->getRechargeByOrdernum($out_trade_no);
        //查询学生资金表状态
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($rechargeinfo['studentid']);
        // 拼装第三方支付插入资金流水表数据
        $paylog = [
            'studentid' =>  $rechargeinfo['studentid'],
            'paynum' =>  $amount,
            'paytype' =>  $paytype,
            'paystatus' =>  $this->paystatus[1],
            'paytime' =>  time(),
            'out_trade_no' =>  $out_trade_no,
            'trade_no' =>  $trade_no,
            'buyer_logon_id' =>  $buyer_logon_id,
        ];

            //开启事务
            Db::startTrans();
            //修改充值状态

            $update_order_res = $rechargemodel->updateRechargeStatus($out_trade_no,$this->rechargestatus);
            file_put_contents('recharge.txt',print_r($update_order_res,true),FILE_APPEND) ;
            //修改资金表状态
            $usablemoney = $fundsinfo['usablemoney'];
            //增加可用余额
            $usablemoney += $amount;
            $fundsdata = [
                'usablemoney' => $usablemoney,
            ];
            file_put_contents('recharge.txt',print_r($fundsdata,true),FILE_APPEND) ;
            $studentfunds_res  = $fundsmodel->updateData($rechargeinfo['studentid'],$fundsdata);
            file_put_contents('recharge.txt',print_r($studentfunds_res,true),FILE_APPEND) ;
            //添加用户流水表状态
            $paylogmodel = new Studentpaylog;
            file_put_contents('recharge.txt',print_r($paylog,true),FILE_APPEND) ;
            $paylog_res =  $paylogmodel->insert($paylog);
            //添加机构可用余额
            $accountmodel = new Organaccount();
            $update_account_res = $accountmodel->updateUsablemoney($amount);
            file_put_contents('recharge.txt',print_r($paylog_res,true),FILE_APPEND) ;
            if($studentfunds_res&&$update_order_res&&$paylog_res&&$update_account_res)
            {
                Db::commit();
                return true;
            }
            else
            {
                Log::write('充值回调修改状态时看哪一步出错'."1:".$studentfunds_res."2:".$update_order_res."3:".$paylog_res."4:".$update_account_res,'error');
                Db::rollback();
                return false;
            }
        }
    /**
     * 异步回调处理套餐
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param out_trade_no 订单号
     * @param  trade_no  交易凭证号
     * @param  buyer_logon_id 买家支付宝账号或者微信openid
     * @param  amount 订单金额
     * @param  paytype 1余额支付2微信支付 3支付宝支付 4银联支付
     * @return   array();
     */
    public function dealwithPackage($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype)
    {
        //先查询订单信息
        file_put_contents('packagebuy.txt',print_r($out_trade_no,true),FILE_APPEND);
        $ordemodel = new \app\student\model\Coursepackageorder();
        file_put_contents('packagebuy.txt',print_r($out_trade_no,true),FILE_APPEND);
        $orderinfo = $ordemodel->getPackageOrderInfo($out_trade_no);
        file_put_contents('packagebuy.txt',print_r($orderinfo,true),FILE_APPEND);
        if(empty($orderinfo) ||$orderinfo['orderstatus'] !== $this->orderstatus){
            log::write('没有此订单信息39001');
            return false;
        }
        file_put_contents('packagebuy.txt',print_r($orderinfo,true),FILE_APPEND);
        //查询学生资金表状态
        $fundsmodel  =  new Studentfunds;
        $fundsinfo = $fundsmodel->getUserBalance($orderinfo['studentid']);
        // 拼装第三方支付插入资金流水表数据
        $paylog = [
            'studentid' =>  $orderinfo['studentid'],
            'paynum' =>  $amount,
            'paytype' =>  $paytype,
            'paystatus' =>  $this->paystatus[2],
            'paytime' =>  time(),
            'out_trade_no' =>  $orderinfo['ordernum'],
            'trade_no' =>  $trade_no,
            'buyer_logon_id' =>  $buyer_logon_id,
        ];
        //开启事务
        Db::startTrans();
        //1修改订单状态
        $condition = ['ordernum'=> $out_trade_no];
        $orderdata = ['orderstatus' => $this->completestatus];
        $update_order_res = $ordemodel->updateData($condition,$orderdata);

        //2修改资金表状态
        //消费总额 第三方支付总额+余额支付
        //3添加用户流水表状态
        $paylogmodel = new Studentpaylog;
        $paylog_res =  $paylogmodel->insert($paylog);
        if($orderinfo['balance'] >0){
            //如果是余额支付
            $frozenmoney = $fundsinfo['frozenmoney'] - $orderinfo['balance'];//减去冻结金额
            $fundsdata = [
                'totalpay' => $fundsinfo['totalpay'] +$orderinfo['amount'],
                'frozenmoney' => $frozenmoney,
            ];
            $balancedata = [
                'studentid' =>  $orderinfo['studentid'],
                'paynum' =>  $orderinfo['balance'],
                'paytype' =>  1,
                'paystatus' =>  $this->paystatus[2],
                'paytime' =>  time(),
                'out_trade_no' =>  $orderinfo['ordernum'],
            ];
            $paylog_res2 =  $paylogmodel->insert($balancedata);
        }else{
            //消费总额 第三方支付总额+余额支付
            $fundsdata = [
                'totalpay' => $fundsinfo['totalpay']+$orderinfo['amount'],
            ];
            $paylog_res2 = 1;
        }

        $studentfunds_res  = $fundsmodel->updateData($orderinfo['studentid'],$fundsdata);
        //4增加机构账户金额
        $organaccmodel = new Organaccount;
        $where = ['organid'=>1];
        $account_res = $organaccmodel->updateFlowOrUsable($where,$orderinfo['amount'],$amount);
        //5套餐使用表里添加记录
        //计算套餐到期时间
        if($orderinfo['efftype'] ==1){
            $packageendtime = $orderinfo['effendtime'];
        }else{
            $nowtime = time();
            $packageendtime = $nowtime + 86400*$orderinfo['efftime'];
        }
        if(empty($orderinfo['packagegiftid'])){
            $data[0] = [
                'packageid' => $orderinfo['packageid'],
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 1,
                'surplus' => $orderinfo['bughour'],
                'total' => $orderinfo['bughour'],
                'endtime' => $packageendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
        }else{
            //如果该套餐赠送课时 同时插入两条信息
            $data[0] = [
                'packageid' => $orderinfo['packageid'],
                'packagegiftid' => $orderinfo['packagegiftid'],
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 1,
                'surplus' => $orderinfo['bughour'],
                'total' => $orderinfo['bughour'],
                'endtime' => $packageendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
            //课时总数
            $num = isset($orderinfo['sendvideo'])?$orderinfo['sendvideo']:$orderinfo['sendlive'];
            //计算套餐赠送到期时间
            if($orderinfo['giftefftype'] ==1){
                $packagegiftendtime = $orderinfo['gifteffendtime'];
            }else{
                $nowtime = time();
                $packagegiftendtime = $nowtime + 86400*$orderinfo['giftefftime'];
            }
            $data[1] = [
                'packageid' => $orderinfo['packageid'],
                'packagegiftid' => $orderinfo['packagegiftid'],
                'studentid' => $orderinfo['studentid'],
                'bugtime' => time(),
                'type' => 2,
                'surplus' => $num,
                'total' => $num,
                'endtime' => $packagegiftendtime,
                'ordernum' => $orderinfo['ordernum'],
            ];
        }
        file_put_contents('packagebuy.txt',print_r($data,true),FILE_APPEND);
        //添加套餐使用情况

        $packageusemodel  = new \app\student\model\Coursepackageuse();
        $packageuse_res = $packageusemodel->addData($data);
        if($studentfunds_res&&$update_order_res&&$paylog_res&&$account_res&&$packageuse_res&&$paylog_res2)
        {
            file_put_contents('packagebuy.txt',print_r('success',true),FILE_APPEND);
            Db::commit();
            return true;
        }
        else
        {
            file_put_contents('packagebuy.txt',print_r('支付回调修改订单状态时看哪一步出错'."1:".$studentfunds_res."2:".$update_order_res."3:".$paylog_res."4:".$account_res.'5'.$packageuse_res.'6'.$paylog_res2,true),FILE_APPEND) ;
            Db::rollback();
            return false;
        }
    }
    /**
     * 取消30分钟内 删选范围条件两天内的订单
     * @Author yr
     * @DateTime 2018-05-21T13:11:19+0800
     *
     */
    public function batchCancelOrders(){
        $today = strtotime(date("Y-m-d"),time());
        $preday = $today-86400;
        //查询出30分钟内所有未支付的订单
        $ordermodel = new Ordermanage;
        $nowtime  = time();
        $half_an_hour = $nowtime-30*60;
        $orderarr = $ordermodel->getUnpaidOrderArr($preday,$half_an_hour);
        $fundsmodel = new Studentfunds();
        foreach($orderarr as $k=>$value){
            //查看班级类型
            switch ($value['coursetype']){
                case 1:
                    //录播课
                    //修改订单
                    $where = [
                        'ordernum' => $value['ordernum'],
                    ];
                    $data = [
                        'orderstatus'=> $this->cancelstatus
                    ];
                    Db::startTrans();
                    //回滚冻结金额
                    $fundswhere['studentid'] = $value['studentid'];
                    if($value['balance'] !== '0.00'){
                        $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$value['balance']);
                    }else{
                        $updatefunds_res = 1;
                    }
                    //修改订单状态
                    $res = $ordermodel->updateBalanceInfo($where,$data);
                    if($res&&$updatefunds_res>=0){
                        //拼装消息数据
                        $msgList['studentid'] = $value['studentid'];
                        $msgList['ordernum'] = $value['ordernum'];
                        $msgList['orderid'] = $value['id'];
                        /*订单失效发送消息      start*/
                        $msgobj = new StudendMsg;
                        $msgobj->cancelOrderNotice($msgList);
                        /*------------end -------------*/
                        Db::commit();

                    }else{
                        Db::rollback();
                        log::write($value['ordernum'].'修改失败'.'1:'.$res."2:".$updatefunds_res);
                    }
                    break;
                case 2:
                    //直播课
                    $this->dealCancerOrder($value,$value['fullpeople']);
                    break;
                 default:
                    break;
            }

        }

    }
    /**
     * 取消30分钟内 删选范围条件两天内的套餐订单
     * @Author yr
     * @DateTime 2018-05-21T13:11:19+0800
     *
     */
    public function batchCancelPackageOrders()
    {
        $today = strtotime(date("Y-m-d"), time());
        $preday = $today - 86400;
        //查询出30分钟内所有未支付的订单
        $ordermodel = new \app\student\model\Coursepackageorder();
        $nowtime = time();
        $half_an_hour = $nowtime - 30 * 60;
        $orderarr = $ordermodel->getUnpaidOrderArr($preday, $half_an_hour);
        $fundsmodel = new Studentfunds();
        foreach ($orderarr as $k => $value) {
            //一对一课程直接修改订单状态
            //修改订单
            $where = [
                'ordernum' => $value['ordernum'],
            ];
            $data = [
                'orderstatus' => $this->cancelstatus
            ];
            Db::startTrans();
            //回滚冻结金额
            $fundswhere['studentid'] = $value['studentid'];
            if ($value['balance'] > 0) {
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere, $value['balance']);
            } else {
                $updatefunds_res = 1;
            }
            //修改订单状态
            $res = $ordermodel->updateData($where, $data);
            if ($res && $updatefunds_res >= 0) {
                Db::commit();

            } else {
                Db::rollback();
                log::write($value['ordernum'] . '修改失败');
            }

        }
    }
    /**
     * dealwithPackageUse 处理套餐过期时间
     * @Author yr
     * @DateTime 2018-05-21T13:11:19+0800
     *
     */
    public function dealwithPackageUse(){
        $packagemodel = new \app\student\model\Coursepackageuse();
        $timenow  = time();
        $where = [
            'endtime' => ['lt',$timenow],
            'ifuse' => ['neq' ,2]
        ];
        $field = 'id';
        $list = $packagemodel->getStudentPackage($where,$field);
        foreach($list as $k=>$v){
            $updatewhere = [
                'id' => $v['id'],
            ];
            $updatefield = [
                'ifuse' => 2
            ];
            $packagemodel->updateData($updatewhere,$updatefield);
        }
        return true;
    }

    /**
     * 大小班取消订单统一修改订单状态方法
     * @param $result 订单数据
     * @param $fullpeople 满员人数
     * @Author yr
     * @return array
     *
     */
    private function dealCancerOrder($result,$fullpeople){
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
        //拼装消息数据
        $msgobj = new StudendMsg;
        $msgList['studentid'] = $result['studentid'];
        $msgList['ordernum'] = $result['ordernum'];
        $msgList['orderid'] = $result['id'];
        //查询该班级是否为授课中和已经结束 4授课中 5已结束(已取消)
        if($result['classstatus'] == 4 ||$result['classstatus'] == 5){
            Db::startTrans();
            $res = $ordermodel->updateBalanceInfo($order_where,$order_data);
            $fundswhere['studentid'] = $result['studentid'];
            if($result['balance'] > 0){
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
            }else{
                $updatefunds_res = 1;
            }
            if($res && $updatefunds_res>=0){
                /*订单失效发送消息      start*/
                $msgobj->cancelOrderNotice($msgList);
                /*------------end -------------*/
                Db::commit();
                return true;
            }else{
                Db::rollback();
                return false;
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
            //事务开始
            Db::startTrans();
            $res = $ordermodel->updateBalanceInfo($order_where,$order_data);
            $fundswhere['studentid'] = $result['studentid'];
            if($result['balance']  !== '0.00'){
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
            }else{
                $updatefunds_res = 1;
            }
            if($res && $updatefunds_res){
                /*订单失效发送消息      start*/
                $msgobj->cancelOrderNotice($msgList);
                /*------------end -------------*/
                Db::commit();
                return true;
            }else{
                Db::rollback();
                return false;
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
        //回滚冻结金额
        $fundswhere['studentid'] = $result['studentid']['studentid'];
        if($result['balance'] > 0){
            $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere,$result['balance']);
        }else{
            $updatefunds_res = 1;
        }
        if($order_result && $schedu_result>=0 && $updatefunds_res>=0){

            /*订单失效发送消息      start*/
            $msgobj->cancelOrderNotice($msgList);
            /*------------end -------------*/
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return false;
        }

    }
}
?>