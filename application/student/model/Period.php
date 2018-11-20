<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 课时Model
 * @ yr
*/
class Period extends Model{
    protected $table = 'nm_period';
    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }
    public function __construct(){
    }
    /**
     * [getPeriodList 获取指定单元的课时List]
     * @Author yr
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $curriculumid [课程id]
     * @return   array
     */
    public function getPeriodList($unitid){
        $lists =Db::table($this->table)
            ->where('delflag','eq','1')
            ->where('unitid',$unitid)
            ->field('curriculumid,periodname,periodsort')
            ->order('periodsort')
            ->select();
        return  $lists;
    }
    /**
     * [getLessonsList 获取指定单元的课时List]
     * @Author yr
     * 小班和大班课程关联查询出上课时间
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $unitid[课程单元id]
     * @param    [int]        $schedulingid[排课 id]
     * @return   array
     */
    public function getLessonsList($unitid){
        $where = [
            'l.unitid'=> $unitid,
        ];
            $lists =Db::table($this->table.' l')
                ->field('l.curriculumid,l.periodname,l.periodsort,l.id as lessonsid')
                ->where($where)
                ->order('l.periodsort')
                ->select();
        return  $lists;
    }
    /**
     * [getFileInfo 获取录播课的视频Url]
     * @Author yr
     * 小班和大班课程关联查询出上课时间
     * @DateTime 2018-04-23T13:58:56+0800
     * @param    [int]        $unitid[课程单元id]
     * @param    [int]        $schedulingid[排课 id]
     * @return   array
     */
    public function getFileInfo($lessonsid){
      $result = Db::table($this->table.' l')
          ->field('f.fileurl,f.cosurl')
          ->join('nm_filemanage f','f.fileid=l.courseware','LEFT')
          ->where('id','eq',$lessonsid)
          ->find();
      return $result;
    }
}







