<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;
use app\admin\model\Coursetagrelation;
use app\admin\model\Unit;
use app\admin\model\Period;

/*
 * 课程Model
 * @ jcr
*/
class Curriculum extends Model{

    protected $table = 'curriculum';
    protected $pagenum; //每页显示行数
    protected $field = true;

	// 课程添加验证规则
	public $rule = [
		'classtypes' => 'require',
		'categorystr' => 'require',
		'coursename'  => 'require|max:20',
		'subhead' => 'require|max:100',
		'imageurl' => 'require',
		'generalize' => 'require',
	];



	// 课程添加验证规则
	public $rule1 = [
		'price' => 'require|number',
	];
	public $message = [];
	public $message1 = [];


    //自定义初始化
    protected function initialize(){
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
        $this->message = [
			'classtypes.require' => lang('10506'),
			'categorystr.require' => lang('10514'),
			'coursename.require' => lang('10515'),
			'coursename.max'     => lang('10516'),
			'subhead.require' => lang('10517'),
			'subhead.max'     => lang('10518'),
			'imageurl.require' => lang('10519'),
			'generalize.require' => lang('10520')
		];

        $this->message1 = [
			'price.require' => lang('10504'),
			'price.number' => lang('10505'),
		];
    }


    /**
     * getId 根据课程id 查询课程详情
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
     * getId 基础查询机构课程
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @param $limit 查询页数
     * @param $pagenum 一页几条
     * @param $findIn 兼容开班类型 1是一对一 2是小课班 3是大课班 find_in_set 查询
     * @return array();
     */
     public function getCurriculumList($where,$field,$orderbys='',$limit = 1,$pagenum){
		 $lists = Db::name($this->table)->page($limit,$pagenum)->order($orderbys)->where($where)->field($field)->select();
         return $lists;
     }



    /**
     * getId 查询机构课程列表总行数
     * @ jcr
     * @param $where 查询条件
     * @param $field 查询内容 默认不传全部
     * @return int;
     */
    public function getCurriculumCount($where){
        $counts = Db::name($this->table)->where($where)->count();
        return $counts;
    }
    /**
     * getId 查询机构课程列表总行数
     * @ jcr
     * @param $status 1 已上架 0 未上架
     * @param $field 查询内容 默认不传全部
     * @return int;
     */
    public function getStatusNum($status = 1){
        $counts = Db::name($this->table)
                        ->where('status','eq',$status)
                        ->where('delflag','eq',1)
                        ->count();
        return $counts;
    }


    /**
     * 返回详情只定的字段
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getSelectId($id){
        return $this->getId($id,'id,coursename,addtime,subhead,imageurl,price,status,generalize,categoryid,classtypes,categorystr,periodnum,schedule,delflag,classnum,maxprice,teacherid,giftstatus,giftjson');
    }


    /**
    * getId 查询机构课程列表 数据组装
    * @ jcr
    * @param $where 查询条件
    * @return array();
    */
    public function getAdminCurriculumList($data,$pagenum){
        if (!$data) $data = [];
        // 过滤数组中的空值 和 没定义的字段
        
//        $findin = isset($data['classtypes'])?' FIND_IN_SET('.$data['classtypes'].',classtypes) ':'';

        $where = where_filter($data,array('status','coursename','classtypes'));
        if(isset($where['coursename'])){
			$where['coursename'] = ['like','%'.$where['coursename'].'%'];
		}

        $field = 'id,imageurl,coursename,price,status,categoryid,categorystr,periodnum,classtypes,addtime';
        $where['delflag'] = 1; //未删除数据


        // 查询列表
        $indata['data'] = $this->getCurriculumList($where,$field,'addtime desc',$data['limit'],$pagenum);

        // 列表对应总行数
        $indata['pageinfo'] = array('pagesize'=>$pagenum,'pagenum'=>$data['limit'],'total'=>count($indata['data'])?$this->getCurriculumCount($where):0);
        return $indata;
    }

