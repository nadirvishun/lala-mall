<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$sid = intval($_GPC['sid']);
$url = iurl('store/order/');
$forward = trim($_GPC['referer']);
$account = store_account($sid);

if (empty($account)) {
	$config_settle = $_W['we7_wmall']['config']['settle'];
	$insert = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'fee_limit' => $config_settle['get_cash_fee_limit'], 'fee_rate' => $config_settle['get_cash_fee_rate'], 'fee_min' => $config_settle['get_cash_fee_min'], 'fee_max' => $config_settle['get_cash_fee_max']);
	pdo_insert('tiny_wmall_store_account', $insert);
}

header('location: ' . $url);
exit();

?>
