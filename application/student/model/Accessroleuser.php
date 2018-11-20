<?php
namespace app\student\model;
use think\Db;
class Accessroleuser
{
    /**
     *  新增用户后 ，给用户添加的默认的组
     *  用户       $uid
     *  用户类型   $usertype
     *  根据用户的类型 添加到默认分组
     *
     */
    public function addUserDefaultAcl($uid,$usertype){
        $typevsrole = [//1为老师0为超级管理员，2机构添加的管理账号
            1 => 2 ,//1老师类型   对应
            2 => 1 ,//用户类型 2  对应 机构管理员 角色
            3 => 3 ,//3学生类型 对应3 学生角色
            4 => 4 ,//4好际星app学生 对应4 好际星app学生组
        ] ;

        $data = [
            'roleid' => $typevsrole[$usertype],
            'uid' => $uid,
            'usertype' => $usertype,

        ] ;
        Db::table('nm_accessroleuser')
            ->insert($data);

    }

}