    /**
     * 课程添加
     * @ jcr
     * @param $data 添加数据源
     */
    public function addTables($data){

        // 取出分类id
        if(!isset($data['categoryid'])){
            $cate = explode(',',$data['categorystr']);
            $data['categoryid'] = $cate[count($cate)-1];
        }

        $arrcount = 0;
        foreach ($data['inperiod'] as $key => $value) {
            $arrcount += count($value['list']);
        }

        //开启事务
        Db::name($this->table)->startTrans();

        $tabledata = where_filter($data,array('coursename','subhead','imageurl','price','status','generalize','categoryid','classtypes','categorystr'));
        $tabledata['addtime'] = time();
        $tabledata['periodnum'] = $arrcount;
        $id = Db::name($this->table)->insertGetId($tabledata);
        if(!$id){
            Db::name($this->table)->rollback();
            return array('code'=>500,'info'=>'添加失败');
        }

        //继续走 写入课程标签
        //处理 标签批量插入的数据结构
        foreach ($data['labellist'] as $k => $v){
            $coursetagrelation[] = array('courseid'=>$id,'tagid'=>$v);
        }
        $coursetagrelationew = new Coursetagrelation();
        if(!$coursetagrelationew->addAll($coursetagrelation)){
            //插入课程标签失败 回滚
            Db::name($this->table)->rollback();
            return array('code'=>500,'info'=>'添加失败');
        }
        
        //继续走 添加课时单元
        $period = new Period();
        $unit = new Unit();
        foreach($data['inperiod'] as $k => $v){
            $v['curriculumid'] = $id;
            $unitinfo = $unit->addEdit(array_filter($v));
            if($unitinfo['code']==500){
                Db::name($this->table)->rollback();
                return array('code'=>500,'info'=>'添加失败-课时单元出异常');
                break;
            }

            //处理课时数据结构
            foreach ($v['list'] as $key => $val) {
                $val['curriculumid'] = $id;
                $val['unitid'] = $unitinfo['info'];
                $v['list'][$key] = array_filter($val);
            }

            $periodinfo = $period->addEdit($v['list'],'all');
            if($periodinfo['code']==500){
                Db::name($this->table)->rollback();
                return array('code'=>500,'info'=>'添加失败-课时添加异常');
                break;
            }
        }

        //处理完成 提交事务
        Db::name($this->table)->commit();
        return array('code'=>0,'info'=>'添加成功');        
    }


    /**
     * 课程添加 第一步
     * @ jcr
     * @param $data 添加数据源
     */
    public function addOne($data,$info){

        $coursetagrelation = new Coursetagrelation();
        if(isset($data['labellist'])&&$data['labellist']){
            // 标签
            $data['labellist'] = explode('-',$data['labellist']);
            sort($data['labellist']);
            $data['tagids'] = $data['labellist']?implode(',',$data['labellist']):'';
        }

        //if(!isset($data['categoryid'])){
		$cate = explode('-',$data['categorystr']);
		$data['categoryid'] = $cate[count($cate)-1];
        //}

        //开启事务
        Db::startTrans();
        $tabledata = where_filter($data,array('id','coursename','subhead','imageurl','generalize','categoryid','classtypes','categorystr','teacherid'));
        if(isset($tabledata['id'])){
            //编辑
            //判断是否有改动
            $id = $data['id'];
            $lines = $this->thanArr($tabledata,$info);

            if(isset($data['labellist'])&&$data['labellist']){
                // 获取原有课程标签            
                $courlist = $coursetagrelation->getArrListId($info['id']);            
                $courlist = array_column($courlist,'tagid');
                // 我要添加的标签
                $addArrs = array_diff($data['labellist'],$courlist);
                // 我要删除的标签
                $deleArrs = array_diff($courlist,$data['labellist']);
            }else{
                $addArrs = [];
                $deleArrs = [];
            }
            
            if(!$lines){
            	// 没有任何修改 直接返回成功 不做其他操作
                Db::rollback();
                return array('code'=>0,'info'=> lang('success'));
            }

            if($lines){
                if(!Db::name($this->table)->where('id','eq',$info['id'])->update($tabledata)){
                    Db::rollback();
                    return array('code'=>10004,'info'=> lang('error'));
                }
            }

            if($addArrs){
                // 添加标签                
                foreach ($addArrs as $key => $value) {
                    $addlist[$key]['tagid'] = $value;
                    $addlist[$key]['courseid'] = $info['id'];
                }
                if(!$coursetagrelation->addAll($addlist)){
                    //插入课程标签失败 回滚
                    Db::rollback();
                    return array('code'=>10005,'info'=> lang('error'));
                }
            }

            if($deleArrs){
                // 删除标签
                $where = where_or($deleArrs,'tagid','eq');
                $where['courseid'] = $info['id'];
                if(!$coursetagrelation->deleteIds($where)){
                    Db::rollback();
                    return array('code'=>10006,'info'=> lang('error'));
                }
            }
            $id = $info['id'];
        }else{
            // 添加模块
			$maxid = Db::name($this->table)->max('id');
			$maxid = $maxid?$maxid:0;
			$tabledata['currsort'] = $maxid+1;
            $tabledata['addtime'] = time();
            $id = Db::name($this->table)->insertGetId($tabledata);
            if(!$id){
                Db::rollback();
                return array('code'=>10007,'info'=> lang('error'));
            }

            //继续走 写入课程标签
            if(isset($data['labellist'])&&$data['labellist']){
                //处理 标签批量插入的数据结构
                foreach ($data['labellist'] as $k => $v){
                    $coursetagrela[] = array('courseid'=>$id,'tagid'=>$v);
                }

                if(!$coursetagrelation->addAll($coursetagrela)){
                    //插入课程标签失败 回滚
                    Db::rollback();
                    return array('code'=>10008,'info'=> lang('error'));
                }
            }
            
        }
        //处理完成 提交事务
        Db::commit();
        return array('code'=>0,'id'=>$id,'info'=>lang('success'));
    }


