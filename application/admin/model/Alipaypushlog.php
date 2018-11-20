<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 课程单元Model
 * @ jcr
*/
class Alipaypushlog extends Model{
    protected $pk    = 'id';//操作的表主键
    protected $table = 'nm_alipaypushlog';//表名称
    /**
     * 将签名验证成功的推单数据 插入数据表
     * @author wyx
     * @param $data 添加数据源
     */
    public function addAlipayPushLog($data){
        //将表单数据插入数据库
        return $this->allowField(true)->save($data);
    }

}
