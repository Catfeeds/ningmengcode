<?php
namespace app\index\controller;
// use think\Controller;
use think\Session;
use login\Authorize;
use login\Rsa;
class Index extends Authorize
{   

    public function index()
    {	
        
        //http://global.talk-cloud.net/ClientAPI/checkroom?serial=2063061506&userrole=2&nickname=123&password=4
        $fp = fsockopen("global.talk-cloud.net", 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $out = "GET /ClientAPI/checkroom?serial=2063061506&userrole=2&nickname=123&password=4 HTTP/1.1\r\n";
            $out .= "Host: global.talk-cloud.net\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            while (!feof($fp)) {
                file_put_contents('aa.txt',fgets($fp, 1024),FILE_APPEND );
            }
            fclose($fp);
        }
        // $fp = fsockopen("demo.talk-cloud.net", 80, $errno, $errstr, 30);
        // if (!$fp) {
        //     echo "$errstr ($errno)<br />\n";
        // } else {
        //     $out = "GET /ClientAPI/checkroom?serial=664628333&userrole=2&nickname=123&userpwd=admin&account=admin HTTP/1.1\r\n";
        //     $out .= "Host: demo.talk-cloud.net\r\n";
        //     $out .= "Connection: Close\r\n\r\n";
        //     fwrite($fp, $out);
        //     while (!feof($fp)) {
        //         file_put_contents('aa.txt',fgets($fp, 1024),FILE_APPEND );
        //     }
        //     fclose($fp);
        // }

        // $name = 'wangyeanbo';
        // echo uniqid();
        // exit();
    	// Session::set('tset','you name little dog');
    	// echo Session::get('tset');
     //    return '';
    }
    /**
     * [index 用户登陆验证]
     * @Author wyx
     * @DateTime 2018-04-27T11:34:51+0800
     * @return   [type]                   [description]
     * usertype 后台的 管理员 可以入 012 然后从session 中获取 usertype 得到真正的类型
     * http://local.menke.com/index/index/userAuth/username/updatename123/password/123456
     */
    public function userAuth()
    {	
    	$user = $this->request->param('username') ;
    	$pass = $this->request->param('password') ;
    	$type  = 2 ;// 根据 不同的入口 来确定 是教师 还是

    	$usermsg = $this->checkUser($user,$pass,$type,Session::get('organid'));
    	 
    	var_dump($usermsg);
        return 'welecome dog in our room ,die dog! fuck you muther fuck!! ';
    }
    /**
     * [userinsert 注册 测试]
     * @Author wyx
     * @DateTime 2018-04-27T20:35:07+0800
     * @return   [type]                   [description]
     * http://local.menke.com/index/index/userinsert/
     */
    public function userinsert(){

    	$pass = "123456" ;
    	//模拟 用户注册后生成 密码 和 mix
    	$var = $this->createUserMark($pass);
    	var_dump($var);
    }
    /**
     * [cksign description]
     * @Author
     * @DateTime 2018-04-27T20:51:30+0800
     * @return   [type]                   [description]
     * http://local.menke.com/index/index/cksign
     */
    public function cksign1(){
    	//{ ["code"]=> int(0) ["data"]=> array(2) { ["noncestr"]=> string(11) "kfZDwIW6HrL" ["mixauth"]=> string(33) "bvcc24hprtgjobrvkd0bnpjeu2u085288" } ["info"]=> string(12) "登陆成功" }
    	$_POST['noncestr'] = "kfZDwIW6HrL" ;//给客户端的密钥
    	
    	$_POST['mixauth'] = "bvcc24hprtgjobrvkd0bnpjeu2u085288" ;//给客户端的登陆标记

    	$_POST['teacherid'] = 23 ;
    	$_POST['search'] = '18801234564' ;
    	$_POST['noncestr'] = '62a690f8b3156a0e75ff3d490190abd94fbf6c9a' ;//用户 提交的签名
    	//$_POST['noncestr'] = '62a690f8b3156a0e75ff3d490190abd94fbf6c' ;//用户 提交的签名

    	####################################测试############
    	
    	$_POST['noncestr'] = '' ;//给客户端的密钥 服务端不需要
    	
    	$_POST['mixauth']   = $this->request->param('mixauth') ;//给客户端的登陆标记

    	$_POST['teacherid'] = $this->request->param('teacherid') ;
    	$_POST['search']    = $this->request->param('search') ;
    	$_POST['noncestr']  = $this->request->param('noncestr') ;//用户 提交的签名


    	// Session::set('tset','you name little dog');
    	var_dump(Session::get('tset'));

    	$flag = $this->checkDataSign();
    	var_dump($flag);

    	// http://local.menke.com/index/index/cksign/mixauth/bvcc24hprtgjobrvkd0bnpjeu2u085288/teacherid/23/search/18801234564/noncestr/62a690f8b3156a0e75ff3d490190abd94fbf6c9a

    }
    //http://local.menke.com/index/index/cksign
    public function cksign(){
        // $postname = input('post.name');
        $getname = $this->request->param('name');
        $getname  = input('param.name');
        var_dump($_GET);
        // var_dump($postname) ;
        var_dump($getname) ;exit();

    	// http://local.menke.com/index/index/cksign/mixauth/bvcc24hprtgjobrvkd0bnpjeu2u085288/teacherid/23/search/18801234564/noncestr/62a690f8b3156a0e75ff3d490190abd94fbf6c9a
    	return $this->fetch();

    }
    //上传测试\
    //http://local.menke.com/index/index/search
    public function search(){
        $url    =  'http://local.menke.com/index/index/upload' ;
        $url    =  'http://share.51menke/index/index/upload' ;
        // var_dump($url);exit();
        $relative = 'robots.txt';
        $path = realpath($relative);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        $data = array('file' => new \CURLFile($path));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        // curl_setopt($curl, CURLOPT_USERAGENT,"TEST");
        // var_dump($data);exit();
        $result = curl_exec($curl);
        $error = curl_error($curl);
        var_dump($result);
    }
    //上传测试 http://local.menke.com/index/index/upload
    public function upload(){
        echo '<img src="http://cat-1254220117.cosbj.myqcloud.com/advertisement/0/official/20180604202134546.png">';exit();
        echo '<img src="http://cat-1254220117.cosbj.myqcloud.com/123timg1.jpg">';exit();
        $dst = '123timg1.jpg';
        $cos = new \QcloudManage;
        $res = $cos->upload('timg.jpg', $dst);
        
        $bizAttr = '';
        $authority = 'eWPrivateRPublic';
        $customerHeaders = array(
            'x-cos-acl' => 'public-read',
            'Content-Type' => 'image/jpg',
        );
        $cos->updateFile($dst, $bizAttr,$authority, $customerHeaders);
        var_dump($res);

        exit();
        // echo 123123;exit();
        file_put_contents(time().".json", json_encode($_FILES));
        $tmp_name = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        var_dump($_FILES);
        move_uploaded_file($tmp_name,'audit/'.$name); 
    }
    // 加解密
    // local.menke.com/index/Index/testrsa
    public function testrsa(){
        $data = $this->request->param('datastr') ;
        var_dump($data);
        if( !empty($data) ){
            $ret = new Rsa ;// 1加密
            $retstr = $ret->rsaDecryptorign($data) ;// 1加密
            var_dump($retstr) ;
            return ;

        }
        return $this->fetch();
    }
    // local.menke.com/index/Index/getpub
    public function getpub(){
        $path = '../extend/login/cacert/public.pem' ;
        $cont = file_get_contents($path);
        echo $cont;
    }

}


