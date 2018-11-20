<?php
namespace app\official\controller;
use think\Controller;
use think\Session;
use app\official\business\RecommendManage;
use app\admin\business\RecommendManage as AdminRecomm;
use app\official\model\Officialuseroperate;
class Recommend extends Controller
{	
    /**
     * [getCategoryRecomm 获取官方推荐分类]
     * @Author wyx
     * @DateTime 2018-04-21T15:58:48+0800
     * @return   [type]                   [description]
     * URL:/official/recommend/getCategoryRecomm
     * 设计 10个推荐 无需分页
     */
    public function getCategoryRecomm()
    {	
    	//机构 标识id
        // $organid = Session::get('organid');
    	$organid = 1 ;

    	$manageobj = new RecommendManage;
    	//获取教师列表信息,默认分页为5条
    	$teachlist = $manageobj->getCategoryRecomm($organid);
    	
    	// var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist;
    }
    /**
     * [getCategoryTree 获取官方的分类树]
     * @Author wyx
     * @DateTime 2018-04-21T15:58:48+0800
     * @return   [type]                   [description]
     * URL:/official/recommend/getCategoryTree
     * 设计 10个推荐 无需分页
     */
    public function getCategoryTree()
    {   
        //机构 标识id
        // $organid = Session::get('organid');
        $organid = 1 ;

        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $teachlist = $manageobj->getCategoryTree($organid);
        
        // var_dump($teachlist);
        $this->ajaxReturn($teachlist);
        return $teachlist;
    }
    /**
     * [updateCateRecomm 交换课程的推荐位置]
     * @Author wyx
     * @DateTime 2018-04-21T17:51:32+0800
     * @return   [type]                   [description]
     * URL:/official/recommend/updateCateRecomm
     */
    public function updateCateRecomm(){
        // $organid = Session::get('organid');
        $organid   = 1 ;
        $ids   = $this->request->param('ids');// 12^32

        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $changeflag = $manageobj->updateCateRecomm($organid,$ids);
        // var_dump($changeflag);
        $this->ajaxReturn($changeflag);
        return $changeflag;
    }
    /**
     * [delRecomm 删除推荐分类]
     * @Author wyx
     * @DateTime 2018-04-21T17:52:40+0800
     * @return   [type]                   [description]
     * URL:/official/recommend/delRecomm
     */
    public function delRecomm(){
        // $organid = Session::get('organid');
        $organid   = 1 ;
        $cateid = $this->request->param('cateid');

        $manageobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $delstatus = $manageobj->delRecomm($organid,$cateid);
        // var_dump($changestatus);
        $this->ajaxReturn($delstatus);
        return $changestatus ;
    }
    /**
     * [exchangeCatePos 交换官方推荐分类位置]
     * @Author wyx
     * @DateTime 2018-05-26
     * @return   [type]                   [description]
     * URL:/official/recommend/exchangeCatePos
     */
    public function exchangeCatePos(){
        $organid = Session::get('organid');
        $organid   = 1 ;
        $idx1   = $this->request->param('cateid1');
        $idx2   = $this->request->param('cateid2');
        // $idx1   = 7 ;
        // $idx2   = 8 ;

        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->exchangeCatePos($organid,$idx1,$idx2);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     * [getRecommOrgan 获取官方后台的机构推荐]
     * @Author wyx
     * @DateTime 2018-05-26 
     * @return   [type]                   [description]
     * URL:/official/recommend/getRecommOrgan
     * 总共十条记录 目前不分页
     */
    public function getRecommOrgan()
    {          
        //机构 标识id
        // $organid = Session::get('organid');

        $recommobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $recommlist = $recommobj->getRecommOrgan();
        
        // var_dump($teachlist);
        $this->ajaxReturn($recommlist);
        return $teachlist;
    }
    /**
     * [getFreeOrgan 获取官方后台免费机构列表]
     * @author wyx
     * @DateTime 2018-05-28 
     * @return   [type]                   [description]
     * URL:/official/recommend/getFreeOrgan
     * 
     */
    public function getFreeOrgan()
    {   
        //获取第几页信息
        $pagenum   = $this->request->param('pagenum');
        $name      = $this->request->param('name');
        $limit = config('param.pagesize')['officialrecomm_freeorgan'] ;

        $recommobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $recommlist = $recommobj->getFreeOrgan($name,$pagenum,$limit);
        
        // var_dump($teachlist);
        $this->ajaxReturn($recommlist);
        return $teachlist;
    }
    /**
     * [updateFreeOrgan 更新官方后台免费机构列表]
     * @author wyx
     * @DateTime 2018-05-28 
     * @return   [type]                   [description]
     * URL:/official/recommend/updateFreeOrgan
     * 
     */
    public function updateFreeOrgan()
    {   
        //需要 新增的推荐机构id
        $ids   = $this->request->param('ids');// 12^32

        $recommobj = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $recommlist = $recommobj->updateFreeOrgan($ids);
        
        // var_dump($teachlist);
        $this->ajaxReturn($recommlist);
        return $teachlist;
    }
    /**
     * [exchangeOrganPos 交换官方推荐免费机构的位置]
     * @Author wyx
     * @DateTime 2018-05-28
     * @return   [type]                   [description]
     * URL:/official/recommend/exchangeOrganPos
     */
    public function exchangeOrganPos(){
        $idx1   = $this->request->param('organ1');
        $idx2   = $this->request->param('organ2');

        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->exchangeOrganPos($idx1,$idx2);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     * [delCommOrgan 移出推荐列表]
     * @Author wyx
     * @DateTime 2018-05-28
     * @return   [type]                   [description]
     * URL:/official/recommend/delCommOrgan
     */
    public function delCommOrgan(){
        // 要移除推荐的 机构id
        $organid   = $this->request->param('organid');

        $manageobj    = new RecommendManage;
        //获取教师列表信息,默认分页为5条
        $lablelist = $manageobj->delCommOrgan($organid);
        // var_dump($lablelist);
        $this->ajaxReturn($lablelist);
        return $lablelist;
    }
    /**
     *  [getOrganSlide 获取官方轮播图]
     *  @return   [type]                   [description]
     *  URL:/official/recommend/getOrganSlide
     *
     */
    public function getOrganSlide(){
        
        $organid   = 1 ;

        $manageobj = new AdminRecomm;
        //获取机构的轮播图
        $slideimg = $manageobj->getOrganSlide($organid);
        $this->ajaxReturn($slideimg);
    }
    /**
     *  [addSlideImage 添加官方轮播图]
     *  @return   [type]                   [description]
     *  URL:/official/recommend/addSlideImage
     *
     */
    public function addSlideImage(){
        
        $organid   = 1 ;

        $manageobj = new AdminRecomm;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->addSlideImage($_POST,$organid);
        //添加操作日志
        $operateobj = new Officialuseroperate();
        $operateFlag = $operateobj->addOperateRecord('添加了官网轮播图');
        $this->ajaxReturn($slideimg);
    }
    /**
     *  [addSlideImage 编辑官方轮播图]
     *  @return   [type]                   [description]
     *  URL:/official/recommend/editSlideImage
     *
     */
    public function editSlideImage(){
        
        $organid   = 1 ;

        $manageobj = new AdminRecomm;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->editSlideImage($_POST,$organid);
        //添加操作日志
        $operateobj = new Officialuseroperate();
        $operateFlag = $operateobj->addOperateRecord('修改了官网轮播图');
        $this->ajaxReturn($slideimg);
    }
    /**
     *  [delSlideImage 编辑官方轮播图]
     *  @return   [type]                   [description]
     *  URL:/official/recommend/delSlideImage
     *
     */
    public function delSlideImage(){
        
        $organid   = 1 ;
        $id = $this->request->param('id');

        $manageobj = new AdminRecomm;
        //获取教师列表信息,默认分页为5条
        $slideimg = $manageobj->delSlideImage($id,$organid);
        //添加操作日志
        $operateobj = new Officialuseroperate();
        $operateFlag = $operateobj->addOperateRecord('删除了官网轮播图');
        $this->ajaxReturn($slideimg);
    }
   
}
