<?php
namespace app\admin\controller;
use think\Request;
use think\Session;
use login\Authorize;
use think\Controller;
use app\admin\business\OrganManage;
use app\admin\model\Adminmember;
use app\student\business\MyOrderManage;
use app\official\business\ConfigManage;
class Organ extends Authorize
{
	protected $restype;

    public function __construct(){
        parent::__construct();
        $this->restype = 2;
    }

	/**
	 *  如果有默认访问的话 返回空字符串
	 *  URL:/admin/organ/index
	 */
	public function index(){
		return '' ;
	}

	/**
	 *  机构购买套餐方法 订单状态查询接口
	 *  @param    $mealnumber  套餐id
	 *  @param    $支付类型    微信2  支付宝3 
	 *  URL:/admin/organ/orderQuery
	 */
	public function orderQuery(){
		$ordernum = $this->request->post('ordernum');
        $orderobj = new MyOrderManage;
        $res = $orderobj->queryOrder($ordernum);
        $this->ajaxReturn($res);

	}

	/**
	 *  更新登陆用户的信息
	 *  URL:/admin/organ/updateUserMsg
	 */
	public function updateUserMsg(){
		$uid     = $this->userInfo['info']['uid'];  
		//获取修改类型 1 编辑资料 2 修改密码  3 更换手机号
		$userimg  = $this->request->param('userimg') ;
		$username = $this->request->param('username') ;
		$organobj = new OrganManage;
		$msg = $organobj->updateUserMsg($userimg,$username,$uid);
		$this->ajaxReturn($msg);
	}
	/**
	 *  更改用户的密码或更换手机号 发短信验证
	 *  当前用户
	 *  URL:/admin/organ/sendMessage
	 */
	public function sendMessage(){
		//获取用户的 手机号前缀 和手机号
		$prephone= $this->request->param('prephone') ;
		$mobile  = $this->request->param('mobile') ;
		$token   = $this->userInfo['token'];// 用户登录标记
		$organobj = new OrganManage;
		$msg = $organobj->sendMessage($prephone,$mobile,$token);
		$this->ajaxReturn($msg);
	}

	/**
	 *  更改用户的密码
	 *  URL:/admin/organ/updatePass
	 */
	public function updatePass(){
		//获取修改类型 1 编辑资料 2 修改密码  3 更换手机号
		$mark  = $this->request->param('mark') ;
		$pass  = $this->request->param('pass') ;
		$repass= $this->request->param('repass') ;
		$uid   = $this->userInfo['info']['uid'] ;
		$token = $this->userInfo['token'];// 用户登录标记

		$organobj = new OrganManage;
		$msg = $organobj->updatePass($mark,$pass,$repass,$uid,$token);
		$this->ajaxReturn($msg);
	}

	/**
	 *  用户更换手机号码
	 *  URL:/admin/organ/changeMobile
	 */
	public function changeMobile(){
		//获取修改类型 1 编辑资料 2 修改密码  3 更换手机号
		$mark    = $this->request->param('mark') ;
		$oldphone= $this->request->param('oldphone') ;
		$newphone= $this->request->param('newphone') ;
		$uid     = $this->userInfo['info']['uid'] ;
		$vip     = $this->userInfo['info']['vip'] ;
		$token   = $this->userInfo['token'];// 用户登录标记

		$organobj = new OrganManage;
		$msg = $organobj->changeMobile($mark,$oldphone,$newphone,$uid,$token,$vip);
		$this->ajaxReturn($msg);
	}

