<?php
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use think\Log;
/**
* 统计机构缴费认证
**/
class Organauditbillorder extends Model
{
	//统计机构缴费认证
	protected $pk    = 'id';
	protected $table = 'nm_organauditbillorder';

  //获取机构缴费总金额
  /**
   * [getOrganPayAuditBillTotalSum //获取机构缴费总金额]
   * @Author zzq
   * @DateTime 2018-05-15 
   * @param 无参数         []   ]   
   * @return   [array]                   [description]
   */
  public function getOrganPayAuditBillTotalSum(){

      $totalSum = Db::table($this->table)->where('orderstatus','EQ','2')->where('paystatus','EQ','2')->sum('orderprice');
      $data = [];
      $data['totalSum'] = (float)($totalSum);
      return return_format($data, 0, lang('success'));
  }

  public function getOrganPayAuditBillList($data){
        $data['orderbys'] = 'id desc';
        $field = 'id,domain,organname,billname,billinfo,orderprice,paytime,during';
        $where = [];
        if (!empty($data['domain'])) {
            $where['domain'] = ['like', "%" . $data['domain'] . "%"];
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
            $where['paytime'] = ['>= time', $fromdate];
        }elseif(!empty($enddate) && empty($fromdate)){
            //小于某个时间
            $where['paytime'] = ['<= time', $ConvertEnddate];
        }elseif(!empty($enddate) && !empty($fromdate)){
            $where['paytime'] = ['between time', [$fromdate,$ConvertEnddate] ];
        }
        // var_dump($where);
        // die;
        $where['orderstatus'] = ['EQ', 2];
        $where['paystatus'] = ['EQ', 2];
        $lists = Db::table($this->table)->page($data['pagenum'], $data['pernum'])->order($data['orderbys'])->where($where)->page($data['pagenum'], $data['pernum'])->field($field)->select();
        //var_dump($lists);
        if(!$lists){
            //没有数据
            return return_format('', 50031, lang('50031'));
        }
        // var_dump($this->getLastSql());
        //die;
        foreach ($lists as $k => $v) {

            $lists[$k]['paytime'] = Date('Y-m-d H:i:s', $v['paytime']);
            //有效天数
            $lists[$k]['usetime'] = ceil($lists[$k]['during']/(3600*24));
            
        }
        $count = Db::table($this->table)->order($data['orderbys'])->where($where)->field($field)->count();
        $ret = [];
        $ret['lists'] = $lists;
        $ret['count'] = $count;
        $pagenum = ceil($count / $data['pernum']);
        $ret['pagenum'] = $pagenum;
        $ret['pernum'] = $data['pernum'];
        return return_format($ret, 0, lang('success'));
  }

  //认证完成后自动购买一个0元套餐，有效期是一年
  public function buyFreeBillOrder($organid){
    Log::write('@@@@@@@@自动成功购买免费套餐开始@@@@@@@@');
    Db::startTrans() ;
    try{
      //查找这个organid对应的domain organname 
      $organinfo = Db::table('nm_organ')->where('id','EQ',$organid)->find();
      //查找这个机构对应的超级管理员
      $organuser = Db::table('nm_allaccount')->where('organid','EQ',$organid)->find();
      $time = time();
      $trade_no = getOrderNum();
      $data = [];
      $data['out_trade_no'] = $trade_no;//生成充值订单号
      $data['trade_no'] = $trade_no;//生成充值订单号
      $data['billname']  = '免费版套餐';
      $data['billinfo']  = '0点';
      $data['orderprice']= '0.00';
      $data['paytype']   = '1';
      $data['ordertime'] = $time;
      $data['paytime'] = $time;
      $data['vip']       = '1';
      $data['domain']    = $organinfo['domain'] ? $organinfo['domain'] : '';
      $data['organname'] = $organinfo['organname'] ? $organinfo['organname'] : '';
      $data['organid']   = $organid;
      $data['billid']    = '1';
      $data['during']    = '31536000';
      $data['uid']       = $organuser['uid'] ? $organuser['uid'] : '';
      $data['buyer_id']       = $organuser['uid'] ? $organuser['uid'] : '';
      $data['orderstatus']       = '2';
      $data['paystatus']       = '2';
      $data['billid']       = '1';
      $billOrderId = Db::table('nm_organauditbillorder')->insertGetId($data);
      //修改该机构的validtime和usetrial
      $ret['usetrial'] = '3';
      $ret['validtime'] =  $time + $data['during'];
      $res = Db::table('nm_organ')->where('id','eq',$organid)->update($ret) ;
      Log::write($this->getLastSql());
      Log::write('结果:'.$res);
      // 提交事务
      Db::commit();
      Log::write('成功购买了免费套餐');
    } catch (\Exception $e) {
      // 回滚事务
      Db::rollback();
      Log::write($e->getMessage());
      Log::write('购买免费套餐失败');
    }
    Log::write('@@@@@@@@自动成功购买免费套餐结束@@@@@@@@');
  }
}	


