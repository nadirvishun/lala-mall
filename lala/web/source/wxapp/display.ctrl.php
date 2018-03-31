<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('wxapp');
load()->model('account');

$_W['page']['title'] = '小程序列表';

$dos = array('display', 'switch', 'rank', 'home');
$do = in_array($do, $dos) ? $do : 'display';
if ($do == 'rank' || $do == 'switch') {
	$uniacid = intval($_GPC['uniacid']);
	if (!empty($uniacid)) {
		$wxapp_info = wxapp_fetch($uniacid);
		if (empty($wxapp_info)) {
			itoast('小程序不存在', referer(), 'error');
		}
	}
}
if ($do == 'home') {
	$last_uniacid = uni_account_last_switch();
	$url = url('wxapp/display');
	if (empty($last_uniacid)) {
		itoast('', $url, 'info');
	}
	if (!empty($last_uniacid) && $last_uniacid != $_W['uniacid']) {
		wxapp_switch($last_uniacid);
	}
	$permission = permission_account_user_role($_W['uid'], $last_uniacid);
	if (empty($permission)) {
		itoast('', $url, 'info');
	}
	$last_version = wxapp_fetch($last_uniacid);
	if (!empty($last_version)) {
		$url = url('wxapp/version/home', array('version_id' => $last_version['version']['id']));
	}
	itoast('', $url, 'info');
} elseif ($do == 'display') {
		$account_info = permission_user_account_num();

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$account_table = table('account');
	$account_table->searchWithType(array(ACCOUNT_TYPE_APP_NORMAL));

	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)) {
		$account_table->searchWithKeyword($keyword);
	}

	$account_table->accountRankOrder();
	$account_table->searchWithPage($pindex, $psize);
	$wxapp_lists = $account_table->searchAccountList();
	$total = $account_table->getLastQueryTotal();

	if (!empty($wxapp_lists)) {
		foreach ($wxapp_lists as &$account) {
			$account = uni_fetch($account['uniacid']);
			$account['versions'] = wxapp_get_some_lastversions($account['uniacid']);
			if (!empty($account['versions'])) {
				foreach ($account['versions'] as $version) {
					if (!empty($version['current'])) {
						$account['current_version'] = $version;
					}
				}
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	template('wxapp/account-display');
} elseif ($do == 'switch') {
	$module_name = trim($_GPC['module']);
	$version_id = !empty($_GPC['version_id']) ? intval($_GPC['version_id']) : $wxapp_info['version']['id'];
	if (!empty($module_name) && !empty($version_id)) {
		$version_info = wxapp_version($version_id);
		$module_info = array();
		if (!empty($version_info['modules'])) {
			foreach ($version_info['modules'] as $key => $module_val) {
				if ($module_val['name'] == $module_name) {
					$module_info = $module_val;
				}
			}
		}
		if (empty($version_id) || empty($module_info)) {
			itoast('版本信息错误');
		}
		$url = url('home/welcome/ext/', array('m' => $module_name, 'version_id' => $version_id));
		if (!empty($module_info['account']['uniacid'])) {
			uni_account_switch($module_info['account']['uniacid'], $url);
		} else {
			wxapp_switch($version_info['uniacid'], $url);
		}
	}
	wxapp_update_last_use_version($uniacid, $version_id);
	wxapp_switch($uniacid, url('wxapp/version/home', array('version_id' => $version_id)));
	exit;
} elseif ($do == 'rank') {
	uni_account_rank_top($uniacid);
	itoast('更新成功', '', '');
}