	/**
	 * [getOrganMsg 获取机构信息表单]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:21:42+0800
	 * @return   [string]                   [机构表单页面]
	 * URL:/admin/organ/getOrganMsg
	 */
	public function getOrganMsg(){
		$organobj = new OrganManage;
		$msg = $organobj->getOrganmsgById();
		return $msg;
	}
	/**
	 * [updateOrganMsg 更新机构信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T11:56:11+0800
	 * @return   [array]                   [更新结果]
	 * URL:/admin/organ/updateOrganMsg
	 */
	public function updateOrganMsg(){
		$data = Request::instance()->post();
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$teachlist = $organobj->updateOrganMsg($data);
		return $teachlist;
	}
	/**
	 * [getPayMethod 获取收款方式]
	 * @Author wyx
	 * @DateTime 2018-04-23T13:40:34+0800
	 * @return   [array]                   [返回查询的机构支付方式]
	 * URL:/admin/organ/getPayMethod
	 */
	public function getPayMethod(){
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$paylist = $organobj->getPayMethods();

		// var_dump($paylist);
		$this->ajaxReturn($paylist);
		return $paylist;
	}
	/**
	 * [updatePayMethod 更新机构的收款方式]
	 * @Author wyx
	 * @DateTime 2018-04-23T15:15:18+0800
	 * @return   [array]              [更新结果的状态]
	 * URL:/admin/organ/updatePayMethod
	 */
	public function updatePayMethod(){
		$data = Request::instance()->post();

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$teachlist = $organobj->updatePayMethod($data);

		// var_dump($teachlist);
		$this->ajaxReturn($teachlist);
		return $teachlist;
	}
	/**
	 * 获取课堂配置
	 * @Author wyx
	 * @DateTime 2018-04-23T15:15:18+0800
	 * @return   [array]              [更新结果的状态]
	 * URL:/admin/organ/getOrganConfig
	 *
	 */
	public function getOrganConfig(){
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$teachlist = $organobj->getOrganConfig();
		$this->ajaxReturn($teachlist);
	}