    /**
     * 课程添加 第二步
     * @ jcr
     * @param $data 添加数据源
     */
    public function addTwo($data,$info){

        $arrcount = 0;
        foreach ($data['inperiod'] as $key => $value) {
            $arrcount += count($value['list']);
        }
        
        //开启事务
        Db::startTrans();
        $tabledata = where_filter($data,array('coursename','subhead','imageurl','price','status','generalize','categoryid','classtypes','categorystr'));
        $tabledata['periodnum'] = $arrcount;

        $period = new Period();
        $unit = new Unit();

        if($info['schedule']==0){
            //添加 添加课时单元

			$tabledata['schedule'] = 1;

            foreach($data['inperiod'] as $k => $v){
                $v['curriculumid'] = $info['id'];
                $unitinfo = $unit->addEdit(array_filter($v));
                if($unitinfo['code']!=0){
                    Db::rollback();
                    return array('code'=>10014,'info'=>lang('error'));
                    break;
                }

                //处理课时数据结构
                foreach ($v['list'] as $key => $val) {
                    $val['curriculumid'] = $info['id'];
                    $val['unitid'] = $unitinfo['info'];
                    $val = where_filter(array_filter($val),array('id','periodname','periodsort','courseware','curriculumid','unitid'));
					$val['courseware'] = isset($val['courseware'])?$val['courseware']:'';
					ksort($val);
                    $v['list'][$key] = $val;
                }

                $periodinfo = $period->addEdit($v['list'],'all');
                if($periodinfo['code']!=0){
                    Db::rollback();
                    return array('code'=>10015,'info'=>lang('error'));
                    break;
                }
            }


            $adds = Db::name($this->table)->where('id','eq',$info['id'])->update($tabledata);
            if(!$adds){
                Db::rollback();
                return array('code'=>10016,'info'=>lang('error'));
            }
        }else{
            //编辑
            $intypes = false; //是否编辑状态
            $inperiodarr = $unit->getLists($info['id']);
            $perList = $period->getIdsLists($info['id']);

            // 处理数组结构
            $inperiodkeyarr = arr_key_value($inperiodarr,'id');            
            $perkeyarr = arr_key_value($perList,'id');

            // 删除单元 比较提交数据单元id 和 数据库取出数据单元id 差集 返回 要删除的单元 id 数组
            $deleteunit = array_diff(array_column($inperiodarr,'id'),array_filter(array_column($data['inperiod'],'id')));

            // 要删除的课时
            $deleteperiod = [];
            //新增单元 包括下级
            $arrA = []; 
            // 要新增的课时
            $addperiod = [];

            // 处理涉及到的所有情况
            foreach ($data['inperiod'] as $key => $value) {
                if($value['id']==''){
                    //不存在id 新增的单元
                    $arrA[] = $value;
                    unset($data['inperiod'][$key]);
                }else{
                    //还存在的课程单元 1、编辑 2、不变
                    if($value['unitname']!=$inperiodkeyarr[$value['id']]['unitname'] || 
                        $value['unitsort']!=$inperiodkeyarr[$value['id']]['unitsort']){                        
                        $unitinfo = $unit->addEdit(array_filter($value));
                        if($unitinfo['code']!=0){
                            Db::rollback();
                            return array('code'=>10017,'info'=>lang('error'));
                            break;
                        }
                        $intypes = true; // 发生变动
                    }

                    // 查看是否有被删除的课时 对比上传数组和库查询数组的差集 
                    $periodbad = array_diff($this->getPeriodIdArr($perList,$value['id']),array_filter(array_column($value['list'],'id')));

                    if($periodbad) $deleteperiod = array_merge($deleteperiod,$periodbad);

                    foreach ($value['list'] as $k => $v) {
                        if($v['id']==''){
                            //新增课时
                            $v['curriculumid'] = $info['id'];
                            $v['unitid'] = $value['id'];
                            $v = where_filter(array_filter($v),array('id','periodname','periodsort','courseware','curriculumid','unitid'));
							$v['courseware'] = isset($v['courseware'])?$v['courseware']:'';
							ksort($v);
                            $addperiod[] = $v;
                        }else{
                            // 编辑课时
                            if($v['periodname']!=$perkeyarr[$v['id']]['periodname']||
                                $v['periodsort']!=$perkeyarr[$v['id']]['periodsort']||
                                $v['courseware']!=$perkeyarr[$v['id']]['courseware']){

                                $periodinfo = $period->addEdit($v);
                                if($periodinfo['code']!=0){
                                    Db::rollback();
                                    return array('code'=>10018,'info'=>lang('error'));
                                    break;
                                }
                                $intypes = true; // 发生变动
                            }
                        }
                    }
                }
            }

            // 添加课程单元
            if($arrA){
                foreach($arrA as $k => $v){
                    $v['curriculumid'] = $info['id'];
                    $unitinfo = $unit->addEdit(array_filter($v));
                    if($unitinfo['code']!=0){
                        Db::rollback();
                        return array('code'=>10019,'info'=>lang('error'));
                        break;
                    }
                    //处理课时数据结构 将其丢入新增课时统一处理
                    foreach ($v['list'] as $key => $val) {
                        $val['curriculumid'] = $info['id'];
                        $val['unitid'] = $unitinfo['info'];
                        $val = where_filter(array_filter($val),array('id','periodname','periodsort','courseware','curriculumid','unitid'));
                        $val['courseware'] = isset($val['courseware'])?$val['courseware']:'';
						ksort($val);
                        $addperiod[] = $val;
                    }
                }
                $intypes = true; // 发生变动
            }

            //新增课时
            if($addperiod){
                $periodinfo = $period->addEdit($addperiod,'all');
                if($periodinfo['code']!=0){
                    Db::rollback();
                    return array('code'=>10020,'info'=>lang('error'));
                }
                $intypes = true; // 发生变动
            }

            // 删除单元
            if($deleteunit){
                //单元删除了，这是要移除的课时
                foreach ($deleteunit as $key => $value) {
                    $deleteperiod = array_merge($deleteperiod,$this->getPeriodIdArr($perList,$value));
                }

                $where = where_or($deleteunit,'id','eq');
                $unitcount = $unit->deleteIds($where);
                if(!$unitcount){
                    Db::rollback();
                    return array('code'=>10021,'info'=>lang('error'));
                }
                $intypes = true; // 发生变动
            }

            // 删除课时
            if($deleteperiod){
                $where = where_or($deleteperiod,'id','eq');
                $periodcount = $period->deleteIds($where);
                if(!$periodcount){
                    Db::rollback();
                    return array('code'=>10022,'info'=>lang('error'));
                }
                $intypes = true; // 发生变动
            }

            if(!$intypes){
                Db::rollback();
                return array('code'=>0,'info'=>lang('success'));
            }

            //课时数量发生变动
            if($arrcount!= $info['periodnum']){
                $tabledata['periodnum'] = $arrcount;
                $adds = Db::name($this->table)->where('id','eq',$info['id'])->update($tabledata);
                if(!$adds){
                    Db::rollback();
                    return array('code'=>10023,'info'=>lang('error'));
                }
            }
        }

        //处理完成 提交事务
        Db::commit();
        return array('code'=>0,'info'=>lang('success'));
    }


