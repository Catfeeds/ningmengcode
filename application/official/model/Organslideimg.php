<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use app\official\model\Officialuseroperate;
class Organslideimg extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organslideimg';

	
	 // 课程添加验证规则
    protected $rule = [
        'remark'  => 'require',
        'imagepath'  => 'require',
    ];
    protected $message  = [];
    protected function initialize() {
        parent::initialize();
        $this->message = [
            'remark.require' => lang('50275'),
            'imagepath.require' => lang('50276'),
        ];
    }
    /**
     * [getOrganSlideImgList 获取官方机构默认广告列表]
     * @Author zzq
     * @DateTime 
     * @param    没有参数
     * @return   [array]                              [description]
     */
    public function getOrganSlideImgList(){
        
        $field = 'id,remark,imagepath,sortid' ;
        try{
            $res = Db::table($this->table)
            ->field($field)
            ->where('organid','EQ',0)
            ->select() ;
            return return_format($res,0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }


    /**
     * [getOrganSlideImgById 获取轮播详情]
     * @Author zzq
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $organid   [广告id]
     * @return   [array]                              [description]
     */
    public function getOrganSlideImgById($id){
        
        $field = 'id,remark,imagepath,sortid,addtime' ;
        try{
            $res = Db::table($this->table)
            ->field($field)
            ->where('id','EQ',$id)
            ->where('organid','EQ',0)
            ->find();
            if($res){
                return return_format($res,0,lang('success'));
            }else{
                return return_format('',50088,lang('50088')) ;
            }
             
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
    /**
     * [addSlideImage 添加广告]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function addOrganSlideImg($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',50010,$validate->getError()) ;
        }

        //构造入库数据
        $insertdata = [
                    'remark'    => $data['remark'],
                    'imagepath' => $data['imagepath'],
                    'addtime'   => time(),
                    'sortid'    => $data['sortnum'],
                    'organid'   => 0,
                ] ;
        try{
            $flag = $this->insert($insertdata);
            //添加操作日志
            $obj = new Officialuseroperate();
            $obj->addOperateRecord('添加了广告'); 
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

    }
    /**
     * [editOrganSlideImg 更新广告]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function editOrganSlideImg($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',-1,$validate->getError()) ;
        }
        //构造入库数据
        $insertdata = [
                    'remark'    => $data['remark'],
                    'imagepath' => $data['imagepath'],
                ] ;
        
        try{
            $flag = $this->save($insertdata,['id'=>$data['id']]);
            //添加操作日志
            $obj = new Officialuseroperate();
            $obj->addOperateRecord('编辑了广告'); 
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
	/**
	 * [delOrganSlideImg] 
	 * @Author zzq
	 * @param    [int]        $organid   查询参数
	 * @return   [array]            [description]
	 */
	public function delOrganSlideImg($id){
        $where = [
                'organid' => 0,
                'id'      => $id,
            ] ;

        try{
            $flag = Db::table($this->table)
                ->where($where)
                ->delete();
            if($flag){
                //添加操作日志
                $obj = new Officialuseroperate();
                $obj->addOperateRecord('删除了广告'); 
                return return_format('',0,lang('success')) ;
            }else{
                return return_format('',50004,lang('50004')) ;
            }
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

	}

	
    //添加机构后，自动导入广告的轮播图
    public function afterAddOrganChangeSlideImg($organid){
        //如果官网的轮播图不全，就执行，返回false

        //获取
        $res = $this->getOrganSlideImgList();
        if($res['data']){
            $count = count($res['data']);
        }else{
            return false;
        }
        if($count < 5){
            return false;
        }
        //否则导入图片
        $data = $res['data'];
        $ret = [];
        foreach($data as $k => $v){
            $ret[$k]['remark'] = $v['remark']; 
            $ret[$k]['imagepath'] = $v['imagepath']; 
            $ret[$k]['sortid'] = $v['sortid'];
            $ret[$k]['addtime'] = time(); 
            $ret[$k]['organid'] = $organid;  
        }
        //循环插入
        $this->saveAll($ret);

    }
}
