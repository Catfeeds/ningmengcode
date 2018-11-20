<?php
/**
 * 机构端教师管理 业务逻辑层
 *
 *
 */
namespace app\teacher\business;
use app\teacher\model\StudentInfo;
use app\teacher\model\OrderManage;
use app\teacher\model\Studentfunds;
use app\appteacher\model\Organ;
use think\Db;
class StudentManage{
    /**获取当前教师的学生列表
	 * [getUserList description]
	 * @Author wangwy
	 * @param $mobil   根据手机号查询  可选
	 * @param $nickname根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $teacherid   教師id      必填
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getUserList($mobil,$nickname,$pagenum,$teacherid,$limit){
		//获取当前时间
		$where = [] ;
		!empty($mobil) && $where['mobile'] = $mobil ;
		!empty($nickname) && $where['nickname'] = ['like','%'.$nickname.'%'] ;

		$where['t.teacherid'] = $teacherid;
        $where['t.orderstatus'] = 20;
          
		$usermodel = new StudentInfo;
        $list = $usermodel->getUserList($where,$pagenum.','.$limit);
		//list 分为低于20个，高于20个,做出循环
        $x = 0; //循环次数限制90次
        do{
//            if($pagenum>0){
//                $start = ($pagenum - 1 ) * $limit ;
//                $limitstr = $start.','.$limit ;
//            }else{
//                $start = 0 ;
//                $limitstr = $start.','.$limit;
//            }
            $limit += isset($list)?20-count($list):0;
            $list = $usermodel->getUserList($where,$pagenum.','.$limit);
            $x+=1;
        }while(count($list) < 20 && $x <= 90);
        $list =  self::moreArrayunique($list);
		$now = date('Y',time());//当前时间
		foreach ($list as $key => $val) {
			# 根据生日获取年龄
			// $birthday = getdate($val['birth']);
			// $month=0;
            //  if($now['month']>$birthday['month'])
            //  $month=1;
            //  if($now['month']==$birthday['month'])
            //  if($now['mday']>=$birthday['mday'])
            //  $month=1;
            //错误用法： $val['age'] = $now['year']-$birthday['year']+$month;
            //$list[$key]['age'] = $now['year']-$birthday['year']+$month;
            $list[$key]['age'] = !empty($val['birth'])?$now - date('Y',$val['birth']):'';

		}
		$count = $usermodel->getOnlyListcount($where);
        //dump($count);
		if (empty($list)) {
			return return_format('',0,lang('success'));
		}else{
			return return_format(['data'=>$list,'pageinfo'=>array('pagenum'=>$pagenum,'pagesize'=>$limit,'total'=>$count)],0,lang('success'));
		}


	}


	/**
     * @desc 根据生日获取年龄
     * @Author wangwy
     * @param     string $birth
     * @return    integer
     */
    // function getAge($birthday) {
    //     $birthday=getDate(strtotime($birthday));
    //     $now=getDate();
    //     $month=0;
    //     if($now['month']>$birthday['month'])
    //     $month=1;
    //     if($now['month']==$birthday['month'])
    //     if($now['mday']>=$birthday['mday'])
    //     $month=1;
    //     return $now['year']-$birth['year']+$month;
    // }




    /**
	 * [getUserDetail 获取学员的详细信息]
	 * @Author wangwy
	 * @param    [int]        $studentid [学生标识id]
	 * @param    [int]        $organid   [机构标识id]
	 * @return   [array]                 [description]
	 */
	public function getUserDetail($studentid,$pagenum,$pagesize){
		if($studentid>0){
			$usermodel = new Studentinfo;
			$data = $usermodel->getUserDetail($studentid);
			if(!empty($data)){
				$data['birthday']  = date('Y-m-d',$data['birth']);
				//获取学生购买过的课程
				$orderobj  = new Ordermanage;
		        $orderlist = $orderobj->getStudentOrder($studentid,$pagenum,$pagesize) ;
		        //$orderlist['ordertime'] = date('Y-m-d H:i:s',$orderlist['ordertime']);
		        foreach ($orderlist['data'] as $key => $val) {
		        	$orderlist['data'][$key]['ordertime']= date('Y-m-d H:i:s',$val['ordertime']);
		        }
				return return_format(['data'=>$data,'courselist'=>$orderlist],0,lang('success')) ;
			}else{
				return return_format([],0,lang('success')) ;
			}
		}else{
			return return_format([],21006,lang('21006')) ;
		}
	}







