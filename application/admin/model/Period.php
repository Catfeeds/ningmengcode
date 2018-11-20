<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 课程Model
 * @ jcr
*/
class Period extends Model{

    protected $table = 'period';
    protected $pagenum; //每页显示行数



    // 课程添加验证规则
    protected $rule = [
        'periodname'  => 'require',
        // 'courseware'  => 'require',

    ];

    protected $message  = [];

	//自定义初始化
	protected function initialize(){
		$this->pagenum = config('paginate.list_rows');
		parent::initialize();
		$this->message = [
			'periodname.require' => lang('10508'),
			// 'courseware.require' => '课时课件不能为空'
		];
	}

    /**
     * getId 根据课时id 
     * @ jcr
     * @param $id 课程id
     * @param $field 查询内容 默认不传全部
     * @return array();
    */
	 public function getId($id,$field){
	     if (!$id) return false;
	     return Db::name($this->table)->where(array('id'=>$id))->field($field)->find();
     }

    /**
     * getId 基础查询课时
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @return array();
     */
     protected function getCurriculumList($where,$field,$orderbys='',$limit = 1,$pagenum){
         $pagenum = $pagenum?$pagenum:$this->pagenum;
         $lists = Db::name($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->field($field)->select();
         return $lists;
     }


     /**
     * getId 根据课时单元查询课时
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getLists($data){
        $where = where_or($data,'unitid','eq');
        $lists = Db::name($this->table)
                            ->where($where)
                            ->where('delflag','eq',1)
                            ->order('periodsort asc')
                            ->field('id,periodname,periodsort,courseware,unitid')->select();
        return $lists;
     }


     /**
     * getId 根据课程查询课时
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getIdsLists($id){
        $where['curriculumid'] = $id;
        $lists = Db::name($this->table)
                            ->where($where)
                            ->where('delflag','eq',1)
                            ->order('periodsort asc')
                            ->field('id,periodname,periodsort,courseware,unitid,curriculumid')->select();
        return $lists;
     }






    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data,$type=''){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)&&$type!='all'){
            return array('code'=>10050,'info'=>$validate->getError());
        }

        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','periodname','periodsort','courseware','delflag'));
            $ids = Db::name($this->table)->where('id','eq',$data['id'])
										->update($data);
        }else{
            //添加
            if($type=='all'){
                //批量插入
                $ids = Db::name($this->table)->insertAll($data);
            }else{
                $data = where_filter($data,array('id','periodname','periodsort','courseware','curriculumid','unitid'));
                $ids = Db::name($this->table)->insertGetId($data);
            }
        }
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>10051,'info'=>'添加课程单元异常');
    }


    /**
     * 删除课时
     */
    public function deleteIds($where){
        return Db::name($this->table)->where($where)->update(['delflag'=>0]);
    }

	/**
     *  根据课程id和课时名称查询课时
     * @ lc
     * @param $where 查询
     * @return array();
     */
     public function getListsByCurriculumid($where){
        $lists = Db::name($this->table)
                            ->where($where)
                            ->order('periodsort asc')
                            ->field('id,periodname')->select();
        return $lists;
     }
}
