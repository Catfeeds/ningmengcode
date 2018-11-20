<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
use app\admin\model\Studentinfo;
use app\admin\model\Knowledge;
class StudentCategory extends Model
{	

	protected $pk    = 'id';
	protected $table = 'nm_studentcategory';
	protected $rule = [
			'name' => 'require|max:30',
		];
	protected $message = [];

    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->pagenum = config('paginate.list_rows');
        $this->message = [
            'name.require'   => lang('70002'),
            'name.max'       => lang('70003'),

        ];
    }
	
	/**
	 * 从数据库获取
	 * @Author lc
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                   [description]
	 */
    public function getCategoryList($where,$field,$limitstr,$order='id asc')
    {
        // var_dump($order);
        return Db::table($this->table)->where($where)->field($field)->limit($limitstr)->order($order)->select();
    }
	
    /**
     * @Author lc
     * @param $where    array       必填
     * @param $order    string      必填
     * @param $limitstr string      必填
     * @DateTime 2018-04-17T11:32:53+0800
     * @return   array                   [description]
     *
     */   
    public function getCategoryListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
	
    /**
	 * 根据teacherid获取 学生分类的详细信息
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @param  $teachid 学生分类表teacherid
	 * @return   array                [description]
	 */
    public function getCategoryData($field,$id)
    {
        return Db::table($this->table)
        ->where('id',$id)
        ->field($field)->find();
    }
   
    /**
     * [addTeacher 添加学生分类数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function addCategory($data){
  //   	$validate = new Validate($this->rule, $this->message);
		// $result = $validate->check($data);
		if( empty($data['name'])){
			return return_format('',40099);
		}else{
				$alldata = [
					'name' => $data['name'] ,
				];
				$logflag = Db::table($this->table)->insert($alldata);
                if($logflag){
                    return return_format('',0);
                }else{
				    return return_format('',40101);
                }
		}
    }
	
    /**
     * [updateCategory 更新学生分类数据]
     * @Author lc
     * @DateTime 2018-04-19T20:47:03+0800
     * @param    [array]     $data [提交的数据]
     */
    public function updateCategory($data){
    	$validate = new Validate($this->rule, $this->message);
		if(!$validate->check($data)){
			return return_format('',40110,$validate->getError());
		}else{
            if($data['id']>0){
				if(!$this->checkCategoryExsit($data['id'])) return return_format('',70005);
                $where = ['id'=>$data['id']];
                $allaccountdata  = [
                    'name'=> $data['name'],
                ] ;

                Db::table($this->table)->where($where)->update($allaccountdata) ;
				return return_format('',0);
			}else{
				return return_format('',70004);
			}
		}
    }
	
    /**
     * [switchCategoryStatus 修改学生分类的可用状态]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]       $id [学生分类id]
     * @param    [int]       $dataflag  [要修改的标记状态]
     * @return   [array]                [返回数组]
     */
    public function switchCategoryStatus($id,$status){
		if(!$this->checkCategoryExsit($id)) return return_format('',70005);
        $data['status']= $status ;
        $where = ['id'=>$id] ;

         Db::table($this->table)->where($where)->update($data) ;
         return return_format('',0);
    }
	
    /**
     * [delTeacher 删除学生分类信息]
     * @Author lc
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $id [学生分类id]
     * @return   [type]               [description]
     */
    public function delCategory($id){
		//开启事务
		Db::startTrans();
    	$flag1 = Db::table($this->table)->where(['id'=>$id])->update(['delflag'=>1, 'status'=>0]);
		if(!$flag1){
			Db::rollback();
			return return_format('',40101);
		}
		
		//删除该分类下学生的分类信息
		$studentModle = new Studentinfo;
		if(!empty($studentModle->getStudentBycategoryid($id))){
			$flag2 = Db::table('nm_studentinfo')->where(['categoryid'=>$id])->update(['categoryid'=>'']);
			if(!$flag2){
				Db::rollback();
				return return_format('',40101);
			}
		}
		
		//删除该分类下的知识
		$knowledgeModle = new Knowledge;
		if(!empty($knowledgeModle->checkKnowledgeByStuCate($id))){
			$flag3 = $knowledgeModle->updateKnowledgesByStuCate($id);
			if(!$flag3){
				Db::rollback();
				return return_format('',40101);
			}
		}
		
		//处理完成 提交事务
		Db::commit();
		return array('code'=>0,'info'=>lang('success'));
    }
	
	/**
	 * checkCategoryExsit 检查分类是否存在
	 * @param category id
	 * @return [bool]
	 */
	public function checkCategoryExsit($id){
		$result = Db::table($this->table)
        ->where('id',$id)
        ->field('id')->find();
		return !empty($result) ? true : false;
	}
	
	/**
	 * 获取所有类型
	 * 
	 */
	public function getAllCategoryList()
    {
        return Db::table($this->table)->where(['delflag' => 0])->field('id,name')->order('id asc')->select();
    }
	
	/**
	 * 根据name获取类型数据
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getFieldByName($name, $field)
    {
        return Db::table($this->table)
        ->where('name',$name)
		->where('delflag', 0)
        ->field($field)->find();
    }
}
