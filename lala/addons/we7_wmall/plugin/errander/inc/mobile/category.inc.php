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
	$delivery_time = errander_delivery_times($id);
	$time_flag = 0;
	$predict_index = 0;
	$data = array_order(TIMESTAMP, $delivery_time['timestamp']);
	if(!empty($data)) {
		$time_flag = 1;
		$predict_index = array_search($data, $delivery_time['timestamp']);
		$predict_day = $delivery_time['days'][0];
		$predict_time = "{$delivery_time['times'][$predict_index]['start']}~{$delivery_time['times'][$predict_index]['end']}";
		$text_time = "立即配送";
		$predict_extra_price = $delivery_time['times'][$predict_index]['fee'];
	} else {
		$predict_day = $delivery_time['days'][1];
		$predict_times = array_shift($delivery_time['times']);
		$predict_time = "{$predict_times['start']}~{$predict_times['end']}";
		$text_time = "{$predict_day} {$predict_time}";
	}
	$predict_delivery_price = $category['start_fee'] + $delivery_time['times'][$predict_index]['fee'];
	$predict_delivery_price = "配送费{$predict_delivery_price}元起";

	$rule = array(
		'type' => $category['type'],
		'start_fee' => $category['start_fee'],
		'start_km' => $category['start_km'],
		'pre_km_fee' => $category['pre_km_fee'],
		'weight_fee_status' => $category['weight_fee_status'],
		'weight_fee' => $category['weight_fee'],
		'tip_min' => $category['tip_min'],
		'tip_max' => $category['tip_max'],
		'multiaddress' => $category['multiaddress'],
	);
	$filter = array('serve_radius' => 0, 'location_x' => $_config_plugin['map']['location_x'], 'location_y' => $_config_plugin['map']['location_y']);
	$serves = member_fetchall_serve_address($filter);
	$addresses = member_fetchall_address($filter);

	if(!empty($_COOKIE['errander_order'])) {
		$errander_order = json_decode($_COOKIE['errander_order'], true);
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
	include itemplate("category" . ucfirst($category['type']));
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
		'multiaddress' => $_GPC['multiaddress'],
		'thumbs' => $_GPC['thumbs'],
	);
	setcookie('errander_order', json_encode($errander_order), 180 + time());
	exit();
}
