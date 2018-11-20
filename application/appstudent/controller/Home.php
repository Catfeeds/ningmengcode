<?php
namespace app\appstudent\controller;
use alipay\Alipaydeal;
use app\appstudent\business\AppMyCourseManage;
use app\appstudent\business\AppUserManage;
use app\microsite\business\MicroMyCourseManage;
use app\official\controller\Base;
use app\student\business\MyCourseManage;
use app\student\business\PackageManage;
use Order;
use think\cache\driver\Redis;
use think\Controller;
use think\Loader;
use CURLFile;
use app\student\model\City;
use Messages;
use Login;
use Verifyhelper;
use think\Request;
use think\View;
Loader::import('alipay.config');
Loader::import('alipay.pagepay.service.AlipayTradeService');
Loader::import('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
use think\exception;
use think\Queue;
use Filemimeverify;
use MongoDB\Driver\Manager;
use MongoCollection;
use wxpay\Wxpay;
use think\Db;

    class Home extends Controller
    {
        protected $wxnotifyurl = '/admin/ServerNotice/wxCourseNotify';
        protected $paytype = 6; //苹果支付类型 6
        protected $env = [
            'https://buy.itunes.apple.com/verifyReceipt',//正式验证接口地址
            'https://sandbox.itunes.apple.com/verifyReceipt'//沙箱验证接口地址
        ];
        public function redpackage(){//微信红包算法
            $url = 'http://talkcloud002.cn-gd.ufileos.com/talkcloud002_201810171555573843.jpg';
            $result = file_get_contents($url);
            $image_data_base64 = 'data:image/png;base64,' . base64_encode($result);
           echo  $image_data_base64;die();
            ob_start();
            print_r($result);
            $imageString = ob_get_contents();
            ob_end_clean();
            $image_data_base64 = 'data:image/png;base64,' . base64_encode($imageString);
            echo $image_data_base64;die();
            print_r($result);die();
            $num = 10;//定义红包数量
            $price = 10;//定义红包金额
            $min = 0.01;//定义最小红包金额
            for($i=0;$i<10;$i++){

            }

    }
        /**
         * 签到首页
         * @Author lc
         * @DateTime 2018-04-17T13:11:19+0800
         * @return   [type]                   [description]
         */
        public function signinHome()
        {
            $studentid = $this->request->param('studentid');
            //$studentid = $this->request->param('studentid');
            $userobj = new AppUserManage();
            $list = $userobj->getSigninHomeData($studentid);
            $this->ajaxReturn($list);
            return $list;
        }
        public function curl_test(){
           // $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4dde403819aa20f9&redirect_uri=http://ni//://ningmeng.talk-cloud.cn/home&response_type=code&scope=snsapi_userinfo&state=1&connect_redirect=1#wechat_redirect';
            $url = 'https://www.baidu.com/';
            $res = curl_get($url);
            print_r($res);die();
        }
        public function redis()
        {
            $redis = new \Redis();

            $redis->connect('127.0.0.1',6379);
            $result = $redis->get('sess:19584vusbo7htbosn8pdr8olt1');
            $data = explode('|',$result);
            $data = $data[1];
            $res = unserialize($data);
            $arr = array('c','c++','php','java','go','python');
            foreach($arr as $k=>$v){
                $redis->rpush("myqueue",$v);
                $redis->rpush("myqueue1",$v);
                echo $k."号入队成功"."<br/>";
                /*
                 *  0号入队成功
                 *  1号入队成功
                 *  2号入队成功
                 *  3号入队成功
                 *  4号入队成功
                 *  5号入队成功
                 */

            }

        }

        public function getClassSchedule()
        {
            $courseid = '240';
            $coursetype = '2';
            $schedulingid = '347';
            $courseobj = new MicroMyCourseManage();
            $res =  $courseobj ->getClassSchedule($coursetype,$courseid,$schedulingid);
            $this->ajaxReturn($res);

        }
        public function queuqout()
        {
            $redis = new \Redis();
            $redis->connect('127.0.0.1',6379);
            $value = $redis->rpop('mylist');
            //$value = $redis->lpop('myqueue');
            if($value){
                print_r("出队的值".$value) ;
            }else{
                print_r("完成") ;

            }
        }

        public function test2(){
            $wxpayobj = new Wxpay();
            $code = '0117bcDp1NPs0q0ZKiDp1UjuDp17bcDb';
            file_put_contents('wxgzh.txt',print_r('查看是否有code:'.$code,true),FILE_APPEND) ;
            $openid = $wxpayobj->getOpenid($code);
            echo $openid;die();
            $data = $this->request->param('companyid');
            var_dump($data);die();
        }
        public function searchNode(){
            $modules = ['apphjx'];  //模块名称
            $except_modules = ['Homepage.php']; //不需要添加的控制器名称
            $i = 0;
            foreach ($modules as $module) {
                $all_controller = $this->getController($module);

                foreach ($all_controller as $controller) {
                    $controller_name = $controller;
                    if(in_array($controller_name,$except_modules)){
                        unset($controller);
                        continue;
                    }
                    $all_action = $this->getAction($module, $controller_name);
                    foreach ($all_action as $action) {
                        $data[$i] = array(
                            'name' => strtolower($module).'_'.strtolower(basename($controller,".php")). '_' .strtolower($action),
                        );
                        $i++;
                    }
                }
            }

            $data = array_values($data);
            foreach($data as $k=>$v){
                $arr[$k] = explode('_',$v['name']);
                $where = [
                    'module'=> $arr[$k][0],
                    'controller'=> $arr[$k][1],
                    'action'=> $arr[$k][2],
                ];
                $isexist = $this->selectNode($where);
                if($isexist){
                   continue;
                }else{
                    //入库
                    $this->nodeInsertDb($arr[$k][0],$arr[$k][1],$arr[$k][2]);
                }

            }
        }

        //获取所有控制器名称
        protected function getController($module){
            if(empty($module)) return null;
            $module_path = APP_PATH  . $module . '/controller/';  //控制器路径
            if(!is_dir($module_path)) return null;
            $module_path .= '*.php';
            $ary_files = glob($module_path);
            foreach ($ary_files as $file) {
                if (is_dir($file)) {
                    continue;
                }else {
                    $files[] = basename($file);
                }
            }
            return $files;
        }

        //获取所有方法名称
        protected function getAction($module, $controller){
            if(empty($controller)) return null;
            $content = file_get_contents(APP_PATH .$module.'/controller/'.$controller,true);
            preg_match_all("/^\s*public.*?function(.*?)\(.*?\)/im", $content, $matches);
            $functions = $matches[1];
            //排除部分方法
            $inherents_functions = array('_initialize','__construct','getActionName','isAjax','display','show','fetch','buildHtml','assign','__set','get','__get','__isset','__call','error','success','ajaxReturn','redirect','__destruct','_empty');
            foreach ($functions as $func){
                $func = trim($func);
                if(!in_array($func, $inherents_functions)){
                    $customer_functions[] = $func;
                }
            }
            return $customer_functions;
        }
        /**
         *  将节点入库
         *
         */
        private function nodeInsertDb($module,$controller,$action){
            $data = [
                'module'    => $module ,
                'controller'=> $controller ,
                'action'    => $action ,

            ] ;
            Db::startTrans();
            $insertid = Db::table('nm_accessnode')->insertGetId($data);
            if($module == 'admin'){
                $roleid = 1;
            }elseif($module == 'teacher'){
                $roleid = 2;
            }elseif($module == 'apphjx'){
                $roleid = 4;
            }else{
                $roleid = 3;
            }
            $info = [
                'roleid' =>$roleid,
                'nodeid' =>  $insertid
            ];
            $result = Db::table('nm_accessroleallow')->insert($info);
            if($insertid && $result){
                Db::commit();
            }else{
                Db::rollback();
            }
        }
        /**
         *  查询节点是否存在
         *
         */
        private function selectNode($where){
            $result = Db::table('nm_accessnode')->where($where)->count();
            return $result;
        }
        public function gotoPaytest()
        {
            //$studentid = $this->userid;
            $studentid = 5;
            $ordernum = '201810191702409676197019';
            $paytype = 2;
            $orderobj = new PackageManage();
            $baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."/studentid/$studentid/ordernum/$ordernum/paytype/$paytype");
            /*  $studentid = 1;
              $ordernum = '201805261741194972438610';
              $amount = '200.00';
              $usablemoney = '99998404.00';
              $paytype = '2';
              $coursename = 'php精讲';
              $classtype = '2';
              $gradename = '班级名称';*/
            $res = $orderobj->gotoPay($studentid,$ordernum,$paytype,$type=3,$baseUrl);
            $this->ajaxReturn($res);

        }
        public function appalipay(){
            $model = new Alipaydeal();
            $out_trade_no = getOrderNum();
            $subject = '测试';
            $total_amount= '0.01';
            $body = '测试';
            $returnurl = 'http://test.menke.com/web';
            $callbackurl = 'http://test.menke.com/web';
            $result = $model->appAlipay($out_trade_no,$subject,$total_amount,$body,$returnurl,$callbackurl,$timeExpress = '29m');
            print_r($result);die();
        }
        public function jsapi(){
            $rechargenum = getOrderNum();
            $title = '微信公众号支付';
            $total_amount = '0.01';
            $profile = '测试';
            $returnrul = config('param.server_url') . $this->wxnotifyurl;//异步回调地址
            $obj  = new Wxpay();
            $code = 111;
            $result = $obj->jsapiWxpay($rechargenum,'学生充值', $total_amount,'充值', $returnrul,$code);
            //$result = '{"appId":"wx4dde403819aa20f9","nonceStr":"chvarm1ut5jbe07ziofshzu87048ker3","package":"prepay_id=wx281802498681205d0d8d85023429915561","signType":"MD5","timeStamp":"1538128969","paySign":"77AE4F60F7AEE869F8D012AA63FD1A83"}';
            //$view = new View();
            $data =json_decode($result,true);
           $this->ajaxReturn($data);

        }
        public function getOpenid(){
            if(!isset($_GET['code'])){
                $obj = new Wxpay();
                $obj->getOpenid();
            }else{
                $code = $_GET['code'];
                $data['code'] = 0;
                $data['msg'] = '操作成功';
                $data['data'] = $code;
                $this->ajaxReturn($data);
            }
        }
        public function getWxcode(){
            $code = $_GET['code'];
            $data['code'] = 0;
            $data['msg'] = '操作成功';
            $data['data'] = $code;
            $this->ajaxReturn($data);
        }
        public function test(){
            $view = new View();
            $view->display('ceshi');
        }

        /**
         * [submitApplylession 调班申请]
         * @Author yr
         * @DateTime 2018-04-27T14:06:17+0800
         * @param    [string]           date  必填日期 eg: 2018-04-06
         * @return   [type]                   [description]
         * URL:appstudent/Mycourse/submitApplylession
         */
        public function submitApplylession(){
            $curriculumid = 174;//原课程id
            $oldlessonsid = 1387;//原班级id
            $newlessonsid = 1392;//新班级id
            $studentid = 145;
            $currobj = new MyCourseManage;
            $data = $currobj->submitApplyLession($studentid,$curriculumid,$oldlessonsid,$newlessonsid);
            $this->ajaxReturn($data);

        }
        /**
         * @Author YR
         *  去苹果服务器二次验证代码
         * @param [$receipt]        客户端支付的收据 二进制流文件
         * @param [$requesturl]      发起请求的url
         * @DateTime 2018-08-11
         * @return   [type]                   [description]
         */
        public function cehsiya(){
            $result = 'CN￥1.25';
            $res = substr($result,5);
            $amount = $res;
            $usablemoney = '0.00';
            //增加可用余额
            $usablemoney += $amount;
            echo $usablemoney;die();
            echo $res;die();
            $receipt = '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';
            $url = $this->env[1];
            $res = $this->getReceiptData($receipt,$url);
            print_r($res);die();
        }
        protected function getReceiptData($receipt, $requesturl) {
            $postData = json_encode(
                array('receipt-data' => $receipt)
            );
            $ch = curl_init($requesturl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $response = curl_exec($ch);
            $errno    = curl_errno($ch);
            //$errmsg   = curl_error($ch);
            curl_close($ch);
            if ($errno != 0) {//curl请求有错误
                return [
                    'errNo' => -3002,
                    'errMsg' => '请求超时，请稍后重试',
                ];
            }else{
                $data = json_decode($response, true);
                if (!is_array($data)) {
                    return [
                        'errNo' => -3003,
                        'errMsg' => '苹果返回数据有误，请稍后重试',
                    ];
                }
                //判断购买时候成功
                if (!isset($data['status']) || $data['status'] != 0) {
                    // 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
                    if($data['status'] == 21007){
                        //调取沙箱环境支付 调用沙箱支付url
                        $sandbox =  1;
                        //获取当前的环境url
                        $url = $this->env[$sandbox];
                        $res = $this->getReceiptData($receipt,$url);
                        return $res;

                    }
                    return [
                        'errNo' => -$data['status'],
                        'errMsg' => '购买失败',
                    ];
                }
                //返回产品的信息
                $order = $data['receipt']['in_app'][0];
                $order['errNo'] = 0;
                return $order;
            }
        }

        public function appPay(){
            $out_trade_no = getOrderNum();
            $subject = '测试的';
            $total_amount = '0.01';
            $body = '测试';
            $returnurl = '/organweb#/paymentSuccess?type=0&order=01807051234554';
            $callbackurl = 'http://share.51menke/index/Alipay/wxnotify';
            $alipayobj = new Alipaydeal;
            $result = $alipayobj->appAlipay($out_trade_no,$subject,$total_amount,$body,$returnurl,$callbackurl,$timeExpress = '29m');
            $data = [
                'code' => 0,
                'data' => $result,
                'msg' => '操作成功',
            ];
            $this->ajaxReturn($data);
        }
        public function appWxPay(){
            $out_trade_no = getOrderNum();
            $subject = '测试的';
            $total_amount = '0.01';
            $body = '测试';
            $callbackurl = 'http://share.51menke/index/Alipay/wxnotify';
            $alipayobj = new Wxpay;
            $result = $alipayobj->appWxPay($out_trade_no, $subject, $total_amount, $body, $callbackurl);
            $data = [
                'code' => 0,
                'data' => $result,
                'msg' => '操作成功',
            ];
            $this->ajaxReturn($data);
        }
        public function  upload(){
            phpinfo();die();
            $file = $_FILES;
            dump($file);die();
            $path = $_FILES['upload']['tmp_name'];
            dump($path);
            //检查文件真实扩展名
            $mimeobj = new Filemimeverify;
            $result = $mimeobj->_getMimeType($path);
            echo $result;die();
            dump($file);die();
        }
        public function actionWithHelloJob(){

            // 1.当前任务将由哪个类来负责处理。
            //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
            $jobHandlerClassName  = 'app\appstudent\Home\actionWithHelloJob';
            // 2.当前任务归属的队列名称，如果为新队列，会自动创建
            $jobQueueName  	  = "helloJobQueue";
            // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
            //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
            $jobData       	  = [ 'ts' => time(), 'bizId' => uniqid() , 'a' => 1 ] ;
            // 4.将该任务推送到消息队列，等待对应的消费者去执行
            $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
            // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
            if( $isPushed !== false ){
                echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
            }else{
                echo 'Oops, something went wrong.';
            }
        }
        public function mongo_ceshi(){

        }
        public function pathinfo(){
           phpinfo();
        }
        public function  ajax_ceshi(){
            $data = [
                'code' =>0,
                'info' => '成功',
                'data'=> ''
            ];
            $this->ajaxReturn($data);
    }
        public function ceshiy(){
                //serial 教室编号 int 9  必填，教室号，非 0 开始的数字串，
                echo $this->userid;
                $time  = time();
                $sign =  MD5('LNIWjlgmvqwbt4hy'.$time.'139284386'.'0');
                $ckey = 'LNIWjlgmvqwbt4hy';
                $chairmansourcepwd='1';
                $ChairmanPWD = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $ckey, $chairmansourcepwd, MCRYPT_MODE_ECB);
                $userpassword = bin2hex($ChairmanPWD);
                $url  = "http://demo.talk-cloud.net/WebAPI/entry/domain/www/serial/13      9284386/username/whj/usertype/0/pid/0/ts/$time/auth/$sign/userpassword/ $userpassword/servername//jumpurl/http://www.talk-cloud.com";
                echo $url;die();
        }
        public function home(){
            $url = 'http://talkcloud002.cn-gd.ufileos.com/talkcloud002_201810291030485473.jpg';
            $url1 = 'http://talkcloud002.cn-gd.ufileos.com/talkcloud002_201810311924072625.png';
            $data['result'] = get_base64_img($url);
            $data['result1'] = get_base64_img($url1);
            $this->ajaxReturn($data);
            $view = new View();

            return $view->fetch('base');

        }
        public function verify(){
            $obj = new  Verifyhelper;
            $obj->verify();
        }
        /**
         * 学生端首页课程
         * @Author why
         */
        public function getCourse ()
        {
            $out_trade_no  = '201804281944329376183807';
            $trade_no   = '2018454555555555555555455';
            $buyer_logon_id  = 'dsadaserscxrerqaedadsad';
            $out_biz_no   = 'dasddddddddddddddddddsadsadsad';
            $amount  = '45.00';
            $paytype  = '2';
            $ordermodel = new Order;
            $ordermodel->delOrder($out_trade_no,$trade_no,$buyer_logon_id,$out_biz_no,$amount,$paytype);
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
        public function ceshi(){
            $view = new View();

            return $view->fetch('ceshi');
           /* $file = '/static/ceshi.jpg';
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
            $res=json_decode($info,true);*/
            //var_dump($res);
    }
        public function index(){
            $obj = new Messages;
            $obj->sendMeg();
            $out_trade_no = '201805091016519432276325';
            $trade_no = '201845555555555555555555555555';
            $buyer_logon_id = '18235102743';
            $amount = '45.00';
            $paytype = 2;
            $orderobj  = new Order;
            $res = $orderobj->dealwithRecharge($out_trade_no,$trade_no,$buyer_logon_id,$amount,$paytype);
            echo $res;die();
             DIE();
            $alipay = new Alipaydeal;
            $out_trade_no = '111';
            $subject = 1;
            $total_amount = 111;
            $body = 111;
            $returnrul = 'dsd';
            $alipay->createPayRequest($out_trade_no,$subject,$total_amount,$body,$returnrul);die();
            $path = ROOT_PATH . 'public' . DS . 'static';
            $model = new City;
            $result = $model->getAllList();
            $newarray = array();
            foreach($result as $k=>$v){
                $newarray[$k][$v['id']] = $v['name'];
            }
            $json_string = json_encode($newarray);

            $res = file_put_contents($path.'/city.json',$json_string);
    // 写入文件

        }

    }
