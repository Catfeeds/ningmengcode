<?php

namespace app\teacher\business;
use app\teacher\model\Scheduling;
use app\teacher\model\StudentPaylog;
use app\teacher\model\Coursecomment;
use app\teacher\model\OrderManage;
use app\teacher\model\TeacherInfo;
use app\teacher\model\Teachertagrelate;
use app\teacher\model\Organ;


//业务逻辑；教师主页展示

class AllteacherInfo{

    /**
     * 获取教师首页个人信息
     * @Author wangwy
     * @param $teacherid  老师id
     * @param $limit   取出多少条记录
     * @return array
     */
    public function getPersoninfo($teacherid,$limita,$limitb,$limitc){
        if (isset($teacherid)) {
            //教师头部个人信息
            // $where = [];
            // !empty($teacherid) && $where['teacherid'] = $teacherid;
            // !empty($organid) && $where['organid'] = $organid;
            $field = 'imageurl,nickname,sex,birth,country,province,city,profile,classesnum';
            $lista = new TeacherInfo;
            $data=$lista->getTeacherData($field,$teacherid);
            if (empty($data['birth'])) {
              $Age = '';
            }else{
              //根据当前时间获取年龄
              $Age = date('Y') - date('Y',$data['birth']) ;
            }
        
            //获取评论表中信息
            $listb= new Coursecomment;
            $listc = $listb->getComment($teacherid);

            $num = count($listc);//评分次数
            // $num = 5;
            $snum = 0;//评分总数
            foreach ($listc as $ky => $vala) {
              # 累计评分总数
              $snum += $vala['score'];
            }
            $every = $num !=0?$snum/$num:0;//平均得分
            $every = round($every,1);
            $listd = new Scheduling;
            $liste = $listd->getCurrische($teacherid);
            //获取curriculumid的数量
            $curriculumnum = count($liste);
            //***********************************************//
            //课程总览部分

            //创建排课表对象，调用方法获取数据
            $curriculum = new Scheduling;
            $listcurri = $curriculum->getSchCurri($teacherid,$limita);
            $classstatus = ['0'=>'暂停招生','1'=>'招生中','2'=>'已成班','3'=>'已满员','4'=>'授课中','5'=>'已结束','6'=>'已超时'];
            foreach ($listcurri as $key => $value){
                $listcurri[$key]['studentstatus'] = $classstatus[$value['classstatus']];
            }
            //根据从排课表查询出的classtypes来判断成班人数最大值
//            $classmax = [];
//            $max = 1;
//            foreach ($listcurri as $ky => $val) {
//              if ($val['type'] == 1) {
//                // 当开班类型classypes=1，一对一
//                //$max = $inputmax;
//                $max = 1;
//              }elseif($val['type'] == 2){
//                //当开班类型classtypes=2,小班课，默认6
//                //$max = $inputmax;
//              $max = 6;
//            }elseif($val['type'] == 3){
//              //当开班类型classtypes=2，大班课，默认1000
//              //$max = $inputmax;
//              $max = 1000;
//            }
//            $classmax[$ky]['max'] = $max;
//        }


                //判断$list数组中二级数组中的classnum和status,判断是否继续招生
//            foreach ($listcurri as $key => $value) {
//                if ($value['realnum']==0 && $value['status']==1) {
//                  // 0<classnum<max,status = 1，未暂停招生
//                  $listcurri[$key]['studentstatus'] = '招生中';
//                }elseif ($value['realnum']>0 && $value['realnum']<$classmax[$key]['max'] && $value['status']==1) {
//                  //classname =0,status =1，未暂停招生;
//                  $listcurri[$key]['studentstatus'] = '招生中';
//                }else{
//                    $listcurri[$key]['studentstatus'] = '已暂停';
//                }
//            }

            //*****************************************//
            //学生总览
            $studentmodel = new OrderManage;
            //获取学生id和课程名称
            $liststudent = $studentmodel->getstudentlist($teacherid,$limitb);
            $liststudent = self::moreArrayunique($liststudent);
              //获取该教师所有学生人数
            $listfordd = $studentmodel->getStudentnum($teacherid);
            $listfordd = array_unique($listfordd);
            $classnum = count($listfordd);
            //print_r($liststudent);
            //******************************************//
            //课品总览
            $commnetmodel = new Coursecomment;
            $listcomment = $commnetmodel->getCommentList($teacherid,$limitc);

            $newarr=[];
              //存储该教师的信息
            $newarr['imageurl'] = $data['imageurl'];
            $newarr['nickname'] = $data['nickname'];
            $newarr['sex'] = $data['sex'];
            $newarr['age'] = $Age;
            $newarr['country'] = $data['country'];
            $newarr['province'] = $data['province'];
            $newarr['city'] = $data['city'];
            $newarr['classesnum'] = $data['classesnum'];
            $newarr['profile'] = $data['profile'];
            $newarr['allscore'] = $every;//平均综合得分
            $newarr['curriculumnum'] = $curriculumnum;//课程数
            $newarr['classnum'] = $classnum;//开课学生数
            //$newarr['lable'] = $lablearr;//标签
            foreach ($listcurri as $ky => $vala) {
                // code...
                $listcurri[$ky]['curriculumname'] = $vala['curriculumname'];
                $listcurri[$ky]['type'] = $vala['type'];
                $listcurri[$ky]['classnum'] = $vala['classnum'];
                $listcurri[$ky]['status'] = $vala['status'];
                $listcurri[$ky]['realnum'] = $vala['realnum'];
            }
            foreach ($liststudent as $ky => $valb) {
                // code...
                $liststudent[$ky]['coursename'] = $valb['coursename'];
                $liststudent[$ky]['nickname']  =  $valb['nickname'];
                $liststudent[$ky]['ordertime']  =  date('Y-m-d H:i:s',$valb['ordertime']);
                //$newarr['student'][$ky]['ordertime'] = date(time());
            }
            foreach ($listcomment as $ky => $valc) {
                $newarr['comment'][$ky]['content'] = $valc['content'];
                $newarr['comment'][$ky]['addtime'] = date('Y-m-d',$valc['addtime']);
                $newarr['comment'][$ky]['studentnickname'] = $valc['nickname'];
                $newarr['comment'][$ky]['score'] = $valc['score'];
            }
            $newarr['curri'] = $listcurri;
            $newarr['student'] = $liststudent;
            $newarr['tagname'] = !empty($lablearr['alltagmsg'])?$lablearr['alltagmsg'][0]['tagname']:[];

            if (empty($newarr)) {
                return return_format([],0,lang('success'));
            }else{
                return return_format($newarr,0,lang('success'));
            }
        }else{
            return return_format('',20001,lang('20001'));
        }

    }



