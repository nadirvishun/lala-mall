<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');

/*
 * $printer_type 打印机类型 (1:飞蛾, 2:飞印, 3:365)
 * $deviceno 机器号
 * $key 密钥
 * $member_code 商户编号(只有飞印打印机有)
 * $content 打印机内容
 * $orderindex 订单编号(只有飞印打印机有)
*/
function print_add_order($printer_type, $deviceno, $key, $member_code, $api_key, $content, $times = 1, $orderindex = 0) {
	if($printer_type == 'feie') {
		$postdata = array(
			'sn' => $deviceno,
			'key' => $key,
			'printContent' => implode('<BR>', $content),
			'times' => $times,
		);
		$http = print_feie_url($deviceno);
		$posturl = $http . '/FeieServer/printOrderAction';
	} elseif($printer_type == 'feiyin') {
		$qrcode = str_replace(array("<QR>", "</QR>"), array('', ''), $content['qrcode']);
		//$content['qrcode'] = "<QRcode# size=8>{$qrcode}</QRcode#>";

		$content = implode("\n", $content);
		$content = str_replace(array("<CB>", "</CB>"), array('', ''), $content);
		$content = str_replace(array("<L>", "</L>"), array('', ''), $content);
		$postdata = array(
			'memberCode' => $member_code,
			'deviceNo' => $deviceno,
			'reqTime' => number_format(1000*time(), 0, '', ''),
			'msgDetail' => $content,
			'mode' => 2,
			'msgNo' => $orderindex,
		);
		$securityCode = $member_code . $content . $deviceno . $orderindex . $postdata['reqTime'] . $key;
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://my.feyin.net:80/api/sendMsg';
	} elseif($printer_type == 'AiPrint') {
		unset($content['qrcode']);
		$content = implode("\n", $content);
		$content = str_replace(array("<CB>", "</CB>"), array('', ''), $content);
		$content = str_replace(array("<L>", "</L>"), array('', ''), $content);
		$postdata = array(
			'memberCode' => $member_code,
			'deviceNo' => $deviceno,
			'reqTime' => number_format(1000*time(), 0, '', ''),
			'msgDetail' => $content,
			'mode' => 2,
			'msgNo' => $orderindex,
		);
		$securityCode = $member_code . $content . $deviceno . $orderindex . $postdata['reqTime'] . $key;
		$postdata['securityCode'] = md5($securityCode);
		$posturl = 'http://iprint.ieeoo.com/porderPrint';
	} elseif($printer_type == '365') {
		if(substr($deviceno, 0, 4) == 'kdt1') {
			//gprs打印机
			$qrcode = str_replace(array("<QR>", "</QR>"), array('', ''), $content['qrcode']);
			$qrlength = chr(strlen($qrcode));
			$content['qrcode'] = "^Q{$qrlength}{$qrcode}";
			array_unshift($content, "^N{$times}^F1");
			$content = str_replace(array("<CB>", "</CB>"), array('^H2', ''), $content);
		}
		$content = implode("\n", $content);
		$content = str_replace(array("<L>", "</L>"), array('', ''), $content);
		$postdata = array(
			'deviceNo' => $deviceno,
			'key' => $key,
			'printContent' => $content,
			'times' => $times
		);

		$posturl = 'http://open.printcenter.cn:8080/addOrder';
	} elseif($printer_type == 'yilianyun') {
		array_unshift($content, "**{$times}");
		$content['title'] = str_replace(array("<CB>", "</CB>"), array("<center>", "</center>"), $content['title']);
		$content = implode("\n", $content);
		$content = str_replace(array("<QR>", "</QR>"), array('<q>', '</q>'), $content);
		$content = str_replace(array("<L>", "</L>"), array('', ''), $content);
		$time = time();
		$sign = strtoupper(md5("{$api_key}machine_code{$deviceno}partner{$member_code}time{$time}{$key}"));
		$postdata = array(
			'partner' => $member_code,
			'machine_code' => $deviceno,
			'sign' => $sign,
			'content' => $content,
			'time' => $time
		);
		$postdata = http_build_query($postdata);
		$posturl = 'http://open.10ss.net:8888';
	} elseif($printer_type == 'qiyun') {
		$content = str_replace(array("<CB>", "</CB>"), array("", ""), $content);
		$content = str_replace(array("<L>", "</L>"), array('', ''), $content);
		$content = str_replace(array("<N>", "</N>"), array('', ''), $content);
		$content = implode("\r\n", $content) . "\r\n\r\n\r\n\r\n\r\n";
		$time = time();
		$sign = strtoupper(md5("{$api_key}machine_code{$deviceno}partner{$member_code}time{$time}{$key}"));
		$postdata = array(
			'partner'=> $member_code,
			'machine_code'=> $deviceno,
			'sign' => $sign,
			'content' => $content,
			'time'=> $time,
		);
		$postdata = http_build_query($postdata);
		$posturl = 'http://openapi.qiyunkuailian.com';
	}

	if($printer_type == 'feiyin' || $printer_type == 'AiPrint') {
		$response = ihttp_post($posturl, $postdata);
		if(is_error($response)) {
			return error(-1, "错误: {$response['message']}");
		}
		$result['responseCode'] = intval($response['content']);
		$result['orderindex'] = $orderindex;
		if($result['responseCode'] == 0) {
			return $result['orderindex'];
		} else {
			$errors = print_code_msg();
			return error(-1, $errors[$printer_type]['printorder'][$result['responseCode']]);
		}
	} else {
		$response = ihttp_post($posturl, $postdata);
		if(is_error($response)) {
			return error(-1, "错误: {$response['message']}");
		}
		if(in_array($printer_type, array('feie', '365'))) {
			$result = @json_decode($response['content'], true);
		} elseif($printer_type == 'qiyun'){
			$result = @json_decode($response['content'], true);
			if($result['code'] == 200) {
				$result['responseCode'] = 0;
			} else {
				$result['responseCode'] = $result['code'];
				$result['responseMsg'] = $result['msg'];
			}
		} elseif($printer_type == 'yilianyun'){
			$result = @json_decode($response['content'], true);
			if($result['state'] == 1) {
				$result['responseCode'] = 0;
				$result['orderindex'] = $result['id'];
			} else {
				$result['responseCode'] = $result['state'];
			}
		} else {
			$result['responseCode'] = intval($response['content']);
			$result['orderindex'] = $orderindex;
		}

		if($result['responseCode'] == 0 || ($printer_type == '365' && $result['responseCode'] == 1)) {
			return $result['orderindex'];
		} else {
			if(!empty($result['responseMsg'])) {
				return error(-1, $result['responseMsg']);
			}
			$errors = print_code_msg();
			return error(-1, $errors[$printer_type]['printorder'][$result['responseCode']]);
		}
	}
}

