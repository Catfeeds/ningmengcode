<?php
namespace app\admin\business;

use app\admin\model\Promotion;
use think\Validate;

class PromotionTui
{
    /**
     * [getCurriculum 课程推荐-分类列表]
     * @param  [type] $curricu[课程]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @return [array]
     */
    public function getCategoryHandle($categoryname,$pagenum,$limit)
    {
        $where = [];
        //查询条件判断
        !empty($categoryname) && $where['categoryname'] = ['like','%'.$categoryname.'%'] ;
        $where['delflag'] = ['=','1'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit;
        }
        $categoryobj = new Promotion();
        //获取当前页数数据
        $categorydata = $categoryobj->getCategoryList($where,$limitstr);

        //获取符合条件的数据的总条数
        $total = $categoryobj->getCategoryListCount($where,$limitstr);
        $result = [
            'categorylist'=>$categorydata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [sortCurriculum 课程推荐分类上移、下移]
     * @param  [type] $idx1 [课程id]
     * @param  [type] $idx2 [课程id]
     * @return [array]
     */
    public function sortCategory($idx1,$idx2)
    {
        //合并数据
        $categoryobj = new Promotion();
        $ids = [];
        $ids[0] = $idx1;
        $ids[1] = $idx2;
        $where['id'] = ['in',$ids] ;
        return $categoryobj->sortCategory($where);

    }
    /**
     * [seeCategory 查看课程分类下的课程]
     * @param  [type] $categoryid [课程分类id]
     * @return [array]
     */
    public function seeCategory($id)
    {
        //获取该分类下的课程id
        $categoryobj = new Promotion();
        $ids = $categoryobj->getCategoryIds($id);
        //获取课程数据
        $curriculum = $categoryobj->getCurriculumArray($ids[0]['curriculumids']);
        return return_format($curriculum,0,'操作成功');
    }
    /**
     * [CategoryInsert 课程分类添加]
     * @param  [type] $categoryid [分类名称]
     * @return [array]
     */
    public function CategoryInsert($categoryname)
    {
        $categoryobj = new Promotion();
        //分类添加不能大于6个
        $categorycount = $categoryobj->CategoryTest();
        if($categorycount>5){
            return return_format('分类不能多于六个',60011,'操作失败') ;
        }
        $res = $categoryobj->addCategory($categoryname);
        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('添加失败',60001,'error') ;
        }
    }
    /**
     * [courseAdd 课程分类添加]
     * @param  [type] $categoryid [分类id]
     * @param  [type] $courseids [课程ids]
     * @return [array]
     */
    public function courseAdd($categoryid,$courseids)
    {
        //获取该分类下的课程ids
        $categoryobj = new Promotion();
        $categoryids = $categoryobj->courseTesing($categoryid);
        if(!empty($categoryids[0]['curriculumids'])){
            $ids = explode(",",$categoryids[0]['curriculumids']);
            $coursecount = count($ids);//分类下课程数
        }else{
            //如果返回数据为空，默认值为零
            $coursecount = 0;
        }
        //判断每一分类下的课程，不能超过四门
        $addids = explode(",",$courseids);
        $addcoursecount = count($addids);
        if($coursecount+$addcoursecount>4){
            return return_format('推荐课程不能多于四门',60011,'操作失败') ;
        }
        if(empty($categoryids[0]['curriculumids'])){
            $addcourseids = $courseids;
        }else{
            $addcourseids = $categoryids[0]['curriculumids'].','.$courseids;
        }
        $res = $categoryobj->addCourse($addcourseids,$categoryid);

        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('添加失败',60001,'error') ;
        }
    }
    /**
     * [getCurriculum 课程推荐列表]
     * @param  [type] $curricu[课程]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @return [array]
     */
    public function getCurriculum($curriculum,$pagenum,$limit,$recommend)
    {
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $curriculumobj = new Promotion();
        //获取当前页数数据
        $curriculumdata = $curriculumobj->getCurriculumList($recommend,$curriculum,$limitstr);

        //获取符合条件的数据的总条数
        $total = $curriculumobj->getCurriculumListCount($recommend,$curriculum);
        $result = [
                'curriculumlist'=>$curriculumdata,
                // 内容结果集
                'pageinfo'=>[
                    'pagesize'=>$limit ,// 每页多少条记录
                    'pagenum' =>$pagenum ,//当前页码
                    'total'   => $total
                ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [delCurriculum 课程推荐删除]
     * @param  [type] $curriculumid [课程id]
     * @return [array]
     */
    public function delCurriculum($categoryid,$courseid)
    {
        //获取该分类下的课程ids
        $categoryobj = new Promotion();
        $categoryids = $categoryobj->courseTesing($categoryid);
        $ids = explode(",",$categoryids[0]['curriculumids']);
        $arr = array_diff($ids,["$courseid"]);
        //处理成字符串
        $curriculumids = implode(",",$arr);
        $curriculumobj = new Promotion();
        $res = $curriculumobj->delCurriculuml($categoryid,$curriculumids,$courseid);
        if($res){
            return return_format('删除成功',0,'操作成功') ;
        }else{
            return return_format('删除失败',60001,'操作失败') ;
        }
    }
    /**
     * [proCategoryDel 课程推荐-分类删除]
     * @param  [type] $curriculumid [课程id]
     * @return [array]
     */
    public function proCategoryDel($categoryid)
    {
        $categoryobj = new Promotion();
        $res = $categoryobj->delCategory($categoryid);
        if($res){
            return return_format('删除成功',0,'操作成功') ;
        }else{
            return return_format('删除失败',60001,'操作失败') ;
        }
    }
    /**
     * [addCurriculum 课程推荐添加]
     * @param  [type] $curriculumid [课程id]
     * @return [array]
     */
    public function addCurriculum($curriculumids)
    {
        $curriculum_ids = explode(',',$curriculumids);
        $curriculumobj = new Promotion();
        //推荐课程限制四条
        $count = $curriculumobj->AddCount();
        $addcount = count($curriculum_ids)+$count;
        if($addcount>4){return return_format('推荐课程不能多于四门',60011,'error') ;}
        //添加
        $res = $curriculumobj->AddCurriculuml($curriculum_ids);
        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('该课程id已添加或不存在',60002,'error') ;
        }
    }
    /**
     * [sortCurriculum 课程推荐上移、下移]
     * @param  [type] $idx1 [课程id]
     * @param  [type] $idx2 [课程id]
     * @return [array]
     */
    public function sortCurriculum($idx1,$idx2)
    {
        //合并数据
        $curriculumobj = new Promotion();
        $ids = [];
        $ids[0] = $idx1;
        $ids[1] = $idx2;
        $where['id'] = ['in',$ids] ;
        return $curriculumobj->sortCurriculuml($where);

    }
    /**
     * [getTeacher 老师推荐列表]
     * @param  [type] $teachername[老师]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @return [array]
     */
    public function getTeacher($teachername,$pagenum,$limit,$recommend)
    {

//        var_dump($teachername.$pagenum.$limit.$recommend);die;
        $where = [];
        //查询条件判断
        !empty($teachername) && $where['nickname'] = ['like','%'.$teachername.'%'] ;
        $where['delflag'] = ['=','1'];
        $where['recommend'] = ['=',$recommend];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $teacherobj = new Promotion();
        //获取当前页数数据
        $teacherdata = $teacherobj->getTeacherList($where,$limitstr);

        //获取符合条件的数据的总条数
        $total = $teacherobj->getTeacherListCount($where);
        $result = [
            'teacherlist'=>$teacherdata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
        return $result;
    }
    /**
     * [delTeacher 老师推荐删除]
     * @param  [type] $curriculumid [课程id]
     * @return [array]
     */
    public function delTeacher($teacherid)
    {
        $teacher_id = explode(',',$teacherid);
        $teacherobj = new Promotion();
        $res = $teacherobj->delTeacher($teacher_id);
        if($res){
            return return_format('删除成功',0,'操作成功') ;
        }else{
            return return_format('该老师id已删除或不存在',60004,'error') ;
        }
    }
    /**
     * [addTeacher 老师推荐添加]
     * @param  [type] $curriculumid [课程id]
     * @return [array]
     */
    public function addTeacher($teacherid)
    {
        $teacher_id = explode(',',$teacherid);
        $teacherobj = new Promotion();
        $res = $teacherobj->addTeacher($teacher_id);
        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('该老师id已添加或不存在',60003,'error') ;
        }
    }
    /**
     * [sortTeacher 老师推荐上移、下移]
     * @param  [type] $idx1 [老师id]
     * @param  [type] $idx2 [老师id]
     * @return [array]
     */
    public function sortTeacher($idx1,$idx2)
    {
        //合并数据
        $teacherobj = new Promotion();
        $ids = [];
        $ids[0] = $idx1;
        $ids[1] = $idx2;
        $where['teacherid'] = ['in',$ids] ;
        $res = $teacherobj->sortTeacher($where);
        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('该老师id已添加或不存在',60003,'error') ;
        }
    }
    /**
     * [homeList 首页广告]
     * @param
     * @return [array]
     */
    public function homeList()
    {
        $homeobj = new Promotion();
        $res = $homeobj->listHomepage();
        if($res){
            return return_format($res,0,'操作成功') ;
        }else{
            return return_format('',60007,'操作失败') ;
        }
    }
    /**
     * [homeDel 首页广告删除]
     * @param $ads_id  广告id
     * @return [array]
     */
    public function homeDel($ads_id)
    {
        $homeobj = new Promotion();
        $res = $homeobj->delHomepage($ads_id);
        if($res){
            return return_format('' ,0,'操作成功') ;
        }else{
            return return_format('删除失败',60008,'error') ;
        }
    }
    /**
     * [homeCourse 课程列表]
     * @param   $param
     * @return [array]
     */
    public function homeCourse($param,$limit)
    {
        $where = [];
        $pagenum = $param['page_num'];
        $coursename = $param['name'];
        //查询条件判断
        !empty($coursename) && $where['coursename'] = ['like','%'.$coursename.'%'] ;
        $where['delflag'] = ['=','1'];
        $where['status'] = ['=','1'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $homeobj = new Promotion();
        $coursearray = $homeobj->HomepageCourseList($where,$limitstr);
        foreach ($coursearray as $k=>$v){
            $coursearray[$k]['checked'] = 0;
        }
        $total = $homeobj->HomepageCourseListCount($where,$limitstr);
        $result = [
            'courselist'=>$coursearray,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
        return $result;
    }
    /**
     * [homeTeacher 老师列表]
     * @param   $param
     * @return [array]
     */
    public function homeTeacher($param,$limit)
    {
        $where = [];
        $pagenum = $param['page_num'];
        $teachername = $param['name'];
        //查询条件判断
        !empty($teachername) && $where['teachername'] = ['like','%'.$teachername.'%'] ;
        $where['delflag'] = ['=','1'];
        $where['accountstatus'] = ['=','0'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $homeobj = new Promotion();
        $teacherarray = $homeobj->HomepageTeacherList($where,$limitstr);
        foreach ($teacherarray as $k=>$v){
            $teacherarray[$k]['checked'] = 0;
        }
        $total = $homeobj->HomepageTeacherListCount($where,$limitstr);
        $result = [
            'courselist'=>$teacherarray,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
        return $result;
    }
    /**
     * [homeDel 首页广告添加]
     * @param $Adsname  广告名称
     * @param $allfiles  图片地址
     * @return [array]
     */
    public function homeAdd($param)
    {
        $homeobj = new Promotion();
        $data = [
            'remark' => $param['remark'],
            'imagepath' => $param['imagepath'],
            'urltype' => $param['urltype'],
            'teacherid' => $param['teacherid'],
            'courseid' => $param['courseid'],
            'url' => $param['url'],
            'addtime' => time()
        ];
        $res = $homeobj->addHomepage($data);
        if($res){
            return return_format('',0,'操作成功') ;
        }else{
            return return_format('添加失败',60007,'操作失败') ;
        }

    }
    /**
     * [courseExpress 课程id标识]
     * @param $param
     * @return [array]
     */
    public function courseExpress($param,$limit)
    {
        if(empty($param['pagenum'])){
            //获取请求id所在页数
            $courseid = $param['express'];
            $pagenum = self::getNowPage($courseid,$limit);
        }else{
            $pagenum = $param['pagenum'];
        }

        $where = [];
        $coursename = $param['name'];
        //查询条件判断
        !empty($coursename) && $where['coursename'] = ['like','%'.$coursename.'%'] ;
        $where['delflag'] = ['=','1'];
        $where['status'] = ['=','1'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $homeobj = new Promotion();
        $teacher = $homeobj->HomepageNow($where,$limitstr);
        $teacherarray = self::Checkend($teacher,$param['express'],'id');
        $total = $homeobj->HomepageNowCount($where,$limitstr);
        $result = [
            'courselist'=>$teacherarray,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
        return $result;

    }
    //课程选中标识
    public function Checkend($teacher,$id,$public_id)
    {
        foreach($teacher as $j=>&$h) {
            if ($teacher[$j]["$public_id"] == $id) {
                $teacher[$j]['checked'] = 1;
            }else{
                $teacher[$j]['checked'] = 0;
            }
        }
        return $teacher;
    }
    /**
     * [teacherExpress 老师id标识]
     * @param $param
     * @return [array]
     */
    public function teacherExpress($param,$limit)
    {
        if(empty($param['pagenum'])){
            //获取请求id所在页数
            $teacherid = $param['express'];
            $pagenum = self::getTeacherPage($teacherid,$limit);
//            return return_format($pagenum);
        }else{
            $pagenum = $param['pagenum'];
        }

        $where = [];
        $teachername = $param['name'];
        //查询条件判断
        !empty($teachername) && $where['teachername'] = ['like','%'.$teachername.'%'] ;
        $where['delflag'] = ['=','1'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $homeobj = new Promotion();
        $teacher = $homeobj->HomepageTeacherNow($where,$limitstr);
//        return return_format($teacher);
        $teacherarray = self::Checkend($teacher,$param['express'],'teacherid');
        $total = $homeobj->HomepageTeacherNowCount($where,$limitstr);
        $result = [
            'courselist'=>$teacherarray,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
        return $result;

    }
    /**
     * [getNowPage 获取标识id所在页]
     * @param $courseid  课程id
     * @param $limit  限制页数
     * @return [array]
     */
    public function getTeacherPage($teacherid,$limit)
    {
        $teacherobj = new Promotion();
        $count = $teacherobj->getTeacherCount($teacherid);
        $nowpage = ceil($count/$limit);
        return $nowpage;

    }
    /**
     * [getNowPage 获取标识id所在页]
     * @param $courseid  课程id
     * @param $limit  限制页数
     * @return [array]
     */
    public function getNowPage($courseid,$limit)
    {
        $courseobj = new Promotion();
        $count = $courseobj->getCourseCount($courseid);
        $nowpage = ceil($count/$limit);
        return $nowpage;

    }
    /**
     * [homeGet 首页广告编辑]
     * @param $Adsid  广告id
     * @return [array]
     */
    public function homeGet($Adsid)
    {
        $homeobj = new Promotion();
        $res = $homeobj->getHomepage($Adsid);
        $result = [
            'adsarray'=>$res,
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [homeDel 首页广告编辑]
     * @param $Adsname  广告名称
     * @param $allfiles  图片地址
     * @param $allfiles  图片地址
     * @return [array]
     */
    public function homeEdit($param)
    {
        $homeobj = new Promotion();
        $res = $homeobj->editHomepage($param);
        if($res){
            return return_format('',0,'操作成功') ;
        }else{
            return return_format('操作失败',60007,'error') ;
        }

    }
    /**
     * [typeList 分类列表]
     * @param
     * @return [array]
     */
    public function typeList($recommend)
    {
        $typeobj = new Promotion();
        empty($recommend)?$recommend=0:$recommend;
        $res = $typeobj->listType($recommend);
        if($res){
            return return_format($res,0,'操作成功') ;
        }else{
            return return_format('',60007,'error') ;
        }
    }
    /**
     * [addType  分类添加]
     * @param  [type] $typeid [分类id]
     * @return [array]
     */
    public function addType($typeid)
    {
        $type_id = explode(',',$typeid);
        $typeobj = new Promotion();
        $res = $typeobj->addType($type_id);
        if($res){
            return return_format('添加成功',0,'操作成功') ;
        }else{
            return return_format('该分类id已添加或不存在',60004,'error') ;
        }
    }
    /**
     * [delType  分类删除]
     * @param  [type] $typeid [分类id]
     * @return [array]
     */
    public function delType($typeid)
    {
        $typeobj = new Promotion();
        $res = $typeobj->delType($typeid);
        if($res){
            return return_format('删除成功',0,'操作成功') ;
        }else{
            return return_format('该分类id已删除或不存在',60004,'error') ;
        }
    }

    /**
     * [freeCollection  免费领取列表]
     * @param  [type] $typeid [分类id]
     * @return [array]
     */
    public function freeCollection($start_time,$end_time,$page_num,$limit)
    {

        $where = [];
        //查询条件判断
        !empty($start_time) && $where['receivetime'] = ['between',[$start_time,$end_time]];
        //分页处理
        if($page_num>0){
            $start = ($page_num - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $promtionobj = new Promotion();
        //获取当前页数数据
        $freedata = $promtionobj->getFreeCollection($where,$limitstr);
//        return return_format($freedata);die;
        //获取符合条件的数据的总条数
        $total = $promtionobj->getFreeCollectionCount($where);
        $result = [
            'freelist'=>$freedata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$page_num ,//当前页码
                'total'   => $total //
            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [getSet  获取套餐列表数据]
     * @param  [type] $setname [套餐名称]
     * @param  [type] $pagenum [当前页]
     * @param  [type] $limit [每页显示条数]
     * @return [array]
     */
    public function getSet($setname,$pagenum,$limit)
    {
        $where = [];
        //查询条件判断
        !empty($setname) && $where['setmeal'] = ['like','%'.$setname.'%'] ;
        $where['delflag'] = ['=','1'];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $setobj = new Promotion();
        //获取当前页数数据
        $setdata = $setobj->getSetList($where,$limitstr);
        //获取符合条件的数据的总条数
        $total = $setobj->getSetListCount($where);
        $result = [
            'setlist'=>$setdata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //
            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [updateSetShelf  套餐列表-上下状态]
     * @param  [type] $setid [套餐id]
     * @param  [type] $shelf [套餐上下架状态]
     * @return [array]
     */
    public function updateSetShelf($setid,$shelf)
    {
        $typeobj = new Promotion();
        $res = $typeobj->updateShelf($setid,$shelf);
        if($res){
            return return_format('操作成功',0,'操作成功') ;
        }else{
            return return_format('操作失败',60004,'error');
        }
    }
    /**
     * [getSetCategory  获取分类]
     * @param  [type] $typeid [分类ids]
     * @return [array]
     */
    public function getSetCategory()
    {
        $categoryobj = new Promotion();
        $list = $categoryobj->getCategory();
        if ($list) {
            // $count = count($list);
            // 过滤部分没上级的2级标签
            $arrOne = [];
            foreach ($list as $key => $value) {
                if ($value['fatherid'] == 0) {
                    $arrOne[] = $value['id'];
                }
            }
//            foreach ($list as $k => $v) {
//                if ($v['fatherid'] != 0 && !in_array($v['fatherid'], $arrOne)) {
//                    unset($list[$k]);
//                }
//            }
            $list = array_values($list);
            $list = toTree($list, 'id', 'fatherid', 'children');
            $check = self::checkSet($list);
//            return return_format($check);
            return return_format($check, 0, lang('success'));
        } else {
            return return_format('', 10116, lang('error_log'));
        }
    }
    /**
     * [checkSet  标识子类的分类]
     * @param  [type] $list [分类tree]
     * @return [array]
     */
    public function checkSet($list)
    {
        foreach($list as $key => &$val) {
            if (array_key_exists('children', $list[$key])) {
                $list[$key]['three'] = 0;
                foreach ($list[$key]['children'] as $ke => &$va) {
                    if (array_key_exists('children', $list[$key]['children'][$ke])) {
                        $list[$key]['children'][$ke]['three'] = 0;
                        foreach ($list[$key]['children'][$ke]['children'] as $k => &$v) {
                            $list[$key]['children'][$ke]['children'][$k]['three'] = 1;
                        }
                    }else{
                        $list[$key]['children'][$ke]['three'] = 1;
                    }
                }
            }else{
                $list[$key]['three'] = 1;
            }
        }
        return $list;
    }
    /**
     * [getSetCurriculum 课程列表]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @return [array]
     */
    public function getSetCurriculum($pagenum,$limit,$coursename,$classtypes)
    {
        $where = [];
        !empty($coursename) && $where['coursename'] = ['like','%'.$coursename.'%'] ;
        !empty($classtypes) && $where['classtypes'] = ['eq',"$classtypes"] ;
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $curriculumobj = new Promotion();
        //获取未删除、价格等于0的课程
        $zero_where = [];
        $zero_where['curr.delflag'] = ['eq','1'];
        $zero_where['curr.price'] = ['eq','0'];
        $zero_where['sch.price'] = ['gt','0'];
        $iddata = $curriculumobj->getnoDeleteData($zero_where);
        $idarray = [];
        if(!empty($iddata)){
            foreach($iddata as $k=>$v){
                $idarray[] = $iddata[$k]['id'];
            }
            //去重
            $ids = array_unique($idarray);
            $curriculumids = implode(',',$ids);
        }else{
            $curriculumids = '';
        }
        //获取当前页数数据
        $curriculumdata = $curriculumobj->getSetCurriculum($where,$limitstr,$curriculumids);
        //获取符合条件的数据的总条数
        $total = $curriculumobj->getSetCurriculumCount($where,$curriculumids);
        $result = [
            'curriculumlist'=>$curriculumdata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [getSetDetailed  套餐明细]
     * @param  [type] $setid [分类ids]
     * @return [array]
     */
    public function getSetDetailed($setid)
    {
        $setobj = new Promotion();
        //获取套餐明细数据-购买课时数据
        $bug = $setobj->getSetBug($setid);
        if(isset($bug[0])){
            //判断套餐时候有赠送课时
            if($bug[0]['givestatus']==1){
                $set_use = $setobj->getSetGive($setid);
                $give = $setobj->getGiveData($setid);
                $set_use[0]['threshold'] = $give[0]['threshold'];
                $set_use[0]['trialtype'] = $give[0]['trialtype'];
                $set_use[0]['efftype'] = $give[0]['efftype'];
                $set_use[0]['bughour'] = $give[0]['bughour'];
            }else{
                $set_use = '';
            }
            //合并数据
            $result = [
                'bug'=>$bug,
                'give'=>$set_use,
                // 内容结果集
            ];
            return return_format($result,0,'操作成功') ;
        }
        else{
            return return_format('','60000','数据为空');
        }

    }
    /**
     * [getSetData 学生使用详细]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @param  [type] $usestatus [使用状态]
     * @return [array]
     */
    public function getSetData($usestatus,$pagenum,$limit,$setid)
    {
        $where = [];
        //查询条件判断
        !empty($usestatus) && $where['use.ifuse'] = ['=',"$usestatus"];
        $where['use.packageid'] = ['=',"$setid"];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $setobj = new Promotion();
        //获取当前页数数据
        $setdata = $setobj->getSetDatalist($where,$limitstr);
        //获取符合条件的数据的总条数
        $total = $setobj->getDataCount($where);
        $result = [
            'setdata'=>$setdata,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //
            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [setDel  套餐列表-删除]
     * @param   $setid [套餐id]
     * @return [array]
     */
    public function setDel($setid)
    {
        $setobj = new Promotion();
        //检测套餐能否删除
        $where = [];
        $where['packageid'] = ['eq',$setid];
        $where['ifuse'] = ['neq','2'];
        $tesing = $setobj->testSet($where);
        if($tesing>0){
            return return_format('套餐正在使用，不能删除',60013,'error');
        }
        //删除
        $res = $setobj->setDe1($setid);
        if($res){
            return return_format('删除成功',0,'操作成功');
        }else{
            return return_format('删除失败',60012,'error');
        }
    }
    /**
     * [setInsert  套餐添加]
     * @param   $param [添加数据]
     * @return [array]
     */
    public function setInsert($param)
    {
        if($param['trialtype']['status']==3){
            if(empty($param['trialtype']['curriculumids'])){
                return return_format('选定课程不能为空',60016,'操作失败');
            }
        }
        if($param['givestatus']==1){
            if($param['gifttrialtype']['status']==3){
                if(empty($param['gifttrialtype']['giftcurriculumids'])){
                    return return_format('选定课程不能为空',60016,'操作失败');
                }
            }
        }
        $status = $param['givestatus'];
        $give = self::arrayHandle($param);
        $set = new Promotion();
        $res = $set->setInsertDb($give,$status);
        if($res){
            return return_format('添加成功',0,'操作成功');
        }else{
            return return_format('添加失败',60000,'操作失败');
        }
    }
    /**
     * arrayHandle
     */
    public function arrayHandle($param)
    {
        $zero_status = [];
        $zero_status['trialtype'] = $param['trialtype']['status'];
        $zero_status['efftype'] = $param['efftype']['status'];
        $zero_status['bughour'] = $param['bughour'];
        $zero_status['setmeal'] = $param['setmeal'];
        $zero_status['setprice'] = $param['setprice'];
        $zero_status['givestatus'] = $param['givestatus'];
        $zero_status['limitbuy'] = $param['limitbuy'];
        $zero_status['threshold'] = $param['threshold'];
        $zero_status['content'] = $param['content'];
        $zero_status['efftime'] = $param['efftype']['efftime'];
        $zero_status['effstarttime'] = $param['efftype']['effstarttime'];
        $zero_status['effendtime'] = $param['efftype']['effendtime'];
        $zero_status['categoryids'] = $param['trialtype']['categoryids'];
        if(!empty($param['trialtype']['categoryids'])){
            $zero_status['categoryapppint'] = $this->CategoryAppoint($param['trialtype']['categoryids']);
        }
        $zero_status['curriculumids'] = $param['trialtype']['curriculumids'];
        $zero_status['setimgpath'] = $param['setimgpath'];
        $one_status = [];
        $one_status['sendvideo'] = $param['sendvideo'];
        $one_status['sendlive'] = $param['sendlive'];
        $one_status['giftthreshold'] = $param['giftthreshold'];
        $one_status['giftefftype'] = $param['giftefftype']['status'];
        $one_status['gifteffstarttime'] = $param['giftefftype']['gifteffstarttime'];
        $one_status['gifteffendtime'] = $param['giftefftype']['gifteffendtime'];
        $one_status['giftefftime'] = $param['giftefftype']['giftefftime'];
        $one_status['gifttrialtype'] = $param['gifttrialtype']['status'];
        $one_status['giftcategoryids'] = $param['gifttrialtype']['giftcategoryids'];
        $one_status['giftcurriculumids'] = $param['gifttrialtype']['giftcurriculumids'];
        $give = [];
        $give['zero_status'] =  $zero_status;
        $give['one'] =  $one_status;
        return $give;

    }
    public function  CategoryAppoint($param){
        $categoryobj = new Promotion();
        $list = $categoryobj->getCategory();
        if ($list) {
            // $count = count($list);
            // 过滤部分没上级的2级标签
            $arrOne = [];
            foreach ($list as $key => $value) {
                if ($value['fatherid'] == 0) {
                    $arrOne[] = $value['id'];
                }
            }
//            foreach ($list as $k => $v) {
//                if ($v['fatherid'] != 0 && !in_array($v['fatherid'], $arrOne)) {
//                    unset($list[$k]);
//                }
//            }
            $list = array_values($list);
            $list = toTree($list, 'id', 'fatherid', 'children');
        }
//        $param  = '1,2,3,8,9,10,12,13,14'; // 1,2,3,5,6,8
        $arr2 = explode(',',$param);
        $return = [];
        foreach ($list as $k => $v){
            if(isset($v['children'])){
                $erji = [];
                foreach ($v['children'] as $key => $val){
                    if(isset($val['children'])){
                        $indata = array_column($val['children'],'id');
                        $diff = array_diff($indata,$arr2);
                        if($diff && count($diff) == count($val['children'])){

                        }else if($diff){
                            $return = array_merge($return,array_intersect($indata,$arr2));
                            $return[] = $val['id'];
                        }else{
                            $erji[] = $val['id'];
                            $return[] = $val['id'];
                        }
                    }
                }
                if(count($v['children']) == count($erji)){
                    foreach ($erji as $i => $al){
                        $heh =  array_search($al, $return);
                        unset($return[$heh]);
                    }
                    $return[] = $v['id'];
                }else if(!in_array($v['id'],$arr2)){
                }else{
                    $return[] = $v['id'];
                }
            }
        }
        sort($return);
        $category = implode(',',$return);
        return $category;
    }
    /**
     * [SetUpdate  获取套餐数据]
     * @param   $setid ]
     * @return [array]
     */
    public function SetUpdate($setid)
    {
        $set = new Promotion();
        if(!empty($setid)){
            $where = [];
            $where['packageid'] = ['eq',$setid];
            $where['orderstatus'] = ['eq','20'];
            $testing = $set->Testing($where);
            if(!empty($testing)){
                return return_format('',60000,'改套餐已使用，不能编辑');
            }
        }
        $res = $set->setUpdateDb($setid);
        if(empty($res)){return return_format('',60000,'套餐不存在');}
        //判断套餐是否有赠送课时
        if($res[0]['givestatus']==1)
        {
            $getgive = $set->giveData($setid);
            $result = [
                'bug'=>$res,
                'give'=>$getgive,
                // 内容结果集
            ];
            return return_format($result,0,'操作成功');
        }
        $result = [
            'bug'=>$res,
            // 内容结果集
        ] ;
        return return_format($result,0,'操作成功');
    }
    public function UpdateCategory($categoryids)
    {
        $categoryobj = new Promotion();
        $listArray = $categoryobj->UpdateCategory();
        $list = self::CategoryHandle($listArray,$categoryids);
        if ($list) {
//             $count = count($list);
            // 过滤部分没上级的2级标签
            $arrOne = [];
            foreach ($list as $key => $value) {
                if ($value['fatherid'] == 0) {
                    $arrOne[] = $value['id'];
                }
            }
//            foreach ($list as $k => $v) {
//                if ($v['fatherid'] != 0 && !in_array($v['fatherid'], $arrOne)) {
//                    unset($list[$k]);
//                }
//            }
            $list = array_values($list);
            $list = toTree($list, 'id', 'fatherid', 'children');
            return return_format($list, 0, lang('success'));
        } else {
            return return_format('', 10116, lang('error_log'));
        }
    }
    //分类选中状态
    public function CategoryHandle($listArray,$categoryids)
    {
        $ids = explode(',',$categoryids);
        if(is_array($ids)){
            foreach($ids as $k=>$v){
                foreach($listArray as $j=>&$h){
                    if($listArray[$j]['id'] == $ids[$k]) {
                        $listArray[$j]['checked']=1;
                    }
                }
            }
        }
        return $listArray;
    }
    /**
     * [getSetCurriculum 套餐列表-编辑-指定课程]
     * @param  [type] $limit [一页几条]
     * @param  [type] $pagenum [分页条数]
     * @param  [type] $curriculumids [课程ids]
     * @return [array]
     */
    public function getSetCurriculums($pagenum,$limit,$coursename,$classtypes,$curriculumids)
    {
        $where = [];
        //查询条件判断
        !empty($coursename) && $where['coursename'] = ['like','%'.$coursename.'%'] ;
        !empty($classtypes) && $where['classtypes'] = ['eq',$classtypes];
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $curriculumobj = new Promotion();
        //获取未删除、价格等于0的课程
        $zero_where = [];
        $zero_where['curr.delflag'] = ['eq','1'];
        $zero_where['curr.price'] = ['eq','0'];
        $zero_where['sch.price'] = ['gt','0'];
        $iddata = $curriculumobj->getnoDeleteData($zero_where);
        $idarray = [];
        if(!empty($iddata)){
            foreach($iddata as $k=>$v){
                $idarray[] = $iddata[$k]['id'];
            }
            //去重
            $ids = array_unique($idarray);
            $curriculumidss = implode(',',$ids);
        }else{
            $curriculumidss = '';
        }
        //获取当前页数数据
        $curriculumdata = $curriculumobj->getSetCurriculum($where,$limitstr,$curriculumidss);
        $list = self::curriculumHandle($curriculumids,$curriculumdata);
        //获取符合条件的数据的总条数
        $total = $curriculumobj->getSetCurriculumCount($where,$curriculumidss);
        $result = [
            'curriculumlist'=>$list,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //
            ]
        ] ;
        return return_format($result,0,'操作成功') ;
    }
    /**
     * [curriculumHandle 课程选中状态标记]
     * @param  [type] $curriculumids [课程ids]
     * @param  [type] $curriculumdata [课程数据]
     * @return [array]
     */
    public function curriculumHandle($curriculumids,$curriculumdata)
    {
        $ids = explode(',',$curriculumids);
        if(is_array($ids)){
            foreach($ids as $k=>$v){
                foreach($curriculumdata as $j=>&$h){
                    if($curriculumdata[$j]['id'] == $ids[$k]) {
                        $curriculumdata[$j]['checked']=1;
                    }else{
                        $curriculumdata[$j]['checked']=0;
                    }
                }
            }
        }
        return $curriculumdata;
    }
    /**
     * [curriculumHandle 课程选中状态标记]
     * @param  [type] $curriculumids [课程ids]
     * @param  [type] $curriculumdata [课程数据]
     * @return [array]
     */
    public function setModify($param)
    {
        $modify = new Promotion();
        //编辑套餐的时候，查看当前是都有人购买该套餐
        $res = $modify->UpdateBuyModify($param);
        if($res){
            return return_format('修改成功',0,'操作成功');
        }else{
            return return_format('修改失败',60000,'操作失败');
        }
    }
    public function categoryTrans($categoryids)
    {
        $setobj = new Promotion();
        $categoryarray = $setobj->Trans($categoryids);
        if(!$categoryarray){
            return return_format('操作失败',60000,'操作失败');
        }
        $category = [];
        foreach($categoryarray as $v){
            $category[] = $v['categoryname'];
        }
        $trans = implode(",",$category);
        $result = [
            'categoryname'=>$trans,
        ] ;
        return return_format($result,0,'操作成功');
    }

}