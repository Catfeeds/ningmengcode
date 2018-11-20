<?php
/**
 * 对接网课端 对接业务逻辑
 */
namespace app\admin\business;
use app\admin\model\Classroom;
use app\admin\model\Dockinglog;
use app\admin\model\Filemanage;
use app\admin\model\Organ;
use app\admin\model\Organconfig;
use app\admin\model\Toteachtime;
use app\admin\model\Scheduling;
use think\Log;

class Docking {
//	protected $key  =  'LNIWjlgmvqwbt4hy';
//	protected $WKURL = 'http://demo.talk-cloud.net';
	protected $key = 'crmf32aQ5Qr1MXpC';
	protected $WKURL = 'http://global.talk-cloud.net';

	//生成不唯一的随机数
	public function arrlist($arr) {
		$str = rand(100000, 999999);
		if (!in_array($str, $arr)) {
			return $str;
		} else {
			// 回调
			return $this->arrlist($arr);
		}
	}



	//预约课堂
	public function addClassRoom() {
		$toteach = new Toteachtime();
		// 查询对应当天的课时时间
		$datalist = $toteach->getTimeList();
		if($datalist){
		    foreach ($datalist as $k => $v) {
				// 计算 开始时间 5分钟内执行
				$times = strtotime($v['intime'].' '.get_time_key(explode(',',$v['timekey'])[0]))-1800;
				if(time()>=$times){
					$this->operateRoomInfo($v,$toteach);
				}
		    }
		}
	}

	/**
	 * [operateRoomInfo 开教室]
	 * @param  [type] $v       [description]
	 * @param  [type] $toteach [description]
	 * @return [type]          [description]
	 */
	public function operateRoomInfo($v, $toteach) {
		if($v['classstatus']==6){
			// 数据已过期不做任何处理
			return false;
		}else if($v['realnum']==0&&$v['type']!=1){
			// 大班课小班课无人购买
			$sch = new Scheduling();
			$sch->automateEdit(['classstatus'=>6,'id'=>$v['schedulingid'],],$toteach);
			return false;
		}

		$region = new Organ();



		$classroom = new Classroom();

		$data['starttime'] = strtotime($v['intime'] . ' ' . get_time_key(explode(',', $v['timekey'])[0]));
		//结束时间
		$data['endtime'] = $data['starttime'] + $v['classhour'] * 60;
		$data['roomname'] = $v['periodname'];
		$data['roomtype'] = $v['type']==1?0:3; // 0： 1对1   3： 1对多
		$data['key'] = $region->getOrganid()['roomkey'];
		$data['videotype'] = 2; //视频分辨率 0:176*144 1:320*240 2：640*480 3:1280*720 4:1920*1080
		$data['videoframerate'] = 10; //视频帧数 10,15,20,25,30
		$data['autoopenav'] = 0;
		$data['passwordrequired'] = 1; //学生进去教室是否需要密码 0否 1是
		$data['chairmanpwd'] = $this->arrlist($data); //老师密码
		$data['assistantpwd'] = $this->arrlist($data); //主教密码
		$data['patrolpwd'] = $this->arrlist($data); //巡课密码
		$data['confuserpwd'] = $this->arrlist($data); //学生密码

		$infos = curl_post($this->WKURL . '/WebAPI/roomcreate', $data);
		$infos = json_decode($infos, true);
		// var_dump($infos);
		if ($infos['result'] == 0) {
			$data['addtime'] = time(); // 添加时间
			$data['shuttime'] = $data['endtime']; // 添加时间
			$data['classroomno'] = $infos['serial']; // 教室号
			$data['toteachtimeid'] = $v['id']; // 课时表id
			// 教室号等返回数据注入

			$classinfo = $classroom->addRoom($data);

			if ($classinfo) {
				//关联对应的课时 课件
				// 获取对应的资源关联id
				$files = new Filemanage();
				$listid = $files->getIdIn(implode(',', explode('-', $v['courseware'])));
				if($listid){
					$this->relateCourse($data['key'], $data['classroomno'], array_column($listid, 'relateid'));
				}
			}

			// 更新对应的教室已开教室
			$toteach->editId(['status' => 1, 'id' => $v['id']]);

			// 此处只有在课时就一节课的情况下执行
//			if($v['type']!=1&&$v['periodnum']==1){
				// 在只有一节课的情况下 不为一对一 去更新对应的教室状态
				$sch = new Scheduling();
				$indata = ['toteachtimeid' => $v['id'],
							'id'           => $v['schedulingid'],
							'classstatus'  => 4,
							'type'         => $v['type'],
							'periodnum'	   => $v['periodnum']
						  ];
				// 更新开课状态
				$sch->automateEdit($indata,$toteach);
//			}

		} else {
			// 记录异常
			$dockinglog = new Dockinglog();
			$datalog = ['dockingurl' => $this->WKURL . '/WebAPI/roomcreate',
				'code' => $infos['result'],
				'content' => json_encode($data)];
			$dockinglog->addEdit($datalog);
		}
	}


