<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 机构对应的流水
 * @ jcr
*/
class Organpaylog extends Model{

    protected $table = 'nm_organpaylog';
    protected $organid;
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){
        parent::initialize();        
        $this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
    }


    /**
     * getId 查询机构下对应订单账单
     * @ jcr
     * @param $where    查询条件
     * @param $pagenum  第几页
     * @param $limit    一页几条
     * @return array();
     */
    public function getOrganList($data,$pagenum,$limit){
        foreach ($data as $k => $v) {
            $where['log.'.$k] = $v;
        }
        $field = 'log.studentid,log.paynum,log.paystatus,log.paytime as addtime,log.out_trade_no,log.rakeprice,log.realityprice,o.coursename,log.teacherid';
        $lists = Db::table($this->table)->alias('log')
                                        ->where($where)
                                        ->join(' nm_ordermanage o ',' log.out_trade_no = o.ordernum  ','LEFT')
                                        ->field($field)
                                        ->page($pagenum,$limit)
                                        ->select();
        return $lists;
    }

    /**
     * [getListlog 查询老师指定时间流水]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getListlog($where){
        return Db::table($this->table)->where($where)->field('count(paystatus) as counts,SUM(realityprice) as realityprice')->select();
    }



  
    /**
     * [getOrganCount 查询机构流水对应条数]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getOrganCount($where){
        return Db::table($this->table)->where($where)->count();
    }

    /**
     * [addPayLog 订单结算]
     * @param [type] $data [description]
     */
    public function addPayLog($data){
        // 机构流水表记录
        $payData = ['courseid'  => $data['curriculumid'],
                    'paytype'   => 1,
                    'paystatus' => 1,
                    'paytime'   => time(),
                    'out_trade_no'=>$data['ordernum'],
                    'teacherid' => $data['teacherid'],
                    'studentid' => $data['studentid']];
        // 订单金额
        $payData['paynum'] = $data['amount'];
        // 抽成比例
        $payData['rake'] = 0;  
        // 抽成金额
        $payData['rakeprice'] = ($payData['paynum']*$payData['rake'])/100;
        // 机构实际收入
        $payData['realityprice'] = $payData['paynum'] - $payData['rakeprice'];

        // 开启事务
        Db::startTrans();
        try{
            if(Db::table($this->table)->insert($payData)==0){
                Db::rollback();
                return false;
            }

            // 更新 订单状态同步
            $orderage = new Ordermanage();
            if(!$orderage->orderSave(['ordernum'=>$data['ordernum']],['closingstatus'=>2,'finishtime'=>$payData['paytime']])){
                Db::rollback();
                return false;
            }

            // 机构账户余额加钱、
            $organ = new Organaccount();
            if(!$organ->editUsablemoney(0,$payData['realityprice'],1)){
                Db::rollback();
                return false;
            }

            Db::commit();
            return true;
        }catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }



}
