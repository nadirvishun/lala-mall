<?php
defined('IN_IA') or exit('Access Denied');
include('init.php');

function p($data) {
	echo '<pre style="padding-left: 200px">';
	print_r($data);
	echo '</pre>';
}

class Mloader {
	private $cache = array();
	function func($name) {
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/function/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Helper Function /addons/we7_wmall/function/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}

	function model($name) {
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Model /addons/we7_wmall/model/' . $name . '.mod.php', E_USER_NOTICE);
			return false;
		}
	}

	function classs($name) {
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/class/' . $name . '.class.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Class /addons/we7_wmall/class/' . $name . '.class.php', E_USER_ERROR);
			return false;
		}
	}
}

function iwurl($segment, $params = array(), $script = './index.php?') {
	list($controller, $action, $do) = explode('/', $segment);
	$url = $script;
	if (!empty($controller)) {
		$url .= "c={$controller}&";
	}
	if (!empty($action)) {
		$url .= "a={$action}&";
	}
	if (!empty($do)) {
		$url .= "do={$do}&";
	}
	if (!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}
	return $url;
}

function iurl($segment, $params = array(), $addhost = false) {
	global $_W;
	list($ctrl, $ac, $op, $ta) = explode('/', $segment);
	$params = array_merge(array('ctrl' => $ctrl, 'ac' => $ac, 'op' => $op, 'ta' => $ta, 'do' => 'web', 'm' => 'we7_wmall'), $params);
	$url = iwurl('site/entry', $params);
	if($_W['_controller'] == 'store' || $ctrl == 'store') {
		$params['i'] = $_W['uniacid'];
		$url = iwurl('site/entry', $params, './wmerchant.php?');
	} else if(defined('IN_AGENT') || $params['agent'] == 1) {
		unset($params['agent']);
		$params['i'] = $_W['uniacid'];
		$url = iwurl('site/entry', $params, './wagent.php?');
	}
	if($addhost) {
		$url = $_W['siteroot'] . 'web/' . substr($url, 2);
	}
	return $url;
}

function imurl($segment, $params = array(), $addhost = false) {
	global $_W;
	list($ctrl, $ac, $op, $ta) = explode('/', $segment);
	/*	$basic = array('agentid' => $_W['agentid'], 'ctrl' => $ctrl, 'ac' => $ac, 'op' => $op, 'ta' => $ta, 'do' => 'mobile', 'm' => 'we7_wmall');
		if($basic['agentid'] <= 0) {
			unset($basic['agentid']);
		}*/
	$basic = array('ctrl' => $ctrl, 'ac' => $ac, 'op' => $op, 'ta' => $ta, 'do' => 'mobile', 'm' => 'we7_wmall');
	$params = array_merge($basic, $params);

	$url = murl('entry', $params);
	if($addhost) {
		$url = $_W['siteroot'] . 'app/' . substr($url, 2);
	}
	return $url;
}

function ifilter_url($params) {
	global $_W;
	if(empty($params)) {
		return '';
	}
	$query_arr = array();
	$parse = parse_url($_W['siteurl']);
	if(!empty($parse['query'])) {
		$query = $parse['query'];
		parse_str($query, $query_arr);
	}
	$params = explode(',', $params);
	foreach($params as $val) {
		if(!empty($val)) {
			$data = explode(':', $val);
			$query_arr[$data[0]] = trim($data[1]);
		}
	}
	$query_arr['page'] = 1;
	$query = http_build_query($query_arr);
	if($_W['_controller'] == 'store') {
		return './wmerchant.php?' . $query;
	} elseif(defined('IN_AGENT')) {
		return './wagent.php?' . $query;
	}
	return './index.php?' . $query;
}

function module_familys() {
	return array(
		'basic' => array(
			'title' => '外送基础版',
			'css' => 'label label-success'
		),
		'errander' => array(
			'title' => '外送+跑腿',
			'css' => 'label label-success'
		),
		'errander_deliveryerApp' => array(
			'title' => '外送+跑腿+配送员app',
			'css' => 'label label-success'
		),
		'vip' => array(
			'title' => 'vip版',
			'css' => 'label label-success'
		),
	);
}

