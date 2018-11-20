<?php

namespace app\teacher\model;
use think\Db;
use think\Model;
use think\Validate;
use think\Session;
//操作学生信息表

class StudentInfo extends Model
{
  protected $pk = 'id';
  protected $table = "nm_studentinfo";
  protected $rule = [
		'mobile'   => 'require|max:25',
		'nickname' => 'require|max:30',
		'sex'      => 'number|between:0,2',
    'prphone'  => 'require',
		'country'  => 'number',
		'province' => 'number',
		'city'     => 'number',
	];
  protected $message = [
		'mobile.require'   => '手机号必须填写',
		'mobile.max'       => '名称最多不能超过25个字符',
		'nickname.require' => '昵称必须填写',
		'mobile.max'       => '昵称不能超过30个字符',
		'sex.number'       => '性别必须是数字',
		'sex.between'      => '性别只能在0-2之间',
		'country.number'   => '必须是数字',
		'province.number'  => '必须是数字',
		'city.number'      => '必须是数字',
	];


    /**
     * [checkLogin 获取教师用户信息]
     * @Author wangwy
     * @param    [vachar]        $mobile  [手机号]
     * @return   array
     */
    public function checkLogin($mobile){
        $where['mobile'] =  $mobile;
        $lists = Db::table($this->table)->where($where)->field('teacherid,username,password,mobile,addtime,status,organid,delflag')->find();
        return $lists;
    }




   /**
    * 从数据库获取
    * @Author wangwwy
    * @param $where    array       必填
    * @param $limitstr string      必填
    * @return   array                   [description]
    */
     public function getStudentinfo($where,$limitstr){
       return Db::table($this->table)->where($where)->field('nickname')->select();
     }

    /**
	 * 从数据库获取
	 * @Author wangwy
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @return   array                   [description]
	 */
     public function getUserList($where,$limit)
     {
         return Db::table($this->table)->alias('c')
            ->join('nm_ordermanage t','c.id=t.studentid','RIGHT')
            ->where($where)
            ->page($limit)
            ->order('c.id', 'asc')
            ->column('c.id,c.nickname,c.sex,c.country,c.province,c.city,c.birth','c.id');
         //return Db::table($this->table)->getLastSql();
     }
    /**
	 * 从数据库获取
	 * @Author wangwy
	 * @param $where    array       必填
	 * @param $limitstr string      必填
	 * @return   array                   [description]
	 */
     public function getUserListcount($where)
     {
        return Db::table($this->table)->alias('c')
           ->join('nm_ordermanage t','c.id=t.studentid')
           ->where($where)
           ->count();
     }

    /** 获取订单中不会重复的
     * @param $where
     * @return int
     */
     public function getOnlyListcount($where)
     {
        $re = Db::table($this->table)->alias('c')
           ->join('nm_ordermanage t','c.id=t.studentid','right')
           ->where($where)
           ->column('c.username','c.id');
        //return Db::table($this->table)->getLastSql();
        return count($re);
     }

    /**
     * [getUserDetail 获取学生的详细信息]
     * @Author
     * @DateTime 2018-04-20T13:48:56+0800
     * @param    [int]      $studentid [学生标识]
     * @param    [int]      $organid   [教師id]
     * @return   [array]               [description]
     */
     public function getUserDetail($studentid)
     {
    	$field = 'imageurl,nickname,mobile,sex,birth,country,province,city,profile' ;
        return Db::table($this->table)
            ->where('id','eq',$studentid)
            ->field($field)
            ->find();
     }




      /**
     * [addStudent 添加学生信息]
     * @Author wangwy
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
     // public function addStudent($data){
     //     //数据验证
     // 	$validate = new Validate($this->rule, $this->message);
 	  	// $result   = $validate->check($data);
     //
 	  	// if(!$validate->check($data)){
 	  	// 	return return_format('',-1,$validate->getError());
 	  	// }else{
 	  	// 	$arr = explode('-',$data['birth']) ;
 	  	// 	$data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
     //           Db::startTrans();
     //           try{
     //   			$return = $this->allowField(true)->save($data);
     //               $id = $this->id;
     //
     //               Db::table('nm_studentfunds')->insert(['studentid'=>$id,'organid'=>$data['organid'],'teacherid'=>$data['teacherid']]);
     //
     //               Db::commit();
     //           }catch(\Exception $e){
     //               // 回滚事务
     //               Db::rollback();
     //           }
     //
 	  	// 	return return_format($return,0,'添加成功');
 	  	// }
     // }
    /**
     * [addStudent 添加学生信息]
     * @Author wangwy
     * @param    [array]       $data [要入库的用户信息]
     * @return   [array]
     */
     public function addStudent($data,$teacherid){
         //数据验证
     	$validate = new Validate($this->rule, $this->message);
 	  	$result   = $validate->check($data);

 	  	if(!$validate->check($data)){
 	  		return return_format('',-1,$validate->getError());
 	  	}else{
 	  		//$arr = explode('-',$data['birth']) ;
 	  	//	$data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
               Db::startTrans();
               try{
                   $newdata = $data;
                   $newdata['teacherid'] = $teacherid ;
       		   	     $return = $this->allowField(true)->save($newdata);
                   $id = $this->id;

                   Db::table('nm_studentfunds')->insert(['studentid'=>$id,'organid'=>$newdata['organid']]);
                   Db::commit();
                    return return_format($return,0,lang('success'));
               }catch(\Exception $e){
                   // 回滚事务
                   Db::rollback();
                   return return_format('',20301,lang('20301'));
               }
           //isset($return)?$return:NULL;
 	  	}
     }


