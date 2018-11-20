<?php
/**
 * 课程推荐业务逻辑层
 */
namespace app\admin\business;

use app\admin\model\Educationaldb;
use think\Validate;
use Messages;

class EducationalHandle
{
    /**
     * [TransferList 教务-调班列表]
     * @param  [type] $status [请求状态]
     * @param  [type] $studentname [学生姓名]
     * @param  [type] $limit [限制条数]
     * @param  [type] $pagenum [请求页]
     * @return [array]
     */
    public function TransferList($status,$studentname, $pagenum, $limit)
    {
        $where = [];
        //查询条件判断
        !empty($studentname) && $where['stu.nickname'] = ['like', "%$studentname%"];
        if($studentname=='0'){$where['stu.nickname'] = ['like', "%$studentname%"];}
        if($status==0){
            $where['tran.status'] = ['eq',"$status"];
        }else{
            $where['tran.status'] = ['neq','0'];
        }
//        return return_format($where);
        //分页处理
        if ($pagenum > 0) {
            $start = ($pagenum - 1) * $limit;
            $limitstr = $start . ',' . $limit;
        } else {
            $start = 0;
            $limitstr = $start . ',' . $limit;
        }
        $classobj = new Educationaldb();
        //获取当前页数数据
        $categorydata = $classobj->transferClassList($where,$limitstr);

        //获取符合条件的数据的总条数
        $total = $classobj->transferClassListCount($where, $limitstr);
        $result = [
            'classlist' => $categorydata,
            // 内容结果集
            'pageinfo' => [
                'pagesize' => $limit,// 每页多少条记录
                'pagenum' => $pagenum,//当前页码
                'total' => $total //

            ]
        ];
        return return_format($result, 0, '操作成功');
    }
    /**
 * [getCurriculum 教务-调班列表-同意、拒绝]
 * @param  [type] $status [请求状态]
 * @param  [type] $tranid [调班id]
 * @return [array]
 */
    public function TransferApply($status,$tranid)
    {
        //调班成功调用短信接口
        //短信发送老师、学生
        $messageobj = new Messages;
        $obj = new Educationaldb();
        if($status==1){
            //获取学生手机号、老师手机号
            //获取当前页数数据
            $mobile_arrray = $obj->getSuccess($tranid);
            if(empty($mobile_arrray)){return return_format('', 60000, '数据不存在');}
            /*****start新老师短信发送******/
            $prphone = $mobile_arrray[0]['tea_prphone'];
            $param = [];
            $mobile = $mobile_arrray[0]['teacher_mobile'];
            $param[0] = $mobile_arrray[0]['teachername'];
            $param[1] = $mobile_arrray[0]['class'];
            $param[2] = $mobile_arrray[0]['student_name'];
            $tea_res = $messageobj->sendMeg($mobile,5,$param, $prphone);
            if($tea_res['result']!=0){return return_format($tea_res, 60000, '调课异常');}
            /*****end新老师短信发送******/

            /*****start学生短信发送******/
            $params = [];
            $params[0] = $mobile_arrray[0]['student_name'];
            $params[1] = $mobile_arrray[0]['class'];
            $prphones = $mobile_arrray[0]['stu_prphone'];
            $mobiles = $mobile_arrray[0]['student_mobile'];
            $update_time = time();
            $stu_res = $messageobj->sendMeg($mobiles,6,$params,$prphones);
            /*****end学生短信发送******/
            /*****start向原班级老师发送短信******/
            $old_params = [];
            $old_params[0] = $mobile_arrray[0]['old_teachername'];
            $old_params[1] = $mobile_arrray[0]['class'];
            $old_params[2] = $mobile_arrray[0]['student_name'];
            $old_prphones = $mobile_arrray[0]['old_tea_prphone'];
            $old_mobiles = $mobile_arrray[0]['old_teacher_mobile'];
            $stu_res = $messageobj->sendMeg($old_mobiles,12,$old_params,$old_prphones);
            /*****end向原班级老师发送短信******/
            if($tea_res&&$stu_res){
                $res = $obj->TranferUpdate($tranid,1,$update_time);
                if($res){
                    return return_format('', 0, '操作成功');
                }else{
                    return return_format('', 6000, '操作失败');
                }
            }
        }
        //调班失败
        //短信只发送给学生
        elseif($status==2){
            //获取学生手机号
            $mobile_arrray = $obj->getFail($tranid);
            if(empty($mobile_arrray)){return return_format('', 60000, '数据不存在');}
            $params = [];
            $params[0] = $mobile_arrray[0]['student_name'];
            $prphones = $mobile_arrray[0]['prphone'];
            $mobiles = $mobile_arrray[0]['student_mobile'];
            $stu_res = $messageobj->sendMeg($mobiles,7,$params,$prphones);
            if($stu_res['result']!=0){return return_format($stu_res, 60000, '调课异常');}
            $update_time = time();
            $res = $obj->TranferUpdate($tranid,2,$update_time);
            if($res){
                return return_format('', 0, '操作成功');
            }else{
                return return_format('', 6000, '操作失败');
            }
            return return_format($stu_res);
        }else{
            return return_format('状态值错误', 60000, '调课失败');
        }
    }
    /**
     * [TransferLesson 教务-调课列表]
     * @param  [type] $status [请求状态]
     * @param  [type] $studentname [学生姓名]
     * @param  [type] $limit [限制条数]
     * @param  [type] $pagenum [请求页]
     * @return [array]
     */
    public function TransferLesson($status,$studentname, $pagenum, $limit)
    {
        $lessonobj = new Educationaldb();
        $where = [];
        //查询条件判断
        !empty($studentname) && $where['stu.nickname'] = ['like', "%$studentname%"];
        if($studentname=='0'){$where['stu.nickname'] = ['like', "%$studentname%"];}
        if($status==0){
            $where['tran.status'] = ['eq',"$status"];
        }else{
            $where['tran.status'] = ['neq','0'];
        }
        //分页处理
        if ($pagenum > 0) {
            $start = ($pagenum - 1) * $limit;
            $limitstr = $start.','.$limit;
        } else {
            $start = 0;
            $limitstr = $start . ',' . $limit;
            //获取当前页数数据
        }
        $data = $lessonobj->transferLessonList($where,$limitstr);
        //班级名称遍历
        $lesson_param = self::transferHandle($data);
        //获取符合条件的数据的总条数
        $total = $lessonobj->transferLessonListCount($where,$limitstr);
        $result = [
            'lesslist' => $lesson_param,
            // 内容结果集
            'pageinfo' => [
                'pagesize' => $limit,// 每页多少条记录
                'pagenum' => $pagenum,//当前页码
                'total' => $total
            ]
        ];
        return return_format($result);
    }
    public function transferHandle($data)
    {
        foreach($data as $k=>$v){
            //获取班级名称
            $lessonobj = new Educationaldb();
            $result = $lessonobj->getClassName($v['old_class'],$v['new_class']);
            unset($data[$k]['old_class']);
            unset($data[$k]['new_class']);
            $data[$k]['old_class'] = $result['old_class'];
            $data[$k]['new_class'] = $result['new_class'];
        }
        return $data;
    }
    /**
     * [getCurriculum 教务-调课列表-同意、拒绝]
     * @param  [type] $status [请求状态]
     * @param  [type] $tranid [调班id]
     * @return [array]
     */
    public function TransferLessonApply($status,$tranid)
    {
        //调课成功调用短信接口
        //短信发送老师、学生
        $messageobj = new Messages;
        $obj = new Educationaldb();
        if($status==1){
            //获取学生手机号、老师手机号
            //获取当前页数数据
            $mobile_arrray = $obj->getLessSuccess($tranid);
            if(empty($mobile_arrray)){return return_format('', 60000, '数据不存在');}
            /*********新老师发送***********/
            $prphone = $mobile_arrray[0]['tea_prphone'];
            $param = [];
            $mobile = $mobile_arrray[0]['tea_mobile'];
            $param[0] = $mobile_arrray[0]['teachername'];
            $param[1] = $mobile_arrray[0]['coursename'];
            $param[2] = $mobile_arrray[0]['newlesson'];
            $param[3] = $mobile_arrray[0]['username'];
            $tea_res = $messageobj->sendMeg($mobile,8,$param, $prphone);
            /*********新老师发送***********/
            if($tea_res['result']!=0){return return_format($tea_res, 60000, '调课异常');}
            /*********学生发送***********/
            $params = [];
            $params[0] = $mobile_arrray[0]['username'];
            $params[1] = $mobile_arrray[0]['coursename'];
            $params[2] = date("Y-m-d H:i:s",$mobile_arrray[0]['starttime']);
            $prphones = $mobile_arrray[0]['stu_prphone'];
            $mobiles = $mobile_arrray[0]['stu_mobile'];
            $update_time = time();
            $stu_res = $messageobj->sendMeg($mobiles,9,$params,$prphones);
            /*********学生发送***********/
            /*********原老师发送***********/
            $old_params = [];
            $old_params[0] = $mobile_arrray[0]['old_teachername'];
            $old_params[1] = $mobile_arrray[0]['coursename'];
            $old_params[2] = $mobile_arrray[0]['oldlesson'];
            $old_params[3] = $mobile_arrray[0]['username'];
            $old_prphones = $mobile_arrray[0]['old_tea_prphone'];
            $old_mobiles = $mobile_arrray[0]['old_tea_mobile'];
            $stu_res = $messageobj->sendMeg($old_mobiles,13,$old_params,$old_prphones);
            /*********原老师发送***********/
            if($tea_res&&$stu_res){
                $res = $obj->TranferLessonUpdate($tranid,1,$update_time);
                if($res){
                    return return_format('', 0, '操作成功');
                }else{
                    return return_format('', 6000, '操作失败');
                }
            }
        }
        //调班失败
        //短信只发送给学生
        elseif($status==2){
            //获取学生手机号
            $mobile_arrray = $obj->getLessonFail($tranid);
            if(empty($mobile_arrray)){return return_format('', 60000, '数据不存在');}
            $params = [];
            $params[0] = $mobile_arrray[0]['username'];
            $params[1] = $mobile_arrray[0]['coursename'];
            $prphones = $mobile_arrray[0]['stu_prphone'];
            $mobiles = $mobile_arrray[0]['stu_mobile'];
            $stu_res = $messageobj->sendMeg($mobiles,10,$params,$prphones);
            if($stu_res['result']!=0){return return_format($stu_res, 60000, '调课异常');}
            $update_time = time();
            $res = $obj->TranferLessonUpdate($tranid,2,$update_time);
            if($res){
                return return_format('', 0, '操作成功');
            }else{
                return return_format('', 6000, '操作失败');
            }
            return return_format($stu_res);
        }else{
            return return_format('状态值错误', 60000, '调课失败');
        }
    }
    /**
     * [getCurriculum 教务-调课列表-同意、拒绝-检测此课时是否已经开班]
     * @param  [type] $tranid [调班id]
     * @return [array]
     */
    public function testingLessonApply($tranid)
    {
        $obj = new Educationaldb();
        //获取lessionid
        $to_teacher_id = $obj->getToteachId($tranid);
//        return return_format($to_teacher_id);
        if(empty($to_teacher_id)){
            return return_format('','0','');
        }
        //检测该课时时候已经开班
        $testing = $obj->testingRomm($to_teacher_id[0]['id']);
        if(empty($testing)){
            return return_format('','0','');
        }else{
            return return_format('','60018','该课时已经开始上课了，您确定同意调课申请吗');
        }
    }
    /**
     * [testingInfluence 调班列表->检测调班是否会对学生造成影响]
     * @param  [type] $tranid [调班id]
     * @return [array]
     */
    public function testingInfluence($tranid)
    {
        $obj = new Educationaldb();
        //获取原班级id、学生名称
        $schedulingid = $obj->getSchedulingId($tranid);
        if(empty($schedulingid)) return return_format('','60023','调班ID不存在');
        //获取该班级最早的一个课时
        $lessoin_time = $obj->getLessonTime($schedulingid[0]['oldschedulingid']);
        if(empty($lessoin_time)) return_format('','60024','原班级课时数据为空');
        //判断该班级下的课时是否已经开始或结束
        if($lessoin_time[0]['starttime']<time()){
            $data = '学生所调班级已有部分课程开始上课，同意调班后可能影响学生上课，您确定同意'.$schedulingid[0]['nickname'].'学生的调班申请吗？';
            return return_format('','0',$data);
        }else{
            return return_format('','0','您确定同意'.$schedulingid[0]['nickname'].'学生的调班申请吗？');
        }
    }
}