// 2018-05-05 16:38:53  Array
// (
//     [gmt_create] => 2018-05-05 16:38:43
//     [charset] => UTF-8
//     [gmt_payment] => 2018-05-05 16:38:52
//     [notify_time] => 2018-05-05 16:38:53
//     [subject] => 测试
//     [sign] => D/O7nc0zp63tRAKDidKQrmGqrXMSOJJQx1yjEdEUKHDbZsQd1ItxGuIeOl11CRSaRZJ/3LNbLBOULHWzjfzY13jcqQrgXufiwxds/gfpU6+IOeB6etWQDg58XQfiotSR39PzocdAILVEMXIi3IqkH+gUuU7uB22MOuGzZ+Fy0OHDM+VMoVgLPCr7/jNrVURffBm9P8d2w1c6ngPXfgoyvyi1Hi4boRUMlNCdCatIx1+qEWfcyPPk5/DHSklupG7D4l/qoGCn212W+I3WqfzewsbUQz5TDPNU0u724ndMzvVcuMTCOnyWc8IHRxKJ59fOJPjGsGThDqcTHKaUtKFdmA==
//     [buyer_id] => 2088202946628870
//     [invoice_amount] => 0.01
//     [version] => 1.0
//     [notify_id] => aeeb60c9c58f8096035e85be1a34eb2mpt
//     [fund_bill_list] => [{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]
//     [notify_type] => trade_status_sync
//     [out_trade_no] => 201855163820924
//     [total_amount] => 0.01
//     [trade_status] => TRADE_SUCCESS
//     [trade_no] => 2018050521001004870512589854
//     [auth_app_id] => 2018042802603488
//     [receipt_amount] => 0.01
//     [point_amount] => 0.00
//     [app_id] => 2018042802603488
//     [buyer_pay_amount] => 0.01
//     [sign_type] => RSA2
//     [seller_id] => 2088621840658044
// )
