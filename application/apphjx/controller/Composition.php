<?php
namespace app\apphjx\controller;
use app\admin\model\Category;
use think\Log;
use login\Authorize;
use think\Request;
use think\Controller;
use app\apphjx\business\CompositionManage;
class Composition extends Authorize
{
    public $studentid;
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token');
        $this->studentid = $this->userInfo['info']['uid'];
    }
    /**
     * 首页列表
     * @Author ZQY
     * @DateTime 2018-10-16 18:04:23
     * URL:/apphjx/Composition/homePageList
     */
    public function homePageList()
    {
        $compositionstatus = $this->request->param('compositionstatus');
        $pagenum = $this->request->param('pagenum');
        $studentid = $this->studentid;
        $limit = config('param.pagesize')['app_composition_list'];
        $homepage = new CompositionManage;
        $homepage_list = $homepage->getHomepageList($compositionstatus,$studentid,$pagenum,$limit);
        $this->ajaxReturn($homepage_list);
        return $homepage_list;

    }
    /**
     * 首页列表-作文详情
     * @Author ZQY
     * @DateTime 2018-10-17 15:49:23
     * URL:/apphjx/Composition/compositionDetail
     */
    public function compositionDetail()
    {
        $compositionid = $this->request->param('compositionid');
        $composition = new CompositionManage();
        $composition_data = $composition->seeCompositionData($compositionid);
        $this->ajaxReturn($composition_data);
        return $composition_data;

    }
    /**
     * 首页列表-作文详情-查看评论
     * @Author ZQY
     * @DateTime 2018-10-17 20:19:19
     * URL:/apphjx/Composition/seeComment
     */
    public function seeComment()
    {
        $compositionid = $this->request->param('compositionid');
        $composition = new CompositionManage();
        $comment_data = $composition->seeComment($compositionid);
        $this->ajaxReturn($comment_data);
        return $comment_data;

    }
    /**
     * 首页列表-作文-添加或修改评价
     * @Author ZQY
     * @DateTime 2018-10-18 10:15:19
     * URL:/apphjx/Composition/modifyAddComposition
     */
    public function modifyAddComposition()
    {
        $compositionid = $this->request->param('compositionid');
        $reviewscore = $this->request->param('reviewscore');
        $commentcontent = $this->request->param('commentcontent');
        $commentids     = $this->request->param('commentids');
        $studentid = $this->studentid;
        $composition = new CompositionManage();
        $comment_data = $composition->modifyAddComposition($compositionid,$reviewscore,$commentcontent,$studentid,$commentids);
        $this->ajaxReturn($comment_data);
        return $comment_data;
    }
    /**
     * 首页列表-作文-添加或修改作文
     * @Author ZQY
     * @DateTime 2018-10-18 15:00:00
     * URL:/apphjx/Composition/compositionModifyAdd
     */
    public function compositionModifyAdd()
    {
        //判断是否上传轨迹文件，
        if(isset($_FILES['myfile']['tmp_name']))
        {
            $file_dir = './upload/apphjx/';
            if(!file_exists($file_dir))  mkdir($file_dir,0777);
            $file_name = time().rand(0,999).'.txt';
            $res = move_uploaded_file($_FILES['myfile']['tmp_name'],$file_dir.$file_name);
            if(!$res){
                return return_format('','60026','文件上传失败');
            }
            $filetxt = $file_dir.$file_name;
            //读取文件中的数据
            $trajectory = file_get_contents($filetxt);
            //读取完后，删除本地文件
            unlink($filetxt);
        }else{
            $trajectory = "";
        }
        $data = $this->request->param();
        $data['data'] = json_decode($data['data'], true);
        $compositionid = isset($data['data']['compositionid'])?$data['data']['compositionid']:'null';
        $type = isset($data['data']['type'])?$data['data']['type']:'null';
        $title = isset($data['data']['title'])?$data['data']['title']:'null';
        $imgurl = isset($data['data']['imgurl'])?$data['data']['imgurl']:'null';
        $label = isset($data['data']['label'])?$data['data']['label']:'null';
        $content = isset($data['data']['content'])?$data['data']['content']:'null';
        $videourl = isset($data['data']['videourl'])?$data['data']['videourl']:'null';
        $trajectorys = serialize($trajectory);
        $studentid = $this->studentid;
        $composition = new CompositionManage();
        $comment_data = $composition->compositionAddOrUpdate($compositionid,$type,$title,$imgurl,$label,$studentid,$content,$trajectorys,$videourl);
        $this->ajaxReturn($comment_data);
        return $comment_data;
    }
}