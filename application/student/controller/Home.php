<?php
namespace app\student\controller;
use app\admin\model\Alipaypushlog;
use app\admin\model\Wxpaypushlog;
use app\student\business\MyCourseManage;
use app\student\business\PackageManage;
use app\student\business\ScheduManage;
use app\student\business\UserManage;
use app\student\model\Applylessonsrecord;
use app\student\model\Applyschedulingrecord;
use app\student\model\Category;
use app\student\model\Coursepackageuse;
use app\student\model\Curriculum;
use app\student\model\Ordermanage;
use app\student\model\Toteachtime;
use login\Authorize;
use app\index\controller\Index;
use Order;
use think\Config;
use think\Controller;
use think\Loader;
use CURLFile;
use app\student\model\City;
use Messages;
use think\Request;
use Base;
use think\Lang;
use think\Db;
use app\student\model\Lessons;
use app\index\business\UserLogin;
Loader::import('alipay.config');
Loader::import('alipay.pagepay.service.AlipayTradeService');
Loader::import('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
use app\student\model\Playback;
use app\student\model\Teacherinfo;
use think\View;
use wxpay\Wxpay;
use phpqrcode\qrcode;
use alipay\Alipaydeal;
use app\student\business\MyOrderManage;
use wxpay\WechatPayNotify;
use app\admin\model\Organ;
class Home extends Controller
{
    protected $wxnotifyurl = '/admin/ServerNotice/wxCourseNotify';
   /* public function __construct(){
        // 必须先调用父类的构造函数
        parent::__construct();
    }*/
    /**
     * 选择学生的标签
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @return   array();
     * URL:/student/User/getUserTag
     */
    public function ceshisql()
    {
        $data['appid'] = 'wxe84e35ae248bb82f';
        $data['bank_type'] = 'CFT';
        $data['cash_fee'] = '1';
        $data['fee_type'] = 'CNY';
        $data['is_subscribe'] = 'N';
        $data['mch_id'] = '1284715401';
        $data['nonce_str'] = 'amjem2jttfeqryfu9jvo9bg7jsw9dn3t';
        $data['openid'] = 'oj8hXxC2YrWTY6TMZURle8Cdi4_A';
        $data['out_trade_no'] = '201810252252398231365228';
        $data['result_code'] = 'SUCCESS';
        $data['return_code'] = 'SUCCESS';
        $data['sign'] = '6C10ED4A1144B3A51E2C0A8F28263FEC';
        $data['time_end'] = '20181025225244';
        $data['total_fee'] = '1';
        $data['trade_type'] = 'APP';
        $data['transaction_id'] = '4200000202201810252202233074';
        $res = new Wxpaypushlog();
        $res->addAlipayPushLog($data);


    }
    public function getUserTag(){
        $obj = new UserManage;
        $res = $obj ->getUserTag();
        $this->ajaxReturn($res);
    }
    public function ceshi_upload(){
        $file = APP_PATH.'../public/test.mp4';
        $result =  $this->video_info($file);
        print_r($result);die();
    }
    function video_info($file) {
        define('KC_FFMPEG_PATH', 'E:\ffmpeg\bin/ffmpeg -i "%s" 2>&1');
        ob_start();
        passthru(sprintf(KC_FFMPEG_PATH, $file));
        $info = ob_get_contents();
        ob_end_clean();
        // 通过使用输出缓冲，获取到ffmpeg所有输出的内容。
        $ret = array();
        // Duration: 01:24:12.73, start: 0.000000, bitrate: 456 kb/s
        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
            $ret['duration'] = $match[1]; // 提取出播放时间
            $da = explode(':', $match[1]);
            $ret['seconds'] = $da[0] * 3600 + $da[1] * 60 + $da[2]; // 转换为秒
            $ret['start'] = $match[2]; // 开始时间
            $ret['bitrate'] = $match[3]; // bitrate 码率 单位 kb

        }
        // Stream #0.1: Video: rv40, yuv420p, 512x384, 355 kb/s, 12.05 fps, 12 tbr, 1k tbn, 12 tbc
        if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
            $ret['vcodec'] = $match[1]; // 编码格式
            $ret['vformat'] = $match[2]; // 视频格式
            $ret['resolution'] = $match[3]; // 分辨率
            $a = explode('x', $match[3]);
            $ret['width'] = $a[0];
            $ret['height'] = $a[1];
        }
        // Stream #0.0: Audio: cook, 44100 Hz, stereo, s16, 96 kb/s
        if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
            $ret['acodec'] = $match[1]; // 音频编码
            $ret['asamplerate'] = $match[2]; // 音频采样频率

        }
        if (isset($ret['seconds']) && isset($ret['start'])) {
            $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间

        }
        $ret['size'] = filesize($file); // 文件大小
        print_r($ret);die();
        return array($ret, $info);
    }
    /**
     * 查询已批阅的作业详情
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/Home/getCompleteQuestionList
     */
    public function getCompleteQuestionList(){
        $model = new Order();
        $out_trade_no = '201810102208408730659528';
        $trade_no = '201810102148259873420725';
        $buyer_logon_id = 'dsadsssss';
        $amount = '4.13';
        $paytype = 2;
        $result= $model->dealwithPackage($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);die();
        $toteachmodel = new Toteachtime();
        $toteachid = 945;
        $toteachwhere = ['t.id'=>$toteachid];
        $classarr = $toteachmodel->getCourseidOrscheduid($toteachwhere);
        $coursemodel = new Curriculum();
        $coursewhere = ['c.id'=>$classarr['curriculumid'],'c.delflag'=>1];
        $courseinfo = $coursemodel->getCourserById($coursewhere);
        if(empty($courseinfo) || empty($classarr['delflag'])){
            echo "2222";die();
            return return_format('',33965,'该课程已被删除,不能进入教室');
        }
        echo "11";die();
        $data = [
            'gmt_create' => '2018-10-10 16:28:36',
            'charset' => 'UTF-8',
            'gmt_payment' => '2018-10-10 16:28:44',
            'notify_time' => '2018-10-10 16:28:44',
            'subject' => '11111111111',
            'sign' => 'XNH3+tnF/AytHNn4UQhRfMwEPNR4RFy95t49Gxp3HHt6CsWZftzyM8URNZmKZnsOoEkAw7UJfjKfr+h+YxkEUAyUZ4KuiaQZgUw2RiLpc6nEEzINo8n23TsVlCXnNE1mDfURuqtXduaPyYzFVYX+C7yE8Mu9VmddNY/R//Ap8UonmOKYHhJkBLujNM13D51pyoCYfkSQqkURPHE3fY2VXVD0OgXiaH5zcTFfWRePZ5Uoj0MxLFAi+0Ba0EU3FCZRIwt/OorWwPmNx5V9bswg+5Sjv+GzBY3himJ/KKAxcQc6FIkSB9vWkfqDLMJHzN9zSIYxdi+Eg3p9eY9YFEZb3Q==',
            'buyer_id' => '2088902822604201',
            'body' => '11111111111',
            'invoice_amount' => '0.10',
            'version' => '1.0',
            'notify_id' => '2018101000222162844004200513055348',
            'fund_bill_list' => '{"amount":"0.10","fundChannel":"ALIPAYACCOUNT"}',
            'notify_type' => 'trade_status_sync',
            'out_trade_no' => '201810101628252671349427',
            'total_amount' => '0.10',
            'trade_status' => 'TRADE_SUCCESS',
            'trade_no' => '2018101022001404200592176068',
            'auth_app_id' => '2015103100613133',
            'receipt_amount' => '0.10',
            'point_amount' => '0.00',
            'app_id' => '2015103100613133',
            'buyer_pay_amount' => '0.10',
            'sign_type' => 'RSA2',
            'seller_id' => '2088911764314697',
        ];
        $obj = new Alipaypushlog();
        $result = $obj->addAlipayPushLog($data);

       /* $lessonid = 10;
        $classid = 10;
        $studentid = 6;
        $obj = new UserManage;
        $res = $obj ->getCompleteQuestions($studentid,$lessonid,$classid);
        $this->ajaxReturn($res);*/
    }
    public function home(){
        $view = new View();
        return $view->fetch('home');

    }
    /**
     * [getLessonsByDate 通过日期获取课节信息]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getLessonsByDate
     */
    public function getLessonsByDate(){
        $date = '2018-9-30' ;
        $studentid = 1;
        $currobj = new MyCourseManage;
        $lessonsarr = $currobj->getLessonsByDate($date,$studentid);
        $this->ajaxReturn($lessonsarr);

    }
    /**
     * [applyChangeClass 调班申请]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/submitApplyClasss
     */
    public function submitApplyClasss(){
        $curriculumid = 188;//原课程id
        $oldschedulingid = 283;//原班级id
        $newschedulingid = 284;//新班级id
        $studentid = 1;
        $currobj = new MyCourseManage;
        $data = $currobj->submitApplyClasss($studentid,$curriculumid,$oldschedulingid, $newschedulingid);
        $this->ajaxReturn($data);

    }
    /**
     * [getBuyCourseList 获取学生买过的所有班级]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getBuyCourseList
     */
    public function getBuyCourseList(){
        $studentid = 1;
        $obj = new MyCourseManage();
        $result = $obj->getBuyCourseList($studentid);
        $this->ajaxReturn( $result);
    }
    /**
     * [getAllClassList 获取学生买过的所有班级]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getAllClassList
     */
    public function getAllClassList(){
        $studentid = 1;
        $obj = new MyCourseManage();
        $result = $obj->getAllClassList($studentid);
        $this->ajaxReturn( $result);
    }
   public function testce(){
       $a= '0.01';
       $b = '0';
       if($a>$b){
           echo '大';
       }elseif($a==$b){
           echo '等于';
       }else{
           echo '小';
       }
   }
    /**
     * 学生充值接口
     * @Author yr
     * @param  userid  学生id
     * @param  amount  充值金额
     * student/User/studentRecharge
     * @return array();
     */
    public function studentRecharge(){
        $ordermodel = new Order;
        $out_trade_no = '201810151012138761545533';
        $trade_no = '2018101510121387615455331';
        $buyer_logon_id = 'dsssssssssss';
        $amount = '0.01';
        $paytype = '2';
        $result = $ordermodel->dealwithRecharge($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);
        dump($result);die();
        $studentid = 1;
        $amount =  '0.01';
        $paytype =  3;
        $source=  1;
        $userobj = new UserManage;
        $res = $userobj->studentRecharge($studentid,$amount,$paytype,$source);
        $this->ajaxReturn($res);
    }
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  paytype支付方式 如: 1 或 1,2
     * @return   array();
     * URL:/student/Package/gotoPay
     */
    public function gotoPaya()
    {
        $ordermodel = new Order();
        $out_trade_no = '201810151115278162719526';
        $trade_no = '2018101511091824252377311';
        $buyer_logon_id = 'dsaaaaaaaaaaaaa';
        $amount = '400';
        $paytype = '2';
        $result = $ordermodel->dealwithPackage($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);
        print_r($result);die();
        //$studentid = $this->userid;
        $studentid = 1;
        $ordernum = '201810110029527273577921';
        $paytype = '1,3';
        $orderobj = new PackageManage();
        /*  $studentid = 1;
          $ordernum = '201805261741194972438610';
          $amount = '200.00';
          $usablemoney = '99998404.00';
          $paytype = '2';
          $coursename = 'php精讲';
          $classtype = '2';
          $gradename = '班级名称';*/
        $res = $orderobj->gotoPay($studentid, $ordernum, $paytype);
        $this->ajaxReturn($res);
    }
        /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 如: 1 或 1,2
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     * URL:/student/Home/gotoPay
     */
    public function gotoPay()
    {

        $studentid = 236;
        $ordernum = '201810162025559135129131';
        $paytype = '1';
        $orderobj = new MyOrderManage;
        /*  $studentid = 1;
          $ordernum = '201805261741194972438610';
          $amount = '200.00';
          $usablemoney = '99998404.00';
          $paytype = '2';
          $coursename = 'php精讲';
          $classtype = '2';
          $gradename = '班级名称';*/
        $res = $orderobj->gotoPay($studentid, $ordernum, $paytype);
        $this->ajaxReturn($res);
    }
    /**
     * 订单取消接口
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Package/cancelOrder
     */
    public function cancelOrder(){
        $ordernum = $this->request->param('ordernum');
        $orderobj = new PackageManage();
        $res = $orderobj->cancelOrder($ordernum);
        $this->ajaxReturn($res);
    }
    /**
     * 订单取消接口
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param ordernum int 订单号
     * @return   array();
     * URL:/student/Myorder/cancelOrder
     */
    public function cancelOrderc(){
        $ordernum = '201809291008208609148517';
        $orderobj = new MyOrderManage;
        $res = $orderobj->cancelOrder($ordernum);
        $this->ajaxReturn($res);
    }
        /**
     * 学生端首页课程
     * @Author why
     */
    public function jsapi(){
        $out_trade_no = '2018145678';
        $title = '微信公众号支付';
        $total_amount = '0.01';
        $profile = '测试';

        $returnrul = config('param.server_url') . $this->wxnotifyurl;//异步回调地址
        echo $returnrul;die();
        $obj  = new Wxpay();
        $result = $obj->jsapiWxpay($out_trade_no,$title,$total_amount,$profile,$returnrul,$expire=1740);
    }
   /* protected $userid;
    public function __construct()
    {
        echo $this->userid;die();
    }*/
    /**
     * [getBuyCourseList 获取学生买过的所有班级]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Home/countLearned
     */
    function countLearned(){
        $schedulingid = 166;
        if(empty($schedulingid)){
            return '0';
        }
        //统计排课下所有的课节
        $learnsmodel = new Lessons;
        $learnlist   = $learnsmodel->getLessonsNum($schedulingid);
        $learnlist = array_column($learnlist,'learnid');
        $length = count($learnlist);
        $timemodel  = new Toteachtime;
        //下班课，大班课已学课时
        $learndnum = $timemodel->getLessonsCount($learnlist);
        echo $learndnum;die();
        //已学除以总数
        if($learndnum == 0){
            $radio = '0';
        }else{
            $radio = round(intval($learndnum)/$length*100);
            if($radio>=100){
                $radio = '100';
            }else{
                $radio = $radio;
            }

        }
      echo  $radio;
    }

    /**
     * 查询我的套餐详情
     * @Author yr
     * @DateTime 2018-09-03T14:11:19+0800
     * @param    packageid  int  套餐id
     * @return   array();
     * URL:/student/Home/getPackageDetail
     */
    public function getPackageDetail()
    {
        $packageid = '240';
        $packageobj = new PackageManage;
        $res =  $packageobj->getPackageDetail($packageid);
        $this->ajaxReturn($res);

    }
    public function getHomeworkList(){
        $pagenum = 1;
        $status = 0;
        $studentid = 1;
        $limit = config('param.pagesize')['student_homework_list'];
        $userobj = new UserManage;
        $res = $userobj->getHomeworkList($studentid,$status,$pagenum,$limit);
        $this->ajaxReturn($res);
    }
    /**
     * 写作业 查询我的题库
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              keshi_id  课时id
     * @return   array();
     * URL:/student/User/getQuestionList
     */
    public function getQuestionList(){
        $currmodel= new Category();
        $str = '35,36,37,38,41,43,44,45,46,50,51,59';
        $arr = explode(',',$str);
        $where  = ['id' => ['in',$arr]];
        $field = 'id,categoryname,fatherid';
        $result = $currmodel ->getSelectInfo($where,$field);
        $res = generateTree($result,'id');
        print_r( $res);die();
       /* $arr = explode(',',$str);
           for($i=0;$i<count($arr);$i++){
               $arr[$i] = $currmodel->getSubs($arr[$i]);
               if(count($arr) >count($arr[$i])){
                   $res = array_diff($arr[$i],$arr);
                   if(empty($res)){

                   }
                   $newarr =
               }
           }*/
        $lessonsid = 1302;
        $classid = 210;
        $studentid = 146;
        $obj = new UserManage;
        $res = $obj ->getQuestionList($studentid,$lessonsid,$classid);
        $this->ajaxReturn($res);
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
    public function entry(){
        $classinfo['classroomno'] = '1954859971';
        $classinfo['confuserpwd']  = '111111';
        $key = 'crmf32aQ5Qr1MXpC';
        $nickname = 'ceshi';
        $time  = time();
        //必填， 0：主讲(老师 )  1：助教 2: 学员   3：直播用户  4:巡检员
        $usertype = '2';
        //，auth 值为 MD5(key + ts +serial + usertype)
        $sign =  MD5($key.$time.$classinfo['classroomno'].$usertype);
        //学生密码
        $userpassword = getencrypt($classinfo['confuserpwd'],$key);
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$classinfo['classroomno']}/username/$nickname/usertype/$usertype/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
        echo $url;die();
    }
    /**
     * 查询学生个人资料
     * @Author yr
     * @param  userid  学生id
     * student/User/getStudentInfo
     * @return array();
     */
    public function getStudentInfo(){
        $studentid = 1;
        $userobj = new UserManage;
        $res = $userobj->getStudentInfo($studentid);
        $this->ajaxReturn($res);
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
    }

    /**
     * 查询学生满足该课程的优惠券
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @return   array();
     * URL:/student/Home/getUserPackage
     */
    public function getUserPackage()
    {
        $curriculumid = 170;
        $amount = '';
        $studentid = 152;
        $orderobj = new MyOrderManage;
        $res = $orderobj->getUserPackage($studentid,$curriculumid,$amount);
        $this->ajaxReturn($res);
    }
    /**
     * 获取学生所有的分析反馈信息
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]                 studentid       学生id
     * @return   array();
     * URL:/student/Home/getFeedbackList
     */
    public function getFeedbackList(){
        $studentid = 144;
        $pagenum = $this->request->post('pagenum');
        $search = $this->request->post('search');
        $search = isset($search)?$search:'';
        $userobj = new UserManage;
        $limit = config('param.pagesize')['student_messagelist'];
        $res = $userobj->getFeedbackList($studentid,$pagenum,$limit,$search);
        $this->ajaxReturn($res);
    }

    /**
     * [submitApplylession 调班申请]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/submitApplyClasss
     */
    public function submitApplylession(){
        $curriculumid = 1;
        $oldlessonsid = 23;
        $newlessonsid = 43;
        $studentid = 1;
        $currobj = new MyCourseManage;
        $data = $currobj->submitApplyLession($studentid,$curriculumid,$oldlessonsid,$newlessonsid);
        $this->ajaxReturn($data);

    }
    /**
     * [getHomeworkByLessionid 通过课时查看学生作业]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getHomeworkByLessionid
     */
    public function getHomeworkByLessionid(){
        $lessonsid = 1094;
        $studentid = 143;
        $currobj = new MyCourseManage;
        $data = $currobj->getHomeworkByLessionid($lessonsid,$studentid);
        $this->ajaxReturn($data);

    }
    /**
     * [getTeacherComment 获取老师点评]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Home/getTeacherComment
     */
    public function getTeacherComment(){
        $lessonsid = 1140;
        $studentid = 146;
        $currobj = new MyCourseManage;
        $data = $currobj->getTeacherComment($lessonsid,$studentid);
        $this->ajaxReturn($data);

    }
    /**
     * 查询订单状态
     * @Author yr
     * @DateTime 2018-04-23T13:11:19+0800
     * @param ordernum string 订单号
     * @param type int 类型1查询课程2查询套餐
     * @return   array();
     * URL:/student/Myorder/queryOrderStatus
     */
    public function queryOrderStatus()
    {
        $ordernum = '201809191001032685699119';
        $type = 2;
        $type = isset($type)?$type:1;
        $orderobj = new MyOrderManage;
        $res = $orderobj->queryOrderStatus($ordernum,$type);
        $this->ajaxReturn($res);
    }
    /**
     * [studentCourseList 学生课表]
     * @Author yr
     * @DateTime 2018-04-25T09:44:53+0800
     * @return   [type]                   [description]
     * URL:/student/Mycourse/studentCourseList
     */
    public function studentCourseList(){
        //生成缓存维一标识
        $obj  = new \Verifyhelper();
        $res = $obj->verify();
        print_r($res['codeimg']);die();
        $mobile = '182351027';
        $prphone = '86';
        $messageobj = new \Messages;
        $params = [$mobile];
        $result  = $messageobj->sendMeg($mobile,$type=11, $params,$prphone);
        print_r($result);die();
        die();
        //$organid = Session::get('organid');
        $studentid = '144';
        //指定 获取的 时间
        $date = '2018-9-4' ;
        //如果没有提供 使用当前日期
        if(empty($date)) $date = date('Y-m-d') ;
        $organobj = new MyCourseManage;
        $listarr = $organobj->studentCourseList($date,$studentid);
        $this->ajaxReturn($listarr);
    }
    public function getCurriculumInfo()
    {
        //查询学生购买过得课程的课节总数
        $studentid = 1;
        $ordermodel = new Ordermanage();
        $where = [
            'studentid' => 1,
            'coursetype' => 2,
            'orderstatus' => 20,
        ];
        $scheduingarr = $ordermodel->getAllScheding($where);
        $applyclassmodel = new Applyschedulingrecord();
        $changeawhere = [
            'studentid' => $studentid,
            'status' => 1,
        ];
        $changefield = 'oldschedulingid';
        $changearr  = $applyclassmodel->getColumnIds($changeawhere,$changefield);//把调班的班级id剔除
        foreach($scheduingarr as $k=>$v){
            if(in_array($v,$changearr)){
                unset($scheduingarr[$k]);
            }
        }
        $scheduingarr = array_values($scheduingarr);
        $lessonmodel = new Lessons();
        $lessonwhere = [
            'schedulingid' => ['in',$scheduingarr],
            'delflag' => 1
        ];
        $lessonsids = $lessonmodel->getLessonids($lessonwhere);//未调过课的所有课时id
        //剔除所有调过课的原课时id 新增所有新的课时id
        $applylessonmodel = new Applylessonsrecord();
        $applywhere = ['studentid'=>$studentid,'status'=>1];
        $applyoldfield = 'oldlessonsid';
        $applynewfield = 'newlessonsid';
        $oldlessonsids = $applylessonmodel->getColumnIds($applywhere,$applyoldfield);//所有调过课的课时id
        $newlessonsids = $applylessonmodel->getColumnIds($applywhere,$applynewfield);//所有调过课的新的课时id
        $oldclassids = $applyclassmodel->getColumnIds($applywhere,$applynewfield);//旧班级的所有课时id集合
        $newclassids = $applyclassmodel->getColumnIds($applywhere,$applynewfield);//新班级的所有课时id集合
        //调过班的课时id追加
        if(!empty($oldclassids)){
            foreach($oldclassids as $k=>$v){
                if(!empty($v)){
                    $oldclassids[$k] = explode(',',$v);
                    foreach($oldclassids[$k] as $key=>$value){
                        array_push($lessonsids,$value);
                    }
                }
            }
        }
        //调过班的新课时id追加
        if(!empty($newclassids)){
            foreach ($newclassids as $k=>$v){
                if(!empty($v)){
                    $newclassids[$k] = explode(',',$v);
                    foreach($newclassids[$k] as $key=>$value){
                        array_push($lessonsids,$value);
                    }
                }
            }
        }
        if(!empty($oldlessonsids)){
            foreach($lessonsids as $k=>$v){
                if(in_array($v,$oldlessonsids)){
                    unset($lessonsids[$k]);
                }
            }
        }
        $newidarr = array_merge($lessonsids,$newlessonsids);
        print_r($newidarr);
        var_dump($scheduingarr);die();
        $courseid= 160;
        $teacherid = $this->request->param('teacherid');
        $date = $this->request->param('date');
        $fullpeople = $this->request->param('fullpeople');
        $scheduobj = new ScheduManage();
        $res =  $scheduobj ->getCurriculumInfo($courseid,$teacherid,$date,$fullpeople);
        $this->ajaxReturn($res);

    }
    /**
     * [ 获取可选择的课时名称]
     * @Author yr
     * @DateTime 2018-04-27T14:06:17+0800
     * @return   [type]                   [description]
     * URL:student/Mycourse/getSelectableLessons
     */
    /**
     * 学生选择支付方式付款
     * @Author yr
     * @DateTime 2018-04-28T13:11:19+0800
     * @param  string ordernum 订单号
     * @param  float usablemoney账户余额
     * @param  paytype支付方式 如: 1 或 1,2
     * @param  amount 订单价格
     * @param  studentid 学生id
     * @param  coursename 课程名称
     * @param  classtype 班级类型
     * @param  gradename 课程名称
     * @return   array();
     * URL:/student/Myorder/gotoPay
     */
    public function gotoPays()
    {
        $toteachmodel = new Toteachtime;
        $time = time();
        $wherearr = [
            'schedulingid' =>159 ,
            'delflag' => 1,
            'starttime' => ['lt',$time],
        ];
        $toteachwhere = [
            'schedulingid' => 189,
            'delflag' => 1,
            'starttime' => ['gt',$time],
        ];
        $oldlessons = $toteachmodel->getStudentAttendLesson($wherearr);
        $oldlessonstr = implode(',',$oldlessons);
        $newlessons = $toteachmodel->getStudentAttendLesson($toteachwhere);
        $newlessonstr = implode(',',$newlessons);
        echo $newlessonstr;echo "<pre>";
        echo $oldlessonstr;die();
        $studentid = 1;
        $ordernum = '201809181158179571582803';
        $paytype = '1,2';
        $orderobj = new PackageManage();
        /*  $studentid = 1;
          $ordernum = '201805261741194972438610';
          $amount = '200.00';
          $usablemoney = '99998404.00';
          $paytype = '2';
          $coursename = 'php精讲';
          $classtype = '2';
          $gradename = '班级名称';*/
        $res = $orderobj->gotoPay($studentid,$ordernum,$paytype);
        $this->ajaxReturn($res);

    }
    /**
     * 已完成的作业发送消息提醒
     * @Author yr
     * @DateTime 2018-04-23T20:11:19+0800
     * @param    [int]              homeworkid  学生作业表id
     * @return   array();
     * URL:/student/Home/sendHomeworkMessage
     */
    public function sendHomeworkMessage(){
        $homeworkid = $this->request->param('homeworkid');
        $homeworkid = 4;
        $studentid = 144;
        $obj = new UserManage();
        $res = $obj ->sendHomeworkMessage($studentid,$homeworkid);
        $this->ajaxReturn($res);
    }
    /**
     * 删除优惠券
     * @Author yr
     * @DateTime 2018-05-02T15:11:19+0800
     * @param packageuseid int 优惠券使用的id
     * @return   array();
     * URL:/student/Home/deletePackageUse
     */
    public function deletePackageUse()
    {
        $packageuseid = $this->request->post('packageuseid');
        $orderobj = new PackageManage();
        $res = $orderobj->deletePackageUse($packageuseid);
        $this->ajaxReturn($res);
    }
    /**
     * 查询我的课程
     * @Author yr
     * @DateTime 2018-04-25T14:11:19+0800
     * @param    studentid int   学生id
     * @param    coursetype  int   1录播课2直播课
     * @param    pagenum int   分页页数
     * @param    limit int   每页页数
     * @return   array();
     * URL:/student/Home/getBuyCurriculum
     */
    public function getBuyCurriculum()
    {

        $studentid = 1;
        $pagenum = 1;
        $coursetype = 2;
        /*$limit = $this->request->param('limit');*/
        $limit = config('param.pagesize')['student_curriculum'];
        $courseobj = new MyCourseManage;
        $res =  $courseobj ->getBuyList($studentid,$coursetype,$pagenum,$limit);
        $this->ajaxReturn($res);

    }
    public function getSelectableLessons(){
        $date = date("Y-m-d");
        $firstday = date('Y-m-01 00:00:00', strtotime($date));  //本月第一天
        $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day")); //本月最后一天
        $studentid =1;
        $startday= strtotime( $firstday);
        $endday= strtotime(  $lastday);
        $lessonsid = $this->request->post('lessonsid');//原课时id
        $lessonsid = 1254;//原课时id
        $oldschedulingid = $this->request->param('schedulingid');//原班级id
        $oldschedulingid = 168;//原班级id
        $curriculumid = $this->request->param('curriculumid');//原班级id
        $curriculumid =146;//原班级id
        $periodid = $this->request->param('periodid');//原班级id
        $periodid = 166;//原班级id
        $obj = new MyCourseManage();
        $result = $obj->getSelectableLessons($studentid,$lessonsid,$oldschedulingid,$curriculumid,$periodid);
        $this->ajaxReturn( $result);
    }


   public function ceshitesta(){
       $studentid = 143;
       $model = new MyCourseManage();
       $result = $model->getAllClassList($studentid );
        $this->ajaxReturn($result);
       $packageusemodel = new Coursepackageuse();
       $studentid = 1;
       $useinfo['packagegiftid'] = 50;
       $useinfo['usenum'] = 1;
       $where = [
           'studentid' => $studentid,
           'packagegiftid' => $useinfo['packagegiftid'],
       ];
       $usedata = [
           'ifuse'=> 1,
           'usetime'=> time(),
           'surplus' => Db::raw('surplus-'. $useinfo['usenum']),
       ];
$update_use_res = $packageusemodel->updateData($where,$usedata);
echo $update_use_res;die();
die();
       $group[1] = 1;
      for($i=1;$i<5;$i++){
            if(empty($group[$i])){
                $group[$i] = $i;
            }
      }
       dump($group);die();
       $redisobj = new \Redis();
       $redisobj->connect('192.168.1.3');
 /*      $res = $redisobj->hGet('converting1','00004-fade-4a64-904e-e8237f82a7f4-37923          8062');
       dump($res);die();*/
       $result = $redisobj->hGetAll('converting1');
       dump($result);die();
        $sql= 'show create table nm_studentattendance';
        $result = Db::query($sql);
        print_r($result);die();
       $v['attendancestatus'] = null;
       dump($v['attendancestatus'] !== 0);
       dump(!isset($v['attendancestatus']));
       $attendancestatus = $v['attendancestatus'] !== 0&&!isset($v['attendancestatus'])?1:$v['attendancestatus'];//默认是出勤
       echo $attendancestatus;die();
       snappy_uncompress();
       $categorymodel = new Category();
       $result = $categorymodel->getCategory();
       $res = generateTree($result,'category_id');
       $this->ajaxReturn($res);
       $ordermodel= new Order();
       $out_trade_no = '201809042003553024230938';
       $trade_no = '201809042003553024230938as';
       $buyer_logon_id = '6225317174@qq.com';
       $amount = '0.05';
       $paytype = 2;
       $ordermodel->dealwithPackage($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);
   }
   public function sendmobile(){
       $orderobj = new Order();
       $orderobj->batchCancelPackageOrders();
   }
   public function ceshid(){
        $a = [
            0=>['name'=>1],
            1=>['name' =>0],
            2=>['name' =>1],
        ];
        foreach($a as $k=>$v){
            if($v['name'] == 0){
                echo $k;
            }
            echo $k.":";
        }
        die();
       $out_trade_no = '201808291000087401044141';
       $trade_no = '2018454678787878778';
       $buyer_logon_id = '625317174@qq.com';
       $amount = '0.01';
       $paytype = 2;
       $ordermodel = new Order;
       $result = $ordermodel->dealwithOrder($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);
       dump($result);die();
   }
   public function intoroom(){
       $data  = ['delflag'=>0 ,'recommend'=> 0 ] ;
       $where = ['teacherid'=>1] ;

       Db::table('nm_teacherinfo')->where($where)->exp('teachername',' concat("#",teachername)' )->update($data) ;
       $sql =    Db::table('nm_teacherinfo')->getLastSql();
       echo $sql;die();
       $ceshi = lang('success');
       dump($ceshi);die();
        $time= date('Y-m-d',-18259200);
        echo $time;die();
       $date = '1969-6-4';
       $time = mktime(0,0,0,06,04,1969);
       echo $time;die();
       $amount = '2.49';
       $balance = '1.89';
       $amounts =  $amount *100;
       $usablemoneys = $balance *100;
       $amount  = bcsub($amounts,$usablemoneys)/100;
       echo $amount;die();
       $toteachid = 389;
       $organid =1;
       if($toteachid<1 || $organid<1) return return_format('',33027,lang('param_error'));
       //实例化模型
       $playbackmodel = new Playback;
       $teachermodel = new Teacherinfo;
       $data = $playbackmodel->getVideourl($toteachid);
       if(empty($data)){
           return return_format('',33028,lang('33028'));
       }
       foreach($data as $k=>$v){
           $videoinfo[$k]['playpath'] = $v['playpath'];
           $videoinfo[$k]['https_playpath'] = $v['https_playpath'];
           //时间戳转化为时分秒
           $videoinfo[$k]['duration'] = secToTime(ceil($v['duration']/1000));
           $videoinfo[$k]['part'] = $k+1;
       }
       //获取老师名称
       $teachername = $teachermodel->getTeacherId($data[0]['teacherid'],'teachername');
       $newarr['teachername'] = $teachername['teachername'];
       //获取上课时间
       if(!empty($timearr[0])){
           $hourarr = explode(':',get_time_key($timearr[0])) ;
           $datearr = explode('-',$data[0]['intime']) ;
           $unixtime = mktime($hourarr[0],$hourarr[1],0,$datearr[1],$datearr[2],$datearr[0]) ;
           $newarr['starttime'] = date('Y-m-d H:i:s',$unixtime);
       }else{
           $newarr['starttime'] = '';
       }
       //获取课时名称
       $learnsmodel = new Lessons;
       $lessonsname = $learnsmodel->getFieldName($data[0]['lessonsid'],'periodname');
       $newarr['lessonsname'] = $lessonsname['periodname'];
       $newarr['video'] =  $videoinfo;
       $res = return_format($newarr,0,lang('success'));
       $this->ajaxReturn($res);die();
     $obj = new Lang;
      $obj->load(APP_PATH.'lang/'.'en-us.php');
      $res =  Lang('success');
      dump($res);die();
       //实例化模型
     /*  $classmodel = new Classroom;
       $organconfigmodel = new Organconfig;
       $teacherobj = new TeacherInfo;
       $nicknamearr = $teacherobj->getNick($teacherid,$organid);
       $nickname = $nicknamearr['nickname'];
       $keyarr  = $organconfigmodel->getRoomkey($organid);
       $key = $keyarr['roomkey'];
       $classinfo = $classmodel->getClassInfo($toteachid);
       //如果无法获取教室信息？则开教室
       if(empty($classinfo)){
           $obj = new Docking;
           $toteachmodel = new Toteachtime();
           $list = $toteachmodel->getTimeList($toteachid);
           $adminteachmodel = new \app\admin\model\Toteachtime();
           $obj->operateRoomInfo($list, $adminteachmodel,$organid);
           $classinfo = $classmodel->getClassInfo($toteachid);
           if(empty($classinfo)){
               return return_format('',33125,'系统繁忙请稍后再试');
           }
       }*/
         $key = 'crmf32aQ5Qr1MXpC';
       $classinfo['classroomno'] = '1491392143';
       $nickname = 'ceshi';
       $time  = time();
       $sign =  MD5($key.$time.$classinfo['classroomno'].'0');
       $userpassword = getencrypt('900209',$key);
       $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$classinfo['classroomno']}/username/$nickname/usertype/0/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
       echo $url;die();
       $data['url'] = $url;
   }
    public function devideRoleInit(){
        //查看 目前的角色
        $groups = Db::table('nm_accessrole')
            ->field('id,rolename')
            ->select();

        $data = [] ;
        foreach ($groups as $group) {
            $result = Db::table('nm_accessnode')
                ->field('id')
                ->where('module','EQ',$group['rolename'])
                ->whereOr('module','EQ','app'.$group['rolename'])
                ->select();

            foreach ($result as $value) {
                array_push($data,['roleid'=>$group['id'],'nodeid'=>$value['id']]);
            }

        }

        $result = Db::table('nm_accessroleallow')
            ->insertAll($data);

        // var_dump($data);exit();

    }
    public function qrcode(){
        $ordernum = '201806041549189797232804';
        $afterid = '206';
        $model = new Toteachtime();
        $afterlist = $model->getTimekeyByOrdernum($ordernum,$afterid);
     /*   $afterlist['timekey'] = explode(',', $afterlist['timekey']);*/
        if (!empty(  $afterlist)) {
            echo  '33333';
            $aftertime = strtotime($afterlist['intime'] . ' ' . get_time_key($afterlist['timekey'][0]));
            echo "11";die();
            if ($currtime >= $aftertime) {
                return return_format('', 33107, '当前课时时间设置晚于后一课时的时间');
            }
        }
        echo '2222222';die();
        dump($afterlist);die();
        $password = 'dsadad';
        $result =  preg_match('/^[_0-9a-z]{6,16}$/i',$password);
        dump($result);die();
        $key =  'crmf32aQ5Qr1MXpC';
        $no = '1673163028';
        $time  = time();
        $sign =  MD5($key.$time.$no.'2');
        $classinfo['confuserpwd'] = '553155';
        $userpassword = getencrypt($classinfo['confuserpwd'],$key);
        $url  = "http://global.talk-cloud.net/WebAPI/entry/domain/51menke/serial/{$no}/username/whj/usertype/2/pid/0/ts/$time/auth/$sign/userpassword/$userpassword/servername//jumpurl/http://www.talk-cloud.com";
        echo $url;die();
        $data['url'] = $url;
        $toteachmodel = new \app\admin\model\Toteachtime();
        dump($toteachmodel);die();
        dump($_SERVER);DIE();
        $returnurl = $_SERVER['HTTP_HOST'];
        echo $returnurl;die();
        $num = '1.95';
        $res=sprintf("%.2f", $num);

        echo $res;die();
        $array = [
            'during'=>31536000,
            'organid'=>44,
        ];
        $obj = new Organ;
        $res = $obj->upVipTime($array);
        /*echo $res;die();
        $notify = new  WechatPayNotify;
        $notify->Handle();
        $alipay = new Alipaydeal;
        $wxobj = new Wxpay;
        $ordernum = '201806111155561857698113';
        $resul =   $alipay->closeOrder($ordernum);
        dump($resul);die();
        $url = 'https://www.baidu.com';
        $url = 'https://www.baidu.com';//加http://这样扫码可以直接跳转url
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $object = new QRcode();
        ob_start();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
        $imageString = ob_get_contents();
        ob_end_clean();
        $image_data_base64 = 'data:image/png;base64,'.base64_encode ($imageString);
        return $image_data_base64;


        //$path=ROOT_PATH.'public/static/images/qrcode/';
        //$QRcode->png($data,$path.$fileName,$level,$size);// 生成本地图片
        $value = $url;                  //二维码内容
        $res = $obj::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);
        $QR = $filename;                //已经生成的原始二维码图片文件
        $QR = imagecreatefromstring(file_get_contents($QR));
        //输出图片
        imagepng($QR, 'qrcode.png');
        imagedestroy($QR);*/
    }
    function base64EncodeImage ($image_file) {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
    public function getCourse()
    {
        $obj =  new Wxpay;
        $url = 'dsds';
        $res = $obj->createWxQrcode($url);
        dump($res);die();
        $ordernum = $this->request->post('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->queryOrder($ordernum);
        dump($res);die();
        $loginobj = new UserLogin;
        $post = $loginobj->rsaDecode('aoS4IJaTIqFrOgQolq7VtQj8vzeEO9PfjTaDVeToQ6N9tRQmt5nmaTGLw8g7gOB943nq5DIqfQCqF9QsFExLiYr1UbUQ6wnBdt4gK2BOJ312bf3HT39vr7RtxIzNEzbuc0sJl0+cWKG4w4NeTDONk01kpzefMK7U6TT5pekYv3CiywuyXAfoegLWLMpmKRmNwqTnzsGuli5IP/BDeVT/mxrWVu7ZYtrHDLg8oXZ8Hnu6Bbvf+gEq0t7ocj2Aus7A5FH+rnaGYZXjux/jsZu+YhP8Tx5aXC3Qx0JQSxMC0C4mp4/bhPmQn0F3ZkdfYppsjiPW1frKV2Qi71sQLGCdjA==');
        dump($post);die();
        $obj = new Loginbase;
        $res = $obj->checkUserLogin();
        dump($res);die();
        //实例化redis
          $redis = new Redis();
     //连接
    $redis->connect('127.0.0.1', 6379);
     //检测是否连接成功
   /* echo "Server is running: " . $redis->ping();die();*/


    // 输出结果 Server is running: +PONG
        echo $this->organid;die();
        $studentid = $this->userarray['uid'];
        $organid = $this->userarray['organid'];
        $ceshi = config('param.ceshi');
        $out_trade_no  = '201805091948257745801014';
        $trade_no   = '2018454555555555555555455';
        $buyer_logon_id  = 'dsadaserscxrerqaedadsad';
        $out_biz_no   = 'dasddddddddddddddddddsadsadsad';
        $amount  = '45.00';
        $paytype  = '2';
        $ordermodel = new Order;
        $res = $ordermodel->dealwithOrder($out_trade_no,$trade_no,$out_biz_no,$amount,$paytype);
        echo $res;die();
        die();
        //课程的ID
        $id = input('get.id');
        //课程的父id
        $fid = input('fatherid');
        //课程的层级
        $rank = input('rank');
        //标签的ID
        $tid = input('tagId');
        //标签的父id
        $tagFid = input('tagFid');

        $class = new LogicHome;

        $cours = $class->getCourse($id,$fid,$rank,$tid,$tagFid);

        echo $cours;

    }
    public function ceshi1(){
        //让APP端传递一串字符串用于校验使用
        $sendVerifyName= I('get.verifyName');
        //根据你定义的规则对str校验，这里忽略
        //...
        //生成缓存名，用于保存验证码
        //根据你的规则生成，比如md5加盐处理
        $sendVerifyName = md5($sendVerifyName . '12345');
        //实例化验证码对象
        $Verify = new \Think\Verify();
        //================你的验证码参数start==================
        $Verify->fontSize = 20;
        $Verify->length   = 4;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 150;
        $Verify->imageH = 50;
        $Verify->expire = 600;
        $Verify->fontttf = '5.ttf';
        //================你的验证码参数end==================
        //生成验证码
        $Verify->entry();
        //后续处理，用于验证，这里可以封装成函数操作
        //最好不要改下面的第二个和第三个参数，不然就需要修改\Think\Verify()原文件
        $key = substr(md5($Verify->seKey), 5, 8);
        $str = substr(md5($Verify->seKey), 8, 10);
        $key = md5($key . $str);
        S($sendVerifyName, session($key), 180);
    }
    public function ceshi(){
        $order = new Order;
        $res = $order->dealwithOrder();
        $file = '/static/ceshi.jpg';
        //var_dump($file);
        //$post['file'] = '@'.$file;
        $obj = new CurlFile($file);
        $obj->setMimeType("image/jpeg");//必须指定文件类型，否则会默认为application/octet-stream，二进制流文件</span>
        $post['file'] =  $obj;
        $post['abc'] = "abc";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        //启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_URL, "http://localhost/php/public/index.php/student/Home/index");//上传类

        $info= curl_exec($ch);
        curl_close($ch);
        var_dump($info);die();
        file_put_contents('./1.html',$info);
        $res=json_decode($info,true);
        //var_dump($res);
}
    public function index(){
        $path = ROOT_PATH . 'public' . DS . 'static';
        $array = file_get_contents($path.'/a.php');

        $array = explode(',',$array);
        $newarr = array();
        foreach($array as $k=>$v){
            $arr = explode('-',$v);
            $newarr[$k]['enname'] = $arr[0];
            $newarr[$k]['znname'] = $arr[1];
            $newarr[$k]['code'] = $arr[2];
        }
        /*$model = new City;
        $result = $model->getAllList();
        $newarray = array();
        foreach($result as $k=>$v){
            $newarray[$v['id']] = $v['name'];
        }*/
       $json_string = json_encode($newarr);
        file_put_contents($path.'/prphone.json',$json_string);
// 写入文件

    }

}
