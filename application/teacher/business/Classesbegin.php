<?php
namespace app\teacher\business;
use app\teacher\model\Curriculum;
use app\teacher\model\Category;
use app\teacher\model\Unit;
use app\teacher\model\Period;
use app\teacher\model\Scheduling;
use app\teacher\model\OrderManage;
use app\teacher\model\TeacherInfo;
use app\teacher\model\Teachertime;
use app\teacher\model\ToteachTime;
use app\teacher\model\Lessons;
use app\teacher\model\Organ;
use app\teacher\model\Filemanage;
use app\teacher\model\Unitdeputy;
use think\Validate;
/**
 * 课程业务逻辑层
 */
class Classesbegin
{
    protected $WKURL = 'http://global.talk-cloud.net';

    /*
    * @ 开课列表
    * @Author wangwy
    * @param $where 查询条件
    * @param $pagenum 每页显示行数
    * @param $limit 查询页数
    **/
    public function getSchedulinglists($data,$pagesize){
        $scheduling = new Scheduling();
        $list = $scheduling->getClassesList($data,$pagesize);
        if($list['data']){
            $category = new Category();
            $order = new Ordermanage();
            $classstatus = ['0'=>'未招生','1'=>'招生中','2'=>'招生中','3'=>'已满员','4'=>'授课中','5'=>'已结束',6=>'已超时'];

            foreach ($list['data'] as $key => &$val){
                // 处理分类
                $val['categoryname'] = $category->getCategoryName(explode('-',$val['categorystr']));
                $val['payordernum'] = $order->getPaySchedulingCount($val['id']);
                // 开班状态的转义
                $val['classstatusStr'] = $classstatus[$val['classstatus']];
            }
            return return_format($list,0,lang('success'));
        }else{
            return return_format([],0,lang('success'));
        }
    }



