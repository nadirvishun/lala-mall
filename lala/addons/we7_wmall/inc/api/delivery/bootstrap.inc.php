<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC, $_POST;
mload()->model('common');
mload()->model('store');
mload()->model('order');
mload()->model('deliveryer');

if(in_array($do, array('login'))) {
	$result = api_check_sign($_POST, $_POST['sign']);
	if(!$result) {
		//message(ierror(-1, '签名错误'), '', 'ajax');
	}
} else {
	$token = trim($_POST['token']);
	if(empty($token)) {
		message(ierror(-1, '身份验证失败, 请重新登陆'), '', 'ajax');
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'token' => $token));
	if(empty($deliveryer)) {
		message(ierror(-1, '身份验证失败, 请重新登陆'), '', 'ajax');
	}

	$sids = pdo_fetchall('select sid from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and sid = 0', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer['id']), 'sid');
	$sids = array_unique(array_keys($sids));
	if(empty($sids)) {
		message(ierror(-1, '您没有抢单的权限, 请联系平台管理员分配接单权限'), '', 'ajax');
	}
	$_W['we7_wmall']['deliveryer']['user'] = $deliveryer;
	$_W['we7_wmall']['deliveryer']['store'] = $sids;
	$_W['we7_wmall']['deliveryer']['type'] = 1;
}

$_W['we7_wmall']['config'] = get_system_config();
$config_takeout = $_W['we7_wmall']['config']['takeout'];
$config_delivery = $_W['we7_wmall']['config']['delivery'];
$config_errander = get_plugin_config('errander');

