<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 订单表Model
 * @ yr
*/
class Ordermanage extends Model{
    protected $table = 'nm_ordermanage';
    //订单完成状态
    protected $completestatus = 20;
    //订单取消状态
    protected $cancelstatus = 10;
    //已经下单状态
    protected $orderstatus = 0;
    protected $rule = [
        'ordernum' => 'require',
        'coursename'      => 'require',
        'teacherid'  => 'require|number',
        'ordertime' => 'require',
        'studentid'     => 'require|number',
        'amount'     => 'require',
        'ordersource'     => 'require|number',
        'curriculumid'     => 'require|number',
    ];
    protected $message = [ ];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'ordernum.require' => lang('34005'),
            'coursename.require' => lang('34006'),
            'ordertime.number' => lang('34007'),
            'teacherid.require' => lang('34008'),
            'teacherid.number' => lang('34009'),
            'studentid.require'   => lang('34010'),
            'studentid.number'   => lang('34011'),
            'amount.require'  => lang('34012'),
            'curriculumid.require'      => lang('34017'),
            'curriculumid.number'      => lang('34018'),
        ];
    }
    /**
     * [gotoOrder 学生统一下单]
     * @Author yr
     * @DateTime 2018-04-21T13:50:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function gotoOrder($data){
        if(!empty($data['gradename'])){
            $data['classname'] = $data['gradename'];
        }
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($data)){
          $res =  $validate->getError();
          return $res;
        }else{
            $data = where_filter($data,array('ordernum','classname','coursename','teacherid','ordertime','studentid','amount','ordersource','curriculumid','schedulingid','discount','originprice','coursetype','orderstatus','paytime','usepackage','addressid'));
            $ids = Db::table($this->table)->insertGetId($data);
            return $ids;
        }
    }
    /**
     * [getOrderStudentNum 获取指定老师的报名学生人数]
     * @Author yr
     * @DateTime 2018-04-21T13:50:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getOrderBySchedu($studentid,$schedulingid){
        $res = Db::table($this->table)
                ->where('studentid','eq',$studentid)
                ->where('schedulingid','eq',$schedulingid)
                ->where('orderstatus','neq',10)
                ->count();
        return $res;
    }
    /**
     * [getOrderStudentNum 获取指定老师的报名学生人数]
     * @Author yr
     * @DateTime 2018-04-21T13:50:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getOrderByCourseid($studentid,$courseid){
        $res = Db::table($this->table)
            ->where('studentid','eq',$studentid)
            ->where('curriculumid','eq',$courseid)
            ->where('orderstatus','neq',10)
            ->count();
        return $res;
    }
    /**
     * [getOrderStudentNum 获取指定老师的报名学生人数]
     * @Author yr
     * @DateTime 2018-04-21T13:50:56+0800
     * @param    [int]        $teacherid [老师id]
     * @return   array
     */
    public function getOrderStudentNum($teacherid){
        $res = Db::table($this->table)
            ->where('studentid','in',function ($query) use($teacherid) {
                $query->table('nm_ordermanage')->where('teacherid','eq',$teacherid)->where('orderstatus','EQ',$this->completestatus)->field('studentid');
        })->count('distinct studentid');
        return $res;
    }
    /**
     * [getStudentOrder 查询学生购买的未完成的一对一课程]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getStudentIsClosing($studentid){
        $lists = Db::table($this->table)
            ->where('studentid','eq',$studentid)
            ->where('orderstatus','eq',$this->completestatus)
            ->where('closingstatus','neq',0)
            ->count();
        return  $lists;
    }
    /**
     * [getStudentOrder 按直播课订单状态获取指定学生的订单列表]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getStudentOrder($userid,$limitstr,$orderstatus=null,$where){
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
        if(!empty($where['coursetype'])){
            if($where['coursetype'] == 1){
                $field = ',c.price as price';
            }else{
                $field  = ',s.price as price';
            }
        }else{
            $field = '';
        }
        $field =  'o.schedulingid,o.teacherid,o.curriculumid,o.id as orderid,o.ordernum,o.classname,o.coursename,FROM_UNIXTIME(o.ordertime) as ordertime,o.amount,o.originprice,o.orderstatus,o.coursetype,t.nickname as teachername,c.imageurl,s.classstatus,c.subhead'.$field;
        $lists = Db::table($this->table. ' o')
            ->field($field)
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where('studentid','eq',$userid)
            ->where($where)
            ->order('ordertime desc')
            ->limit($limitstr)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [studentOrderCount 获取指定学生的订单总数]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function studentOrderCount($userid,$orderstatus=null,$where){
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
            ->where('studentid','eq',$userid)
            ->where($where)
            ->count();
        return  $lists;
    }
    /**
     * [getStudentOrder 按订单状态获取指定学生的订单列表]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getStudentUndoneClass($userid,$limitstr){
        $lists = Db::table($this->table. ' o')
            ->field('o.schedulingid,o.teacherid,o.curriculumid,o.id as orderid,o.ordernum,o.classname,o.coursename,FROM_UNIXTIME(o.ordertime) as ordertime,o.amount,o.originprice,o.orderstatus,t.nickname as teachername,s.imageurl,s.classstatus,c.subhead')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where('studentid','eq',$userid)
            ->where('orderstatus','eq',$this->completestatus)
            ->where('closingstatus','eq','0')
            ->order('ordertime desc')
            ->limit($limitstr)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [studentOrderCount 获取指定学生的订单总数]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function studentUndoneClassCount($userid){
        $lists = Db::table($this->table)
            ->where('studentid','eq',$userid)
            ->where('orderstatus','eq',$this->completestatus)
            ->where('closingstatus','eq','0')
            ->count();
        return  $lists;
    }
    /**
     * [getReserveClass 获取一对一的全部课程]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getReserveClass($userid,$limitstr){
        $where['orderstatus'] = ['eq',$this->completestatus];
        $lists = Db::table($this->table. ' o')
            ->field('o.schedulingid,o.teacherid,o.curriculumid,o.id as orderid,o.ordernum,o.classname,o.coursename,FROM_UNIXTIME(o.ordertime) as ordertime,o.amount,o.originprice,o.orderstatus,t.nickname as teachername,s.imageurl,s.classstatus,c.subhead')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where('o.studentid','eq',$userid)
            ->where('o.closingstatus','eq',0)
            ->where($where)
            ->order('o.ordertime desc')
            ->limit($limitstr)
            ->select();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getReserveClass 获取一对一的全部课程]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param    [int]        $userid       [学生id]
     * @param    [string]     $limitstr     [分页条件]
     * @param    [string]     $orderstatus     [订单状态]
     * @return   array
     */
    public function getReserveCount($userid){
        $where['orderstatus'] = ['eq',$this->completestatus];
        $lists = Db::table($this->table. ' o')
            ->where('studentid','eq',$userid)
            ->where('closingstatus','eq',0)
            ->where($where)
            ->count();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getUnpaidOrderInfo 获取指定学生的订单列表]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param orderid int 订单Id
     * @param ordernum int 订单号
     * @param userid int 用户id 用户ID
     * @return   array
     */
    public function getUnpaidOrderInfo($userid,$ordernum){
        $lists =Db::table($this->table. ' o')
            ->field('o.id as orderid,o.schedulingid,o.curriculumid,o.ordernum,o.classname,o.coursename,o.originprice,o.discount,FROM_UNIXTIME(o.ordertime) as ordertime,o.amount,o.orderstatus,t.nickname as teachername,s.imageurl,s.classstatus,o.balance,o.coursetype,o.usepackage')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where('o.studentid','eq',$userid)
            ->where('o.ordernum','eq',$ordernum)
            ->find();
        return  $lists;
    }
    /**
     * [getOrderInfo 根据订单号获取订单信息]
     * @Author yr
     * @DateTime 2018-04-24T13:50:56+0800
     * @param orderid int 订单Id
     * @param ordernum int 订单号
     * @param userid int 用户id 用户ID
     * @return   array
     */
    public function getOrderInfo($ordernum){
        $lists =Db::table($this->table. ' o')
            ->field('o.id as orderid,o.studentid,o.curriculumid,o.ordernum,o.classname,o.coursename,o.schedulingid,o.originprice,o.discount,c.classtypes,FROM_UNIXTIME(o.ordertime) as ordertime,o.schedulingid,o.amount,o.paytype,o.orderstatus,o.balance,t.nickname as teachername,c.imageurl,o.coursetype,f.usablemoney,t.imageurl as teacherimage,FROM_UNIXTIME(p.paytime) as paytime,s.fullpeople,s.classstatus,c.giftdescribe,s.starttime,s.endtime,c.periodnum,o.usepackage')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_studentfunds f','o.studentid=f.studentid','LEFT')
            ->join('nm_studentpaylog p','o.ordernum=p.out_trade_no','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where('o.ordernum','eq',$ordernum)
            ->find();
        return  $lists;
    }
    /**
     * [getApplyPeople 获取该课程下的报名人数]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function getApplyPeople($schedulingid){
        $lists =Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->where('orderstatus',['eq',$this->orderstatus],['eq',$this->completestatus],'or' )
            ->count();
        $sql = Db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [isBuy 查询学生是否购买过该班级]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function isBuy($schedulingid ,$studentid){
        $lists =Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->where('studentid','eq',$studentid )
            ->where('orderstatus',['eq',$this->orderstatus],['eq',$this->completestatus],'or' )
            ->count();
        return  $lists;
    }
    /**
     * [isBuy 查询学生是否购买过该班级]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function isStudentBuy($schedulingid ,$studentid){
        $lists =Db::table($this->table)
            ->where('schedulingid','eq',$schedulingid)
            ->where('studentid','eq',$studentid )
            ->where('orderstatus','eq',$this->completestatus)
            ->count();
        return  $lists;
    }
    /**
     * [isBuyCourse 查询学生是否购买过该课程]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function isBuyCourse($courseid ,$studentid){
        $lists =Db::table($this->table)
            ->where('curriculumid','eq',$courseid)
            ->where('studentid','eq',$studentid )
            ->where('orderstatus','eq',$this->completestatus)
            ->count();
        return  $lists;
    }
    /**
     * [isBuyCourse 查询学生是否购买过该课程]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function getClassOrdernum($classid,$studentid){
        $field = 'ordernum';
        $lists = Db::table($this->table)
            ->field($field)
            ->where('schedulingid','eq',$classid)
            ->where('studentid','eq',$studentid )
            ->where('orderstatus','eq',$this->completestatus)
            ->find();
        return  $lists;
    }
    /**
     * [isBuyCourse 查询学生是否购买过该课程]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param schedulingid int 课程id
     * @return   array
     */
    public function getCourseOrdernum($courseid ,$studentid){
        $field = 'ordernum';
        $lists = Db::table($this->table)
            ->field($field)
            ->where('curriculumid','eq',$courseid)
            ->where('studentid','eq',$studentid )
            ->where('orderstatus','eq',$this->completestatus)
            ->find();
        return  $lists;
    }
    /**
     * [getOrderdata 根据订单id查询订单信息]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param id int 订单id
     * @return   array
     */
    public function getOrderdata($id){
        $lists =Db::table($this->table.' o')
            ->field('o.discount,o.originprice,o.ordernum,o.amount,s.usablemoney')
            ->join('nm_studentfunds s','o.studentid=s.studentid','LEFT')
            ->where('id','eq',$id )
            ->find();
        return  $lists;
    }
    /**
     * [getUnpaidOrderArr 获取30分钟内 删选条件2天内未支付的订单]
     * @Author yr
     * @DateTime 2018-04-28T13:50:56+0800
     * @param id int 订单id
     * @return   array
     */
    public function getUnpaidOrderArr($preday,$half_an_hour){
        $lists = Db::table($this->table.' o')
            ->field('o.studentid,o.amount,o.balance,orderstatus,o.schedulingid,o.ordernum,o.id,s.frozenmoney,o.balance,l.fullpeople,l.classstatus,o.coursetype')
            ->join('nm_studentfunds s','o.studentid = s.studentid','LEFT')
            ->join('nm_scheduling l','o.schedulingid = l.id','LEFT')
            ->where('o.ordertime','lt',$half_an_hour)
            ->where('o.ordertime','gt',$preday)
            ->where('o.orderstatus','eq',$this->orderstatus)
            ->select();
        $sql = Db::table($this->table.' o')->getLastSql();
        return  $lists;
    }
    /**
     * [updateOrderInfo 修改订单状态]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @param id int 订单id
     * @return   array
     */
    public function updateOrderInfo($ordernum,$paytype,$orderstatus){
        $where = ['ordernum'=>$ordernum];
        $data['paytype'] = $paytype;
        $data['orderstatus'] = $orderstatus;
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [updateBalanceInfo修改订单状态]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @param ordernum int 订单号
     * @return   array
     */
    public function updateBalanceInfo($where,$data){
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
    /**
     * [orderSendNotice查询还有5分钟就要取消的订单]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function orderSendNotice($starttime,$endtime){
        $lists = Db::table($this->table.' o')
            ->field('o.ordernum,o.coursename,t.nickname as teachername,s.prphone,s.mobile')
            ->join('mk_studentinfo s','o.studentid = s.id','LEFT')
            ->join('mk_teacherinfo t','t.teacherid = o.teacherid','LEFT')
            ->where('o.ordertime','egt',$starttime)
            ->where('o.ordertime','lt',$endtime)
            ->where('o.orderstatus','eq',$this->orderstatus)
            ->select();
        $sql = Db::table($this->table.' o')->getLastSql();
        return  $lists;
    }
    /**
     * [getStudentAllOrder 查询学生所有的订单]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function getStudentAllOrder($where){
        $lists = Db::table($this->table. ' o')
            ->field('o.schedulingid,s.gradename,c.periodnum,o.curriculumid')
            ->join('nm_curriculum c','o.curriculumid=c.id','LEFT')
            ->join('nm_teacherinfo t','o.teacherid=t.teacherid','LEFT')
            ->join('nm_scheduling s','o.schedulingid=s.id','LEFT')
            ->where($where)
            ->select();
        return $lists;
    }
    /**
     * [getBuyCourseList 查询学生购买过的不重复的订单]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function getBuyCourseList($where){
        $lists = Db::table($this->table)
            ->field('coursename,curriculumid')
            ->where($where)
            ->group('curriculumid')
            ->select();
        return $lists;
    }
    /**
     * [getAllScheding 查询学生购买的所有班级id]
     * @Author yr
     * @DateTime 2018-04-29T13:50:56+0800
     * @return   array
     */
    public function getAllScheding($where){
        $lists = Db::table($this->table)
            ->where($where)
            ->column('schedulingid');
        return $lists;
    }
}







