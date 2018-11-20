<?php

namespace app\appteacher\business;
use app\appteacher\model\Organ;
use think\Request;
use app\appteacher\model\TeacherInfo;
use app\teacher\model\Allaccount;
class LoginManage
{
      public function  __construct() {
      //定义空的数组对象
      $this->foo = (object)array();
      //定义空字符串
      $this->str = '';
      }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */

    // public function login ($mobile,$password,$organid)
    // {

    //     //先判断手机号长度
    //     if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
    //         return return_format($this->str,32000,'请输入6-12位手机号');
    //     }else{
    //         $studentmodel = new Studentinfo;
    //         $data = $studentmodel ->checkLogin($mobile,$organid);
    //     //如果长度没问题判断手机号是否存在,或者手机号被删除
    //     if(!$data || $data['delflag'] == 0){
    //         return return_format($this->str,32001,'手机号不存在');
    //     }else{
    //         //判断用户登录状态，是否禁用
    //     if($data['status'] == 1){
    //         return  return_format($this->str,32002,'该手机号已被禁用!请联系管理员');
    //     }
    //     $res = $this->checkUserMark($password,$data['mix'],$data['password']);
    //         //如果用户名存在判断密码是否正确
    //     if($res == false){
    //        return  return_format($this->str,32003,'密码错误');
    //     }else{
    //         //设置token
    //         $loginobj = new Login;
    //         $token = $loginobj->settoken($data['id'],1);
    //         $data['token'] = $token;
    //         return  return_format($data,0,'登录成功');
    //     }

    //     }


    // }

    /**
     * [adminLogin 教师登录验证]
     * @Author wyx
     * @DateTime 2018-05-10
     * @param    [int]             $username [登陆名字]
     * @param    [int]             $passwd   [密码]
     * @param    [int]             $organid [机构登陆后存入机构id]
     * @return   [array]                    [机构信息]
     */
     public function teacherLogin($mobile,$passwd,$domain){
       if (is_numeric($domain)) {
         $organid = $domain;
       }else{
         $organ = new Organ;
         $organidarr = $organ->getOrganid($domain);
         $organid = $organidarr['id'];
       }
         if(empty($organid)) return return_format('',233333,'该机构不存在');
         // if($organid>0 && !empty($username) && !empty($passwd) ){
         //     $auth = new \Authorize;
         //     return $auth->checkUser($username,$passwd,1,$organid);
         // }else{
         //     return return_format('',-20010,'用户或密码不能为空');
         // }
         if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(trim($mobile))){
             return return_format($this->str,22000,'请输入6-12位手机号');
         }else{
             $teachermodel = new TeacherInfo;
             $accountmodel = new Allaccount;
             $data = $teachermodel ->checkLogin($mobile,$organid);
             $datab = $accountmodel ->checkLogin($mobile,$organid);
             //如果长度没问题判断手机号是否存在,或者手机号被删除
             if(!$data || $data['delflag'] == 0){
                 return return_format($this->str,22001,'手机号不存在');
             }else{
                 //判断用户登录状态，是否禁用
                  if($datab['status'] == 1){
                      return  return_format($this->str,22002,'该账号号已被禁用!请联系管理员');
                  }
                  $author = new \login\Authorize;
                  $res = $author->checkUserMark($passwd,$datab['mix'],$datab['password']);
                    //如果用户名存在判断密码是否正确
                  if($res == false){
                     return  return_format($this->str,32003,'密码错误');
                  }else{
                      //设置token
                      unset($data['password']);
                      unset($data['mix']);
                      $loginobj = new \TeacherLogin;
                      $token = $loginobj->settoken($data['teacherid'],$organid,2);
                      $data['token'] = $token;
                      //机构图片
                      $data['organimage'] = isset($organidarr)?$organidarr['imageurl']:'';
                      return  return_format($data,0,'登录成功');
                  }
             }

         }
     }



}
