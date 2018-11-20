<?php
namespace app\student\model;
use think\Db;
class Coursetags
{

    /**
     * 默认全选查询第一级分类
     * @return mixed
     */
    public function getTags ()
    {
       return Db::table('nm_coursetags')
            ->field('id as tagid,tagname,fatherid')
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->order('fatherid,sort')
            ->select();
    }

    public function getFtags ($id)
    {

        return Db::table('nm_coursetags')
            ->where(['status'=>1])
            ->where(['delflag'=>1])
            ->where(['fatherid'=>$id])
            ->select();
    }

}