    /**
     * 课程添加编辑 第三步
     * @ jcr
     * @param $data 添加数据源
     */
    public function addTri($data,$info){
        $tabledata = where_filter($data,array('price','status','delflag','giftstatus'));

        if($info['schedule']==1){
            //添加
			$tabledata['giftjson'] = $data['giftjson'];
            $tabledata['schedule'] = 2; 
        }else{
            //判断是否有改动
			if(isset($data['giftstatus'])){
				$tabledata['giftjson'] = $data['giftjson'];
			}
            if(!$this->thanArr($tabledata,$info)){
                return array('code'=>0,'info'=>lang('success'));
            }
        }

        $updetes = Db::name($this->table)->where('id','eq',$info['id'])->update($tabledata);
        if(!$updetes){
            return array('code'=>10027,'info'=>lang('error'));
        }
        return array('code'=>0,'info'=>lang('success'));
    }





    /**
     * 课程 启用 禁用 删除
     * @ jcr
     * @param $data 添加数据源
     */
    public function eidtOperate($data){
        $tabledata = where_filter($data,array('status','delflag'));
        $updetes = Db::name($this->table)->where('id','eq',$data['id'])
//										 ->where('schedule','eq',2)
			   							 ->update($tabledata);
        if(!$updetes){
            return array('code'=>10030,'info'=>lang('error'));
        }
        return array('code'=>0,'info'=>lang('success'));
    }


