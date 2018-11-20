<?php
namespace app\student\business;
use app\student\model\City;
use app\student\model\Curriculum;
use app\student\model\Exercisesubject;
use app\student\model\Exercisesubjectoptions;
use app\student\model\Periodexerciseinfo;
use app\student\model\Lessons;
use app\student\model\Organ;
use app\student\model\Receive;
use app\student\model\Studentaddress;
use app\student\model\Studentattendance;
use app\student\model\Studentcategory;
use app\student\model\Studentchildtag;
use app\student\model\Studenthomework;
use app\student\model\Studenthomeworkanswer;
use app\student\model\Studentinfo;
use app\student\model\Studentfunds;
use app\student\model\Studentpaylog;
use app\student\model\Recharge;
use app\student\model\Organcollection;
use app\student\model\Studenttag;
use app\student\model\Teachercollection;
use app\student\model\Classcollection;
use app\student\model\Teacherinfo;
use think\Cache;
use think\Validate;
use wxpay\Wxpay;
use alipay\Alipaydeal;
use Messages;
use Think\Log;
use Verifyhelper;
use app\student\model\Message;
use think\Request;
session_start();
class UserManage
{
    protected $foo;
    protected $str;
    protected $expire = 900;
    protected $subject = '学生充值';
    protected $wxnotifyurl = '/admin/ServerNotice/wxRechargeNotify';
    protected $alinotifyurl = '/admin/ServerNotice/aliRechargeNotify';
    protected $aliappnotifyurl = '/admin/ServerNotice/aliappRechargeNotify';
    protected $wxappCourseurl = '/admin/ServerNotice/wxappCourseNotify';
    protected $wxappPackageyurl = '/admin/ServerNotice/wxappPackageNotify';
    protected $wxappRechargeurl = '/admin/ServerNotice/wxappRechargeNotify';
    //支付类型 2:微信支付3支付宝4银
    protected $paytype = [2,3,4];
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
        }
    /**
     * 获取学生个人资料信息
     * @Author yr
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function getStudentInfo($userid){
        //判断参数是否合法
        if(!is_intnum($userid)){
            return return_format($this->str,37000,lang('param_error'));
        }
        $studentmodel = new Studentinfo;
        $result = $studentmodel->getStudentInfo($userid);
        $birthstr = $result['birth'];
        $result['year'] = empty($birthstr)? '':date('Y',$birthstr);
        $result['month'] = empty($birthstr)? '':date('m',$birthstr);
        $result['date'] = empty($birthstr)?'':date('d',$birthstr);
        $result['birth'] = empty($birthstr)?'':date('Y-m-d',$result['birth']);
        unset($result['password']);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 获取学生个人资金流水
     * @Author yr
     * @param userid int 学生用户id
     * @param pagenum int 分页页数
     * @param limit int  每页条数
     * @return array
     *
     */
    public function getStudentPaylog($userid,$pagenum,$limit){
        //判断参数是否合法
        if(!is_intnum($userid) || !is_intnum($limit)){
            return return_format($this->str,37001,lang('param_error'));
        }
        //判断分页页数
        if(is_intnum($pagenum)>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit;
        }
        $studentmodel = new Studentpaylog;
        $studentinfo = $studentmodel->getStudentPaylog($userid,$limitstr);
        $total = $studentmodel->studentPaylogCount($userid);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $studentinfo;
        if(empty($alllist['data'])){
            return return_format([],0,lang('success'));
        }else{
            return return_format($alllist,0,lang('success'));
        }
    }
    /**
     * 修改学生信息
     * @Author yr
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function updateStudentInfo($data){
        //判断参数是否合法
        $data['id'] = $data['studentid'];
        $userid = $data['studentid'];
        $allowfield = ['imageurl','nickname','sex','country','province','city','birth','prphone','categoryid','tag','childtag'];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield);
        $newdata['profile'] = $data['profile'];
        $studentmodel = new Studentinfo;
        return  $studentmodel->updateStudentInfo($userid,$newdata);
    }
    /**
     * 找回密码，学生注册发送手机验证码
     * @Author yr
     * @param $mobile   手机号
     * @param $code     验证码code
     * @param $type     业务类型 1找回密码 2 注册
     * @param $sessionid 图形验证码标识
     * @return array
     *
     */
    public function sendMsg($mobile,$code,$sessionid,$type,$prphone)
    {
        if( empty($mobile) || empty($code)){
            return return_format($this->str,37008,lang('param_error'));
        }
        //删除redis里面的key
        Cache::rm('mobile'.$mobile);
        //判断图形验证码是否正确
        $verfyobj = new Verifyhelper;
        $verify_res = $verfyobj->check(strtolower($code),$sessionid);
        if(!$verify_res){
            return return_format($this->str,37009,lang('37009'));
        }
        //先判断手机号长度
        if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
            return return_format($this->str,37010,lang('37010'));
        }else{
            $studentmodel = new Studentinfo;
            $data = $studentmodel ->checkLogin($mobile);
            switch($type){
                case 1:
                    //找回密码
                    //如果长度没问题判断手机号是否存在,或者手机号被删除
                    if(!$data || $data['delflag'] == 0){
                        return return_format($this->str,37011,lang('37011'));
                    }else {
                        //判断用户登录状态，是否禁用
                        if ($data['status'] == 1) {
                            return return_format($this->str, 37012, lang('37012'));
                        }
                    }
                    $prphone = trim($data['prphone']);
                    break;
                case 2:
                    $prphone = isset($prphone)?$prphone:'86';
                    //学生注册
                    if($data){
                        return return_format('',37013,lang('37013'));
                    }
                break;
                default:
                    return return_format('',37014,lang('param_error'));
            }
                $mobile_code = rand(100000,999999);
                //此处调用短信接口,发送验证码
                $messageobj = new Messages;

         /*       echo $mobile;echo $prphone;echo $mobile_code;die();*/
                $send_result = $messageobj->sendMeg($mobile,$type=4,$params = [$mobile_code],$prphone);
                if($send_result['result'] == 0){
                    return return_format('',0,lang('success'));
                }else{
                    if($send_result['result'] == 1016){
                        return return_format('',37106,$send_result['errmsg']);
                    }
                    Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
                    return return_format('',37015,lang('37015'));
                }
        }

    }
    /**
     * 修改手机号发送手机验证码
     * @Author yr
     * @param $mobile
     * @param $prphone
     * @param $type 默认0  1:赠送免费课程发送短信
     * @return array
     *
     */
    public function sendUpdatemobileMsg($newmobile,$prphone,$type=0)
    {
        if(empty($prphone)){
            return return_format('',37019,lang('param_error'));
        }
        //删除redis的缓存
        Cache::rm('mobile'.$newmobile);
        //先判断手机号长度
        if(strlen($newmobile)<6 || strlen($newmobile)>12 || !is_numeric(rtrim($newmobile))){
            return return_format($this->str,37017,lang('37017'));
        }else{
                if($type == 1){
                    $receivemodel = new Receive;
                    $ismobiletrue = $receivemodel->isMobileExist($newmobile,$prphone);
                    if($ismobiletrue){
                        return return_format('',37300,lang('您已经申请领取过免费课程'));
                    }
                }
                $mobile_code = rand(100000,999999);
                //此处调用短信接口,发送验证码
                $messageobj = new Messages;
                $send_result = $messageobj->sendMeg($newmobile,$type=4,$params = [$mobile_code],$prphone);
                if($send_result['result'] == 0){
                    return return_format('',0,lang('success'));
                }else{
                    if($send_result['result'] == 1016){
                        return return_format('',37106,$send_result['errmsg']);
                    }
                    Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
                    return return_format('',37018,lang('37018'));
                }
            }
    }
    /**
     * 随机生成图形验证码
     * @Author yr
     * @return array
     *
     */
    public function getCaptcha(){

        //生成缓存维一标识
        $obj  = new Verifyhelper;
        $res = $obj->verify();
        return return_format($res,0,lang('success'));
    }
    /**
     * 修改密码
     * @Author yr
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [string]               newpass   新密码
     * @return array
     *
     */
    public function updatePassword($mobile,$code,$newpass){
        if(empty($mobile) || empty($code) || empty($newpass)){
            return return_format($this->str,37019,lang('param_error'));
        }
        if(!verifyPassword($newpass)){
            return return_format($this->str,37020,lang('37020'));
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$mobile);

        if(trim($cachedata) !== trim($code)){
            //如果验证码输入错误超限 重新发送短信验证码
         /*  if(!verifyErrorCodeNum($mobile)){
               return return_format($this->str,37022,lang('37022'));
           }*/
            return return_format($this->str,37023,lang('37023'));
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($mobile);
        $encryptpass = $this->createUserMark($newpass);
        $mix = $encryptpass['mix'];
        $password = $encryptpass['password'];
        if(empty($info)){
            return return_format($this->str,37024,lang('37024'));
        }elseif($info['password'] == trim($password)){
            return return_format($this->str,37025,lang('37025'));
        }else{
            $result = $studentmodel->updateStudentPass($password,$mobile,$mix);
            if($result){
                Cache::rm('mobile'.$mobile);
                return return_format($this->str,0,lang('success'));
            }else{
                return return_format($this->str,37026,'error');
            }
        }


    }
    /**
     * 修改个人中心密码
     * @Author yr
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [string]               newpass   新密码
     * @return array
     *
     */
    public function updateUserPassword($mobile,$code,$newpass,$repass){
        if(empty($mobile) || empty($code) || empty($newpass)||empty($repass)){
            return return_format($this->str,37027,lang('param_error'));
        }
        if(trim($newpass) !== trim($repass)){
            return return_format('',37028,lang('37028'));
        }
        if(strlen($newpass)>16 || strlen($newpass)<6){
            return return_format($this->str,37029,lang('37029'));
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$mobile);
        if(empty( $cachedata)){
            return return_format($this->str,37030,lang('37030'));
        }
        if(trim($cachedata) !== trim($code)){
            if(!verifyErrorCodeNum($mobile)){
                return return_format($this->str,37031,lang('37031'));
            }
            return return_format($this->str,37032,lang('37032'));
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($mobile);
        $encryptpass = $this->createUserMark($newpass);
        $mix = $encryptpass['mix'];
        $password = $encryptpass['password'];
        if(empty($info)){
            return return_format($this->str,37033,lang('37033'));
        }elseif(trim($info['password']) == trim($password)){
            return return_format($this->str,37034,lang('37034'));
        }else{
            $result = $studentmodel->updateStudentPass($password,$mobile,$mix);
            if($result){
                Cache::rm('mobile'.$mobile);
                return return_format($this->str,0,lang('success'));
            }else{
                return return_format($this->str,37035,lang('error'));
            }
        }


    }
    /**
     * 修改手机号
     * @Author yr
     * @param    [string]              oldmobile  必填原有手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                   newmobile  新手机号
     * @param   [int]                   studentid   用户Id
     * @return array
     *
     */
    public function updateMobile($oldmobile,$newmobile,$code ,$studentid,$prphone){
        if( empty($oldmobile) || empty($code) || empty($newmobile)){
            return return_format($this->str,37036,lang('param_error'));
        }
        if(strlen($newmobile)>15 || strlen($newmobile)<6){
            return return_format($this->str,37037,lang('37037'));
        }
        $cachedata = Cache::get('mobile'.$newmobile);
        if(empty($cachedata)){
            return return_format($this->str,37038,lang('37038'));
        }
        //验证验证码是否正确并且验证次数
        if(trim($cachedata) !== trim($code)){
            if(!verifyErrorCodeNum($newmobile)){
                return return_format($this->str,37039,lang('37039'));
            }
            return return_format($this->str,37040,lang('37040'));
        }
        $studentmodel = new Studentinfo;
        $oldpassword = $studentmodel->checkLogin($newmobile);
        if(!empty($oldpassword)){
            return return_format($this->str,37041,lang('37041'));
        }elseif($oldmobile == $newmobile){
            return return_format($this->str,37042,lang('37042'));
        }else{
            $result = $studentmodel->updateMobile($studentid,$newmobile,$prphone);
            if($result){
                Cache::rm($newmobile);
                return return_format($this->str,0,lang('success'));
            }else{
                return return_format($this->str,37043,lang('error'));
            }
        }


    }
    /**
     * 学生端充值
     * @Author yr
     * @param  userid  学生id
     * @param  amount  充值金额
     * @return array
     *
     */
    public function studentRecharge($studentid,$amount,$paytype,$source,$type=0){
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,37044,lang('param_error'));
        }
       /* if(intval($amount)<100){
            return  return_format('',39032,'充值金额不能少于100元');
        }*/
        if(intval($amount)<0){
             return  return_format('',37045,lang('37045'));
         }
        if(!in_array($paytype,$this->paytype)){
            return  return_format('',37046,lang('37046'));
        }
        $studentmodel = new Studentinfo;
        $result = $studentmodel->getStudentInfo($studentid);
        if(empty($result)){
            return return_format('',37047,lang('37047'));
        }
       /* $amount = sprintf("%.2f", $amount);*/
//        $amount = 0.01;
        //生成充值订单号
        $rechargenum = getOrderNum();
        //拼装充值订单信息
        $data['studentid'] = $studentid;
        //暂且不做优惠券
        $data['price'] = $amount;
        $data['addtime'] = time();
        $data['paytype'] = $paytype;
        $data['rechargenum'] = $rechargenum;
        $data['source'] = $source;
        $rechargemodel = new Recharge;
        $insertres = $rechargemodel->insert($data);
        if(!$insertres){
            return return_format('',37048,lang('37048'));
        }
        switch ($paytype){
            case 2: //微信支付
                //type等于1app充值 type=0 pc充值
                $wxpayobj = new Wxpay;
                if($type == 1){//app支付
                    $notifyurl = config('param.server_url').$this->wxappRechargeurl;//异步回调支付
                    $result = $wxpayobj->appWxpay($rechargenum,$this->subject,$amount,$this->subject,$notifyurl);
                    if ($result['result_code'] !== 'SUCCESS') {
                        return return_format('', 34034, lang('33031'));
                    }
                    $result['ordernum'] = $rechargenum;
                    $data['data'] = $result;
                    return return_format($data, 0, lang('success'));
                }else{//扫码支付
                    $notifyurl = config('param.server_url').$this->wxnotifyurl;//异步回调支付
                    $result = $wxpayobj->createWxPayUrl($rechargenum,$this->subject,$amount,$this->subject,$notifyurl);
                    if($result['result_code'] !== 'SUCCESS'){
                        return return_format('',37049,lang('error'));
                    }
                    $url = $result['code_url'];
                    //生成微信支付二维码流文件
                    $image = get_base64_qrcode($url);
                    $data['type'] = 1;
                    $data['codeurl'] = $image;
                    return  return_format($data,0,lang('success'));
                }
                break;
            case 3://支付宝支付
                $alipayobj = new Alipaydeal;
                if($type == 1){//app支付
                    //拼接同步地址
                    $returnurl = "/student/wxPay/success.html?type=2";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    //拼接回调地址
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->appAlipay($rechargenum,$this->subject,$amount,$this->subject,$returnurl,$notifyurl);
                    $result['ordernum'] = $rechargenum;
                    $data['data'] = $res;
                    return return_format($data,0,lang('success'));
                }else{//扫码支付
                    //拼接同步地址
                    $returnurl = "/student/wxPay/success.html?type=2";
                    $returnurl = config('param.http_name').$_SERVER['HTTP_HOST'].$returnurl;
                    //拼接回调地址
                    $notifyurl = config('param.server_url').$this->alinotifyurl;
                    $res =  $alipayobj->createPayRequest($rechargenum,$this->subject,$amount,$this->subject,$returnurl,$notifyurl);
                    $data['data'] = $res;
                    return return_format($data,0,lang('success'));
                }
                break;
            //银联充值
            case 4:
                break;
            default:
                return return_format('',37050,lang('param_error'));
        }
    }
    /**
     * 机构收藏
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function organCollect($studentid){
        if(!is_intnum($studentid)){
            return return_format('',37051,lang('param_error'));
        }
        //查看是否收藏过该机构
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Organcollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',37052,lang('37052'));
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',37053,lang('error'));
        }else{
            return return_format('',0,lang('success'));
        }
    }
    /**
     * 老师收藏
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function teacherCollect($teacherid,$studentid){
        if(!is_intnum($teacherid)||!is_intnum($studentid)){
            return return_format('',37054,lang('param_error'));
        }
        //查看是否收藏过该机构
        $where['teacherid'] = $teacherid;
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Teachercollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',37055,lang('37055'));
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',37056,lang('37056'));
        }else{
            /*发送站内消息 start*/
            $usermodel = new Studentinfo;
            $msgList['nickname'] = $usermodel->getStudentInfo($studentid)['nickname'];
            $msgList['userid'] = $teacherid;
            $msgobj = new \StudendMsg;
            $msgobj->collectTeacherMsg($msgList);
            /*end*/
            return return_format('',0,lang('success'));
        }
    }
    /**
     * 班级收藏
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function classCollect($courseid,$studentid){
        if(!is_intnum($courseid)||!is_intnum($studentid)){
            return return_format('',37057,lang('param_error'));
        }
        //查看是否收藏过该机构
        $where['courseid'] = $courseid;
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Classcollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',37058,lang('37058'));
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',37059,lang('37059'));
        }else{
            return return_format('',0,lang('success'));
        }
    }
    /**
     * 查询我收藏的老师
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function teacherCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',37060,lang('param_error'));
        }
        $pagenum = isset($pagenum)?$pagenum:0;
            //判断分页页数
            if($pagenum>0){
                $start = ($pagenum - 1 ) * $limit ;
                $limitstr = $start.','.$limit ;
            }else{
                $start = 0 ;
                $limitstr = $start.','.$limit ;
            }
        $teachermodel = new Teachercollection;
        $result = $teachermodel->getCollectList($studentid,$limitstr);
        $total = $teachermodel->getCollectCount($studentid);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            $data['pageinfo'] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 查询我收藏的机构
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function organCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',37061,lang('param_error'));
        }
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $teachermodel = new Organcollection;
        $result = $teachermodel->getCollectList($studentid,$limitstr);
        $total = $teachermodel->getCollectCount($studentid);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            $data['pageinfo'] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 查询我收藏的班级课程
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function classCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',37061,lang('param_error'));
        }
        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $currmodel = new Classcollection;
        $result = $currmodel->getCollectList($studentid,$limitstr);
        $total = $currmodel->getCollectCount($studentid);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            $data['pageinfo'] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,lang('success'));
        }
    }
    /**
     * 取消收藏的机构
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function cancelOrganCollect($studentid){
        if(!is_intnum($studentid)){
            return return_format('',37062,lang('param_error'));
        }
        $collectmodel = new Organcollection;
        $where = [
            'studentid' => $studentid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',0,lang('error'));
        }
    }
    /**
     * 取消收藏的老师
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function cancelTeacherCollect($teacherid,$studentid){
        if(!is_intnum($studentid) || !is_intnum($teacherid)){
            return return_format('',37063,lang('param_error'));
        }
        $collectmodel = new Teachercollection;
        $where = [
            'studentid' => $studentid,
            'teacherid' => $teacherid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',37064,lang('error'));
        }
    }
    /**
     * 取消收藏的班级
     * @Author yr
     * @param  studentid  学生id
     * @return array
     *
     */
    public function cancelClassCollect($courseid,$studentid){
        if(!is_intnum($studentid) || !is_intnum($courseid)){
            return return_format('',37065,lang('param_error'));
        }
        $collectmodel = new Classcollection;
        $where = [
            'studentid' => $studentid,
            'courseid' => $courseid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',37066,lang('error'));
        }
    }
    /**
     * 查询我的作业列表
     * @Author yr
     * @param  $studentid  学生id
     * @param  $status  区分作业完成状态 0.未完成 1已完成 2完成
     * @param  $pagenum 分页页数
     * @param  $limit  每页条目数
     * @return array
     *
     */
     public function getHomeworkList($studentid,$status,$pagenum,$limit){
         if(!is_intnum($studentid)){
             return return_format('',-37200,lang('-37200'));
         }
         $status_arr = [0,1,2];
         if(!in_array($status,$status_arr)){
             return return_format('',-37201,lang('-37201'));
         }
         $pagenum = isset($pagenum)?$pagenum:0;
         //判断分页页数
         if($pagenum>0){
             $start = ($pagenum - 1 ) * $limit ;
             $limitstr = $start.','.$limit ;
         }else{
             $start = 0 ;
             $limitstr = $start.','.$limit ;
         }
         //根据状态拼装查询条件
         switch($status){
             case 0:
                 //未完成
                $where = [
                    'h.issubmited' => 0,
                    'h.studentid' => $studentid,
                ];
                 break;
             case 1:
                 //已完成未批阅
                 $where = [
                     'h.issubmited' => 1,
                     'h.reviewstatus' => 0,
                     'h.studentid' => $studentid,
                 ];
                break;
             case 2:
                 //已批阅
                 $where = [
                     'h.reviewstatus' => 1,
                     'h.studentid' => $studentid,
                 ];
                 break;
             default:
                 //参数错误
                 return return_format('',-37201,lang('-37201'));
         }
         $model = new Studenthomework;
         $list = $model ->getHomeworkList($where,$limitstr);
         $total = $model->getHomeworkCount($where);
         $teachermodel = new Teacherinfo;
		 $lessonmodel = new Lessons;
         $exerciseinfomodel = new Periodexerciseinfo;
         foreach ($list as $k=>$v){
             $list[$k]['teachername'] = $teachermodel->getTeacherName($v['teacherid']);
             $list[$k]['endtime'] = date('Y-m-d',$v['endtime']);
			 $periodid = $lessonmodel->getFieldName($v['lessonid'], 'periodid')['periodid'];
			 $list[$k]['subjectcount'] = $exerciseinfomodel->getSubjectCount($periodid)['subjectcount'];
         }
         $data['data'] = $list;
         //分页信息
         $data['pageinfo'] = [
             'pagenum'=> $pagenum,
             'limit' => $limit,
             'total' => $total
         ];
         return return_format( $data,0,lang('success'));

     }
    /**
     * 通过课时名称查询我的作业列表
     * @Author yr
     * @param  $studentid  学生id
     * @param  $search  搜索名称
     * @param  $status  区分作业完成状态 0.未完成 1已完成 2完成
     * @param  $pagenum 分页页数
     * @param  $limit  每页条目数
     * @return array
     *
     */
     public function searchHomework($studentid,$search,$status,$pagenum,$limit){
         if(!is_intnum($studentid)){
             return return_format('',-37200,lang('-37200'));
         }
         $status_arr = ['0','1','2'];
         if(!in_array($status,$status_arr)){
             return return_format('',-37201,lang('-37201'));
         }
         $pagenum = isset($pagenum)?$pagenum:0;
         //判断分页页数
         if($pagenum>0){
             $start = ($pagenum - 1 ) * $limit ;
             $limitstr = $start.','.$limit ;
         }else{
             $start = 0 ;
             $limitstr = $start.','.$limit;
         }
         //根据状态拼装查询条件
         switch($status){
             case 0:
                 //未完成
                 $where = [
                     'h.issubmited' => 0,
                     'h.studentid' => $studentid,
                 ];
                 break;
             case 1:
                 //已完成未批阅
                 $where = [
                     'h.issubmited' => 1,
                     'h.reviewstatus' => 0,
                     'h.studentid' => $studentid,
                 ];
                 break;
             case 2:
                 //已批阅
                 $where = [
                     'h.reviewstatus' =>1,
                     'h.studentid' => $studentid,
                 ];
                 break;
             default:
                 //参数错误
                 return return_format('',-37201,lang('-37201'));
         }
         $model = new Studenthomework;
         $list = $model ->searchHomework($where,$limitstr,$search);
         $total = $model->searchHomeworkCount($where,$search);
         $teachermodel = new Teacherinfo;
         foreach ($list as $k=>$v){
             $list[$k]['teachername'] = $teachermodel->getTeacherName($v['teacherid']);
         }
         $data['data'] = $list;
         //分页信息
         $data['pageinfo'] = [
             'pagenum'=> $pagenum,
             'limit' => $limit,
             'total' => $total
         ];

         return return_format($data,0,lang('success'));
     }
    /**
     * 查询未完成作业对应的题目
     * @Author yr
     * @param  $studentid  学生id
     * @param  $lessonsid  课时id
     * @param  $classid 班级id
     * @return array
     *
     */
     public function getQuestionList($studentid,$lessonsid,$classid){
             if(!is_intnum($studentid)){
                 return return_format('',-37201,lang('-37201'));
             }
             if(!is_intnum($lessonsid)){
                 return return_format('',-37202,lang('-37202'));
             }
             $lessonsmodel = new Lessons;
             $field = 'periodid';
             $periodid = $lessonsmodel->getFieldName($lessonsid,$field)['periodid'];
             $data = $this->queryQuestions($studentid,$classid,$lessonsid,$periodid);
             return return_format($data,0,lang('success'));
     }
    /**
     * 提交作业
     * @Author yr
     * @param  $studentid  学生id
     * @param  $periodid  课时id
     * @param  $classid  班级id
     * @param  $answers  答案
     * @return array
     *
     */
     public function submitQuestions($studentid,$classid,$lessonid,$homeworkid,$answers){
         if(!is_intnum($studentid)){
             return return_format('',-37201,lang('-37201'));
         }
         if(!is_intnum($lessonid)){
             return return_format('',-37202,lang('-37202'));
         }
         if(!is_intnum($classid)){
             return return_format('',-37203,lang('-37203'));
         }
         $answermodel = new Studenthomeworkanswer;
         $data = [];
         foreach($answers as $k=>$v){
            $data[$k]['classid'] = $classid;
            $data[$k]['lessonid'] = $lessonid;
            $data[$k]['subjectid'] = $v['subjectid'];
            $data[$k]['answer'] = $v['answer'];
            $data[$k]['studentid'] = $studentid;
         }
         $add_result = $answermodel->submitQuestions($data);
         if($add_result){
             //修改作业表状态为已完成
             $homeworkmodel = new Studenthomework;
             $where = [
                 'id' => $homeworkid
             ];
             $fieldarr = [
                 'issubmited' =>1,
                 'submittime' => time()
             ];
             $update_result = $homeworkmodel->updateData($where,$fieldarr);
             if($update_result){
                 return return_format('',0,lang('success'));
             }
                return return_format('',0,lang('error'));
         }else{
                return return_format('',0,lang('error'));
         }
     }
    /**
     * 已完成的作业 展示修改
     * @Author yr
     * @param  $studentid  学生id
     * @param  $periodid  课时id
     * @param  $answers  答案
     * @return array
     *
     */
    public function showUpdateQuestions($studentid,$lessonid,$classid){
        if(!is_intnum($studentid)){
            return return_format('',-37201,lang('37201'));
        }
        if(!is_intnum($lessonid)){
            return return_format('',-37202,lang('37202'));
        }
        $lessonsmodel = new Lessons;
        $field = 'periodid';
        $periodid = $lessonsmodel->getFieldName($lessonid,$field)['periodid'];
        $data = $this->queryQuestions($studentid,$classid,$lessonid,$periodid,$is_update=1);
        return return_format($data,0,lang('success'));
    }
    /**
     * 查询作业对应的题目
     * @Author yr
     * @param  $studentid  学生id
     * @param  $periodid  课时id
     * @param  $classid  班级id
     * @param  $answers  答案
     * @return array
     *
     */
    public function submitUpdateQuestions($studentid,$classid,$lessonid,$homeworkid='',$answers){
        if(!is_intnum($studentid)){
            return return_format('',-37201,lang('-37201'));
        }
        if(!is_intnum($lessonid)){
            return return_format('',-37202,lang('-37202'));
        }
        if(!is_intnum($classid)){
            return return_format('',-37203,lang('-37203'));
        }
        $answermodel = new Studenthomeworkanswer;
        foreach($answers as $k=>$v){
            $where = [
                'classid' => $classid,
                'lessonid' => $lessonid,
                'subjectid' => $v['subjectid'],
                'studentid' => $studentid,
            ];
            $data = [
                'answer' => $v['answer']
            ];
            $res = $answermodel->updateQuestions($where,$data);
            if($res<0){
                return return_format('',0,lang('error'));
            }
        }
        return return_format('',0,lang('success'));
    }
    /**
     * 已完成的作业 展示修改
     * @Author yr
     * @param  $studentid  学生id
     * @param  $periodid  课时id
     * @param  $answers  答案
     * @return array
     *
     */
    public function getCompleteQuestions($studentid,$lessonid,$classid){

        if(!is_intnum($studentid)){
            return return_format('',-37201,lang('37201'));
        }
        if(!is_intnum($lessonid)){
            return return_format('',-37202,lang('37202'));
        }
        $lessonsmodel = new Lessons;
        $field = 'periodid';
        $periodid = $lessonsmodel->getFieldName($lessonid,$field)['periodid'];
        $data = $this->queryQuestions($studentid,$classid,$lessonid,$periodid,$is_update=2);
        return return_format($data,0,lang('success'));
    }
    /**
     * 发送消息提醒
     * @Author yr
     * @param  $studentid  学生id
     * @param  $homeworkid 学生作业id
     * @return array
     *
     */
    public function sendHomeworkMessage($studentid,$homeworkid){
        if(!is_intnum($studentid) || !is_intnum($homeworkid)){
            return return_format('',-37203,lang('param_error'));
        }
        $sendobj = new \StudendMsg();
        $homeworkmodel = new Studenthomework();
        $where = [
            'id' => $homeworkid
        ];
        $field = 'teacherid,sendstatus';
        $homeworkinfo = $homeworkmodel->getHomeworkInfo($where,$field);
        if($homeworkinfo['sendstatus'] == 1){
            return return_format('',-37204,'您已发送过消息提醒');
        }
        $studentmodel = new Studentinfo();
        $nickname = $studentmodel->getStudentInfo($studentid)['nickname'];
        $msglist['teacherid'] = $homeworkinfo['teacherid'];
        $msglist['nickname'] = $nickname;
        $msglist['homeworkid'] = $homeworkid;
        $result = $sendobj->sendHomeworkMsg($msglist);
         if($result){
             //修改状态
             $updatewhere = ['id'=>$homeworkid];
             $updatefield = ['sendstatus'=>1];
             $update_res =  $homeworkmodel->updateData($updatewhere,$updatefield);
             if($update_res){
                return  return_format('',0,lang('success'));
             }else{
                return return_format('',-37206,lang('error'));
             }
         }else{
                return return_format('',-37205,lang('error'));
         }

    }
    /**
     * 统一查询题库 或者答案
     * @Author yr
     * @param  $studentid  学生id
     * @param  $periodid  课时id
     * @param  $is_update 是否修改 0未完成1已完成2.已批阅
     * @param  $answers  答案
     * @return array
     *
     */
    public function queryQuestions($studentid,$classid,$lessonsid,$periodid,$is_update=0){
        //查询题库 用课程的课时id
        $subjectmodel = new Exercisesubject;
        $where = [
            'e.periodid' => $periodid,
        ];
        $list  = $subjectmodel->getSubjectList($where);
        foreach($list as $k=>$v){
            if(!empty($v['imageurl'])){
                $list[$k]['imageurl'] = explode(',',$v['imageurl']);
            }else{
                $list[$k]['imageurl'] = [];
            }
        }
        //根据题型型分组
        $grouped = [];
        foreach ($list as $value) {
            $grouped[$value['type']][] = $value;
        }
       for($i=1;$i<5;$i++){
            if(empty($grouped[$i])){
                $grouped[$i] = [];
            }
       }
        $optionmodel  = new Exercisesubjectoptions;
        $answermodel = new Studenthomeworkanswer;
        $exerciseinfomodel = new Periodexerciseinfo;
        $subjectmodel = new Exercisesubject;
        $lessonmodel = new Lessons;
        foreach($grouped as $k=>$v){
            if(!empty($grouped[$k])){
                foreach($grouped[$k] as $key=>$value){
                    //获取选择题选项
                    if($k == 1 || $k==2){
                        $optionwhere = [
                            'subjectid' => $value['subjectid']
                        ];
                        $grouped[$k][$key]['options'] = $optionmodel->getSubjectOptions( $optionwhere);
                    }
                    //如果是修改 查询每道题的答案
                    $where = [
                        'classid' => $classid,
                        'lessonid' => $lessonsid,
                        'subjectid' => $value['subjectid'],
                        'studentid' => $studentid
                    ];
                    if($is_update !=0){
                        $grouped[$k][$key]['answers'] = $answermodel->getAnswers($where)['answer'];
                    }
                    //已批阅的需要加正确答案
                    if($is_update == 2){
                        $subject_where = [
                            'id' => $value['subjectid']
                        ];
                        $grouped[$k][$key]['teacherscore'] = $answermodel->getAnswers($where)['score'];
                        $grouped[$k][$key]['comment'] = $answermodel->getAnswers($where)['comment'];
                        $grouped[$k][$key]['correctanswer'] = $subjectmodel->getSubjectInfo($subject_where)['correctanswer'];
                    }
                }
            }
        }

        $exerciseinfo = $exerciseinfomodel->getSubjectCount($periodid);
        //查询课时名称和题库数量
        $result['subject'] = $grouped;
        $result['subject_count'] = $exerciseinfo['subjectcount'];
        $result['periodname'] = $exerciseinfo['periodname'];
        $result['lessonsid'] = $lessonsid;
        $result['classid'] = $classid;
        $fieldname = 'periodname';
        $result['lessonsname'] = $lessonmodel->getFieldName($lessonsid,$fieldname)['periodname'];
        //获取总分
        $homeworkmodel = new Studenthomework;
        $wherearr = [
            'lessonid'=> $lessonsid,
            'classid'=> $classid,
            'studentid'=> $studentid,
        ];
        $field = ['score','id'];
        $result['totalscore'] = $homeworkmodel->getHomeworkInfo($wherearr,$field)['score'];
        $result['homeworkid'] = $homeworkmodel->getHomeworkInfo($wherearr,$field)['id'];
        return $result;
    }
    /**
     * 获取用户喜欢的分类
     * @Author yr
     * @return array
     *
     */
    public function getUserFavorCategory(){
        $catemodel = new Studentcategory();
        $result = $catemodel->getUserFavorCategory();
        foreach($result as $k=>$v){
            $result[$k]['flag'] = false;
        }
        return return_format($result,0,lang('success'));
    }
    /**
     * 添加用户喜欢的分类
     * @Author yr
     * @return array
     *
     */
    public function addUserFavorCategory($categoryid,$studentid){
        if(empty($categoryid)){
            return return_format('',-37204,lang('-37204'));
        }
        if(!is_intnum($studentid)){
            return return_format('',-37200,lang('-37200'));
        }
        $usermodel = new Studentinfo;
        $result = $usermodel->favorAdd($categoryid,$studentid);
        if($result>=0){
            return return_format($result,0,lang('success'));
        }

    }
    /**
     * 获取用户的标签
     * @Author yr
     * @return array
     *
     */
    public function getUserTag(){
        $catemodel = new Studenttag;
        $result = $catemodel->getUserTag();
        $childmodel= new Studentchildtag;
        $where = [
            'fatherid' => $result['tagid']
        ];
        $result['childarr'] = $childmodel->getChildTag($where);
        if(!empty($result['childarr'])){
            foreach($result['childarr'] as $k=>$v){
                $result['childarr'][$k]['flag'] = false;
            }
        }
        return return_format($result,0,lang('success'));
    }
    /**
     * 添加用户喜欢的分类
     * @Author yr
     * @return array
     *
     */
    public function addUserTag($fatherid,$tagids,$studentid){
        if(empty($fatherid)){
            return return_format('',-37204,lang('-37204'));
        }
        if(!is_intnum($studentid)){
            return return_format('',-37200,lang('-37200'));
        }
        $usermodel = new Studentinfo;
        $where = [
            'id' => $studentid
        ];
        $field = [
            'tag' => $fatherid,
            'childtag' =>$tagids
        ];
        $result = $usermodel->updateAppuserInfo($where,$field);
        if($result>=0){
            return return_format($result,0,lang('success'));
        }else{
            return return_format('',0,lang('error'));
        }

    }
    /**
     * 查询学生消息列表
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              pagenum  页码数
     * @return   array();
     * URL:/student/User/messageList
     */
    public function messageList($studentid,$pagenum,$limit,$type=0){
        if(!is_intnum($studentid) || !is_intnum($pagenum)){
            return return_format('',39138,lang('param_error'));
        }
		if(!in_array($type, [0,1,2])){
            return return_format('',39138,lang('param_error'));
        }

        $pagenum = isset($pagenum)?$pagenum:0;
        //判断分页页数
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $messagemodel = new Message;
        $list = $messagemodel->getMessageList($studentid,$limitstr,$type);
        $total = $messagemodel->getMessageCount($studentid,$type);
        
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            }
        }
		$data['data'] = $list;
		$data['pageinfo'] = [
            'pagenum'=> $pagenum,
            'limit' => $limit,
            'total' => $total
        ];
        return return_format($data, 0, lang('success'));
    }
    /**
     * 修改学生消息状态
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              messageid 消息id
     * @return   array();
     * URL:/student/User/messageList
     */
    public function updateMsgStatus($latelytime,$studentid){
        if(!is_numeric($latelytime)){
            return return_format('',39301,lang('param_error'));
        }
        $messagemodel = new Message;
        $where['addtime'] = ['ELT',$latelytime];
        $where['usertype'] = 3;
        $where['userid'] = ['ELT',$latelytime];
        //消息状态 1：查看 0：未查看
        $data['istoview'] = 1;
        $result = $messagemodel->updateMsgStatus($where,$data);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',39302,lang('error'));
        }

    }
    /**
     * 删除消息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @param    [int]              messageid 消息id
     * @return   array();
     * URL:/student/User/deleteMsg
     */
    public function deleteMsg($messageids){
        if(empty($messageids)){
            return return_format('',39301,lang('param_error'));
        }
        $messagemodel = new Message;
        $idarr = explode(',',$messageids);
        if(count($idarr) == 1){
            $where['id'] = $messageids;
            $data['delflag'] = 0;
            $result = $messagemodel->updateMsgStatus($where,$data);
        }else{
            $data = [];
            foreach($idarr as $k=>$v){
                $data[$k]['id'] = $v;
                $data[$k]['delflag'] = 0;
            }
            $result = $messagemodel->saveAllMsg($data);
        }
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',39302,lang('error'));
        }

    }
    /**
     * 查询新消息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              studentid  学生id
     * @return   array();
     * URL:/student/User/getNewMsg
     */
    public function getNewMsg($studentid){
        //实例化模型
        $msgobj = new Message;
        //查询出最新的5条消息
        $newmsglist = $msgobj->getNewMsg($studentid);
        if(empty($newmsglist)){
            $data['status'] = 0;
        }else{
            $data['status'] = 1;
        }
        $data['data'] = $newmsglist;
        return return_format($data,0,lang('success'));
    }
    /**
     * 添加收货地址
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [array]       data       学生
     * @return   array();
     * URL:/student/User/addUserAddress
     */
    public function addOrUpdateAddress($data){
        //最多可以添加10条数据
        $addressmodel = new Studentaddress;
        if(empty($data['id'])){//如果是添加
            $count = $addressmodel->getAddressCount($data['studentid']);
            if($count>=10){
                return return_format('',39401,lang('您最多可以保存10条收货地址'));
            }
        }

        $rule = [
            'pid'  => 'require|number',
            'cityid'  => 'require|number',
            /*'areaid'  => 'require|number',*/
            'address'  => 'require',
            'zipcode'  => 'require|length:6|number',
            'linkman'  => 'require',
            'mobile'  => 'require|length:6,20',
        ];
        $msg = [
            'pid.require' => '请选择国家',
            'pid.number' => '参数pid必须为数字',
            'cityid.require' => '请选择城市',
            'cityid.number' => '参数cityid必须为数字',
          /*  'areaid.require' => '请选择区域id',
            'areaid.number' => '参数areaid必须为数字',*/
            'address.require' => '详细地址必须填写',
            'linkman.require' => '收货人必须填写',
            'zipcode.require' => '邮编必须填写',
            'zipcode.length' => '邮编必须为6位数字',
            'zipcode.number' => '邮编必须为6位数字',
            'mobile.require' => '手机号必须填写',
            'mobile.length'     => '请输入6-20电话或手机号',
        ];
        $validate = new Validate($rule,$msg);
        $result   = $validate->check($data);
        if(true !== $result){
            return return_format('',39400,$validate->getError());
        }
        $result = $addressmodel->addOrUpdateAddress($data);
        if($result>=0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',0,lang('error'));
        }
    }
    /**
     * 获取所有学生的收货地址
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [array]       studentid       学生id
     * @return   array();
     * URL:/student/User/getAddressList
     */
    public function getAddressList($studentid){
        if(!is_intnum($studentid)){
            return return_format('',39401,lang('参数studentid错误'));
        }
        $addressmodel = new Studentaddress;
        $result = $addressmodel->getAddressList($studentid);
        $citymodel = new City();
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['pname'] = $citymodel->getName($v['pid']);//省名称
                $result[$k]['cname'] = $citymodel->getName($v['cityid']);//城市名称
                if($v['areaid'] ==0){
                    $result[$k]['aname'] = '';
                }else{
                    $result[$k]['aname'] = $citymodel->getName($v['areaid']);//区域名称
                }

            }
        }
        return return_format($result,0,lang('success'));

    }
    /**
     * 删除学生的收货地址
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [array]       addressid      地址id
     * @return   array();
     */
    public function deleteAddress($addressid){
        if(!is_intnum($addressid )){
            return return_format('',39401,lang('参数addressid错误'));
        }
        $addressmodel = new Studentaddress;
        $result = $addressmodel->deleteAddress($addressid);
        if($result){
            return return_format($result,0,lang('success'));
        }else{
            return return_format($result,0,lang('error'));
        }
    }
    /**
     * 获取学生分析反馈列表
     * @Author yr
     * @param userid int 学生用户id
     * @param pagenum int 分页页数
     * @param limit int  每页条数
     * @return array
     *
     */
    public function getFeedbackList($userid,$pagenum,$limit,$search){
        //判断参数是否合法
        if(!is_intnum($userid) || !is_intnum($limit)){
            return return_format($this->str,37001,lang('param_error'));
        }
        //判断分页页数
        if(is_intnum($pagenum)>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit;
        }
        $studentmodel = new Studentattendance();
        $where = [
            'studentid' => $userid
        ];
        $studentinfo = $studentmodel->getFeedbackList($where,$limitstr,$search);
        $total = $studentmodel->getFeedbackCount($where,$search);
        //分页信息
        $alllist['pageinfo'] = [
            'pagesize'=>$limit ,// 每页多少条记录
            'pagenum' =>$pagenum ,//当前页码
            'total'   => $total // 符合条件总的记录数
        ];
        $alllist['data'] = $studentinfo;
        if(empty($alllist['data'])){
            $alllist['data'] = [];
        }
        return return_format($alllist,0,lang('success'));
    }
    /**
     * 给用户生成 密码 和 mix
     * [createUserMark 生成用户的机密字符存储在数据库，当用户登陆时比对]
     * 创建用户时调用
     * @Author wyx
     * @DateTime 2018-04-27T16:22:58+0800
     * @param    [string]            $pass    [密码]
     * @return   [type]                   [description]
     */
    public function createUserMark($pass){
        $mix = $this->getRandString(16) ;
        $md5str = md5(md5($pass).$mix);

        for ($i=0; $i < 5; $i++) {
            $md5str = md5($md5str) ;
        }

        return ['mix'=>$mix,'password'=>$md5str] ;

    }
    /**
     * [getRandString 生成随机字符串]
     * @Author wyx
     * @DateTime 2018-04-27T14:53:16+0800
     * @param    [int]                      [设置需要的字符串的长度默认为8]
     * @return   [string]                   [description]
     */
    private function getRandString($length=8){
        $numstr    = '1234567890' ;
        $originstr = 'abcdefghijklmnopqrstuvwxyz' ;
        $origin = str_repeat($numstr,6).$originstr.strtoupper($originstr) ;

        return substr(str_shuffle($origin), -$length);

    }
}