/*
 * $printer_type 打印机类型
 * $deviceno 机器号或者商户编号(飞印独有)
 * $key 密钥
 * $orderindex 订单id(打印时候第三方打印软件返回的)
*/
function print_query_order_status($printer_type, $deviceno, $key, $member_code, $orderindex) {
	if($printer_type == 'feie') {
		$postdata = array(
			'sn' => $deviceno,
			'key' => $key,
			'index' => $orderindex
		);
		$http = print_feie_url($deviceno);
		$posturl = $http . '/FeieServer/queryOrderStateAction';
		$response = ihttp_post($posturl, $postdata);
	} elseif($printer_type == 'feiyin') {
		$postdata = array(
			'memberCode' => $member_code,
			'key' => $key,
			'msgNo' => $orderindex,
			'reqTime' => number_format(1000*time(), 0, '', ''),
		);
		$securityCode = $member_code . $postdata['reqTime'] . $key . $orderindex;
		$postdata['securityCode'] = md5($securityCode);

		$posturl = 'http://my.feyin.net/api/queryState?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	} elseif($printer_type == 'AiPrint') {
		$postdata = array(
			'memberCode' => $member_code,
			'msgNo' => $orderindex,
			'reqTime' => number_format(1000*time(), 0, '', ''),
		);
		$securityCode = $member_code . $postdata['reqTime'] . $key . $orderindex;
		$postdata['securityCode'] = md5($securityCode);

		$posturl = 'http://iprint.ieeoo.com/porderqueryState?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	} elseif($printer_type == '365') {
		$postdata = array(
			'deviceNo' => $deviceno,
			'key' => $key,
			'orderindex' => $orderindex,
		);

		$posturl = 'http://open.printcenter.cn:8080/queryOrder';
		$response = ihttp_post($posturl, $postdata);
	}
	if(is_error($response)) {
		return error(-1, "错误: {$response['message']}");
	}
	if(in_array($printer_type, array('feie', '365'))) {
		$result = @json_decode($response['content'], true);
	} else {
		$result['responseCode'] = intval($response['content']);
	}
	$status = 2;
	if(in_array($printer_type, array('feie', '365'))) {
		if($result['responseCode'] == 0) {
			if($printer_type == 'feie') {
				$status = ($result['msg'] == '已打印' ? 1 : 2);
			} else {
				$status = 1;
			}
		}
	} else {
		if($result['responseCode'] == 1) {
			$status = 1;
		}
	}
	return $status;
}

