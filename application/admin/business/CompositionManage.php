<?php
/**
 * 机构端教师管理 业务逻辑层
 * 
 * 
 */
namespace app\admin\business;
use app\admin\model\Studentinfo;
use app\admin\model\Composition;
use app\admin\model\Compositioncomment;
use app\admin\model\Hjxappcommentlabel;
use \Messages;
class CompositionManage{
	
	/**
	 * [getCompositionStatistics 获取学员的详细信息]
	 * @Author wyx
	 * @DateTime 2018-04-20T13:56:36+0800
	 * @param    [int]        $studentid [学生标识id]
	 * @return   [array]                 [description]
	 */
	public function getCompositionStatistics(){
	
			$commodel = new Composition;
			$data['total'] = $commodel->getCompositionListCount(['c.delflag'=>0, 'submit'=>1]);
			$data['reviewed'] = $commodel->getCompositionListCount(['c.delflag'=>0, 'c.reviewstatus'=>2, 'c.submit'=>1]);
			
			return return_format($data,0, lang('success'));

	}
	
	
	/**
	 * [getHjxUserList description]
	 * @Author lc
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getCompositionList($reviewstatus,$nickname,$studentreviewscore,$pagenum,$limit){
		$where = [] ;
		if(!in_array($reviewstatus, [0,1])) return return_format('', 90001, lang('param_error'));
		if($reviewstatus == 0){
			$where['c.reviewstatus'] = ['in', '0,1'];
			$where['c.submit'] = 1;
			$where['c.delflag'] = 0;
			$order = 'c.id desc';
		}else{
			$where['c.reviewstatus'] = 2;
			$where['c.delflag'] = 0;
			$where['c.submit'] = 1;
			if($studentreviewscore > 0){
				$where['ccc.reviewscore'] = $studentreviewscore;
			}
			$order = 'cc.reviewtime desc';
		}
		
		!empty($nickname) && $where['hs.nickname'] = ['like', '%'.$nickname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		//$where['delflag'] = 1;

		$commodel = new Composition;
		$comcommentmodel = new Compositioncomment;
		$resultarr = $commodel->getCompositionList($where,$limitstr,$order);
		
		foreach ($resultarr as &$val) {
			$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
			if($val['reviewstatus'] == 0){
				$preid = $commodel->getPreCompositionData($val['id'], $val['studentid'])['id'];
				$predata = $comcommentmodel->getTeacherComment($preid);
				$val['pretechername'] = empty($predata) ? null : $predata['teachername'];
				$val['prereviewtime'] = empty($predata) ? null : $predata['reviewtime'];
			}else if($val['reviewstatus'] == 2){
				$val['techername'] = $comcommentmodel->getTeacherComment($val['id'])['teachername'];
				$val['reviewtime'] = date('Y-m-d H:i:s', $val['reviewtime']);
			}
			unset($val['studentid']);
			unset($val['reviewstatus']);
		}
		
		$total = $commodel->getCompositionListCount($where);
		//返回数组组装
		$result = [
				'data'=>$resultarr,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;

		return return_format($result,0) ;
	}
	
	/**
	 * [getCompositionDetail description]
	 * @Author lc
	 * @DateTime 2018-04-20T11:08:35+0800
	 * @param $compositionid
	 * @return array
	 */
	public function getCompositioninfo($compositionid){
		if(!is_intnum($compositionid)){
			return return_format('', 90008, lang('param_error')) ;
		}
		
		$commodel = new Composition;
		$comcommentmodel = new Compositioncomment;
		$labelmodel = new Hjxappcommentlabel;
		$result['composition'] = $commodel->getCompositionData($compositionid);
		$result['composition']['content'] = urldecode($result['composition']['content']);
		if($result['composition']['reviewstatus'] == 2){
			$result['techercomment'] = $comcommentmodel->getTeacherComment($compositionid);
			$result['studentcomment'] = $comcommentmodel->getStudentComment($compositionid);
			if($result['studentcomment']['commentlabelids']){
				$result['studentcomment']['commentlabels'] = $labelmodel->getLabelNamesByIds($result['studentcomment']['commentlabelids']);
				unset($result['studentcomment']['commentlabelids']);
			}
		}

		return return_format($result,0);
	}
	
	/**
	 * [delComposition 伪删除信息]
	 * @Author
	 * @DateTime 2018-04-20T09:57:00+0800
	 * @param    [int]       $id [description]
	 * @return   [type]                 [description]
	 */
	public function delComposition($id){
		if($id>0){
			$Compositionobj = new Composition;
			$compositiondata = $Compositionobj->getCompositionData($id);
			$return = $Compositionobj->delComposition($id);
			if($return['code'] == 0){
				$megs = new \Messages();
				
				$vals['usertype'] = 4;
				$vals['userid'] = '4' . $compositiondata['studentid'];
				$vals['title'] = '删除提醒';
				$vals['content'] = "您的作文{$compositiondata['title']}已被管理员删除";
				$info = $megs->addMessage($vals, 14);
				return return_format('', 0, lang('success'));
			}else{
				return return_format('', 10020, lang('error'));
			}
		}else{
			return return_format('',40115);
		}
	}
}