/*
 * $type (1:广告页, 2:首页幻灯片)
 * get_index_slide
 * */
function sys_fetch_slide($type = 1) {
	global $_W;
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_slide') .'where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1 order by displayorder desc' ,array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':type' => $type));
	if($type == 1) {
		shuffle($slides);
	}
	return $slides;
}

function tpl_format($title, $ordersn, $orderstatus, $remark = '') {
	$send = array(
		'first' => array(
			'value' => $title,
			'color' => '#ff510'
		),
		'OrderSn' => array(
			'value' => $ordersn,
			'color' => '#ff510'
		),
		'OrderStatus' => array(
			'value' => $orderstatus,
			'color' => '#ff510'
		),
		'remark' => array(
			'value' => $remark,
			'color' => '#ff510'
		),
	);
	return $send;
}

function array_compare($key, $array) {
	$keys = array_keys($array);
	$keys[] = $key;
	asort($keys);
	$values = array_values($keys);
	$index = array_search($key, $values);
	if($index >= 0) {
		$now = $values[$index];
		$next = $values[$index + 1];
		if($now == $next) {
			$next = intval($next);
			return $array[$next];
		}
		$index = $values[$index - 1];
		return $array[$index];
	}
	return false;
}

function store_orderbys() {
	return array(
		'distance' => array(
			'title' => '离我最近',
			'key' => 'distance',
			'val' => 'asc',
			'css' => 'icon-b distance',
		),
		'sailed' => array(
			'title' => '销量最高',
			'key' => 'sailed',
			'val' => 'desc',
			'css' => 'icon-b sailed-num',
		),
		'score' => array(
			'title' => '评分最高',
			'key' => 'score',
			'val' => 'desc',
			'css' => 'icon-b score',
		),
		'send_price' => array(
			'title' => '起送价最低',
			'key' => 'send_price',
			'val' => 'asc',
			'css' => 'icon-b send-price',
		),
		'delivery_time' => array(
			'title' => '送单速度最快',
			'key' => 'delivery_time',
			'val' => 'asc',
			'css' => 'icon-b delivery-time',
		),
	);
}

function store_discounts() {
	return array(
		'mallNewMember' => array(
			'title' => '首单立减',
			'key' => 'mallNewMember',
			'val' => 1,
			'css' => 'icon-b mallNewMember',
		),
		'newMember' => array(
			'title' => '新用户立减',
			'key' => 'newMember',
			'val' => 1,
			'css' => 'icon-b newMember',
		),
		'discount' => array(
			'title' => '立减优惠',
			'key' => 'discount',
			'val' => 1,
			'css' => 'icon-b discount',
		),
		'cashGrant' => array(
			'title' => '下单返现',
			'key' => 'cashGrant',
			'val' => 1,
			'css' => 'icon-b cashGrant',
		),
		'grant' => array(
			'title' => '下单满赠',
			'key' => 'grant',
			'val' => 1,
			'css' => 'icon-b grant',
		),
		'delivery_price' => array(
			'title' => '免配送费',
			'key' => 'delivery_price',
			'val' => 0,
			'css' => 'icon-b mian',
		),
		'bargain' => array(
			'title' => '特价优惠',
			'key' => 'bargain',
			'val' => 1,
			'css' => 'icon-b bargain',
		),
		'couponCollect' => array(
			'title' => '进店领券',
			'key' => 'couponCollect',
			'val' => 1,
			'css' => 'icon-b couponCollect',
		),
		'couponGrant' => array(
			'title' => '下单返券',
			'key' => 'couponGrant',
			'val' => 1,
			'css' => 'icon-b couponGrant',
		),
		'selfDelivery' => array(
			'title' => '自提优惠',
			'key' => 'selfDelivery',
			'val' => 1,
			'css' => 'icon-b selfDelivery',
		),
		'invoice_status' => array(
			'title' => '支持开发票',
			'key' => 'invoice_status',
			'val' => 1,
			'css' => 'icon-b invoice',
		),
	);
}

