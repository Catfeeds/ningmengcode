<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;

class Ordermanage extends Model
{
	protected $pk    = 'id';
	protected $table = 'nm_ordermanage';


	//获取累计交易金额
    /**
     * [getTradeTotalSum]
     * @Author zzq
     * @DateTime 2018-05-15
     * @return   [array]                   [description]
     */
    public function getTradeTotalSum() {
        $where = [];
        $where['orderstatus'] = ['EGT', 20];
        $totalSum = Db::table($this->table)->where($where)->sum('amount');
        $data = [];
        $data['totalSum'] = $totalSum;
        // ->select();
        return return_format($data, 0, lang('success'));
    }
    //获取订单列表
    
    /**
     * [getOrderList]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param data array           [提交的信息]
     * @return   [array]                   [description]
     */
    public function getOrderList($data) {
        $data['orderbys'] = 'a.id desc';
        $field = 'a.id,a.ordernum,a.ordertime,a.amount,b.domain,b.organname,c.nickname';
        $where = [];
        if (!empty($data['domain'])) {
            $where['b.domain'] = ['like', "%" . $data['domain'] . "%"];
        }
        //根据时间的范围搜索
        $fromdate  = $data['fromdate'];
        $enddate  = $data['enddate'];
        //转成时间戳
        $ConvertEndtime = strtotime($enddate);
        $ConvertEndtime = $ConvertEndtime + 60*60*24;
        $ConvertEnddate = Date('Y-m-d',$ConvertEndtime);
        // var_dump($ConvertEnddate);
        // die;
        if(empty($enddate) && !empty($fromdate)){
            //大于某个时间
            $where['a.ordertime'] = ['>= time', $fromdate];
        }elseif(!empty($enddate) && empty($fromdate)){
            //小于某个时间
            $where['a.ordertime'] = ['<= time', $ConvertEnddate];
        }elseif(!empty($enddate) && !empty($fromdate)){
            $where['a.ordertime'] = ['between time', [$fromdate,$ConvertEnddate] ];
        }
        // var_dump($where);
        // die;
        $where['a.orderstatus'] = ['EGT', 20];
        $lists = Db::table($this->table)->page($data['pagenum'], $data['pernum'])->alias('a')->join('nm_organ b', 'a.organid = b.id', 'LEFT')->join('nm_studentinfo c', 'a.studentid = c.id', 'LEFT')->order($data['orderbys'])->where($where)->page($data['pagenum'], $data['pernum'])->field($field)->select();
        //var_dump($lists);
        if(!$lists){
            //没有数据
            return return_format('', 50030, lang('50030'));
        }
        // var_dump($this->getLastSql());
        //die;
        foreach ($lists as $k => $v) {
            $lists[$k]['ordertime'] = Date('Y-m-d H:i:s', $v['ordertime']);
        }
        $count = Db::table($this->table)->alias('a')->join('nm_organ b', 'a.organid = b.id', 'LEFT')->join('nm_studentinfo c', 'a.studentid = c.id', 'LEFT')->order($data['orderbys'])->where($where)->field($field)->count();
        $ret = [];
        $ret['lists'] = $lists;
        $ret['count'] = $count;
        $pagenum = ceil($count / $data['pernum']);
        $ret['pagenum'] = $pagenum;
        $ret['pernum'] = $data['pernum'];
        return return_format($ret, 0, lang('success'));
    }
    //获取订单详情
    
    /**
     * [getOrderDetail]
     * @Author zzq
     * @DateTime 2018-05-15
     * @param $key string           [数据表中的字段]
     * @param $value string           [数据表中的字段对应的值]
     * @return   [array]                   [description]
     */
    public function getOrderDetail($key, $value) {
        $field = 'a.id,a.ordernum,a.orderstatus,a.ordertime,c.nickname,a.ordersource,a.paytype,a.originprice,a.discount,a.amount,a.balance,a.coursename,a.classname,a.type,d.teachername,b.organname';
        $ret = Db::table($this->table)->alias('a')->join('nm_organ b', 'a.organid = b.id', 'LEFT')->join('nm_studentinfo c', 'a.studentid = c.id', 'LEFT')->join('nm_teacherinfo d', 'a.teacherid = d.teacherid', 'LEFT')->where($key, '=', $value)->field($field)->find();
        if (!$ret) {
            return return_format('', 50087, lang('50087'));
        }
         // var_dump($this->getLastSql());
         // die;
        // var_dump($ret);
        // die;
        $data = [];
        //订单状态名字 下单渠道 支付方式 下单时间  入账金额
        $data['id'] = $ret['id'];
        $data['ordernum'] = $ret['ordernum'];
        $data['orderstaus'] = getOrderStatus($ret['orderstatus']);
        $data['ordertime'] = Date('Y-m-d H:i:s', $ret['ordertime']);
        $data['nickname'] = $ret['nickname'];
        $data['ordersource'] = getOrderSource($ret['ordersource']);
        $data['paytype'] = getOrderPayType($ret['paytype']);
        $data['originprice'] = $ret['originprice'];
        $data['discount'] = $ret['discount'];
        $data['amount'] = $ret['amount'];
        $data['balance'] = $ret['balance'];
        $data['inMoney'] = (float)($ret['amount'] - $ret['balance']); //入账金额
        $data['coursename'] = $ret['coursename'];
        $data['classname'] = $ret['classname'];
        $data['type'] = $ret['type'];
        $data['teachername'] = $ret['teachername'];
        $data['organname'] = $ret['organname'];
        return return_format($data, 0, lang('success'));
    }
        


}	