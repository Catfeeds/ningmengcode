<?php

namespace app\teacher\model;

use think\Model;
use think\Db;
//对课时表进行操作
class Period extends Model
{
  protected $table = 'nm_period';
  protected $organid;
  protected $pagenum; //每页显示行数

  //自定义初始化
  protected function initialize(){
      //$this->organid = 1;
      $this->pagenum = config('paginate.list_rows');
      parent::initialize();
  }

  // 课程添加验证规则
  protected $rule = [
      'periodname'  => 'require',
      // 'courseware'  => 'require',

  ];

  protected $message  = [
      'periodname.require' => '课时名称不能为空',  ];


  /**
   * getId 根据课程查询课时
   * @ jcr
   * @param $where 查询
   * @return array();
   */
   public function getIdsLists($id){
       $where['curriculumid'] = $id;
       $lists = Db::table($this->table)
                           ->where($where)
                           ->where('delflag','eq',1)
                           ->order('periodsort desc')
                           ->field('id,periodname,periodsort,courseware,unitid,curriculumid')->select();
       //print_r(Db::table($this->table)->getLastSql());
       return $lists;
   }

}
