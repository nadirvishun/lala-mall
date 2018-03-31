<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'settle';
if($op == 'settle') {
	$_W['page']['title'] = '商户入驻';
	if($_W['ispost']) {
		$settle = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'mobile_verify_status' => intval($_GPC['mobile_verify_status']),
			'store_label_new' => intval($_GPC['store_label_new']),
			'custom_goods_sailed_status' => intval($_GPC['custom_goods_sailed_status']),
			'self_audit_comment' => intval($_GPC['self_audit_comment']),
		);
		set_config_text('agreement_settle', htmlspecialchars_decode($_GPC['agreement_settle']));
		set_system_config('store.settle', $settle);
		imessage(error(0, '商户入驻设置成功'), referer(), 'ajax');
	}
	$settle = $_config['store']['settle'];
	$settle['agreement_settle'] = get_config_text('agreement_settle');
	include itemplate('config/settle');
}

if($op == 'serve_fee') {
	$_W['page']['title'] = '服务费率';
	$serve_fee = $_config['store']['serve_fee'];
	if($_W['ispost']) {
		$fee_takeout = $serve_fee['fee_takeout'];

		$takeout_GPC = $_GPC['fee_takeout'];
		$fee_takeout['type'] = intval($takeout_GPC['type']) ? intval($takeout_GPC['type']) : 1;
		if($fee_takeout['type'] == 2) {
			$fee_takeout['fee'] = floatval($takeout_GPC['fee']);
		} else {
			$fee_takeout['fee_rate'] = floatval($takeout_GPC['fee_rate']);
			$items_yes = array_filter($takeout_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_takeout['items_yes'] = $items_yes;
			$items_no = array_filter($takeout_GPC['items_no'], trim);
			$fee_takeout['items_no'] = $items_no;
		}

		$fee_selfDelivery = $serve_fee['fee_selfDelivery'];

		$selfDelivery_GPC = $_GPC['fee_selfDelivery'];
		$fee_selfDelivery['type'] = intval($selfDelivery_GPC['type']) ? intval($selfDelivery_GPC['type']) : 1;
		if($fee_selfDelivery['type'] == 2) {
			$fee_selfDelivery['fee'] = floatval($selfDelivery_GPC['fee']);
		} else {
			$fee_selfDelivery['fee_rate'] = floatval($selfDelivery_GPC['fee_rate']);
			$items_yes = array_filter($selfDelivery_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_selfDelivery['items_yes'] = $items_yes;
			$items_no = array_filter($selfDelivery_GPC['items_no'], trim);
			$fee_selfDelivery['items_no'] = $items_no;
		}

		$fee_instore = $serve_fee['fee_instore'];

		$instore_GPC = $_GPC['fee_instore'];
		$fee_instore['type'] = intval($instore_GPC['type']) ? intval($instore_GPC['type']) : 1;
		if($fee_instore['type'] == 2) {
			$fee_instore['fee'] = floatval($instore_GPC['fee']);
		} else {
			$fee_instore['fee_rate'] = floatval($instore_GPC['fee_rate']);
			$items_yes = array_filter($instore_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_instore['items_yes'] = $items_yes;
			$items_no = array_filter($instore_GPC['items_no'], trim);
			$fee_instore['items_no'] = $items_no;
		}

		$fee_paybill = $serve_fee['fee_paybill'];

		$paybill_GPC = $_GPC['fee_paybill'];
		$fee_paybill['type'] = intval($paybill_GPC['type']) ? intval($paybill_GPC['type']) : 1;
		if($fee_paybill['type'] == 2) {
			$fee_paybill['fee'] = floatval($paybill_GPC['fee']);
		} else {
			$fee_paybill['fee_rate'] = floatval($paybill_GPC['fee_rate']);
		}

		$serve_fee = array(
			'fee_takeout' => $fee_takeout,
			'fee_selfDelivery' => $fee_selfDelivery,
			'fee_instore' => $fee_instore,
			'fee_paybill' => $fee_paybill,
			'get_cash_fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'get_cash_fee_rate' => trim($_GPC['get_cash_fee_rate']),
			'get_cash_fee_min' => intval($_GPC['get_cash_fee_min']),
			'get_cash_fee_max' => intval($_GPC['get_cash_fee_max']),
			'store_label_new' => intval($_GPC['store_label_new']),
		);
		set_system_config('store.serve_fee', $serve_fee);
		$sync = intval($_GPC['sync']);
		if($sync == 1) {
			$update = array(
				'fee_takeout' => iserializer($fee_takeout),
				'fee_selfDelivery' => iserializer($fee_selfDelivery),
				'fee_instore' => iserializer($fee_instore),
				'fee_paybill' => iserializer($fee_paybill),
				'fee_rate' => $serve_fee['get_cash_fee_rate'],
				'fee_min' => $serve_fee['get_cash_fee_min'],
				'fee_max' => $serve_fee['get_cash_fee_max'],
				'fee_limit' => $serve_fee['get_cash_fee_limit'],
			);
			pdo_update('tiny_wmall_store_account', $update, array('uniacid' => $_W['uniacid']));
		}
		imessage(error(0, '商户服务费率设置成功'), referer(), 'ajax');
	}
	$serve_fee = $_config['store']['serve_fee'];
	include itemplate('config/serve_fee');
}

if($op == 'delivery') {
	$_W['page']['title'] = '配送模式';
	if($_W['ispost']) {
		if(empty($_GPC['times']['start'])) {
			imessage(error(-1, '请先生成配送时间段'), '', 'ajax');
		}
		$delivery = array(
			'delivery_mode' => intval($_GPC['delivery_mode']),
			'delivery_fee_mode' => intval($_GPC['delivery_fee_mode']),
			'delivery_fee' => floatval($_GPC['delivery_fee']),
			'pre_delivery_time_minute' => intval($_GPC['pre_delivery_time_minute']),
		);
		if($delivery['delivery_fee_mode'] == 2) {
			$delivery['delivery_fee'] = array(
				'start_fee' => trim($_GPC['start_fee']),
				'start_km' => trim($_GPC['start_km']),
				'pre_km_fee' => trim($_GPC['pre_km_fee']),
			);
		}
		set_system_config('store.delivery', $delivery);
		$times = array();
		if(!empty($_GPC['times']['start'])) {
			foreach($_GPC['times']['start'] as $key => $val) {
				$start = trim($val);
				$end = trim($_GPC['times']['end'][$key]);
				if(empty($start) || empty($end)) {
					continue;
				}
				$times[] = array(
					'start' => $start,
					'end' => $end,
					'status' => intval($_GPC['times']['status'][$key]),
					'fee' => intval($_GPC['times']['fee'][$key])
				);
			}
		}
		set_config_text('takeout_delivery_time', iserializer($times));

		$delivery_sync = intval($_GPC['delivery_sync']);
		if($delivery_sync == 1) {
			$update = array(
				'delivery_mode' => $delivery['delivery_mode'],
				'delivery_fee_mode' => $delivery['delivery_fee_mode'],
				'delivery_price' => $delivery['delivery_fee'],
				'delivery_free_price' => 0,
				'delivery_times' => iserializer($times)
			);
			if($delivery['delivery_fee_mode'] == 2) {
				$update['delivery_price'] = iserializer($delivery['delivery_fee']);
				$update['not_in_serve_radius'] = 1;
				$update['auto_get_address'] = 1;
			}
			pdo_update('tiny_wmall_store', $update, array('uniacid' => $_W['uniacid']));
			$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id'));
			foreach($stores as $store) {
				store_delivery_times($store['id'], true);
			}
		}
		imessage(error(0, '配送模式设置成功'), referer(), 'ajax');
	}
	$delivery = $_config['store']['delivery'];
	$delivery_times = get_config_text('takeout_delivery_time');
	include itemplate('config/delivery');
}