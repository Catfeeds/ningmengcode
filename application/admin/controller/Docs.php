<?php
namespace app\admin\controller;
use think\Controller;
use think\View;

class Docs extends Controller {


	
    public function index(){
    	require  "./../application/admin/api/JiCR.php";
    	require  "./../application/admin/api/WangWY.php";
    	require  "./../application/admin/api/WangYX.php";
    	require  "./../application/admin/api/YuRui.php";
    	require  "./../application/admin/api/ZhaoZQ.php";
        require  "./../application/admin/api/LiChen.php";
        require  "./../application/admin/api/ZhangQY.php";
        require  "./../application/admin/api/HjxLiChen.php";
        require  "./../application/admin/api/HjxZhangQY.php";
        require  "./../application/admin/api/HjxChenYi.php";

	    // $api[] = array(
	    // 			'url'=>'/admin/Course/getCurricukumList',
	    // 			'name'=>'课程列表接口',
	    // 			'type'=>'get',
	    // 			'data'=>"{'status':1,'coursename':1}",
	    // 			'tip'=>"{'status':'0下架 1上架','coursename':'课程名称','limit':'第几页'}",
	    // 			'returns'=>"{'id': '课程id',
	    // 						'imageurl': '课程图片',
     //                            'coursename': '课程名称',
     //                            'price': '基础价',
     //                            'status': '状态 0下架 1上架',
     //                            'categoryid': 6,
     //                            'categoryname': '分类名称'}",
	    // 			);


//$JiCR=$WangWY=$YuRui=$WangYX=$ZhaoZQ=[];
	    $api = array_merge($JiCR,$WangWY,$YuRui,$WangYX,$ZhaoZQ,$LiChen,$ZhangQY,$HjxLiChen,$HjxZhangQY,$HjxChenYi);
	   //$api = array_merge($YuRui,$WangYX,$ZhaoZQ,$LiChen);

   		$view = new View();
		$view->num = sizeof($api);
		$view->api = $api;
		return $view->fetch('home');
    }


    public function index11(){
    	$view = new View();
    	return $view->fetch('index');
    }



    /**
	 * 上传测试
	 */
    public function updates(){
//		require_once("../extend/ucloud/proxy.php");
//		$bucket = 'talkcloud002';
////		$key = $UCLOUD_PUBLIC_KEY;
//		$file = './aa.txt';
//		$indata =  UCloud_MultipartForm($bucket, 'talkcloud002_aaa11aa.txt', $file);
//		dump($indata);
	}

   
	
}