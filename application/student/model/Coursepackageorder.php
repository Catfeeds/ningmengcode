<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
class Coursepackageorder extends Model
{
    protected $table = 'nm_coursepackageorder';
    //订单完成状态
    protected $completestatus = 20;
    //订单取消状态
    protected $cancelstatus = 10;
    //已经下单状态
    protected $orderstatus = 0;
    protected $rule = [
        'ordernum' => 'require',
        'setmeal'      => 'require',
        'ordertime' => 'require',
        'studentid'     => 'require|number',
        'amount'     => 'require',
        'ordersource'     => 'require|number',
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'ordernum.require' => lang('38103'),
            'setmeal.require' => lang('38104'),
            'ordertime.number' => lang('38105'),
            'studentid.require'   => lang('38106'),
            'studentid.number'   => lang('38107'),
            'amount.require'  => lang('39108'),
        ];
    }
    /**
     * [addOrder 套餐统一下单]
     * @Author yr
     * @DateTime 2018-04-21T13:50:56+0800
     * @return   array
     */
    public function addOrder($data){
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)){
            $res['msg'] =  $validate->getError();
            $res['code'] = '38102';
            return $res;
        }else{
            $data = where_filter($data,array('ordernum','packageid','setmeal','ordertime','studentid','amount','ordersource','orderstatus','paytime','packagegiftid'));
            $ids = Db::table($this->table)->insertGetId($data);
            $res['code'] = 0;
            return $res;
        }
    }
    /**
     * [getBuyCount 获取学生购买套餐的次数]
     * @Author yr
     * @DateTime 2018-04-23T11:38:01+0800
     * @return   [type]                            [description]
     */
    public function getBuyCount($where){
        $result = Db::table($this->table)
            ->where($where)
            ->count();
        return $result;
    }
    /**
     * [getPackageOrderInfo 根据订单号获取订单信息]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param ordernum int 订单号
     * @return   array
     */
    public function getPackageOrderInfo($ordernum){
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid,o.amount,o.orderstatus,o.studentid,o.ordernum,o.balance,o.ordertime,s.usablemoney';
        $lists = Db::table($this->table. ' o')
            ->field($field)
            ->join('nm_coursepackage p','o.packageid=p.id','LEFT')
            ->join('nm_coursepackagegift g','o.packagegiftid=g.id','LEFT')
            ->join('nm_studentfunds s','o.studentid=s.studentid','LEFT')
            ->where('o.ordernum','eq',$ordernum)
            ->find();
        return  $lists;
    }
    /**
     * [getStudentPackageOrderr 获取学生订单]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getStudentPackageOrder($where,$limitstr,$orderstatus=null){
        $orderstatus = isset($orderstatus)?$orderstatus:false;
        if($orderstatus == false){
            $where['orderstatus'] = ['egt',$this->orderstatus];
            //代表未支付订单
        }elseif($orderstatus == 1){
            $where['orderstatus'] = ['in','0,10'];
        }else{
            //
            $where['orderstatus'] = ['eq',$orderstatus];
        }
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid,o.amount,o.orderstatus,FROM_UNIXTIME(o.ordertime) as ordertime,o.ordernum';
        $lists = Db::table($this->table. ' o')
            ->field($field)
            ->join('nm_coursepackage p','o.packageid=p.id','LEFT')
            ->join('nm_coursepackagegift g','o.packagegiftid=g.id','LEFT')
            ->where($where)
            ->order('ordertime desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getStudentPackageOrderCount 获取学生订单数量]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getStudentPackageOrderCount ($where,$orderstatus=null){
        $orderstatus = isset($orderstatus)?$orderstatus:false;
        if($orderstatus == false){
            $where['orderstatus'] = ['egt',$this->orderstatus];
            //代表未支付订单
        }elseif($orderstatus == 1){
            $where['orderstatus'] = ['in','0,10'];
        }else{
            //
            $where['orderstatus'] = ['eq',$orderstatus];
        }
        $lists = Db::table($this->table)
            ->where($where)
            ->count();
        return  $lists;
    }
    /**
     * [updateData 修改订单信息]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @return   array
     */
    public function updateData($where,$data){
        $result  = Db::table($this->table)->where($where)->update($data);
        return $result;
    }
    /**
     * 取消30分钟内 删选范围条件两天内的套餐订单
     * @Author yr
     * @DateTime 2018-05-21T13:11:19+0800
     *
     */
    public function getUnpaidOrderArr($preday,$half_an_hour){
        $lists = Db::table($this->table.' o')
            ->field('o.ordernum,o.studentid,o.balance')
            ->where('o.ordertime','lt',$half_an_hour)
            ->where('o.ordertime','gt',$preday)
            ->where('o.orderstatus','eq',$this->orderstatus)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getOrderInfo 获取学生订单]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getOrderInfo($where){
        $field = 'p.id as packageid,p.bughour,p.setmeal,p.setimgpath,p.setprice,p.limitbuy,p.threshold,p.efftype,p.effendtime,p.effstarttime,p.efftime,p.trialtype,p.content,p.givestatus,g.sendvideo,g.sendlive,g.giftthreshold,g.giftefftype,g.gifteffstarttime,g.gifteffendtime,g.giftefftime,g.gifttrialtype,g.id as packagegiftid,o.amount,o.orderstatus,FROM_UNIXTIME(o.ordertime) as ordertime,o.ordernum';
        $lists = Db::table($this->table. ' o')
            ->field($field)
            ->join('nm_coursepackage p','o.packageid=p.id','LEFT')
            ->join('nm_coursepackagegift g','o.packagegiftid=g.id','LEFT')
            ->where($where)
            ->order('ordertime desc')
            ->select();
        return  $lists;
    }
}
