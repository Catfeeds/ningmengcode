<?php
namespace app\index\model;
use think\Db;
class test
{
    public function mygetData()
    {
        return Db::table('test')->field('id,name,title')->select();
    }
}
