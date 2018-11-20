<?php
/**
*官方后台管理员控制器
**/
namespace app\official\controller;
use app\official\controller\Base;
use app\official\business\UserManage;
use think\Session;
use think\Request;
class User extends Base
{
	/**
	 * //添加管理员
	 * @Author zzq
	 * @param $username   string  [用户名]
	 * @param $realname   string  [真实姓名]
	 * @param $mobile   string  [手机号]
	 * @param $password   string  [密码]
	 * @param $repassword   string  [重复密码]
	 * @return array  [返回信息]
	 * 
	 */
	public function addOfficialUser(){
		$data = [];
		$username = Request::instance()->post('username');
		$realname = Request::instance()->post('realname');
		$mobile = Request::instance()->post('mobile');
		$password = Request::instance()->post('password');
		$repassword = Request::instance()->post('repassword');
		$info = Request::instance()->post('info');
		$data['username'] = $username ? $username : '';
		$data['realname'] = $realname ? $realname : '';
		$data['mobile'] = $mobile ? $mobile : '';
		$data['password'] = $password ? $password : '';
		$data['repassword'] = $repassword ? $repassword : '';
		$data['info'] = $info ? $info : '';
		$userManage = new UserManage();
		$res = $userManage->addOfficialUser($data);
		$this->ajaxReturn($res);
        return $res; 
	}

    /**
     * [getOfficialUserList //获取管理员列表]
     * @Author zzq
     * @DateTime 2018-05-10
     * @param username string 管理员名（搜索关键字） 
     * @param $orderbys str 排序方式
     * @param $pagenum int 页码数
     * @param $pernum int 一页几条
     * @return array 机构列表
     */
    public function getOfficialUserList(){
        $data = [];
        $username = Request::instance()->post('username');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'username'=>$username ? $username : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_user_list'],
        ];
		$userManage = new UserManage();
		$res = $userManage->getOfficialUserList($data);
		$this->ajaxReturn($res);
        return $res;    
    }

	
	/**
	 * //获取单个用户信息
	 * @Author zzq
	 * @param $id   int  [广告id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOfficialUserById(){
		$id = Request::instance()->post('id');
		$userManage = new UserManage();
		$res = $userManage->getOfficialUserById($id);
		$this->ajaxReturn($res);
        return $res; 		
	}

	/**
	 * //编辑用户
	 * @Author zzq
	 * @param $username   string  [用户名]
	 * @param $realname   string  [真实姓名]
	 * @param $mobile   string  [手机号]
	 * @param $password   string  [密码]
	 * @param $repassword   string  [重复密码]
	 * @param $id   int  [用户id]
	 * @return array  [返回信息]
	 * 
	 */
	public function editOfficialUser(){
		$data = [];
		$id = Request::instance()->post('id');
		$username = Request::instance()->post('username');
		$realname = Request::instance()->post('realname');
		$mobile = Request::instance()->post('mobile');
		$password = Request::instance()->post('password');
		$repassword = Request::instance()->post('repassword');
		$info = Request::instance()->post('info');

		$data['id'] = $id ? $id : '';
		$data['username'] = $username ? $username : '';
		$data['realname'] = $realname ? $realname : '';
		$data['mobile'] = $mobile ? $mobile : '';
		$data['password'] = $password ? $password : '';
		$data['repassword'] = $repassword ? $repassword : '';
		$data['info'] = $info ? $info : '';

		$userManage = new UserManage();
		$res = $userManage->editOfficialUser($data);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * //删除用户
	 * @Author zzq
	 * @param $id   int  [用户id]
	 * @return array  [返回信息]
	 * 
	 */
	public function delOfficialUser(){
		$id = Request::instance()->post('id');
		$userManage = new UserManage();
		$res = $userManage->delOfficialUser($id);
		$this->ajaxReturn($res);
        return $res; 
	}

	/**
	 * //禁用或者启用用户
	 * @Author zzq
	 * @param $id   int  [用户id]
	 * @param $status   int  [当前用户的状态值]
	 * @return array  [返回信息]
	 * 
	 */
	public function setOfficialUserOnOrOff(){
		$id = Request::instance()->post('id');
		$status = Request::instance()->post('status');
		$id = $id ? $id : '';
		$status = $status ? $status : '';
		$userManage = new UserManage();
		$res = $userManage->setOfficialUserOnOrOff($id,$status);
		$this->ajaxReturn($res);
        return $res; 
	}

	
	/**
	 * ///展示操作日志
	 * @Author zzq
	 * @param $username   string  [搜索的管理员名]
	 * @param $date   string  [日期格式20180523]
	 * @param $orderbys   string  [排序方式]
	 * @param $pagenum   int  [页码数]
	 * @param $pernum   int  [每页此案时数目]
	 * @return array  [返回信息]
	 * 
	 */
	public function getUserOperateRecordList(){
		$data = [];
		$username = Request::instance()->post('username');
		$date = Request::instance()->post('date');
        $orderbys = Request::instance()->post('orderbys');
        $pagenum = Request::instance()->post('pagenum');
        $pernum = Request::instance()->post('pernum');
        $data = [
            'username'=>$username ? $username : '',
            'date'=>$date ? $date : '',
            'orderbys'=>$orderbys ? $orderbys : 'id desc',
            'pagenum'=>$pagenum ? $pagenum : 1,
            'pernum'=>config('param.pagesize')['official_user_operate_list'],
        ];
		$userManage = new UserManage();
		$res = $userManage->getUserOperateRecordList($data);
		$this->ajaxReturn($res);
        return $res; 		
	}

	//批量删除操作日志
	/**
	 * ///展示操作日志
	 * @Author zzq
	 * @param $ids   string  [需要删除的id的集合]
	 * @return array  [返回信息]
	 * 
	 */
	public function delUserOperateRecord(){
		$ids = Request::instance()->post('ids');
		$ids = $ids ? $ids : '';
		$userManage = new UserManage();
		$res = $userManage->delUserOperateRecord($ids);
		$this->ajaxReturn($res);
        return $res; 		
	}

	
	/**
	 * //获取管理员登录信息
	 * @Author zzq
	 * @param $id   string  [管理员的id]
	 * @return array  [返回信息]
	 * 
	 */
	public function getOfficialUserLoginInfo(){
		$id = Request::instance()->post('id');
		$id = $id ? $id : '';
		$userManage = new UserManage();
		$res = $userManage->getOfficialUserLoginInfo($id);
		$this->ajaxReturn($res);
        return $res; 		
	}

}	