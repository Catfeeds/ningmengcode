<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 14:28
 */

namespace app\teacher\business;
use app\admin\model\Allaccount;
use app\admin\model\Message;
use app\teacher\controller\Order;
use app\teacher\controller\Teacher;
use app\teacher\model\OrderManage;
use app\teacher\model\Organ;
use app\teacher\model\Scheduling;
use app\teacher\model\StudentInfo;
use app\teacher\model\Teacherinfo;
use app\teacher\model\Teachercollection;
use app\teacher\model\Teacherinvite;
use app\teacher\model\ToteachTime;
use app\teacher\model\Organauthinfo;
use \Messages;
use \Push;
class MessageManager
{

    /*
     * 提醒收藏的老师有新的课
     * @Author wangwy
     */
    public function remindAddSchedule ($teacherid,$organid,$schedulingid){
        $cllobj = new Teachercollection();
        $stuidarr = $cllobj->getStucll($teacherid);
        $teacherobj = new Teacherinfo();
        $nickname = $teacherobj->getNick($teacherid,$organid);
        $nickname = !empty($nickname)?$nickname['nickname']:'';
        foreach($stuidarr as $k => $v){
            //$contentarr[$k]['uid'] = !empty($v)?'1'.$v:'';
            $contentarr[$k]['content'] = '您收藏的'.$nickname.'老师，有新课程开班啦，快去报名吧';
            $contentarr[$k]['userid'] = !empty($v)?'3'.$v:'';
            $contentarr[$k]['title'] = '课程提醒';
            $contentarr[$k]['usertype'] = 3;//学生
            $contentarr[$k]['externalid'] = $schedulingid;//班级id
        }
        $msgobj = new Messages();
        $type = 5;
        $msg = $msgobj->addMessagearr($contentarr,$type);
        return return_format($msg,0,lang('success'));

    }
    //将实时提醒消息发布到订阅频道上
    protected function useRedis($contentarr){
        //开始将数据存储在redis中
        $redis = new \redis();
        //log::write(date('Y-m-d H:i:s',time()).'开始链接reids','log',TRUE);
        $link = $redis->connect('10.0.0.9',6379);
        $auth = $redis->auth('vkpKvCgiA*wN4Xo*');
        // 再消息订阅频道发布
        $channel = 'info';
        $res = $redis->publish($channel,json_encode($contentarr));
        return $res;
    }

    /*
     *  教师消息列表(pc端)
     * @param 		$where	 查询条件
	 * @pagenum		$pagenum 第几页
	 * @param		$organid 机构ID
     */
    public function getMessageList($pagenum,$teacherid){
        $msgobj = new \Messages;
        $where['source'] = 1;//来自pc端
        $where['delflag'] = 1;
        $where['usertype'] = 1;
        $where['userid'] = $teacherid;
        $msg = $msgobj->getMessageList($where,$pagenum);
        return $msg;
    }
    /*
     * 查看消息
     *  * @param $id		// 消息ID
	 * @param $istoview	// 是否被查看 0未查看 1已查看
     */
    public function setMessage($idarr){
        $istoview = 1;
        $msgobj = new \Messages;
        $msg = $msgobj->viewMessagearr($idarr,$istoview);
        return $msg;
    }

