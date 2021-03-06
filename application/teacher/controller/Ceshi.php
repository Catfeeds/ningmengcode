<?php

namespace app\teacher\controller;
use app\admin\business\OrganManage;
use app\teacher\business\Classesbegin;
use app\teacher\business\MessageManager;
use app\teacher\business\StudentManage;
use app\teacher\model\TeacherInfo;
use think\Model;
use think\Controller;
use think\Request;
use think\Db;
use think\Cache;
use think\Validate;
use app\teacher\model\OrderManage;
use app\teacher\model\ToteachTime;
use app\teacher\business\CurriculumModule;
use app\teacher\business\HomeworkModule;
use think\Config;

class Ceshi extends Controller
{
    // protected $pushapiurl;
    // $this->pushapiurl = 23;

    protected $table = "mk_studentinfo";
    protected $rule = [
        'mobile'   => 'require|max:25',
        'nickname' => 'require|max:30',
        'sex'      => 'number|between:0,2',
    'prphone'  => 'require',
        'country'  => 'number',
        'province' => 'number',
        'city'     => 'number',
    ];
  protected $message = [
        'mobile.require'   => '手机号必须填写',
        'mobile.max'       => '名称最多不能超过25个字符',
        'nickname.require' => '昵称必须填写',
        'mobile.max'       => '昵称不能超过30个字符',
        'sex.number'       => '性别必须是数字',
        'sex.between'      => '性别只能在0-2之间',
        'country.number'   => '必须是数字',
        'province.number'  => '必须是数字',
        'city.number'      => '必须是数字',
    ];
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // # 创建订单管理，获取该课程学生列表（包括人数）
        // $period = new OrderManage;
        // $list = $period->getOrderStu(1,1,1);
        // return  json_encode($list);
        // print_r($list);