    /**
     * [updateStudent 更新学生数据]
     * @Author wangwy
     * @DateTime 2018-04-20T21:02:32+0800
     * @param    [array]       $data    [需要更新的字段]
     * @param    [int]         $teacherid [教师id]
     * @return   [array]                [返回数据]
     */
     public function updateStudent($data,$teacherid){
    	$validate = new Validate($this->rule, $this->message);
		$result = $validate->check($data);
		if(!$validate->check($data)){
			return return_format('',-1,$validate->getError());
		}else{
			$arr = explode('-',$data['birth']) ;
			$data['birth'] = mktime(0,0,0,$arr[1],$arr[2],$arr[0]) ;
			//获取where条件
			$organid= Db::table($this->table)->alias('c')->join('nm_ordermanage t','c.id=t.studentid')->where('t.teacherid','eq',$teacherid)->field('c.organid')->find();

			$where = ['id'=>$data['id'],'organid'=>$organid['organid']] ;
			if($data['id']>0){
				$return = $this->allowField(true)->save($data,$where);
				return return_format($return,0,'更新成功');

			}else{
				return return_format('',-1,'参数异常');
			}
		}
     }



    /**
     * [delStudent 删除学生信息]
     * @Author wangwy
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $teacherid   [机构标识]
     * @param    [int]     $userid    [学生id]
     * @return   [type]               [description]
     */
     public function delStudent($userid,$teacherid){
    	$data  = ['delflag'=>1] ;
    	//获取where条件
		$organid= Db::table($this->table)->alias('c')->join('nm_ordermanage t','c.id=t.studentid')->where('t.teacherid','eq',$teacherid)->field('c.organid')->find();
    	$where = ['id'=>$userid,'organid'=>$organid['organid']] ;
    	return $this->save($data,$where);
     }


    // /**
    //  * [getStudentnameById 通过学生id来获取学生的名字]
    //  * @Author wangwy
    //  * @DateTime 2018-04-21T11:55:09+0800
    //  * @param    [int]        $userid  [学生id]
    //  * @param    [int]        $teacherid [教师id]
    //  * @return   [array]               [查询结果]
    //  */
    // public function getStudentnameById($userid,$teacherid){
    // 	$organid= Db::table($this->table)->alias('c')->join('nm_ordermanage t','c.id=t.studentid')->where('t.teacherid','eq',$teacherid)->field('c.organid')->find();
    // 	return $this->get(['id'=>$userid,'organid'=>$organid]);
    // }

    /**
     * [getStudentnameByIds 订单表分页后获取x学生名称]
     * @Author  wangwy
     * @param    [array]          $arr [学生ids]
     * @return   [type]               [description]
     */
     public function getStudentnameById($arr){
        return Db::table($this->table)
            ->where('id','IN',$arr)
            ->column('id,nickname');
     }
    /**
    * 查学生名称
    * @Author  wangwy
    */
     public function getStudentById($id){
      return Db::table($this->table)
          ->where('id','eq',$id)
          ->field('id,nickname')
          ->find();
     }


    // /**
    //  * [getStudentnameByIds 订单表分页后获取教师名称]
    //  * @Author  wangwy
    //  * @param    [array]          $arr [学生ids]
    //  * @return   [type]               [description]
    //  */
    // public function getStudentnameByIds($arr){
    //     return Db::table($this->table)->where('id','IN',$arr)
    //     ->column('id,username');
    // }


    /**
     * [delStudent 获取学生的昵称列表]
     * @Author wangwy
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $organid   [机构标识]
     * @param    [int]     $teacherid    [教师id]
     * @return   [type]               [description]
     */

     public function getNicklist($teacherid,$organid){
        return Db::table($this->table)
               ->where('teacherid','eq',$teacherid)
               ->where('organid','eq',$organid)
               ->field('nickname,mobile,sex')->select();
               //print_r(Db::table($this->table)->getlastsql());
     }
    /*
     * [delStudent 更改学生密码]
     * @Author wangwy
     * @DateTime 2018-04-20T09:59:05+0800
     * @param    [int]     $organid   [机构标识]
     * @param    [int]     $teacherid    [教师id]
     * @return   [type]               [description]
     */

     public function insertPassword($mobile,$password){
        return Db::table($this->table)
               ->where('mobile','eq',$mobile)
               //->where('organid','eq',$organid)
               ->update(['mix'=>$password['mix'],'password'=>$password['password']]);
     }

     /*
      * [获取学生手机号]
      *  @Author wangwy
      */

     public function getStudentMobile($idarr){
         return Db::table($this->table)
             ->where('id','IN',$idarr)
             ->column('prphone,mobile','id');
     }

    /**
     * @param $where
     * @param $field
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
     public function getAllfind($where,$field){
         return Db::table($this->table)
             ->where($where)
             ->field($field)
             ->find();
     }
     public function getAllcolumn($where,$field,$pagenum,$pagesize){
          $info['data'] = Db::table($this->table)
             ->where($where)
             ->page($pagenum,$pagesize)
             ->column($field);
         $info['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$pagenum,'total'=>$this->getAllcolumncount($where));
         return $info;
     }
     public function getAllcolumncount($where){
         return Db::table($this->table)
             ->where($where)
             ->count();
     }


}
