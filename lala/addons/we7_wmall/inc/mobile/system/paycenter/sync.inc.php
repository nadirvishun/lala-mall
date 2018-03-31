<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC['ta']);

if ($ta == 'qianfan') {
	$tid = trim($_GPC['tid']);
	$order_id = trim($_GPC['order_id']);
	$log = pdo_get('tiny_wmall_paylog', array('order_sn' => $tid));

	if (empty($log)) {
		message(error(-1, '交易记录不存在'), '', 'ajax');
	}

	$log = pdo_get('tiny_wmall_paylog', array('order_sn' => $tid, 'out_trade_order_id' => $order_id));

	if (!empty($log)) {
		message(error(-1, '交易记录重复'), '', 'ajax');
	}

	$update = array('out_trade_order_id' => $order_id);
	pdo_update('tiny_wmall_paylog', $update, array('order_sn' => $tid));
	message(error(0, ''), '', 'ajax');
}

?>
