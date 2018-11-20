<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Organslideimg extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_organslideimg';

	
	 // 课程添加验证规则
    protected $rule = [
        'remark'  => 'require',
        'image'  => 'require',
    ];

    protected $message  = [];
    //自定义初始化
    protected function initialize(){
        parent::initialize();
        $this->message = [
            'remark.require' => lang('40061'),
            'image.require' => lang('40062'),
        ];
    }

    /**
     * [getOrganSlide 获取机构老师的课]
     * @Author wyx
     * @DateTime 2018-04-25T10:20:43+0800
     * @return   [array]                              [description]
     */
    public function getOrganSlide(){
        
        $field = 'id,remark,imagepath,sortid' ;
        return Db::table($this->table)
        ->field($field)
        ->order('sortid')
        ->select() ;
    }
    /**
     * [addSlideImage 添加机构轮播图]
     * @Author wyx
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [int]                   $sortnum [排序id]
     * @param    [array]                 $data    [用户提交的数据]
     * @return   [array]                          [description]
     */
    public function addSlideImage($sortnum,$data){
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',40061,$validate->getError()) ;
        }
        //构造入库数据
        $insertdata = [
                    'remark'    => $data['remark'],
                    'imagepath' => $data['image'],
                    'addtime'   => time(),
                    'sortid'    => $sortnum
                ] ;
        
        $flag = Db::table($this->table)->insert($insertdata);

        if($flag){
            return return_format($flag,0) ;
        }else{
            return return_format('',40060) ;
        }
    }
    /**
     * [editSlideImage 更新机构轮播图]
     * @Author wyx
     * @DateTime 2018-04-25T14:27:25+0800
     * @param    [array]                 $data    [用户提交的数据]\
     * @return   [array]                          [description]
     */
    public function editSlideImage($data){
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',40064,$validate->getError()) ;
        }
        //构造入库数据
        $insertdata = [
                    'remark'    => $data['remark'],
                    'imagepath' => $data['image'],
                ] ;
        $where = [
                'id'      => $data['id'],
            ] ;
        
        $flag = Db::table($this->table)
        ->where($where)
        ->update($insertdata);

        if($flag){
            return return_format($flag,0) ;
        }else{
            return return_format('',40065) ;
        }
    }
	/**
	 * [delSlideImage 获取机构上传的图片数量
	 * @Author jcr
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   查询参数
	 * 
	 */
	public function delSlideImage($id){
        $where = [
                'id'      => $id,
            ] ;
		$flag = Db::table($this->table)
		        ->where($where)
                ->delete();
		if($flag){
            return return_format($flag,0) ;
        }else{
            return return_format('',40067) ;
        }
	}

	
    
}
