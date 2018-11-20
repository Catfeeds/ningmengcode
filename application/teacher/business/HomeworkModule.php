<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 20:48
 */

namespace app\teacher\business;
use app\teacher\model\Curriculum;
use app\teacher\model\ExerciseSubject;
use app\teacher\model\ExercisesubjectOptions;
use app\teacher\model\Lessons;
use app\teacher\model\OrderManage;
use app\teacher\model\Period;
use app\teacher\model\StudentHomework;
use app\teacher\model\SchedulingLessonInfo;
use app\teacher\model\Scheduling;
use app\teacher\model\StudentInfo;
use app\teacher\model\StudentHomeworkAnswer;
use app\teacher\model\ApplyschedulingRecord;
use app\teacher\model\ApplylessonsRecord;

class HomeworkModule
{
    /** 布置作业
     * @param $schedulingid
     * @param $lessonsid
     * @param $starttime
     * @param $endtime
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function arrangeHomework($schedulingid,$lessonsid,$starttime,$endtime,$teacherid){
        $scheobj = new Scheduling();
        $schehowobj = new SchedulingLessonInfo;
        $stuworkobj = new StudentHomework();

        $curriculumid = $scheobj->getAlllist(['id'=>$schedulingid],'curriculumid');
        $curriculumid = $curriculumid['curriculumid'];
        $time = time();
        $ce = $schehowobj->getAllfind(['classid'=>$schedulingid,'lessonid'=>$lessonsid],'id');//同一课时不能重复留作业
        if(!$ce){
            $re = $schehowobj->addHomework(['classid'=>$schedulingid,'courseid'=>$curriculumid,'lessonid'=>$lessonsid,'starttime'=>$starttime,'endtime'=>$endtime,'addtime'=>$time,'teacherid'=>$teacherid]);
            $stuarr = $this->getAllstuid($teacherid,$schedulingid,$lessonsid);
            //dump($stuarr);
            if($stuarr){
                foreach ($stuarr as $k =>$v ){
                    if(!$stuworkobj->addHomework(['classid'=>$schedulingid,'courseid'=>$curriculumid,'lessonid'=>$lessonsid,'studentid'=>$v['studentid'],'teacherid'=>$teacherid])){
                        return return_format('',20002,lang('20002'));
                    }
                }
            }

        }else{
            return return_format('',20002,'同一课时不能重复留作业');
        }
        //$re = $schehowobj->upHomework(['classid'=>$schedulingid,'lessonid'=>$lessonsid],['starttime'=>$starttime,'endtime'=>$endtime]);
        if($re){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',22019,lang('22019'));
        }
    }

    /**
     * @param $teacherid
     * @param $schedulingid
     * @param $lessonsid
     * @return array
     */
    public function getAllstuid($teacherid,$schedulingid,$lessonsid){
        $orderobj = new OrderManage;
        $applayobj = new ApplyschedulingRecord;
        $applesonobj = new ApplylessonsRecord;
        $reA = $orderobj->getStudenAlllist(['teacherid'=>$teacherid,'schedulingid'=>$schedulingid,'orderstatus'=>20]);
        //到调班表中查询改班级数量
        $reB = $applayobj->getAlschedulinglist(['newschedulingid'=>$schedulingid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reC = $applayobj->getAlschedulinglist(['oldschedulingid'=>$schedulingid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');//调走的学生数

        $reD = $applesonobj->getAltlessonslist(['newlessonsid'=>$lessonsid,'newteacherid'=>$teacherid,'status'=>1],'studentid');
        $reE = $applesonobj->getAltlessonslist(['oldlessonsid'=>$lessonsid,'oldteacherid'=>$teacherid,'status'=>1],'studentid');
        $rePlus = array_merge($reA,$reB,$reD);
        $redel  = array_merge($reC,$reE);
        foreach ($rePlus as $k =>$v){
            if(in_array($v,$redel)){
                array_splice($replus,$k,1);
            }
        }
        $receive = $rePlus;
        return $receive;
    }

    /**
     * @param $schedulingid
     * @param $teacherid
     * @return array
     */
    public function getChoicelist($schedulingid,$periodid,$lessonid,$teacherid){
        //该老师的班级选项列表
        if($schedulingid){
            $lessonobj = new Lessons();
            //$periodobj = new Period();
            //$list = $lessonobj->getAllcolumn(['schedulingid'=>$schedulingid],'id,periodname');
            $list = $lessonobj->getAllcolumn(['schedulingid'=>$schedulingid],'periodid,id,periodname');
        }elseif(!$schedulingid){
            $schehowobj = new Scheduling;
            $list = $schehowobj->getAllcloumn(['teacherid'=>$teacherid,'type'=>2,'delflag'=>1,'classstatus'=>['in',[4,5]]],'id,gradename');
        }
        if($schedulingid && $periodid){
            $exerobj = new ExerciseSubject();
            $reA = $exerobj->showSubjectlist(['periodid'=>$periodid,'type'=>[3,4]],'id,type,name,imageurl,score,correctanswer,analysis');
            $reB = $exerobj->showSubjectoptlist(['periodid'=>$periodid,'type'=>[1,2]],'s.id,subjectid,type,name,imageurl,score,optionname,correctanswer');
            $subjectarr = array_column($reB,'subjectid');
            $subjectarr = array_unique($subjectarr);
            foreach ($reB as $k =>$v){
                foreach ($subjectarr as $ky =>$vl){
                    if($vl==$v['subjectid']){
                        $reM[$vl]['subjectid'] = $v['subjectid'];
                        $reM[$vl]['type'] = $v['type'];
                        $reM[$vl]['name'] = $v['name'];
                        $reM[$vl]['imageurl'] = $v['imageurl'];
                        $reM[$vl]['score'] = $v['score'];
                        $reM[$vl]['optionname'][] = $v['optionname'];
                        $reM[$vl]['correctanswer'] = $v['correctanswer'];
                    }
                }
            }
            $reM = isset($reM)?$reM:[];
            $reS = array_merge($reA,$reM);
            foreach ($reS as $k =>$v){
                $list['exercise'][$v['type']][] = $v;
            }
            $list['sum'] = $this->getAlterRecordcount($teacherid,$schedulingid,$lessonid);
//            $opt = $optionobj->showOptionlist([,'delflag'=>0],'subjectid,optionname');
//            foreach ($list['exercise'] as $k =>$v){
//                if($v['type']==1||$v['type']==2){
//                    $list['exercise']['option'] =$opt[$v['id']]['optionname'];
//                }
//            }
        }
        if($list){
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }
    }
    /**以班级为单位统计作业列表
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param null $curriculumid
     * @param null $schedulingid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scheHomeworkList($teacherid,$pagenum,$pagesize,$coursename = null,$schedulingname=null){
        //首先统计，一共有多少个班级留了作业
        $schehowobj = new SchedulingLessonInfo;
        $orderobj = new OrderManage();
        $scheduobj = new Scheduling();
        $courseobj = new Curriculum();
        $applayobj = new ApplyschedulingRecord;
        if(!empty($schedulingname)){
            $schedulingidarr = $scheduobj->getAllcloumn(['gradename'=>['like','%'.$schedulingname.'%']],'id');
        }else{
            $schedulingidarr = null;
        }
        if(!empty($coursename)){
            $curriculumidarr = $courseobj->getAllist(['coursename'=>['like','%'.$coursename.'%']],'id');
        }else{
            $curriculumidarr = null;
        }
        $classarr = $schehowobj->getClassarr($teacherid,$pagenum,$pagesize,$curriculumidarr,$schedulingidarr);
        foreach($classarr['data'] as $k =>$v){
            //获取该班级所有的
            //$re[$k]['stunum'] = $stuhowobj->getStunum($v['teacherid'],$v['course_id'],$v['class_id']);
            $numA = $orderobj->getOrderListCount(['teacherid'=>$teacherid,'schedulingid'=>['in',$v['classid']],'orderstatus'=>20]);
            //到调班表中查询改班级数量
            $numB = $applayobj->getAlschedulingCount(['newschedulingid'=>$v['classid'],'status'=>1]);
            $numC = $applayobj->getAlschedulingCount(['oldschedulingid'=>$v['classid'],'status'=>1]);//调走的学生数
            $classarr['data'][$k]['stunum'] = $numA+$numB-$numC;//订单班级加上调班学生的数量
            $classarr['data'][$k]['worknum'] = $schehowobj->getWorknum($teacherid,$v['courseid'],$v['classid']);
            $mm = $scheduobj->getAlllist(['id'=>$v['classid']],'curriculumname,gradename');
            $classarr['data'][$k]['coursename'] = $mm['curriculumname'];
            $classarr['data'][$k]['classname'] = $mm['gradename'];
        }
        if($classarr){
            return return_format($classarr,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }

    /** 以课时为单位统计作业
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param $schedulingid
     * @param $curriculumid
     * @param $lessonsid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function showLesssonsHomeList($teacherid,$pagenum,$pagesize,$schedulingid,$curriculumid,$periodname){
        $stuhowobj = new StudentHomework;
        $schehowobj = new SchedulingLessonInfo;
        $scheduobj = new Scheduling;
        $lessonsobj = new Lessons;
        //$applayobj = new ApplyschedulingRecord;
        if(!empty($periodname)){
            $lessonsidarr = $lessonsobj->getAllcolumn(['periodname'=>['like','%'.$periodname.'%']],'id');
        }else{
            $lessonsidarr = null;
        }
        //dump($lessonsidarr);
        $re = $schehowobj->lessonHomeworkList($teacherid,$pagenum,$pagesize,$schedulingid,$curriculumid,$lessonsidarr);
        //print_r($re);
        //dump($re);
        foreach($re['data'] as $k =>$v){
            //获取该班级所有的
            //$re[$k]['stunum'] = $stuhowobj->getStunum($v['teacherid'],$v['course_id'],$v['class_id']);
            //$re[$k]['stunum'] = $orderobj->getOrderListCount(['teacherid'=>$teacherid,'schedulingid'=>$schedulingid,'type'=>1]);//学生数量
            $re['data'][$k]['stunum'] = $this->getAlterRecordcount($teacherid,$schedulingid,$v['lessonid']);//学生数量
            //$re[$k]['worknum'] = $schehowobj->getWorknum($v['teacherid'],$v['course_id'],$v['class_id']);
            $re['data'][$k]['worknum'] = $stuhowobj->getWorknum($teacherid,$v['courseid'],$v['classid'],$v['lessonid']);//已提交作业数量
            $mm = $scheduobj->getAlllist(['id'=>$v['classid']],'curriculumname,gradename');
            $re['data'][$k]['coursename'] = $mm['curriculumname'];
            $re['data'][$k]['classname'] = $mm['gradename'];
            $newpername = $lessonsobj->getAllfind(['id'=>$v['lessonid']],'periodname');
            $re['data'][$k]['periodname'] = $newpername['periodname'];
            $periodarr = $lessonsobj->getAllcolumn(['id'=>$v['lessonid']],'periodid');
            $re['data'][$k]['periodid'] = !empty($periodarr[0])?$periodarr[0]:'';
        }
        if($re){
            return return_format($re,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }
    }

    /** 学员交作业明细
     * @param $teacherid
     * @param $pagenum
     * @param $pagesize
     * @param $reviewstatus
     * @param $curriculumid
     * @param $schedulingid
     * @param $lessonsid
     * @param $studentid
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function stuHomeworkList($teacherid,$pagenum,$pagesize,$reviewstatus,$curriculumid,$schedulingid,$lessonsid,$studentname){
        $stuhomobj = new StudentHomework();
        $stuobj = new StudentInfo();
        if(!empty($studentname)){
            $studentid = $stuobj->getAllfind(['nickname'=>['like','%'.$studentname.'%']],'id');
            $studentid = $studentid['id'];
        }else{
            $studentid = null;
        }
        $list = $stuhomobj->homeworkList($teacherid,$pagenum,$pagesize,$reviewstatus,$curriculumid,$schedulingid,$lessonsid,$studentid);
        $stuidarr = array_column($list['data'],'studentid');
        $stunamearr =  $stuobj->getStudentnameById($stuidarr);
        foreach($list['data'] as $k =>$v){
            $list['data'][$k]['nickname']= $stunamearr[$v['studentid']];
        }
        if($list){
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }

    /** 学生作业详情
     * @param $schedulingid
     * @param $curriculumid
     * @param $lessonsid
     * @param $studentid
     * @return array
     */
    public function showExerciselist($schedulingid,$curriculumid,$lessonsid,$studentid){
        //1.展示作业中的习题并按照类型分组
        $exerobj = new ExerciseSubject();
        $stuanswerobj = new StudentHomeworkAnswer();
        $optionobj = new ExercisesubjectOptions();
        $lessobj = new Lessons();
        //$list =  $lessobj->
        $list = $exerobj->showExerciselist($curriculumid,$lessonsid);
        //$list = $exerobj->showSubjectoptlist(['periodid'=>$lessonsid],'id,type,name,imageurl,analysis,correctanswer,score');
        //2.按照当前学生id得到的答案整合到该习题中
        $subjectidarr = array_column($list,'id');
        $stuanswer = $stuanswerobj->showStuAnswer($schedulingid,$lessonsid,$subjectidarr,$studentid);
        $total = [];
        foreach($list as $k =>$v){
            $total[] = $list[$k]['id'];//将选择题的id记录下来
            $list[$k]['answer'] = isset($stuanswer[$v['id']]['answer'])?$stuanswer[$v['id']]['answer']:'';
            //$list[$k]['ownscore'] = isset($stuanswer[$v['id']]['score'])?$stuanswer[$v['id']]['score']:'';//实际得分
            $list[$k]['comment'] = isset($stuanswer[$v['id']]['comment'])?$stuanswer[$v['id']]['comment']:'';
            $list[$k]['workid'] = isset($stuanswer[$v['id']]['id'])?$stuanswer[$v['id']]['id']:'';
        }
        $tolist = $optionobj->showOptionlist(['subjectid'=>['in',$total]],'id,subjectid,optionname');
        $arr = array_column($tolist,'subjectid');
        $arr = array_unique($arr);
        foreach ($tolist as $k =>$v){
            foreach ($arr as $ky => $vl){
                //$ars = isset($v[$vl])?$v[$vl]:'';
                if($v['subjectid'] == $vl){
                    $ars[$vl][] = $v['optionname'];
                }
            }
        }
        foreach ($list as $k =>$v) {
            if($list[$k]['type']==1||$list[$k]['type']==2){
                $list[$k]['option'] = isset($ars[$v['id']])?$ars[$v['id']]:'';
            }
        }
        //3.对照标准答案对选择题进行判断对错
        //$numA = 0;//单选题正确数量
        //$numB = 0;//多选题正确数量
        $re = [];
        foreach($list as $k => $v){
            if($v['type'] == 1){
                //$list[$k]['isYF'] = $v['answer']==$v['correctanswer']?'Y':'F';
                if($v['answer']==$v['correctanswer']){
                    $list[$k]['isYF'] = 'Y';
                    //$numA += 1;
                }
                $list[$k]['stuscore'] = $v['answer']==$v['correctanswer']?$v['score']:0;//选择题分数
                //$m = $v['score'] != ''?$v['score']:0;//题分值
                //$re[1]['sum'] = $m*$numA;//选择题(单选)总分
                //$sumA = $m*$numA;//选择题(单选)总分
            }elseif($v['type'] == 2){
                if($v['answer']==$v['correctanswer']){
                    $list[$k]['isYF'] = 'Y';
                    //$numB += 1;
                }
                $list[$k]['stuscore'] = $v['answer']==$v['correctanswer']?$v['score']:0;//选择题分数
                //$m = $v['score'] != ''?$v['score']:0;//题分值
                //$re[2]['sum'] = $m*$numB;//选择题(多选)总分
                //$sumB = $m*$numB;//选择题(多选)总分
            }elseif($v['type'] == 3||$v['type'] == 4){
                $list[$k]['stuscore'] = isset($stuanswer[$v['id']]['score'])?$stuanswer[$v['id']]['score']:'';//实际得分
            }
        }
        //$re['sumA'] = !empty($sumA)?$sumA:'';
        //$re['sumB'] = !empty($sumB)?$sumB:'';
        foreach($list as $k => $v){
            $re[$v['type']][] = $v;
        }
        if($re){
            return return_format($re,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }

    /** 批阅作业
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     * @return array
     */
    public function showMarking($teacherid,$lessonsid,$studentid,$a,$b,$c,$d){
        //按格式输入所有题目的分数,$c :填空题，$d:作文题
        //$b = ['1'=>['workid'=>66,'score'=>44,'comment'=>'哈哈哈哈']
        //      '2'=>['workid'=>67,'score'=>23,'comment'=>'哈哈']]
        //1.整合评分表，对评分进行统计入表
        $list = array_merge($a,$b,$c,$d);
        $stuanswerobj = new StudentHomeworkAnswer();
        $stuworkobj = new StudentHomework();
        $lessonsobj = new Lessons();
        //$schehowobj = new SchedulingLessonInfo;
        //$orderobj = new OrderManage;

        $sum = 0;
        foreach($list as $k =>$v){
            $re = $stuanswerobj->updateStuScore($v['workid'],$v);
            $sum += $re?1:0;
        }
        $scorearr = array_column($list,'score');
        $score = array_sum($scorearr);//每个学生的总成绩
        //$avgscore = $score/count($list);//班级平均分
        $schedulingid = $lessonsobj->getAllfind(['id'=>$lessonsid],'schedulingid');
        $schedulingid = $schedulingid['schedulingid'];
//        dump(count($list));
        //判断学生总数
        if($sum == count($list)){
            //评分完成后，更改学生作业状态为已批阅
            $stuworkobj->updateMarking($lessonsid,$studentid,$score);
            //每次批阅完成一个学生后，检查是否所有作业都已提交，并且都已批阅
            $this->upStatus($teacherid,$schedulingid,$lessonsid);
            return return_format([],0,lang('success'));
        }else{
            return return_format('',20002,'相同分数请勿重复批阅');
        }
    }

    /** 改平均分和批阅状态
     * @param $teacherid
     * @param $schedulingid
     * @param $lessonid
     * @param $avgscore
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function upStatus($teacherid,$schedulingid,$lessonid){
        //每次批阅完成一个学生后，检查是否所有作业都已提交，并且都已批阅
        //$stuanswerobj = new StudentHomeworkAnswer();
        $stuworkobj = new StudentHomework();
        $schehowobj = new SchedulingLessonInfo;
        //$stunum = $this->getAlterRecordcount($teacherid,$schedulingid,$lessonid);//学生数量
        //$re[$k]['worknum'] = $schehowobj->getWorknum($v['teacherid'],$v['course_id'],$v['class_id']);
        $worknum = $stuworkobj->getmarkWorknum($teacherid,$schedulingid,$lessonid);//已提交作业数量并且已经被批阅的
        $list = $stuworkobj->getAllscore(['teacherid'=>$teacherid,'classid'=>$schedulingid,'lessonid'=>$lessonid]);
        $allscore = array_sum($list);
        if($worknum == 0){
            $avgscore = 0;
        }else{
            $avgscore = $allscore/$worknum;
            $avgscore = round($avgscore,2);
        }
        //附加逻辑，后所上交的作业全部被批阅后才能更改班级作业批阅状态
        $mknum = $stuworkobj->getViewnum(['teacherid'=>$teacherid,'classid'=>$schedulingid,'lessonid'=>$lessonid,'issubmited'=>1,'reviewstatus'=>0]);
        if($mknum == 0){
            $schehowobj->updateScheinfo($lessonid,$teacherid,$avgscore,1);
        }
        return return_format('',0,lang('success'));
    }

    /**
     * @param $teacherid
     * @param $schedulingid
     * @param $lessonsid
     * @return int|string|void
     */
    public function getAlterRecordcount($teacherid,$schedulingid,$lessonsid){
        $orderobj = new OrderManage();
        $applayobj = new ApplyschedulingRecord;
        $applesonobj = new ApplylessonsRecord;
        $numA = $orderobj->getOrderListCount(['teacherid'=>$teacherid,'schedulingid'=>$schedulingid,'orderstatus'=>20]);
        //到调班表中查询改班级数量
        $numB = $applayobj->getAlschedulingCount(['newschedulingid'=>$schedulingid,'newteacherid'=>$teacherid,'status'=>1]);
        $numC = $applayobj->getAlschedulingCount(['oldschedulingid'=>$schedulingid,'oldteacherid'=>$teacherid,'status'=>1]);//调走的学生数

        $numD = $applesonobj->getAltlessonsCount(['newlessonsid'=>$lessonsid,'newteacherid'=>$teacherid,'status'=>1]);
        $numE = $applesonobj->getAltlessonsCount(['oldlessonsid'=>$lessonsid,'oldteacherid'=>$teacherid,'status'=>1]);
        $num = $numA+$numB+$numD-$numC-$numE;
        return $num;
    }


    /** 作业统计表
     * @param $teacherid
     * @param $schedulingid
     * @param $curriculumid
     * @param $lessonid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function totalHomework($teacherid,$schedulingid,$curriculumid,$lessonid){
        $scheduobj = new Scheduling();
        $lessonsobj = new Lessons();
        $exerobj = new ExerciseSubject();
        $stuworkobj = new StudentHomework();
        $stuanswerobj = new StudentHomeworkAnswer();
        $schehowobj = new SchedulingLessonInfo;
        $mm = $scheduobj->getAlllist(['id'=>$schedulingid],'curriculumname,gradename');
        $newpername = $lessonsobj->getAllfind(['id'=>$lessonid],'periodname');
        $periodidarr = $lessonsobj->getAllfind(['id'=>$lessonid],'periodid');
        $periodid =  $periodidarr['periodid'];
        $stunum = $this->getAlterRecordcount($teacherid,$schedulingid,$lessonid);//学生数量
        $worknum = $stuworkobj->getmarkWorknum($teacherid,$schedulingid,$lessonid);//已提交作业数量并且已经被批阅的
        $avgscore = $schehowobj->getAllfind(['classid'=>$schedulingid,'lessonid'=>$lessonid,'teacherid'=>$teacherid],'avgscore');//平均分
        $subjectidarr = $exerobj->showSubjectlist(['courseid'=>$curriculumid,'periodid'=>$periodid,'type'=>[1,2]],'type,id');//该课时
        foreach ($subjectidarr as $k =>$v){
            $corrnum = $stuanswerobj->getHomeworkCount(['score'=>['neq',0],'subjectid'=>$v,'lessonid'=>$lessonid]);//当前这道题答题正确的学生数量
            $list['percent'][$k][$v]['percent'] = $stunum==0?0:round($corrnum/$stunum,2)*100;
            $list['percent'][$k][$v]['corrnum'] = $corrnum;
            $list['percent'][$k][$v]['$stunum'] = $stunum;
        }
        //dump($subjectidarr);
        $list['curriculumname'] = $mm['curriculumname'];
        $list['gradename'] = $mm['gradename'];
        $list['periodname'] = $newpername['periodname'];
        $list['avgscore'] = $avgscore['avgscore'];
        $list['homepercent'] = $stunum==0?0:round($worknum/$stunum,2);
        $list['worknum'] = $worknum;
        $list['stunum'] = $stunum;
        if($list){
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',20002,lang('20002'));
        }

    }

}