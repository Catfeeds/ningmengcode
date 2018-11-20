<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $_POST = array_map('filterKey',$_POST);
// $_GET  = array_map('filterKey',$_GET);
// function filterKey($value){
//     if(is_array($value)){
//         array_filter($value, 'filterKey');
//     }else{
//         $sqlkey = array("select", 'insert', "update", "delete",'\*' , "union", "into", "load_file", "outfile","database",'table','column','INFORMATION_SCHEMA','alter','index');
//         foreach ($sqlkey as $val) {
//             $pattern = '/'.$val.'/i';
//             $value = preg_replace($pattern, '', $value);

//         }
//         return $value;

//     }
// }
// [ 应用入口文件 ]
 //echo phpinfo();
// 定义应用目录
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token,lang');
}else{

	define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
	require __DIR__ . '/../thinkphp/start.php';
}