function upload_file($file, $type, $name = '') {
	global $_W;
	if (empty($file['name'])) {
		return error(-1, '上传失败, 请选择要上传的文件！');
	}
	if ($file['error'] != 0) {
		return error(-1, '上传失败, 请重试.');
	}
	load()->func('file');
	$pathinfo = pathinfo($file['name']);
	$ext = strtolower($pathinfo['extension']);
	$basename = strtolower($pathinfo['basename']);
	if($name != '') {
		$basename = $name;
	}
	$path = "resource/{$type}s/{$_W['uniacid']}/";
	mkdirs(MODULE_ROOT . '/' . $path);
	if (!strexists($basename, $ext)) {
		$basename .= '.' . $ext;
	}

	if (!file_move($file['tmp_name'],  MODULE_ROOT . '/' . $path . $basename)) {
		return error(-1, '保存上传文件失败');
	}
	return $path . $basename;
}

function read_excel($filename) {
	include_once (IA_ROOT . '/framework/library/phpexcel/PHPExcel.php');
	$filename = MODULE_ROOT . '/' . $filename;
	if(!file_exists($filename)) {
		return error(-1, '文件不存在或已经删除');
	}
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if($ext == 'xlsx') {
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	} else {
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}

	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filename);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$highestRow = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	$excelData = array();
	for ($row = 1; $row <= $highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
		}
	}
	return $excelData;
}

function sub_day($staday) {
	$value = TIMESTAMP - $staday;
	if($value < 0) {
		return '';
	} elseif($value >= 0 && $value < 59) {
		return ($value+1)."秒";
	} elseif($value >= 60 && $value < 3600) {
		$min = intval($value / 60);
		return $min." 分钟";
	} elseif($value >=3600 && $value < 86400) {
		$h = intval($value / 3600);
		return $h." 小时";
	} elseif($value >= 86400 && $value < 86400*30) {
		$d = intval($value / 86400);
		return intval($d)." 天";
	} elseif($value >= 86400*30 && $value < 86400*30*12) {
		$mon  = intval($value / (86400*30));
		return $mon." 月";
	} else {
		$y = intval($value / (86400*30*12));
		return $y." 年";
	}
}

function sub_time($time) {
	$rtime = date("m-d H:i", $time);
	$htime = date("H:i", $time);
	$time = time() - $time;
	if ($time < 60) {
		$str = '刚刚';
	} elseif ($time < 3600) {
		$min = floor($time / 60);
		$str = $min . '分钟前';
	} elseif ($time < 86400) {
		$h = floor($time / (60 * 60));
		$str = $h.'小时前 '. $htime;
	} elseif ($time < 259200) {
		$d = floor($time / 86400);
		if($d == 1) {
			$str = '昨天 '. $rtime;
		} else {
			$str = '前天 '. $rtime;
		}
	} else {
		$str = $rtime;
	}
	return $str;
}

function transform_time($time) {
	$data = '';
	if ($time >= 0) {
		$days = intval($time / 86400);
		if($days > 0) {
			$data .= "{$days}天";
		}
		$remain = $time % 86400;
		$hours = intval($remain / 3600);
		if($hours > 0) {
			$data .= "{$hours}小时";
		}
		$remain = $remain % 3600;
		$minutes = intval($remain / 60);
		if($minutes > 0) {
			$data .= "{$minutes}分钟";
		}
		$seconds = $remain % 60;
		if($seconds > 0 || empty($days) && empty($hours) && empty($minutes)) {
			$data .= "{$seconds}秒";
		}
	}
	return $data;
}