function print_query_printer_status($printer_type, $deviceno, $key, $member_code) {
	if($printer_type == 'feie') {
		$postdata = array(
			'sn' => $deviceno,
			'key' => $key,
		);
		$http = print_feie_url($deviceno);
		$posturl = $http . '/FeieServer/queryPrinterStatusAction';
		$response = ihttp_post($posturl, $postdata);
	} elseif($printer_type == 'feiyin') {
		$postdata = array(
			'memberCode' => $member_code,
			'reqTime' => number_format(1000*time(), 0, '', ''),
		);
		$securityCode = $member_code . $postdata['reqTime'] . $key;
		$postdata['securityCode'] = md5($securityCode);

		$posturl = 'http://my.feyin.net/api/listDevice?' . http_build_query($postdata);
		$response = ihttp_get($posturl);
	} elseif($printer_type == '365') {
		$postdata = array(
			'deviceNo' => $deviceno,
			'key' => $key,
		);
		$posturl = 'http://open.printcenter.cn:8080/queryPrinterStatus';
		$response = ihttp_post($posturl, $postdata);
	}

	if(is_error($response)) {
		return error(-1, "错误: {$response['message']}");
	}
	if(in_array($printer_type, array('feie', '365'))) {
		$result = @json_decode($response['content'], true);
	} else {
		$result = intval($response['content']);
		if(is_numeric($result) && $result < 0) {
			$errors = print_code_msg();
			return $errors[$printer_type]['qureystate'][$result];

		} else {
			$result = isimplexml_load_string($response['content']);
			$result = json_decode(json_encode($result), true);
			return $result['device']['deviceStatus'] . ',纸张状态:' . $result['device']['paperStatus'];
		}
	}
	$errors = print_code_msg();
	if($printer_type == 'feiyin' || $printer_type == '365') {
		return $errors[$printer_type]['qureystate'][$result['responseCode']];
	} else {
		return $result['msg'];
	}
}

