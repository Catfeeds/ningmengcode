<?php
namespace app\student\business;
use alipay\Alipaydeal;
use app\student\model\Category;
use app\student\model\Coursepackage;
use app\student\model\Coursepackageorder;
use app\student\model\Coursepackageuse;
use app\student\model\Curriculum;
use app\student\model\Ordermanage;
use app\student\model\Studentfunds;
use app\student\model\Studentinfo;
use think\Cache;
use wxpay\Wxpay;
use think\Db;

class PackageManage
{
    protected  $foo;
    protected  $str;
    protected  $completestatus = 20;//套餐订单完成状态
    protected  $cancelstatus = 10;//套餐订单取消状态
    protected $paytype = [
        '1','2','3','4','1,2','1,3','1,4'
    ];
    //支付组合类型
    protected $paymethod = [
        '1','2','3','4','1,2','1,3','1,4'
    ];
    protected $wxnotifyurl = '/admin/ServerNotice/wxPackageNotify'; //微信异步回调地址
    protected $alinotifyurl = '/admin/ServerNotice/aliPackageNotify'; //支付宝异步回调地址
    protected $wxappCourseurl = '/admin/ServerNotice/wxappCourseNotify';//微信app买课支付回调
    protected $wxappPackageyurl = '/admin/ServerNotice/wxappPackageNotify';//微信app套餐支付支付回调
    protected $wxappRechargeurl = '/admin/ServerNotice/wxappRechargeNotify';//微信app充值支付回调
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
    }
    /**
     * 获取套餐列表
     * @Author yr
     * @return array
     *
     */
    public function getPackageList($pagenum,$limit){
        if(!is_intnum($pagenum)){
            return return_format('',38000,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $packetmodel = new Coursepackage();
        $alllist['data'] = $packetmodel->getPackageList($limitstr);
        $total =   $packetmodel->getPackageCount();
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        return return_format($alllist,0,lang('success'));
    }
    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     *
     */
    public function getPackageDetail($packageid){
        if(!is_intnum($packageid)){
            return return_format('',38001,lang('param_error'));
        }
        $packetmodel = new Coursepackage();
        $result = $packetmodel->getPackageDetail($packageid);
        if($result['trialtype'] == 2){
            $ids = explode(',',$result['categoryapppint']);
            $catemodel = new Category();
            $where = ['id'=>['in',$ids]];
            $field = 'categoryname,id,fatherid';
            $categoryidsarr = $catemodel->getSelectInfo($where,$field);
            $arr = [];
            foreach($categoryidsarr as $key=>$value){
                if($value['fatherid'] == 0){
                    array_push($arr,$value['id']);
                }else{
                    array_push($arr,$value['id']);
                    array_push($arr,$value['fatherid']);
                }
            }
            $array = array_unique($arr);
            $where = ['id'=>['in',$array]];
            $field = 'categoryname,id,fatherid';
            $categoryidsarr = $catemodel->getSelectInfo($where,$field);
            $categoryarr = generateTree($categoryidsarr,'id');
        }else{
            $categoryarr = [];
        }
        if($result['trialtype'] == 3){
            $coursearr = explode(',',$result['curriculumids']);
            $curriculummodel = new Curriculum();
            $where = ['id'=>['in', $coursearr]];
            $courselist =  $curriculummodel->getSelectData($where);

        }else{
           $courselist = [];
        }
        $result['categoryarr'] = $categoryarr;
        $result['courselist'] = $courselist;
        return return_format($result,0,lang('success'));
    }
    /**
     * 套餐统一下单
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param studentid int 学生用户id
     * @param  amount  float 订单金额
     * @param  int  ordersource    下单渠道 1web 2app
     * @return   array();
     */
    public function gotoOrder($studentid,$packageid,$ordersource){
        if(!is_intnum($studentid) || !is_intnum($ordersource)  || !is_intnum($packageid)){
            return return_format($this->str,38110,lang('param_error'));
        }

        //拼装套餐订单信息
        $packagemodel = new Coursepackage;
        $packageinfo = $packagemodel->getPackageDetail($packageid);
        $time = time();
        if($packageinfo['efftype'] == 1 && $packageinfo['effendtime'] <= $time){
            return return_format('',38112,'该套餐已经过期,不能购买');
        }
        $limitbuy = $packageinfo['limitbuy'];//限购次数
        //查看该套餐购买的限制条件
        if($limitbuy !=0){
            //查看已购买的套餐次数
            $ordermodel = new Coursepackageorder;
            $where = [
                'studentid' => $studentid,
                'orderstatus' => $this->completestatus,
                'packageid' => $packageid
            ];
            $buycount = $ordermodel->getBuyCount($where);
            if($buycount + 1> $limitbuy){
                return return_format('',38112,"该套餐最多购买$limitbuy".'次');
            }
        }
        $ordernum = getOrderNum();
        //拼装订单信息
        $data['setmeal'] = $packageinfo['setmeal'];
        $data['packagegiftid'] = $packageinfo['packagegiftid'];
        $data['packageid'] = $packageid;
        //暂且不做优惠券
        $data['amount'] = $packageinfo['setprice'];
        $data['ordersource'] = $ordersource;
        $data['ordernum'] = $ordernum;
        $data['ordertime'] = time();
        $data['studentid'] = $studentid;
        $ordermodel = new Coursepackageorder;
        $result = $ordermodel->addOrder($data);
        $info['ordernum'] = $data['ordernum'];
        if($result['code'] !==0){
            return return_format('',$result['code'],$result['msg']);
        }else{
            return return_format($info,0,lang('success'));
        }
    }
    /**
     * 学生显示套餐详情
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     */
    public function showOrderDetail($ordernum){
        if(empty($ordernum)){
            return return_format('',34021,lang('param_error'));
        }
        $ordermodel = new Coursepackageorder;
        $orderinfo = $ordermodel->getPackageOrderInfo($ordernum);
        if(empty($orderinfo)){
            return return_format('',34022,lang('34022'));
        }else{
            return return_format($orderinfo,0,lang('success'));
        }

    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  paytype支付方式 1,2 余额支付在前，其他支付在后面
     * @param  studentid 学生id
     * @return   array();
     */
    public function gotoPay($studentid,$ordernum,$paytype,$type=2){

        if(!in_array($paytype,$this->paymethod)){
            return return_format('',34026,lang('34026'));
        }
        if(!is_intnum($studentid)){
            return return_format('',34027,lang('param_error'));
        }
        $studentinfomodel = new Studentinfo();
        $mobile = $studentinfomodel->getStudentInfo($studentid)['mobile'];
        $openid = Cache::get($mobile.'-'.'openid');//获取openid
        $ordemodel = new Coursepackageorder();
        $orderinfo = $ordemodel->getPackageOrderInfo($ordernum);
        if($orderinfo['orderstatus'] ==10){
            return return_format('',34031,lang('34031'));
        }
        if($orderinfo['orderstatus'] ==20){
            return return_format('',34032,lang('34032'));
        }
        $amount = $orderinfo['amount'];
        $subject = $orderinfo['setmeal'];
        $body = $orderinfo['setmeal'];
        $studentfundmodel = new Studentfunds;
        $usablemoney = $studentfundmodel->getUserBalance($studentid)['usablemoney'];
        if(!in_array($paytype,$this->paytype)){
            return return_format('',34033,lang('34026'));
        }
        //根据type跳到不同的支付方式
        $paytype = explode(',',$paytype);
        $length = count($paytype);
        //如果length为1是单一支付方式
        if($length == 1){
            $paytype = intval($paytype[0]);
            //查看此订单是否是混合支付.如果是混合支付
            if($orderinfo['balance'] !== '0.00'){
                //该笔第三方支付的价钱是订单实际价格-余额支付的部分
                $amounts = $orderinfo['amount'] *100;
                $usablemoneys = $orderinfo['balance']*100;
                $amount  = (float)($amounts - $usablemoneys)/100;
            }
            switch ($paytype){
                case 1:
                    //余额支付 直接扣款
                    $orderclass = new \Order();
                    $result = $orderclass->packageBalancePay($studentid,$orderinfo,$amount,$usablemoney,$paytype);
                    return $result;
                    break;
                case 2://微信支付
                    $wxpayobj = new Wxpay;
                    $notifyurl = config('param.server_url') . $this->wxnotifyurl;//异步回调地址 扫码和公众号支付
                    if($type == 1){//app支付
                        $appnotifyurl = config('param.server_url') . $this->wxappPackageyurl;
                        $result = $wxpayobj->appWxpay($ordernum, $subject, $amount, $body, $appnotifyurl);
                        if ($result['result_code'] !== 'SUCCESS') {
                            return return_format('', 34034, lang('33031'));
                        }
                        $data['data'] = $result;
                        return return_format($data, 0, lang('success'));
                    }elseif($type == 2){//扫码支付
                        $result = $wxpayobj->createWxPayUrl($ordernum,$subject,$amount,$body,$notifyurl);
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
                        file_put_contents('wxgzh.txt',print_r('提交前检查参数:'.$ordernum.$subject.$amount.$body.$notifyurl,true),FILE_APPEND) ;
                        $result = $wxpayobj->jsapiWxpay($ordernum,$subject,$amount,$body,$notifyurl,$openid);
                        file_put_contents('wxgzh.txt',print_r($result,true),FILE_APPEND) ;
                        if(empty($result)){
                            return return_format('',34035,lang('支付失败'));
                        }
                        $data = json_decode($result,true);
                        file_put_contents('wxgzh.txt',print_r($data,true),FILE_APPEND) ;
                        return return_format($data,0,lang('success'));

                    }

                    break;
                case 3: //支付宝支付
                    $alipayobj = new Alipaydeal();

                    if($type == 1){//app支付
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=1";
                        $returnurl = config('param.http_name') . $_SERVER['HTTP_HOST'] . $returnurl;
                        $notifyurl = config('param.server_url') . $this->alinotifyurl;
                        $res = $alipayobj->appAlipay($ordernum,$subject,$amount,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data, 0, lang('success'));
                    }else{//扫码支付
                        $returnurl = "/web/#/paymentSuccess?type=0&order=$ordernum&buyClass=1";
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
                $result = $ordermodel->delFreeze($studentid,$ordernum,$usablemoney,$businiesstype=1);
                if($result !=1){
                    return $result;
                }
                $orderinfo['balance'] = $usablemoney;//如果修改数据库成功，重新赋值
            }
            $mixtype = $paytype[1];
            switch($mixtype){
                case 2:
                    //微信支付
                    $wxpayobj = new Wxpay;
                    $amounts = $amount *100;
                    $usablemoneys = $orderinfo['balance']*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;
                    if($type == 1){//app支付
                        $appnotifyurl = config('param.server_url').$this->wxnotifyurl;
                        $result = $wxpayobj->appWxpay($ordernum, $subject,$price,$body, $appnotifyurl);
                        if ($result['result_code'] !== 'SUCCESS') {
                            return return_format('', 34034, lang('33031'));
                        }
                        $data['data'] = $result;
                        return return_format($data, 0, lang('success'));
                    }elseif($type == 2){//扫码支付
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
                        $result = $wxpayobj->jsapiWxpay($ordernum,$subject,$price,$body,$notifyurl,$openid);
                        if(empty($result)){
                            return return_format('',34035,lang('支付失败'));
                        }
                        $data = json_decode($result,true);
                        return return_format($data,0,lang('success'));
                    }
                    break;

                case 3:
                    //支付宝支付
                    $amounts = $amount *100;
                    $usablemoneys = $orderinfo['balance']*100;
                    $price  = (float)($amounts - $usablemoneys)/100;
                    $alipayobj = new Alipaydeal;
                    if($type == 1){//app支付
                        $returnurl = "/organweb#/paymentSuccess?type=0&order=$ordernum";
                        $returnurl = config('param.http_name') . $_SERVER['HTTP_HOST'] . $returnurl;
                        $notifyurl = config('param.server_url') . $this->alinotifyurl;
                        $res = $alipayobj->appAlipay($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data, 0, lang('success'));
                    }else{
                        $alipayobj = new Alipaydeal;
                        $returnurl = "/web#/paymentSuccess?type=0&order=$ordernum";
                        $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                        $notifyurl = config('param.server_url').$this->alinotifyurl;
                        $res =  $alipayobj->createPayRequest($ordernum,$subject,$price,$body,$returnurl,$notifyurl);
                        $data['data'] = $res;
                        return return_format($data,0,lang('success'));
                    }
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
    public function getPackageOrderList($userid,$pagenum,$limit)
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
        $ordermodel = new Coursepackageorder();
        $where = [
            'studentid' => $userid
        ];
        $result = $ordermodel->getStudentPackageOrder($where,$limitstr,$orderstatus=null);
        $total = $ordermodel->getStudentPackageOrderCount($where,$orderstatus=null);
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
     * 取消订单
     * @Author yr
     * @param ordernum int 订单号
     * @return array
     *
     */
    public function cancelOrder($ordernum)
    {
        //先判断订单号是否合法
        if (!is_numeric($ordernum)) {
            return return_format($this->str, 34045, lang('param_error'));
        }
        //实例化订单表
        $ordermodel = new Coursepackageorder();
        $result = $ordermodel->getPackageOrderInfo($ordernum);
        //班级满员人数
        if (empty($result)) {
            return return_format($result, 34046, lang('34046'));
        } elseif ($result['orderstatus'] !== 0) {
            return return_format($this->str, 34047, lang('34047'));
        } else {
            Db::startTrans();
            //修改套餐订单状态
            $where = [
                'ordernum' => $ordernum
            ];
            $data = ['orderstatus' => 10];
            $update_result = $ordermodel->updateData($where,$data);
            //回滚冻结金额
            $fundsmodel = new Studentfunds();
            $fundswhere['studentid'] = $result['studentid'];
            if ($result['balance'] > 0) {
                $updatefunds_res = $fundsmodel->updateFrozenOrUsable($fundswhere, $result['balance']);
            } else {
                $updatefunds_res = 1;
            }
            if($update_result&&$updatefunds_res>=0){
                Db::commit();
                return return_format('',0,lang('success'));
            }else{
                Db::rollback();
                return return_format('',38111,lang('error'));
            }
        }
    }
    /**
     * 查询套餐订单详情和支付状态
     * @param $data 订单数据
     * @param $ordernum 订单号
     * @Author yr
     * @return array
     *
     */
    public function orderSuccess($ordernum){
        if(empty($ordernum)){
            return return_format('',38112,lang('34055'));
        }
        $orderobj = new Coursepackageorder();
        $result = $orderobj->getPackageOrderInfo($ordernum);
        if(empty($result)){
            return return_format('',38113,lang('34056'));
        }
        return return_format($result,0,lang('success'));
    }
    /**
     * 查询我的套餐使用列表
     * @param $studentid int 学生id
     * @param $pagenum  int  分页页数
     * @param $status  int  0待使用 1已使用 2已过期
     * @Author yr
     * @return array
     *
     */
    public function packageUseList($studentid,$pagenum,$status,$limit){
        if(!is_intnum($studentid) || !is_intnum($pagenum) || !is_intnum($limit) || !is_intnum($status)){
            return return_format('',38114,lang('param_error'));
        }
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        if($status == 3){
            $where = [
                'u.studentid' => $studentid,
                'u.delflag' => '1',
            ];
        }else{
            $where = [
                'u.ifuse' => $status,
                'u.studentid' => $studentid,
                'u.delflag' => '1',
            ];
        }

        $packagusemodel = new Coursepackageuse();
        $result['data'] = $packagusemodel->getDataByStatus($where,$limitstr);
        $total = $packagusemodel->getDataByStatusCount($where);
        $result['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        return return_format($result,0,lang('success'));

    }
    /**
     * 删除优惠券
     * @param $packageuseid int  优惠券的使用Id
     * @Author yr
     * @return array
     *
     */
    public function deletePackageUse($packageuseid){
        if(!is_intnum($packageuseid)){
            return return_format('',38115,lang('param_error'));
        }
        $packageusemodel = new Coursepackageuse;
        $where = [
            'id'=>$packageuseid
        ];
        $field = [
            'delflag' => 0
        ];
        $result = $packageusemodel->updateData($where,$field);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',38116,lang('error'));
        }
    }
    private  function getSubs($data){
            $categorymodel = new Category();
            $catearr = explode(',',$data);
            if(!empty($catearr)){
                   $result = [];
                   foreach($catearr as $k=>$v){
                           $catearr[$k] = $categorymodel->getSubs($v);
                          $result  = array_merge($result,$catearr[$k]);
                        }
       }
        return  $result;
    }
}