	/**
	 * 修改价格
	 * @param $id
	 * @param $data
	 * @return $data
	 */
    public function edit($id,$data){
		return Db::name($this->table)->where('id',$id)->update($data);
	}

    /**
     * 对比字段修改
     * @param  [type] $data  [description]
     * @param  [type] $inarr [description]
     * @return [type]        [description]
     */
    function thanArr($data,$inarr){
        $arr = [];
        foreach ($data as $key => $value) {
            if($data[$key] != $inarr[$key] ){
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    /**
     * 根据课程单元id 获取对应的课时id
     * $data 包含所有课程单元的课时
     * $id 课程单元id
     */
    function getPeriodIdArr($data,$id){
        $arr = [];
        foreach ($data as $key => $val) {
            if($id==$val['unitid']){
                $arr[] = $val['id'];
            }
        }
        return $arr;
    }


	/**
	 * 拷贝数据转换对应的分类ID对应值
	 * @param $catelist
	 * @param $str
	 * @return array()
	 */
    function getCartStr($catelist,$str){
		$arr = array_filter(explode('-',$str));
		$inarr = [];
		foreach ($arr as $key => $val){
			$inarr[] = $catelist[$val];
		}
		return implode(',',$inarr);
	}

    /**
     *  @author wyx
     *  @param  $data array 课程id数组
     *
     *
     */
    public function getCurriculumImageById($data){
        return Db::name($this->table)
        ->where('id','IN',$data)
        ->column('id,imageurl');
    }

    /**
     *  @author lc
     *  @param  $where
     *
     *
     */
    public function getAllCurriculumByName($where){
        return Db::name($this->table)
        ->where($where)
        ->field('id,coursename')
		->select();
    }

	/**
	 * 根据name获取课程数据
	 * @Author lc
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array                [description]
	 */
    public function getFieldByName($name, $field)
    {
        return Db::name($this->table)
        ->where('coursename',$name)
		->where('status', 1)
		->where('delflag', 1)
        ->field($field)->find();
    }

}
