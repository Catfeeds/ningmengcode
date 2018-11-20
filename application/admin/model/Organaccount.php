<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
use app\admin\model\DbModel;

/*
 * 机构余额表
 * @ jcr
*/
class Organaccount extends Model{

    protected $table = 'nm_organaccount';
    protected $organid;
    protected $pagenum; //每页显示行数

    //自定义初始化
    protected function initialize(){        
        $this->organid = 1;
        $this->pagenum = config('paginate.list_rows');
        parent::initialize();
    }

   
    /**
     * getOrganAccountId 获取对应机构下的账户余额
     * @ jcr
     * @return array();
     */
    public function getOrganAccountId(){
        return Db::table($this->table)->where('organid',1)->field('usablemoney,frozenmoney,tradeflow')->find();
    }

    
    /**
     * [setWithdraw 提现申请和提现恢复]
     * @param [type] $type  [0 提现申请 1提现恢复]
     * @param [type] $price [提现金额]
     */
    public function setWithdraw($type,$price){
        if($type==0){
            // 提现申请 余额减 冻结加
            $info = Db::table($this->table)
                            ->where(['organid'=>1,'usablemoney'=>array('egt',$price)])
                            ->exp('usablemoney','usablemoney - '.$price)
                            ->exp('frozenmoney','frozenmoney + '.$price)
                            ->update();
        }else{
            // 取消申请 资金回滚 余额加 冻结减
            return Db::table($this->table)
                            ->where(['organid'=>1,'frozenmoney'=>array('egt',$price)])
                            ->exp('usablemoney','usablemoney + '.$price)
                            ->exp('frozenmoney','frozenmoney - '.$price)
                            ->update();
        }
        return $info;
    }
    /**
     *  获取机构 交易总额 ，所有的下单成功的，不包含充值 
     *  @author wyx
     *  @param  $organid   机构标识id
     *
     */
    public function getOrganTradeFlow(){
        return Db::table($this->table)
        ->field('tradeflow amount')
//        ->group('organid')
        ->find();
    }


    /**
     * [editUsablemoney 加减机构账户余额]
     * @param  [type] $type    [0 加钱 1减钱]
     * @param  [type] $price   [操作金额]
     * @param  [type] $organid [所属机构]
     * @return [type]          [description]
     */
    public function editUsablemoney($type,$price,$organid){
        if($type==0){
            // 结算 机构账户余额增加
            return Db::table($this->table)->where('organid',$organid)->setInc('usablemoney',$price);
        }else{
            // 减少机构账户余额
            return Db::table($this->table)->where('organid',$organid)->setDec('usablemoney',$price);
        }
    }





}