    /**
    * 获取课程总览
    * @Author wangwy
    * @param $teacherid  老师id
	  * @param $limit   取出多少条记录
	  * @return array
    */
    public function getCurriculumList($teacherid,$organid,$limitstr){
        $where = [];
         //!empty($teacherid) && $where['teacherid'] = $teacherid;
        !empty($teacherid) && $where['teacherid'] = 1;

        //创建排课表对象，调用方法获取数据
        $curriculum = new Scheduling;
        $list = $curriculum->getSchCurri($teacherid,$organid,$limitstr);

        //根据从排课表查询出的classtypes来判断成班人数最大值
        $classmax = [];
        $max = 1;
        foreach ($list as $ky => $val) {
            if ($val['type'] == 1) {
              // 当开班类型classypes=1，一对一
              //$max = $inputmax;
              $max = 1;
            }elseif($val['type'] == 2){
              //当开班类型classtypes=2,小班课，默认6
              //$max = $inputmax;
              $max = 6;
            }elseif($val['type'] == 3){
              //当开班类型classtypes=2，大班课，默认1000
              //$max = $inputmax;
              $max = 1000;
            }
            $classmax[$ky]['max'] = $max;
        }

        //判断$list数组中二级数组中的classnum和status,判断是否继续招生
        foreach ($list as $key => $value) {
            if ($value['classnum']==0&&$value['status']==1) {
              // 0<classnum<max,status = 1，未暂停招生
              $value['studentstatus'] = '招生中';
            }elseif ($value['classnum']>0&&$value['classnum']<$classmax[$key]['max']&&$value['status']==1) {
              //classname =0,status =1，未暂停招生;
              $value['studentstatus'] = '招生中';
            }
        }
        return  $list;

    }






    /**
     * 获取学生总览
     * @Author wangwy
     * @param $teacherid  教师id
     * @param $limitstr   取出多少条记录
     * @return array
     */
    public function getStudentList($teacherid,$organid,$limitstr){
        $studentmodel = new OrderManage;
        //获取学生id和课程名称
        $list = $studentmodel->getstudentlist($teacherid,$organid,$limitstr);
        return $list;
    }



