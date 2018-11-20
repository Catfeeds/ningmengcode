<?php
namespace app\student\model;
use think\Db;
class Allaccount
{
    /**
     * 学生端登陆检查
     * @Author why
     * @param $phone
     * @return mixed
     */
    public function checkLogin ($phone)
    {
        $data = 'select password,nickname,id,imageurl from nm_studentinfo where moblie = '.$phone;

        $info = Db::query($data);

        $info = $info[0];

        return $info;
    }

}
