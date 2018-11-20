<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 课程单元Model
 * @ jcr
*/
class Coursecomment extends Model{

    protected $table = 'coursecomment';
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
    }


    /**
     * getId 根据课程单元id
     * @ jcr
     * @param $id 课程单元id
     * @param $field 查询内容 默认不传全部
     * @return array();
    */
	public function getId($id,$field){
	     if (!$id) return false;
	     return Db::name($this->table)->where(array('id'=>$id))->field($field)->find();
    }

    
    /**
     * getId 查询对应课程评论
     * @ jcr
     * @param $where 查询条件
     * @return array();
     */
    public function getList($where,$limit=1,$pagenum){
        $pagenum = $pagenum?$pagenum:$this->pagenum;

        $data['data'] = Db::name($this->table)->alias('c')
                            ->join('nm_scheduling s','c.schedulingid = s.id','LEFT')
                            ->where($where)
                            ->where('c.delflag','eq',1)
                            ->where('c.status','eq',1)
                            ->page($limit,$pagenum)
                            ->field('c.id,c.content,c.classtype,c.studentid,c.allaccountid,c.score,c.addtime,s.gradename')->select();
        $inwhere = ['curriculumid'=>$where['c.curriculumid']];
        isset($where['c.lessonsid']) && $inwhere['lessonsid'] = $where['c.lessonsid'];

        $data['pageinfo'] = array('pagesize'=>$pagenum,'pagenum'=>$limit,'total'=>$this->getListCount($inwhere));
        return $data;
    }


    /**
     * getId 查询对应课程评论数量
     * @ jcr
     * @param $where 查询条件
     * @return array();
     */
    public function getListCount($where){
        return Db::name($this->table)
                            ->where($where)
                            ->where('status','eq',1)
                            ->where('delflag','eq',1)->count();
    }


}
