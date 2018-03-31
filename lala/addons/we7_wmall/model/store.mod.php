<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function is_favorite_store($sid, $uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$is_ok = pdo_get('tiny_wmall_store_favorite', array('sid' => $sid, 'uid' => $uid));
	return $is_ok;
}

function store_set_openplateform($sid, $key, $value) {
	global $_W;
	$openplateform = store_get_openplateform($sid);
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$openplateform[$keys[0]] = $value;
	} elseif($counts == 2) {
		if(!is_array($openplateform[$keys[0]])) {
			$openplateform[$keys[0]] = array();
		}
		$openplateform[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		if(!is_array($openplateform[$keys[0]])) {
			$openplateform[$keys[0]] = array();
		} elseif(!is_array($openplateform[$keys[0]][$keys[1]])) {
			$openplateform[$keys[0]][$keys[1]] = array();
		}
		$openplateform[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_store', array('openplateform_extra' => iserializer($openplateform)), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	return true;
}

function store_get_openplateform($sid, $key = '') {
	global $_W;
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('openplateform_extra'));
	$openplateform = iunserializer($store['openplateform_extra']);
	if(!is_array($openplateform)) {
		$openplateform = array();
	}
	if(empty($key)) {
		return $openplateform;
	}
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		return $openplateform[$key];
	} elseif($counts == 2) {
		return $openplateform[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $openplateform[$keys[0]][$keys[1]][$keys[1]];
	}
	return true;
}

function clerk_manage($id) {
	global $_W;
	$perm = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $id, 'role' => 'manager'), array(), 'sid');
	if(empty($perm)) {
		return array();
	}
	return array_keys($perm);
}

//get_store
function store_fetch($id, $field = array()) {
	global $_W;
	if(empty($id)) {
		return false;
	}
	$field_str = '*';
	if(!empty($field)) {
		$field_str = implode(',', $field);
	}
	$data = pdo_fetch("SELECT {$field_str} FROM " . tablename('tiny_wmall_store') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if(empty($data)) {
		return error(-1, '门店不存在或已删除');
	}
	$cid = array_filter(explode('|', $data['cid']));
	$cid = implode(',', $cid);
	if(!empty($data['cid']) && !empty($cid)) {
		$category = pdo_fetchall('select title from ' . tablename('tiny_wmall_store_category') . " where uniacid = :uniacid and id in ({$cid})", array(':uniacid' => $_W['uniacid']));
		$data['category'] = array();
		if(!empty($category)) {
			foreach($category as $val) {
				$data['category'][] = $val['title'];
			}
			$data['category'] = implode('、', $data['category']);
		}
	}
	$se_fileds = array('thumbs', 'delivery_areas', 'delivery_extra', 'sns', 'payment', 'business_hours', 'remind_reply', 'qualification', 'comment_reply', 'wechat_qrcode', 'custom_url', 'serve_fee', 'order_note', 'delivery_times', 'openplateform_extra');
	foreach($se_fileds as $se_filed) {
		if(isset($data[$se_filed])) {
			if(!in_array($se_filed, array('thumbs', 'delivery_areas'))) {
				$data[$se_filed] = (array)iunserializer($data[$se_filed]);
			} else {
				$data[$se_filed] = iunserializer($data[$se_filed]);
			}
		}
	}
	$data['is_in_business_hours'] = $data['is_in_business'];
	if(isset($data['business_hours'])) {
		$data['is_in_business_hours'] = $data['is_in_business_hours'] && store_is_in_business_hours($data['business_hours']);
		$hour = array();
		foreach($data['business_hours'] as $li) {
			if(!is_array($li)) continue;
			$hour[] = "{$li['s']}~{$li['e']}";
		}
		$data['business_hours_cn'] = implode(',', $hour);
	}
	if(isset($data['score'])) {
		$data['score_cn'] = round($data['score'] / 5, 2) * 100;
	}
	if(isset($data['delivery_fee_mode'])) {
		if($data['delivery_fee_mode'] == 1) {
			$data['order_address_limit'] = 1; //不检测距离
			if(!$data['not_in_serve_radius'] && $data['serve_radius'] > 0) {
				$data['order_address_limit'] = 2; //检测门店到收货地址的距离,超过配送范围不让下单
			}
		} elseif($data['delivery_fee_mode'] == 2) {
			$data['delivery_price_extra'] = iunserializer($data['delivery_price']);
			$data['delivery_price'] = $data['delivery_price_extra']['start_fee'];
			if(!$data['not_in_serve_radius'] && $data['serve_radius'] > 0) {
				$data['order_address_limit'] = 2; //检测门店到收货地址的距离,超过配送范围不让下单
			} else {
				$data['order_address_limit'] = 3; //检测门店到收货地址的距离
			}
		} elseif($data['delivery_fee_mode'] == 3) {
			$data['order_address_limit'] = 4;
			$price = store_order_condition($data);
			$data['delivery_price'] = $price['delivery_price'];
			$data['send_price'] = $price['send_price'];
		}
	}
	return $data;
}

function store_manager($sid) {
	global $_W;
	$perm = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'role' => 'manager'));
	$clerk = array();
	if(!empty($perm)) {
		$clerk =  pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $perm['clerk_id']));
	}
	return $clerk;
}

