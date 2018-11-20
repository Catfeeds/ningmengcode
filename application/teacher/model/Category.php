<?php
namespace app\teacher\model;
use think\Model;
use think\Db;;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 分类Model
 * @ jcr
*/
class Category extends Model{

    protected $table = 'nm_category';
    protected $organid;
    protected $pagenum; //每页显示行数

    // 分类添加验证规则
    protected $rule = [
        'categoryname'  => 'require|max:20'];
    protected $message = [];
      //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message=[
            'categoryname.require' => lang('23009'),
            'categoryname.max'     => lang('23010')
        ];     
    }
    public function __construct(){
        $this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
    }

    /**
     * getId 根据分类id 查询分类详情
     * @ jcr
     * @param $id 分类id
     * @param $field 查询内容 默认不传全部
     * @return array();
    */
	 public function getId($id,$field){
	     if (!$id) return false;
	     return Db::table($this->table)->where(array('id'=>$id))->find();
     }

    /**
    * getId 查询分类列表
    * @ jcr
    * @param $where 查询条件
    * @param $field 查询内容 默认不传全部
    * @param $limit 查询页数
    * @param $pagenum 一页几条
    * @return array();
   */

    public function getCategoryList($where,$field,$orderbys = '',$limit = 1,$pagenum){
        if (!$where) $where = [];
        //$where['organid'] = $this->organid;
        $pagenum = $pagenum?$pagenum:$this->pagenum;
        $lists = Db::table($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->field($field)->select();
        return $lists;
    }


    /**
     * 课程列表分类处理
     * @jcr
     */
    public function getCategoryName($data){
        // 遍历生成对应的或关系查询
        foreach ($data as $k => $v){
            $arrs[] = array('eq',$v);
        }
        $arrs[] = 'or';
        $where['id'] = $arrs;
        $lists = Db::table($this->table)->where($where)->field('id,categoryname,rank')->order('rank asc')->limit(6)->select();
        return $lists?implode('/',array_column($lists,'categoryname')):'-';
    }

    /**
     * getId 查询移动上下项
     * @ jcr
     * @param $data['operate'] 分类操作 0上移 1下移
     * @param $data['rank'] 分类操作 级别
     * @param $data['sort'] 分类操作 当前排序值
     */
    public function getCategoryListSort($data){
        if (!$data) $data = [];
        // 处理查询条件
        $where = where_filter($data,array('rank','sort'));
        $where['organid'] = $this->organid;
        $where['sort'] = $data['operate']?array('elt',$data['sort']):array('egt',$data['sort']);
        // 处理排序规则
        $order = $data['operate']?'sort desc':'sort asc';
        $lists = Db::table($this->table)->where($where)->field('id,sort')->order($order)->limit(2)->select();
        return $lists;
    }



    /**
    * getId 查询机构分类列表总行数
    * @ jcr
    * @param $where 查询条件
    * @param $field 查询内容 默认不传全部
    * @return int;
   */
    public function getCategoryCount($where){
        if (!$where) $where = [];
        $counts = Db::table($this->table)->where($where)->count();
        return $counts;
    }

     /**
     * 分类编辑/添加
     * @ jcr
     * @ data 添加数据源
     * @ $affairs 添加回调开启事务更新排序值
     */
     public function editAdd($data,$affairs=false){
         $validate = new Validate($this->rule, $this->message);
         if(!isset($data['id'])||(isset($data['categoryname'])&&isset($data['id']))){
             // 添加时验证 和 编辑类名时验证
             if(!$validate->check($data)){
                 return array('code'=>500,'info'=>$validate->getError());
             }
         }

         if(isset($data['id'])){
             // 允许传输的编辑字段
             $data = where_filter($data,array('id','status','sort','delflag','categoryname'));
             $info = Db::table($this->table)->where(['id'=>$data['id'],'organid'=>$this->organid])->update($data);
             if($info&&$affairs){
                 return true;
             }else if($affairs){
                 return false;
             }
         }else{
             //添加模块
             $data['addtime'] = time();
             $data = where_filter($data,array('id','status','sort','delflag','categoryname','rank','fatherid','path'));
             //开启事务
             Db::table($this->table)->startTrans();
             $info = Db::table($this->table)->insertGetId($data);
             if($info){
                 //回调 更新sort 同步主键
                 $editstatus = $this->editAdd(['id'=>$info,'sort'=>$info],true);
                 //var_dump($editstatus);
                 if($editstatus){
                     Db::table($this->table)->commit();
                 }else{
                     Db::table($this->table)->rollback();
                     return array('code'=>500,'info'=>'添加失败');
                 }
             }
         }
         return array('code'=>0,'info'=>isset($data['id'])?'修改成功':'添加成功');
     }

     /**
     * 特殊需求，上下移动
     * @ jcr
     * @ data 修改数据源
     **/
    public function editSoct($data){
        // 开启事务 交换两个id 对应的排序值
        Db::table($this->table)->startTrans();
        $onein = $this->editAdd($data[0]);
        $twoin = $this->editAdd($data[1]);
        if($onein&&$twoin){
            Db::table($this->table)->commit();
            return true;
        }else{
            Db::table($this->table)->rollback();
            return false;
        }
    }

    /**
     * [getCategory 获取分类列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @return   array
     */
    public function getCategory($organid){
        $lists = Db::table($this->table)
            ->field('id as category_id,categoryname,rank,fatherid,sort')
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->where('organid','eq',$organid)
            ->order('rank,sort asc')
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return $lists;
    }
    /**
     * [getChildList 获取下级分类list]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @return   array
     */
    public function getChildList($organid,$category_id){
        $lists = Db::table($this->table)
            ->field('id as category_id ,categoryname,rank,fatherid,sort')
            ->where('fatherid','eq', $category_id)
            ->where('delflag','eq','1')
            ->where('status','eq','1')
            ->where('organid','eq',$organid)
            ->order('sort asc')
            ->select();
        return $lists;
    }


    /**
     * [get_parent_id 递归查询下级id]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @return   array
     */
    function get_category($cid){
        $category_ids = $cid.",";
        $ids =  Db::table($this->table)->where('fatherid','eq',$cid)->field('id')->select();
        foreach( $ids as $key => $val )
            $category_ids .= $this->get_category( $val['id'] );
        return $category_ids;
    }










}
