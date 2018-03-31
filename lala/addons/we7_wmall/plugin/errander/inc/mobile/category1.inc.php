<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$id = intval($_GPC['id']);
	$category = errander_category_fetch($id);
	if(empty($category)) {
		imessage('跑腿类型不存在', imurl('errander/index'), 'error');
	}
	if(empty($category['status'])) {
		imessage('该跑腿类型已关闭', imurl('errander/index'), 'info');
	}
	$types = errander_types();
	$_W['page']['title'] = $types[$category['type']]['text'];
	$title = "{$types[$category['type']]['text']} - {$category['title']}";

	$rule = array(
		'start_fee' => $category['start_fee'],
		'start_km' => $category['start_km'],
		'pre_km_fee' => $category['pre_km_fee'],
		'tip_min' => $category['tip_min'],
		'tip_max' => $category['tip_max']
	);
	$filter = array('serve_radius' => $_config_plugin['serve_radius'], 'location_x' => $_config_plugin['map']['location_x'], 'location_y' => $_config_plugin['map']['location_y']);
	$serves = member_fetchall_serve_address($filter);
	$addresses = member_fetchall_address($filter);

	if(!empty($_COOKIE['errander_order'])) {
		$errander_order = json_decode($_COOKIE['errander_order'], true);
		setcookie('errander_order', 0, -1000);
	}
	$start_address_id = intval($_GPC['start_address_id']) ? intval($_GPC['start_address_id']) : $errander_order['start_address_id'];
	if($start_address_id > 0) {
		$start_address = member_fetch_address($start_address_id);
	}
	$end_address_id = intval($_GPC['end_address_id']) ? intval($_GPC['end_address_id']) : $errander_order['end_address_id'];
	if($end_address_id > 0) {
		$end_address = member_fetch_address($end_address_id);
	}
	$payment = get_available_payment('errander');
	$pay_types = order_pay_types();
	$price_select = array(
		array('id' => 1, 'title' => '100元以下'),
		array('id' => 2, 'title' => '100元-200元'),
		array('id' => 3, 'title' => '200元-300元'),
		array('id' => 4, 'title' => '300元-400元'),
		array('id' => 5, 'title' => '400元-500元'),
	);
	$groupid = $_W['member']['groupid'];
	if($groupid > 0) {
		$member_group = pdo_get('tiny_wmall_member_groups', array('uniacid' => $_W['uniacid'], 'id' => $groupid));
	}
	$agreement_errander = get_config_text('agreement_errander');
	include itemplate("category" . ucfirst($category['type'] . 1));
}

if($op == 'cart') {
	setcookie('errander_order', 0, -1000);
	$errander_order = array(
		'goods_name' => trim($_GPC['goods_name']),
		'goods_weight' => trim($_GPC['goods_weight']),
		'goods_price' => trim($_GPC['goods_price']),
		'note' => trim($_GPC['note']),
		'delivery_tips' => trim($_GPC['delivery_tips']),
		'is_anonymous' => trim($_GPC['is_anonymous']),
		'start_address_id' => trim($_GPC['start_address_id']),
		'end_address_id' => trim($_GPC['end_address_id']),
	);
	setcookie('errander_order', json_encode($errander_order), 600 + time());
	exit();
}

if($op == 'addaddress') {
	$_W['page']['title'] = '新增收货地址';
	$cid = intval($_GPC['id']);
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
			'address' => trim($_GPC['address']),
			'number' => trim($_GPC['number']),
			'location_x' => trim($_GPC['location_x']),
			'location_y' => trim($_GPC['location_y']),
			'type' => 1
		);
		pdo_insert('tiny_wmall_address', $data);
		imessage(error(0, '保存地址成功'), '', 'ajax');
	}
	include itemplate('addAddress');
}

if($op == 'suggestion') {
	load()->func('communication');
	$key = trim($_GPC['key']);
	$config = $_W['we7_wmall']['config'];
	$query = array(
		'keywords' => $key,
		'city' => '全国',
		'output' => 'json',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
	);
	if(!empty($config['takeout']['range']['city'])) {
		$query['city'] = $config['takeout']['range']['city'];
	}
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/assistant/inputtips?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if(!empty($result['tips'])) {
		$distance_sort = 0;
		foreach($result['tips'] as $key => &$val) {
			$val['distance'] = 10000000;
			$val['distance_available'] = 0;
			$val['address_available'] = 1;
			if(is_array($val['location'])) {
				unset($val[$key]);
			} else {
				$location = explode(',', $val['location']);
				$val['lng'] = $location[0];
				$val['lat'] = $location[1];
			}
			if(!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			} else {
				$val['address'] = $val['district'];
			}
		}
	}
	imessage(error(0, $result['tips']), '', 'ajax');
}