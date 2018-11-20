<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件

/**
 * @ jcr 过滤查询条件里的空值
 * @ $inArray 过滤不是查询条件的字段
 */
use phpqrcode\qrcode;
use think\Cache;
function where_filter($arr, $inArray = []) {
	foreach ($arr as $k => $v) {
		if ($v === '' || $v === null) {
			unset($arr[$k]);
		} else if ($inArray && !in_array($k, $inArray)) {
			unset($arr[$k]);
		}
	}
	return $arr ? $arr : [];
}
function is_weixin(){

    if ( strpos($_SERVER['HTTP_USER_AGENT'],

            'MicroMessenger') !== false ) {

        return true;

    }

    return false;

}
/**
 * [return_format 返回数据格式化]
 * @Author
 * @DateTime 2018-04-18T14:29:25+0800
 * @$data 数据
 * @$code 错误码
 * @$info 返回数据描述
 *
 * @return   ['code'=>'0','data'=>'','info'=>'添加成功']
 * 如果返回值多个 比如有分页data值变为 ['data'=>array,'pageinfo'=>['pagesize','pagenum','total']]
 * 错误 0 代表 没有错误
 * pagesize=> 每页多少行
 * pagenum=> 当前第几页
 * total=> 总记录行数
 */
function return_format($data, $code = 0, $info = '') {
	if ($info === '') {
		$info = $code == 0 ? lang('success') : lang(strval($code));
	}
	return ['code' => $code, 'data' => $data, 'info' => $info];
}
/**
 * @ jcr path 字符串拼接
 * $arr
 */
function str_path_join($arr, $strings = '-') {
	$arr = array_filter($arr);
	if (!$arr) {
		return '';
	}

	$str = '';
	foreach ($arr as $k => $v) {
		$str .= $v . $strings;
	}
	return trim($str, '-');
}
/**
 * 把返回的数据集转换成Tree
 * @Author jcr
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @return array
 */
function toTree($list = null, $pk = 'id', $pid = 'pid', $child = '_child') {
	if (null === $list) {
		// 默认直接取查询返回的结果集合
		$list = &$this->dataList;
	}
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$_key = is_object($data) ? $data->$pk : $data[$pk];
			$refer[$_key] = &$list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = is_object($data) ? $data->$pid : $data[$pid];
			$is_exist_pid = false;
			foreach ($refer as $k => $v) {
				if ($parentId == $k) {
					$is_exist_pid = true;
					break;
				}
			}
			if ($is_exist_pid) {
				if (isset($refer[$parentId])) {
					$parent = &$refer[$parentId];
					$parent[$child][] = &$list[$key];
				}
			} else {
				$tree[] = &$list[$key];
			}
		}
	}
	return $tree;
}
/**
 * 将数字转换成中文回显
 * @Author jcr
 * @param array $list 要转换的数字
 * @param string $mode parent标记字段
 * @return array
 */
function numtochr($num, $mode = true) {
	$char = array("零", "一", "二", "三", "四", "五", "六", "七", "八", "九");
	$dw = array("", "十", "百", "千", "", "万", "亿", "兆");
	$dec = "点";
	$retval = "";
	if ($mode) {
		preg_match_all("/^0*(\d*)\.?(\d*)/", $num, $ar);
	} else {
		preg_match_all("/(\d*)\.?(\d*)/", $num, $ar);
	}

	if ($ar[2][0] != "") {
		$retval = $dec . $this->ch_num($ar[2][0], false);
	}
	//如果有小数，先递归处理小数
	if ($ar[1][0] != "") {
		$str = strrev($ar[1][0]);
		for ($i = 0; $i < strlen($str); $i++) {
			$out[$i] = $char[$str[$i]];
			if ($mode) {
				$out[$i] .= $str[$i] != "0" ? $dw[$i % 4] : "";
				if ($str[$i] + $str[$i - 1] == 0) {
					$out[$i] = "";
				}

				if ($i % 4 == 0) {
					$out[$i] .= $dw[4 + floor($i / 4)];
				}

			}
		}
		$retval = join("", array_reverse($out)) . $retval;
	}
	return $retval;
}
/**
 * [return_json 返回json数据]
 * @Author yr
 * @$data 数据
 * @$code 错误码
 * @$info 返回数据描述
 *
 * @return
{
"code": 1,
"data": "",
"info": "密码不正确"
}
 * 错误 0 代表 没有错误
 */
