<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use app\official\model\Officialuseroperate;
class Organauditbill extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organauditbill';

	
	 // 课程添加验证规则
    protected $rule = [
        'name'  => 'require|max:50',
        'info'  => 'require|max:200',
        'logo' => 'require|max:500',
        'indate' => 'require|number',
        'price' => 'require|number',
    ];

    protected $message  = [];
    protected function initialize() {
        parent::initialize();
        $this->message = [
            'name.require' => lang('50277'),
            'name.max' => lang('50278'),
            'info.require' => lang('50279'),
            'info.max' => lang('50280'),
            'logo.require' => lang('50281'),
            'logo.max' => lang('50282'),
            'indate.require' => lang('50283'),
            'indate.max' => lang('50284'),
            'price.require' => lang('50285'),
            'price.max' => lang('50286'),
        ];
    }

    /**
     * [getOrganAuditBillById 获取套餐详情]
     * @Author zzq
     * @DateTime 2018-04-25T10:20:43+0800
     * @param    [int]                   $organid   [套餐id]
     * @return   [array]                              [description]
     */
    public function getOrganAuditBillById($id){
        
        $field = 'id,name,info,logo,indate,price,status,ontrial' ;
        try{
            $res = Db::table($this->table)
            ->field($field)
            ->where('id','EQ',$id)
            ->find();
            if($res){
                return return_format($res,0,lang('success'));
            }else{
                return return_format('',50054,lang('50054')) ;
            }
             
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
    /**
     * [addOrganAuditBill 添加套餐]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function addOrganAuditBill($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format($validate->getError(),50010,lang('50010')) ;
        }

        //构造入库数据
        $insertdata = [
                    'name'    => $data['name'],
                    'logo' => $data['logo'],
                    'info' => $data['info'],
                    'indate' => $data['indate'],
                    'price' => $data['price'],
                    'addtime'   => time(),
                    'ontrial' => $data['ontrial']
                ] ;
        try{
            $flag = $this->insert($insertdata);
            //添加操作日志
            //$obj = new Officialuseroperate();
            //$obj->addOperateRecord('添加了套餐'); 
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50055,lang('50055')) ;
        }

    }
    /**
     * [editOrganAuditBillg 更新套餐]
     * @Author zzq
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function editOrganAuditBill($data){

        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',50010,$validate->getError()) ;
        }
        //构造入库数据
        $insertdata = [
                    'name'    => $data['name'],
                    'logo' => $data['logo'],
                    'info' => $data['info'],
                    'indate' => $data['indate'],
                    'price' => $data['price'],
                    'ontrial' => $data['ontrial']
                ] ;
        
        try{
            $flag = $this->save($insertdata,['id'=>$data['id']]);
            //添加操作日志
            //$obj = new Officialuseroperate();
            //$obj->addOperateRecord('编辑了套餐'); 
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50056,lang('50056')) ;
        }     
    }
	/**
	 * [delOrganAuditBill] 
	 * @Author zzq
	 * @param    [int]        $organid   查询参数
	 * @return   [array]            [description]
	 */
	public function delOrganAuditBill($id){
        $where = [
                'id'      => $id,
            ] ;

        try{
            $flag = Db::table($this->table)
                ->where($where)
                ->delete();
            if($flag){
                //添加操作日志
                //$obj = new Officialuseroperate();
                //$obj->addOperateRecord('删除了套餐'); 
                return return_format('',0,lang('success')) ;
            }else{
                return return_format('',50057,lang('50057')) ;
            }
        }catch(\Exception $e){
            return return_format($e->getMessage(),50057,lang('50057')) ;
        }

	}

    /**
     * [updateOrganAuditBillStatusById] 
     * @Author zzq
     * @param    [int]        $organid   查询参数
     * @return   [array]            [description]
     */
    public function updateOrganAuditBillStatusById($data){
        // var_dump($data);
        // die;
        $result = $this->getOrganAuditBillById($data['id']);
        if($result['code'] != 0){
            return return_format('',50054,lang('50054')) ;
        }
        $insertdata = [
            'status'=>$data['status']
        ];
        try{
            $flag = $this->save($insertdata,['id'=>$data['id']]);
            return return_format('',0,lang('success')) ;
        }catch(\Exception $e){
            return return_format($e->getMessage(),50058,lang('50058')) ;
        }

    }
}