function check_verifycode($mobile, $code) {
	global $_W;
	$isexist = pdo_fetch('select * from ' . tablename('uni_verifycode') . ' where uniacid = :uniacid and receiver = :receiver and verifycode = :verifycode and createtime >= :createtime', array(':uniacid' => $_W['uniacid'], ':receiver' => $mobile, ':verifycode' => $code, ':createtime' => time()-1800));
	if(!empty($isexist)) {
		return true;
	}
	return false;
}

function slog($type, $title, $params, $message) {
	global $_W;
	//wxtplNotice, managerappJpush, deliveryerappJpush, alidayuSms, alidayuCall
	if(empty($type)) {
		return error(-1, '错误类型不能为空');
	}
	if(empty($message)) {
		return error(-1, '错误信息不能为空');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'type' => $type,
		'title' => $title,
		'params' => iserializer($params),
		'message' => iserializer($message),
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_system_log', $data);
	return true;
}

function sys_notice_settle($sid, $type = 'clerk', $note= '') {
	global $_W;
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = store_manager($sid);
	$store_status = array(
		1 => '审核通过',
		2 => '审核中',
		3 => '审核未通过',
	);
	$acc = WeAccount::create($_W['acid']);
	itemplate_cloud();
	if($type == 'clerk') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店申请人信息不完善');
		}
		//通知申请人
		$tips = "【{$store['title']}】申请入驻【{$_W['we7_wmall']['config']['mall']['title']}】进度通知";
		$remark = array(
			"申请时间: " . date('Y-m-d H: i', $store['addtime']),
			"审核时间: " . date('Y-m-d H: i', time()),
			"登陆账号: " . $store['manager']['title'],
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store_status[$store['status']],
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '平台商户入驻进度微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'manager') {
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(empty($maneger['openid'])) {
			return error(-1, '平台管理员信息不存在');
		}
		$tips = "尊敬的【{$maneger['nickname']}】，有新的商家提交了入驻请求。请登录电脑进行审核";
		$remark = array(
			"商家地址: {$store['address']}",
			"申请人手机号: {$store['manager']['mobile']}",
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['manager']['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date('Y-m-d H:i', time()),
				'color' => '#ff510',
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '平台商户入驻微信通知平台管理员', $send, $status['message']);
		}
	}
	return $status;
}

function ifile_put_contents($filename, $data) {
	global $_W;
	load()->func('file');
	$filename = MODULE_ROOT . '/' . $filename;
	mkdirs(dirname($filename));
	file_put_contents($filename, $data);
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($filename);
}

