<?php
/**
 * 学生消息提醒
 * User: yr
 * Date: 2018/7/18
 * Time: 17:39
 */
use app\student\model\Ordermanage;
use app\student\model\Toteachtime;
use app\student\model\Unitdeputy;
use app\student\model\Classcollection;
class StudendMsg
{
    protected $messageobj;
    protected $usertype;
    public function __construct()
    {
        $this->messageobj = new Messages;
        //用户类型  0为机构超级管理员 1为老师 2机构添加的管理账号 3学生 4 官方 5官方添加管理员
        $this->usertype = 3;
        $this->teachertype = 1;
    }
    /*
  * 消息推送 学员课时结束后
  * 3【评论提醒】您已经完成了小班课小学三年级语文精讲课程第*单元****第*课时《我们的名族小学》的课程学习，在学习过程中收获颇多，快来写下你对讲师的评论吧
  *  @param nickname 学生昵称
  *  @param content 消息内容
  *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
  *  @param usertype 用户类型
  *  @param studentid 学生id
     * crontab 定时任务 执行频率5分钟执行一次
  *  @Author yr
  */
    public function sendCommentNotice(){
        $toteachmodel = new Toteachtime;
        $nowtime  = strtotime(date('Y-m-d H:i'));
        $starttime = $nowtime -5*60;
        $endtitme = $nowtime;
        //查询满足条件的课时
        $list = $toteachmodel->getDataByEndtime($starttime,$endtitme);
        $type = 3;//业务类型
        $title = '购买提醒';
        $singleArr = [];
        //实例化单元表
        $unitmodel = new Unitdeputy;
        //实例化订单表
        $ordermodel = new Ordermanage;
        $moreArr = [];
        //定义小课班和直播课的list
        $newArr = [];
        if(!empty($list)){
            foreach($list as $k=>$v){
                if($v['type'] == 1){
                    //如果是一对一 直接发送消息
                    $singleArr[$k]['title'] = $title;
                    $unitnum  = $unitmodel->getUnitSort($v['unitid']);
                    $typename = getClassName($v['type']);
                    $singleArr[$k]['content'] = "您已经完成了{$typename}{$v['coursename']}第{$unitnum}单元****第{$v['periodsort']}课时《{$v['periodname']}》的课程学习，在学习过程中收获颇多，快来写下你对讲师的评论吧";
                    $singleArr[$k]['usertype'] = $this->usertype;
                    $singleArr[$k]['userid'] = $this->usertype.$v['studentid'];
                    $singleArr[$k]['externalid'] = $v['lessonsid'];
                    $singleArr[$k]['organid'] = 1; //接受消息用户机构id 学生默认1
                }else{
                    //如果是小课班和直播课 需要查询出购买过此课程的所有学生 在发送消息
                    $moreArr[$k]['userlist'] = $ordermodel->getBuyStudents($v['schedulingid']);
                    //获取买过该课节的所有学生
                    $userlist = array_column($moreArr[$k]['userlist'],'studentid');
                    $typename = getClassName($v['type']);
                    foreach($userlist as $key=>$value){
                        $newarray[$key]['title'] = $title;
                        $unitnum  = $unitmodel->getUnitSort($v['unitid']);
                        $newarray[$key]['content'] = "您已经完成了{$typename}{$v['coursename']}第{$unitnum}单元****第{$v['periodsort']}课时《{$v['periodname']}》的课程学习，在学习过程中收获颇多，快来写下你对讲师的评论吧";
                        $newarray[$key]['usertype'] = $this->usertype;
                        $newarray[$key]['userid'] = $this->usertype.$value;
                        $newarray[$key]['externalid'] = $v['lessonsid'];
                        $newarray[$key]['organid'] = 1;//接受消息用户机构id 学生默认1
                        array_push($newArr, $newarray[$key]);
                    }
                }
            }
            //合并一对一和一对多的消息数组
            $msgList = array_merge($singleArr,$newArr);
            $this->messageobj->addMessagearr($msgList,$type);
        }

    }
    /*
    * 消息推送 每周一进度提醒
    * 4【上课提醒】您报名的***课程，已经进行了4节课的学习，剩余5节课，加油哦
    *  @param coursename 课程名称
    *  @param content 消息内容
    *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
    *  @param usertype 用户类型
    *  @param studentid 学生id
    * crontab 定时任务 每周一 9.00提醒
    *  @Author yr
    */
    public function sendProgressMsg(){
        //查询出已支付的所有订单
        $title = '上课提醒';
        $type = 2;//业务类型
        $ordermodel = new Ordermanage;
        $orderList = $ordermodel->getPaidOrderList();
        $toteachmodel = new Toteachtime;
        $timenow = time();
        $msgList = [];
        foreach($orderList as $k=>$v){
            $msgList[$k]['title'] = $title;
            $periodnum = $v['periodnum'];
            if($v['type'] == 1){
                //如果是一对一 通过ordernum和课时表关联
                $where['ordernum'] = $v['ordernum'];
                $where['endtime']  = ['EGT',$timenow];
            }else{
                //一对多通过 schedulingid 和课时表关联
                $where['schedulingid'] = $v['schedulingid'];
                $where['endtime']  = ['EGT',$timenow];
            }
            $finishnum = $toteachmodel->getEndLessonsCount($where);//以结束的课时数量
            $surplusnum = $periodnum - $finishnum;
            $msgList[$k]['content'] = "您报名的{$v['coursename']}课程，已经进行了{$finishnum}节课的学习，剩余{$surplusnum}节课，加油哦";
            $msgList[$k]['usertype'] = $this->usertype;
            $msgList[$k]['userid'] = $this->usertype.$v['studentid'];
            $msgList[$k]['organid'] = 1; //接受消息用户机构id 学生默认1
            $msgList[$k]['externalid'] = '';
        }
        //合并一对一和一对多的消息数组
        $this->messageobj->addMessagearr($msgList,$type);

    }
    /*
  * 消息推送 1对1学员超过1周未预约课程
  * 【预约提醒】您已超过一周未预约课程，请及时预约
  *  @param coursename 课程名称
  *  @param content 消息内容
  *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
  *  @param usertype 用户类型
  *  @param studentid 学生id
  * crontab 定时任务 每天一次 9.00
  *  @Author yr
  */
    public function unreservationsMsg(){
        //查询出已支付的所有订单
        $title = '预约提醒';
        $type = 4;//业务类型
        $collectmodel = new Classcollection;
        $collectList = $collectmodel->getList();
        $ordermodel = new Ordermanage;
        $msgList = [];
        foreach($collectList as $k=>$v){
            //查看学生是否购买过此班级 如果购买从数组里删除
            $isbuy = $ordermodel->isBuy($v['schedulingid'],$v['studentid']);
            if($isbuy<=0){
                unset($collectList[$k]);
            }else{
                $msgList[$k]['title'] = $title;
                $msgList[$k]['content'] = "您收藏的{$v['curriculumname']}课程，余位已经不多，快去报名吧";
                $msgList[$k]['usertype'] = $this->usertype;
                $msgList[$k]['userid'] = $this->usertype.$v['studentid'];
                $msgList[$k]['organid'] = 1; //接受消息用户机构id 学生默认1
                $msgList[$k]['externalid'] = $v['schedulingid'];
            }

        }
        $this->messageobj->addMessagearr($msgList,$type);

    }
    /*
   * 消息推送 学员收藏课程但是还未购买
   * 7【课程提醒】您收藏的**课程，余位已经不多，快去报名吧
   *  @param coursename 课程名称
   *  @param content 消息内容
   *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
   *  @param usertype 用户类型
   *  @param studentid 学生id
   * crontab 定时任务 每隔两天发送一次 9.00提醒
   *  @Author yr
   */
    public function collectNotBuyMsg(){
        //查询出已支付的所有订单
        $title = '课程提醒';
        $type = 5;//业务类型
        $collectmodel = new Classcollection;
        $collectList = $collectmodel->getList();
        $ordermodel = new Ordermanage;
        $msgList = [];
        foreach($collectList as $k=>$v){
            //查看学生是否购买过此班级 如果购买从数组里删除
            $isbuy = $ordermodel->isBuy($v['schedulingid'],$v['studentid']);
            if($isbuy<=0){
                unset($collectList[$k]);
            }else{
                $msgList[$k]['title'] = $title;
                $msgList[$k]['content'] = "您收藏的{$v['curriculumname']}课程，余位已经不多，快去报名吧";
                $msgList[$k]['usertype'] = $this->usertype;
                $msgList[$k]['userid'] = $this->usertype.$v['studentid'];
                $msgList[$k]['organid'] = 1; //接受消息用户机构id 学生默认1
                $msgList[$k]['externalid'] = $v['schedulingid'];
            }

        }
        $this->messageobj->addMessagearr($msgList,$type);

    }
    /*
  * 消息推送 订单已失效
  * 9【订单提醒】您的订单（订单编号）已失效，快去重新下单吧
  *  @param ordernum 学生昵称
  *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
  *  @param usertype 用户类型
  *  @param studentid 学生id
  *  @Author yr
  */
    public function cancelOrderNotice($msgList){
        $type = 1;
        $data['title'] = '订单提醒';
        $data['content'] = "您的订单({$msgList['ordernum']})已失效，快去重新下单吧";
        $data['usertype'] = $this->usertype;
        $data['userid'] = $this->usertype.$msgList['studentid'];
        $data['externalid'] = $msgList['orderid'];
        $this->messageobj->addMessage($data,$type);
    }
    /*---------------------以下给老师发送--------------------------*/
    /*
    * 消息推送 学员购买完成该老师课程之后  小课班和直播课
    * 12【购买提醒】学员**购买了您的课程，该班级报名总人数为55，尚有余位5人
    *  @param nickname 学生昵称
    *  @param content 消息内容
    *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
    *  @param usertype 用户类型
    *  @param teacherid 老师id
    * 使用到的url student/User/teacherCollect
    *  @Author yr
    */
    public function sendBuyMsg($msgList){
        $type = 7;
        $data['title'] = '购买提醒';
        $data['content'] = "学员{$msgList['nickname']}购买了您的课程，该班级报名总人数为{$msgList['totalpeople']}，尚有余位{$msgList['surpluspeople']}人";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$msgList['teacherid'];
        $data['externalid'] = '';
        $this->messageobj->addMessage($data,$type);
    }
    /*
    * 消息推送 学员收藏该老师之后
    * 14【收藏提醒】学员**已成功关注您
    *  @param nickname 学生昵称
    *  @param content 消息内容
    *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
    *  @param usertype 用户类型
    *  @param teacherid 老师id
    * 使用到的url student/User/teacherCollect
    *  @Author yr
    */
    public function collectTeacherMsg($userinfo){
        $type = 8;
        $data['title'] = '收藏提醒';
        $data['content'] = "学员.{$userinfo['nickname']}.已成功关注您";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$userinfo['userid'];
        $data['externalid'] = $userinfo['userid'];
        $this->messageobj->addMessage($data,$type);
    }
    /*
   * 消息推送 学员收藏该老师之后
   * 15【收藏提醒】学员**已经成功关注您的**课程
   *  @param nickname 学生昵称
   *  @param content 消息内容
   *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
   *  @param usertype 用户类型
   *  @param teacherid 老师id
   *  @param coursename 课程名称
   * 使用到的url student/User/classCollect
   *  @Author yr
   */
    public function collectCourseMsg($msgList){
        $type = 8;
        $data['title'] = '收藏提醒';
        $data['content'] = "学员{$msgList['nickname']}已经成功关注您的{$msgList['coursename']}课程";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$msgList['userid'];
        $data['externalid'] = $msgList['userid'];
        $this->messageobj->addMessage($data,$type);
    }
    /*
    * 消息推送 学员评价该老师后
    * 16【评价提醒】收到来自**学员的评价，快去查看吧（点击查看跳转至教师我的评论页）
    *  @param nickname 学生昵称
    *  @param content 消息内容
    *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
    *  @param usertype 用户类型
    *  @param teacherid 老师id
    *  @param coursename 课程名称
    * 使用到的url student/Mycourse/gotoComment
    *  @Author yr
    */
    public function commentMsg($msgList){
        $type = 1;
        $data['title'] = '评价提醒';
        $data['content'] = "收到来自{$msgList['nickname']}学员的评价";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$msgList['teacherid'];
        $data['externalid'] = $msgList['lessonsid'];
        $this->messageobj->addMessage($data,$type);
    }
    /*
    * 消息推送 收到1对1预约提醒
    * 17【预约提醒】**学员预约6月27日15:30进行**课程，请按时进入教室
    *  @param nickname 学生昵称
    *  @param content 消息内容
    *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
    *  @param usertype 用户类型
    *  @param teacherid 老师id
    *  @param coursename 课程名称
    * 使用到的url student/Mycourse/gotoComment
    *  @Author yr
    */
    public function reservationsMsg($msgList){
        $type = 4;
        $data['title'] = '预约提醒';
        $data['content'] = "{$msgList['nickname']}学员预约{$msgList['month']}月{$msgList['day']}日{$msgList['hour']}:{$msgList['minute']}进行{$msgList['coursename']}课程，请按时进入教室";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$msgList['teacherid'];
        $data['externalid'] = $msgList['schedulingid'];
        $this->messageobj->addMessage($data,$type,$msgList['organid']);
    }
    /*
   * 消息推送 作业提醒
   * 【作业提醒】学员**提醒您批改作业
   *  @param nickname 学生昵称
   *  @param content 消息内容
   *  @param type     // type类型 1订单提醒2上课提醒3评论提醒4预约提醒5课程提醒6推荐消息7购买提醒8收藏提醒
   *  @param usertype 用户类型
   *  @param teacherid 老师id
   *  @param coursename 课程名称
   * 使用到的url student/User/sendhomeworkMsg
   *  @Author yr
   */
    public function sendHomeworkMsg($msgList){
        $type = 10;
        $data['title'] = '作业提醒';
        $data['content'] = "学员{$msgList['nickname']}提醒您批改作业";
        $data['usertype'] = $this->teachertype;
        $data['userid'] = $this->teachertype.$msgList['teacherid'];
        $data['externalid'] = $msgList['homeworkid'];
        $result = $this->messageobj->addMessage($data,$type);
        return $result;
    }
}