function store_fetchall($field = array()) {
	global $_W;
	$field_str = '*';
	if(!empty($field)) {
		$field_str = implode(',', $field);
	}
	$data = pdo_fetchall("SELECT {$field_str} FROM " . tablename('tiny_wmall_store') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');
	$se_fileds = array('thumbs', 'sns', 'mobile_verify', 'payment', 'business_hours', 'thumbs', 'remind_reply', 'comment_reply', 'wechat_qrcode', 'custom_url');
	foreach($se_fileds as $se_filed) {
		if(isset($data[$se_filed])) {
			if($se_filed != 'thumbs') {
				$data[$se_filed] = (array)iunserializer($data[$se_filed]);
			} else {
				$data[$se_filed] = iunserializer($data[$se_filed]);
			}
		}
	}
	if(isset($data['business_hours'])) {
		$data['is_in_business_hours'] = store_is_in_business_hours($data['business_hours']);
		$hour = array();
		foreach($data['business_hours'] as $li) {
			$hour[] = "{$li['s']}~{$li['e']}";
		}
		$data['business_hours_cn'] = implode(',', $hour);
	}
	if(isset($data['score'])) {
		$data['score_cn'] = round($data['score'] / 5, 2) * 100;
	}
	return $data;
}

//store_fetchall_category
function store_fetchall_category() {
	global $_W, $_GPC;
	$data = pdo_fetchall('select id,thumb,link,title from ' . tablename('tiny_wmall_store_category') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	if(!empty($data)) {
		foreach($data as &$da) {
			$da['thumb'] = tomedia($da['thumb']);
			$da['is_sys'] = 0;
			if(empty($da['link'])) {
				$da['is_sys'] = 1;
				$da['link'] = imurl('wmall/home/search', array('cid' => $da['id'], 'order' => $_GPC['order'], 'dis' => $_GPC['dis']));
			}
		}
	}
	return $data;
}
//store_fetch_category
function store_fetch_category() {
	global $_W, $_GPC;
	$cid = intval($_GPC['cid']);
	$category = pdo_get('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $cid, 'status' => 1));
	if(!empty($category)) {
		if(!empty($category['nav']) && $category['nav_status'] == 1){
			$category['nav'] = iunserializer($category['nav']);
		}
		if(!empty($category['slide']) && $category['slide_status'] == 1){
			$category['slide'] = iunserializer($category['slide']);
			array_sort($category['slide'], 'displayorder', SORT_DESC);
		}
	}
	return $category;
}


//store_fetch_activity
function store_fetch_activity($sid, $type = array()) {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);

	if(!empty($type)) {
		$type = implode("','", $type);
		$type = "'{$type}'";
		$condition .= " and type in ({$type})";
	}
	$data = pdo_fetchall("SELECT title,type FROM " . tablename('tiny_wmall_store_activity') . $condition, $params, 'type');
	$activity['num'] = count($data);
	$activity['items'] = $data;
	return $activity;
}

//is_in_business_hours
function store_is_in_business_hours($business_hours) {
	if(!is_array($business_hours)) {
		return true;
	}
	$business_hours_flag = false;
	foreach($business_hours as $li) {
		if(!is_array($li)) {
			continue;
		}
		$li_s_tmp = explode(':', $li['s']); //开始时间
		$li_e_tmp = explode(':', $li['e']); //结束时间
		$s_timepas = mktime($li_s_tmp[0], $li_s_tmp[1]);
		$e_timepas = mktime($li_e_tmp[0], $li_e_tmp[1]);
		$now = TIMESTAMP;
		if($now >= $s_timepas && $now <= $e_timepas) {
			$business_hours_flag = true;
			break;
		}
	}
	return $business_hours_flag;
}

function store_business_hours_init($sid = 0) {
	global $_W;
	if($sid > 0) {
		$store = store_fetch($sid, array('business_hours', 'is_in_business'));
		$is_rest = 1;
		if($store['is_in_business'] && store_is_in_business_hours($store['business_hours'])) {
			$is_rest = 0;
		}
		pdo_update('tiny_wmall_store', array('is_rest' => $is_rest), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	} else {
		$stores = pdo_fetchall('select id,business_hours,is_in_business from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
		if(!empty($stores)) {
			foreach($stores as $row) {
				$row['business_hours'] = iunserializer($row['business_hours']);
				$is_rest = 1;
				if($row['is_in_business'] && store_is_in_business_hours($row['business_hours'])) {
					$is_rest = 0;
				}
				pdo_update('tiny_wmall_store', array('is_rest' => $is_rest), array('uniacid' => $_W['uniacid'], 'id' => $row['id']));
			}
		}
	}
	return true;
}

//get_goods_category
function store_fetchall_goods_category($store_id, $status = '-1') {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $store_id);
	if($status >= 0) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods_category') . $condition . ' order by displayorder desc, id asc', $params, 'id');
	return $data;
}

//get_goods_one
function store_fetch_goods($id, $field = array('basic', 'options')) {
	global $_W;
	$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($goods)) {
		return error(-1, '商品不存在或已删除');
	}
	$goods['thumb_'] = tomedia($goods['thumb']);
	if(in_array('options', $field) && $goods['is_options']) {
		$goods['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $id));
	}
	return $goods;
}

/*计算门店的评价*/
function store_comment_stat($sid, $update = true) {
	global $_W;
	$stat = array();
	$stat['goods_quality'] = round(pdo_fetchcolumn('select avg(goods_quality) from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)), 1);
	$stat['delivery_service'] = round(pdo_fetchcolumn('select avg(delivery_service) from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)), 1);
	$stat['score'] = round(($stat['goods_quality'] + $stat['delivery_service']) / 2, 1);
	if($update) {
		pdo_update('tiny_wmall_store', array('score' => $stat['score']), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	}
	return $stat;
}

function store_status() {
	$data = array(
		'0' => array(
			'css' => 'label label-default',
			'text' => '隐藏中',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-success',
			'text' => '显示中',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '审核中',
		),
		'3' => array(
			'css' => 'label label-danger',
			'text' => '审核未通过',
		),
	);
	return $data;
}

function store_account($sid, $fileds = array()) {
	global $_W;
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'],'sid' => $sid), $fileds);
	if(!empty($account)) {
		$se_fileds = array('wechat', 'alipay', 'fee_takeout', 'fee_selfDelivery', 'fee_instore', 'fee_paybill', 'fee_eleme', 'fee_meituan');
		foreach($se_fileds as $se_filed) {
			if(isset($account[$se_filed])) {
				$account[$se_filed] = (array)iunserializer($account[$se_filed]);
			}
		}
	}
	return $account;
}

function store_update_account($sid, $fee, $trade_type, $extra, $remark = '') {
	global $_W;
	//$trade_type 1: 订单入账, 2: 申请提现, 3: 账户清零
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(empty($account)) {
		return error(-1, '账户不存在');
	}
	$now_amount = $account['amount'] + $fee;
	pdo_update('tiny_wmall_store_account', array('amount' => $now_amount), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	$log = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'trade_type' => $trade_type,
		'extra' => $extra,
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_store_current_log', $log);
	return true;
}

function store_getcash_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '提现成功',
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '申请中',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '提现失败',
		),
	);
	return $data;
}

function store_delivery_times($sid, $force_update = false) {
	global $_W;
	$cache_key = "we7wmall_store_delivery_times|{$sid}|{$_W['uniacid']}";
	if(!$force_update) {
		$data = cache_read($cache_key);
		if(!empty($data) && $data['updatetime'] > TIMESTAMP) {
			return $data;
		}
	}
	$store = store_fetch($sid, array('id', 'delivery_reserve_days', 'delivery_within_days', 'delivery_time', 'delivery_times', 'delivery_fee_mode', 'delivery_price'));

	//配送时间
	$days = array();
	$totaytime = strtotime(date('Y-m-d'));
	if($store['delivery_reserve_days'] > 0) { //需提前几天点外卖
		$days[] = date('m-d', $totaytime + $store['delivery_reserve_days'] * 86400);
	} elseif($store['delivery_within_days'] > 0) {//可提前几天点外卖
		for($i = 0; $i <= $store['delivery_within_days']; $i++) {
			$days[] = date('m-d', $totaytime + $i * 86400);
		}
	} else {
		$days[] = date('m-d');
	}

	//配送时间段
	$times = $store['delivery_times'];
	$timestamp = array();
	if(!empty($times)) {
		foreach($times as $key => &$row) {
			if(empty($row['status'])) {
				unset($times[$key]);
				continue;
			}
			if($store['delivery_fee_mode'] == 1) {
				$row['delivery_price'] = $store['delivery_price'] + $row['fee'];
				$row['delivery_price_cn'] = "{$row['delivery_price']}元配送费";
			} else {
				$row['delivery_price'] = $store['delivery_price'] + $row['fee'];
				$row['delivery_price_cn'] = "配送费{$row['delivery_price']}元起";
			}
			$end = explode(':', $row['end']); //开始时间
			$row['timestamp'] = mktime($end[0], $end[1]);
			$timestamp[$key] = $row['timestamp'];
		}
	} else {
		$start = mktime(8, 0);
		$end = mktime(22, 0);
		for($i = $start; $i < $end;) {
			if($store['delivery_fee_mode'] == 1) {
				$store['delivery_price_cn'] = "{$store['delivery_price']}元配送费";
			} else {
				$store['delivery_price_cn'] = "配送费{$store['delivery_price']}元起";
			}
			$times[] = array(
				'start' => date('H:i', $i),
				'end' => date('H:i', $i + 1800),
				'timestamp' => $i + 1800,
				'fee' => 0,
				'delivery_price' => $store['delivery_price'],
				'delivery_price_cn' => $store['delivery_price_cn'],
			);
			$timestamp[] = $i + 1800;
			$i += 1800;
		}
	}
	$data = array(
		'days' => $days,
		'times' => $times,
		'timestamp' => $timestamp,
		'updatetime' => strtotime(date('Y-m-d')) + 86400,
		'reserve' => ($store['delivery_reserve_days'] > 0 ? 1 : 0),
	);
	cache_write($cache_key, $data);
	return $data;
}

function store_delivery_modes() {
	$data = array(
		'1' => array(
			'css' => 'label label-danger',
			'text' => '店内配送员',
			'color' => ''
		),
		'2' => array(
			'css' => 'label label-success',
			'text' => '平台配送员',
		),
	);
	return $data;
}

function store_fetchall_by_condition($type = 'hot') {
	global $_W;
	if($type == 'hot') {
		$stores = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 order by click desc, displayorder desc limit 4', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	} elseif($type == 'recommend') {
		$stores = pdo_fetchall('select id,title,logo,content,business_hours,delivery_fee_mode,delivery_price,delivery_areas,send_price,delivery_time,forward_mode,forward_url from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 and is_recommend = 1 order by displayorder desc limit 8', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
		if(!empty($stores)) {
			foreach($stores as &$row) {
				$row['activity'] = store_fetch_activity($row['id'], array('discount_status', 'discount_data'));
				$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
				if($row['delivery_fee_mode'] == 2) {
					$row['delivery_price'] = iunserializer($row['delivery_price']);
					$row['delivery_price'] = $row['delivery_price']['start_fee'];
				} elseif($row['delivery_fee_mode'] == 3) {
					$row['delivery_areas'] = iunserializer($row['delivery_areas']);
					if(!is_array($row['delivery_areas'])) {
						$row['delivery_areas'] = array();
					}
					$price = store_order_condition($row);
					$row['delivery_price'] = $price['delivery_price'];
					$row['send_price'] = $price['send_price'];
				}
			}
		}
	}
	return $stores;
}

function store_forward_url($sid, $forward_mode, $forward_url = '') {
	if($forward_mode == 0) {
		$url = imurl('wmall/store/goods', array('sid' => $sid));
	} elseif($forward_mode == 1) {
		$url = imurl('wmall/store/index', array('sid' => $sid));
	} elseif($forward_mode == 3) {
		$url = imurl('wmall/store/assign', array('sid' => $sid));
	} elseif($forward_mode == 4) {
		$url = imurl('wmall/store/reserve', array('sid' => $sid));
	} elseif($forward_mode == 6) {
		$url = imurl('wmall/store/paybill', array('sid' => $sid));
	} elseif($forward_mode == 5) {
		$url = $forward_url;
	}
	return $url;
}

function store_order_serial_sn($store_id){
	global $_W;
	$serial_sn = pdo_fetchcolumn('select serial_sn from' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and addtime > :addtime order by serial_sn desc', array(':uniacid' => $_W['uniacid'], ':sid' => $store_id, ':addtime' => strtotime(date('Y-m-d'))));
	$serial_sn = intval($serial_sn) + 1;
	return $serial_sn;
}

//checkstore
function store_check() {
	global $_W, $_GPC;
	if(!defined('IN_MOBILE')) {
		if(!empty($_GPC['_sid'])) {
			$sid = intval($_GPC['_sid']);
			isetcookie('__sid', $sid, 86400);
		} else {
			$sid = intval($_GPC['__sid']);
		}
	} else {
		$sid = intval($_GPC['sid']);
	}
	if(!defined('IN_MOBILE')) {
		if($_W['role'] != 'manager' && empty($_W['isfounder'])) {
			if($_W['we7_wmall']['store']['id'] != $sid) {
				message('您没有该门店的管理权限', '', 'error');
			}
		}
	}
	$store = pdo_fetch('SELECT id, title, status, pc_notice_status, delivery_mode FROM ' . tablename('tiny_wmall_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
	if(empty($store)) {
		if(!defined('IN_MOBILE')) {
			message('门店信息不存在或已删除', '', 'error');
		}
		exit();
	}
	$store['manager'] = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'role' => 'manager'));
	$store['account'] = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $store['id']));
	$_W['we7_wmall']['store'] = $store;
	return $store;
}

function store_serve_fee_items() {
	return array(
		'yes' => array(
			'price' => '商品费用',
			'box_price' => '餐盒费',
			'pack_fee' => '包装费',
			'delivery_fee' => '配送费',
		),
		'no' => array(
			'store_discount_fee' => '商户活动补贴'
		)
	);
}

function is_in_store_radius($sid, $lnglat, $area_id = 0) {
	global $_W;
	if(is_array($sid)) {
		$store = $sid;
	}
	if(empty($store)) {
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode', 'delivery_areas', 'serve_radius', 'not_in_serve_radius'));
		if(empty($store)) {
			return error(-1, '门店不存在');
		}
	}
	$flag = false;
	if(empty($lnglat[0]) || empty($lnglat[1])) {
		return error(-1, '地址经纬度不完善');
	}
	if($store['delivery_fee_mode'] == 1 || $store['delivery_fee_mode'] == 2) {
		if(!$store['not_in_serve_radius'] && $store['serve_radius'] > 0) {
			$dist = distanceBetween($lnglat[0], $lnglat[1], $store['location_y'], $store['location_x']);
			if($dist <= ($store['serve_radius'] * 1000)) {
				$flag = true;
			}
		} else {
			$flag = true;
		}
	} elseif($store['delivery_fee_mode'] == 3) {
		if(empty($store['delivery_areas'])) {
			return error(-1, '门店没有设置配送区域');
		}
		if(!empty($area_id)) {
			$store['delivery_areas'] = array($store['delivery_areas'][$area_id]);
		}
		foreach($store['delivery_areas'] as $area) {
			$flag = isPointInPolygon($area['path'], array($lnglat[0], $lnglat[1]));
			if($flag) {
				break;
			}
		}
	}
	return $flag;
}

function store_order_condition($sid, $lnglat = array()) {
	global $_GPC;
	if(is_array($sid)) {
		$store = $sid;
	}
	if(empty($store)) {
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode', 'delivery_areas', 'delivery_price', 'delivery_free_price', 'send_price'));
		if(empty($store)) {
			return error(-1, '门店不存在');
		}
	}
	$price = array(
		'send_price' => $store['send_price'],
		'delivery_price' => $store['delivery_price'],
		'delivery_free_price' => $store['delivery_free_price'],
	);
	if($store['delivery_fee_mode'] == 3) {
		if(empty($lnglat)) {
			if($_GPC['address_id'] > 0) {
				$address = member_fetch_address($_GPC['address_id']);
				$lnglat = array($address['location_y'], $address['location_x']);
			} else {
				$lnglat = array($_GPC['__lng'], $_GPC['__lat']);
			}
		}
		$delivery_price_arr = array(0);
		$send_price_arr = array(0);
		foreach($store['delivery_areas'] as $key => $area) {
			$in = isPointInPolygon($area['path'], $lnglat);
			if($in) {
				isetcookie('_guess_area', $key, 300);
				$price['delivery_price'] = $area['delivery_price'];
				$price['send_price'] = $area['send_price'];
				$price['delivery_free_price'] = $area['delivery_free_price'];
				break;
			}
			$delivery_price_arr[] = $area['delivery_price'];
			$send_price_arr[] = $area['send_price'];
		}
		if(!$in) {
			$price['delivery_price'] = max($delivery_price_arr);
			$price['send_price'] = max($send_price_arr);
		}
	}
	return $price;
}

function store_notice_stat($clerk_id = 0) {
	global $_W;
	if(empty($clerk_id)) {
		$clerk_id = $_W['clerk']['id'];
	}
	$new_id = pdo_fetchcolumn('SELECT notice_id FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid ORDER BY notice_id DESC LIMIT 1', array(':uid' => $clerk_id));
	$new_id = intval($new_id);
	$notices = pdo_fetchall('SELECT id FROM ' . tablename('tiny_wmall_notice') . ' WHERE status = 1 AND type = :type AND id > :id', array(':type' => 'store',':id' => $new_id));
	if(!empty($notices)) {
		foreach($notices as &$notice) {
			$insert = array(
				'uid' => $clerk_id,
				'notice_id' => $notice['id'],
				'is_new' => 1,
			);
			pdo_insert('tiny_wmall_notice_read_log', $insert);
		}
	}
	$total = intval(pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid AND is_new = 1', array(':uid' => $clerk_id)));
	return $total;
}




