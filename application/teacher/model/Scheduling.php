<?php

namespace app\teacher\model;
use think\Db;
use think\Model;
use app\teacher\model\Curriculum;
use app\teacher\model\Organconfig;
use app\teacher\model\ToteachTime;
//对排课表（排班）进行操作

class Scheduling extends Model
{
    protected $pk = 'allaccountid';
    protected $table = 'nm_scheduling';
	protected $organid;


    // 分类添加验证规则 一对一验证规则
    public $rule = [
        'curriculumid'  => 'require',
        'totalprice'  => 'require|number',
        'teacherid'  => 'require'
        ];

    public $message  = [
        'categoryname.require' => '请选择课程',
        'totalprice.max'     => '价格不能为空',
        'totalprice.number'     => '价格必须是数字',
        'teacherid.require'     => '请选择开课老师',
    ];

    // 大班课和小班课 验证规则
    public $rulemax = [
        'curriculumid'  => 'require',
        'totalprice'  => 'require|number',
        'periodname' => 'require',
        'gradename'  => 'require',
        'teacherid'  => 'require'
        ];

    public $messagemax  = [
        'categoryname.require' => '请选择课程',
        'totalprice.max'     => '价格不能为空',
        'totalprice.number'     => '价格必须是数字',
        'periodname.require' => '课时名称不能为空',
        'gradename.require' => '班级名称不能为空',
        'teacherid.require'     => '请选择开课老师',
    ];

   /*
    * 根据teacherid，获取课程id
    * @Author wangwy
    * @param $teacherid  教师id
    * @param $limitstr   取出多少条记录
    * @return array
    */

    public function getSchedule($teacherid,$limitstr){
        $where = ['teacherid','eq',$teacherid];
        return Db::table($this->table)
            ->where($where)
            ->field('teacherid,status')
            ->limit($limitstr)
            ->order('curriculumid','desc')
            ->select();
    }

   // /**
   //  * 关联表模型,主要关联排课表和课程表
   //  * @ wangwy
   //  * @param
   //  * @return array();
   // */
   // public function schedule_curri(){
   //   return $this->hasOne('Curriculum','curriculumid','id')->field('
   //   coursename,classtypes,classnum,status');
   // }
   // *
   //  * getId 根据课程id 查询课程名称，开班类型
   //  * @ wangwy
   //  * @param $where
   //  * @param $limitstr 课程id
   //  * @return array();

   //  public static function getSchCurri($allaccountid,$limitstr){
   //   return self::with('schedule_curri')->where($where)->limit($limitstr)->select();
   // }

    /*
     * 关联表模型,主要关联排课表和课程表
     * @Author wangwwy
     * @param $allaccountid  教师id
     * @param $limitstr   取出多少条记录
     * @return array
     */
    public function getSchCurri($teacherid,$limitstr){
        $where['teacherid'] = $teacherid;
        $list = Db::table($this->table)
                    ->where($where)
                    ->field('curriculumname,type,status,realnum,classstatus')
                    ->limit($limitstr)->select();
        return $list?$list:[];
                   // print_r(Db::table($this->table)->getlastsql());
    }



  /*
   * [getOpenClassList 获取开过的课程包括成功失败都算]
   * @Author wyx
   * @DateTime 2018-04-19T15:31:56+0800
   * @param    [int]        $organid   [机构id]
   * @param    [int]        $teacherid [教师id]
   * @return   [type]                  [description]
   */
    public function getOpenClassList($organid,$teacherid){
        $where  = [
           'sg.teacherid' => $teacherid ,
           'sg.organid'   => $organid
        ] ;
        $field = 'sg.id,sg.gradename,sg.price,sg.curriculumid,sg.type,cm.coursename,cm.imageurl' ;
        return Db::table($this->table.' sg')->field($field)->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')->where($where)->select() ;
    }

   /*
    * 获取课程id
    * @Author wangwwy
    * @param $teacherid  教师id
    * @param $organid  取出多少条记录
    * @return array
    */

    public function getCurrische($teacherid){
        return Db::table($this->table)
            ->field('curriculumid,realnum')
            ->where('teacherid','eq',$teacherid)
            ->where('delflag','eq',1)
            ->select();
    }



