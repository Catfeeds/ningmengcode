<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;

/*
 * 老师推荐Model
 * @ yr
*/
class Recommendteacher extends Model{
    protected $table = 'nm_recommendteacher';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getRecommentList 获取机构老师推荐list信息]
     * @Author yr
     * @DateTime 2018-04-21T10:31:56+0800
     * @return   array
     */
    public function getRecommentList(){
        $where['recommend'] =  1;
        $lists = Db::table($this->table)
            ->alias('a')
            ->join('teacherinfo t','a.teacherid = t.teacherid')
            ->where(  'a.recommend = 1'or 'a.settop = 1' )
            ->field(['a.teachername','t.imageurl','t.classesnum','t.profile','a.teacherid','a.settop'])
            ->order('a.settop desc,	a.sortnum')
            ->limit(6)
            ->select();
        return $lists;
    }
}







