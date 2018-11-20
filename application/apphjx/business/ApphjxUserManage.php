<?php
namespace app\apphjx\business;
use app\apphjx\model\Hjxappstudentinfo;
use think\Validate;
use think\Cache;
use Messages;
use Think\Log;
session_start();
class ApphjxUserManage
{
    protected $foo;
    protected $str;

    public function  __construct() {
        //定义空的数组对象
        $this->foo = (object)array();
        //定义空字符串
        $this->str = '';
    }
    /**
     * 修改app学生资料
     * @Author cy
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function updateAppuserInfo($data){
        if(!is_intnum($data['id'])){
            return return_format('',39100,lang('param_error'));
        }
        $where = ['id'=>$data['id']];
        unset($data['id']);
        unset($data['studentid']);
        //设置允许修改的字段
        $allowfiled = array('imageurl','nickname','sex','school','categoryid','class','equipment');
        $keyarr = array_keys($data);
        foreach($keyarr as $k=>$v){
            if(!in_array($v,$allowfiled)){
                return return_format('',39101,lang('param_error'));
            }
        }
        $usermodel = new Hjxappstudentinfo;
        $update_res = $usermodel->updateAppuserInfo($where,$data);
        if($update_res>=0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',39102,lang('error'));
        }
    }

    /**
     * 登陆发送手机验证码
     * @Author lc
     * @param $mobile   手机号
     * @param $prphone   手机号前缀
     * @return array
     *
     */
    public function sendMsg($mobile,$prphone)
    {
        if( empty($mobile)){
            return return_format($this->str,37008,lang('param_error'));
        }
		
        Cache::rm('mobile'.$mobile);
        
        if(strlen($mobile)<6 || strlen($mobile)>12 || !is_numeric(rtrim($mobile))){
            return return_format($this->str,37010,lang('37010'));
        }else{
           /*  $studentmodel = new Studentinfo;
            $data = $studentmodel ->checkLogin($mobile); */
           
            $mobile_code = rand(100000,999999);
			
            //此处调用短信接口,发送验证码
            $messageobj = new Messages;
            $send_result = $messageobj->sendMeg($mobile,$type=15,$params = [$mobile_code],$prphone);
            if($send_result['result'] == 0){
                return return_format('',0,lang('success'));
            }else{
                Log::write('发送验证码错误号:'.$send_result['result'].'发送验证码错误信息:'.$send_result['errmsg']);
                return return_format('',37015,lang('37015'));
            }
        }

    }


    /**
     * 获取学生个人资料信息
     * @Author cy
     * @param userid int 学生用户id
     * @return array
     *
     */
    public function getStudentInfo($studentid){
        //判断参数是否合法-new
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $result = $studentmodel->getStudentInfo($studentid);
        $classcategory = $this->getClassInfo();
        $result['grade'] = $classcategory;
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 获取学生班级资料信息
     * @Author cy
     * @return array
     *
     */
    public function getClassInfo()
    {
        $studentmodel = new Hjxappstudentinfo;
        $result = $studentmodel->getClassCategory();
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 获取文章标签信息
     * @Author cy
     * @return array
     *
     */
    public function getLabelInfo($studentid)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $result = $studentmodel->getLabelInfo($studentid);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 新增作文标签
     * @Author cy
     * @param $studentid 学生id
     * @param $label     标签名
     * @return array
     *
     */
    public function createLabelInfo($studentid,$label)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $data['studentid']  = $studentid;
        $data['lablename']  = $label;

        $labelist      = $studentmodel->getLabelist($studentid);
        if($labelist >=10){
            return return_format([],80012,lang('标签个数不能超过10个'));
        }
        $result = $studentmodel->createLabelInfo($data);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 删除作文标签
     * @Author cy
     * @param $studentid 学生id
     * @param $labelid   标签id
     * @return array
     *
     */
    public function deleteLabelInfo($studentid,$labelid)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $where['studentid']  = $studentid;
        $where['id']  = $labelid;
        $result = $studentmodel->deleteLabelInfo($where);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 修改作文标签
     * @Author cy
     * @param $studentid 学生id
     * @param $labelid   标签id
     * @param $label     标签名
     * @return array
     *
     */
    public function updateLabelInfo($studentid,$labelid,$label)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $where['studentid']  = $studentid;
        $where['id']  = $labelid;
        $data['lablename'] = $label;
        $result = $studentmodel->updateLabelInfo($where,$data);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 搜索页-标签搜索
     * @Author cy
     * @param $studentid 学生id
     * @param $labelid   标签id
     * @return array
     *
     */
    public function searchLabelInfo($studentid,$labelid,$pagenum,$limit)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $articlemodel = new Hjxappstudentinfo();
        $handleLable  = new CompositionManage();
        $where['studentid'] = $studentid;
        $articles           = $articlemodel->searchLabelInfo($where,$limitstr,$labelid);
        $total              = $articlemodel->searchLabelInfoCount($where,$labelid);
        $data             = [];
        //判断是否有相应标签
        foreach ($articles as $k => $v)
        {
            $labels = explode(',',$v['label']);
            if(in_array($labelid,$labels)){
                $data[] = $articles[$k];
            }
        }

        $data = $handleLable->handleLable($data);
        //标签转换成汉字
        $result = [
            'articles'=>$data,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ];
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
    /**
     * 搜索页-文章标题搜索
     * @Author cy
     * @param $studentid 学生id
     * @param $keywords  关键词
     * @return array
     *
     */
    public function searchArticleInfo($studentid,$keywords,$pagenum,$limit)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        //分页处理
        if($pagenum>0){
            $start = ($pagenum - 1 ) * $limit;
            $limitstr = $start.','.$limit ;
        }else{
            $start = 0 ;
            $limitstr = $start.','.$limit ;
        }
        $articlemodel = new Hjxappstudentinfo();
        $handleLable  = new CompositionManage();
        $where['studentid'] = $studentid;
        $where['title']     = array('like',"%{$keywords}%");
        $articles           = $articlemodel->searchArticleInfo($where,$limitstr);
        $total              = $articlemodel->searchArticleInfoCount($where);
        $data               = $handleLable->handleLable($articles);
        //标签转换成汉字
        $result = [
            'articles'=>$data,
            // 内容结果集
            'pageinfo'=>[
                'pagesize'=>$limit ,// 每页多少条记录
                'pagenum' =>$pagenum ,//当前页码
                'total'   => $total //

            ]
        ];
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }

    /**
     * 设置页面-获取设备名称
     * @Author cy
     * @param $studentid 学生id
     * @param $equipment 设备名称
     * @return array
     *
     */
    public function getEquipmentInfo($studentid)
    {
        //判断参数是否合法
        if(!is_intnum($studentid)){
            return return_format($this->str,39132,lang('param_error'));
        }
        $studentmodel = new Hjxappstudentinfo;
        $result = $studentmodel->getEquipmentInfo($studentid);
        if(empty($result)){
            return return_format([],0,lang('success'));
        }else{
            return return_format($result,0,lang('success'));
        }
    }
}
