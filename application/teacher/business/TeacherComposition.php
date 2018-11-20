<?php
namespace app\teacher\business;

use app\teacher\model\Composition;

class TeacherComposition
{
    /**
     * [handleCompositionList 机构后台-作文批改-列表]
     * @param  $reviewstatus [批阅状态]
     * @param  $studentname [学生名称]
     * @param  $pagenum [分页数]
     * @param  $limit [每页显示条数]
     * @return [array]
     */
    public function handleCompositionList($reviewstatus,$studentname,$pagenum,$limit,$teacherid)
    {
        if(!is_numeric($reviewstatus)) return return_format('','60025');
        //判断请求状态类型（0：未批阅 1：以批阅）
        $where = [];
        !empty($studentname) && $where['stu.nickname'] = ['like','%'.$studentname.'%'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        switch ($reviewstatus){
            case 0:
                    $where['reviewstatus'] = ['eq',"$reviewstatus"];
                    $where['submit'] = ['eq','1'];
                    $composition = new Composition();
                    $composition_data = $composition->getZoroCompositionData($where,$limitstr);
                    if(!empty($composition_data)){
                        //获取上次批改人、批改时间
                         $composition_datas = $this->getLastData($composition_data);
                    }else{
                         $composition_datas = '';
                    }
                    $total = $composition->getZeroCompositionDataCount($where);
                break;
            case 1:
                $composition = new Composition();
                $where['comm.type'] = ['eq','1'];
                $where['comm.userid'] = ['eq',$teacherid];
                $composition_datas = $composition->getOneCompositionData($where,$limitstr);
                $total = $composition->getOneCompositionDataCount($where,$teacherid);
                break;
            default:
                return return_format('',60019,'参数有误') ;
        }
        if(is_array($composition_datas)){
            foreach ($composition_datas as $k=>&$v){
                $composition_datas[$k]['title'] = urldecode($composition_datas[$k]['titles']);
                unset($composition_datas[$k]['titles']);
            }
        }
        $result = [
            'composition'=>$composition_datas,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total
            ]
        ] ;
        return return_format($result,'0','操作成功');
    }
    /**
     * [getLastData 机构后台-作文批改-获取作文数据]
     * @param  $composition_data []
     * @return [array]
     */
    public function getLastData($composition_data)
    {
        $composition = new Composition();
        foreach($composition_data as $k=>&$v){
            $where = [];
            $where['studentid'] = ['eq',$composition_data[$k]['studentid']];
            $where['id'] = ['lt',$composition_data[$k]['id']];
            //获取学生上次写的作文
            $last = $composition->getLastCompositionData($where);
            //查看上篇作文是否有评论
            if(empty($last)){
                $composition_data[$k]['lastname'] = '';
                $composition_data[$k]['lasttime'] = '';
            }else{
                if($last[0]['reviewstatus']==0){
                    $composition_data[$k]['lastname'] = '';
                    $composition_data[$k]['lasttime'] = '';
                }elseif($last[0]['reviewstatus']==1){
                    $composition_data[$k]['lastname'] = '';
                    $composition_data[$k]['lasttime'] = '';
                }else{
                    $last_com = $composition->ifLastComposition($last[0]['id']);
                    if(empty($last_com)){
                        $composition_data[$k]['lastname'] = '';
                        $composition_data[$k]['lasttime'] = '';
                    }else{
                        $composition_data[$k]['lastname'] = $last_com[0]['nickname'];
                        $composition_data[$k]['lasttime'] = $last_com[0]['reviewtime'];
                    }
                }
            }
        }
        return $composition_data;
    }
    /**
     * [getCompositionData 机构后台-作文批改-获取作文数据]
     * @param  $reviewstatus [作业id]
     * @return [array]
     */
    public function getCompositionData($compositionid)
    {
        $composition = new Composition();
        //修改该作文为批阅中状态
        $res = $composition->updateReviewStatus($compositionid);
        if(!$res){
            return return_format('','60000','操作失败');
        }
        //获取批阅数据
        $composition_data = $composition->getCompositionData($compositionid);
        if(is_array($composition_data)){
            foreach($composition_data as $k=>&$v){
                $composition_data[$k]['content'] = urldecode($composition_data[$k]['contents']);
                $composition_data[$k]['title'] = urldecode($composition_data[$k]['titles']);
                unset($composition_data[$k]['contents']);
                unset($composition_data[$k]['titles']);
            }
        }
        return return_format($composition_data,'0','操作成功');
    }
    /**
     * [getCompositionData 机构后台-作文批改-获取作文数据]
     * @param  $reviewstatus [作业id]
     * @return [array]
     */
    public function checkCompositionData($compositionid)
    {
        $composition = new Composition();
        //查看该作文是否有老师批改中
        $res = $composition->checkReviewStatus($compositionid);
        if(is_array($res)){
            if($res[0]['reviewstatus']==1){
                return return_format('','60000','该作文已有老师正在批阅');
            }else{
                return return_format('','0','操作成功');
            }
        }else{
            return return_format('','60000','参数错误');
        }
    }
    /**
     * [getCompositionData 机构后台-作文批改]
     * @param  $compositionid [作业id]
     * @param  $reviewscore [评分]
     * @param  $teacherid [老师id]
     * @param  $commentcontent [内容]
     * @return [array]
     */
    public function reviewComposition($compositionid,$reviewscore,$commentcontent,$teacherid)
    {
        if(!is_numeric($compositionid)) return_format('','60027');
        $updatetime = time();
        $data = [
            'compositionid' => $compositionid,
            'reviewscore' => $reviewscore,
            'commentcontent' => $commentcontent,
            'userid' => $teacherid,
            'reviewtime' => $updatetime,
            'type' => 1,
        ];
        $composition = new Composition();
        //检测作文是否已经批阅、防止重复提交
        $where = [];
        $where['compositionid'] = ['eq',"$compositionid"];
        $where['type'] = ['eq',"1"];
        $testing = $composition->testingReview($where);
        if(!empty($testing)) return return_format('','60026');
        //提交
        $composition_data = $composition->addCompositionData($data,$compositionid);
        if($composition_data){
            return return_format('','0','操作成功');
        }else{
            return return_format('','60028');
        }
    }
    /**
     * [getCompositionData 机构后台-修改作文批改]
     * @param  $compositionid [作业id]
     * @param  $reviewscore [评分]
     * @param  $teacherid [老师id]
     * @param  $commentcontent [内容]
     * @return [array]
     */
    public function UpdateReviewComposition($compositionid,$reviewscore,$commentcontent)
    {
        $updatetime = time();
        $data = [];
        $data['reviewscore'] = $reviewscore;
        $data['commentcontent'] = $commentcontent;
        $data['reviewtime'] = $updatetime;
        $composition = new Composition();
        $composition_data = $composition->UpdateCompositionData($data,$compositionid,1);
        if($composition_data){
            return return_format('','0','操作成功');
        }else{
            return return_format('','60000','操作失败');
        }
    }
    /**
     * [seeCompositionData 机构后台-作文批改-查看详情]
     * @param  $reviewstatus [作业id]
     * @return [array]
     */
    public function seeCompositionData($compositionid)
    {
        //获取批阅数据
        $composition = new Composition();
        //获取作文数据
        $composition_data = $composition->getCompositionData($compositionid);
        if(is_array($composition_data)){
            foreach($composition_data as $k=>&$v){
                $composition_data[$k]['content'] = urldecode($composition_data[$k]['contents']);
                $composition_data[$k]['title'] = urldecode($composition_data[$k]['titles']);
                unset($composition_data[$k]['contents']);
                unset($composition_data[$k]['titles']);
            }
        }
        //获取老师评价数据
        $teacher_data = $composition->getTeacherData($compositionid);
        $student_data = $composition->getStudentData($compositionid);
        $result = [
            'composition'=>$composition_data,
            'teacher'=>$teacher_data,
            'student'=>$student_data,
        ] ;
        return return_format($result,'0','操作成功');
    }
    /**
     * [compositionRegresses 教师端-修改批阅状态]
     * @param  $reviewstatus [作业id]
     * @return [array]
     */
    public function compositionRegresses($compositionid)
    {
        $composition = new Composition();
        $composition_data = $composition->compositionRegressesStatus($compositionid);
        if($composition_data){
            return return_format('','0','操作成功');
        }else{
            return return_format('','60000','操作失败');
        }
    }
}