	/**
	 * 修改课堂配置
	 * @Author wyx
	 * @DateTime 2018-04-23T15:15:18+0800
	 * @return   [array]              [更新结果的状态]
	 * URL:/admin/organ/setOrganConfig
	 *
	 */
	public function setOrganConfig(){
		//$organid = 26;
		$toonetime = Request::instance()->post('toonetime');
		$smallclasstime = Request::instance()->post('smallclasstime');
		$bigclasstime = Request::instance()->post('bigclasstime');
		$maxclass = Request::instance()->post('maxclass');
		$minclass = Request::instance()->post('minclass');
		$data['toonetime'] = $toonetime ? $toonetime : '';
		$data['smallclasstime'] = $smallclasstime ? $smallclasstime : '';
		$data['bigclasstime'] = $bigclasstime ? $bigclasstime : '';
		$data['maxclass'] = $maxclass ? $maxclass : '';
		$data['minclass'] = $minclass ? $minclass : '';

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$teachlist = $organobj->setOrganConfig(1,$data);

		// var_dump($teachlist);
		$this->ajaxReturn($teachlist);
	}
	/**
	 * [getAdminList 获取机构添加的管理员列表]
	 * @Author wyx
	 * @DateTime 2018-04-23T18:45:13+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getAdminList
	 */
	public function getAdminList(){
		$limit   = config('param.pagesize')['adminorder_adminlist'] ;

		$username = $this->request->param('username') ;
		$pagenum  = $this->request->param('pagenum') ;

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$adminlist = $organobj->getAdminList($username,$pagenum,$limit);

		// var_dump($adminlist);
		$this->ajaxReturn($adminlist);
		return $adminlist;
	}
	/**
	 * [getAdminUser 获取单个机构管理员的信息]
	 * @Author
	 * @DateTime 2018-04-23T21:14:39+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getAdminUser
	 */
	public function getAdminUser(){

		$adminid = $this->request->param('adminid') ;//使用adminmember 中主键
		// $adminid = 1 ;//使用allaccount表 uid
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$admininfo = $organobj->getAdminUser($adminid);
		// var_dump($admininfo);
		$this->ajaxReturn($admininfo);
		return $admininfo;
	}
	/**
	 * [updateAdminUser 更新机构的管理员的信息]
	 * @Author wyx
	 * @DateTime 2018-04-23T21:16:02+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/updateAdminUser
	 */
	public function updateAdminUser(){
		$adminid = $this->request->param('adminid') ;//使用adminmember 中主键
		// $adminid = 19 ;//使用allaccount表 uid
		$data = Request::instance()->post();

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$retmsg = $organobj->updateAdminUser($data,$adminid);
		// var_dump($retmsg) ;
		$this->ajaxReturn($retmsg);
		return $retmsg;
	}
	/**
	 * [addAdminUser 为机构添加管理员]
	 * @Author wyx
	 * @DateTime 2018-04-23T21:11:01+0800
	 * URL:/admin/organ/addAdminUser
	 */
	public function addAdminUser(){
		$data = Request::instance()->post();

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$addflag = $organobj->addAdminUser($data);
		// var_dump($addflag);
		$this->ajaxReturn($addflag);
		return $addflag ;


	}
	/**
	 * [delAdminUser 删除机构添加的管理员]
	 * @Author wyx
	 * @DateTime 2018-04-23T21:13:36+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/delAdminUser
	 */
	public function delAdminUser(){
		$adminid = $this->request->param('adminid');

		// $adminid = 23;
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$delret = $organobj->delAdminUser($adminid);
		// var_dump($delret);

		$this->ajaxReturn($delret);
	}
	/**
	 * [switchAdminFlag 切换管理员的启用状态]
	 * @Author wyx
	 * @DateTime 2018-04-23T21:17:24+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/switchAdminFlag
	 */
	public function switchAdminFlag(){
		$adminid = $this->request->param('adminid') ;
		// $adminid = 22 ;
		$flag    = $this->request->param('flag') ;
		// $flag    = 1 ;

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$switchflag = $organobj->switchAdminFlag($flag,$adminid);
		// var_dump($switchflag);
		$this->ajaxReturn($switchflag);
		return $switchflag;

	}
	/**
	 * [organCourseList 机构课表]
	 * @Author wyx
	 * @DateTime 2018-04-25T09:44:53+0800
	 * @return   [type]                   [description]
	 * URL:/admin/organ/organCourseList
	 */
	public function organCourseList(){
		//指定 获取的 时间
		$date = $this->request->param('date') ;
		//如果没有提供 使用当前日期
		if(empty($date)) $date = date('Y-m-d') ;

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$listarr = $organobj->organCourseList($date);
		// var_dump($listarr);
		$this->ajaxReturn($listarr);
		echo json_encode($listarr) ;

	}
	/**
	 * [getLessonsByDate 通过日期获取有课的老师及其信息]
	 * @Author wyx
	 * @DateTime 2018-04-25T14:06:17+0800
	 * @param    [string]           date  必填日期 eg: 2018-04-06     
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getLessonsByDate
	 */
	public function getLessonsByDate(){
		$date = $this->request->param('date') ;
		$pagenum = $this->request->param('pagenum') ;
		$limit = config('param.pagesize')['adminorder_lessonlist'] ;
		// $date = '2018-03-26' ;
        $restype = $this->restype;
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $organobj->getLessonsByDate($pagenum,$limit,$restype,$date);
		$this->ajaxReturn($Lessonsarr);
		return $Lessonsarr ;

	}

	/**
     * 课时查询信息课时详情
     * @Author wangwy
     * @param 使用$teacherid 做查询
     * @return [type]                   [description]
     * URL:/teacher/PersonalCourse/getPeriodinfo
     */

    public function getPeriodinfo(){
        $data = Request::instance()->POST(false);
//         $data['restype']=$this->restype;
//         if ($this->restype ==1) {
//         	$data['teacherid'] = $this->userInfo['info']['teacherid'];
//         }
         //$data['limit'] = $data['pagesize'];
        $period = new OrganManage;
        $list = $period->getPeriodList($data);
        //var_dump($list);
        $this->ajaxReturn($list);
        return $list;
    }
    /**
     * 课时查询评价
     * @Author wangwy
     * @param 使用$teacherid 做查询
     * @return [type]                   [description]
     * URL:/teacher/PersonalCourse/getperComment
     */