function return_json($data, $code = 0, $info = '') {
	$data = ['code' => $code, 'data' => $data, 'info' => $info];
	$json_data = json_encode($data);
	exit($json_data);
}
/**
 * @php jcr
 * 组装sql 条件或
 * $arr 数据源
 * $key 最终赋值
 * $where 关系
 */
function where_or($arr, $key, $where) {
	if (!$arr) {
		return [];
	}

	$arrs = [];
	if (count($arr) == 1) {
		$arrs[$key] = array($where, array_values($arr)[0]);
	} else {
		foreach ($arr as $k => $value) {
			$inArr[] = array($where, $value);
		}
		$inArr[] = 'or';
		$arrs[$key] = $inArr;
	}
	return $arrs;
}
/**
 * @php jcr
 * 变动二维数组结构 将其id为作为键值去指向
 * $arr 数组
 * $key 对应id字段
 */
function arr_key_value($arr, $key) {
	if (!$arr) {
		return [];
	}

	$arrList = [];
	foreach ($arr as $k => $v) {
		$arrList[$v[$key]] = $v;
	}
	return $arrList;
}
/**
 * @php yr
 * 判断参数是否为正整数
 * $arr 数组
 * $key 对应id字段
 */
function is_intnum($str) {
	if (!is_numeric($str) || $str < 0) {
		return false;
	}
	return true;
}
/**
 * @php 根据开始时间计算应有的时间区间 键
 * @author [JCR] <[email address]>
 */
function array_series($key, $time) {
	$count = ceil($time / 10);
	$arr[] = $key;
	if ($count > 1) {
		for ($i = 1; $i < $count; $i++) {
			$inkey = $key + $i;
			$arr[] = $inkey > 143 ? $inkey - 144 : $inkey;
		}
	}
	return $arr;
}

/**
 * 对比字段修改 JCR
 * @param  [type] $data  [description] 现提交数据
 * @param  [type] $inarr [description] 数据库数据源数据
 * @return [type]        [description]
 */
function thanArr($data,$inarr){
	$arr = [];
	foreach ($data as $key => $value) {
		if($data[$key] != $inarr[$key] ){
			$arr[$key] = $value;
		}
	}
	return $arr;
}

/**
 * @ 根据键值获取对应时间段
 * @param  [type] $key [键值]
 * @return [type]      [description]
 */
function get_time_key($key) {
	$timeKey = config('param.ClassTime');
	return $timeKey[$key];
}

/**
 * 根据时间段获取指定的键
 * @param $val
 * @return mixed
 */
function get_time_key_value($val){
	$timeKey = config('param.ClassTime');
	$key = array_keys($timeKey,$val);
	return $key?$key[0]:false;
}

/**
 * 手动数组分页
 * @param $list		数据源
 * @param $page		第几页
 * @param $limit	一页几条
 */
function pageLimit($list,$page,$limit){
	if(!$list) return $list;
	$count = count($list);
	$start = ($page - 1) * $limit;
	$end = $page * $limit;
	$end = $end > $count ? $count : $end;
	$datalist = [];
	if($start > $end) return $datalist;
	for ($i = $start;$i<$end;$i++){
		$datalist[] = $list[$i];
	}
	return $datalist;
}


/**
 * http 请求POST
 */
