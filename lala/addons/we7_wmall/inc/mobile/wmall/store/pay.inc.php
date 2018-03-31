<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$id = intval($_GPC['id']);
$type = trim($_GPC['order_type']);
if(empty($id) || empty($type)) {
	imessage('参数错误', '', 'error');
}
$tables = array(
	'order' => 'tiny_wmall_order',
	'card' => 'tiny_wmall_delivery_cards_order',
	'errander' => 'tiny_wmall_errander_order'
);

$titles = array(
	'order' => "{$_W['account']['name']}-外卖订单支付",
	'card' => "{$_W['account']['name']}-配送会员卡",
	'errander' => "{$_W['account']['name']}-随意购",
);
$order = pdo_get($tables[$type], array('uniacid' => $_W['uniacid'], 'id' => $id));
if(empty($order)) {
	imessage('订单不存在或已删除', '', 'error');
}
if(!empty($order['is_pay'])) {
	imessage('该订单已付款', '', 'info');
}
if($type == 'order' && $order['status'] == 6) {
	imessage('订单已取消', imurl('wmall/order/index'), 'info');
}
if($order['final_fee'] == 0) {
	imessage('订单支付成功', '', 'success');
}

$record = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'order_id' => $id, 'order_type' => $type));
if(empty($record)) {
	$record = array(
		'uniacid' => $_W['uniacid'],
		'order_sn' => $order['ordersn'] ? $order['ordersn'] : $order['order_sn'], //这个是唯一的.
		'order_id' => $id,
		'order_type' => $type,
		'fee' => $order['final_fee'],
		'status' => 0,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_paylog', $record);
} else {
	if($record['status'] == 1) {
		imessage('该订单已支付,请勿重复支付', '', 'error');
	}
}

$params = array(
	'module' => 'we7_wmall',
	'ordersn' => $record['order_sn'],
	'tid' => $record['order_sn'],
	'user' => $_W['member']['uid'],
	'fee' => $record['fee'],
	'title' => $titles[$record['order_type']],
);

$log = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => $params['module'], 'tid' => $params['tid']));
if(empty($log)) {
	$log = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'openid' => $params['user'],
		'module' => $params['module'],
		'tid' => $params['tid'],
		'fee' => $params['fee'],
		'card_fee' => $params['fee'],
		'status' => '0',
		'is_usecard' => '0',
	);
	pdo_insert('core_paylog', $log);
}
if($log['status'] == 1) {
	imessage('该订单已支付,请勿重复支付', '', 'error');
}

$available_pay_types = $_W['we7_wmall']['config']['payment'];
unset($available_pay_types['alipay_cert'], $available_pay_types['wechat_cert']);
if($record['order_type'] == 'order') {
	$sid = $order['sid'];
	$store_payment = store_fetch($sid, array('payment'));
	foreach($available_pay_types as $key => $row) {
		if($row != 1 || !in_array($key, $store_payment['payment'])) {
			unset($available_pay_types[$key]);
		}
	}
} elseif($record['order_type'] == 'card' || $record['order_type'] == 'errander') {
	unset($available_pay_types['delivery']);
}
$payment = $available_pay_types;

$pay_type = !empty($_GPC['pay_type']) ? trim($_GPC['pay_type']) : $order['pay_type'];
if($pay_type && !$_GPC['type'] && $available_pay_types[$pay_type] == 1) {
	$params = base64_encode(json_encode($params));
	header('location:' . murl("mc/cash/{$pay_type}" , array('params' => $params)));
	die;
} else {
	if(empty($available_pay_types)) {
		imessage('没有有效的支付方式, 请联系网站管理员.');
	}
	if(empty($_W['member']['uid'])) {
		$payment['credit'] = false;
	}
	if (!empty($payment['credit'])) {
		$credtis = mc_credit_fetch($_W['member']['uid']);
	}
	include itemplate('public/paycenter');
}