    public function getperComment(){
        $data = Request::instance()->POST(false);
        $data['pagesize']=10;
        $data['restype']=$this->restype;
        if ($this->restype ==1) {
        	$data['teacherid'] = $this->userInfo['info']['teacherid'];
        }
        $period = new OrganManage;
        $list = $period->getperComment($data['restype'],$data['teacherid'],$data['lessonsid'],$data['date'],$data['pagenum'],$data['pagesize']);

        //var_dump($list);
        $this->ajaxReturn($list);
        return $list;
    }

    /**
     * [getLessonsPlayback 通过toteachid获取视频回放的相关信息]
     * @Author WangWY
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/getLessonsPlayback
     */
    public function getLessonsPlayback(){
        $toteachid = $this->request->param('toteachid');
        $currobj  =  new OrganManage;
        //获取教师列表信息,默认分页为5条
        $Lessonsarr = $currobj->getLessonsPlayback($toteachid);
        $this->ajaxReturn($Lessonsarr);
        return $Lessonsarr;
    }

     /**
     * [addWarefile 添加课时相关的文件夹列表和 资源列表关联]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    public function addWarefile(){
        $data = Request::instance()->post(false);
        //$data['showname'] = '文件夹1';
        //$data['limit']  = 1;
        // $data['id'] =1;//lessons表主键
        // $data['fileid'] = [1,2];//课件对应的fileid数组
        $data['restype']=$this->restype;
//        if ($this->restype ==1) {
//        	$data['teacherid'] = $this->userInfo['info']['teacherid'];
//        }
        $classesbegin = new OrganManage;
        $dataReturn = $classesbegin->addCourseware($data,$fileid=$data['fileid']);
        $this->ajaxReturn($dataReturn);
        return $dataReturn;
    }

     /**
     * [delWarefile 删除课时相关的文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     * URL:/teacher/Resources/getWarefile
     */
    public function delWarefile(){
        $data = Request::instance()->post(false);
        // $data['showname'] = '文件夹1';
        // $data['limit']  = 1;
        //$data['id'] =1;
        //$data['fileid'] = [1,2];
        $data['restype']=$this->restype;
        $classesbegin = new OrganManage;
        $dataReturn = $classesbegin->delCourseware($data,$fileid=$data['fileid']);
        $this->ajaxReturn($dataReturn);
        return $dataReturn;
    }
     /**
     * [getFileList 文件夹列表和 资源列表]
     * @param  $showname 文件夹名称
     * @param  $limit    第几页
     * @return [type] [description]
     *URL:/teacher/Resources/getFileList
     */
    public function getFileList(){
        $data = Request::instance()->post(false);
        //$data['showname'] = '文件夹1';
        //$data['fatherid']=isset($data['fatherid'])?$data['fatherid']:0;
        $data['pagenum']  = isset($data['pagenum'])?$data['pagenum']:1;
        $data['restype']=$this->restype;
        $data['usetype']=2;
        $data['filetype'] = 3;//同时显示共有和私有课件
        //$data['teacherid'] = $this->userInfo['info']['teacherid'];
        //$data['teacherid'] = $this->teacherid;
        $classesbegin = new OrganManage;
        $dataReturn = $classesbegin->getFileList($data);
        $this->ajaxReturn($dataReturn);
        return $dataReturn;
    }