        // #获取了c.type,c.timekey,t.courseware,t.periodname,c.scheduleid
        // $periodb = new ToteachTime;
        // $listb = $periodb->getteachtime(1,1,1,1);


    }
       public function getStudentOrder($studentid=1,$organid=1){

        $field = 't.imageurl,c.coursename,c.classname,c.orderstatus,t.classtypes,s.teachername,c.paytype,c.originprice,c.discount,c.amount,c.ordernum,c.ordertime' ;
        $ll= Db::table('mk_ordermanage')->alias('c')
        ->join('mk_teacherinfo s','c.teacherid = s.teacherid ')
        ->join('mk_curriculum t','t.id=c.curriculumid')
        ->where('c.studentid','eq',$studentid)
        ->where('c.orderstatus','EGT',0)
        ->where('c.organid','eq',$organid)
        ->field($field)
        ->select() ;
        print_r(Db::table($this->table)->getlastsql());
    }

    public function teachCourseList(){
        //$starttime=1;
        $endtime=2;$teacherid=1;$organid=2;
        //$field = 'intime,count(id) num' ;
        $list = Db::name('mk_toteachtime')
        ->where('teacherid','EQ',1)
        ->where('organid','EQ',1)
        ->where('delflag','EQ',1)
        ->group('intime')
        ->column('id') ;
    print_r(Db::table('mk_toteachtime')->getlastsql());

    }
   public function getstudentlist($teacherid=1,$organid=1,$limitstr=5){

        $field = 'c.ordertime,c.coursename,s.nickname' ;
        $cc= Db::table('mk_ordermanage')->alias('c')
        ->join('mk_studentinfo s','c.studentid = s.id ')
        ->where('c.teacherid','eq',$teacherid)
        ->where('c.orderstatus','EGT',0)
        ->where('c.organid','eq',$organid)
        ->field($field)
        ->limit($limitstr)
        ->select();
        print_r(Db::table($this->table)->getlastsql());
    }
    public function getB(){
      $arr = [111,111];
      $list = Db::table('mk_studentinfo')
            ->where('id','IN',$arr)
            ->column('id,username');
          $this->ajaxreturn($list);
    }
     public function  getA(){
      $orderid =504;
      $organid = 14;
      $field = 'ordernum,curriculumid,orderstatus,ordertime,studentid,ordersource,paytype,originprice,discount,amount,type,coursename,classname,teacherid' ;
      $list= Db::table('mk_ordermanage')
            ->field($field)
        ->where('id','eq',$orderid)
        ->where('organid','eq',$organid)
        ->find() ;
        $this->ajaxreturn($list);
    }
    public function getS(){
      $id = 110;
      $organid = 14;
      $list = Db::table($this->table)
          ->where('id','eq',$id)
          ->where('organid','eq',$organid)
          ->field('id,nickname')
          ->find();
          $this->ajaxreturn($list);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read()
    {
        //
        $mobile = '16601151019';
        $cachedata = Cache::get('mobile'.$mobile);
        $this->ajaxreturn($cachedata);
        //var_dump($cachedata);
    }



    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        //
        $k = time();
        print_r($k);
    }
    //protected $table = 'mk_organ';
    //$domain = 'test';
    //
    public function getOrganid(){
        $domain = 'test';
        $mm = Db::table($this->table)->where('id','eq',1)->field('domain')->select();
        print_r(Db::table($this->table)->getlastsql());
    }
    public function getFileList(){
        $data['teacherid'] =2;
        $data['fatherid'] =0;
        //$data['showname'] ='文件夹1';
        $limit = 5;
        $pagenum = 1;
        //$pagenum = $pagenum?$pagenum:$this->pagenum;
        //$cc = Db::raw('NULL OR 2');
        $where  = [
                    'fatherid' => isset($data['fatherid'])?$data['fatherid']:0 ,
                    'organid'  => 1 ,
                    'delflag'  => 1,
                    'teacherid' => [['eq',2],NULL,'or']
                ] ;
        if(isset($data['showname'])){
            $where['showname'] = array('like',$data['showname']);
        }
        $field = 'fileid,fileurl,showname,fatherid,addtime,sizes' ;
        $info['data'] = Db::table('mk_filemanage')->field($field)->where($where)->page($limit,$pagenum)->select();
        print_r($info);

        exit();
        $info['pageinfo'] = array('pagesize'=>$pagenum,'pagenum'=>$limit,'total'=>$this->getFileCount($where));
        return $info;
    }


    public function addStudent($data =['mobile'=>'16666'],$teacherid=1,$organid=2){
        $validate = new Validate($this->rule, $this->message);
        $result   = $validate->check($data);
         //数据验证
            $data['nickname'] = '贝吉塔';
            $id= 1;
            Db::table('mk_studentfunds')->insert(['studentid'=>$id,'organid'=>$organid]);
            $return = $this->allowField(true)->save($data);
          print_r(Db::table('mk_studentfunds')->getlastsql());
          print_r(Db::table('mk_studentinfo')->getlastsql());
        }


   public function eve(){
      $t = new Ceshi;
      $m = Cache::get('mobile'.'18840869755');
      print_r($m);
   }

    public function rcMobile(){
      //  $toteachB = [['coursename'=>'ssss'],['coursename'=>'assms']];
        //$stubils  = [['mobile'=>1111111,'prphone'=>32435],['mobile'=>3333333,'prphone'=>343245]];
        //$nickarr  = ['sss','lllll'];
        //foreach($toteachB as $k => $v){
          //  foreach($stubils as $ky =>$vl){
            //    $arrB[$ky] = ['prphone'=> $vl['prphone'],
              //      'mobile'=>$vl['mobile'],
                //    'params'=> [$nickarr[$ky],$v['coursename']]
                //];
            //}
        //}
        //$this->ajaxReturn($arrB);
        //$data = Request::instance()->post(false);
        //$mm = new CurriculumModule();
        $cc = \app\admin\business\TimingTask::rcMobile();
        //$cc = $mm->rcMobile();
        $this->ajaxReturn($cc);
    }
    public function RemindMessage(){
      // $cccc = new \app\admin\lib\Crond;
      //   $mmc = $cccc->doCron();
        $data = Request::instance()->post(false);
        //$mm = new CurriculumModule();
        $cc = \app\admin\business\TimingTask::RemindMessage();
        //$cc = $mm->RemindMessage();
        $this->ajaxReturn($cc);
        //$this->ajaxReturn($mmc);
    }
    public function getStudentMobile(){
         // $idarr = [89,42];
         $mbk =  Db::table('mk_studentinfo')
             ->where('id','IN',[15,89,85])
             ->column('prphone,mobile','id');
             print_r(Db::table('mk_studentinfo')->getlastsql());
    }

    /*
     * 分享二维码注册页面
     *  @Author wangwy
     *  @param
     *  url: admin/Teacher/
     */
    public function shareQrcode(){
        //Header("Content-type:image/png");
        $data = Request::instance()->post();
        $organid = 1;
        $url = 'http://fish.51menke.net';
        $Qrcode = new \app\teacher\business\TeacherManage;
        $QR = $Qrcode->shareQrcode($url,$organid);

        //imagepng($QR['data']);
        //imagepng($QR['data']);
        //print_r($QR['data']);\

        return $QR['data'] ;

    }
    public function bibao (){
        $teacherid = 1;
        $organid = 14;
       //  $mm = function($query) use($teacherid,$organid){
       //          $query->field('teacherid')
       //          ->table('mk_teacherinfo')
       //              ->where('teacherid','eq',$teacherid)
       //              ->where('organid','eq',$organid);
       //      };
       // Db::table($this->table)
       //      ->insert($mm);
            
       //      function($query) use($teacherid,$organid){
       //          $query->field('teacherid,imageurl,resttime,prphone,mobile,birth,addtime,sex,country,province,city,profile,teachername,nickname,classesnum,accountstatus,recommend,sortnum,delflag,organid,identphoto,slogan,initials')
       //          ->table('mk_teacherinfo')
       //              ->where('teacherid','eq',$teacherid)
       //              ->where('organid','eq',$organid);
       //      };
        // $categoryid = 1;

        //      $lists = Db::table('mk_scheduling'.' s')
        //     ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.nickname as teachername,c.imageurl,c.coursename,c.subhead,s.gradename')
        //     ->join('mk_curriculum c','s.curriculumid = c.id','LEFT')
        //     ->join('mk_teacherinfo t','s.teacherid = t.teacherid','LEFT')
        //     ->where('s.status','eq',1)
        //     ->where('s.delflag','eq',1)
        //  /*   ->where('s.classstatus','in','0,1,2,3,4')*/
        //     ->where('s.curriculumid','IN',function($query) use($categoryid,$organid){
        //         $query->field('id')->table('mk_curriculum')->where('organid','eq',$organid)->where('categoryid','in',$categoryid);
        //     })
        //     ->order('s.sortnum')
        //     ->select();
        // $sql = db::table($this->table)->getLastSql();
        // return  $sql;
            //print_r(Db::table($this->table)->getlastsql());
         // $list = Db::table('mk_teacherinfo')
         //    ->where('teacherid','in',function($query) use($teacherid,$organid){
         //        $query->field('teacherid,imageurl,resttime,prphone,mobile,birth,addtime,sex,country,province,city,profile,teachername,nickname,classesnum,accountstatus,recommend,sortnum,delflag,organid,identphoto,slogan,initials')
         //         ->table('mk_teacherinfo')
         //         ->where('teacherid','eq',$teacherid)
         //         ->where('organid','eq',$organid);
         //    })
         //    ->field('teacherid')
         //      ->select();
          // $mc =Db::query(' SELECT `teacherid`,`imageurl`,`resttime`,`prphone`,`mobile`,`birth`,`addtime`,`sex`,`country`,`province`,`city`,`profile`,`teachername`,`nickname`,`classesnum`,`accountstatus`,`recommend`,`sortnum`,`delflag`,`organid`,`identphoto`,`slogan`,`initials` FROM `mk_teacherinfo` WHERE `teacherid` = 1 AND `organid` = 14 LIMIT 1');
          // $sql = db::table('mk_teacherinfo')->getLastSql();
          // dump($sql);
        //   // dump($mc);
        // $mm = function($query) use($teacherid,$organid){
        //              $query->field('imageurl,resttime,prphone,mobile,birth,addtime,sex,country,province,city,profile,teachername,nickname,classesnum,accountstatus,recommend,sortnum,delflag,organid,identphoto,slogan,initials')
        //             ->where('teacherid','eq',$teacherid)
        //             ->where('organid','eq',$organid);
        //         //return $m;
        //     };
// print_r($mm['static']);
//         exit();
         // $mm = Db::query(' SELECT `imageurl`,`resttime`,`prphone`,`mobile`,`birth`,`addtime`,`sex`,`country`,`province`,`city`,`profile`,`teachername`,`nickname`,`classesnum`,`accountstatus`,`recommend`,`sortnum`,`delflag`,`organid`,`identphoto`,`slogan`,`initials` FROM `mk_teacherinfo` WHERE `teacherid` = 1 AND `organid` = 14 LIMIT 1');
        $mm = Db::table('mk_teacherinfo')
             ->where(['teacherid'=>$teacherid,'organid'=>$organid])
             ->field('imageurl,resttime,prphone,mobile,birth,addtime,sex,country,province,city,profile,teachername,nickname,classesnum,accountstatus,recommend,sortnum,delflag,organid,identphoto,slogan,initials')
             ->find();
        // $mm = json_decode(json_encode($mm),true);
        // print_r($mm); 
         //$mm = $mm[0];
        
        Db::table('mk_teacherinfo')
            ->insert($mm);
        // $ll = Db::table('mk_teacherinfo')
        // ->field('teacherid')
        // ->where('teacherid','eq',1)
        // ->find();
        
        //$ls = Db::query('SELECT `teacherid` FROM `mk_teacherinfo` WHERE `teacherid` = 1 LIMIT 1');
        $sql = db::table('mk_teacherinfo')->getLastSql();
        print_r($sql);
        //return $sql;
        // Db::execute('insert into mk_teacherinfo (imageurl,resttime,prphone,mobile,birth,addtime,sex,country,province,city,profile,teachername,nickname,classesnum,accountstatus,recommend,sortnum,delflag,organid,identphoto,slogan,initials) values  (5, "thinkphp", 1)');
    }

    public function ddd (){
        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = '';
        // 推送的url地址，上线时改成自己的服务器地址
        $push_api_url = "http://192.168.1.32:2121/";
        $post_data = array(
           'type' => 'publish',
           'content' => '这个是推送的测试数据',
           'to' => $to_uid, 
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        var_export($return);
    }
    public function dge(){
        $content = '测试结果是什么';
        $mm = $this->sendMsgToAllByPhpSocketIO($content);
        return $mm;
    }
    protected function sendMsgToAllByPhpSocketIO($content=''){
    try{ $to_uid = '';

        // 获取推送的url
        //$url = Config::get('apiserver.socketUrl');
        $url = "http://192.168.1.32:2121/";
        // 拼接消息体
        $contentJson=[
            'type'=>'publish',
            'content'=>$content,
            'to' => $to_uid, 
        ];
        // 向所有在线的用户进行推送
        $mm = curl_post($url,[],$contentJson);
        return $mm;
    }catch (\Exception $exception){
        dump($exception->getMessage());
        return false;
    }

}







/**
 * 推送事件
 * 典型调用方式：
 * $push = new PushEvent();
 * $push->setUser($user_id)->setContent($string)->push();
 *
 * Class PushEvent
 * @package app\lib\event
 */



/**
     * @var string 目标用户id
     */
    protected $to_user = '';
 
    /**
     * @var string 推送服务地址
     */
    protected $push_api_url = 'http://192.168.1.32:2121/';
 
    /**
     * @var string 推送内容
     */
    protected $content = '少年想玩吗';
 
    /**
     * 设置推送用户，若参数留空则推送到所有在线用户
     *
     * @param string $user
     * @return $this
     */
    public function setUser($user = '')
    {
        $this->to_user = $user ? : '';
        return $this;
    }
 
    /**
     * 设置推送内容
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }
    //protected $push_api_url ;
 
    /**
     * 推送
     */
    public function push()
    {
        Header("Access-Control-Allow-Origin:*");
        $data = [
            'type' => 'publish',
            'content' => $this->content,
            'to' => $this->to_user,
        ];
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $this->push_api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $res = curl_exec($ch);
        curl_close($ch);
        // echo 123123123;
        // if($res == "ok"){
        //  //执行推送成功的业务逻辑,比如将消息插入到数据表
        // }
        echo($res);
 
    }
    public function registerTeacherss(){
        $mobile= '11111111';
        $organid = 51;
        $mm = new \app\teacher\model\TeacherInfo;
        $mm->registerTeachers($mobile,$organid);
    }
    public function inviteobj(){
        $mc = new \app\teacher\business\TeacherManage();
        $mm = $mc->inviteTeacher('86','18310614871','64','http://fish.51menke.net',6);
        //return $mm;
        $this->ajaxReturn($mm);
    }
    public function delMsglist(){
        $mc = new \app\teacher\business\MessageManager();
        $mm = $mc->delMsglist([1,2,3]);
        $this->ajaxReturn($mm);
    }
    public function Msglist(){
        $mc = new \app\teacher\business\MessageManager();
        $mm = $mc->getMessageList(1,259);
        $this->ajaxReturn($mm);
    }
    public function stulis(){
        $mc = new \app\teacher\business\MessageManager();
        //$mc = new \app\appteacher\business\StudentManage();
        $mm = $mc->invitationList(213);
        $this->ajaxReturn($mm);
    }
    public function bingorgan(){
        $mc = new \app\teacher\business\TeacherManage();
        //$mc = new \app\appteacher\business\StudentManage();
        $mm = $mc->bindOrgan(213,64);
        $this->ajaxReturn($mm);
    }
    public function anylist(){
        $mc = new \app\teacher\business\MessageManager();
        //$mc = new \app\appteacher\business\StudentManage();
        $mm = $mc->getMessageList(1,215,65);
        $this->ajaxReturn($mm);

//        $content =$teachname.',您好!'.$organname.'向您发送了入驻邀请，赶紧登录51门课网处理吧!';
//        $msg = $inviteobj->addInvite($teacherid,$organid,$content);
    }

    public function cccq(){
        $mm = new TeacherInfo();
        $mc = $mm->registerTeacher(86,'11117777',1);
        $this->ajaxReturn($mc);
    }
    public function jiebang(){
        $mm = new \app\teacher\business\TeacherManage();
        $mc = $mm->liftedOrgan(213,'64');
        $this->ajaxReturn($mc);
    }
   public function getUserinfo(){
       $mm = new \app\teacher\business\StudentManage();
       $mc = $mm->getUserDetail(109,'64');
       $this->ajaxReturn($mc);
   }
    public function jiebangff(){
        $mm = new \app\admin\business\OrganManage();
        $mc = $mm->liftedOrgan(213,'64');
        $this->ajaxReturn($mc);
    }
    public function updatepa(){
        $mm = new \app\teacher\business\TeacherManage();
        $mc = $mm->updatePassword(18310614870,'86',333333,64,123456
        ,123456
        );
        $this->ajaxReturn($mc);
    }
    public function msgls(){
        $mm = new \app\appteacher\business\MessageManager();
        $mc = $mm->getMessageList(1,'229',71,3
        ,123456
        );
        $this->ajaxReturn($mc);
    }
    public function scheHomeworkList(){
        $mm = new HomeworkModule();
        $mc = $mm->scheHomeworkList(88,1,71,'','');
        $this->ajaxReturn($mc);
    }
    public function delfile(){
        $mm = new Classesbegin();
        $data['fileid'] =1;
        $data['filetype'] =0;
        $data['teacherid'] =1;
        $data['delflag'] =1;
        $mc = $mm->deleteFile($data);
        $this->ajaxReturn($mc);
    }
    public function getperiodinf(){
        $s = null;
        var_dump(isset($s));
        $mm = new CurriculumModule();
        $data['fileid'] =1;
        $data['filetype'] =0;
        $data['teacherid'] =1;
        $data['delflag'] =1;
        $mc = $mm->getAttendance(1493,88,1,10);
       // $statusnum = 0;
//        foreach($mc['data'] as $k => $v){
//            $statusnum += $v['status'] == 1?1:0;
//        }
        $this->ajaxReturn($mc );
    }
    public function upperiodinf(){
        $mm = new CurriculumModule();
        $data[0]['lessonsid'] = 1493;
        $data[0]['studentid'] = 239;
        $data[0]['attendancestatus'] = '0';
        //$data[0]['score'] = 3;
        //$data[0]['comment'] = 'jjd';
        //$data[1] = ['lessonsid'=>117,'studentid'=>22,'attendancestatus'=>1,'score'=>5,'comment'=>'sssss'];
        $mc = $mm->upAttendance($data,88);

        $this->ajaxReturn($mc);
    }
    public function getSchedulCurriinf(){
        $mm = new Classesbegin();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->getSchedulCurriinf($data,88);

        $this->ajaxReturn($mc);
    }
    public function uploadhit(){
        header('Access-Control-Allow-Origin: *');
        $mm = new \UcloudManage();
        $mc = $mm->uploadpost('1.jpg','hello.jpg');
        $this->ajaxReturn($mc);
    }
    public function showLesssonsHomeList(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->showLesssonsHomeList(88,1,20,184,150,'土');

        $this->ajaxReturn($mc);
    }
    public function stuHomeworkList(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->stuHomeworkList(88,1,20,0,150,184,1097,'A');

        $this->ajaxReturn($mc);
    }
    public function showExerciselist(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->showExerciselist(210,147,1302,146);

        $this->ajaxReturn($mc);
    }
    public function getChoicelist(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->getChoicelist(202,'185',1292,88);

        $this->ajaxReturn($mc);
    }
    public function getAllstuid(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->getAllstuid(88,184,1098);

        $this->ajaxReturn($mc);
    }
    public function showMarking(){
        $mm = new HomeworkModule();
        $a = [['workid'=>45,'score'=>3224,'comment'=>'sdfvvvdhhhh']];
//        $b = [['workid'=>68,'score'=>44,'comment'=>'哈哈哈哈'],['workid'=>69,'score'=>44,'comment'=>'哈哈哈哈']];
//        $c = [['workid'=>70,'score'=>44,'comment'=>'哈哈哈哈']];
        $b = [];
        $c = [];
        $d = [];
        $mc = $mm->showMarking(88,1309,146,$a,$b,$c,$d);

        $this->ajaxReturn($mc);
    }
    public function upstatus(){
        $mm = new HomeworkModule();
        $a = [['workid'=>45,'score'=>3224,'comment'=>'sdfvvvdhhhh']];
//        $b = [['workid'=>68,'score'=>44,'comment'=>'哈哈哈哈'],['workid'=>69,'score'=>44,'comment'=>'哈哈哈哈']];
//        $c = [['workid'=>70,'score'=>44,'comment'=>'哈哈哈哈']];
        $b = [];
        $c = [];
        $d = [];
        $mc = $mm->upstatus(88,214,1309);

        $this->ajaxReturn($mc);
    }
    public function ceshiupdate(){
        $where = ['id'=>15];
        Db::table('nm_studenthomeworkanswer')
            ->where($where)
            ->update(['score'=>33,'comment'=>'哈哈哈']);
        return Db::table($this->table)->getLastSql();
    }
    public function totalHomework(){
       return round(1/1,2)*100;
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->totalHomework(88,334,197,1550);
        $this->ajaxReturn($mc);
    }
    public function arrangeHomework(){
        $mm = new HomeworkModule();
        $data['id'] = 36;
        $data['teacherid'] = 88;
        $mc = $mm->arrangeHomework(184,1097,123124,1,88);
        $this->ajaxReturn($mc);
    }
    public function getUserDetail(){
        $mm = new StudentManage();
        $mc = $mm->getUserDetail(1,1,20);
        $this->ajaxReturn($mc);
    }
    public function getUserList(){
        $mm = new StudentManage();
        $mc = $mm->getUserList('','',1,21,20);
        $this->ajaxReturn($mc);
    }
    public function noticeAll(){
        $mm = new MessageManager();
        $mc = $mm->noticeAll([1,143,144,146],'小明','红红火火恍恍惚惚');
        $this->ajaxReturn($mc);
    }
    public function getSchedulinglists(){
        $mm = new Classesbegin();
        $mc = $mm->getSchedulinglists(['pagenum'=>1,'teacherid'=>88],20);
        $this->ajaxReturn($mc);
    }
    public function periodStulist(){
        $mm = new CurriculumModule();
        return Config::get('cache.host');
        return Cache::get('host');
        return Cache::get('config.host');
        $mc = $mm->periodStulist(1,20,259,null,255);
        $this->ajaxReturn($mc);
    }

    public function showfileCs(){
        $mm = new Classesbegin();
        $mc = $mm->getFileList(['fatherid'=>968,'filetype'=>1,'pagenum'=>1,'showname'=>'','usetype'=>0,'teacherid'=>259]);
        $this->ajaxReturn($mc);
    }
    public function getwareee(){
        $mm = new CurriculumModule();
        $mc = $mm->getPeriodList(['id'=>1578,'toteachtimeid'=>1403]);
        $this->ajaxReturn($mc);
    }
    public function getwarorgan(){
        $mm = new OrganManage();
        $mc = $mm->getPeriodList(['id'=>1578,'toteachtimeid'=>1403,'date'=>'2018-10-31','starttime'=>'2018-10-31 02:00' ,'endtime'=>'2018-10-31 02:40']);
        $this->ajaxReturn($mc);
    }
    public function upimg(){

        $ma = 'data:image/png;base64,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%E2%80%A6%E2%80%A6';
        $mm = 'iVBORw0KGgoAAAANSUhEUgAAA/cAAAP3AQMAAABTSCyMAAAABlBMVEX///8AAABVwtN+AAADc0lEQVR4nO3dQXLaMBSAYTJdZMkRfBQfDY7GUThCl1l06lZupNGLJAKZSWewv38FRn4f3mpsOBwkSZIkSZIkSZIkSZKkDfRjudk1rzv9ffMzv5k/LvuVPzneHnfm8/l8Pp/P5/P5fD6fz9+n3x5OZT8seenODf7v7rQBxOfz+Xw+n8/n8/l8Pp+/Gz/s3Lw1flNZ0vfnem1zFXw+n8/n8/l8Pp/P5/P5/MpfO9UfX/LRZjifz+fz+Xw+n8/n8/l8Pv9xv3/mazqaXkzL/9h/4vP5fD6fz+fz+Xw+n89/er8tnJlKvz+zPv90qZeU8lV8y/NXfD6fz+fz+Xw+n8/n8/lP7Y+71meGzZ3+FtFxOKl8RT6fz+fz+Xw+n8/n8/n8ffl3FPZ/DsFPTUt1/813xOfz+Xw+n8/n8/l8Pp//1H4Y3r+55pOHm0rnw+gpqjCFz+fz+Xw+n8/n8/l8Pn9ffqkd3l2y5K/4Wi8Jd/E89PwTn8/n8/l8Pp/P5/P5fP7W/XBmKTzctHaqh895STNlfIvOwufz+Xw+n8/n8/l8Pp+/Tz9U7r9pN3eaM/tLjsOveE98Pp/P5/P5fD6fz+fz+Rv0w85N+/zT9XN/Wj70e7ykmcLn8/l8Pp/P5/P5fD6fv3l/7bRUd86ExsP7U0qX7N++Cj6fz+fz+Xw+n8/n8/n8zfvhcH//px2eam+uyVfxyC4Sn8/n8/l8Pp/P5/P5fP7m/XDm2pyF9GZa3v85KSxJhc2dUv9XhMuUQXw+n8/n8/l8Pp/P5/P5G/bL80/9rofbOzf3T/n6mXw+n8/n8/l8Pp/P5/P5T+3f0Se36JTCV5y/cBV8Pp/P5/P5fD6fz+fz+Vv0H9n/KTfXpNb7b9KLqfb7/78UlvD5fD6fz+fz+Xw+n8/n79BvD6eyv3Zaqh+XWerh/Snz8Cr4fD6fz+fz+Xw+n8/n83fqH+sT3oZ+6bUZ3hT2f279/gyfz+fz+Xw+n8/n8/l8/r79MLz9/+spf3R+P/DSnTKIz+fz+Xw+n8/n8/l8Pp//r1Ptz0uvcz03LLnWEJ/P5/P5fD6fz+fz+Xz+Tv228ZlleHoz1VJ7i053NJ/P5/P5fD6fz+fz+Xz+zvxx149L4v7PeMqlRspXbC+Uz+fz+Xw+n8/n8/l8Pn/rviRJkiRJkiRJkiRJkvSE/QEf0bU7igTe0gAAAABJRU5ErkJggg==';
        $kk = new \Upload;
        $mc = $kk->SaveFormUpload('',$ma);
        $this->ajaxReturn($mc);
    }
    function SaveFormUpload($savepath, $img, $types=array()){
        $basedir = '/Uploadimg/headimg/'.$savepath;
        $fullpath = dirname(THINK_PATH).$basedir;
        if(!is_dir($fullpath)){
            mkdir($fullpath,0777,true);
        }
        $types = empty($types)? array('jpg', 'gif', 'png', 'jpeg'):$types;
        $img = str_replace(array('_','-'), array('/','+'), $img);
        $b64img = substr($img, 0,100);
        dump($b64img);
        dump(strlen($b64img));
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)){
            $type = $matches[2];
            if(!in_array($type, $types)){
                return array('error'=>1,'msg'=>'图片格式不正确','url'=>'');
            }
            $imglen = strlen($b64img);
            $filesize = ($imglen-($imglen/8)*2);
            echo '原图大小'.number_format(($filesize/1024),2).'KB';

            $img = str_replace($matches[1], '', $b64img);
            $img = base64_decode($img);
            $photo = '/'.md5(date('YmdHis').rand(1000, 9999)).'.'.$type;
            file_put_contents($fullpath.$photo, $img);
            return array('error'=>0,'msg'=>'保存图片成功','url'=>$basedir.$photo);
        }
        return array('error'=>2,'msg'=>'请选择要上传的图片','url'=>'');
    }
    public function doCron(){
       // return config('param.server_url');
        $ds = new \app\admin\lib\Crond();
        $rs = $ds->doCron();
        $this->ajaxReturn($rs);
    }
    public function makeroom(){
        $m =json_encode(['s'=>66]);
        // $m = json_decode(json_encode("{'ss':66}"),true);
        var_dump($m);
        die();
        $this->ajaxReturn($m);
        $obj = new \app\admin\business\TimingTask();
        $sc = $obj->makeRoom();
        $this->ajaxReturn($sc);
    }
    public function addCourseware(){
        $obj = new CurriculumModule();
        $re = $obj->addCourseware(['id'=>'1','fileid'=>[1,2]],[1,2]);
        $this->ajaxReturn($re);
    }









}
