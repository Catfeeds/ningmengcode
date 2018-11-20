<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 课程单元Model
 * @ jcr
*/
class Wxpaypushlog extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_wxpaypushlog';//表名称
    /**
     * 将签名验证成功的推单数据 插入数据表
     * @author wyx
     * @param $data 添加数据源
     */
    public function addAlipayPushLog($data){
        //将表单数据插入数据库

        $result = $this->allowField(true)->save($data);
        $sql = Db::table('nm_wxpaypushlog')->getLastSql();
        file_put_contents('wxpushlog.txt',print_r($sql,true),FILE_APPEND) ;
        return $result;
    }

}