function sys_notice_store_getcash($sid, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = store_manager($store['id']);
	if($type != 'borrow_openid') {
		$log = pdo_get('tiny_wmall_store_getcash_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $getcash_log_id));
		if(empty($log)) {
			return error(-1, '提现记录不存在');
		}
	}
	$log['account'] = iunserializer($log['account']);
	$acc = WeAccount::create($_W['acid']);
	itemplate_cloud();
	if($type == 'apply') {
		if(!empty($store['manager']) && !empty($store['manager']['openid'])) {
			//通知申请人
			$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: 微信",
				"真实姓名: " . $log['account']['realname'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '商户提现申请微信通知申请人', $send, $status['message']);
			}
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,【{$store['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: 微信",
				"真实姓名: " . $log['account']['realname'],
				"提现总金额: " . $log['get_fee'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '商户申请提现微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: 微信",
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_success_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '商户申请提现成功微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】, 【{$store['title']}】账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: 微信",
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '商户申请提现失败微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'borrow_openid') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】, 您正在进行门店【{$store['title']}】的提现申请。平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
		$remark = array(
			"申请门店: " . $store['title'],
			"账户类型: 微信",
			'请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员'
		);
		$params = array(
			'first' => $tips,
			'money' => $getcash_log_id,
			'timet' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$url = imurl("wmall/auth/oauth", array('params' => base64_encode(json_encode($payment_wechat[$payment_wechat['type']]))), true);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '微信端商户申请提现授权微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'cancel') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$addtime = date('Y-m-d H:i', $log['addtime']);
		$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】在{$addtime}的申请提现已被平台管理员撤销";
		$remark = array(
			"订单　号: " . $log['trade_no'],
			"申请门店: " . $store['title'],
			"撤销时间: " . date('Y-m-d H:i', $log['endtime']),
			'撤销原因: ' . $note,
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '商户申请提现被平台管理员撤销微信通知申请人', $send, $status['message']);
		}
	}
	return $status;
}

function sys_wechat_tpl_format($params) {
	$send = array();
	foreach($params as $key => $param) {
		$send[$key] = array(
			'value' => $param,
			'color' => '#ff510',
		);
	}
	return $send;
}

/**
 * 计算两个坐标之间的距离(米)
 * @param float $fP1Lat 起点(纬度)
 * @param float $fP1Lon 起点(经度)
 * @param float $fP2Lat 终点(纬度)
 * @param float $fP2Lon 终点(经度)
 * @return int
 */
function distanceBetween($longitude1, $latitude1, $longitude2, $latitude2) {
	$radLat1 = radian ( $latitude1 );
	$radLat2 = radian ( $latitude2 );
	$a = radian ( $latitude1 ) - radian ( $latitude2 );
	$b = radian ( $longitude1 ) - radian ( $longitude2 );
	$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) *
			cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
	$s = $s * 6378.137; //乘上地球半径，单位为公里
	$s = round ( $s * 10000 ) / 10000; //单位为公里(km)
	return $s * 1000; //单位为m
}

function radian($d) {
	return $d * 3.1415926535898 / 180.0;
}

function distance($origins, $destination, $type = 1) {
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'type' => $type,
		'origins' => implode(',', $origins),
		'destination' => implode(',', $destination),
		'output' => 'json',
	);
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get('http://restapi.amap.com/v3/distance?' . $query);
	if(is_error($result)) {
		return error(-1, $result['info']);
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 1) {
		return error(-1, $result['info']);
	}
	return round($result['results'][0]['distance'] / 1000, 3);
}

function ip2city($ip = '') {
	global $_W;
	if(empty($ip)) {
		$ip = $_W['client_ip'];
	}
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'ip' => $ip,
		'output' => 'json',
	);
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get('http://restapi.amap.com/v3/ip?' . $query);
	if(is_error($result)) {
		return error(-1, $result['info']);
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 1) {
		return error(-1, $result['info']);
	}
	return $result;
}

function isPointInPolygon($polygon, $lnglat){
	$count = count($polygon);
	$px = $lnglat[1];
	$py = $lnglat[0];
	$flag = FALSE;
	for ($i = 0, $j = $count - 1; $i < $count; $j = $i, $i++) {
		$sy = $polygon[$i][0];
		$sx = $polygon[$i][1];
		$ty = $polygon[$j][0];
		$tx = $polygon[$j][1];
		if ($px == $sx && $py == $sy || $px == $tx && $py == $ty)
			return TRUE;
		if ($sy < $py && $ty >= $py || $sy >= $py && $ty < $py) {
			$x = $sx + ($py - $sy) * ($tx - $sx) / ($ty - $sy);
			if ($x == $px)
				return TRUE;
			if ($x > $px)
				$flag = !$flag;
		}
	}
	return $flag;
}

function array_order($value, $array) {
	$array[] = $value;
	asort($array);
	$array = array_values($array);
	$index = array_search($value, $array);
	return $array[$index + 1];
}

function itpl_ueditor($id, $value = '', $options = array(), $isNews = false) {
	global $_W;
	$s = '';
	if (!defined('TPL_INIT_UEDITOR')) {
		$s .= '<script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/ueditor.config.js"></script><script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>';
	}
	$options['height'] = empty($options['height']) ? 200 : $options['height'];
	$s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
	$s .= "
	<script type=\"text/javascript\">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': {$options['height']},
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multiple : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			UE.registerUI('myinsertvideo',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(video){
								if (!video) {
									return;
								} else {
									var videoType = video.isRemote ? 'iframe' : 'video';
									editor.execCommand('insertvideo', {
										'url' : video.url,
										'width' : 300,
										'height' : 200
									}, videoType);
								}
							}, {type : 'video', isNews : '".$isNews."'});
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入视频',
					title: '插入视频',
					cssRules :'background-position: -320px -20px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 20);
			".(!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '')."
	</script>";
	return $s;
}

function sys_notice_deliveryer_getcash($deliveryer_id, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer',  array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在');
	}
	if($type != 'borrow_openid') {
		$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id, 'id' => $getcash_log_id));
		if(empty($log)) {
			return error(-1, '提现记录不存在');
		}
	}
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		if(!empty($deliveryer['openid'])) {
			//通知申请人
			$tips = "您好,【{$deliveryer['title']}】, 您的账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请　人: " . $deliveryer['title'],
				"手机　号: " . $deliveryer['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '配送员申请提现微信通知申请人', $send, $status['message']);
			}
		}

		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,配送员【{$deliveryer['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请　人: " . $deliveryer['title'],
				"手机　号: " . $deliveryer['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '配送员申请提现微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		$tips = "您好,【{$deliveryer['title']}】,您的账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $deliveryer['title'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['take_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_success_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '配送员申请提现成功微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		$tips = "您好,【{$deliveryer['title']}】, 您的账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $deliveryer['title'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '配送员申请提现失败微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'borrow_openid') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		//通知申请人
		$tips = "您好,【{$deliveryer['title']}】, 您正在进行提现申请.平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
		$remark = array(
			"申请　人: " . $deliveryer['title'],
			"手机　号: " . $deliveryer['mobile'],
			'请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'timet' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$url = imurl("wmall/auth/oauth", array('params' => base64_encode(json_encode($payment_wechat[$payment_wechat['type']]))), true);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '微信端配送员申请提现授权微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'cancel') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		$addtime = date('Y-m-d H:i', $log['addtime']);
		$tips = "您好,【{$deliveryer['title']}】,您在{$addtime}的申请提现已被平台管理员撤销";
		$remark = array(
			"订单　号: " . $log['trade_no'],
			"撤销时间: " . date('Y-m-d H:i', $log['endtime']),
			"撤销原因: " . $note,
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '配送员申请提现被平台管理员取消微信通知申请人', $send, $status['message']);
		}
	}
	return $status;
}

function date2week($timestamp) {
	$weekdays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
	$week = date('w', $timestamp);
	return $weekdays[$week];
}

function media_id2url($media_id) {
	mload()->classs('wxaccount');
	$acc = new WxAccount();
	$data = $acc->media_download($media_id);
	if(is_error($data)) {
		return $data;
	}
	return $data;
}

function ierror($result_code, $result_message = '调用接口成功', $data = array('resultCode' => '')) {
	$result = array(
		'resultCode' => $result_code,
		'resultMessage' => $result_message,
		'data' => $data,
	);
	return $result;
}

function Jpush_deliveryer_send($title, $alert, $extras = array(), $audience = '', $platform = 'all') {
	global $_W;
	$config = $_W['we7_wmall']['config']['app']['deliveryer'];
	if(empty($config['push_key']) || empty($config['push_secret'])) {
		return error(-1, 'key或secret不完善');
	}
	if(empty($config['serial_sn'])) {
		return error(-1, 'app序列号不完善');
	}
	$sound_router = array(
		'takeout' => array(
			'ordernew' => 'orderSound.wav',
			'orderassign' => 'assignSound.wav',
			'ordercancel' => 'cancelSound.wav',
		),
		'errander' => array(
			'ordernew' => 'erranderOrderSound.wav',
			'orderassign' => 'erranderAssignSound.wav',
			'ordercancel' => 'erranderCancelSound.wav',
		),
	);
	$sound = $sound_router[$extras['redirect_type']]['notify_type'];
	if(empty($sound)) {
		$sound = 'default';
	}
	$push_tag = trim($config['serial_sn']);
	if($config['android_version'] == 2) {
		$push_tag = $config['push_tags']['working'];
	}
	if(empty($audience)) {
		$audience = array(
			'tag' => array(
				$push_tag
			)
		);
	}
	$extras_orginal = array(
		'voice_play_nums' => 1,
		'voice_text' => '',
		'redirect_type' => 'order',
		'redirect_extra' => '',
	);
	$extras = array_merge($extras_orginal, $extras);
	$jpush_andriod = array(
		'platform' => 'android',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'android' => array(
				'alert' => $alert,
				'title' => $title,
				'builder_id' => 1,
				'extras' => $extras
			)
		),
	);
	$jpush_ios = array(
		'platform' => 'ios',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'ios' => array(
				'alert' => $alert,
				'sound' => $sound,
				'badge' => '+1',
				'extras' => $extras
			),
		),
		'options' => array(
			'apns_production' => 1
		),
	);
	load()->func('communication');
	$extra = array(
		'Authorization' => "Basic " . base64_encode("{$config['push_key']}:{$config['push_secret']}")
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_andriod), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('deliveryerappJpush', '配送员App极光推送(andriod)通知配送员', $jpush_andriod, $response['message']);
	}
	if(empty($config['ios_build_type'])) {
		$extra = array('Authorization' => "Basic ZWQ4YzE3YmM3YjJlOWYzMGEyYWZlMThiOjIxZmM5ZjBiOGU4ODRmNDUzOTMxN2MyZQ==");
	}
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_ios), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('deliveryerappJpush', '配送员App极光推送(ios)通知配送员', $jpush_ios, $response['message']);
	}
	return true;
}

