<?php
/**
 * @ 财务模块控制器 
 */
namespace app\admin\controller;
use think\Controller;
use app\admin\business\MenuTree;
use think\Request;
use login\Authorize;


class Authoritys extends Authorize
{
	//自定义初始化
	protected function _initialize() {
        parent::_initialize();
		header('Access-Control-Allow-Origin: *');
	}


	/**
	 * [getUserGroup 获取部门列表]
	 * @author	[name]	< JCR >
	 */
	public function getUserGroup(){
		$data = Request::instance()->POST();
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$menutree = new MenuTree();
		$dataReturn = $menutree->getUserGroup($data,20);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * [getUserGroup 获取全部部门列表]
	 * @author	[name]	< JCR >
	 */
	public function getAllUserGroup(){
		$menutree = new MenuTree();
		$dataReturn = $menutree->getAllUserGroup();
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * [addGroup 添加部门]
	 * @author	[name]	< JCR >
	 * @param 	[type]	名称
	 * @param 	[treepath]
	 */
	public function addGroup(){
		$data = Request::instance()->POST();
		$menutree = new MenuTree();
		$dataReturn = $menutree->addGroup($data);
		$this->ajaxReturn($dataReturn);
	}


	/**
	 * [addGroup 删除部门]
	 * @author	[name]	< JCR >
	 * @param 	[id]	分组id
	 * @param 	[treepath]
	 */
	public function deleteGroup(){
		$data = Request::instance()->POST();
		$menutree = new MenuTree();
		$dataReturn = $menutree->deleteGroup($data);
		$this->ajaxReturn($dataReturn);
	}

    /**
     * [getAccount 左侧菜单列表树]、
     * @author [name] < JCR >
     * @param  [type] $[type]    [1已结算 0待结算]
     * @param  [type] $[pagenum] [分页第几页]
     * @return [type] [description]
     */
    public function menuListTree(){
    	$data = Request::instance()->POST();
    	$menutree = new MenuTree();
    	$dataReturn = $menutree->menuListTree($data);
        $this->ajaxReturn($dataReturn);
    }


	/**
	 * [getMenuTree 获取对应角色所属部门的左侧菜单]
	 * @author	[name]	< JCR >
	 */
	public function getMenuTree(){
		$menutree = new MenuTree();
		$dataReturn = $menutree->getMenuTree($this->userInfo['info']['groupids']);
		$this->ajaxReturn($dataReturn);
	}


}
