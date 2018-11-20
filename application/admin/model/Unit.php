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
class Unit extends Model{

    protected $table = 'unit';
    protected $pagenum; //每页显示行数



    // 课程添加验证规则
    protected $rule = [
        'unitname'  => 'require|max:50',
    ];

    protected $message  = [];

	//自定义初始化
	protected function initialize(){
		parent::initialize();
		$this->pagenum = config('paginate.list_rows');
		$this->message = [
			'unitname.require' => lang('10512'),
			'unitname.max'     => lang('10513'),
		];
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
     * getId 基础查询课程单元
     * @ jcr
     * @param $where 查询条件
     * @return array();
     */
    public function getLists($id){

        $lists = Db::name($this->table)
                            ->where('curriculumid','eq',$id)
                            ->where('delflag','eq',1)
                            ->order('unitsort asc')
                            ->field('id,unitname,curriculumid,unitsort')->select();
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
            $ids = Db::name($this->table)->where('id','eq',$data['id'])->update($data);
        }else{
            //添加
            $data = where_filter($data,array('id','curriculumid','unitname','unitsort'));
            $ids = Db::name($this->table)->insertGetId($data);
        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10053,'info'=>'添加课时异常');
    }


	/**
	 * [addInser 复制时直插]
	 * @param $data
	 * @return int|string
	 */
    public function addInser($data){
		return  Db::name($this->table)->insertGetId($data);
	}

    /**
     * 删除单元
     */
    public function deleteIds($where){
        return Db::name($this->table)->where($where)->update(['delflag'=>0]);

    }


}