    /*
     * 推荐消息，新的课程开班
     * 已注册未报名课程/课程已经结束但没有新课程
     *
     * @Author wangwy
     */
    public function remindRecommendSchedule ($schedulingid){

        //已注册未报名课程
        //筛选课程已经结束但是还没下单新课程(小班课，直播课)
        $time = time();//当前时间戳
        $studobj = new StudentInfo();
        $orderobj = new OrderManage();
        $teachtimeobj = new ToteachTime();

        $stuidarr = $studobj->getAllstudentid();//获取所有学生id
        $copystuidarr = $stuidarr;
        foreach($stuidarr as $k =>$v){
            $ordernumarr = [];
            $schedulingarr = [];
            $resarr = $orderobj->getClassall($v);//schedulingid ordernum
            $ordernumarr= array_column($resarr,'ordernum');
            $ordernumarr = array_unique($ordernumarr);
            $schedulingarr = array_column($resarr,'schedulingid');//一对多状态下
            $schedulingarr = array_unique($schedulingarr);//一对一状态下

            $time = time();
            //当前学生的所有订单是否有未上完的课程
            $whea = ['endtime'=>['egt',$time],'schedulingid'=>['in',$schedulingarr],'delflag'=>['eq',1]];//不符合发送要求的
            $wheb = ['endtime'=>['egt',$time],'ordernum'=>['in',$ordernumarr],'delflag'=>['eq',1]];//不符合发送要求的
            //一对多
            if(!empty($schedulingarr)){
                $rea = $teachtimeobj->getAllisnull($whea);
            }

            //一对一
            if(!empty($ordernumarr)){
                $reb = $teachtimeobj->getAllisnull($wheb);
                $lllr[] = !empty($reb)?$v:'';
                foreach($ordernumarr as $key => $val){
                    //$whebs = ['endtime'=>['egt',$time],'ordernum'=>['eq',$val]];
                    $whebm = ['ordernum'=>['eq',$val],'delflag'=>['eq',1]];
                    //$rebs = $teachtimeobj->getAllisnull($whebs);
                    $rebm = $teachtimeobj->getAllisnull($whebm);
                    if(empty($rebm)){
                        //未预约课程
                        $lllr[] = $v;
                    }
                    //$rrrb [] = empty($rebs)?:;//存储不符合发送要求的数据
                }
                $lllr = array_filter($lllr);//不符合发送要求的订单数量
            }


            if($rea || count($lllr)>0){
                //$allarr[] = $v;//下过单的学生且课未上完id
                array_splice($copystuidarr,$k,1);
            }
        }

        foreach($copystuidarr as $k => $v){
            //$contentarr[$k]['uid'] = !empty($v)?'1'.$v:'';
            $contentarr[$k]['content'] = '新课程开课了，快去报名吧，';
            $contentarr[$k]['userid'] = !empty($v)?'3'.$v:'';
            $contentarr[$k]['title'] = '推荐消息';
            $contentarr[$k]['usertype'] = 3;//学生
            $contentarr[$k]['externalid'] = $schedulingid;//班级id
        }
        $msgobj = new Messages();
        $type = 6;
        $msg = $msgobj->addMessagearr($contentarr,$type);
        return $msg;

    }

    /*
     * 教师登陆后的机构邀请列表
     * @Author wangwy
     */
    public function invitationList($teacherid){
        $inviteobj = new Teacherinvite();
        $organarr = $inviteobj->invitationGroupList($teacherid);
        $organidarr = array_column($organarr,'organid');
        $organobj = new Organ();
        $organnamearr = $organobj->getOrganname($organidarr);
        //$organauthobj = new Organauthinfo;
        $allacount = new \app\teacher\model\Allaccount();
        $arr = [];
        foreach($organarr as $k => $v){
            if(empty($organnamearr[$v['organid']])){
                $mm = $allacount->getUsername($v['organid']);
                $arr[$k]['organname'] = $mm['username'];
            }else{
                $arr[$k]['organname'] = $organnamearr[$v['organid']];
            }
            $arr[$k]['organid'] = $v['organid'];
            $arr[$k]['addtime'] = $v['addtime'];
            $arr[$k]['viewed'] = $v['viewed'];
            $arr[$k]['id'] = $v['id'];
        }
        $arr = array_values($arr);
        if($arr){
            return return_format($arr,0,lang('success'));
        }else{
            return return_format($arr,20002,lang('20002'));
        }

    }
    /*
     * 批量删除消息的接口
     * @Author wangwy
     */
    public function delMsglist($idarr){
        $msgobj = new Message();
        $where = ['id'=>['in',$idarr]];
        $data = ['delflag'=>0];
        $rec = $msgobj->updateMessage($data,$where);
        if($rec){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',20538,lang('20538'));
        }

    }

    /** 向学生群发消息
     * @param $studentarr
     * @param $title
     * @param $content
     * @return array
     */
    public function noticeAll($studentarr,$title,$content){
        if(!is_array($studentarr)){
            return return_format('', 20005, lang('20005'));
        }
        if(empty($title) || empty($content)){
            return return_format('', 20005, lang('20005'));
        }
        $msgobj = new Messages();
        foreach($studentarr as $k => $v){
            $contentarr[$k]['content'] = $content;
            $contentarr[$k]['userid'] = $v;
            $contentarr[$k]['title'] = $title;
            $contentarr[$k]['usertype'] = 3;//学生
            // $contentarr[$k]['externalid'] = $schedulingid;//班级id
        }
        $type =11;
        $msg = $msgobj->addMessagearr($contentarr,$type);
        return return_format($msg,0,lang('success'));
    }



    

}