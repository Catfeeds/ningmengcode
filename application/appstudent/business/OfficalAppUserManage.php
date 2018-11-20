<?php
namespace app\appstudent\business;
use app\student\model\Category;
use app\student\model\Studentinfo;
use app\student\model\Studentfunds;
use app\student\model\Studentpaylog;
use app\student\model\Recharge;
use app\student\model\Organ;
use app\student\model\Organcollection;
use app\student\model\Teachercollection;
use app\student\model\Classcollection;
use app\student\model\Teacherinfo;
use think\Cache;
use Wxpay;
use Alipaydeal;
use Messages;
use Think\Log;
use Verifyhelper;
use app\teacher\business\UploadFiles;
session_start();
class OfficalAppUserManage
{
    protected $foo;
    protected $str;
    protected $expire = 900;
    protected $subject = '学生充值';
    protected $wxnotifyurl = 'http://share.51menke/index/Alipay/wxnotify';
    protected $alinotifyurl = 'http://share.51menke/index/Alipay/wxnotify';
    //支付类型 2:微信支付3支付宝4银
    protected $paytype = [2,3,4];
    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
        }
    /**
     * 修改app学生资料
     * @Author yr
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function updateAppuserInfo($data){
        if(!is_intnum($data['id'])){
            return return_format('',39060,'参数错误');
        }
        $where = ['id'=>$data['id']];
        unset($data['id']);
        unset($data['studentid']);
        //设置允许修改的字段
        $allowfiled = array('imageurl','nickname','sex','country','province','city','birth','profile');
        $keyarr = array_keys($data);
        foreach($keyarr as $k=>$v){
            if(!in_array($v,$allowfiled)){
                return return_format('',39601,'参数错误');
            }
            if($v == 'birth'){
                $data['birth'] = strtotime($data['birth']);
            }
        }
        $usermodel = new Studentinfo;
        $update_res = $usermodel->updateAppuserInfo($where,$data);
        if($update_res>=0){
            return return_format('',0,'修改成功');
        }else{
            return return_format('',39061,'修改失败');
        }
    }
    /**
     * 获取感兴趣分类
     * @Author yr
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function getFavorCategory($organid,$studentid){
        if(!is_intnum($organid) || !is_intnum($studentid)){
            return return_format('',39050,'参数异常');
        }
        $studentmodel = new Studentinfo;
        $studentinfo = $studentmodel->getStudentInfo($studentid);
        $ids = $studentinfo['favorcategory'];
        $idarr = explode(',',$ids);
        $categorymodel = new Category;
        $alldata = $categorymodel->getTopList($organid);
        if(empty($alldata)){
            $data['allcategory'] = [];
            return return_format( $data['allcategory'],39061,'数据为空');
        }else{
            foreach($alldata as $k=>$v){
                if(in_array($v['category_id'],$idarr)){
                    $alldata[$k]['is_select'] = true;
                }else{
                    $alldata[$k]['is_select'] = false;
                }

            }
        }
        $data['allcategory'] = $alldata;
        return return_format($data,0,'查询成功');
    }
    /**
     * 获取感兴趣分类
     * @Author yr
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function favorCategoryAdd($ids,$studentid){
        if(!is_intnum($studentid)){
            return return_format('',39050,'参数异常');
        }
        if(empty($ids)){
            return return_format('',39502,'请至少选择一个分类');
        }
        $studentmodel = new Studentinfo;
        $result = $studentmodel->favorAdd($ids,$studentid);
        if($result>0 || $result ==0){
            return return_format('',0,'添加成功');
        }else{
            return return_format('',39051,'修改失败');
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
        $allowfield = ['imageurl','mobile','nickname','sex','country','province','city','birth','profile','id','prphone'];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield) ;
        $studentmodel = new Studentinfo;
        return  $studentmodel->updateStudentInfo($userid,$newdata);
    }
    /**
     * 修改密码发送手机验证码
     * @Author yr
     * @param $mobile
     * @param $code
     * @return array
     *
     */
    public function sendMsg($mobile,$domain)
    {
        if( empty($mobile)){
            return return_format($this->str,39010,'参数类型错误');
        }
        //先判断域名正确性
        $isdomain = CheckUrl($domain);
        if(!$isdomain){
            return return_format('',32004,'请输入正确的域名');
        }else{
            $domainArray = explode('.', $domain);
            $domainArray = explode('//', $domainArray[0]);
            $domain = $domainArray[1];
        }
        $organobj = new Organ;
        $organinfo = $organobj->getOrganmsgByDomain($domain);
        $organid = $organinfo['id'];
        if(empty($organid)){
            return return_format('',39040,'没有此用户信息');
        }
        //先判断手机号长度
        if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
            return return_format($this->str,39012,'请输入6-12位手机号');
        }else{
            $studentmodel = new Studentinfo;
            $data = $studentmodel ->checkLogin($mobile,$organid);
            //如果长度没问题判断手机号是否存在,或者手机号被删除
            if(!$data || $data['delflag'] == 0){
                return return_format($this->str,39013,'手机号不存在');
            }else{
                //判断用户登录状态，是否禁用
                if($data['status'] == 1){
                    return return_format($this->str,39014,'该手机号已被禁用!请联系管理员');
                }
                $mobile_code = rand(100000,999999);
                //此处调用短信接口,发送验证码
                $messageobj = new Messages;
                $prphone = trim($data['prphone'],'\'');
                $prphone = ltrim($prphone,'+');
                $send_result = $messageobj->sendMeg($mobile,$type=4,$params = [$mobile_code,'10'],$prphone);
                if($send_result['result'] == 0){
                    return return_format('',0,"发送成功");
                }else{
                    Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
                    return return_format('',39019,'系统繁忙请稍后再试');
                }
            }

        }

    }
    /**
     * 修改密码
     * @Author yr
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return array
     *
     */
    public function updatePass($mobile,$code,$organid,$newpass){
        if(empty($mobile) || empty($code) || empty($newpass)|| !is_intnum($organid)){
            return return_format($this->str,39020,'参数类型错误');
        }
        if(strlen($newpass)>12 || strlen($newpass)<6){
            return return_format($this->str,39021,'请输入6-12位密码');
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$mobile);
        if(empty( $cachedata)){
            return return_format($this->str,39022,'验证码已失效,请10分钟后重新发送短信验证码');
        }
        if(trim($cachedata) !== trim($code)){
            //如果验证码输入错误超限 重新发送短信验证码
            if(!verifyErrorCodeNum($mobile)){
                return return_format($this->str,39125,'验证码错误次数超限,请重新发送短信验证码');
            }
            return return_format($this->str,39124,'验证码不正确');
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($mobile,$organid);
        $encryptpass = $this->createUserMark($newpass);
        $mix = $encryptpass['mix'];
        $password = $encryptpass['password'];
        if(empty($info)){
            return return_format($this->str,39025,'没有此手机号信息');
        }elseif(trim($info['password']) == trim($password)){
            return return_format($this->str,39026,'新密码与原密码一致');
        }else{
            $result = $studentmodel->updateStudentPass($password,$organid,$mobile,$mix);
            if($result){
                Cache::rm('mobile'.$mobile);
                return return_format($this->str,0,'修改成功,请重新登录');
            }else{
                return return_format($this->str,39028,'修改失败');
            }
        }


    }
    /**
     * 找回密码
     * @Author yr
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return array
     *
     */
    public function updatePassword($mobile,$code,$domain=null,$newpass){
        if(empty($mobile) || empty($code) || empty($newpass)){
            return return_format($this->str,39020,'参数类型错误');
        }
        if(strlen($newpass)>12 || strlen($newpass)<6){
            return return_format($this->str,39021,'请输入6-12位密码');
        }
        $organid  = config('param.affical_organid');
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$mobile);
        if(empty( $cachedata)){
            return return_format($this->str,39022,'验证码已失效,请重新发送');
        }
        if(trim($cachedata) !== trim($code)){
            return return_format($this->str,39024,'验证码不正确');
        }
        if(empty($organid)){
            return return_format('',39041,'该机构下没有此用户信息');
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($mobile,$organid);
        $encryptpass = $this->createUserMark($newpass);
        $mix = $encryptpass['mix'];
        $password = $encryptpass['password'];
        if(empty($info)){
            return return_format($this->str,39025,'没有此手机号信息');
        }elseif(trim($info['password']) == trim($password)){
            return return_format($this->str,39026,'新密码与原密码一致');
        }else{
            $result = $studentmodel->updateStudentPass($password,$organid,$mobile,$mix);
            if($result){
                Cache::rm('mobile'.$mobile);
                return return_format($this->str,0,'修改成功,请重新登录');
            }else{
                return return_format($this->str,39028,'修改失败');
            }
        }


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
            return return_format($this->str,39005,"参数错误");
        }
        $studentmodel = new Studentinfo;
        $result = $studentmodel->getStudentInfo($userid);
        $birthstr = strtotime($result['birth']);
        $result['year'] = date('Y',$birthstr);
        $result['month'] = date('m',$birthstr);
        $result['date'] = date('d',$birthstr);
        unset($result['password']);
        if(empty($result)){
            return return_format([],0,'没有数据');
        }else{
            return return_format($result,0,'查询成功');
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
            return return_format($this->str,39007,"参数错误");
        }
        //判断分页页数
        if(is_intnum($pagenum)>0){
            $start = ($pagenum - 1 ) * $limit ;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
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
            return return_format([],0,'没有数据');
        }else{
            return return_format($alllist,0,'查询成功');
        }
    }
    /**
     * 修改手机号发送手机验证码
     * @Author yr
     * @param $mobile
     * @param $prphone
     * @param $organid
     * @return array
     *
     */
    public function sendUpdatemobileMsg($newmobile,$prphone,$organid)
    {
        if(!is_intnum($organid)){
            return return_format('',39100,'参数错误');
        }
        //先判断手机号长度
        if(strlen($newmobile)<6 || strlen($newmobile)>12 || !is_numeric(rtrim($newmobile))){
            return return_format($this->str,39016,'请输入6-12位手机号');
        }else{
            $mobile_code = rand(100000,999999);
            //此处调用短信接口,发送验证码
            $messageobj = new Messages;
            $send_result = $messageobj->sendMeg($newmobile,$type=4,$params = [$mobile_code,'10'],$prphone);
            if($send_result['result'] == 0){
                return return_format('',0,"发送成功");
            }else{
                Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
                return return_format('',39019,'系统繁忙请稍后再试');
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
        return return_format($res,0,'请求成功');
    }
    /**
     * 修改个人中心密码
     * @Author yr
     * @param    [string]              mobile  必填手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [string]               newpass   新密码
     * @return array
     *
     */
    public function updateUserPassword($mobile,$code,$organid,$newpass,$repass){
        if(empty($mobile) || empty($code) || empty($newpass)|| !is_intnum($organid)||empty($repass)){
            return return_format($this->str,39020,'参数类型错误');
        }
        if(trim($newpass) !== trim($repass)){
            return return_format('',39101,'密码不一致');
        }
        if(strlen($newpass)>12 || strlen($newpass)<6){
            return return_format($this->str,39021,'请输入6-12位密码');
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$mobile);
        if(empty( $cachedata)){
            return return_format($this->str,39022,'验证码已失效');
        }
        if(trim($cachedata) !== trim($code)){
            if(!verifyErrorCodeNum($mobile)){
                return return_format($this->str,39125,'验证码错误次数超限,请重新发送短信验证码');
            }
            return return_format($this->str,39024,'验证码不正确');
        }
        $studentmodel = new Studentinfo;
        $info = $studentmodel->checkLogin($mobile,$organid);
        $encryptpass = $this->createUserMark($newpass);
        $mix = $encryptpass['mix'];
        $password = $encryptpass['password'];
        if(empty($info)){
            return return_format($this->str,39025,'没有此手机号信息');
        }elseif(trim($info['password']) == trim($password)){
            return return_format($this->str,39026,'新密码与原密码一致');
        }else{
            $result = $studentmodel->updateStudentPass($password,$organid,$mobile,$mix);
            if($result){
                Cache::rm('mobile'.$mobile);
                return return_format($this->str,0,'修改成功,请重新登录');
            }else{
                return return_format($this->str,39028,'修改失败');
            }
        }


    }
    /**
     * 修改手机号
     * @Author yr
     * @param    [string]              oldmobile  必填原有手机号
     * @param    [string]              code     验证码
     * @param   [string]               uniqid    tokenid
     * @param   [int]                  organid 机构id
     * @param   [int]                   newmobile  新手机号
     * @param   [int]                   studentid   用户Id
     * @return array
     *
     */
    public function updateMobile($oldmobile,$newmobile,$code ,$organid,$studentid,$prphone){
        if( empty($oldmobile) || empty($code) || empty($newmobile)|| !is_intnum($organid)){
            return return_format($this->str,39020,'参数类型错误');
        }
        if(strlen($newmobile)>12 || strlen($newmobile)<6){
            return return_format($this->str,39021,'请输入6-12手机号');
        }
        //判断验证码是否正确
        $cachedata = Cache::get('mobile'.$newmobile);
        if(empty($cachedata)){
            return return_format($this->str,39022,'验证码已失效');
        }
        if(trim($cachedata) !== trim($code)){
            if(!verifyErrorCodeNum($newmobile)){
                return return_format($this->str,39125,'验证码错误次数超限,请重新发送短信验证码');
            }
            return return_format($this->str,39024,'验证码不正确');
        }
        $studentmodel = new Studentinfo;
        $oldpassword = $studentmodel->checkLogin($oldmobile,$organid);
        if(empty($oldpassword)){
            return return_format($this->str,39025,'没有此手机号信息');
        }elseif($oldmobile == $newmobile){
            return return_format($this->str,39026,'新手机号与原手机一致');
        }else{
            $result = $studentmodel->updateMobile($studentid,$organid,$newmobile,$prphone);
            if($result){
                Cache::rm($newmobile);
                return return_format($this->str,0,'修改成功,请重新登录');
            }else{
                return return_format($this->str,39027,'修改失败');
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
    public function studentRecharge($studentid,$amount,$paytype,$organid,$source){
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39028,'参数异常');
        }
        if(intval($amount)<100){
            return  return_format('',39032,'充值金额不能少于100元');
        }
        if(!in_array($paytype,$this->paytype)){
            return  return_format('',39033,'支付类型错误');
        }
        $studentmodel = new Studentinfo;
        $result = $studentmodel->getStudentInfo($studentid);
        if(empty($result)){
            return return_format('',39029,'没有此学生信息');
        }
        //生成充值订单号
        $rechargenum = getOrderNum();
        //拼装充值订单信息
        $data['studentid'] = $studentid;
        //暂且不做优惠券
        $data['price'] = $amount;
        $data['addtime'] = time();
        $data['paytype'] = $paytype;
        $data['rechargenum'] = $rechargenum;
        $data['organid'] = $organid;
        $data['source'] = $source;
        $rechargemodel = new Recharge;
        $insertres = $rechargemodel->insert($data);
        if(!$insertres){
            return return_format('',39030,'系统繁忙请重新充值');
        }
        switch ($paytype){
            case 2:
                //微信支付
                $wxpayobj = new Wxpay;
                $codeurl = $wxpayobj->createWxPayUrl($rechargenum,$this->subject,$amount,'',$this->wxnotifyurl);
                if($codeurl == false){
                    return return_format('',39034,'支付失败');
                }
                $data = [
                    'codeurl' => $codeurl
                ];
                return  return_format($data,0,'成功');
                break;
            case 3:
                //支付宝支付
                $alipayobj = new Alipaydeal;
                $res =  $alipayobj->createPayRequest($rechargenum,$this->subject,$amount,'',$this->alinotifyurl);
                print_r($res);die();
                break;
            //银联充值
            case 4:
                break;
            default:
                return return_format('',39035,'参数错误');
        }
    }
    /**
     * 机构收藏
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function organCollect($organid,$studentid){
        if(!is_intnum($organid)||!is_intnum($studentid)){
            return return_format('',39135,'参数错误');
        }
        //查看是否收藏过该机构
        $where['organid'] = $organid;
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Organcollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',39136,'该商品已经收藏过');
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',39137,'添加失败');
        }else{
            return return_format('',0,'收藏成功');
        }
    }
    /**
     * 老师收藏
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function teacherCollect($teacherid,$studentid){
        if(!is_intnum($teacherid)||!is_intnum($studentid)){
            return return_format('',39135,'参数错误');
        }
        //查看是否收藏过该机构
        $where['teacherid'] = $teacherid;
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Teachercollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',39136,'您已经收藏过该老师');
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',39137,'收藏失败');
        }else{
            return return_format('',0,'收藏成功');
        }
    }
    /**
     * 班级收藏
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function classCollect($schedulingid,$studentid){
        if(!is_intnum($schedulingid)||!is_intnum($studentid)){
            return return_format('',39135,'参数错误');
        }
        //查看是否收藏过该机构
        $where['schedulingid'] = $schedulingid;
        $where['studentid'] = $studentid;
        $field = 'id';
        $currmodel = new Classcollection;
        $find_result = $currmodel->getDataInfo($where,$field);
        if(!empty($find_result)){
            return return_format('',39136,'您已经收藏过该课程');
        }
        $res = $currmodel->add($where);
        if($res == false){
            return return_format('',39137,'收藏失败');
        }else{
            return return_format('',0,'收藏成功');
        }
    }
    /**
     * 查询我收藏的老师
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function teacherCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',39138,'参数错误');
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
            return return_format([],0,'没有数据');
        }else{
            $data['pageinfo '] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,'请求成功');
        }
    }
    /**
     * 查询我收藏的机构
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function organCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',39138,'参数错误');
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
            return return_format([],0,'没有数据');
        }else{
            $data['pageinfo '] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,'请求成功');
        }
    }
    /**
     * 查询我收藏的班级课程
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function classCollectList($studentid,$pagenum,$limit){
        if(!is_intnum($studentid)){
            return return_format('',39138,'参数错误');
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
        $teachermodel = new Teacherinfo;
        foreach($result as $k=>$v){
            $result[$k]['teachername'] = $teachermodel->getTeacherName($v['teacherid']);
        }
        $total = $currmodel->getCollectCount($studentid);
        if(empty($result)){
            return return_format([],0,'没有数据');
        }else{
            $data['pageinfo '] = [
                'pagenum'=> $pagenum,
                'limit' => $limit,
                'total' => $total
            ];
            $data['data'] = $result;
            return return_format($data,0,'请求成功');
        }
    }
    /**
     * 取消收藏的机构
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function cancelOrganCollect($organid,$studentid){
        if(!is_intnum($studentid) || !is_intnum($organid)){
            return return_format('',39138,'参数错误');
        }
        $collectmodel = new Organcollection;
        $where = [
            'studentid' => $studentid,
            'organid' => $organid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,'取消成功');
        }else{
            return return_format('',39139,'取消失败');
        }
    }
    /**
     * 取消收藏的老师
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function cancelTeacherCollect($teacherid,$studentid){
        if(!is_intnum($studentid) || !is_intnum($teacherid)){
            return return_format('',39138,'参数错误');
        }
        $collectmodel = new Teachercollection;
        $where = [
            'studentid' => $studentid,
            'teacherid' => $teacherid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,'取消成功');
        }else{
            return return_format('',39139,'取消失败');
        }
    }
    /**
     * 取消收藏的班级
     * @Author yr
     * @param  studentid  学生id
     * @param  organid 机构id
     * @return array
     *
     */
    public function cancelClassCollect($schedulingid,$studentid){
        if(!is_intnum($studentid) || !is_intnum($schedulingid)){
            return return_format('',39138,'参数错误');
        }
        $collectmodel = new Classcollection;
        $where = [
            'studentid' => $studentid,
            'schedulingid' => $schedulingid,
            'delflag' => 1,
        ];
        $result = $collectmodel->deleteData($where);
        if($result){
            return return_format('',0,'取消成功');
        }else{
            return return_format('',39139,'取消失败');
        }
    }
}
