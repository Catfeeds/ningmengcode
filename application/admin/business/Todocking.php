<?php
/**
 * 对接网课端 对接业务逻辑
 */
namespace app\admin\business;
use app\admin\model\Classroom;
use app\admin\model\Playback;
use app\admin\model\Toteachtime;

class Todocking {

	/**
	 * setRoomRecord 课程上完 查询课程视频存储起来
	 * @param $data
	 * @return array
	 */
	public function setRoomRecord($data) {
		// G根据 房间号获取对应的时间id
		$classroom = new Classroom();
		$room = $classroom->getRoomInfo($data['serial']);
		if (!$room) {
			return return_format('', 0, '传输房间不存在');
		}

		$docking = new Docking();
		$list = $docking->getRoomRecord($data['serial']);
		if ($list) {
			$inarr = [];
			$Playback = new Playback();
			$playList = $Playback->getList($data['serial']);
			$arrPlayIn = $playList ? array_column($playList, 'playpath') : [];

			foreach ($list as $k => $v) {
				if (!in_array($v['playpath'], $arrPlayIn)) {
					$infoArr = [
						'playpath' => $v['playpath'] ? $v['playpath'] : '',
						'https_playpath' => $v['https_playpath'] ? $v['https_playpath'] : '',
						'serial' => $v['serial'] ? $v['serial'] : '',
						'toteachid' => $room['toteachtimeid'] ? $room['toteachtimeid'] : 0,
						'duration' => $v['duration'] ? $v['duration'] : 0,
						'starttime' => $v['starttime'] ? $v['starttime'] : 0,
					];
					$inarr[] = $infoArr;
				}
			}

			// log::write('录制件录制数据'.json_encode($inarr),'log',TRUE);
			if ($inarr) {
				$info = $Playback->addEdit($inarr, $room['id']);
				if ($info) {
					return return_format('', 0, '插入成功');
				}
			}
			return return_format('', 10095, '插入失败');
		} else {
			return return_format('', 10096, '未检测到教室');
		}
	}

	/**
	 * setDownRoom 课程上完 关闭课程
	 * @param $serial
	 */
	public function setDownRoom($serial) {
		$classroom = new Classroom();
		$room = $classroom->getRoomInfoId($serial);
		if (!$room) {
			return return_format('', 0, '传输房间不存在');
		}

		$toteachtime = new Toteachtime();
		if ($toteachtime->editToteach($room['toteachtimeid'])) {
			return return_format('', 0, '操作成功');
		} else {
			return return_format('', 10221, '操作异常');
		}
	}

}

?>