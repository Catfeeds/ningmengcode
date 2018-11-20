<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use login\Particle;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 机构余额表
 * @ jcr
*/
class Withdraw extends Model{

    protected $table = 'nm_withdraw';
    protected $pagenum; //每页显示行数
    // 课程添加验证规则
    public $rule = [
        'price'        => 'require|number',
        'paytype'      => 'require',
        'cashaccount'  => 'require',

    ];

    public $message = [];

	//自定义初始化
	protected function initialize(){
		parent::initialize();
		$this->pagenum = config('paginate.list_rows');
		$this->message = [
			'price.require'   => lang('10527'),
			'price.number'    => lang('10528'),
			'paytype.require' => lang('10529'),
			'cashaccount.require' => lang('10530'),
		];
	}


    /**
     * [addEdit 添加编辑提现申请]
     * @param [type] $data [description]
     */
    public function addEdit($data){

        $data = where_filter($data,array('id','price','addtime','endtime','reasons','paytype','paystatus','cashaccount'));
        // var_dump($data);
        if(isset($data['id'])){
            //编辑
        }else{
            //添加
            DB::startTrans();
            $data['addtime'] = time();
            $data['paystatus'] = 0;
			// $data['withsn'] = implode('',explode('-',date('Y-m-d-H-i-s',time()))).rand(100000,999999);
			$data['withsn'] = Particle::generateParticle();
            $ids = Db::table($this->table)->insertGetId($data);
            if(!$ids){
                DB::rollback();
                return array('code'=>10089,'info'=>lang('error'));
            }

            $organ = new Organaccount();
            $organinfo = $organ->setWithdraw(0,$data['price']);
            if(!$organinfo){
                DB::rollback();
                return array('code'=>10090,'info'=>lang('error'));
            }

            Db::name($this->table)->commit();
            return array('code'=>0,'info'=>lang('success'));
        }
    }


    
    /**
     * [getList 获取对应的]
     * @param  [type] $data    [筛选条件]
     * @param  [type] $pagenum [第几页]
     * @param  [type] $limit   [一页几条]
     * @return [type]          [description]
     */
    public function getList($data,$pagenum,$limit){
        $where = where_filter($data,array('paystatus'));
        $field = 'id,price,addtime,paystatus,cashaccount,endtime,paytype,withsn,reasons';
        $order = $data['paystatus']==0?'addtime desc':'endtime desc';
        return Db::table($this->table)->where($where)->page($pagenum,$limit)->order($order)->field($field)->select();
    }


    /**
     * [getCount 获取提现总条数]
     * @param  [type] $where [description]
     * @return [type]        [description]
    */
    public function getCount($data){
        $where = where_filter($data,array('paystatus'));
        return Db::table($this->table)->where($where)->count();
    }



    

}