    /**
     * 课程评价总览
     * @Author wangwy
     * @param $allaccountid  老师id
     * @param $limitstr   取出多少条记录
     * @return array
     */
    public function getCommentList($allaccountid,$organid,$limitstr){
        //获取课程评价
	  	  //$allaccountid 从session 中取
        $commnetmodel = new Coursecomment;
        return $commnetmodel->getCommentList($allaccountid,$organid,$limitstr);
    }



    /**
     * 课程评价总览(加课程名称)
     * @Author wangwy
     * @param $pagesize  每页多少行     可选
     * @param $pagenum  分页页码             可选
     * @param $allaccountid  老师id          必填
     * @param $limitstr   取出多少条记录     必填
     * @return array
     */
    public function getCommentLists($pagenum,$pagesize,$allaccountid,$allcommit){
        //获取课程评价
        //$allaccountid 从session 中取
        $where = [] ;
        empty($pagenum) && $pagenum = 1 ;
        if (empty($allaccountid)){
            return_format('',25001,lang('25001'));
        }else{
            $where['c.allaccountid'] = $allaccountid;
        }
        if ($allcommit == 1) {
            // 全部评价
            $where['c.score']= ['between',[1,5]];
        }elseif ($allcommit == 2) {
            $where['c.score'] = ['egt',4];
        }elseif ($allcommit == 3) {
            $where['c.score'] = [['egt',3],['lt',4],'and'];
        }elseif ($allcommit == 4) {
            $where['c.score'] = ['lt',3];
        }
        $commnetmodel = new Coursecomment;
        $list = $commnetmodel->getCommentListb($where,$pagenum,$pagesize);
        //一次获取所有评价个数
        $choice = [['between',[1,5]],['egt',4],[['egt',3],['lt',4],'and'],['lt',3]];  //所有评价条件集合
        $wherechoice = [];
        foreach ($choice as $ky =>$vl){
            $wherechoice['c.allaccountid'] = $allaccountid;
            $wherechoice['c.score'] = $vl;
            $commentsum[$ky] = $commnetmodel->getListCount($wherechoice);
        }
        $list['commentsum'] = $commentsum;
        //单独获取所有评价分数
        $dog = $commnetmodel->getComScore($where);
        $sum = array_sum($dog);
        $ev = count($dog);
        if ($ev!= 0) {
            $list['evescore'] = round($sum/$ev,1);
        }else{
            $list['evescore'] = '';
        }
        foreach ($list['data'] as $ky => $val) {
            $list['data'][$ky]['addtime'] = date('Y-m-d H:i:s',$list['data'][$ky]['addtime']);
        }
      // foreach ($list['data'] as $key => $val) {
      //   $list['data'][$key]['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
      // }
       // $schobj = new scheduling;
       //  foreach ($studentlist as $ky => $val) {
       //    # code...
       //    $cc = $schobj->getGradeone($studentlist[$ky]['curriculumid']);
       //    $studentlist[$ky]['gradename'] = $cc['gradename'];
       //  }
        if (!empty($list)) {
          return return_format($list,0,lang('success'));
        }else{
          return return_format([],0,lang('success'));
        }
    }




    /**
     * 课程评价总览
     * @Author wangwy
     * @param $allaccountid  老师id
     * @param $limitstr   取出多少条记录
     * @return array
     */
    public function getappCommentList($allaccountid,$limitstr){
        //获取课程评价
        //$allaccountid 从session 中取
        $commnetmodel = new Coursecomment;
        return $commnetmodel->getappCommentList($allaccountid,$limitstr);
    }

     /**
    * 登陆后，返回logo和机构名称
    *
    */
    public function getlogo($organid){
        if ($organid)
        $logoobj = new Organ;
        $logo = $logoobj->getlogo($organid);
        if(isset($logo)){
            return return_format($logo,0,lang('success'));
        }else{
            return return_format('',0,lang('success'));
        }
    }

    /**
     * 二维数组去重
     *
     */
    public function moreArrayunique($arr){
        $temp = [];
        foreach ($arr as $ky => $val) {
            $val = implode(',', $val);//降维
            $temp[$ky] = $val;
        }
        $temp = array_unique($temp);//去掉重复的字符串
        $temps = [];
        foreach ($temp as $k => $v) {
            $arrs = explode(',',$v);
            $temps[$k]['ordertime'] = $arrs[0];
            $temps[$k]['coursename'] = $arrs[1];
            $temps[$k]['nickname'] = $arrs[2];
        }
        return $temps;//将拆开的数组重新组装
    }

}



 ?>
