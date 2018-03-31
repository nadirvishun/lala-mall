<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$config_ordergrant = get_plugin_config('ordergrant');
$config_share = $config_ordergrant['share'];

if ($_GPC['ac'] == 'share') {
	if ($config_share['status'] == 0) {
		imessage('该活动未开启', '', 'info');
		return 1;
	}
}
else {
	if ($config_ordergrant['status'] == 0) {
		imessage('该活动未开启', '', 'info');
	}

	$order_days_amount = ordergrant_member_init();
}

?>