    /**
     * [intoClassroom 通过toteachid获取进入教室相关信息]
     * @Author wangwy
     * @DateTime 2018-04-27T14:06:17+0800
     * @param    [string]           date  必填日期 eg: 2018-04-06
     * @return   [type]                   [description]
     * URL:student/Mycourse/intoClassroom
     */
    public function intoClassroom(){
        $toteachid = $this->request->param('toteachid');
        //$teacherid = $this->teacherid;
        //$toteachid = '39';
        $restype = $this->restype;
        if ($this->restype ==1) {
        	$teacherid = $this->userInfo['info']['teacherid'];
        }
        $currobj  =  new OrganManage;
        $data = $currobj->intoOranClassroom($restype,$teacherid,$toteachid);
        $this->ajaxReturn($data);

    }
	/**
	 * [getOrganAnalysis 获取机构的数据统计信息]
	 * @Author wyx
	 * @DateTime 2018-05-16
	 * @param    无
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getOrganAnalysis
	 */
	public function getOrganAnalysis(){
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $organobj->getOrganAnalysis();
		// var_dump($Lessonsarr);
		$this->ajaxReturn($Lessonsarr);
		// return $Lessonsarr ;

	}
	/**
	 * [getOrganAnaCourse 获取]
	 * @Author wyx
	 * @DateTime 2018-05-16
	 * @param    courseline 
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getOrganAnaCourse
	 */
	public function getOrganAnaCourse(){

		$data = Request::instance()->post();

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $organobj->getOrganAnaCourse($data);
		// var_dump($Lessonsarr);
		$this->ajaxReturn($Lessonsarr);
	}
	/**
	 * [getOrganAnaFlow 获取机构的数据统计信息]
	 * @Author wyx
	 * @DateTime 2018-05-16
	 * @param    flowline
	 * @return   [type]                   [description]
	 * URL:/admin/organ/getOrganAnaFlow
	 */
	public function getOrganAnaFlow(){
		$data = Request::instance()->post();

		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $organobj->getOrganAnaFlow($data);
		$this->ajaxReturn($Lessonsarr);
	}


	/**
	 * 获取各端交易流水
	 * @author	JCR
	 */
	public function getOrganPayAnaFlow(){
		$data = Request::instance()->post();
		$organobj  = new OrganManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $organobj->getOrganPayAnaFlow($data);
		$this->ajaxReturn($Lessonsarr);
	}


    /**
     * [setOrganBaseInfo //设置某机构基本信息]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param organid string           [机构id]     
     * @param contactname string               [联系人姓名]     
     * @param contactphone string           [联系人电话]     
     * @param contactemail string           [联系邮箱]     
     * @param summary string           [机构概述]     
     * @param phone string           [客服电话]     
     * @param email string           [客服邮箱]  
     * @param organname string           [机构名]  
     * @param imageurl string           [logo地址]  
     * @return array  返回的信息值
     */
    public function setOrganBaseInfo(){

        $data = Request::instance()->post(false);
		// 此次做了兼容 JCR 2018-08-22
        $data['id'] = 1;
        $organ = new OrganManage();
        $res = $organ->setOrganBaseInfo($data);
        $this->ajaxReturn($res);
        return $res;  
    }



	/**
	 * [getOrganBaseInfo //机构后台 展示基本信息]
	 * @Author zzq
	 * @DateTime 2018-05-03
	 * @param organid int 该机构的id
	 * @return array 机构的基本信息
	 */
	public function getOrganBaseInfo(){
		$organ = new OrganManage();
		$res = $organ->getOrganBaseInfo(1);
		$this->ajaxReturn($res);
	}


    /**
     * [setOrganConfirmInfo //机构后台 修改认证信息]
     * @Author zzq
     * @DateTime 2018-05-03
     * @param organid int           [机构id]     
     * @param idname string               [个人或法人姓名]     
     * @param idnum string           [个人或法人身份证号]     
     * @param frontphoto string           [个人或法人正面照]     
     * @param backphoto string           [个人或法人背面照]     
     * @param organnum string           [营业执照号码]     
     * @param organphoto string           [营业执照照片]  
     * @param organname string           [企业名称]  
     * @param confirmtype           [认证类型  1表示个人 2表示企业]  
     * @return array  返回的信息值
     */
    public function setOrganConfirmInfo(){

        $data = [];
        $data['idname'] = Request::instance()->post('idname','');
        $data['idnum'] = Request::instance()->post('idnum','');
        $data['frontphoto'] = Request::instance()->post('frontphoto','');
        $data['backphoto'] = Request::instance()->post('backphoto','');
        $data['organname'] = Request::instance()->post('organname','');
        $data['organnum'] = Request::instance()->post('organnum','');
        $data['organphoto'] = Request::instance()->post('organphoto','');
        $data['confirmtype'] = Request::instance()->post('confirmtype','');
        $data['organid'] = 1;

        $organ = new OrganManage();
        $res = $organ->setOrganconfirmInfo($data);
        $this->ajaxReturn($res);
        return $res;  

    }
    