	/**
	 * [relateCourse 教室课件关联]
	 * @return [type] [description]
	 */
	public function relateCourse($key, $serial, $fileid) {
		if(!$fileid){
			return false;
		}
		$region = new Organ();
		$data['key'] = $key ? $key : $region->getOrganid()['roomkey'];
		$data['serial'] = $serial;
		$data['fileidarr'] = $fileid;
		$infos = curl_post($this->WKURL . '/WebAPI/roombindfile', $data);
		$infos = json_decode($infos, true);
		// var_dump($infos);
		if ($infos['result'] != 0) {
			// 记录异常
			$dockinglog = new Dockinglog();
			$datalog = ['dockingurl' => $this->WKURL . '/WebAPI/roombindfile',
				'code' => $infos['result'],
				'content' => json_encode($data)];
			$dockinglog->addEdit($datalog);
		} else {
			return true;
		}
	}
	
	/**
	 * [unrelateCourse 教室取消课件关联]
	 * @return [type] [description]
	 */
	public function unrelateCourse($key, $serial, $fileid) {
		if(!$fileid){
			return false;
		}
		$region = new Organ();
		$data['key'] = $key ? $key : $region->getOrganid()['roomkey'];
		$data['serial'] = $serial;
		$data['fileidarr'] = $fileid;
		$infos = curl_post($this->WKURL . '/WebAPI/roomdeletefile', $data);
		$infos = json_decode($infos, true);
		// var_dump($infos);
		if ($infos['result'] != 0) {
			// 记录异常
			$dockinglog = new Dockinglog();
			$datalog = ['dockingurl' => $this->WKURL . '/WebAPI/roomdeletefile',
				'code' => $infos['result'],
				'content' => json_encode($data)];
			$dockinglog->addEdit($datalog);
		} else {
			return true;
		}
	}

	/**
	 * [getRoomRecord 根据上课的toteachtime id 获取对应的教师录制列表]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getRoomRecord($serial) {

		// 获取对应的房间号
		$classroom = new Classroom();
		$region = new Organ();
		$data['key'] = $region->getOrganid()['roomkey'];
		//$data['serial'] = 754611620;
		$data['serial'] = $serial;

		$infos = curl_post($this->WKURL . '/WebAPI/getrecordlist', $data);
		$infos = json_decode($infos, true);

		if ($infos['result'] != 0) {
			//记录异常
			$dockinglog = new Dockinglog();
			$datalog = [
				'dockingurl' => $this->WKURL . '/WebAPI/getrecordlist',
				'code' => $infos['result'],
				'content' => json_encode($data)
			];
			$dockinglog->addEdit($datalog);
			return false;
		} else {
			return $infos['recordlist'];
		}
	}

	/**
	 * [deleteRelate 上传文件]
	 * @return [type] [description]
	 */
	public function uploadFiles($url = "", $showname = "-", $fatherid = 1, $teacherid=0, $usetype = 2,$inurl = '') {
		if(!$url){
			return false;
		}
		
		$filemanage = new Filemanage();
		$filedata['filetype'] = $filemanage->getById($fatherid)['filetype'];
		$filedata['sizes'] = round(filesize($url) / 1024 / 1024, 2);
		$filedata['fatherid'] = $fatherid;
		$filedata['showname'] = $showname;
		$filedata['teacherid'] = $teacherid;
		$filedata['cosurl'] = $inurl;
		if($usetype == 1){ //录制件
			$filedata['fileurl'] = $url;
			$filedata['usetype'] = 1;
		}else{
			$region = new Organ();
			$file = new \CURLFile(realpath($url));

			$data['filedata'] = $file;

			//log::write(.'上传参数','log',TRUE);

			$data['key'] = $region->getOrganid()['roomkey'];
			//$data['key'] = 'LNIWjlgmvqwbt4hy';
			$urlarr = explode('.',$url);
			$urlper = $urlarr[count($urlarr)-1];
			if($urlper=='ppt'||$urlper=='pptx'){
				$data['dynamicppt'] = 1; // 1转换
			}else{
				$data['dynamicppt'] = 0; // 1转换
			}

			$data['isopen'] = 1; //0非公开 1公开
			$data['conversion'] = 1;  // 此处必填1 要么会导致网课那边

			$infos = curl_postFile($this->WKURL . '/WebAPI/uploadfile', $data);
			$infos = json_decode($infos, true);
			// dump(filesize($url));
			// dump($infos);
			if ($infos['result'] != 0) {
				//记录异常
				$dockinglog = new Dockinglog();
				$datalog = [
					'dockingurl' => $this->WKURL . '/WebAPI/uploadfile',
					'code' => $infos['result'],
					'content' => json_encode($data)
				];
				$dockinglog->addEdit($datalog);
				return false;
			} else {
				//上传成功
				$filedata['relateid'] = $infos['fileid']; //上传文件回调id
				$filedata['fileurl'] = $infos['downloadpath'];
				$filedata['usetype'] = 2;
			}
		}
		
		$upInfo = $filemanage->addFile($filedata);
		if($upInfo['code']==0){
			return TRUE;
		}else{
			return false;
		}
	}





