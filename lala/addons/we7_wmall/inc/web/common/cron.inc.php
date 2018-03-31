<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('cron');
mload()->model('clerk');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'task');
set_time_limit(0);

if ($op == 'task') {
	cron_order();
	exit('success');
}

if ($op == 'order_notice') {
	clerk_info_init();
	if (($_GPC['_ac'] == 'takeout') && $_GPC['_status_order_notice']) {
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));

		if (!empty($order)) {
			exit('success');
		}

		exit('error');
		return 1;
	}

	if (($_GPC['_ctrl'] == 'errander') && ($_GPC['_ac'] == 'order') && $_GPC['_status_errander_notice']) {
		$order = pdo_get('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));

		if (!empty($order)) {
			exit('success');
		}

		exit('error');
		return 1;
	}

	if (($_GPC['_ctrl'] == 'store') && ($_GPC['_ac'] == 'order') && $_GPC['_status_store_order_notice']) {
		$sid = intval($_GPC['__sid']);
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1, 'is_pay' => 1));

		if (!empty($order)) {
			exit('success');
		}

		exit('error');
	}
}

?>