   /**
    * 获取机构开课老师
    * @return [type] [description]
    */
    public function getTeacherList($organid){
        $teacher = new TeacherInfo();
        $list = $teacher->getLists($organid);
        if($list){
            return return_format($list,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }


    /**
     * 开课、编辑
     * @author jcr
     * $data 开课数据源
     */
     public function addEdit($data){
         $scheduling = new Scheduling();
         $curriculum = new Curriculum();
         $curriculumInfo = $curriculum->getSelectId($data['curriculumid']);
         if(!$curriculumInfo){
             return return_format('',10061,'您所选的课程发生异常、或不存在');
         }

         $infolist = [];
         if($data['type']==1){
           //查看老师的可预约时间是否设置
         $lable = new Teachertime();
         if($lable->findWeekCount($data['organid'],$data['teacherid'])==0){
           return return_format('',10057,'该老师未设置一对一可预约时间，不能开课');
         }
             //一对一时 验证数据较少
             $validate = new Validate($scheduling->rule,$scheduling->message);
         }else{
             $validate = new Validate($scheduling->rulemax,$scheduling->messagemax);
         }

         if(!$validate->check($data)){
             return return_format('',10057,$validate->getError());
         }

         if($data['type']!=1){
             // 在不为1对1的情况、
             if(!$data['list']){
                 return return_format('',10058,'数据异常');
             }

             $keyArr = [];
             for($i=0;$i<48;$i++){
                $keyArr[] = $i;
             }

             // 获取对应的老师空余时间
             // $teachertime = new Teachertime();
             //$teacheList = $teachertime->findWeekMark($this->organid,$data['teacherid']);
             // $teacheArr = [];
             // foreach ($teacheList as $k => $v) {
             //     $teacheArr[$v['week']] = explode(',',$v['mark']);
             // }
             // 获取老师课程安排时间
             $toteach = new Toteachtime();
             $timeArr = array_column($data['list'],'intime');
             $where = [];
             foreach ($timeArr as $key => $val) {
                 $where[$key]['intime'] = $val;
                 $where[$key]['teacherid'] = $data['teacherid'];
                 $where[$key]['delflag'] = 1;
             }

             // 如果是编辑 不过滤当前时间
             if(isset($data['id'])){
                 $toteachlist = $toteach->getTimekey($where,$data['id']);
             }else{
                 $toteachlist = $toteach->getTimekey($where,false);
             }

             // var_dump($toteachlist);
             $inArrs = [];
             foreach ($toteachlist as $k => $val) {
                 $inArrs[$val['intime']][] = $val['timekey'];
             }
             //取出对应的时间占用的时间段
             foreach ($inArrs as $key => $value) {
                 $inArrs[$key] = explode(',',implode(',',$value));
             }


             $timeInArr = [];
             foreach($data['list'] as $k => $v){
                 if(!isset($v['timekey']) || !isset($v['intime'])){
                     return return_format('',10059,'第'.numtochr($v['unitsort'],true).'单元下、第'.numtochr($v['periodsort']).' 课时请选择排课时间');
                     break;
                 }
                 if(!$v['timekey']){
                     return return_format('',10060,'第'.numtochr($v['unitsort'],true).'单元下、第'.numtochr($v['periodsort']).' 课时请选择排课时间');
                     break;
                 }
                 // 前端传输数组

                 $explodearr = explode(',',$v['timekey']);

                 // 根据最小时间自己计算数组
                 $explodearr = array_series($explodearr[0],$scheduling->getConfigKey($data['type']));
                 $data['list'][$k]['timekey'] = implode(',',$explodearr);


                 //存储结束时间
                 if(count($explodearr)==1){
                     // 数组大小为1时 不存在跨天问题
                     $datatime = strtotime($v['intime'].' '.get_time_key($explodearr[0]));
                 }else{
                     // 数组大于1 可能存在跨天问题
                     if($explodearr[0]>$explodearr[count($explodearr)-1]){
                         //跨天了 起始键 大于 终止键
                         $datatime = strtotime($v['intime'].' '.get_time_key($explodearr[count($explodearr)-1]))+86400;
                     }else{
                         $datatime = strtotime($v['intime'].' '.get_time_key($explodearr[count($explodearr)-1]));
                     }
                 }

                 // 当前时间必须比前一课时时间大
                 if(count($timeInArr)>0){
                     if(strtotime($v['intime'].' '.get_time_key($explodearr[0]))<$timeInArr[count($timeInArr)-1]){
                         return return_format('',10059,'第'.numtochr($v['unitsort'],true).'单元下、第'.numtochr($v['periodsort']).'课时 预约时间要大于前一课时时间');
                         break;
                     }
                 }

                 $timeInArr[] = $datatime;


                 // 根据日期去获取对应的空余时间
                 if(isset($inArrs[$v['intime']])){
                     // $sparetime = array_diff($teacheArr[date('w',strtotime($v['intime']))],$inArrs[$v['intime']]);
                     $sparetime = array_diff($keyArr,$inArrs[$v['intime']]);
                 }else{
                     // $sparetime = $teacheArr[date('w',strtotime($v['intime']))];
                     $sparetime = $keyArr;
                 }


                 // 对比现提交时间和老师空余时间
                 if(array_diff($explodearr,$sparetime)){
                     return return_format('',10060,'第'.numtochr($v['unitsort'],true).'单元下、第'.numtochr($v['periodsort']).'课时 老师时间被占用');
                     break;
                 }

                 // 处理到此 该排查的都排查了 end
             }
         }
         // 添加时价格限制
         // if(isset($data['id'])){
         //     $price = $curriculumInfo['price'] * $curriculumInfo['periodnum'];
         //     if($data['totalprice']<$price){
         //         return return_format('',10062,'开课价格不能少于课时标准价之和'.$price);
         //     }
         // }

         // exit();
         if(isset($data['id'])){
             $info = $scheduling->edits($data,$curriculumInfo);
         }else{
             $info = $scheduling->adds($data,$curriculumInfo);
         }
         return return_format('',$info['code'],$info['info']);
         // var_dump($info);

     }

    /**
    * [getTimeOccupy 开课编辑]
    * @param  [type] $data [description]
    * @return [type]       [description]
    */
    public function addClassEdit($data,$teacherid){
        $data['teacherid'] = $teacherid;
        //如果开班类型不为一对一，则导入数据
        foreach ($data['list'] as $ky => $val) {
            $data['list'][$ky]['teacherid'] = $teacherid;
        }
//        if ($data['type'] != 1) {
//
//        }elseif ($data['type'] == 1){
//            $scheobj = new scheduling;
//            $sche = $scheobj->getOneScheinfo($data['curriculumid'],$teacherid);
//            //如果能够从排课表查询到一对一数据，则无法进行开课
//            if(!empty($sche)){
//                return return_format('',20018,lang('20018'));
//            }
//
//        }
        $add = new \app\admin\business\Classesbegin;
        $return = $add->addEdit($data);
        return $return;
    }

    /**
     * [getTimeOccupy 获取对应老师的占用时间]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getTimeOccupy($data){
        if(!isset($data['teacherid'])&&$data['teacherid']) return return_format('',20007,lang('20007'));
        if(!isset($data['intime'])&&$data['intime']) return return_format('',20008,lang('20008'));
        $data['delflag'] = 1;
        $toteach = new ToteachTime();
        $listTime = $toteach->getTimekey([$data],false);
        if($listTime){
            $listTime = explode(',',implode(',',array_column($listTime,'timekey')));
            return return_format($listTime,0,lang('success'));
        }else{
            return return_format([],0,lang('success'));
        }
    }
    /**
     * [enrollStudent app教师端开课编辑]
     * @return [type] [description]
     */
     public function editClassforapp($data){
         //暂停开课
         self::getTimeOccupy($data);
         //更改班级名称（大班课，小班课）
         //更改课程价格
     }


    /**
     * [enrollStudent 暂停开课]
     * @return [type] [description]
     */
    public function enrollStudent($data){
        $scheduling = new Scheduling();
        $orders = new OrderManage();
        //获取开课详情
        //$organid = 1;
        $info = $scheduling->getInfoId($data['id']);
        if(!$info) return return_format('',20009,lang('20009'));
        //if($info['type']==1) return return_format('',10071,'一对一的课程无法进行暂停操作');
        if($info['status']==$data['status'] && $data['status']==0) return return_format('',20010,lang('20010'));
        if($info['status']==$data['status'] && $data['status']!=0) return return_format('',20011,lang('20011'));
        if($info['type'] !=1 && $orders->getSchedulingIdCount($data['id'],$data['organid'])>0){
            return return_format('',20012,lang('20012'));
        }

        $ids = $scheduling->enrollStudent($data);
        if($ids){
            return return_format('',0,lang('success'));
        }else{
            $returninfo = $data['status']==0?lang('20013'):lang('20014');
            $returncode = $data['status']==0?20013:20014;
            return return_format('',$returncode,$returninfo);
        }
    }

    /**
     * [deleteScheduling 删除开课信息]
     * @return [type] [description]
     */
    public function deleteScheduling($data){
        $scheduling = new Scheduling();
        $orders = new Ordermanage();
        $info = $scheduling->getInfostatus($data['id']);
        //在课程已经结束的情况下可以直接删除
        if ($info['classstatus']!=5){
            //已经有人报名的情况下不可以删除
            if($orders->getSchedulingIdCount($data['id'])>0){
                return return_format('',20015,lang('20015'));
            }
        }
        $infos = $scheduling->deleteScheduling($data,$info['type']);
        return return_format('',$infos['code'],$infos['info']);;
    }



    /**
     * [getSchedulingInfo 开班详情返回数据]
     * @param  [type] $data [参数源]
     * @return [type]       [description]
     */
    public function getSchedulingInfo($data,$teacherid){
        $id = isset($data['id'])?$data['id']:false;
        $scheduling = new Scheduling();
        $info = $scheduling->onetooneClass($data['curriculumid'],$teacherid,$data['type'],$id);
        if(!$info) return return_format('',20006,lang('20006'));
        $info['curriculumidSumPrice'] = $info['periodnum'] * $info['price'];
        // var_dump($info);
        if($data['type']!=1){

            if($id){
                $teacher = new TeacherInfo();
                $lessons = new Lessons();
                $unitdeputy = new Unitdeputy();

                // 获取课程表的单元和课时
                $inperiodarr = $unitdeputy->getLists($info['curriculumid'],$info['id']);
                $inlist = $lessons->getInLists($info['id'],$info['teacherid']);

                foreach ($inperiodarr as $k => $v) {
                    foreach ($inlist as $key => $val) {
                        if($v['id']==$val['unitid']){
                            // 处理数据结构
                            $val['unitsort'] = $v['unitsort'];
                            $val['timestr'] = $val['intime'].' '.get_time_key(explode(',',$val['timekey'])[0]);
                            $val['teachername'] = $teacher->getTeacherId($val['teacherid'],'teachername,imageurl')['teachername'];
                            $inperiodarr[$k]['list'][] = $val;
                        }
                    }
                }

                // var_dump($inperiodarr);
                $info['list'] = $inperiodarr;
            }else{
                // 获取课程表的单元 和 开课表的课时
                $period = new Period();
                $unit = new Unit();

                $inperiodarr = $unit->getLists($data['curriculumid']);
                $perList = $period->getIdsLists($data['curriculumid']);
                foreach ($inperiodarr as $k => $v) {
                    foreach ($perList as $key => $val) {
                        if($v['id']==$val['unitid']){
                            $val['unitsort'] = $v['unitsort'];
                            $inperiodarr[$k]['list'][] = $val;
                        }
                    }
                }
                $info['list'] = $inperiodarr;
            }
        }

        if(isset($info['teacherid'])){
            $teacher = new Teacherinfo();
            $info['teachername'] = $teacher->getTeacherId($info['teacherid'],'teacherid,teachername')['teachername'];
        }

        if($info){
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }



    /**
     * [addFiles 添加文件夹]
     * @param [type] $data [description]
     */
    public function addFiles($data){
        $filemanage = new Filemanage();
        $info = $filemanage->addFile($data);
        if($info['code']==0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',24001,lang('24001'));
        }
    }

    /**
     * [getFileList 文件夹列表 和 资源列表(机构文件和教师个人文件)]
     * @param  [type] $data [description]
     **/
    public function getFileList($data){
        $filemanage = new Filemanage();
        //usetype 1:录制件 2：普通课件 0:所有课件
        $sdata = $data;
        if($data['usetype'] == 0){
            $sdata['usetype'] = ['in',[1,2]];
            $ddata['usetype'] = ['in',[1,2]];
        }else{
            $ddata['usetype'] = $data['usetype'];
        }
        //如果是获取文件夹
        if($data['fatherid'] == 0){
            $sdata['usetype'] = 0;
        }
        //不分页，将pagesize设置1000
        $info = $filemanage->getFileList($sdata,$data['pagenum'],20);
        if($info['data']){
            $region = new Organ();
            $dockinglog = new \app\admin\model\Dockinglog();
            $newdata['authKey'] = $region->getOrganid()['roomkey'];
            foreach ($info['data'] as $k => &$val) {
                //$val['usetype'] = isset($usetype[$val['usetype']]) ? $usetype[$val['usetype']] : '-';
                $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
                $val['juniorcount'] = $val['fatherid']==0?$filemanage->getFileCount(['fatherid'=>$val['fileid'],'usetype'=>$ddata['usetype'],'delflag'=>1]):0;
                $ar = explode('.', $val['showname']);
                $ext = strtolower($ar[count($ar)-1]);
                if($val['usetype'] == 2 && ($ext == 'ppt' || $ext == 'pptx')){
                    $newdata['fileId'] = $val['relateid'];
                    $infos = curl_postFile($this->WKURL . '/WebAPI/getDynamicPptInfo', $newdata);
                    $infos = json_decode($infos, true);
                    if ($infos['result'] != 0) {
                        //记录异常
                        $datalog = [
                            'dockingurl' => $this->WKURL . '/WebAPI/getDynamicPptInfo',
                            'code' => $infos['result'],
                            'content' => json_encode($newdata)
                        ];
                        $dockinglog->addEdit($datalog);
                        $val['previewpath'] = '';
                    } else {
                        $val['previewpath'] = $infos['path'];
                    }
                }else{
                    $val['previewpath'] = config('param.http_name') . $val['cosurl'];
                }
            }
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }
    /*
     * [getFileList 文件夹列表 和 资源列表(机构文件和教师个人文件)]
     * @param  [type] $data [description]
     */
    public function getFileListforapp($data){
        $filemanage = new Filemanage();
        $info = $filemanage->getFileList($data,$data['pagenum'],20);
        if($info['data']){
            $shownamearr = array_column($info['data'],'showname');//获取文件夹名字
            $shownamearr = array_unique($shownamearr);//数组去重
            foreach ($info['data'] as $k => &$val) {
                $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
                $val['juniorcount'] = $val['fatherid']==0?$filemanage->getFileCount(['fatherid'=>$val['fileid'],'delflag'=>1]):0;
                // $val['addtimestr'] = date('Y-m-d H:i:s',$val['addtime']);
            }
            $info['shownamearr'] = $shownamearr;
               // print_r($info);
               // exit();
               // //判断1是做展示用，2是用作关联课件时展示
               // if ($data['condition'] == 2) {
               //   //搜索lessons
               //   $less = new Lessons;
               //   $code = $less->getCourseware([''=>$data['']]);
               //   foreach ($info['data'] as $k => $vl) {
               //     // code...
               //   }
               // }
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }


    /**
     * [deleteFile 删除课件]
     * @param  $fileid [素材id]
     * @return [type] [description]
     */
    public function deleteFile($data){
        $filemanage = new Filemanage();
        $info = $filemanage->addFile($data);
        if($info['code']==0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',$info['code'],'课件操作失败');
        }
    }

    /**
     * [getSchedulCurriinf 课程详情返回数据]
     * @param  [type] $data [参数源]
     * @return [type]       [description]
     */
    public function getSchedulCurriinf($data,$teacherid){
        $id = isset($data['id'])?$data['id']:false;
        $scheduling = new Scheduling();
        $categoryobj = new Category();
        $orderobj = new OrderManage();
        $info = $scheduling->showAllcurribyschid($id);
        if(!$info) return return_format('',20002,lang('20002'));
        $mcarr = explode( '-',$info['categorystr']);

        $info['categoryname'] = $categoryobj->getCategoryName($mcarr);
        $info['buysum'] = $orderobj->getPaySchedulingCount($id);
        $info['addtime'] = date('Y-m-d H:i:s',$info['addtime']);

        $lessons = new Lessons();
        $unitdeputy = new Unitdeputy();
        $totimeobj = new ToteachTime();
        // 获取课程表的单元和课时
        $inperiodarr = $unitdeputy->getLists($info['curriculumid'],$id);
        $inlist = $lessons->getAllInLists($id ,$teacherid);
        $lessonsidarr = array_column($inlist,'id');//lessonsid
        $res = $totimeobj->getTimeall($lessonsidarr);
//        print_r($inlist);
//        exit();
        foreach($inlist as $k => $v){
            $inlist[$k]['teacherid'] = isset($res[$v['id']])?$res[$v['id']]['teacherid']:'';
            $inlist[$k]['intime'] = isset($res[$v['id']])?$res[$v['id']]['intime']:'';
            $inlist[$k]['timekey'] = isset($res[$v['id']])?$res[$v['id']]['timekey']:'';

        }
        foreach ($inperiodarr as $k => $v) {
            foreach ($inlist as $key => $val) {
                if($v['id']==$val['unitid']){
                    // 处理数据结构
                    $val['unitsort'] = $v['unitsort'];
                    $val['timestr'] = isset($val['timekey']) && !empty($val['timekey'])?$val['intime'].' '.get_time_key(explode(',',$val['timekey'])[0]):'';
                    //$val['teachername'] = $teacher->getTeacherId($val['teacherid'],'teachername,imageurl')['teachername'];
                    $inperiodarr[$k]['list'][] = empty($val)?'':$val;
                }
            }
        }

        $info['list'] = $inperiodarr;
        if(isset($info['teacherid'])){
            $teacher = new Teacherinfo();
            $info['teachername'] = $teacher->getTeacherId($info['teacherid'],'teacherid,teachername')['teachername'];
        }

        if($info){
            return return_format($info,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }




}
