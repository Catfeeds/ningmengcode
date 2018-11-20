<?php
/**
 **基类控制器
 **/
use think\Controller;
use think\Db;
/**
 * 学生端查询域名基类
 *
 */
class Base extends Controller{
    public function __construct(){
        // 必须先调用父类的构造函数
        parent::__construct();
        $organid = $this->getUserOrgan();
        $this->organid = $organid;
    }
    public function getUserOrgan(){
        $hostname = $_SERVER['HTTP_HOST'] ;
        $arr = explode('.', $hostname) ;
        //严格校验域名必须三段
        $organstr = $arr[0] ;
        $organmsg = Db::table('nm_organ')->field('id,organname,profile')->where(['domain'=>$organstr])->find() ;
        return $organmsg?$organmsg['id']:1;
    }

}