function Jpush_clerk_send($title, $alert, $extras = array(), $audience = '', $platform = 'all') {
	global $_W;
	$config = $_W['we7_wmall']['config']['app']['manager'];
	if(empty($config['push_key']) || empty($config['push_secret'])) {
		return error(-1, 'key或secret不完善');
	}
	if(empty($config['serial_sn'])) {
		return error(-1, 'app序列号不完善');
	}
	$notify_routers = array(
		'place_order' => 'new',
		'remind' => 'remind',
	);
	$extras['resource'] = "{$_W['siteroot']}/addons/we7_wmall/resource/mp3/{$_W['uniacid']}/{$config['phonic'][$notify_routers[$extras['notify_type']]]}";

	$sound_router = array(
		'place_order' => 'widget/res/sound/orderSound.wav',
		'remind' => 'widget/res/sound/remindSound.wav',
		'cancel' => 'widget/res/sound/cancelSound.wav',
		'refund' => 'widget/res/sound/refundSound.wav',
	);
	$sound = $sound_router[$extras['notify_type']];
	if(empty($sound)) {
		$sound = 'default';
	}
	$tag = trim($config['serial_sn']);
	if(empty($audience)) {
		$audience = array(
			'tag' => array(
				$tag
			)
		);
	}
	$extras_orginal = array(
		'voice_play_type' => 2,
		'notify_type' => $notify_routers[$extras['notify_type']]
	);
	$extras = array_merge($extras, $extras_orginal);
	$jpush = array(
		'platform' => 'android',
		'audience' => $audience,
		'message' => array(
			'msg_content' => $alert,
			'title' => $title,
			'extras' => $extras
		),
	);
	load()->func('communication');
	$extra = array(
		'Authorization' => "Basic " . base64_encode("{$config['push_key']}:{$config['push_secret']}")
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('managerappJpush', '商家App极光推送(andriod)通知店员', $jpush, $response['message']);
	}
	if(empty($config['ios_build_type'])) {
		$extra = array('Authorization' => "Basic ZDY1YTVmNzJjYWIyZjZlOTQzY2QwZDZkOjE4NTYyMTY4MWIwNTRlOTAxOTEzNjJkNg==");
	}
	$jpush_ios = array(
		'platform' => 'ios',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'ios' => array(
				'alert' => $alert,
				'sound' => $sound,
				'badge' => '+1',
				'extras' => $extras
			),
		),
		'options' => array(
			'apns_production' => 1
		),
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_ios), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('managerappJpush', '商家App极光推送(ios)通知店员', $jpush_ios, $response['message']);
	}
	return true;
}