    /**
     * [getOrganConfirmInfo //机构后台 展示认证信息]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 返回信息
     */
    public function getOrganConfirmInfo(){
        $organ = new OrganManage();
        $res = $organ->getOrganConfirmInfo(1);
        $this->ajaxReturn($res);
        return $res; 
    }

    /**
     * [getAuditResByOrganId //获取机构审核结果  成功|失败]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 返回信息
     */
	public function getAuditResByOrganId(){
        $organ = new OrganManage();
        $res = $organ->getOrganAuditResById(1);
        $this->ajaxReturn($res);
        return $res;
	}


	//获取机构被最近的拒绝的原因(放到business)
	//这个是用在机构再次认证的时候
    /**
     * [getLatestResuseInfoByOrganId ////获取机构被最近的拒绝的原因]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @param type int  0的时候表示第一次认证  1表示再次认证
     * @return array 返回信息
     */
	public function getLatestResuseInfoByOrganId(){
        $organ = new OrganManage();
        $res = $organ->getOrganRefuseInfo(1);
        $this->ajaxReturn($res);
        return $res;
	}

    /**
     * [getOrganIntroduceInfo ////机构审核过程中获取机构的介绍信息]
     * @Author zzq
     * @DateTime 2018-05-05
     * @param organid int  机构id
     * @return array 返回信息
     */
    public function getOrganIntroduceInfo(){
        $organManage = new OrganManage();
        $res = $organManage->getOrganIntroduceInfo(1);
        $this->ajaxReturn($res);
        return $res;          
    }
    /**
     * [FromRefusedToUnAudited //设置审核被拒绝的机构状态变为未认证]
     * @Author zzq
     * @DateTime 2018-05-07
     * @param organid int  机构id
     * @return array 返回信息
     */
    public function FromRefusedToUnAudited(){
        $organManage = new OrganManage();
        $res = $organManage->FromRefusedToUnAudited(1);
        $this->ajaxReturn($res);
        return $res;     
    }



	/**
	 * 获取登陆管理员信息
	 * @Author zzq
	 * @param [status] [1表示有效的 2表示所有的都返回]
	 * @return array  [返回信息]
	 *
	 */
	public function getUserMeader(){
		$configManage = new OrganManage();
//		dump($this->userInfo);
		$res = $configManage->getUserMeader($this->userInfo['info']['uid']);
		$this->ajaxReturn($res);
	}


	/**
	 * 修改 关于我们
	 */
	public function setOrganAboutus(){
		$configManage = new OrganManage();
		$aboutus = Request::instance()->post('aboutus');
		$dataReturn = $configManage->setOrgan($aboutus,1);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 获取关于我们
	 */
	public function getOrganAboutus(){
		$configManage = new OrganManage();
		$dataReturn = $configManage->getOrganAboutus(1);
		$this->ajaxReturn($dataReturn);
	}
	
	/**
	 * 修改下载配置
	 */
	public function setOrganDownloadJson(){
		$configManage = new OrganManage();
		$data = Request::instance()->post(false);
		$dataReturn = $configManage->setOrganDownloadJson($data,1);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 获取下载配置
	 */
	public function getOrganDownloadJson(){
		$configManage = new OrganManage();
		$dataReturn = $configManage->getOrganDownloadJson(1);
		$this->ajaxReturn($dataReturn);
	}


}
