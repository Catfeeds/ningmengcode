<?php
namespace app\student\controller;
use app\student\model\Category;
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
use think\Db;
use think\Lang;
use app\student\model\Lessons;
use app\index\business\UserLogin;
Loader::import('alipay.config');
Loader::import('alipay.pagepay.service.AlipayTradeService');
Loader::import('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
use Redis;
use app\student\model\Playback;
use app\student\model\Teacherinfo;
use wxpay\Wxpay;
use phpqrcode\qrcode;
use alipay\Alipaydeal;
use app\student\business\MyOrderManage;
use wxpay\WechatPayNotify;
use app\admin\model\Organ;
class Home extends Controller
{
   /* public function __construct(){
        // 必须先调用父类的构造函数
        parent::__construct();
    }*/
    /**
     * 学生端首页课程
     * @Author why
     */
   /* protected $userid;
    public function __construct()
    {
        echo $this->userid;die();
    }*/
   public function ceshitesta(){
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
       $wxpayobj = new Wxpay();
       file_put_contents('wxgzh.txt',print_r('查看是否有code:'.$code,true),FILE_APPEND) ;
       $openid = $wxpayobj->getOpenid($code);
       echo $openid;die();
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