	/**
	 * [deleteRelate 上传文件 - 折中]
	 * @return [type] [description]
	 * ./static/code.txt
	 * $usetype 用途 1 录制件 2 普通课件
	 */
	public function uploadToFiles($url = "",$showname = "", $fatherid = 1, $teacherid=0,$usetype = 2,$inurl = '') {
		if(!$url) return false;
		$filemanage = new Filemanage();
		$filedata['filetype'] = $filemanage->getById($fatherid)['filetype'];
		$filedata['sizes'] = round(filesize($url) / 1024 / 1024, 2);
		$filedata['fatherid'] = $fatherid;
		$filedata['showname'] = $showname;
		$filedata['teacherid'] = $teacherid;
		$filedata['fileurl'] = $url;
		$filedata['usetype'] = $usetype;
		$filedata['cosurl'] = $inurl;
		$upInfo = $filemanage->addFile($filedata);
		if($upInfo['code']==0){
			return TRUE;
		}else{
			return false;
		}
	}

	/**
	 * 定时任务给拓课传文件
	 */
	public function updateTk(){
		$filemanage = new Filemanage();
		$list = $filemanage->getListAll();
		$WKURLS = 'http://global.talk-cloud.net';
		if($list){
			$dockinglog = new Dockinglog();
			$region = new Organ();
			foreach ($list as $k => $v){
                // 命令行执行文件和入口文件执行地址不一样导致取不到文件 做下兼容
                $urls = './public'.trim($v['fileurl'],'.');
				if(file_exists($urls)){
					$file = new \CURLFile(realpath($urls));
					$data['filedata'] = $file;
					$data['key'] = $region->getOrganid()['roomkey'];
					$urlarr = explode('.',$v['fileurl']);
					$urlper = $urlarr[count($urlarr)-1];

					if($urlper=='ppt'||$urlper=='pptx'){
						$data['dynamicppt'] = 1; // 1转换
					}else{
						$data['dynamicppt'] = 0; // 1转换
					}

					$data['isopen'] = 1; //0非公开 1公开
					$data['conversion'] = 1;  // 此处必填1 要么会导致网课那边

					$infos = curl_postFile($WKURLS . '/WebAPI/uploadfile', $data);
					$infos = json_decode($infos, true);
					if ($infos['result'] != 0) {
						//记录异常
						$datalog = ['dockingurl' => $WKURLS . '/WebAPI/uploadfile',
							'code' => $infos['result'],
							'content' => json_encode($data)];
						$dockinglog->addEdit($datalog);
						// 上传只走一遍 失败了不再进行上传操作
						$filemanage->addFile(['fileid'=>$v['fileid'],'relateid'=>0]);
					}else{
                        is_file($v['fileurl']) && unlink($urls);
						// 上传成功 回掉 更新对应的文件回掉ID和地址
						$filemanage->addFile(['fileid'=>$v['fileid'],'relateid'=>$infos['fileid'],'fileurl'=>$infos['downloadpath']]);

					}
				}
			}
		}
	}



	/**
	 * [deleteRelate 上传文件测试专用]
	 * @return [type] [description]
	 */
	public function ceshiUploadFiles($url = "./static/ssss.mp3", $showname = "-", $fatherid = 1, $teacherid=0) {
		$key  =  'LNIWjlgmvqwbt4hy';
		$WKURL = 'http://demo.talk-cloud.net';
		$region = new Organ();
		$filemanage = new Filemanage();
		$file = new \CURLFile(realpath($url));
		$data['filedata'] = $file;

//		$data['key'] = $region->getOrganid()['roomkey'];
		$data['key'] = $key;
		$urlarr = explode('.',$url);
		$urlper = $urlarr[count($urlarr)-1];
		if($urlper=='ppt'||$urlper=='pptx'){
			$data['dynamicppt'] = 1; // 1转换
		}else{
			$data['dynamicppt'] = 0; // 1转换
		}

		dump($data);

		$data['isopen'] = 1; //0非公开 1公开
		$data['conversion'] = 1;

		$infos = curl_postFile($WKURL . '/WebAPI/uploadfile', $data);
		dump($infos);
		$infos = json_decode($infos, true);
		// dump(filesize($url));
		 dump($infos);
		if ($infos['result'] != 0) {
			//记录异常
			$dockinglog = new Dockinglog();
			$datalog = ['dockingurl' => $WKURL . '/WebAPI/uploadfile',
				'code' => $infos['result'],
				'content' => json_encode($data)];
			$dockinglog->addEdit($datalog);
			return false;
		} else {
			//上传成功
			$filedata['relateid'] = $infos['fileid']; //上传文件回调id
			$filedata['sizes'] = round(filesize($url) / 1024 / 1024, 2);
			$filedata['fileurl'] = $infos['downloadpath'];
			$filedata['fatherid'] = $fatherid;
			$filedata['showname'] = $showname;
			$filedata['fileurl'] = $infos['downloadpath'];
			$filedata['teacherid'] = $teacherid;
			// var_dump($filedata);
			return $filemanage->addFile($filedata);
		}
	}



}

?>