<?php
namespace app\teacher\model;
use think\Model;
use think\Db;
use think\Validate;
use app\teacher\model\DbModel;

/*
 * 课程单元Model
 * @ jcr
*/
class Unitdeputy extends Model{

    protected $table = 'nm_unitdeputy';
    protected $organid;
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        parent::initialize();
    }

    // 课程添加验证规则
    protected $rule = [
        'unitname'  => 'require|max:50',
    ];

    protected $message  = [
        'unitname.require' => '单元名称不能为空',
        'unitname.max'     => '单元名称最多不能超过50个汉字',
    ];

    public function __construct(){
        $this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
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
	     return Db::table($this->table)->where(array('id'=>$id))->field($field)->find();
    }




    /**
     * getId 基础查询课程单元
     * @ jcr
     * @param $where 查询条件
     * @return array();
     */
    public function getLists($id,$schedulingid){

        $lists = Db::table($this->table)
                            ->where('curriculumid','eq',$id)
                            ->where('schedulingid','eq',$schedulingid)
                            ->where('delflag','eq',1)
                            ->order('unitsort asc')
                            ->field('id,unitname,curriculumid,unitsort')->select();
        //print_r(Db::table($this->table)->getlastsql());
        return $lists;
    }




    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)){
            return array('code'=>10052,'info'=>$validate->getError());
        }

        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','unitname','unitsort','delflag'));
            $ids = Db::table($this->table)->where('id','eq',$data['id'])->update($data);
        }else{
            //添加
            $data['organid'] = $this->organid;
            $data = where_filter($data,array('curriculumid','unitname','unitsort','organid','schedulingid'));
            $ids = Db::table($this->table)->insertGetId($data);
        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10053,'info'=>'添加课时异常');
    }

    /**
     * 删除单元
     */
    public function deleteIds($where){
        return Db::table($this->table)->where($where)->update(['delflag'=>0]);

    }


}