function Jpush_response_parse($response) {
	if(is_error($response)) {
		return $response;
	}
	$result = @json_decode($response['content'], true);
	if(!empty($result['error'])) {
		return error(-1, "错误代码: {$result['error']['code']}, 错误信息: {$result['error']['message']}");
	}
	return true;
}

function array_sort($array, $sort_key, $sort_order = SORT_ASC) {
	if(is_array($array)){
		foreach ($array as $row_array){
			$key_array[] = $row_array[$sort_key];
		}
		array_multisort($key_array, $sort_order, $array);
		return $array;
	}
	return false;
}

function array_depth($array) {
	if(!is_array($array)) return 0;
	$max_depth = 1;
	foreach ($array as $value) {
		if (is_array($value)) {
			$depth = array_depth($value) + 1;
			if ($depth > $max_depth) {
				$max_depth = $depth;
			}
		}
	}
	return $max_depth;
}

function multimerge(){
	$arrs = func_get_args();
	$merged = array();
	while($arrs){
		$array = array_shift($arrs);
		if(!$array){
			continue;
		}
		foreach ($array as $key => $value){
			if (is_string($key)){
				if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])){
					$merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
				}else{
					$merged[$key] = $value;
				}
			}else{
				$merged[] = $value;
			}
		}
	}
	return $merged;
}


