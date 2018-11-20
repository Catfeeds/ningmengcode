<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Promotion extends Model
{
    protected $table = 'nm_curriculum';
    protected $teacher_table = 'nm_teacherinfo';
    protected $homepageads_table = 'nm_organslideimg';
    protected $category_table = 'nm_category';
    protected $coursepackage_table = 'nm_coursepackage';
    protected $setorder_table = 'nm_coursepackageorder';
    protected $setuse_table = 'nm_coursepackageuse';
    protected $receive_table = 'nm_receive';
    protected $coursepackagegift_table = 'nm_coursepackagegift';
    protected $curriculumcategory_table = 'nm_recommendcategory';


    //自定义初始化
//    protected function initialize()
//    {
//        parent::initialize();
//    }

    /**
     * [getCategoryList 获取课程推荐-分类列表]
     * @Author ZQY
     * @DateTime 2018-09-11 19:36:41+0800
     * @return   [array]  [获取课程推荐-分类列表]
     */
    public function getCategoryList($where,$limitstr){
        return Db::table($this->curriculumcategory_table)
            ->field('id,categoryname,categorysort')
            ->where($where)
            ->limit($limitstr)
            ->order('categorysort','desc')
            ->select();
    }
    /**
     * [getCategoryListCount 获取课程推荐-分类数]
     * @Author ZQY
     * @DateTime 2018-09-11 19:36:41+0800
     * @return   [array]  [获取课程推荐-分类数]
     */
    public function getCategoryListCount($where){
        return Db::table($this->curriculumcategory_table)
            ->field('id,categoryname,categorysort')
            ->where($where)
            ->count();
    }
    /**
     * [getCurriculumList 推荐课程分类排序]
     * @Author ZQY
     * @DateTime 2018-08-27T20:02:49+0800
     * @return [array]
     */
    public function sortCategory($where)
    {
        $arr = Db::table($this->curriculumcategory_table)->field('id,categorysort')->where($where)->select();
        if(!$arr){
            return return_format('需要交换的课程id不存在',60006,'error');
        }
        if(count($arr)==2){
            Db::table($this->curriculumcategory_table)->where(['id'=>$arr[0]['id']])->update(['categorysort'=>$arr[1]['categorysort']]);
            Db::table($this->curriculumcategory_table)->where(['id'=>$arr[1]['id']])->update(['categorysort'=>$arr[0]['categorysort']]);
            return return_format('',0,'操作成功') ;
        }else{
            return return_format('',60003,'error');
        }

    }
    /**
     * [getCategoryIds 获取分类id]
     * @Author ZQY
     * @DateTime 2018-09-11T20:38:49+0800
     * @return [array]
     */
    public function getCategoryIds($id)
    {
        return Db::table($this->curriculumcategory_table)->field('curriculumids')->where(['id'=>$id])->select();

    }
    /**
     * [$categoryname 添加分类]
     * @Author ZQY
     * @DateTime 2018-09-11T21:15:49+0800
     * @return [array]
     */
    public function addCategory($categoryname)
    {
        //添加排序值
        $big = Db::query("select (max(categorysort))+1 big from nm_recommendcategory");
        if(empty($big[0]['big'])){
            return Db::table($this->curriculumcategory_table)->insert(['categoryname'=>$categoryname]);
        }
        return Db::table($this->curriculumcategory_table)->insert(['categoryname'=>$categoryname,'categorysort'=>$big[0]['big']]);

    }
    /**
     * [$categoryname 添加分类]
     * @Author ZQY
     * @DateTime 2018-09-11T21:15:49+0800
     * @return [array]
     */
    public function delCategory($categoryid)
    {
        return Db::table($this->curriculumcategory_table)->where(['id'=>"$categoryid"])->delete();
    }
    /**
     * [$categoryname 分类列表-添加课程]
     * @Author ZQY
     * @DateTime 2018-09-12T10:23:49+0800
     * @return [array]
     */
    public function addCourse($addcourseids,$categoryid)
    {
        $res = Db::table($this->curriculumcategory_table)->where(['id'=>$categoryid])->update(['curriculumids'=>$addcourseids]);
        Db::table($this->curriculumcategory_table)->where(['id'=>$categoryid])->update(['curriculumids'=>$addcourseids]);
        $ids = explode(",",$addcourseids);
        //给排序赋值，排序使用
        if($res){
            foreach($ids as $v){
                //获取最大排序值
                $big = Db::query("select (max(currsort))+1 big from nm_curriculum");
                $sort = Db::table($this->table)->where(['id'=>$v])->update(['currsort'=>$big[0]['big'],'recommend'=>1]);
            }
            return $sort;
        }

    }
    /**
     * [courseTesing 添加分类]
     * @Author ZQY
     * @DateTime 2018-09-12T10:03:49+0800
     * @return [array]
     */
    public function courseTesing($categoryid)
    {
        return Db::table($this->curriculumcategory_table)->field("curriculumids")->where(['id'=>$categoryid])->select();
    }
    /**
     * [getCurriculumArray 获取课程数据]
     * @Author ZQY
     * @DateTime 2018-09-11T20:38:49+0800
     * @return [array]
     */
    public function getCurriculumArray($ids)
    {
        return Db::table($this->table)->field('id,coursename')->where('id','in',$ids)->order('currsort','desc')->select();
    }
    /**
     * [getCurriculumList 获取课程推荐列表]
     * @Author ZQY
     * @DateTime 2018-08-27T09:59:41+0800
     * @return   [array]  [返回推荐课程列表数组]
     */
    public function getCurriculumList($recommend,$curriculum,$limitstr){
       return Db::table($this->table)
            ->where(function($query) use($curriculum,$recommend){
                $query->where('classtypes','eq','1')
                        ->where('coursename','like',"%$curriculum%")
                        ->where('delflag','eq','1')
                        ->where('recommend','eq',"$recommend")
                        ->where('status','eq','1');
            })
            ->whereOr(function($query) use($curriculum,$recommend){
                $query->where('classtypes','eq','2')
                    ->where('coursename','like',"%$curriculum%")
                    ->where('delflag','eq','1')
                    ->where('recommend','eq',"$recommend")
                    ->where('status','eq','1')
                    ->where('classnum','gt','0');
            })
            ->field('id,coursename,currsort')
            ->limit($limitstr)
            ->order('currsort','desc')
            ->select();
    }
    /**
     * [getOrderListCount 获取推荐列表总行数]
     * @Author ZQY
     * @DateTime 2018-08-27T09:54:41+0800
     * @return   [array]        [返回推荐课程页数]
     */
    public function getCurriculumListCount($recommend,$curriculum){
        return Db::table($this->table)
            ->where(function($query) use($curriculum,$recommend){
                $query->where('classtypes','eq','1')
                    ->where('coursename','like',"%$curriculum%")
                    ->where('delflag','eq','1')
                    ->where('recommend','eq',"$recommend")
                    ->where('status','eq','1');
            })
            ->whereOr(function($query) use($curriculum,$recommend){
                $query->where('classtypes','eq','2')
                    ->where('coursename','like',"%$curriculum%")
                    ->where('delflag','eq','1')
                    ->where('recommend','eq',"$recommend")
                    ->where('status','eq','1')
                    ->where('classnum','gt','0');
            })
            ->count();

//        return Db::table($this->table)->where($where)->count();
    }
    /**
     * [delCurriculuml 更改分类下的课程ids]
     * @Author ZQY
     * @DateTime 2018-08-27T15:40:41+0800
     * @return  [array]
     */
    public function delCurriculuml($categoryid,$curriculumids,$courseid)
    {
        Db::startTrans();
        //更改分类下的课程ids
        $update_courseids = Db::table($this->curriculumcategory_table)->where(['id'=>$categoryid])->update(['curriculumids'=>"$curriculumids"]);
        //更改课程推荐表中的推荐状态
        $res = Db::table($this->table)->where(['id'=>"$courseid"])->update(['recommend'=>'0']);
        if($update_courseids&&$res){
            // 提交事务
            Db::commit();
            return true;
        }else {
            // 回滚事务
            Db::rollback();
            return false;
        }


    }
    /**
     * [getCurriculumList 添加推荐课程]
     * @Author ZQY
     * @DateTime 2018-08-27T17:53:48+0800
     * @return [array]
     */
    public function AddCurriculuml($curriculumids)
    {
        //修改是否为推荐课状态
        $where = [];
        $where['id'] = ['in',$curriculumids];
        $res = Db::table($this->table)->where($where)->update(['recommend'=>'1']);
        //给排序赋值，排序使用
        if($res){
            foreach($curriculumids as $v){
                //获取最大排序值
                $big = Db::query("select (max(currsort))+1 big from nm_curriculum");
                $sort = Db::table($this->table)->where(['id'=>$v])->update(['currsort'=>$big[0]['big']]);
            }
            return $sort;
        }
    }
    /**
     * [AddCount 检测课程推荐数量]
     * @Author ZQY
     * @DateTime 2018-09-04T20:02:49+0800
     * @return [array]
     */
    public function AddCount()
    {
        //课程推荐数量统计
        return Db::table($this->table)
            ->where(function($query){
                $query->where('classtypes','eq','1')
                    ->where('delflag','eq','1')
                    ->where('recommend','eq','1')
                    ->where('status','eq','1');
            })
            ->whereOr(function($query){
                $query->where('classtypes','eq','2')
                    ->where('delflag','eq','1')
                    ->where('recommend','eq','1')
                    ->where('status','eq','1')
                    ->where('classnum','gt','0');
            })
            ->count();
    }
    /**
     * [getCurriculumList 推荐课程排序]
     * @Author ZQY
     * @DateTime 2018-08-27T20:02:49+0800
     * @return [array]
     */
    public function sortCurriculuml($where)
    {
        $arr = Db::table($this->table)->field('id,currsort')->where($where)->select();
        if(!$arr){
            return return_format('需要交换的课程id不存在',60006,'error');
        }
        if(count($arr)==2){
            Db::table($this->table)->where(['id'=>$arr[0]['id']])->update(['currsort'=>$arr[1]['currsort']]);
            Db::table($this->table)->where(['id'=>$arr[1]['id']])->update(['currsort'=>$arr[0]['currsort']]);
            return return_format('',0,'操作成功') ;
        }else{
            return return_format('',60003,'error');
        }

    }
    /**
     * [getCurriculumList 获取老师推荐列表]
     * @Author ZQY
     * @DateTime 2018-08-27T20:59:41+0800
     * @return   [array]  [返回推荐老师列表数组]
     */
    public function getTeacherList($where,$limitstr)
    {
         return Db::table($this->teacher_table)->where($where)->field('teacherid,nickname teachername,sortnum')->limit($limitstr)->order('sortnum','desc')->select();
    }
    /**
     * [getOrderListCount 获取推荐列表总行数]
     * @Author ZQY
     * @DateTime 2018-08-27T09:54:41+0800
     * @return   [array]        [返回推荐课程页数]
     */
    public function getteacherListCount($where){
        return Db::table($this->teacher_table)->where($where)->count();
    }
    /**
     * [AddTeacher 老师推荐删除]
     * @Author ZQY
     * @DateTime 2018-08-28T09:38:41+0800
     * @return   [array]
     */
    public function delTeacher($teacher_id)
    {
        $where['teacherid'] = ['in',$teacher_id];
        return Db::table($this->teacher_table)->where($where)->update(['recommend'=>'0']);
    }
    /**
     * [AddTeacher 老师推荐添加]
     * @Author ZQY
     * @DateTime 2018-08-28 10:01:41
     * @return   [array]
     */
    public function addTeacher($teacher_id)
    {
        $where['teacherid'] = ['in',$teacher_id];
        $res = Db::table($this->teacher_table)->where($where)->update(['recommend'=>'1']);
        //给排序赋值，排序使用
        if($res){
            foreach($teacher_id as $v){
                $big = Db::query("select (max(sortnum))+1 big from  nm_teacherinfo");
                $sort = Db::table($this->teacher_table)->where(['teacherid'=>$v])->update(['sortnum'=>$big[0]['big']]);
            }
            return $sort;
        }
    }
    /**
     * [getCurriculumList 推荐老师排序]
     * @Author ZQY
     * @DateTime 2018-08-28T09:02:49+0800
     * @return [array]
     */
    public function sortTeacher($where)
    {
        $arr = Db::table($this->teacher_table)->field('teacherid,sortnum')->where($where)->select();
        if(!$arr) return;
        Db::table($this->teacher_table)->where(['teacherid'=>$arr[0]['teacherid']])->update(['sortnum'=>$arr[1]['sortnum']]);
        return Db::table($this->teacher_table)->where(['teacherid'=>$arr[1]['teacherid']])->update(['sortnum'=>$arr[0]['sortnum']]);

    }

    /**
     * [listHomepage 首页广告列表数据]
     * @Author ZQY
     * @DateTime 2018-08-28T14:49:49+0800
     * @return [array]
     */
    public function listHomepage()
    {
        $res = Db::query('SELECT id,remark,imagepath,urltype,url,(SELECT nickname FROM nm_teacherinfo  WHERE teacherid = nm_organslideimg.teacherid) AS teachername,
(SELECT coursename FROM nm_curriculum WHERE id = nm_organslideimg.courseid) AS coursename
FROM nm_organslideimg order by id desc');
        return $res;
    }
    /**
     * [delHomepage 首页广告删除]
     * @Author ZQY
     * @DateTime 2018-08-28T15:07:49+0800
     * @return [array]
     */
    public function delHomepage($ads_id)
    {
        //删除首页广告
        return Db::table($this->homepageads_table)->where(['id'=>$ads_id])->delete();
    }

    /**
     * [delHomepage 首页广告添加]
     * @Author ZQY
     * @DateTime 2018-08-29T10:07:49+0800
     * @return [array]
     */
    public function addHomepage($data)
    {
        return Db::table($this->homepageads_table)->insert($data);
    }

    /**
     * [getHomepage 获取首页广告]
     * @Author ZQY
     * @DateTime 2018-09-12T11:23:49+0800
     * @return [array]
     */
    public function getHomepage($Adsid)
    {
        return Db::table($this->homepageads_table)->where(['id'=>"$Adsid"])->select();
    }
    /**
     * [delHomepage 首页广告编辑]
     * @Author ZQY
     * @DateTime 2018-08-29T11:23:49+0800
     * @return [array]
     */
    public function editHomepage($param)
    {
         return Db::table($this->homepageads_table)
            ->where('id',$param['adsid'])
            ->update([
                'remark'  => $param['remark'],
                'imagepath'  => $param['imagepath'],
                'addtime'  => time(),
                'urltype'  => $param['urltype'],
                'teacherid'  => $param['teacherid'],
                'courseid'  => $param['courseid'],
                'url'  => $param['url'],
            ]);
//         return Db::table($this->homepageads_table)->getLastSql();
    }
    /**
     * [getCourseCount 获取广告条数]
     * @Author ZQY
     * @DateTime 2018-09-12T11:23:49+0800
     * @return [array]
     */
    public function getTeacherCount($teacherid)
    {
        $where = [];
        $where['teacherid'] = ['<=',"$teacherid"];
        $where['delflag'] = ['=',1];
        return Db::table($this->teacher_table)->where($where)->count();


    }
    /**
     * [getCourseCount 获取广告条数]
     * @Author ZQY
     * @DateTime 2018-09-12T11:23:49+0800
     * @return [array]
     */
    public function getCourseCount($courseid)
    {
        $where = [];
        $where['id'] = ['<=',"$courseid"];
        $where['status'] = ['=',1];
        $where['delflag'] = ['=',1];
        return Db::table($this->table)->where($where)->count();
    }
    /**
     * [CategoryTesting 获取分类的数量]
     * @Author ZQY
     * @DateTime 2018-09-12T18:23:49+0800
     * @return [array]
     */
    public function CategoryTest()
    {
        return Db::table($this->curriculumcategory_table)->where(['delflag'=>'1'])->count();
    }
    /**
     * [listType 分类列表]
     * @Author ZQY
     * @DateTime 2018-08-29T15:18:49+0800
     * @return [array]
     */
    public function listType($recommend)
    {
        $res = Db::query("select DISTINCT id,categoryname as categoryname,rank,(select count(*) from nm_curriculum where nm_category.id=nm_curriculum.categoryid and nm_category.delflag=1 and nm_category.recommend=$recommend) curriculum_num  from nm_category");
        return $res;
    }
    /**
     * [addType 分类推荐添加]
     * @Author ZQY
     * @DateTime 2018-08-29 19:16:41
     * @return   [array]
     */
    public function addType($type_id)
    {
        $where['id'] = ['in',$type_id];
        $res = Db::table($this->category_table)->where($where)->update(['recommend'=>'1']);
        //给排序赋值，排序使用
        if($res){
            foreach($type_id as $v){
                $big = Db::query("select (max(sort))+1 big from  nm_category");
                $sort = Db::table($this->category_table)->where(['id'=>$v])->update(['sort'=>$big[0]['big']]);
            }
            return $sort;
        }
    }
    /**
     * [delType 分类删除]
     * @Author ZQY
     * @DateTime 2018-08-29T19:32:49+0800
     * @return [array]
     */
    public function delType($typeid)
    {
        return Db::table($this->category_table)->where(['id'=>$typeid])->update(['delflag'=>'0']);
    }
    /**
     * [getFreeCollection ]
     * @Author ZQY
     * @DateTime 2018-09-06T19:32:49+0800
     * @return [array]
     */
    public function getFreeCollection($where,$limitstr)
    {
        return Db::table($this->receive_table)->where($where)->field('id,mobile,receivetime,prphone,name')->limit($limitstr)->order('receivetime','desc')->select();

    }
    /**
     * [getFreeCollection ]
     * @Author ZQY
     * @DateTime 2018-09-06T19:32:49+0800
     * @return [array]
     */
    public function getFreeCollectionCount($where)
    {
        return Db::table($this->receive_table)->where($where)->field('id,mobile,receivetime,areacode')->count();
    }
    /**
     * [getSetList 获取套餐列表]
     * @Author ZQY
     * @DateTime 2018-08-31T15:04:41
     * @return   [array]  [返回套餐列表数组]
     */
    public function getSetList($where,$limitstr)
    {
        return Db::table($this->coursepackage_table)->where($where)->field('id,setmeal,trialtype,threshold,setprice,efftype,effstarttime,effendtime,efftime,shelf')->limit($limitstr)->order('id','desc')->select();
    }

    /**
     * [updateShelf 套餐列表-上下状态]
     * @Author ZQY
     * @DateTime 2018-08-29T19:32:49+0800
     * @return [array]
     */
    public function updateShelf($setid,$shelf)
    {
        return Db::table($this->coursepackage_table)->where(['id'=>$setid])->update(['shelf'=>$shelf]);
    }
    /**
     * [getSetListCount 获取套餐列表总行数]
     * @Author ZQY
     * @DateTime 2018-08-31T15:21:41+0800
     * @return   [array]        [返回套餐页数]
     */
    public function getSetListCount($where){
        return Db::table($this->coursepackage_table)->where($where)->count();
    }
    /**
     * [getCategory 获取父级id下的子类]
     * @Author ZQY
     * @DateTime 2018-09-01 14:35:24
     * @return   [array]
     */
    public function getCategory(){
        $lists = Db::table($this->category_table)
            ->where('delflag', 'eq', 1)
            ->where('status', 'eq', 1)
            ->field('id,categoryname title,fatherid')->select();
        return $lists;
    }
    /**
     * [getCurriculumList 获取课程推荐列表]
     * @Author ZQY
     * @DateTime 2018-09-01T17:02:41+0800
     * @return   [array]  [返回推荐课程列表数组]
     */
    public function getSetCurriculum($where,$limitstr,$curriculumids){
          return Db::table($this->table)
            ->where(function($query) use($where){
                $query->where('delflag','eq','1')
                    ->where($where)
                    ->where('price','neq','0');
            })
            ->whereOr(function($query) use($curriculumids,$where){
                $query->where('id','in',"$curriculumids")
                    ->where($where);
            })
            ->field('id,coursename,imageurl,classtypes')
            ->limit($limitstr)
            ->order('currsort','desc')
            ->select();
    }
    /**
     * [getnoDeleteData 获取价格为零、未删除的课程]
     * @Author ZQY
     * @DateTime 2018-10-31T10:31:41+0800
     * @return   [array]  [获取价格为零、未删除的课程]
     */
    public function getnoDeleteData($where){
        return Db::table($this->table)
            ->alias('curr')
            ->join('nm_scheduling sch','curr.id=sch.curriculumid')
            ->where($where)
            ->field('curr.id')
//            ->group('curr.id')
            ->select();
    }
    /**
     * [getSetCurriculumCount 获取推荐列表总行数]
     * @Author ZQY
     * @DateTime 2018-09-01T17:02:41+0800
     * @return   [array]        [返回推荐课程页数]
     */
    public function getSetCurriculumCount($where,$curriculumids){
        return Db::table($this->table)
            ->where(function($query) use($where){
                $query->where('delflag','eq','1')
                    ->where($where)
                    ->where('price','neq','0');
            })
            ->whereOr(function($query) use($curriculumids,$where){
                $query->where('id','in',"$curriculumids")
                    ->where($where);
            })
            ->count();
    }

    /**
     * [getSetDetail 获取套餐明细]
     * @Author ZQY
     * @DateTime 2018-09-03T10:13:41+0800
     * @return   [array]        [返回套餐明细数组]
     */
    public function getSetBug($setid){
        return Db::table($this->setuse_table)
            ->alias('use')
            ->join('nm_coursepackage set','use.packageid = set.id')
            ->field('
                if(set.threshold=0,"无限制",set.threshold) threshold,
                elt(set.trialtype,"全部课程","指定分类","指定课程") trialtype,
                if(set.efftype=1,concat(FROM_UNIXTIME(set.effstarttime,"%Y年%m月%d"),"至",FROM_UNIXTIME(set.effendtime,"%Y年%m月%d")),set.efftime) efftype,
                set.bughour,set.givestatus,
                count(use.type=1 or null) bug,
                count(use.ifuse=1 and type=1 or null) suse,
                count(use.ifuse=0 and type=1 or null) nouse'
            )
            ->where(['use.packageid'=>"$setid"])
            ->select();
    }
    /**
     * [getGiveData 获取套餐明细]
     * @Author ZQY
     * @DateTime 2018-09-18T10:35:41+0800
     * @return   [array]        [返回套餐明细数组]
     */
    public function getGiveData($setid){
        return Db::table($this->coursepackagegift_table)
            ->where(['packageid'=>"$setid"])
            ->field('
                if(giftthreshold=0,"无限制",giftthreshold) threshold,
                elt(gifttrialtype,"全部课程","指定分类","指定课程") trialtype,
                if(giftefftype=1,concat(FROM_UNIXTIME(gifteffstarttime,"%Y年%m月%d"),"至",FROM_UNIXTIME(gifteffendtime,"%Y年%m月%d")),giftefftime) efftype,
                if(sendvideo=0,sendlive,sendvideo) bughour
            ')
            ->select();
    }
    /**
     * [getSetGive 获取套餐使用明细]
     * @Author ZQY
     * @DateTime 2018-09-03T10:37:41+0800
     * @return   [array]
     */
    public function getSetGive($setid){
        return Db::table($this->setuse_table)
            ->where(['packageid'=>"$setid"])
            ->field('
                count(type=2 or null) bug,
                count(ifuse=1 and type=2 or null) suse,
                count(ifuse=0 and type=2 or null) nouse'//type [套餐类型 1套餐 2套餐赠送课时]
            )
            ->select();
//        return Db::table($this->setuse_table)
//            ->alias('use')
//            ->join('nm_coursepackagegift set','use.packageid = set.packageid and use.type=2')
//            ->field('
//                if(set.giftthreshold=0,"无限制",set.giftthreshold) threshold,
//                elt(set.gifttrialtype,"全部课程","指定分类","指定课程") trialtype,
//                if(set.giftefftype=1,concat(FROM_UNIXTIME(set.gifteffstarttime,"%Y年%m月%d"),"至",FROM_UNIXTIME(set.gifteffendtime,"%Y年%m月%d")),set.giftefftime) efftype,
//                if(use.ifuse=2,"已过期","未过期") overdue,
//                count(use.type=2 or null) bug,
//                count(use.ifuse=1 or null) suse,
//                count(use.ifuse=0 or null) nouse'
//            )
//            ->select();
    }
    /**
     * [getSetDatalist 获取学生使用明细]
     * @Author ZQY
     * @DateTime 2018-09-04T20:23:41+0800
     * @return   [array]
     */
    public function getSetDatalist($where,$limitstr)
    {
        return Db::table($this->setuse_table)
            ->alias('use')
            ->join('nm_coursepackage set','use.packageid = set.id')
            ->join('nm_studentinfo stu','use.studentid = stu.id')
            ->field('stu.id,stu.nickname username,stu.mobile,use.bugtime,use.usetime,use.ifuse')
            ->where($where)
            ->limit($limitstr)
            ->select();
    }
    /**
     * [getSetCurriculumCount 获取使用列表总行数]
     * @Author ZQY
     * @DateTime 2018-09-04T20:44:41+0800
     * @return   [array]        [返回使用页数]
     */
    public function getDataCount($where){
        return Db::table($this->setuse_table)
            ->alias('use')
            ->join('nm_coursepackage set','use.packageid = set.id')
            ->join('nm_studentinfo stu','use.studentid = stu.id')
            ->field('stu.id,stu.nickname,stu.mobile,use.bugtime,use.usetime,use.packageid,use.ifuse')
            ->where($where)
            ->count();
    }
    /**
     * [setDe1 更改删除状态]
     * @Author ZQY
     * @DateTime 2018-09-05 14::26:23
     * @return   [boolean]
     */
    public function setDe1($setid){
            return Db::table($this->coursepackage_table)->where(['id'=>$setid])->update(['delflag'=>0]);
    }
    /**
     * [testSet 查询'套餐使用表'中的数据]
     * @Author ZQY
     * @DateTime 2018-09-05 14::57:23
     * @return   [boolean]
     */
    public function testSet($where){
        return Db::table($this->setuse_table)->where($where)->count();
    }
    /**
     * [setInsertDb 套餐添加]
     * @Author ZQY
     * @DateTime 2018-09-07 14::57:23
     * @return   [boolean]
     */
    public function setInsertDb($give,$status)
    {
        if($status==0){
            return Db::table($this->coursepackage_table)->insert($give['zero_status']);
        }else{
            // 启动事务
            Db::startTrans();
            $zero_res = Db::table($this->coursepackage_table)->insert($give['zero_status']);
            $setId = Db::name($this->coursepackage_table)->getLastInsID();
            $give['one']['packageid'] = $setId;
            $one_res = Db::table($this->coursepackagegift_table)->insert($give['one']);
//                $res = Db::table($this->coursepackagegift_table)->getLastSql();
            if($zero_res&&$one_res){
                // 提交事务
                Db::commit();
                return true;
            }else {
                // 回滚事务
                Db::rollback();
                return false;
            }

        }


    }
    /**
     * [setUpdateDb  获取购买课时数据]
     * @Author ZQY
     * @DateTime 2018-09-08 17:00:01
     * @return   [boolean]
     */
    public function setUpdateDb($setid)
    {
        return Db::table($this->coursepackage_table)->where(['id'=>$setid])->field('bughour,setmeal,setimgpath,limitbuy,setprice,threshold,efftype,effendtime,effstarttime,efftime,trialtype,categoryids,curriculumids,content,givestatus')->select();
    }
    /**
     * [setUpdateDb 获取赠送课时数据]
     * @Author ZQY
     * @DateTime 2018-09-08 17:00:01
     * @return   [boolean]
     */
    public function giveData($packageid)
    {
        return Db::table($this->coursepackagegift_table)->where(['packageid'=>$packageid])->field('sendvideo,sendlive,giftthreshold,giftefftype,gifteffstarttime,gifteffendtime,giftefftime,gifttrialtype,giftcategoryids,giftcurriculumids')->select();
    }
    /**
     * [UpdateCategory 标记分类选中状态]
     * @Author ZQY
     * @DateTime 2018-09-08 17:00:01
     * @return   [boolean]
     */
    public function UpdateCategory()
    {
        $lists = Db::table($this->category_table)
            ->where('delflag', 'eq', 1)
            ->where('status', 'eq', 1)
            ->field('id,categoryname title,fatherid')->select();
        return $lists;
    }
    /**
     * [UpdateBuyModify 修改购买课时套餐]
     * @Author ZQY
     * @DateTime 2018-09-08 17:00:01
     * @return   [boolean]
     */
    public function UpdateBuyModify($param)
    {
        if($param['givestatus']==0){
            return Db::table($this->coursepackage_table)
                ->where(['id'=>$param['id']])
                ->update([
                    'bughour'  => $param['bughour'],
                    'setmeal'  => $param['setmeal'],
                    'setprice'  => $param['setprice'],
                    'limitbuy'  => $param['limitbuy'],
                    'threshold'  => $param['threshold'],
                    'efftype'  => $param['efftype']['status'],
                    'effstarttime'  => $param['efftype']['effstarttime'],
                    'effendtime'  => $param['efftype']['effendtime'],
                    'efftime'  => $param['efftype']['efftime'],
                    'trialtype'  => $param['trialtype']['status'],
                    'categoryids'  => $param['trialtype']['categoryids'],
                    'curriculumids'  =>$param['trialtype']['curriculumids'],
                    'content'  => $param['content'],
                    'setimgpath'  => $param['setimgpath'],
                    'givestatus'  => $param['givestatus'],
                ]);
        }elseif($param['givestatus']==1){
            // 启动事务
            $res_buy = Db::table($this->coursepackage_table)
                ->where(['id'=>$param['id']])
                ->update([
                    'bughour'  => $param['bughour'],
                    'setmeal'  => $param['setmeal'],
                    'setprice'  => $param['setprice'],
                    'limitbuy'  => $param['limitbuy'],
                    'threshold'  => $param['threshold'],
                    'efftype'  => $param['efftype']['status'],
                    'effstarttime'  => $param['efftype']['effstarttime'],
                    'effendtime'  => $param['efftype']['effendtime'],
                    'efftime'  => $param['efftype']['efftime'],
                    'trialtype'  => $param['trialtype']['status'],
                    'categoryids'  => $param['trialtype']['categoryids'],
                    'curriculumids'  =>$param['trialtype']['curriculumids'],
                    'content'  => $param['content'],
                    'setimgpath'  => $param['setimgpath'],
                    'givestatus'  => $param['givestatus'],
                ]);
            $res = Db::table($this->coursepackagegift_table)->where('packageid', $param['id'])->select();
            if(empty($res)){
                $res_give = Db::table($this->coursepackagegift_table)->insert([
                    'packageid'  => $param['id'],
                    'sendvideo'  => $param['sendvideo'],
                    'sendlive'  => $param['sendlive'],
                    'giftthreshold'  => $param['giftthreshold'],
                    'giftefftype'  => $param['giftefftype']['status'],
                    'gifteffstarttime'  => $param['giftefftype']['gifteffstarttime'],
                    'gifteffendtime'  => $param['giftefftype']['gifteffendtime'],
                    'giftefftime'  => $param['giftefftype']['giftefftime'],
                    'gifttrialtype'  => $param['gifttrialtype']['status'],
                    'giftcategoryids'  => $param['gifttrialtype']['giftcategoryids'],
                    'giftcurriculumids'  => $param['gifttrialtype']['giftcurriculumids'],
                ]);
            }else{
                $res_give = Db::table($this->coursepackagegift_table)
                    ->where('packageid', $param['id'])
                    ->update([
                        'sendvideo'  => $param['sendvideo'],
                        'sendlive'  => $param['sendlive'],
                        'giftthreshold'  => $param['giftthreshold'],
                        'giftefftype'  => $param['giftefftype']['status'],
                        'gifteffstarttime'  => $param['giftefftype']['gifteffstarttime'],
                        'gifteffendtime'  => $param['giftefftype']['gifteffendtime'],
                        'giftefftime'  => $param['giftefftype']['giftefftime'],
                        'gifttrialtype'  => $param['gifttrialtype']['status'],
                        'giftcategoryids'  => $param['gifttrialtype']['giftcategoryids'],
                        'giftcurriculumids'  => $param['gifttrialtype']['giftcurriculumids'],
                    ]);
            }
            if($res_buy||$res_give){
                return true;
            }else {
                return false;
            }
        }
    }
    /**
     * [Testing ]
     * @Author ZQY
     * @DateTime 2018-09-25 14:29:01
     * @return   [boolean]
     */
    public function Testing($where)
    {
        return Db::table($this->setorder_table)->where($where)->find();

    }
    /**
     * [Trans 分类id转换]
     * @Author ZQY
     * @DateTime 2018-09-11 11:21:01
     * @return   [boolean]
     */
    public function Trans($categoryids)
    {
        $where['id'] = ['IN', $categoryids];
        return Db::table($this->category_table)->where($where)->field('categoryname')->select();
    }
    /**
     * [HomepageCourseList 获取课程列表]
     * @Author ZQY
     * @DateTime 2018-09-01T17:02:41+0800
     * @return   [array]  [返回课程列表数组]
     */
    public function HomepageCourseList($where,$limitstr){
        return Db::table($this->table)->where($where)->field('id,coursename')->limit($limitstr)->select();
    }
    /**
     * [HomepageCourseListCount 返回数据总数]
     * @Author ZQY
     * @DateTime 2018-09-01T17:02:41+0800
     * @return   [array]  [返回数据总数]
     */
    public function HomepageCourseListCount($where){
        return Db::table($this->table)->where($where)->count();
    }
    /**
     * [HomepageTeacherList 获取老师列表]
     * @Author ZQY
     * @DateTime 2018-09-11T17:21:41+0800
     * @return   [array]  [返回课程列表数组]
     */
    public function HomepageTeacherList($where,$limitstr){
        return Db::table($this->teacher_table)->where($where)->field('teacherid,nickname teachername')->limit($limitstr)->select();
    }
    /**
     * [HomepageTeacherListCount 返回数据总数]
     * @Author ZQY
     * @DateTime 2018-09-11T17:21:41+0800
     * @return   [array]
     */
    public function HomepageTeacherListCount($where){
        return Db::table($this->teacher_table)->where($where)->count();
    }
    /**
     * [HomepageNow 推荐课程数据]
     * @Author ZQY
     * @DateTime 2018-09-12T17:29:41+0800
     * @return   [array]
     */
    public function HomepageNow($where,$limitstr){
         return Db::table($this->table)->where($where)->field('id,coursename')->limit($limitstr)->select();
    }
    /**
     * [HomepageNowCount 课程数]
     * @Author ZQY
     * @DateTime 2018-09-11T17:21:41+0800
     * @return   [array]
     */
    public function HomepageNowCount($where){
        return Db::table($this->table)->where($where)->count();
    }
    /**
     * [HomepageTeacherNow 老师数据]
     * @Author ZQY
     * @DateTime 2018-09-11T17:21:41+0800
     * @return   [array]
     */
    public function HomepageTeacherNow($where,$limitstr){
        return Db::table($this->teacher_table)->where($where)->field('teacherid,nickname teachername')->limit($limitstr)->select();
    }
    /**
     * [HomepageTeacherNowCount 老师数据]
     * @Author ZQY
     * @DateTime 2018-09-11T17:21:41+0800
     * @return   [array]  [老师数]
     */
    public function HomepageTeacherNowCount($where){
        return Db::table($this->teacher_table)->where($where)->count();
    }


}
