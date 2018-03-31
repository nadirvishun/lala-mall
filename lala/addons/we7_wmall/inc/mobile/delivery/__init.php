<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
if($_W['_action'] != 'auth') {
	icheckdeliveryer();
	$sids = pdo_fetchall('select sid from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (sid = 0 or (delivery_type = 1 and sid > 0))', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $_W['deliveryer']['id']), 'sid');
	$sids = array_unique(array_keys($sids));
	if(empty($sids)) {
		imessage('您已申请过配送员,请联系平台管理员或店铺管理员分配接单权限', '', 'info');
	}
	$_deliveryer = $deliveryer = $_W['deliveryer'];
	$_stores = $sids;
	$_stores_cn = implode(', ', $sids);
	$_deliveryer_type = 1; //平台配送员.
	if(!in_array(0, $sids)) {
		$_deliveryer_type = 2; //店内配送员
	} else {
		if(count($sids) > 1) {
			$_deliveryer_type = 3;
		}
	}
}
$config_takeout = $_W['we7_wmall']['config']['takeout'];
$config_delivery = $_W['we7_wmall']['config']['delivery'];

$errander_perm = check_plugin_perm('errander');
if($errander_perm) {
	$config_errander = get_plugin_config('errander');
}
$_W['role'] = 'deliveryer';
$_W['role_cn'] = "配送员:{$_W['deliveryer']['title']}";
