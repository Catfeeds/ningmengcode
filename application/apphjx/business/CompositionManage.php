<?php
namespace app\apphjx\business;

use app\apphjx\model\CompositionDb;

class CompositionManage
{
    /**
     * [getHomepageList 好迹星APP-》首页-》作文列表]
     * @param  [type] $compositionstatus [作文类型]
     * @param  [type] $studentid [学生id]
     * @param  [type] $pagenum [当前页]
     * @param  [type] $limit [一页几条]
     * @return [array]
     */
    public function getHomepageList($compositionstatus,$studentid,$pagenum,$limit)
    {
        $composition = new CompositionDb;
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        switch ($compositionstatus){
            case 1:
                $data = $composition->getCompositionDb($studentid,1,$limitstr);
                $add_lable = $this::handleLable($data);
                $total = $composition->getCompositionDbCount($studentid,1);
                break;
            case 2:
                $data = $composition->getCompositionDb($studentid,2,$limitstr);
                $add_lable = $this::handleLable($data);
                $total = $composition->getCompositionDbCount($studentid,2);
                break;
            default:
                return return_format('',60019,'参数有误') ;
        }
        $result = [
            'composition'=>$add_lable,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //
            ]
        ] ;
        return return_format($result,'0','操作成功');
    }
    /**
     * 标签中文转换
     */
    public function handleLable($data)
    {
        $composition = new CompositionDb;
        if(is_array($data)){
            foreach($data as $k=>&$v){
                $where = [];
                $where['id'] = ['in',$data[$k]['label']];
                $lable = $composition->getLable($where);
                $data[$k]['lablename'] = $lable;
                unset($data[$k]['label']);
            }
            //添加批阅、未批阅、未提交状态
            foreach($data as $key=>&$val) {
                if ($data[$key]['reviewstatus'] == 0) {
                    $data[$key]['status'] = 2;
                } elseif ($data[$key]['reviewstatus'] == 1) {
                    $data[$key]['status'] = 2;
                } else {
                    $data[$key]['status'] = 3;
                }
                unset($data[$key]['reviewstatus']);
            }
            return $data;
        }
        return $data;
    }
    /**
     * [seeCompositionData 好迹星app-作文批改-获取作文数据]
     * @param  $compositionid [作业id]
     * @return [array]
     */
    public function seeCompositionData($compositionid)
    {
        //获取批阅数据
        $composition = new CompositionDb();
        //获取作文数据
        $composition_data = $composition->getCompositionData($compositionid);
        //获取老师评价数据
        $teacher_data = $composition->getTeacherData($compositionid);
        $comment_status = $composition->getCommentStatus($compositionid);
        $status = empty($comment_status)?'0':'1';
        if(is_array($composition_data)){
            foreach($composition_data as $k=>&$v){
                $composition_data[$k]['content'] = urldecode($composition_data[$k]['contents']);
                $composition_data[$k]['title'] = urldecode($composition_data[$k]['titles']);
                unset($composition_data[$k]['contents']);
                unset($composition_data[$k]['titles']);
            }
        }
        $result = [
            'composition'=>$composition_data,
            'teacher'=>$teacher_data,
            'status'=>$status,
        ] ;
        return return_format($result,'0','操作成功');
    }
    /**
     * [seeComment 获取学生评论数据]
     * @param  $compositionid [作业id]
     * @return [array]
     */
    public function seeComment($compositionid)
    {
        //获取评论数据
        $composition = new CompositionDb();
        $comment_data = $composition->getCommentData($compositionid);
        $comment_list = $composition->getCommentidsInfo();
        if(empty($comment_data))$comment_data['commentlabelids'] = '';
        $ids = explode(',',$comment_data['commentlabelids']);
        foreach ($ids as $k => $labelid){
                foreach ($comment_list as $i =>$labelinfo){
                    if($labelid == $labelinfo['id']){
                            $comment_list[$i]['checked'] = 1;
                    }else{
                            $comment_list[$i]['checked'] = 0;
                    }
                }
        }
        unset($comment_data['commentlabelids']);
        foreach ($comment_list as $k =>$v){
                switch ($v['star']) {
                    case 1:
                        $list[1][] = $v;
                        break;
                    case 2:
                        $list[2][] = $v;
                        break;
                    case 3:
                        $list[3][] = $v;
                        break;
                    case 4:
                        $list[4][] = $v;
                        break;
                    case 5:
                        $list[5][] = $v;
                        break;
                }
        }
        $comment_data['commentlabel'] = $list;
        return return_format($comment_data,'0','操作成功');
    }
    /**
     * [modifyAddComposition 修改或添加学生评论数据]
     * @param  $compositionid [作业id]
     * @param  $reviewscore [评分]
     * @param  $commentcontent [内容]
     * @param  $studentid [学生id]
     * @return [array]
     */
    public function modifyAddComposition($compositionid,$reviewscore,$commentcontent,$studentid,$commentids)
    {
        //查询是否有该评价
        $composition = new CompositionDb();
        $reviewtime = time();
        $data = [];
        $data['compositionid'] = $compositionid;
        $data['reviewscore'] = $reviewscore;
        $data['commentcontent'] = $commentcontent;
        $data['userid'] = $studentid;
        $data['reviewtime'] = $reviewtime;
        $data['commentlabelids'] = $commentids;
        $data['type'] = 2;
        $comment_data = $composition->getCommentStatus($compositionid);
        if(empty($comment_data)){
            $comment_data = $composition->addComment($data);
        }else{
            $datas = [
                'reviewscore'=>$reviewscore,
                'commentcontent'=>$commentcontent,
                'reviewtime'=>$reviewtime,
                ];
            $comment_data = $composition->updateComment($datas,$compositionid);
        }
        if($comment_data){
            return return_format('','0','操作成功');
        }else{
            return return_format('','60000','操作失败');
        }
    }
    /**
     * [compositionAddOrUpdate 修改或添加学生作文数据]
     * @param  $compositionid [作业id]
     * @param  $type [作文类型]
     * @param  $title [标题]
     * @param  $imgurl [作文图片]
     * @param  $label [标签id]
     * @param  $studentid [学生id]
     * @return [array]
     */
    public function compositionAddOrUpdate($compositionid,$type,$title,$imgurl,$label,$studentid,$content,$trajectory,$videourl)
    {
        $composition = new CompositionDb();
        $data = [];
        $data['type'] = $type;
        $data['title'] = $title;
        $data['imgurl'] = $imgurl;
        $data['label'] = $label;
        $data['studentid'] = $studentid;
        $data['addtime'] = time();
        $data['content'] = $content;
        $data['trajectory'] = $trajectory;
        $data['videourl'] = $videourl;
        if($compositionid==0){
            //作文添加
            $result = $composition->CompositionInsert($data);
        }else{
            if(!is_numeric($compositionid)){
                return return_format('','60019','参数错误');
            }
            $tesing = $composition->testingSubmit($compositionid);
            if(empty($tesing)){
                return return_format('','60020','该作文不存在');
            }else{
                if ($tesing[0]['studentid']!=$studentid){
                    return return_format('','60022','只能修改学生自己的作文');
                }
            }
            //作文修改
            $datas = [
                'type' => $type,
                'title' => $title,
                'imgurl' => $imgurl,
                'label' => $label,
                'studentid' => $studentid,
                'addtime' => time(),
                'content' => $content,
                'videourl' => $videourl,
            ];
            $result = $composition->CompositionUpdate($datas,$compositionid);
        }
        if($result){
            return return_format('',0,'操作成功');
        }else{
            return return_format('','60000','操作失败');
        }
    }


}