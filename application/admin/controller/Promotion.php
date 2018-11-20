<?php
namespace app\admin\controller;
use login\Authorize;
use app\admin\business\PromotionTui;
use think\Controller;
class Promotion extends Authorize
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * [curriculumCategoryList 课程推荐-分类列表]
     * @Author ZQY
     * @DateTime 2018-09-11 19:27:22
     * @return   [array]        [课程推荐-分类列表]
     * URL:/admin/Proadmin/curriculumCategoryList
     */
    public function curriculumCategoryList()
    {
        $categoryname = $this->request->param('categoryname');//课程名称
        $pagenum = $this->request->param('pagenum');//分页页码
        $limit = config('param.pagesize')['admin_curriculumpromotion_list'];
        $category = new PromotionTui;
        $category_list = $category->getCategoryHandle($categoryname,$pagenum,$limit);
        $this->ajaxReturn($category_list);
        return $category_list;
    }
    /**
     * [categoryAdd 课程推荐-分类列表-添加分类]
     * @Author ZQY
     * @DateTime 2018-09-11 20:08:22
     * @return   [array]        [课程推荐-分类列表-添加分类]
     * URL:/admin/Proadmin/categoryAdd
     */
    public function categoryAdd()
    {
        $categoryname = $this->request->param('categoryname');//课程名称
        $category = new PromotionTui;
        $category_add = $category->CategoryInsert($categoryname);
        $this->ajaxReturn($category_add);
        return $category_add;
    }
    /**
     * [courseAdd 课程推荐-分类列表-选择]
     * @Author ZQY
     * @DateTime 2018-09-11 21:33:22
     * @return   [array]        [课程推荐-分类列表-选择]
     * URL:/admin/Proadmin/courseAdd
     */
    public function courseAdd()
    {
        $categoryid = $this->request->param('categoryid');//课程名称
        $courseids = $this->request->param('courseids');//课程ids
        $course = new PromotionTui;
        $course_add = $course->courseAdd($categoryid,$courseids);
        $this->ajaxReturn($course_add);
        return $course_add;
    }
    /**
     * [categorydel 课程推荐-分类列表-分类删除]
     * @Author ZQY
     * @DateTime 2018-09-11 21:33:22
     * @return   [array] [课程推荐-分类列表-分类删除]
     * URL:/admin/Proadmin/courseAdd
     */
    public function categorydel()
    {
        $categoryid = $this->request->param('categoryid');//课程名称
        $category = new PromotionTui;
        $categoryid_del = $category->proCategoryDel($categoryid);
        $this->ajaxReturn($categoryid_del);
        return $categoryid_del;
    }
    /**
     * [CategoryRecoSort 课程推荐-分类列表-上移、下移]
     * @Author ZQY
     * @DateTime 2018-09-11 19:27:22
     * @return   [array]        [课程推荐-分类列表]
     * URL:/admin/Proadmin/CategoryRecoSort
     */
    public function CategoryRecoSort()
    {
        $idx1   = $this->request->param('categoryid1');
        $idx2   = $this->request->param('categoryid2');
        if(!empty($idx1)&&!empty($idx2)&&$idx1!=$idx2){
            $curricu = new PromotionTui;
            $category_sort = $curricu->sortCategory($idx1,$idx2);
            $this->ajaxReturn($category_sort);
            return $category_sort;
        }
    }

    /**
     * [CategoryRecoSee 课程推荐-分类列表-查看]
     * @Author ZQY
     * @DateTime 2018-09-11 20:32:22
     * @return   [array]        [分类列表-查看]
     * URL:/admin/Proadmin/CategoryRecoSee
     */
    public function CategoryRecoSee()
    {
        $id   = $this->request->param('categoryid');
        $category = new PromotionTui;
        $category_see = $category->seeCategory($id);
        $this->ajaxReturn($category_see);
        return $category_see;
    }

    /**
     * [curriculumRecoList 课程推荐列表]
     * @Author ZQY
     * @DateTime 2018-08-27 14:32:22
     * @return   [array]        [课程推荐列表]
     * URL:/admin/Proadmin/curriculumRecoList
     */
    public function curriculumRecoList()
    {
        $curriculum = $this->request->param('coursename');//课程名称
        $pagenum = $this->request->param('pagenum');//分页页码
        $recommend = $this->request->param('recommend');//分页页码
        $limit = config('param.pagesize')['admin_curriculumpromotion_list'];
        $curricu = new PromotionTui;
        $curricu_list = $curricu->getCurriculum($curriculum,$pagenum,$limit,$recommend);
        $this->ajaxReturn($curricu_list);
        return $curricu_list;
    }
    /**
     * [courseDel 课程推荐删除-假删除]
     * @Author ZQY
     * @DateTime 2018-09-12 14:06:01
     * @return   [array]        [课程推荐删除
     * URL:/admin/Proadmin/courseDel
     */
    public function courseDel()
    {
        $categoryid= $this->request->param('categoryid');//课程id
        $courseid = $this->request->param('courseids');//课程id
        $curricu = new PromotionTui;
        $curricu_del = $curricu->delCurriculum($categoryid,$courseid);
        $this->ajaxReturn($curricu_del);
        return $curricu_del;
    }
    /**
     * [curriculumRecoDel 课程推荐添加]
     * @Author ZQY
     * @DateTime 2018-08-27 17:12:23
     * @return   [array]        [课程推荐添加
     * URL:/admin/Proadmin/curriculumRecoAdd
     */
    public function curriculumRecoAdd()
    {
        $curriculumids = $this->request->param('curriculumid');//课程id
        if(!empty($curriculumids)){
            $curricu = new PromotionTui;
            $curricu_del = $curricu->addCurriculum($curriculumids);
            $this->ajaxReturn($curricu_del);
            return $curricu_del;
        }
    }
    /**
     * [curriculumRecoDel 课程推荐上移、下移]
     * @Author ZQY
     * @DateTime 2018-08-27 19:46:23
     * @return   [array]
     * URL:/admin/Proadmin/curriculumRecoSort
     */
    public function curriculumRecoSort()
    {
        $idx1   = $this->request->param('curriculumid1');
        $idx2   = $this->request->param('curriculumid2');
        if(!empty($idx1)&&!empty($idx2)&&$idx1!=$idx2){
            $curricu = new PromotionTui;
            $curricu_sort = $curricu->sortCurriculum($idx1,$idx2);
            $this->ajaxReturn($curricu_sort);
            return $curricu_sort;
        }
    }
    /**
     * [curriculumRecoList 老师推荐列表]
     * @Author ZQY
     * @DateTime 2018-08-27 20:50:02
     * @return   [array]        [老师推荐列表]
     * URL:/admin/Proadmin/teacherRecoList
     */
    public function teacherRecoList()
    {
        $teachername = $this->request->param('teachername');//课程名称
        $pagenum = $this->request->param('pagenum');//分页页码
        $recommend = $this->request->param('recommend');//是否为推荐类型

        empty($recommend)?$recommend = 0:$recommend;
        //机构 标识id
        $limit = config('param.pagesize')['admin_teacherpromotion_list'];

        $curricu = new PromotionTui;
        $curricu_list = $curricu->getTeacher($teachername,$pagenum,$limit,$recommend);
        $this->ajaxReturn($curricu_list);
        return $curricu_list;
    }
    /**
     * [curriculumRecoDel 老师推荐删除]
     * @Author ZQY
     * @DateTime 2018-08-28 09:28:05
     * @return   [array]        [老师推荐删除
     * URL:/admin/Proadmin/teacherRecoDel
     */
    public function teacherRecoDel()
    {
        $teacherid = $this->request->param('teacherid');//课程id
        if(!empty($teacherid)) {
            $teacher = new PromotionTui;
            $teacher_del = $teacher->delTeacher($teacherid);
            $this->ajaxReturn($teacher_del);
            return $teacher_del;
        }
    }
    /**
     * [curriculumRecoDel 老师推荐添加]
     * @Author ZQY
     * @DateTime 2018-08-28 09:56:05
     * @return   [array]        [老师推荐添加
     * URL:/admin/Proadmin/teacherRecoAdd
     */
    public function teacherRecoAdd()
    {
        $teacherid = $this->request->param('teacherid');//课程id
        if(!empty($teacherid)) {
            $teacher = new PromotionTui;
            $teacher_add = $teacher->addTeacher($teacherid);
            $this->ajaxReturn($teacher_add);
            return $teacher_add;
        }
    }
    /**
     * [curriculumRecoDel 老师推荐上移、下移]
     * @Author ZQY
     * @DateTime 2018-08-28 10:43:59
     * @return   [array]
     * URL:/admin/Proadmin/teacherRecoSort
     */
    public function teacherRecoSort()
    {
        $idx1   = $this->request->param('teacherid1');
        $idx2   = $this->request->param('teacherid2');
        if(!empty($idx1)&&!empty($idx2)&&$idx1!=$idx2) {
            $teacher = new PromotionTui;
            $teacher_sort = $teacher->sortTeacher($idx1, $idx2);
            $this->ajaxReturn($teacher_sort);
            return $teacher_sort;
        }
    }
    /**
     * [HomepageAdsList 首页广告列表]
     * @Author ZQY
     * @DateTime 2018-08-28 10:43:59
     * @return   [array]
     * URL:/admin/Proadmin/HomepageAdsList
     */
    public function HomepageAdsList()
    {
        //实例化逻辑层
        $homeList = new PromotionTui;
        $listdata = $homeList->homeList();
        $this->ajaxReturn($listdata);
        return $listdata;
    }
    /**
     * [HomepageAdsDel 首页广告列表删除]
     * @Author ZQY
     * @DateTime 2018-08-28 14:43:59
     * @return   [array]
     * URL:/admin/Proadmin/HomepageAdsDel
     */
    public function HomepageAdsDel()
    {
        $ads_id   = $this->request->param('adsid');
        if(!empty($ads_id)){
            $homeDel = new PromotionTui;
            $ads_del = $homeDel->homeDel($ads_id);
            $this->ajaxReturn($ads_del);
            return $ads_del;
        }
    }
    /**
     * [HomepageAdsUpload 首页广告图片添加]
     * @Author ZQY
     * @DateTime 2018-08-28 15:43:59
     * @return   [array]
     * URL:/admin/Proadmin/HomepageAdsUpload
     */
    public function HomepageAdsUpload()
    {
        $param  = $this->request->param();//提交参数
        //添加广告信息
        $home = new PromotionTui;
        $ads_Add = $home->homeAdd($param);
        $this->ajaxReturn($ads_Add);
    }
    /**
     * [HomeCourseList 首页广告-课程、老师列表]
     * @Author ZQY
     * @DateTime 2018-09-11 16:29:00
     * @return   [array]
     * URL:/admin/Proadmin/HomeCourseList
     */
    public function HomeCourseList()
    {
        $param  = $this->request->param();//提交参数
        $home = new PromotionTui;
        $limit = config('param.pagesize')['admin_curriculum_list'];
        //判断请求类型1：请求课程列表 2：请求老师列表
        if($param['listtype']==1){
            $list = $home->homeCourse($param,$limit);
        }elseif($param['listtype']==2){
            $list = $home->homeTeacher($param,$limit);
        }
        $this->ajaxReturn($list);
        return $list;
    }
    /**
     * [GetHomepageAds 获取需要编辑的首页广告数据]
     * @Author ZQY
     * @DateTime 2018-09-12 15:37:33
     * @return   [array]
     * URL:/admin/Proadmin/GetHomepageAds
     */
    public function GetHomepageAds()
    {
        $Adsid  = $this->request->param('adsid');//广告名称
        $home = new PromotionTui;
        $ads_get = $home->homeGet($Adsid);
        $this->ajaxReturn($ads_get);
    }
    /**
     * [HomeAdsExpress 获取需要编辑的首页广告数据]
     * @Author ZQY
     * @DateTime 2018-09-12 16:01:33
     * @return   [array]
     * URL:/admin/Proadmin/HomeAdsExpress
     */
    public function HomeAdsExpress()
    {
        $home = new PromotionTui;
        $param  = $this->request->param();//广告名称
        $limit = config('param.pagesize')['admin_curriculum_list'];
        if($param['expresstype']==1){
            $res = $home->courseExpress($param,$limit);
        }elseif($param['expresstype']==2){
            $res = $home->teacherExpress($param,$limit);
        }
        $this->ajaxReturn($res);
    }
    /**
     * [HomepageAdsEdit 首页广告编辑]
     * @Author ZQY
     * @DateTime 2018-08-29 11:01:59
     * @return   [array]
     * URL:/admin/Proadmin/HomepageAdsEdit
     */
    public function HomepageAdsEdit()
    {
        $param = $this->request->param();
        $homeDel = new PromotionTui;
        $ads_Edit = $homeDel->homeEdit($param);
        $this->ajaxReturn($ads_Edit);
    }
    /**
     * [TypeProList 分类列表]
     * @Author ZQY
     * @DateTime 2018-08-29 15:12:23
     * @return   [array]
     * URL:/admin/Proadmin/TypeProList
     */
    public function TypeProList()
    {
        $recommend  = $this->request->param('recommend');
        $homeList = new PromotionTui;
        $listdata = $homeList->typeList($recommend);
        $this->ajaxReturn($listdata);
        return $listdata;
    }

    /**
     * [TypeProAdd 分类添加]
     * @Author ZQY
     * @DateTime 2018-08-29 15:12:23
     * @return   [array]
     * URL:/admin/Proadmin/TypeProAdd
     */
    public function TypeProAdd()
    {
        $typeid = $this->request->param('typeid');//课程id
        if(!empty($typeid)){
            $type = new PromotionTui;
            $type_add = $type->addType($typeid);
            $this->ajaxReturn($type_add);
            return $type_add;
        }
    }
    /**
     * [TypeProDel 分类删除]
     * @Author ZQY
     * @DateTime 2018-08-29 19:24:23
     * @return   [array]
     * URL:/admin/Proadmin/TypeProDel
     */
    public function TypeProDel()
    {
        $typeid = $this->request->param('typeid');//课程id
        if(!empty($typeid)){
            $type = new PromotionTui;
            $type_del = $type->delType($typeid);
            $this->ajaxReturn($type_del);
            return $type_del;
        }
    }
    /**
     * [FreeCollectionList 促销]
     * @Author ZQY
     * @DateTime 2018-09-06 15:53:23
     * @return   [array]
     * URL:/admin/Proadmin/FreeCollectionList
     */
    public function FreeCollectionList()
    {
        $start_time = $this->request->param('start_time');//课程id
        $end_time = $this->request->param('end_time');//课程id
        $page_num = $this->request->param('page_num');//课程id
        //显示页数
        $limit = config('param.pagesize')['admin_freeclass_list'];
        $free_class = new PromotionTui;
        $free_array = $free_class->freeCollection($start_time,$end_time,$page_num,$limit);
        $this->ajaxReturn($free_array);
        return $free_array;
    }
    /**
     * [SetProList 套餐列表]
     * @Author ZQY
     * @DateTime 2018-08-31 14:43:00
     * @return   [array]
     * URL:/admin/Proadmin/SetProList
     */
    public function SetProList()
    {
        $setname = $this->request->param('setname');//套餐名称
        $pagenum = $this->request->param('pagenum');//分页页码
        empty($recommend)?$recommend=0:$recommend;//默认页数
        //获取每页展示页数
        $limit = config('param.pagesize')['admin_setpro_list'];
        //实例化套餐逻辑层
        $set = new PromotionTui;
        $set_list = $set->getSet($setname,$pagenum,$limit);
        $this->ajaxReturn($set_list);
        return $set_list;
    }
    /**
     * [SetListShelf 套餐列表-上下状态]
     * @Author ZQY
     * @DateTime 2018-09-06 10:44:00
     * @return   [array]
     * URL:/admin/Proadmin/SetListShelf
     */
    public function SetListShelf()
    {
        $setid = $this->request->param('setid');//套餐id
        $shelf = $this->request->param('shelf');//上下状态
        //实例化套餐逻辑层
        $set = new PromotionTui;
        $set_list = $set->updateSetShelf($setid,$shelf);
        $this->ajaxReturn($set_list);
        return $set_list;
    }

    /**
     * [SetCategory 添加套餐-选择课程分类]
     * @Author ZQY
     * @DateTime 2018-09-01 11:18:00
     * @return   [array]
     * URL:/admin/Proadmin/SetCategory
     */
    public function SetCategory()
    {
        //实例化套餐逻辑层
        $set = new PromotionTui;
        $set_category = $set->getSetCategory();
        $this->ajaxReturn($set_category);
        return $set_category;
    }
    /**
     * [SetAddCurriculum 添加套餐-指定课程]
     * @Author ZQY
     * @DateTime 2018-09-01 16:14:18
     * @return   [array]
     * URL:/admin/Proadmin/SetAddCurriculum
     */
    public function SetAddCurriculum()
    {
        $coursename = $this->request->param('coursename');//课程名称
        $pagenum = $this->request->param('pagenum');//分页页码
        $classtypes = $this->request->param('classtypes');//开班类型
        //机构 标识id
        $limit = config('param.pagesize')['admin_curriculum_list'];
        $set = new PromotionTui;
        $set_curriculum = $set->getSetCurriculum($pagenum,$limit,$coursename,$classtypes);
        $this->ajaxReturn($set_curriculum);
        return $set_curriculum;
    }
    /**
     * [SetDetailedList 套套餐列表-套餐明细]
     * @Author ZQY
     * @DateTime 2018-09-03 10:09:18
     * @return   [array]
     * URL:/admin/Proadmin/SetDetailedList
     */
    public function SetDetailedList()
    {
        $setid = $this->request->param('setid');
        //机构 标识id
        $set = new PromotionTui;
        $set_detailed = $set->getSetDetailed($setid);
        $this->ajaxReturn($set_detailed);
        return $set_detailed;
    }
    /**
     * [SetDataList 套餐列表-数据列表]
     * @Author ZQY
     * @DateTime 2018-09-03 15:59:18
     * @return   [array]
     * URL:/admin/Proadmin/SetDataList
     */
    public function SetDataList()
    {
        // 0:未使用 1:使用 2:已过期
        $usestatus = $this->request->param('usestatus');
        $pagenum = $this->request->param('pagenum');
        $setid = $this->request->param('setid');
        $limit = config('param.pagesize')['admin_setdata_list'];
        //机构 标识id
        $set = new PromotionTui;
        $set_data = $set->getSetData($usestatus,$pagenum,$limit,$setid);
        $this->ajaxReturn($set_data);
        return $set_data;
    }
    /**
     * [SetProDel 套餐列表-删除]
     * @Author ZQY
     * @DateTime 2018-9-5 14:19:33
     * @return   [array]
     * URL:/admin/Proadmin/SetProDel
     */
    public function SetProDel()
    {
        // 套餐id
        $setid = $this->request->param('setid');
        $set = new PromotionTui;
        $set_del = $set->setDel($setid);
        $this->ajaxReturn($set_del);
        return $set_del;
    }
    /**
     * [SetInsert 套餐添加]
     * @Author ZQY
     * @DateTime 2018-09-07 10:10:33
     * @return   [array]
     * URL:/admin/Proadmin/SetInsert
     */
    public function SetInsert()
    {
        // 套餐id
        $param = $this->request->param();
//        return return_format($param);die;
        $set = new PromotionTui;
        $set_add= $set->setInsert($param);
        $this->ajaxReturn($set_add);
        return $set_add;
    }
    /**
     * [SetUpdate 获取编辑数据]
     * @Author ZQY
     * @DateTime 2018-09-08 16:48:33
     * @return   [array]
     * URL:/admin/Proadmin/SetUpdate
     */
    public function SetUpdate()
    {
        // 套餐id
        $setid = $this->request->param('setid');
        $set = new PromotionTui;
        $set_update= $set->SetUpdate($setid);
        $this->ajaxReturn($set_update);
        return $set_update;
    }
    /**
 * [SetUpdateCategory 套餐列表-编辑-指定分类]
 * @Author ZQY
 * @DateTime 2018-09-08 17:48:33
 * @return   [array]
 * URL:/admin/Proadmin/SetUpdateCategory
 */
    public function SetUpdateCategory()
    {
        $categoryids = $this->request->param('categoryids');
        //实例化套餐逻辑层
        $set = new PromotionTui;
        $set_category = $set->UpdateCategory($categoryids);
        $this->ajaxReturn($set_category);
        return $set_category;
    }
    /**
     * [SetUpdateCurriculum 套餐列表-编辑-指定课程]
     * @Author ZQY
     * @DateTime 2018-09-10 17:13:33
     * @return   [array]
     * URL:/admin/Proadmin/SetUpdateCurriculum
     */
    public function SetUpdateCurriculum()
    {
        $coursename = $this->request->param('coursename');//课程名称
        $pagenum = $this->request->param('pagenum');//分页页码
        $classtypes = $this->request->param('classtypes');//开班类型
        $curriculumids = $this->request->param('curriculumids');//课程id
        //机构 标识id
        $limit = config('param.pagesize')['admin_curriculum_list'];
        $set = new PromotionTui;
        $set_curriculum = $set->getSetCurriculums($pagenum,$limit,$coursename,$classtypes,$curriculumids);
        $this->ajaxReturn($set_curriculum);
        return $set_curriculum;
    }
    /**
     * [SetModify 套餐修改]
     * @Author ZQY
     * @DateTime 2018-09-10 20:06:33
     * @return   [array]
     * URL:/admin/Proadmin/SetModify
     */
    public function SetModify()
    {
        // 套餐id
        $param = $this->request->param();
        $set = new PromotionTui;
        $set_update= $set->setModify($param);
        $this->ajaxReturn($set_update);
        return $set_update;
    }
    /**
     * [CategoryTransformation 套餐修改]
     * @Author ZQY
     * @DateTime 2018-09-11 11:16:33
     * @return   [array]
     * URL:/admin/Proadmin/CategoryTransformation
     */
    public function CategoryTransformation()
    {
        // 套餐id
        $categoryids = $this->request->param('categoryids');
        $set = new PromotionTui;
        $transformation = $set->categoryTrans($categoryids);
        $this->ajaxReturn($transformation);
        return $transformation;
    }



}