function category_store_label() {
	global $_W;
	$data = pdo_fetchall('select id, title, alias,  color, is_system, displayorder from' . tablename('tiny_wmall_category') . ' where uniacid = :uniacid and type = :type order by is_system desc, displayorder desc', array(':uniacid' => $_W['uniacid'], ':type' => 'TY_store_label'), 'id');
	return $data;
}

function mktTransfers_get_openid($id, $openid, $money, $type = 'store') {
	return $openid;
}

function tocategory($category, $separator = ',') {
	global $_W;
	if(empty($category)) {
		return '';
	}
	$category_arr = explode('|', $category);
	$category = implode(',', $category_arr);
	$data = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store_category') . " where uniacid = :uniacid and id in ({$category})", array(':uniacid' => $_W['uniacid']), 'id');
	if(empty($data)) {
		return $data;
	}
	$return = array();
	foreach($data as $da) {
		$return[] = $da['title'];
	}
	return implode($separator, $return);
}

function totime($times, $separator = ',') {
	$times = iunserializer($times);
	if(empty($times)) {
		return '';
	}
	$return = array();
	foreach($times as $time) {
		$return[] = "{$time['start_hour']}~{$time['end_hour']}";
	}
	return implode($separator, $return);
}
function toplateform($key, $all = false) {
	$plateform = array(
		'we7_wmall' => array(
			'css' => 'label label-default',
			'text' => '本平台',
			'color' => '',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么平台',
			'color' => '',
		),
		'meituan' => array(
			'css' => 'label label-warning',
			'text' => '美团平台',
			'color' => '',
		),
	);
	if(empty($all)) {
		return $plateform[$key]['text'];
	} else {
		return $plateform[$key];
	}
}

function longurl2short($longurl) {
	load()->func('communication');
	$token = WeAccount::token();
	$url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$token}";
	$send = array(
		'action' => 'long2short',
		'long_url' => $longurl,
	);
	$response = ihttp_request($url, json_encode($send));
	if(is_error($response)) {
		return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
	}
	$result = @json_decode($response['content'], true);
	if(empty($result)) {
		return error(-1, "接口调用失败, 元数据: {$response['meta']}");
	} elseif(!empty($result['errcode'])) {
		return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
	}
	return $result['short_url'];
}