  /*
   * 根据teacherid，获取成班人数
   * @Author wangwy
   * @param $teacherid  教师id
   * @param $limitstr   取出多少条记录
   * @return array
   */

//   public function getClassnum($teacherid,$organid){
//       return Db::table($this->table)
//           ->where('teacherid','eq',$teacherid)
//           ->where('organid','eq',$organid)
//           ->field('classnum')
//           ->select();
//   }


   /**
   * 获取
   * @Author wangwwy
   * @param $teacherid  教师id
   * @param $organid  取出多少条记录
   * @return array
   */

    public function getPeriodinfo($teacherid,$schedulingid){
        return Db::table($this->table)
               ->where('teacherid','eq',$teacherid)
               ->where('id','eq',$schedulingid)
               ->field('curriculumid,gradename')
               ->find();
    }






  /*
   * PC 后台获取 当前教师开课列表
   * @Author jcr
   * @param $data    array       必填
   * @param $order    string      必填
   * @param $limitstr string      必填
   * @DateTime 2018-04-17T11:32:53+0800
   * @return   array            [description]
   */
    public function getClassesList($data,$pagesize,$order='id desc')
    {
        $where = [];
        if(isset($data['name'])&&$data['name']) $where['cm.coursename'] = ['like','%'.$data['name'].'%'];
        if(isset($data['type'])) $where['sg.type'] = $data['type'];
        if(isset($data['teacherid'])) $where['sg.teacherid'] = $data['teacherid'];
        if(isset($data['classstatus']))$where['sg.classstatus'] = $data['classstatus'];
        $where['sg.delflag'] = 1;//1 未删除
        $field = 'sg.id,sg.gradename,sg.curriculumid,sg.classstatus,sg.fullpeople,sg.status,sg.type,sg.recommend,cm.coursename,cm.imageurl,sg.price,sg.totalprice,cm.categorystr' ;

        $infos['data'] = Db::table($this->table.' sg')->field($field)
                ->join('nm_curriculum cm','sg.curriculumid=cm.id','LEFT')
                ->where($where)
                ->page($data['pagenum'],$pagesize)
                ->order($order)
                ->select() ;
         // print_r(Db::table($this->table.' sg')->getlastsql());
        $infos['pageinfo'] = array('pagesize'=>$pagesize,'pagenum'=>$data['pagenum'],'total'=> $this->getCourseListCount($where));
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


    /*
     * 添加排课
     * @author jcr
     * @param [typ`e] $data           [description]
     * @param [type] $curriculumInfo [description]
     */
     public function adds($data,$curriculumInfo,$organid){
     	 $source = where_filter($data,array('curriculumid','type','totalprice','teacherid','gradename','deadline','recommend','status'));
     	 // 课时时间
     	 //$source['classhour'] = $this->getConfigKey($data['type'],$organid);
     	 $source['organid'] = $organid;
     	 $source['curriculumname'] = $curriculumInfo['coursename'];
         $source['addtime'] = time();
 		 $source['imageurl'] = $curriculumInfo['imageurl'];
     	//开启事务
         Db::startTrans();
     	 $id = Db::table($this->table)->insertGetId($source);
     	 if(!$id){
             Db::rollback();
     		return ['code'=>10057,'info'=>'发布失败'];
     	 }


     	 $organs = new Organ();
     	 if($organs->getOrganmsgById($organid)['vip'] == 0){
     		//免费机构
 			$source['id'] = $id;
 			$deputy = new Schedulingdeputy();
 			if(!$deputy->addEdit($source)){
 				Db::rollback();
 				return ['code'=>10057,'info'=>'发布失败'];
 			}
 		 }

     	 // 添加时将period 表数据 复制一份到 lessons
     	 $period = new Period();
     	 $lessons = new Lessons();
         $unit = new Unit();
         $unitdeputy = new Unitdeputy();

         // 获取对应的课时信息
     	 $periodlist = $period->getIdsLists($curriculumInfo['id'],$organid);
         // 获取对应的课时单元
         $unitlist = $unit->getLists($curriculumInfo['id'],$organid);
         $toteachtime = new Toteachtime();
         $sortarr = [];
         if($data['type']!=1){
             foreach ($data['list'] as $k => $v) {
                 $sortarr[$v['id']] = array('intime'=>$v['intime'],'timekey'=>$v['timekey']);
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
             $unitdeptyinfo = $unitdeputy->addEdit($val,$organid);
             if($unitdeptyinfo['code']!=0){
                 Db::rollback();
                 return array('code'=>10064,'info'=>'添加失败');
                 break;
             }

             foreach ($periodlist as $k => $v) {
                 if($v['unitid'] == $unitid){
                     $periodid = $v['id'];
                     unset($v['id']);
                     $v['teacherid'] = $data['teacherid'];
                     $v['schedulingid'] = $id;
                     $v['unitid'] = $unitdeptyinfo['info'];

                     $infos = $lessons->addEdit($v,$organid);
                     if($infos['code']!=0){
                         Db::rollback();
                         return array('code'=>10063,'info'=>'添加失败');
                         break;
                     }

                     // 在班级类型不为一对一时 开课班级选择时间
                     if($data['type']!=1){
                         // 对应时间集合
                         $toteachArr = [
                             'intime'    => $sortarr[$periodid]['intime'],
                             'coursename'=> $curriculumInfo['coursename'],
                             'teacherid' => $data['teacherid'],
                             'type'      => $data['type'],
                             'organid'   => $organid,
                             'timekey'   => $sortarr[$periodid]['timekey'],
                             'lessonsid' => $infos['info'],
                             'schedulingid'=>$id,
                         ];

                         // 计算课时结束时间
                         $toteachArr['endtime'] = $this->endCalculate($toteachArr['intime'],$toteachArr['timekey'],$source['classhour']);

                         if($val['unitsort']==1&&$v['periodsort']==1){
                             // 第一单元的第一个课时
                             if($unitcount==1&&$maxunitCount==1){
                                 //预防只有一个课时的情况
                                 $toteachArr['insort'] = 2;
                             }else{
                                 $toteachArr['insort'] = 1;
                             }
                         }else if($val['unitsort']==$unitcount&&$v['periodsort']==$maxunitCount){
                             // 最后一个单元的最后一个课时
                             $toteachArr['insort'] = 2;
                         }else{
                             $toteachArr['insort'] = 0;
                         }
                         $toteach[] = $toteachArr;
                     }
                 }
             }
         }

         if($toteach){
             if($toteachtime->addEdit($toteach,$organid,'all')['code']!=0){
                 Db::rollback();
                 return array('code'=>10065,'info'=>'添加失败');
             }
         }

     	Db::commit();
     	return ['code'=>0,'info'=>'发布成功'];
     }

    /*
     * [getInfoId 获取开课详情]
     * @param  [type] $id [开课表id]
     * @return [type]     [description]
     */
     public function getInfoId($id){
         $field = 'curriculumid,type,totalprice,gradename,status,classhour,curriculumname,status';
         return Db::table($this->table)->where('id','eq',$id)->field($field)->find();
     }



    /*
     * 开课编辑
     * @param  [type] $data           [提交编辑内容]
     * @param  [type] $curriculumInfo [课程信息]
     * @return [type]                 [description]
     */
    public function edits($data,$curriculumInfo,$organid){
      $source = where_filter($data,array('totalprice','gradename','recommend','status'));

      // 获取对应的排课表信息
      $scheduleInfo = $this->getInfoId($data['id'],$organid);
      $editArr = [];
      if($data['type']==1){
        if($data['totalprice']!=$scheduleInfo['totalprice']){
          $editArr['totalprice'] = $data['totalprice'];
        }
      }else{
        if($data['totalprice']!=$scheduleInfo['totalprice']||
          $data['gradename']!=$scheduleInfo['gradename']
          ){
          $editArr = $source;
        }
      }
      //是否修改
      $inType = false;

      //开启事务
        Db::table($this->table)->startTrans();

      //查看是否需要修改开课表
      if($editArr){
        if(!Db::table($this->table)->where('id','eq',$data['id'])->update($editArr)){
          return array('code'=>10066,'info'=>'编辑失败');
        }
        $inType = true;
      }

      // 获取对应的课时id集
      $lessonsIds = implode(',',array_column($data['list'],'id'));
      var_dump($lessonsIds);

      $toteach = new Toteachtime();

      $list = $toteach->getLessonsIds($lessonsIds);
      $listkey = arr_key_value($list,'lessonsid');

      // var_dump($list);

      // 需要修改的数组
      foreach ($data['list'] as $k => $v) {

        if(isset($listkey[$v['id']])){
          $inone = explode(',',$v['timekey']);
          $intwo = explode(',',$listkey[$v['id']]['timekey']);

          if(array_merge(array_diff($inone,$intwo),array_diff($intwo,$inone))||$v['timekey']!=$listkey[$v['id']]['timekey']){
            $toinfos = $toteach->editlessonsid(array('id'=>$v['id'],'intime'=>$v['intime'],'timekey'=>$v['timekey']));
            if($toinfos['code']!=0){
              Db::table($this->table)->rollback();
              return array('code'=>10067,'info'=>'编辑失败');
              break;
            }
            $inType = true;
          }
        }
      }

      if(!$inType){
        Db::table($this->table)->rollback();
        return ['code'=>10068,'info'=>'请先编辑后再提交'];
      }


      Db::table($this->table)->commit();
      return ['code'=>0,'info'=>'编辑成功'];

    }


    /**
     * [enrollStudent 是否启用]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function enrollStudent($data){
        return Db::table($this->table)->where('id','eq',$data['id'])->where('organid','eq',$data['organid'])->update(['status'=>$data['status']]);
    }


    /*
     * [deleteScheduling 删除开课信息]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function deleteScheduling($data,$type){
      //开启事务
        Db::table($this->table)->startTrans();
        if(!Db::table($this->table)->where('id','eq',$data['id'])->update(['delflag'=>0])){
            Db::rollback();
            return ['code'=>20016,'info'=>lang('20016')];
        }
        //如果开班类型是一对一，而且没有人下单，则toteachtime 没有创建主键
//        $toteach = new Toteachtime();
//        $mm = $toteach->getTotimeSchelist($data['id']);
//        if (!empty($mm)){
//            if(!$toteach->deletes($data['id'])){
//                Db::rollback();
//                return ['code'=>20017,'info'=>lang('20017')];
//            }
//        }
        //在不是一对一且无人报名的情况下允许删除课程表时间
        if($type!=1){
            $toteach = new Toteachtime();
            if(!$toteach->deletes($data['id'])){
                Db::rollback();
                return ['code'=>20017,'info'=>lang('20017')];
            }
        }

        Db::commit();
        return ['code'=>0,'info'=>lang('success')];
    }






    //模拟函数
    public function getConfigs($key){
      $configs = array('1'=>50,'2'=>40,'3'=>30,'classmaxtime'=>45,'classonetime'=>45);
      return $configs[$key];
    }


    /**
     * 获取对应的机构默认设置
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getConfigKey($key){
        $reigonconfig = new Organconfig();
        $info = $reigonconfig->getOrganid($this->organid);
        if($key==1){
          return $info['toonetime'];
        }else if($key==2){
          return $info['smallclasstime'];
        }else{
          return $info['bigclasstime'];
        }


    }



    /**
     *  获取对应开课详情 对应数据
     */
    public function onetooneClass($curriculumid,$teacherid,$type,$id=false){
        if($id){
            // 开课回显
            $info = Db::table($this->table)->alias('s')
                         ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
                         ->where('s.id','eq',$id)
                         ->where('s.teacherid','eq',$teacherid)
                         ->field('s.id,s.totalprice,s.imageurl,c.price,s.gradename,s.teacherid,s.curriculumname,s.curriculumid,c.periodnum,s.classhour')
                         ->find();
        }else{
            // 课程回显
            $curriculum = new Curriculum();
            $info = $curriculum->getId($curriculumid,'periodnum,coursename as curriculumname,price,imageurl');
            $info['classhour'] = $this->getConfigKey($type);
        }
        //print_r(Db::table($this->table)->getlastsql());
        return $info;
    }

    /*
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserList($organid,$categoryid,$tagid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid)
                    ->union("SELECT id FROM nm_curriculum WHERE categoryid IN ($categoryid)");
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        //$sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /*
     * [getFilterCourserList 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserApp($organid,$categoryid,$tagid,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid)
                    ->union("SELECT id FROM nm_curriculum WHERE categoryid IN ($categoryid)");
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        //$sql = db::table($this->table)->getLastSql();
        return  $lists;
    }
    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getFilterCourserCount($organid,$categoryid,$tagid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid)
                    ->union("SELECT id FROM nm_curriculum WHERE categoryid IN ($categoryid)");
            })
            ->count();
        return  $lists;
    }
    //获取班级名称
    public function getgradename($gradearr){
        return Db::table($this->table)->where('id','IN',$gradearr)
            ->column('id,gradename');
    }
    //更改班级名称
    public function updateGradename($data){
        $source = where_filter($data,array('gradename'));
            return Db::table($this->table)->where('id','eq',$data['id'])->where('organid','eq',$data['organid'])->update($source);
    }
    //更改课时总价
    public function updateTotalprice($data){
        $source = where_filter($data,array('totalprice'));
            return Db::table($this->table)->where('id','eq',$data['id'])->where('organid','eq',$data['organid'])->update($source);
    }

    /*
     * [getCourserListByAll 按分类和标签筛选课程]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAll($organid,$categoryid,$tagids,$limitstr){
        $lists = Db::table($this->table.' s')
            ->field('s.id as scheduid,s.curriculumid,s.type,s.totalprice,t.teachername,c.imageurl,c.coursename,c.subhead')
            ->join('nm_curriculum c','s.curriculumid = c.id','LEFT')
            ->join('nm_teacherinfo t','s.teacherid = t.teacherid','LEFT')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagids){
                $query->field('id')->table('nm_curriculum')->where('delflag','eq',1)->where('status','eq',1)->where('categoryid','in',$categoryid)->where($tagids);
            })
            ->order('s.sortnum')
            ->limit($limitstr)
            ->select();
        $sql = db::table($this ->table)->getLastSql();
        return  $lists;
    }

    /**
     * [getFilterCourserCount 按分类和标签筛选课程数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    [int]        $organid  [机构id]
     * @param    [string]     $limitstr [分页条件]
     * @return   array
     */
    public function getCourserListByAllCount($organid,$categoryid,$tagid){
        $lists = Db::table($this->table.' s')
            ->where('s.status','eq',1)
            ->where('s.delflag','eq',1)
            ->where('s.organid','eq',$organid)
            ->where('s.curriculumid','IN',function($query) use($categoryid,$tagid){
                $query->field('id')->table('nm_curriculum')->where('categoryid','IN',function($query) use($tagid){
                    $query->field('courseid')->table('nm_coursetagrelation')->where('tagid','IN',$tagid);
                });
            })
           ->count();
        $sql = db::table($this->table)->getLastSql();
        return  $lists;
    }

    /**
    * 查询一对一开课程
    * @Author WangWY
    *
    */
    public function getOneClass($organid){
        return Db::table($this->table)
                 ->where('type','eq',1)
                 ->where('status','eq',1)
                 ->where('delflag','eq',1)
                 ->where('organid','eq',$organid)
                 ->column('curriculumid');
    }
    /**
     * 查询所有班级的课程id
     * @Author WangWY
     *
     */
    public function getAllClass($teacherid,$organid,$type){
        return Db::table($this->table)
            ->where('status','eq',1)
            ->where('delflag','eq',1)
            ->where('teacherid','eq',$teacherid)
            ->where('organid','eq',$organid)
            ->where('type','eq',$type)
            ->column('curriculumid');
    }

    public function getGradeone($curriculumid){
        return Db::table($this->table)
                 ->where('curriculumid','eq',$curriculumid)
                 ->field('gradename')
                 ->find();
    }
    /*
     *  查询一对一开课的课程排课信息
     *  @Author wangwy
     *
     */
    public function getOneScheinfo($curriculumid,$teacherid){
        return Db::table($this->table)
            ->where('type','eq',1)
            ->where('delflag','eq',1)
            ->where('teacherid','eq',$teacherid)
            ->where('curriculumid','eq',$curriculumid)
            ->field('id,gradename')
            ->find();
    }

    /*
     * 查询课程状态，是否已经结束
     *  @Author wangwy
     */
    public function getInfostatus($id){
        return Db::table($this->table)
            ->where('id','eq',$id)
            ->field('classstatus,type')
            ->find();
    }
    public function getAlllist($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->field($field)
            ->find();
    }
    public function getAllcloumn($where,$field){
        return Db::table($this->table)
            ->where($where)
            ->column($field);
    }
    public function showAllcurribyschid($schedulingid){
        return Db::table($this->table)
            ->alias('c')
            ->join('nm_curriculum t','c.curriculumid = t.id')
            ->join('nm_teacherinfo m','c.teacherid = m.teacherid')
            ->where('c.id','eq',$schedulingid)
            ->field('t.imageurl,t.coursename,t.subhead,t.generalize,t.categoryid,t.categorystr,c.curriculumid,c.type,c.addtime,c.status,m.nickname')
            ->find();
        //return Db::table($this->table)->getLastSql();
    }


}