function curl_post($url = '', $postdata = '', $options = array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	if (!empty($options)) {
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
/**
 * http 请求POST
 */
function curl_postFile($url = '', $postdata = '', $options = array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600); //接收数据的时间
	if (!empty($options)) {
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
/**
 * 支付寶http 请求POST
 */
function curl_postAli($url = '', $postdata = '', $options = array()) {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_ENCODING, "");
	if (!empty($options)) {
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	//var_dump( curl_error($ch) ); //查看錯誤
	curl_close($ch);
	return ($data);
}
/**
 * 向Rest服务器发送请求
 * @param string $http_type http类型,比如https
 * @param string $method 请求方式，比如POST
 * @param string $url 请求的url
 * @return string $data 请求的数据
 */
function http_req($http_type, $method, $url, $data) {
	$ch = curl_init();
	if (strstr($http_type, 'https')) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	if ($method == 'post') {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	} else {
		$url = $url . '?' . $data;
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100000); //超时时间
	try {
		$ret = curl_exec($ch);
	} catch (Exception $e) {
		curl_close($ch);
		return json_encode(array('ret' => 0, 'msg' => 'failure'));
	}
	curl_close($ch);
	return $ret;
}
/**
 * http 请求GET
 * @param  string $url     [description]
 * @param  array  $options [description]
 * @return [type]          [description]
 */
function curl_get($url = '', $options = array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	if (!empty($options)) {
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
/*@php yr
 *@ 时间戳+8位随机数生成维一订单号
 *
 *@return [type]      [description]
 */
function getOrderNum() {
	date_default_timezone_set("PRC");
	//订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
	$order_id_main = date('YmdHis') . rand(10000000, 99999999);
	//订单号码主体长度
	$order_id_len = strlen($order_id_main);
	$order_id_sum = 0;
	for ($i = 0; $i < $order_id_len; $i++) {
		$order_id_sum += (int) (substr($order_id_main, $i, 1));
	}
	//唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
	$order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
	return $order_id;
}
/**
 * 获取二级域名 如.test.com
 *
 * @staticvar type $domain
 * @return type
 */
function getSecondDomain() {
	static $domain;
	if (empty($domain)) {
		$domain = $_SERVER['HTTP_HOST'];
	}
	$domainArray = explode('.', $domain);
	if (count($domainArray) === 2) {
		//test.com
		$domain = '.' . $domain;
	} elseif (count($domainArray) === 3) {
		//www.test.com
		$domain = $domainArray[0];
	} else {
		//www.test.com.cn
		$domain = substr($domain, strpos($domain, '.'));
	}
	return $domain;
}
/**
 * 递归树查找下级
 *$list 需要递归的数组
 *$pk 主键id
 * @staticvar type $domain
 * @return array
 */
function generateTree($list, $pk = 'tagid', $pid = 'fatherid', $child = 'child', $root = 0) {
	$tree = array();
	$packData = array();
	foreach ($list as $data) {
		$packData[$data[$pk]] = $data;
	}
	foreach ($packData as $key => $val) {
		if ($val[$pid] == $root) {
			//代表跟节点
			$tree[] = &$packData[$key];
		} else {
			//找到其父类
			$packData[$val[$pid]][$child][] = &$packData[$key];
		}
	}
	foreach($tree as $k=>$v){
	    if(empty($tree[$k]['child'])){
            $tree[$k]['child'] = [];
        }else{
	        foreach($tree[$k]['child'] as $key=>$value){
	            if(empty($tree[$k]['child'][$key]['child'])){
                    $tree[$k]['child'][$key]['child'] = [];
                }
            }
        }
    }
	return $tree;
}
/**
 * [object_to_array 对象转成数组]
 * @author [JCR] <[email address]>
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
function object_to_array($obj) {
	$obj = (array) $obj;
	foreach ($obj as $k => $v) {
		if (gettype($v) == 'resource') {
			return;
		}
		if (gettype($v) == 'object' || gettype($v) == 'array') {
			$obj[$k] = (array) object_to_array($v);
		}
	}
	return $obj;
}
/**
 *      把秒数转换为时分秒的格式
 *      @param Int $times 时间，单位 秒
 *      @return String
 */
function secToTime($times) {
	$result = '0';
	if ($times > 0) {
		$hour = floor($times / 3600);
		$minute = floor(($times - 3600 * $hour) / 60);
		$second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
		$result = $hour . '时' . $minute . '分' . $second . '秒';
	}
	return $result;
}
/***
 * 返回中文拼音
 * */
function Pinyin($_String, $_Code = 'UTF8') {
	//GBK页面可改为gb2312，其他随意填写为UTF8
	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" . "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" . "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" . "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" . "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" . "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" . "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" . "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" . "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" . "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" . "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" . "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" . "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" . "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" . "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" . "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
	$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" . "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" . "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" . "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" . "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" . "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" . "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" . "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" . "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" . "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" . "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" . "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" . "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" . "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" . "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" . "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" . "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" . "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" . "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" . "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" . "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" . "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" . "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" . "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" . "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" . "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" . "|-10270|-10262|-10260|-10256|-10254";
	$_TDataKey = explode('|', $_DataKey);
	$_TDataValue = explode('|', $_DataValue);
	$_Data = array_combine($_TDataKey, $_TDataValue);
	arsort($_Data);
	reset($_Data);
	if ($_Code != 'gb2312') {
		$_String = _U2_Utf8_Gb($_String);
	}

	$_Res = '';
	for ($i = 0; $i < strlen($_String); $i++) {
		$_P = ord(substr($_String, $i, 1));
		if ($_P > 160) {
			$_Q = ord(substr($_String, ++$i, 1));
			$_P = $_P * 256 + $_Q - 65536;
		}
		$_Res .= _Pinyin($_P, $_Data);
	}
	return preg_replace("/[^a-z0-9]*/", '', $_Res);
}
function _Pinyin($_Num, $_Data) {
	if ($_Num > 0 && $_Num < 160) {
		return chr($_Num);
	} elseif ($_Num < -20319 || $_Num > -10247) {
		return '';
	} else {
		foreach ($_Data as $k => $v) {
			if ($v <= $_Num) {
				break;
			}

		}
		return $k;
	}
}
function _U2_Utf8_Gb($_C) {
	$_String = '';
	if ($_C < 0x80) {
		$_String .= $_C;
	} elseif ($_C < 0x800) {
		$_String .= chr(0xC0 | $_C >> 6);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x10000) {
		$_String .= chr(0xE0 | $_C >> 12);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x200000) {
		$_String .= chr(0xF0 | $_C >> 18);
		$_String .= chr(0x80 | $_C >> 12 & 0x3F);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	}
	return iconv('UTF-8', 'GB2312', $_String);
}
//检测域名格式
function checkUrl($C_url) {
	$str = "/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
	if (!preg_match($str, $C_url)) {
		return false;
	} else {
		return true;
	}
}
// 过滤字符串中的特殊字符
function strFilter($str) {
	$str = str_replace('`', '', $str);
	$str = str_replace('·', '', $str);
	$str = str_replace('~', '', $str);
	$str = str_replace('!', '', $str);
	$str = str_replace('！', '', $str);
	$str = str_replace('@', '', $str);
	$str = str_replace('#', '', $str);
	$str = str_replace('$', '', $str);
	$str = str_replace('￥', '', $str);
	$str = str_replace('%', '', $str);
	$str = str_replace('^', '', $str);
	$str = str_replace('……', '', $str);
	$str = str_replace('&', '', $str);
	$str = str_replace('*', '', $str);
	$str = str_replace('(', '', $str);
	$str = str_replace(')', '', $str);
	$str = str_replace('（', '', $str);
	$str = str_replace('）', '', $str);
	$str = str_replace('-', '', $str);
	$str = str_replace('_', '', $str);
	$str = str_replace('——', '', $str);
	$str = str_replace('+', '', $str);
	$str = str_replace('=', '', $str);
	$str = str_replace('|', '', $str);
	$str = str_replace('\\', '', $str);
	$str = str_replace('[', '', $str);
	$str = str_replace(']', '', $str);
	$str = str_replace('【', '', $str);
	$str = str_replace('】', '', $str);
	$str = str_replace('{', '', $str);
	$str = str_replace('}', '', $str);
	$str = str_replace(';', '', $str);
	$str = str_replace('；', '', $str);
	$str = str_replace(':', '', $str);
	$str = str_replace('：', '', $str);
	$str = str_replace('\'', '', $str);
	$str = str_replace('"', '', $str);
	$str = str_replace('“', '', $str);
	$str = str_replace('”', '', $str);
	$str = str_replace(',', '', $str);
	$str = str_replace('，', '', $str);
	$str = str_replace('<', '', $str);
	$str = str_replace('>', '', $str);
	$str = str_replace('《', '', $str);
	$str = str_replace('》', '', $str);
	$str = str_replace('.', '', $str);
	$str = str_replace('。', '', $str);
	$str = str_replace('/', '', $str);
	$str = str_replace('、', '', $str);
	$str = str_replace('?', '', $str);
	$str = str_replace('？', '', $str);
	return trim($str);
}
/**
 * [get_initial 获取中文字符串首字母 没有返回空字符串]
 * @author [JCR] <[email address]>
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function get_initial($str) {
	$str = Pinyin(strFilter($str));
	if (!$str) {
		return '';
	}

	$strarr = str_split($str);
	foreach ($strarr as $key => $value) {
		if (preg_match("/^[A-Za-z]/", $value)) {
			return $value;
			break;
		}
	}
	return '';
}
function zeroPad($text, $blocksize) {
	if (strlen($text) % $blocksize === 0) {
		return $text;
	}
	$pad = $blocksize - (strlen($text) % $blocksize);
	return $text . str_repeat(chr(0), $pad);
}

/**
 * [getencrypt mcrypt_encrypt加密方法的替代]
 * @author [yr]
 * @param  [type] $str [description]
 * @param  [type] $key [签名key]
 * @return [type]      [description]
 */
function getencrypt($str, $key) {
	$str = zeroPad($str, 16);
	$str = openssl_encrypt($str, "AES-128-ECB", $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
	$str = bin2hex($str);
	return $str;
}

//zzq 2018-05-15添加
/**
 *  getOrderStatus //返回当前的机构审核状态
 *订单状态 0已下单，10已取消，20已支付，30申请退款，40已退款  50.退款驳回
 * @ zzq  2018-05-03
 * @param int $orderstatus 组织机构审核状态
 * @return string 返回信息  ;
 */
function getOrderStatus($orderstatus) {
	$ret = [0, 1, 10, 20, 30, 40, 50];
	if (!in_array($orderstatus, $ret)) {
		return '';
	}
	$str = '';
	switch ($orderstatus) {
	case 0:
		$str = "已下单";
		break;
	case 10:
		$str = "已取消";
		break;
	case 20:
		$str = "已支付";
		break;
	case 30:
		$str = "申请退款";
		break;
	case 40:
		$str = "已退款";
		break;
	case 50:
		$str = "退款驳回";
		break;
	default:
		$str = "订单状态异常";
		break;
	}
	return $str;
}

/**
 *  getOrderSource //获得下单来源
 *下单渠道1pc 2手机
 * @ zzq  2018-05-03
 * @param int $ordersource 获取下单来源
 * @return string 返回信息  ;
 */
function getOrderSource($ordersource) {
	$ret = [1, 2];
	if (!in_array($ordersource, $ret)) {
		return '';
	}
	$str = '';
	switch ($ordersource) {
	case 1:
		$str = "PC";
		break;
	case 2:
		$str = "手机";
		break;
	default:
		$str = "订单来源未知";
		break;
	}
	return $str;
}

/**
 *  getPayType //获得支付方式
 *支付方式0其他，1余额，2微信，3支付宝，4银联
 * @ zzq  2018-05-03
 * @param int $ordersource 获取下单来源
 * @return string 返回信息  ;
 */
function getOrderPayType($paytype) {
	$ret = [0, 1, 2, 3, 4];
	if (!in_array($paytype, $ret)) {
		return '';
	}
	$str = '';
	switch ($paytype) {
	case 0:
		$str = "其他";
		break;
	case 1:
		$str = "余额";
		break;
	case 2:
		$str = "微信";
		break;
	case 3:
		$str = "支付宝";
		break;
	case 4:
		$str = "银联";
		break;
	default:
		$str = "支付方式未知";
		break;
	}
	return $str;
}

/**
 *  getRechargePayType //获得充值支付方式
 *支付类型 2:微信支付3支付宝4银联
 * @ zzq  2018-05-03
 * @param int $paytype 获取下单来源
 * @return string 返回信息  ;
 */
function getRechargePayType($paytype) {
	$ret = [2, 3, 4];
	if (!in_array($paytype, $ret)) {
		return '';
	}
	$str = '';
	switch ($paytype) {
	case 2:
		$str = "微信";
		break;
	case 3:
		$str = "支付宝";
		break;
	case 4:
		$str = "银联";
		break;
	default:
		$str = "支付方式未知";
		break;
	}
	return $str;
}

/**
 *  getPayStatus //获得支付方式
 * 类型 1下单 2充值 3退款 4提现
 * @ zzq  2018-05-03
 * @param int $payStatus 获取下单来源
 * @return string 返回信息  ;
 */
function getStuPayLogPayStatus($paystatus) {
	$ret = [1, 2, 3, 4];
	if (!in_array($paystatus, $ret)) {
		return '';
	}
	$str = '';
	switch ($paystatus) {
	case 1:
		$str = "下单";
		break;
	case 2:
		$str = "充值";
		break;
	case 3:
		$str = "退款";
		break;
	case 3:
		$str = "提现";
		break;
	default:
		$str = "类型未知";
		break;
	}
	return $str;
}

/**
 *  getPayType //获得支付方式
 *支付方式0其他，1余额，2微信，3支付宝，4银联
 * @ zzq  2018-05-03
 * @param int $ordersource 获取下单来源
 * @return string 返回信息  ;
 */
function getStuPayLogPayType($paytype) {
	$ret = [1, 2, 3, 4];
	if (!in_array($paytype, $ret)) {
		return '';
	}
	$str = '';
	switch ($paytype) {
	case 1:
		$str = "余额";
		break;
	case 2:
		$str = "微信";
		break;
	case 3:
		$str = "支付宝";
		break;
	case 4:
		$str = "银联";
		break;
	default:
		$str = "支付方式未知";
		break;
	}
	return $str;
}

/**
 *  getPayType //获得支付方式
 *支付类型 2:微信支付3支付宝4银联
 * @ zzq  2018-05-03
 * @param int $paytype 获取下单来源
 * @return string 返回信息  ;
 */
function getWithdrawPayType($paytype) {
	$ret = [2, 3, 4];
	if (!in_array($paytype, $ret)) {
		return '';
	}
	$str = '';
	switch ($paytype) {
	case 2:
		$str = "微信";
		break;
	case 3:
		$str = "支付宝";
		break;
	case 4:
		$str = "银联";
		break;
	default:
		$str = "支付方式未知";
		break;
	}
	return $str;
}

/**
 *  getWithdrarStatus //获得提现状态
 *充值状态 0提现中 1提现成功 2提现失败 3处理中
 * @ zzq  2018-05-03
 * @param int $getWithdrarStatus 获取提现状态
 * @return string 返回信息  ;
 */
function getWithdrawStatus($getWithdrawStatus) {
	$ret = [0, 1, 2, 3];
	if (!in_array($getWithdrawStatus, $ret)) {
		return '';
	}
	$str = '';
	switch ($getWithdrawStatus) {
	case 0:
		$str = "提现中";
		break;
	case 1:
		$str = "提现成功";
		break;
	case 2:
		$str = "提现失败";
		break;
	case 3:
		$str = "处理中";
		break;
	default:
		$str = "提现状态未知";
		break;
	}
	return $str;
}

/**
 * [getstring 将对应不规则数组转成一个字符串]
 * @author [JCR] <[email address]>
 * @param  [type] $data [description]
 * @param  [type] $str  [description]
 * @return [type]       [description]
 */
function getstring($data, $str) {
	foreach ($data as $key => $value) {
		if (is_array($value)) {
			if ($value) {
				$str .= $key;
				$str = getstring($value, $str);
			}
		} else {
			if (($value || $value === 0 || $value === '0') && $value !== null && $value !== false && $value !== true) {
				$str .= $key . $value;
			}
		}
	}
	return $str;
}

/**
 * [verifyErrorCodeNum 验证验证码错误次数]
 * @author [yr]
 * @param  [type] $mobile [description]
 * @return [type]       [description]
 */
function verifyErrorCodeNum($mobile) {
	$code_error_num = Cache::get('code_error' . $mobile);
	if ($code_error_num) {
		$code_error = config('param.sms_maxallowed');
		if ($code_error_num >= $code_error) {
			Cache::rm('mobile' . $mobile);
			Cache::rm('code_error' . $mobile);
			return false;
		}
		Cache::set('code_error' . $mobile, $code_error_num + 1);
		return true;
	} else {
		$num = 1;
		Cache::set('code_error' . $mobile, $num);
		return true;
	}
}

/**
 * [verifyErrorCodeNumByOfficial 注册机构去验证验证码错误次数]
 * @author [yr]
 * @param  [type] $mobile [description]
 * @return [type]       [description]
 */
function verifyErrorCodeNumByOfficial($mobile, $identifytype) {
	$code_error_num = Cache::get('code_error' . $mobile . $identifytype);
	if ($code_error_num) {
		$code_error = config('param.sms_maxallowed');
		if ($code_error_num >= $code_error) {
			Cache::rm('mobile' . $mobile . $identifytype);
			Cache::rm('code_error' . $mobile . $identifytype);
			return false;
		}
		Cache::set('code_error' . $mobile . $identifytype, $code_error_num + 1);
		return true;
	} else {
		$num = 1;
		Cache::set('code_error' . $mobile . $identifytype, $num);
		return true;
	}
}
/**
 * [getRandNickname 随机生成用户昵称]
 * @author [yr]
 * @param  [type] $mobile [description]
 * @return [type]       [description]
 */
function getRandNickname($id, $presuffix = 'nm_') {
	$sn = date('Y', time());
	$sn = substr($sn, -2);
	$sn .= date('m', time());
	$sn .= sprintf("%04d", $id);
	$str = $presuffix . $sn;
	return $str;
}

/**
 * [checkUserMark description]
 * @Author wyx
 * @DateTime 2018-04-27T16:35:02+0800
 * @param    [string]                 $pass [用户提交密码]
 * @param    [string]                 $mix  [description]
 * @param    [type]                   $sign [description]
 * @return   [bool]                         [true 代表成功，false 代表失败]
 */
function checkUserMark($pass, $mix, $sign) {
	$md5str = md5(md5($pass) . $mix);

	for ($i = 0; $i < 5; $i++) {
		$md5str = md5($md5str);
	}
	if ($sign == $md5str) {
		return true;
	} else {
		return false;
	}
}

/**
 * [tokenStr 生成token]
 * @param  [type] $type [description]
 * @param  [type] $uid  [description]
 * @return [type]       [description]
 */
function token_str($type, $uid) {
	//token的目标长度
	$countlen = 15;
	$length = strlen($uid);

	// uid 字符长度 补全字符串长度2
	$struid = str_pad($length, 2, "0", STR_PAD_LEFT);

	// 生成随机数字长度
	$len = $countlen - strlen($type . $uid . $struid) - 2;
	// 随机数字1
	$lenstr = $len > 0 ? mt_rand((int) str_pad('1', $len, '0'), (int) str_pad('', $len, '9', STR_PAD_LEFT)) : '';
	// 随机数字2
	$inlenstr = mt_rand(10, 99);
	return $type . $lenstr . $inlenstr . $uid . $struid;
}

/**
 * [getTokenKey 根据前端传输的token 取出真正的redis token]
 * @param  [type] $token [description]
 * @return [type]        [description]
 */
function getTokenKey($token) {
	$length = substr($token, -2, 2);
	return substr($token, 0, 1) . '-' . substr($token, -(int) $length - 2, -2);
}
/**
 * [get_base64_qrcode 使用PHPqrcode生成带参数二维码的流
 * @param  [url] 参数url
 * @return [type]        [description]
 */
function get_base64_qrcode($url) {
	$errorCorrectionLevel = 'L'; //容错级别
	$matrixPointSize = 6; //生成图片大小
	$object = new QRcode();
	ob_start();
	$object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
	$imageString = ob_get_contents();
	ob_end_clean();
	$image_data_base64 = 'data:image/png;base64,' . base64_encode($imageString);
	return $image_data_base64;
}

/**
2  * 方法一: 获取指定日期段内每一天的日期
3  * @date 2017-02-23 14:50:29
4  *
5  * @param $startdate
6  * @param $enddate
7  *
8  * @return array
9  */
function getDateRange($startdate) {
	$stime = $startdate;
	$etime = $stime + 86400 * 6;
	$datearr = [];
	while ($stime <= $etime) {
		$datearr[] = date('Y-m-d', $stime); //得到dataarr的日期数组。
		$stime = $stime + 86400;
	}
	return $datearr;

}

/**
 * @param $list   排序数组
 * @param $field  array('parent' => SORT_ASC,'value' => SORT_DESC)
 * @return mixed
 */
function sortByCols($list, $field) {
	$sort_arr = array();
	$sort_rule = '';
	foreach ($field as $sort_field => $sort_way) {
		foreach ($list as $key => $val) {
			$sort_arr[$sort_field][$key] = $val[$sort_field];
		}
		$sort_rule .= '$sort_arr["' . $sort_field . '"],' . $sort_way . ',';
	}
	if (empty($sort_arr) || empty($sort_rule)) {return $list;}
	eval('array_multisort(' . $sort_rule . ' $list);');
	return $list;
}
/**
 * @param $password   正则验证密码
 * @return int  0 错误 1true
 */
function verifyPassword($pass) {
	$result = preg_match('/^[_0-9a-z]{6,16}$/i', $pass);
	return $result;
}
/**
 * @param $domain   填写域名测试
 * @return int  0 错误 1true
 */
function verifyDomain($domain) {
	$result = preg_match('/^[0-9a-zA-Z]*$/i', $domain);
	return $result;
}

/**
 * 得到连续签到次数(app和微网站专用)
 * @param $ar 签到日期数组
 * @return int
 */
function get_consecutive_count($ar){
	if(empty($ar)) return 0;
	$c = 1;
	for($i=0;$i<count($ar)-1;$i++){
		if($ar[$i+1] == date("Y-m-d", strtotime("$ar[$i] -1day")))
			$c++;
		else
			break;
	}
	return $c;
}
/**
 * 正则验证邮政编码
 * @param $code 邮政编码
 * @return int
 */
function is_postcode($str)
{
    if($str == ''){
        return false;
    }
    return (preg_match("/^[0-9]d{5}$/",$str))?true:false;
}
/**
 * [get_base64_img 生成流文件
 * @param  [url] 参数url
 * @return [type]        [description]
 */
function get_base64_img($url) {
    $result = file_get_contents($url);
    $image_data_base64 = 'data:image/png;base64,' . base64_encode($result);;
    return $image_data_base64;
}