	/*
	 * [addStudentInfo 新增学员]
	 * @Author wangwy
	 * @param    [array]      $data    [用户需要入库数据，必须规定可更新字段]
	 * @param    [int]        $teacherid [学生标识id]
	 */
	public function addStudentInfo($data,$teacherid){

		$allowfield = ['prphone','mobile','nickname','sex',];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield) ;
        $usermodel = new StudentInfo;
        $ret = $usermodel->addStudent($newdata,$teacherid);
        // //对studentfunds进行添加
        // $ccc = $usermodel
        // $studentfund = new Studentfunds;
        // $studentfund = $studentfund->addStudentinfo()

       //var_dump($ret);
        if (isset($ret)) {
            $newdata['teacherid'] = $teacherid ;
            //如果数据插入成功,调用重置密码功能
            self::sendPassword($newdata);
            return return_format($ret,0,lang('success'));
        }else{
            return return_format([],21007,lang('21007'));
        }

	}
	/**
	 * [updateStudent 更新学生信息]
	 * @Author wyx
	 * @DateTime 2018-04-20T20:53:36+0800
	 * @param    [array]       $data    [需要更新的字段]
	 * @param    [int]         $teacherid [教师标记id]
	 * @return   [array]
	 */
	public function updateStudentInfo($data,$teacherid){
		$allowfield = ['id','imageurl','mobile','nickname','username','sex','country','province','city','birth','profile','prphone','password'];
        //过滤 多余的字段
        $newdata = where_filter($data,$allowfield) ;
        $usermodel = new Studentinfo;
        $ret = $usermodel->updateStudent($newdata,$teacherid);
        var_dump($ret);
        return $ret ;

	}
	/**
	 * [delStudent 删除学生信息，伪删除]
	 * @Author wyx
	 * @DateTime 2018-04-20T21:15:39+0800
	 * @param    [int]                   $userid  [要删除的学生id]
	 * @param    [int]                   $teacherid [学生id]
	 * @return   [array]                          [操作结果]
	 */
	public function delStudent($userid,$teacherid){
		if($userid>0 && $teacherid>0){
			$usermodel = new StudentInfo;
	        $ret = $usermodel->delStudent($userid,$teacherid);
	        if($ret){
	        	return return_format($ret,0,lang('success')) ;
	        }else{
	        	return return_format('',-1,'删除失败') ;
	        }
		}else{
			return return_format([],-1,'参数不合法') ;
		}
	}

	/**
	 * [delStudent 获取教师添加的学生列表]
	 * @Author wangwy
	 * @DateTime 2018-04-20T21:15:39+0800
	 * @param    [int]                   $userid  [要删除的学生id]
	 * @param    [int]                   $teacherid [教师id]
	 * @return   [array]                          [操作结果]
	 */
	public function tchstuList($data){
	    $stu = new StudentInfo;
	    $tt = $stu->getNicklist($data['teacherid'],$data['organid']);
	    if (empty($tt)) {
			return return_format([],0,lang('success'));
		}else{
			return return_format($tt,0,'数据获取成功');
		}
	}
	/**
	 * [delStudent 重置学生密码]
	 * @Author wangwy
	 * @DateTime 2018-04-20T21:15:39+0800
	 * @param    [int]                   $userid  [要删除的学生id]
	 * @param    [int]                   $teacherid [教师id]
	 * @return   [array]                          [操作结果]
	 */
	public function resetPassword($data){
		 //随机一个六位密码
 		 $params=self::create_rand($length = 6,$type='num');
		 //后台存储密码加密
		 $pass = new \login\Authorize;
		 $password = $pass->createUserMark($params);
		 $allowfield = ['mix','password'];
		 //过滤 多余的字段
		 $newdata = where_filter($password,$allowfield);
         $organ = new Organ;
		 $name = $organ->getOrganname($data['organid']);
		 if (empty($name)) {
		 	  // code...
			  return return_format([],21008,lang('21008'));
		 }
		 // 启动事务
         Db::startTrans();
         try{
			   //随机一个六位密码
				// $params = slef::create_rand($length = 8,$type='num');
			   //将新生成加密后的密码存入后台
			   $stu = new StudentInfo;
			   $tt = $stu->insertPassword($data['mobile'],$password);
				 //存入成功后，将未加密的密码发短信给学生
               $text = new \Messages;
               $shortMeg = $text->sendMeg($data['mobile'],7,[$name['organname'],$params],$data['prphone']);
               // 提交事务
               Db::commit();
               return return_format([],0,lang('success'));
         } catch (\Exception $e) {
               // 回滚事务
               Db::rollback();
               return return_format([],21009,lang('21009'));
         }
    }
	/**
	 * [delStudent 注册学生发送学生密码]
	 * @Author wangwy
	 * @DateTime 2018-04-20T21:15:39+0800
	 * @param    [int]                   $userid  [要删除的学生id]
	 * @param    [int]                   $teacherid [教师id]
	 * @return   [array]                          [操作结果]
	 */
	public function sendPassword($data){
	    //随机一个六位密码
        $params=self::create_rand($length = 6,$type='num');
        //后台存储密码加密
        $pass = new \login\Authorize;
        $password = $pass->createUserMark($params);
        $allowfield = ['mix','password'];
        //过滤 多余的字段
        $newdata = where_filter($password,$allowfield);
        $organ = new Organ;
        $name = $organ->getOrganname($data['organid']);
        if (empty($name)) {
            // code...
			return return_format([],24444,'机构名称查询失败');
        }
        // 启动事务
        Db::startTrans();
        try{
            //将新生成加密后的密码存入后台
            $stu = new StudentInfo;
            $tt = $stu->insertPassword($data['mobile'],$password);
            //存入成功后，将未加密的密码发短信给学生
            $text = new \Messages;
            $shortMeg = $text->sendMeg($data['mobile'],3,[$name['organname'],$params],$data['prphone']);
            // 提交事务
            Db::commit();
            return return_format([],0,'初始密码发送成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return return_format([],-20036,'初始密码发送失败');
        }
    }



		//随机产生六位数密码Begin
    function randStr($len=6,$format='ALL') {
        switch($format) {
        case 'ALL':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; break;
        case 'CHAR':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~'; break;
        case 'NUMBER':
        $chars='0123456789'; break;
        default :
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
        break;
        }
		//microtime 返回当前时间戳的微秒数
        //getmypid getmypid — 获取 PHP 进程的 ID
        mt_srand((double)microtime()*1000000*getmypid());
        $password="";
        while(strlen($password)<$len)
           $password.=substr($chars,(mt_rand()%strlen($chars)),1);
        return $password;
    }
        //随机产生六位数密码End

	/**
     * create_rand随机生成一个字符串
     * @param int $length  字符串的长度
     * @param string $type  类型
     * @return string
     * @author:
     */
    function create_rand($length = 8,$type='all')
    {
        $num = '0123456789';
        $letter = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($type == 'num'){
            $chars = $num;
        }elseif($type=='letter'){
            $chars = $letter;
        }else{
            $chars = $letter.$num;
        }

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;

    }



    /**
    * 二维数组去重
    *
    */
    public function moreArrayunique($arr){
        $temp = [];
        foreach ($arr as $ky => $val) {
       	 $val = implode(',', $val);//降维
       	 $temp[$ky] = $val;
        }
        $temp = array_unique($temp);//去掉重复的字符串
        $temps = [];
        foreach ($temp as $k => $v) {
       	 $arrs = explode(',',$v);
            $temps[$k]['id'] = $arrs[0];
            $temps[$k]['nickname'] = $arrs[1];
            $temps[$k]['sex'] = $arrs[2];
            $temps[$k]['country'] = $arrs[3];
            $temps[$k]['province'] = $arrs[4];
            $temps[$k]['city'] = $arrs[5];
            $temps[$k]['birth'] = $arrs[6];
        }
        return $temps;//将拆开的数组重新组装
    }

}
