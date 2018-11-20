<?php
/**
 * 部门菜单权限 模块
 */
namespace app\admin\business;
use app\admin\model\Accessmenu;
use app\admin\model\Accessusergroup;
use app\admin\model\Adminmember;

class MenuTree{

	/**
	 * [getUserGuoup 部门分组列表]
	 */
	public function getUserGroup($data,$limit){
		$usergroup = new Accessusergroup();
		$where = ['delflag'=>0,'id'=>array('gt',1)];
		$list = $usergroup->getList($where,$data['pagenum'],$limit);
		if($list){
			$access = new Accessmenu();
			$menulist = $access->getList(['status'=>0]);
			$menulist = array_column($menulist,'name','id');
			foreach ($list as $k => &$v){
				$inarr = explode(',',$v['treepath']);
				$titlearr = [];
				foreach ($inarr as $j => $val){
					if(isset($menulist[$val])){
						$titlearr[] = $menulist[$val];
					}
				}
				$v['titleinfo'] = implode('、',$titlearr);
				$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
			}
			$count = $usergroup->getCount($where);
			$pageinfo = array('pagesize'=>$limit,'pagenum'=>$data['pagenum'],'total'=>$count);
			return return_format(['data'=>$list,'pageinfo'=>$pageinfo],0,lang('success'));
		}else{
			return return_format('',11002,lang('error_log'));
		}
	}


	/**
	 * [getUserGuoup 获取全部部门列表]
	 */
	public function getAllUserGroup(){
		$usergroup = new Accessusergroup();
		$where = ['delflag'=>0,'id'=>array('gt',1)];
		$list = $usergroup->getList($where,1,1000);
		if($list){
			return return_format($list,0,lang('success'));
		}else{
			return return_format('',11002,lang('error_log'));
		}
	}


	/**
	 * 添加分组
	 * @param $data
	 */
	public function addGroup($data){
		$data = where_filter($data,['id','name','treepath']);
		if(!isset($data['name'])){
			return return_format('',11003,lang('error_log'));
		}else if(!isset($data['treepath'])){
			return return_format('',11004,lang('error_log'));
		}
		$data['treepath'] = implode(',',array_filter(array_unique(explode(',',$data['treepath']))));
		$usergroup = new Accessusergroup();
		$usergroup->addEdit($data);
		return return_format('',0,lang('success'));
	}


	/**
	 * 删除部门
	 * @param $data
	 */
	public function deleteGroup($data){
		$data = where_filter($data,['id']);
		if(!isset($data['id']) || $data['id']==1){
			return return_format('',11005,lang('error_log'));
		}

		$adminMerder = new Adminmember();
		$count = $adminMerder->getCount(['groupids'=>$data['id']]);
		if($count>0){
			return return_format('',11005,lang('11008'));
		}

		$usergroup = new Accessusergroup();
		$data['delflag'] = 1;
		$num = $usergroup->addEdit($data);
		if($num){
			return return_format('',0,lang('success'));
		}else{
			return return_format('',11006,lang('error'));
		}
	}


	/**
	 * [getAccount 部门菜单]
	 * @param  [type] $data  [提交参数数据源]
	 * @return [type]        [description]
	 */
	public function menuListTree($data){
		$data = where_filter($data,['id']);
		$access = new Accessmenu();
		$where = ['status'=>0];
		$list = $access->getList($where);
		if($list){
			$usergroup = new Accessusergroup();
			$info = [];
			if(isset($data['id'])){
				$info = $usergroup->getById($data['id']);
				if(!$info){
					return return_format(['data'=>$list],11000,lang('param_error'));
				}
				$info = array_unique(explode(',',$info['treepath']));
			}
			foreach ($list as $k => &$v){
				if($info && in_array($v['id'],$info)){
					// 选中状态
					$v['instatus'] = 0;
				}else{
					$v['instatus'] = 1;
				}
				$v['path'] = $v['fatherid']?$v['fatherid'].','.$v['id']:$v['id'];
			}
			$list = toTree($list,'id','fatherid','list');
			foreach ($list as $k => &$val){
				if(isset($val['list'])){
					$sum = array_sum(array_column($val['list'],'instatus'));
					$val['instatus'] = $sum>0?1:0;
				}else{
					$val['instatus'] = 1;
				}
			}

			return return_format($list,0,lang('success'));
		}else{
			return return_format('',11001,lang('error_log'));
		}
	}


	/**
	 * 获取对应的分组下的菜单
	 * @author	JCR
	 * @param	$groups 分组id
	 */
	public function getMenuTree($groups){
		if($groups==1){
			// 超级管理员
			$where = ['status'=>0];
		}else{
			// 角色分组
			$usergroup = new Accessusergroup();
			$info = $usergroup->getById($groups);
			$where = ['status'=>0,'id'=>['in',$info['treepath']]];
		}
 
		$access = new Accessmenu();
		$list = $access->getList($where);
		if($list){
			$list = toTree($list,'id','fatherid','list');
			return return_format($list,0,lang('success'));
		}else{
			return return_format('',11007,lang('error_log'));
		}
	}





    
}



?>