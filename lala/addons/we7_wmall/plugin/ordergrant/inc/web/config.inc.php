<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '下单有礼设置';
$config_ordergrant = get_plugin_config('ordergrant');

if ($_W['ispost']) {
	$continuous = array();

	if (!empty($_GPC['continuous_days'])) {
		foreach ($_GPC['continuous_days'] as $key => $row) {
			if (empty($row) || empty($_GPC['continuous_grant'][$key])) {
				continue;
			}

			$continuous[$key] = array('days' => $row, 'grant' => $_GPC['continuous_grant'][$key]);
		}
	}

	$all = array();

	if (!empty($_GPC['all_days'])) {
		foreach ($_GPC['all_days'] as $key => $row) {
			if (empty($row) || empty($_GPC['all_grant'][$key])) {
				continue;
			}

			$all[$key] = array('days' => $row, 'grant' => $_GPC['all_grant'][$key]);
		}
	}

	$special = array();

	if (!empty($_GPC['special_date'])) {
		foreach ($_GPC['special_date'] as $key => $row) {
			if (empty($row) || empty($_GPC['special_grant'][$key])) {
				continue;
			}

			$special[$key] = array('date' => $row, 'title' => $_GPC['special_title'][$key], 'color' => $_GPC['special_color'][$key], 'grant' => $_GPC['special_grant'][$key]);
		}
	}

	$ordergrant_share = array('title' => trim($_GPC['title']), 'desc' => trim($_GPC['desc']), 'imgUrl' => trim($_GPC['imgUrl']));
	$ordergrant = array('status' => intval($_GPC['status']), 'cycle' => intval($_GPC['cycle']), 'grantType' => trim($_GPC['grantType']), 'first_order_grant' => intval($_GPC['first_order_grant']), 'days_order_grant' => intval($_GPC['days_order_grant']), 'continuous' => $continuous, 'special' => $special, 'all' => $all, 'agreement' => htmlspecialchars_decode($_GPC['agreement']), 'ordergrant_share' => $ordergrant_share);

	if (!empty($config_ordergrant['share'])) {
		$ordergrant['share'] = $config_ordergrant['share'];
	}

	set_plugin_config('ordergrant', $ordergrant);
	imessage(error(0, '下单有礼活动设置成功'), 'refresh', 'ajax');
}

include itemplate('config');

?>
