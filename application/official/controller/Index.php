<?php
/**
*官方后台管理员控制器
**/
namespace app\official\controller;
use app\official\business\AnalysisManage;
use think\Request;
use think\Controller;
use app\official\controller\Base;
use login\Authorize;
class Index extends Authorize
{		
	protected $organid;
    /**
     *  
     *
     */
    public function __construct(){
        parent::__construct();
        $this->organid = $this->userInfo['organid'];  
    }
	/**
	 * 官方后台 首页统计数据
	 * @Author wyx
	 * @param    string  []
	 * @return array  [返回信息]
	 * URL:/official/Index/index
	 */
	public function index(){
		
		$analysisobj = new AnalysisManage;
		$resultdata = $analysisobj->getAnalysisData();

		$this->ajaxReturn($resultdata);
		
	}
	/**
	 * [getOrganAnalysis 获取]
	 * @Author wyx
	 * @DateTime 2018-05-16
	 * @param    courseline 
	 * @return   [type]                   [description]
	 * URL:/official/Index/getOrganAnaCourse
	 */
	public function getOrganAnaCourse(){
		$data = Request::instance()->post();

		$analysisobj  = new AnalysisManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $analysisobj->getOrganAnaCourse($data);
		// var_dump($Lessonsarr);
		$this->ajaxReturn($Lessonsarr);
		// return $Lessonsarr ;
	}
	/**
	 * [getOrganAnalysis 获取机构的数据统计信息]
	 * @Author wyx
	 * @DateTime 2018-05-16
	 * @param    flowline
	 * @return   [type]                   [description]
	 * URL:/official/Index/getOrganAnaFlow
	 */
	public function getOrganAnaFlow(){
		$data = Request::instance()->post();
		
		$analysisobj  = new AnalysisManage;
		//获取教师列表信息,默认分页为5条
		$Lessonsarr = $analysisobj->getOrganAnaFlow($data);
		// var_dump($Lessonsarr);
		$this->ajaxReturn($Lessonsarr);
		// return $Lessonsarr ;
	}

}	