function print_code_msg() {
	$data = array(
		'feie' => array(
			'printorder' => array(
				0 => '服务器接收订单成功',
				1 => '打印机编号错误',
				2 => '服务器处理订单失败',
				3 => '打印内容太长',
				4 => '请求参数错误'
			),
			'qureyorder' => array(
				0 => '已打印/未打印',
				1 => '请求参数错误',
				2 => '服务器处理订单失败',
				3 => '没有找到该索引的订单',
			),
			'qureystate' => array(

			),
		),

		'feiyin' => array(
			'printorder' => array(
				'0' => '正常',
				'-1' => 'IP地址不允许',
				'-2' => '关键参数为空或请求方式不对',
				'-3' => '客户编码不对',
				'-4' => '安全校验码不正确',
				'-5' => '请求时间失效',
				'-6' => '订单内容格式不对',
				'-7' => '重复的消息 （ msgNo 的值重复）',
				'-8' => '消息模式不对',
				'-9' => '服务器错误',
				'-10' => '服务器内部错误',
				'-111' => '打印终端不属于该账户',
			),
			'qureyorder' => array(
				'0' => '打印请求/任务中队列中，等待打印',
				'1' => '打印任务已完成/请求数据已打印',
				'2' => '打印任务/请求失败',
				'9' => '打印任务/请求已发送',
				'-1' => 'IP地址不允许',
				'-2' => '关键参数为空或请求方式不对',
				'-3' => '客户编码不正确',
				'-4' => '安全校验码不正确',
				'-5' => '请求时间失效。请求时间和请求到达飞印API的时间长超出安全范围。',
				'-6' => '订单编号错误或者不存在',
			),
			'qureystate' => array(
				'-1' => 'IP地址不允许',
				'-2' => '关键参数为空或请求方式不对',
				'-3' => '客户编码不正确',
				'-4' => '安全校验码不正确',
				'-5' => ' 同步应用服务器时间 了解更多飞印API的时间安全设置。',
			),
		),

		'365' => array(
		'printorder' => array(
			'0' => '正常',
			'2' => '订单添加成功，但是打印机缺纸，无法打印',
				'3' => '订单添加成功，但是打印机不在线',
				'10' => '内部服务器错误',
				'11' => '参数不正确',
				'12' => '打印机未添加到服务器',
				'13' => '未添加为订单服务器',
				'14' => '订单服务器和打印机不在同一个组',
				'15' => '订单已经存在，不能再次打印',
			),
			'qureyorder' => array(
				'0' => '打印成功',
				'1' => '正在打印中',
				'2' => '打印机缺纸',
				'3' => '打印机下线',
				'16' => '订单不存在',
			),
			'qureystate' => array(
				'1' => '打印机正常在线',
				'2' => '打印机缺纸',
				'3' => '打印机下线',
			)
		),

		'yilianyun' => array(
			'printorder' => array(
				1 => '数据提交成功',
				2 => '提交时间超时。验证你所提交的时间戳超过3分钟后拒绝接受',
				3 => '参数有误',
				4 => 'sign加密验证失败'
			),
			'qureyorder' => array(
				0 => '已打印/未打印',
				1 => '请求参数错误',
				2 => '服务器处理订单失败',
				3 => '没有找到该索引的订单',
			),
			'qureystate' => array(

			),
		),
	);
	return $data;
}

function print_printer_types() {
	return array(
		'feie' => array(
			'text' => '飞鹅打印机',
			'css' => 'label label-success',
		),
		'feiyin' => array(
			'text' => '飞印打印机',
			'css' => 'label label-danger',
		),
		'365' => array(
			'text' => '365打印机',
			'css' => 'label label-warning',
		),
		'AiPrint' => array(
			'text' => 'AiPrint打印机',
			'css' => 'label label-default',
		),
		'yilianyun' => array(
			'text' => '易联云打印机',
			'css' => 'label label-primary',
		),
		'qiyun' => array(
			'text' => '启云打印机',
			'css' => 'label label-info',
		),
	);
}


function print_feie_url($deviceno) {
	$number = substr($deviceno, 2, 1);
	$data = array(
		'5' => 'http://dzp.feieyun.com',
		'6' => 'http://api163.feieyun.com',
		'7' => 'http://api174.feieyun.com'
	);
	return $data[$number];
}