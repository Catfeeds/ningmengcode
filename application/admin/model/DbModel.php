<?php
namespace app\admin\model;
use think\Model;
use think\Db;

/*
 * 课程Model
 * @ jcr
*/
class DbModel extends Model{
    // 数据库配置
    private $configs;

    //自定义初始化
    protected function initialize(){
        parent::initialize();

    }


    public function __construct(){
        $this->configs = config('database');
    }

    /**
     * @ jcr
     * 读写分离，获取从库 数据连接通道
     * @param $type 0 查  1 修改、插入
     * @return 数据库连接通道
     * */
    public static function getCommObj($type=0){
        //后期集成算法 随机取从库
        $configs = config('database');
        if($type==0){
            return Db::connect($configs['type'].'://'.$configs['username'].':'.$configs['password'].'@'.$configs['hostname'].':'.$configs['hostport'].'/'.$configs['database'].'#utf8');
        }else {
            return Db::connect($configs['type'] . '://' . $configs['username'] . ':' . $configs['password'] . '@' . $configs['hostname'] . ':' . $configs['hostport'] . '/' . $configs['database'] . '#utf8');
        }
    }















}
