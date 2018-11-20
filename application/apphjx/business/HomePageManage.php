<?php
namespace app\apphjx\business;

use app\apphjx\model\HomepageDb;

class HomePageManage
{
    /**
     * [getHomepageList 课程推荐-分类列表]
     * @param  [type] $compositionstatus [课程]
     * @param  [type] $studentid [一页几条]
     * @return [array]
     */
    public function getHomepageList($compositionstatus,$studentid)
    {
        $composition = new HomepageDb;
        switch ($compositionstatus){
            case 1:
                $data = $composition->getCompositionDb($studentid,1);
                $add_lable = $this::handleLable($data);
                break;
            case 2:
                $data = $composition->getCompositionDb($studentid,2);
                $add_lable = $this::handleLable($data);
                break;
            default:
                return return_format('',60019,'参数有误') ;
        }
        return return_format($add_lable,'','操作成功');
    }
    /**
     * 标签中文转换
     */
    public function handleLable($data)
    {
        $composition = new HomepageDb;
        foreach($data as $k=>&$v){
            $where = [];
            $where['id'] = ['in',$data[$k]['label']];
            $lable = $composition->getLable($where);
            $data[$k]['lablename'] = $lable;
            unset($data[$k]['label']);
        }
        //添加批阅、未批阅、未提交状态
        foreach($data as $key=>&$val){
            if($data[$key]['submit']==0){
                $data[$key]['status'] = 1;
            }elseif($data[$key]['submit']==1){
                if($data[$key]['reviewstatus']==0){
                    $data[$key]['status'] = 2;
                }elseif($data[$key]['reviewstatus']==1){
                    $data[$key]['status'] = 2;
                }else{
                    $data[$key]['status'] = 3;
                }
            }
            unset($data[$key]['submit']);
            unset($data[$key]['reviewstatus']);
        }
        return $data;
    }
    /**
     * [seeCompositionData 机构后台-作文批改-获取作文数据]
     * @param  $reviewstatus [作业id]
     * @return [array]
     */
    public function seeCompositionData($compositionid)
    {
        //获取批阅数据
        $composition = new HomepageDb();
        //获取作文数据
        $composition_data = $composition->getCompositionData($compositionid);
        //获取老师评价数据
        $teacher_data = $composition->getTeacherData($compositionid);
//        $student_data = $composition->getStudentData($compositionid);
        $result = [
            'composition'=>$composition_data,
            'teacher'=>$teacher_data,
//            'student'=>$student_data,
        ] ;
        return return_format($result,'0','操作成功');
    }

}