<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class Scheduling extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_scheduling';



    // 分类添加验证规则 一对一验证规则
    public $rule = [
        'curriculumid'  => 'require',
        'totalprice'  => 'require|number',
        'teacherid'  => 'require'
        ];

	// 大班课和小班课 验证规则
	public $rulemax = [
		'gradename'  => 'require',
		'price'  => 'require|number',
		'fullpeople'  => 'require'
	];

    public $message;
    public $messagemax;

	//自定义初始化
	protected function initialize(){
		$this->message = [
			'totalprice.max'       => lang('10523'),
			'totalprice.number'    => lang('10524'),
			'teacherid.require'    => lang('10526'),
		];
		$this->messagemax  = [
			'gradename.require'    => lang('10525'),
			'price.require'       => lang('10523'),
			'price.number'    => lang('10524'),
			'fullpeople.require'    => lang('10534'),
//			'fullpeople.number'    => lang('10535'),
		];

	}

    /**
     * [getTeacherList description]
     * @param  [type] $organid [description]
     * @param  [type] $type    [description]
     * @return [type]          [description]
     */
    public function checkSchedulExist($teacherid){
        return Db::table($this->table)->where(['teacherid'=>$teacherid, 'delflag'=>1])->field('id')->find();
    }
    /**
     * [getTeacherList description]
     * @param  [type] $organid [description]
     * @param  [type] $type    [description]
     * @return [type]          [description]
     */
    public function getTeacherList($where){
        return Db::table($this->table)->where($where)->field('teacherid')->select();
    }




	/**
	 * [getOpenClassList 获取开过的课程包括成功失败都算]
	 * @Author wyx
	 * @DateTime 2018-04-19T15:31:56+0800
	 * @param    [int]        $organid   [机构id]
	 * @param    [int]        $teacherid [教师id]
	 * @return   [type]                  [description]
	 */
	public function getOpenClassList($teacherid){
		$where  = [
					'sg.teacherid' => $teacherid
				] ;
		$field = 'sg.id,sg.gradename,sg.price,sg.curriculumid,sg.type,cm.coursename,cm.imageurl' ;
		return Db::table($this->table.' sg')->field($field)->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')->where($where)->select() ;
	}



	/**
	 * 获取开课列表
	 * @Author wyx
	 * @param $where    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array            [description]
	 */
    public function getCourseList($where,$limitstr,$order='id desc')
    {
    	$field = 'sg.id,sg.gradename,sg.teacherid,sg.price,sg.curriculumid,sg.type,sg.recommend,cm.coursename,cm.imageurl' ;

    	return Db::table($this->table.' sg')->field($field)->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')->where($where)->limit($limitstr)->order($order)->select() ;
    }
	/**
	 * [getRecruitCount 获取课程在招生班数量]
	 * @Author jcr
	 * @param [int] $curriculumid 课程id
	 * @return [type] [description]
	 */
	public function getRecruitCount($curriculumid){
		// 大课班 小课班
		$num = Db::table($this->table)->where('curriculumid','eq',$curriculumid)
					->where('type','neq',1)
					->where('status','eq',1)
					->count();
		// 一对一
		$one = Db::table($this->table)->where('curriculumid','eq',$curriculumid)
					->where('status','eq',1)
					->where('type','eq',1)
					->count();
		$count = $one?$num+1:$num;
		return 	$count;
	}


	/**
	 * [exchangeSort 根据两个id来交换sortnum值]
	 * @Author wyx
	 * @DateTime 2018-04-21T19:00:12+0800
	 * @param  $organid   机构类别id
	 * @param  $ids       要交换的id数组
	 * @return   [type]       [description]
	 */
	public function exchangeSort($ids)
    {	
    	$where['id'] = ['in',$ids] ;
    	$arr = Db::table($this->table)->field('id,sortnum')->where($where)->select();
    	if(count($arr)==2){
	    	Db::table($this->table)->where(['id'=>$arr[0]['id']])->update(['sortnum'=>$arr[1]['sortnum']]);
	    	Db::table($this->table)->where(['id'=>$arr[1]['id']])->update(['sortnum'=>$arr[0]['sortnum']]);
    		return return_format('',0);
    	}else{
    		return return_format('',40047);
    	}
	}
    /**
     *  班级总览，今日新增 昨日新增 本月新增
     *  @author wyx
     *  @param  $starttime 获取数据的开始时间
     *  @param  $organid   机构标识id
     *  @return array
     *
     */
    public function getAddClassByDate($starttime){
        return Db::table($this->table)
//        ->field('from_unixtime(addtime,"%Y-%m-%d") datestr,sum(realnum) stunum,count(realnum) counts')
        ->field('from_unixtime(addtime,"%Y-%m-%d") datestr,count(realnum) stunum')
        ->where('addtime','GT',$starttime)
		->where('schedule',1)
		->where('delflag',1)
        ->group('datestr')
        ->select();
    }
    /**
     *  获取机构招生中的班级个数
     *  @author wyx
     *  @param  $organid  机构标识id
     */
    public function getClassing(){
        return Db::table($this->table)
        ->where('status','EQ',1)// 获取招生中
		->where('classstatus','in','0,1,2')
		->where('delflag','EQ',1)// 获取招生中
        ->count();
    }
    /**
     * [setRecommendFlag 设置课程推荐位]
     * @Author
     * @DateTime 2018-04-21T19:57:10+0800
     * @param    [int]          $organid   [机构标记id]
     * @param    [int]          $courseid  [开课表id]
     * @param    [int]          $status    [设置标记值]
     */
    public function setRecommendFlag($courseid,$status){
    	$data  = ['recommend'=>$status] ;
    	$where = ['id'=>$courseid] ;
    	return $this->save($data,$where);
    }


    /**
     * [getInfoId 获取开课详情]
     * @param  [type] $id [开课表id]
     * @return [type]     [description]
     */
    public function getInfoId($id){
    	$field = 'id,curriculumid,type,totalprice,gradename,status,classhour,curriculumname,classstatus,schedule,price,fullpeople,teacherid,periodnum,starttime,endtime';
    	return Db::table($this->table)->where('id','eq',$id)->field($field)->find();
    }


    /**
	 * PC 后台获取 开课列表
	 * @Author jcr
	 * @param $data    array       必填
	 * @param $order    string      必填
	 * @param $limitstr string      必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array            [description]
	 */
    public function getClassesList($data,$limitstr,$order='id desc'){
    	$data = where_filter($data,['name','type','limit','instatus']);
    	$where = [];
//    	isset($data['name']) && $where['cm.coursename'] = ['like','%'.$data['name'].'%'];
		isset($data['name']) && $where['sg.gradename'] = ['like','%'.$data['name'].'%'];
    	isset($data['type']) && $where['sg.type'] = $data['type'];
    	isset($data['instatus']) && $where['sg.classstatus'] = $data['instatus'];


    	$where['sg.delflag'] = 1;
//    	$where['sg.schedule'] = 1;

    	$field = 'sg.id,sg.gradename,sg.realnum as payordernum,sg.curriculumid,sg.classstatus,sg.fullpeople,sg.teacherid,sg.periodnum,sg.status,sg.type,sg.recommend,cm.coursename,cm.imageurl,sg.totalprice,sg.price,cm.categorystr' ;

    	$infos['data'] = Db::table($this->table.' sg')->field($field)
    						->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')
    						->where($where)->page($data['limit'],$limitstr)
    						->order($order)
    						->select();
    	$infos['pageinfo'] = array('pagesize'=>$limitstr,'pagenum'=>$data['limit'],'total'=> $this->getCourseListCount($where));
    	return $infos;
    }


    /**
	 * PC 后台获取 开课列表总行数
	 * @Author jcr
	 * @param $where    array       必填
	 * @DateTime 2018-04-17T11:32:53+0800
	 * @return   array            [description]
	 */
    public function getCourseListCount($where){
    	return Db::table($this->table.' sg')
    						->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')
    						->where($where)
    						->count() ;
    }


	/**
	 * 添加课程分成两步走 第一步
	 * $data	要编辑的数据源
	 * $info
	 */
    public function oneEdit($data,$curriculumInfo,$info){
		if($info){
//			if ($info['schedule']==1){
//				$data['totalprice'] = $data['price'] * $info['periodnum'];
//			}
			$updateDate = thanArr($data,$info);
			if($updateDate){
				$num = Db::table($this->table)->where('id',$data['id'])->update($updateDate);
				if(!$num) return ['code'=>10136,'info'=>lang('error')];;

				if(isset($updateDate['price'])){
					// 同步更新对应的课程价格区间

					$interval = $this->getMaxMin($info['curriculumid']);
					$priceArr['price'] = $interval['min'];
					$priceArr['maxprice'] = $interval['max'];
					$priceArr['classnum'] = $interval['num'];

					$priceArr = thanArr($priceArr,$curriculumInfo);
					if($priceArr){
						$curricu = new Curriculum();
						if(!$curricu->edit($curriculumInfo['id'],$priceArr)){
							Db::rollback();
							return array('id'=>$data['id'],'code'=>10139,'info'=>lang('error'));
						}
					}
				}
			}
			return ['id'=>$data['id'],'code'=>0,'info'=>lang('success')];
		}else{
			$maxid = Db::table($this->table)->max('id');
			$maxid = $maxid?$maxid:0;
			$data['sortnum'] = $maxid+1;
			$data['status'] = 0;
			$data['schedule'] = 0;
			$data['addtime'] = time();
			$data['type'] = $curriculumInfo['classtypes'];
			$data['curriculumname'] = $curriculumInfo['coursename'];
			$data['imageurl'] = $curriculumInfo['imageurl'];
			$data['periodnum'] = $curriculumInfo['periodnum'];
			$id = Db::table($this->table)->insertGetId($data);
			if($id){
				return ['id'=>$id,'code'=>0,'info'=>lang('success')];
			}else{
				return ['id'=>$id,'code'=>10135,'info'=>lang('error')];
			}
		}
	}


    /**
     * 添加排课
     * @author jcr
     * @param [type] $data           [数据源]
     * @param [type] $curriculumInfo [课程id]
     */
    public function adds($data,$scheduleInfo,$curriculumInfo){
    	$source = where_filter($data,array('id','teacherid','deadline','recommend','status'));

		// 课时数量
		$source['schedule'] = 1;
		$source['status'] = 1;
		$source['periodnum'] = $curriculumInfo['periodnum'];
//		$source['totalprice'] = $source['periodnum'] * $scheduleInfo['price'];
    	//开启事务
        Db::startTrans();
		$id = $scheduleInfo['id'];


    	// 添加时将period 表数据 复制一份到 lessons
    	$period = new Period();
    	$lessons = new Lessons();
        $unit = new Unit();
        $unitdeputy = new Unitdeputy();

        // 获取对应的课时信息
    	$periodlist = $period->getIdsLists($curriculumInfo['id']);

        // 获取对应的课时单元
        $unitlist = $unit->getLists($curriculumInfo['id']);

        $toteachtime = new Toteachtime();
            
        $sortarr = [];
        if($scheduleInfo['type']!=1){
            foreach ($data['list'] as $k => $v) {
                $sortarr[$v['id']] = array('intime'=>$v['intime'],'timekey'=>$v['timekey'],'classhour'=>$v['classhour']);
            }
        }
		
        //Toteachtime 表数据结构
        $toteach = [];
        $unitcount = count($unitlist);

        $unitInArr = array_column($unitlist,'id','unitsort');

        // 获取最后一个单元的最大课时数
        $maxunitCount = 0;
        foreach ($periodlist as $k => $v) {
            if($unitInArr[$unitcount]==$v['unitid']){
                $maxunitCount++;
            }
        }

        foreach ($unitlist as $key => $val) {
            $unitid = $val['id'];
            unset($val['id']);
            $val['schedulingid'] = $id;
            $unitdeptyinfo = $unitdeputy->addEdit($val);
            if($unitdeptyinfo['code']!=0){
                Db::rollback();
                return array('code'=>10064,'info'=>lang('error'));
            }

            foreach ($periodlist as $k => $v) {
                if($v['unitid'] == $unitid){
                    $periodid = $v['id'];
                    unset($v['id']);
                    $v['teacherid'] = $data['teacherid'];
                    $v['schedulingid'] = $id;
                    $v['unitid'] = $unitdeptyinfo['info'];
                    $v['periodid'] = $periodid;
					$v['classhour'] = $sortarr[$periodid]['classhour'];
                    $infos = $lessons->addEdit($v);
                    if($infos['code']!=0){
                        Db::rollback();
                        return array('code'=>10065,'info'=>lang('error'));
                    }

                    // 在班级类型不为一对一时 开课班级选择时间
                    if($scheduleInfo['type']!=1){

                        // 对应时间集合
                        $toteachArr = [
                            'intime'    => $sortarr[$periodid]['intime'],
                            'coursename'=> $curriculumInfo['coursename'],
                            'teacherid' => $data['teacherid'],
                            'type'      => $scheduleInfo['type'],
                            'timekey'   => $sortarr[$periodid]['timekey'],
                            'lessonsid' => $infos['info'],
                            'schedulingid'=>$id,
                        ];

                        // 计算课时结束时间
                        $toteachArr['endtime'] = $this->endCalculate($toteachArr['intime'],$toteachArr['timekey'],$sortarr[$periodid]['classhour']);
                        // 计算开始时间
						$toteachArr['starttime'] = $this->getstartTime($toteachArr['intime'],$toteachArr['timekey']);
						// 计算是否是第一节课或者最后一节课
						$toteachArr['insort'] = $this->getInsort($val['unitsort'], $v['periodsort'], $unitcount, $maxunitCount);
//						$toteachArr['insort'] = $insort;

						if($toteachArr['insort'] == 1){
							$source['starttime'] = $toteachArr['intime'];
						}else if($toteachArr['insort'] == 2 && $unitcount ==1 && $maxunitCount==1){
							$source['starttime'] = $toteachArr['intime'];
							$source['endtime'] = $toteachArr['intime'];
						}else if($toteachArr['insort'] == 2){
							$source['endtime'] = $toteachArr['intime'];
						}

                        $toteach[] = $toteachArr;
                    }
                }
            }
        }
		$schedid = Db::table($this->table)->where('id',$scheduleInfo['id'])->update($source);
		if(!$schedid){
			Db::rollback();
			return ['code'=>10062,'info'=>lang('error')];
		}

        // 更新课程部分信息
        $interval = $this->getMaxMin($scheduleInfo['curriculumid']);
        $priceArr['price'] = $interval['min'];
        $priceArr['maxprice'] = $interval['max'];
        $priceArr['classnum'] = $interval['num'];

		$curricu = new Curriculum();
		if(!$curricu->edit($curriculumInfo['id'],$priceArr)){
			Db::rollback();
			return array('code'=>10138,'info'=>lang('error'));
		}

        if($toteach){
            if($toteachtime->addEdit($toteach,'all')['code']!=0){
                Db::rollback();
                return array('code'=>10066,'info'=>lang('error'));
            }
        }

    	Db::commit();
    	return ['code'=>0,'info'=>lang('success')];
    }


    /**
     * 开课编辑
     * @param  [type] $data           [提交编辑内容]
     * @param  [type] $curriculumInfo [课程信息]
     * @return [type]                 [description]
     */
    public function edits($data,$scheduleInfo){
    	$source = where_filter($data,array('id','teacherid','status'));

    	// 已结束的班级不允许编辑
		if($scheduleInfo['classstatus']==5){
			return ['code'=>10123,'info'=>lang('10123')];
		}
		
		$lessons = new Lessons();
		$toteach = new Toteachtime();

    	//开启事务
        Db::startTrans();
		
        if($scheduleInfo['type']!=1){
            // 获取对应的课时id集
            $lessonsIds = implode(',',array_column($data['list'],'id'));
            $list = $toteach->getLessonsIds($lessonsIds);
            $listkey = arr_key_value($list,'lessonsid');

            // 需要修改的数组

			$listCount = count($data['list']);
            foreach ($data['list'] as $k => $v) {
                if(isset($listkey[$v['id']])){
                    $inone = explode(',',$v['timekey']);
                    $intwo = explode(',',$listkey[$v['id']]['timekey']);

                    // 碰撞 看是否需要修改
                    if(array_merge(array_diff($inone,$intwo),array_diff($intwo,$inone)) || $v['timekey']!=$listkey[$v['id']]['timekey'] || $v['intime'] != $listkey[$v['id']]['intime']){
						// 授课中 已上完课的课时不允许编辑 包括上课前5分钟的课时都不允许编辑
						if($scheduleInfo['classstatus']==4){
							// 计算开始时间
							$statime = strtotime($listkey[$v['id']]['intime'].' '.get_time_key($intwo[0]))-300;
							if(time()>=$statime){
								$code = str_replace(['1','2'],[$v['unitsort'],$v['periodsort']],lang('10124'));
								Db::rollback();
								return array('code'=>10124,'info'=>$code);
							}
						}
                        $endtime = $this->endCalculate($v['intime'], $v['timekey'], $v['classhour']);
						// 计算开始时间
						$starttime = $this->getstartTime($v['intime'],$v['timekey']);
						
						//修改课时时长
						if($lessons->getId($v['id'],'classhour')['classhour'] != $v['classhour']){
							$leinfos = $lessons->editBylessonsid(array('id'=>$v['id'],'classhour'=>$v['classhour']));
							if($leinfos['code']!=0){
								Db::rollback();
								return array('code'=>10061,'info'=>lang('error'));
							}
						}
						//修改toteachtime
                        $toinfos = $toteach->editlessonsid(array('id'=>$v['id'],'intime'=>$v['intime'],'timekey'=>$v['timekey'],'endtime'=>$endtime,'starttime'=>$starttime));
                        if($toinfos['code']!=0){
                            Db::rollback();
                            return array('code'=>10061,'info'=>lang('error'));
                        }
                    }

                    if($listCount == 1){
						$source['starttime'] = $v['intime'];
						$source['endtime'] = $v['intime'];
					}else if($listCount>1 && $k == 0){
						$source['starttime'] = $v['intime'];
					}else if($listCount>1 && $k == $listCount - 1){
						$source['endtime'] = $v['intime'];
					}
                }
            }
        }

		$editArr = thanArr($source,$scheduleInfo);
		// 当为已超时情况 编辑后 会重新变为授课中
		if($scheduleInfo['classstatus']==6){
			$editArr['classstatus'] = 1;
		}

		//查看是否需要修改开课表
		if($editArr){
			if(!Db::table($this->table)->where('id','eq',$data['id'])->update($editArr)){
				Db::rollback();
				return array('code'=>10059,'info'=>lang('error'));
			}
		}
		
		//老师修改则修改lessons和teachtime的tercherid
		if(isset($editArr['teacherid']) && $editArr['teacherid'] > 0){
			$leinfo = $lessons->editBySchedulingid(array('schedulingid'=>$data['id'], 'teacherid'=>$editArr['teacherid']));
			$toinfo = $toteach->editBySchedulingid(array('schedulingid'=>$data['id'], 'teacherid'=>$editArr['teacherid']));
			if($leinfo['code']!=0 || $toinfo['code']!=0){
				Db::rollback();
                return array('code'=>11038, 'info'=>lang('11038'));
			}
		}

    	Db::commit();
    	return ['code'=>0,'info'=>lang('success')];
    }


    /**
     * [enrollStudent 是否启用]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function enrollStudent($data,$info){
		Db::startTrans();
    	$num = Db::table($this->table)->where('id','eq',$data['id'])
									  ->update(['status'=>$data['status']]);

    	if(!$num){
			Db::rollback();
			return false;
		}
		if(!$this->editPrice($info['curriculumid'])){
			Db::rollback();
			return false;
		}

		Db::commit();
		return TRUE;
    }


	/**
	 * 获取课程对应对最大价格和最小价格 最大班级数 同步更新课程
	 * @param $curriculumid
	 * @return bool
	 */
    public function editPrice($curriculumid){
		$curricu = new Curriculum();
		$interval = $this->getMaxMin($curriculumid);
		$priceArr['price'] = $interval['min'];
		$priceArr['maxprice'] = $interval['max'];
		$priceArr['classnum'] = $interval['num'];
		$curriculumInfo = $curricu->getSelectId($curriculumid);
		$priceArr = thanArr($priceArr,$curriculumInfo);
		if($priceArr){
			if(!$curricu->edit($curriculumInfo['id'],$priceArr)){
				return false;
			}
		}
		return TRUE;
	}

	/**
	 * 获取 第一个课时 和最后一个课时
	 * @param $unitsort		当前所在单元
	 * @param $periodsort	当前所在课时
	 * @param $unitcount	总的单元数
	 * @param $maxunitCount 单元下总的课时数
	 */
	function getInsort($unitsort, $periodsort, $unitcount, $maxunitCount) {
		// 第一单元的第一个课时
		if ($unitsort == 1 && $periodsort == 1) {
			//预防只有一个课时的情况
			return $unitcount == 1 && $maxunitCount == 1 ? 2 : 1;
		} else if ($unitsort == $unitcount && $periodsort == $maxunitCount) {
			// 最后一个单元的最后一个课时
			return 2;
		} else {
			// 中间过度课时
			return 0;
		}
	}


    /**
     * [automateEdit 执行定时任务去 及时更新对应的开课状态]
     * @param  [type] $data [传输参数数据源]
     * @return [type]       [description]
     */
    public function automateEdit($data,$toteach){
    	if($data['classstatus']==6){
			Db::table($this->table)->where('id','eq',$data['id'])->update(['classstatus'=>$data['classstatus']]);
    		return true;
		}

        // 开启事务
        Db::startTrans();
        if($data['type']!=1){
            // 不为一对一的情况去更新对应的 大课班 小课班状态
            $info = Db::table($this->table)->where('id','eq',$data['id'])->update(['classstatus'=>$data['classstatus']]);
            if(!$info){
                Db::rollback();
                return false;
            }
        }

        if(isset($data['periodnum'])){
        	// 预约教室会走这块
        	if($data['periodnum']!=1&&$data['classstatus']!=4){
				$toteachinfo = $toteach->editId(['insort'=>0,'id'=>$data['toteachtimeid']]);
			}else{
				$toteachinfo['code'] = 0;
			}
		}else{
			$toteachinfo = $toteach->editId(['insort'=>0,'id'=>$data['toteachtimeid']]);
		}

		if($toteachinfo['code']!=0){
			Db::rollback();
			return false;
		}


		Db::commit();
        //处理最后课时时 处理订单状态
        if($data['classstatus']==5){
            $order = new Ordermanage();
            //处理订单状态
            if($data['type']==1){
                //一对一 更新已支付订单 附属状态 已完成
                $order->orderSave(
                            ['ordernum'=>$data['ordernum'],'orderstatus'=>20],
                            ['closingstatus'=>1,'finishtime'=>time()]
                        );
            }else{
                //小班课 和 大班课对应处理 更新已支付订单 附属状态 已完成
                $order->orderSave(
                            ['schedulingid'=>$data['schedulingid'],'orderstatus'=>20],
                            ['closingstatus'=>1,'finishtime'=>time()]
                        );
            }


            // ～～～零时需求～～～ 开课班级课程上完立马结算订单金额
//			$orderlist = $order->getOrderList(['closingstatus'=>1,'orderstatus'=>20,'schedulingid'=>$data['schedulingid']],300);
//			if($orderlist){
//				$organpay = new Organpaylog();
//				foreach ($orderlist as $k => $v) {
//					$organpay->addPayLog($v);
//				}
//			}
        }
        return true;
    }


    /**
     * [enrollStudent 删除开课信息]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function deleteScheduling($data,$subinfo){
    	//开启事务
        Db::startTrans();
        if(!Db::table($this->table)->where('id',$data['id'])->update(['delflag'=>0])){
        	Db::rollback();
    		return ['code'=>10076,'info'=>lang('error')];
        }


		if($subinfo['type']!=1){
			$toteach = new Toteachtime();
			$toteach->deletes($data['id']);
		}

		if(!$this->editPrice($subinfo['curriculumid'])){
			Db::rollback();
			return ['code'=>10139,'info'=>lang('error')];
		}


        Db::commit();
    	return ['code'=>0,'info'=>lang('success')];
    }


	/**
	 * 获取指定课程对应的班级最大价格和最小价格
	 */
    public function getMaxMin($id){
		$list = DB::table($this->table)->where('curriculumid',$id)
										->where('status',1)
										->where('delflag',1)
										->where('schedule',1)
										->column('price','id');
		if($list){
			$max = max($list);
			$min = min($list);
//			if ($max == $min || count($list) == 1){
//				$min = 0;
//			}
		}else{
			$max = 0;
			$min = 0;
		}
		return ['min'=>$min,'max'=>$max,'num'=>count($list)];
	}

    
    /**
     * 获取对应的机构默认设置
     * @param  [type] $key [description]
	 * @param  [type] $people
     * @return [type]      [description]
	 *
     */
    public function getConfigKey(){
        $reigo = new Organ();
        $info = $reigo->getOrganmsgById();
        return $info;
    }
    

    /**
     *  获取对应开课详情 对应数据
     */
    public function onetooneClass($curriculumid,$type,$id=false){
    	if($id){
    		// 开课回显
            $field = 's.id,s.totalprice,s.price,s.gradename,s.fullpeople,s.schedule,s.teacherid,s.curriculumname,s.curriculumid,c.periodnum';
    		$info = Db::table($this->table)->alias('s')
    									   ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
    									   ->where('s.id','eq',$id)
    									   ->field($field)
    									   ->find();
    	}else{
    		// 课程回显
    		$curriculum = new Curriculum();
    		$info = $curriculum->getId($curriculumid,'periodnum,coursename as curriculumname,price');
            //$info['classhour'] = $this->getConfigKey($type)['classhours'];
            $info['schedule'] = '';
    	}
    	return $info;
    }

    /**
     * [endCalculate 获取当前课程的结束时间]
     * @param  [type] $intime  [日期]
     * @param  [type] $timekey [时间所对键值]
     * @param  [type] $minute  [课时]
     * @return [type]          [description]
     */
    public function endCalculate($intime,$timekey,$minute){
        return strtotime($intime.' '.get_time_key(explode(',',$timekey)[0]))+$minute*60;
    }

	/**
	 * [endCalculate 获取当前课时开始时间]
	 * @param  [type] $intime  [日期]
	 * @param  [type] $timekey [时间所对键值]
	 * @return [type]          [description]
	 */
    public function getstartTime($intime,$timekey){
		return strtotime($intime.' '.get_time_key(explode(',',$timekey)[0]));
	}


	/**
	 * [getOrderAccountList 获取对账列表]
	 * @param  [type] $data     [条件源]
	 * @param  [type] $pagenum  [第几页]
	 * @param  [type] $limit    [一页几条]
	 * @return [type]           [description]
	 */
	public function getBillList($where,$pagenum,$limit){
		$field = 's.id,s.teacherid,s.gradename,s.curriculumname,s.periodnum,t.nickname as teachername,s.classstatus,s.realnum';
		return Db::table($this->table)
						->alias('s')
						->join('nm_teacherinfo t','s.teacherid = t.teacherid')
						->where($where)
						->page($pagenum,$limit)->field($field)
						->select();
	}

	/**
	 * [getOrderCounts 根据条件查询总行数]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function getBillCount($where){
		return Db::table($this->table)
						->alias('s')
						->join('nm_teacherinfo t','s.teacherid = t.teacherid')
						->where($where)
						->count();
	}


	/**
	 * getCount 查询符合条件的条数
	 */
	public function getCount($where){
		return Db::table($this->table)->where($where)->count();
	}

	/**
	 * getList	根据条件查询列表
	 */
	public function getList($where,$order,$field,$limit,$pagenum){
		return Db::table($this->table)->where($where)->order($order)->field($field)->page($limit,$pagenum)->select();
	}

}
