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
class Lessons extends Model{

    protected $table = 'lessons';
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
     * getLists 根据课时单元查询课时 和课时对应时间
     * @ jcr
     * @param $where 查询
     * @return array();
     */
     public function getInLists($id,$teacherid){
        $lists = Db::name($this->table)->alias('u')
                            ->join('nm_toteachtime t',' u.id = t.lessonsid ','LEFT')
                            ->where('u.schedulingid','eq',$id)
                            ->where('t.type','neq',1)
                            ->where('u.teacherid','eq',$teacherid)
                            ->where('u.delflag','eq',1)
                            ->order('u.periodsort asc')
                            ->field('u.id,u.periodname,u.periodsort,u.courseware,u.unitid,u.classhour,t.intime,t.timekey,t.teacherid')->select();
        return $lists;
     }


	/**
	 * getLists 根据课程ID 获取对应的课时
	 * @ jcr
	 * @param $where 查询
	 * @return array();
	 */
	public function getLists($id,$pagenum,$limit){
		$lists = Db::name($this->table)->alias('u')
					->join('nm_toteachtime t',' u.id = t.lessonsid ','LEFT')
					->where('u.schedulingid','eq',$id)
					->where('u.delflag','eq',1)
					->order('u.unitid asc,u.periodsort asc')
					->field('u.id,u.periodname,u.periodsort,t.starttime,t.endtime,t.teacherid')
					->page($pagenum,$limit)
					->select();
		return $lists;
	}

    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addEdit($data,$type=false){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)&&$type!='all'){
            return array('code'=>10050,'info'=>$validate->getError());
        }

        if(isset($data['id'])){
            //修改
            $data = where_filter($data,array('id','periodname','periodsort','courseware','delflag'));
            $ids = Db::name($this->table)->where('id','eq',$data['id'])->update($data);
        }else{
            //添加
            if($type=='all'){
                //批量插入
                $data = array_filter($data);
                $ids = Db::name($this->table)->insertAll($data);
            }else{
                $data = where_filter($data,array('id','periodname','periodsort','courseware','curriculumid','unitid','schedulingid','teacherid','periodid','classhour'));
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
     * 根据id 获取课节指定字段
     * @php lc
     * $id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getFieldName($id,$field){
        return Db::name($this->table)->where('id','eq',$id)->field($field)->find();
    }
	
	/**
     * 根据periodid获取指定字段
     * @php lc
     * $id
     * @$field 查询字段
     * @return [type] [description]
     */
    public function getFieldByPeriodid($periodid,$field='id'){
        return Db::name($this->table)->where('periodid','eq',$periodid)->field($field)->select();
    }
    
	/**
     * 根据id编辑课时信息
     */
    public function editBylessonsid($data){
        $data = where_filter($data,array('id','classhour'));
        $id = $data['id'];
        unset($data['id']);
        $id = Db::name($this->table)->where('id','eq',$id)->update($data);
        return $id?array('code'=>0,'info'=>$id):array('code'=>11037,'info'=>lang('11037'));
    }
	
	/**
     * 根据schedulingid修改teacherid
     */
    public function editBySchedulingid($data){
        $data = where_filter($data, array('schedulingid','teacherid'));
        $schedulingid = $data['schedulingid'];
        unset($data['schedulingid']);
        $ids = Db::name($this->table)->where('schedulingid','eq',$schedulingid)->where('delflag', 1)->update($data);
        return $ids?array('code'=>0,'info'=>$ids):array('code'=>11038,'info'=>lang('11038'));
    }
}
