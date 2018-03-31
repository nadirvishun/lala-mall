<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function paybill_order_fetch($id) {
	global $_W;
	$order = pdo_get('tiny_wmall_paybill_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(!empty($order)) {
		if(empty($order['is_pay'])) {
			$order['pay_type_cn'] = '未支付';
		} else {
			$pay_types = order_pay_types();
			$order['pay_type_cn'] = !empty($pay_types[$order['pay_type']]['text']) ? $pay_types[$order['pay_type']]['text'] : '其他支付方式';
		}
	}
	return $order;
}

function paybill_order_status_update($id, $type, $extra = array()) {
	global $_W;
	$order = paybill_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'pay_type' => $extra['type'],
			'final_fee' => $extra['card_fee'],
			'paytime' => TIMESTAMP,
			'transaction_id' => $extra['transaction_id'],
			'out_trade_no' => $extra['uniontid'],
		);
		pdo_update('tiny_wmall_paybill_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		store_update_account($order['sid'], $order['store_final_fee'], 4, $order['id']);
		paybill_order_status_notice($order['id'], 'pay');
		paybill_order_clerk_notice($order['id'], 'pay');
	}
}

function paybill_order_status_notice($orderOrid, $type, $extra = array()) {
	global $_W;
	$order = $orderOrid;
	if(!is_array($orderOrid)) {
		$order = paybill_order_fetch($orderOrid);
	}
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$store = store_fetch($order['sid'], array('title'));
	if(!empty($order['openid'])) {
		$acc = WeAccount::create($order['acid']);
		if($type == 'pay') {
			$title = '您的订单已付款';
			$remark = array(
				"门店名称: {$store['title']}",
				"支付方式: {$order['pay_type_cn']}",
				"支付时间: " . date('m-d H:i:s', $order['paytime']),
				"订单金额: ￥{$order['total_fee']}",
				"优惠金额: ￥{$order['discount_fee']}",
				"实付金额: ￥{$order['final_fee']}",
			);
		}
		if(!empty($extra)) {
			$remark = array_merge($remark, $extra);
		}
		if(is_array($remark)) {
			$remark = implode("\n", $remark);
		}
		$send = tpl_format($title, $order['order_sn'], '已支付', $remark);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '买单订单状态改变微信通知顾客', $send, $status['message']);
		}
	}
	return true;
}

function paybill_order_clerk_notice($orderOrid, $type, $extra = array()) {
	global $_W;
	$order = $orderOrid;
	if(!is_array($orderOrid)) {
		$order = paybill_order_fetch($orderOrid);
	}
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$store = store_fetch($order['sid'], array('title', 'id', 'push_token'));
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if(empty($clerks)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if($type == 'pay') {
		$title = "店铺{$store['title']}有新的买单订单啦,订单号:#{$order['serial_sn']}";
		$remark = array(
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('m-d H:i:s', $order['paytime']),
			"订单金额: ￥{$order['total_fee']}",
			"优惠金额: ￥{$order['discount_fee']}",
			"实付金额: ￥{$order['final_fee']}",
		);
	}
	if(!empty($extra)) {
		$remark = array_merge($remark, $extra);
	}
	if(is_array($remark)) {
		$remark = implode("\n", $remark);
	}
	$url = imurl('manage/paycenter/paybill/detail', array('id' => $order['id'], 'sid' => $order['sid']), true);
	$send = tpl_format($title, $order['order_sn'], '已支付', $remark, $url);
	foreach($clerks as $clerk) {
		$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '买单订单状态变动微信通知商户', $send, $status['message']);
		}
	}
	if(in_array($type, array('pay'))) {
		$audience = array(
			'tag' => array(
				$store['push_token']
			)
		);
		$data = Jpush_clerk_send('您的店铺有新的顾客买单啦', $title, array('voice_text' => $title, 'url' => $url, 'notify_type' => $type), $audience);
	}
	return true;
}

function paybill_order_serial_sn($store_id){
	global $_W;
	$serial_sn = pdo_fetchcolumn('select serial_sn from' . tablename('tiny_wmall_paybill_order') . ' where uniacid = :uniacid and sid = :sid and addtime > :addtime order by serial_sn desc', array(':uniacid' => $_W['uniacid'], ':sid' => $store_id, ':addtime' => strtotime(date('Y-m-d'))));
	$serial_sn = intval($serial_sn) + 1;
	return $serial_sn;
}

function paybill_order_update_bill($orderOrid){
	global $_W;
	if(!is_array($orderOrid)){
		$order = pdo_get('tiny_wmall_paybill_order', array('uniacid' => $_W['uniacid'], 'id' => $orderOrid));
	} else {
		$order = $orderOrid;
	}
	if(empty($order)){
		return error(-1, '订单不存在或已删除');
	}
	$account = store_account($order['sid'], 'fee_paybill');
	if($account['fee_paybill']['type'] == 1) {
		$fee_rate = $account['fee_paybill']['fee_rate'];
		$plateform_serve_fee = ($fee_rate / 100) * $order['final_fee'];
		$text = "{$order['total_fee']} x {$fee_rate}%";
		$plateform_serve = array(
			'type' => 1,
			'fee_rate' => $fee_rate,
			'fee' => $plateform_serve_fee,
			'note' => $text,
		);
	} else {
		$plateform_serve_fee = $account['fee_paybill']['fee'];
		$plateform_serve = array(
			'type' => 2,
			'fee_rate' => 0,
			'fee' => $account['fee_paybill']['fee'],
			'note' => "每单固定{$account['fee_paybill']['fee']}元",
		);
	}
	$store_final_fee = round($order['final_fee'] - $plateform_serve_fee, 2);
	if($_W['is_agent']){
		$account_agent = get_agent($order['agentid'], 'fee');
		$agent_fee_config = $account_agent['fee']['fee_paybill'];  
		if($agent_fee_config['type'] == 2){  
			$agent_serve_fee = $agent_fee_config['fee']; 
			$agent_serve = array(
				'fee_type' => 2,
				'fee_rate' => 0,
				'fee' => $agent_serve_fee,
				'note' => "每单固定{$agent_serve_fee}元"
			);
		} else { 
			$agent_serve_rate = floatval($agent_fee_config['fee_rate']);  
			$agent_serve_fee = round($order['final_fee'] * ($agent_serve_rate / 100), 2);
			$text = "{$order['total_fee']} x {$agent_serve_rate}%";
			$agent_serve = array(
				'fee_type' => 1,
				'fee_rate' => $agent_serve_rate,
				'fee' => $agent_serve_fee,
				'note' => $text
			);
		}
		$agent_final_fee = $plateform_serve_fee - $agent_serve_fee;	
	}
	$date = array(
		'agent_final_fee' => $agent_final_fee,
		'agent_serve' => iserializer($agent_serve),
		'agent_serve_fee' => $agent_serve_fee,
		'plateform_serve' => iserializer($plateform_serve),
		'plateform_serve_fee' => $plateform_serve_fee,
		'store_final_fee' => $store_final_fee
	);
	pdo_update('tiny_wmall_paybill_order', $date, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	return true;
}




