<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');
load()->model('wxapp');

$dos = array('display', 'switch', 'getall_last_switch', 'have_permission_uniacids', 'accounts_dropdown_menu');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'display') {
	$user_module = array();
	if (!$_W['isfounder']) {
		$account_table = table('account');
		$user_table = table('users');
		$user_owned_account = $account_table->userOwnedAccount($_W['uid']);
		if (!empty($user_owned_account) && is_array($user_owned_account)) {
			foreach ($user_owned_account as $uniacid => $account) {
				$account_module = uni_modules_by_uniacid($uniacid);
				$account_user_module = $user_table->userPermission($_W['uid'], $uniacid);
				if (!empty($account_user_module) && is_array($account_user_module)) {
					$account_module = array_intersect_key($account_module, $account_user_module);
				}
				$user_module = array_merge($user_module, $account_module);
			}
		}
	} else {
		$user_module = user_modules($_W['uid']);
	}
	foreach ($user_module as $key => $module_value) {
		if (!empty($module_value['issystem'])) {
			unset($user_module[$key]);
		}
	}
	template('module/display');
}

if ($do == 'switch') {
	$module_name = trim($_GPC['module_name']);
	$module_info = module_fetch($module_name);
	$uniacid = intval($_GPC['uniacid']);
	$version_id = intval($_GPC['version_id']);
	if (empty($module_name) || empty($module_info)) {
		itoast('模块不存在或已经删除！', referer(), 'error');
	}
	if (empty($uniacid) && empty($version_id)) {
		$last_module_info = module_last_switch($module_name);
		if (empty($last_module_info)) {
			$accounts_list = module_link_uniacid_fetch($_W['uid'], $module_name);
			$current_account = current($accounts_list);
			$uniacid = $current_account['uniacid'];
			$version_id = $current_account['version_id'];
		} else {
			$uniacid = $last_module_info['uniacid'];
			$version_id = $last_module_info['version_id'];
		}
	}
	if (empty($uniacid) && empty($version_id)) {
		itoast('该模块暂无可用的公众号或小程序，请先分配权限', url('module/display'), 'info');
	}

	module_save_switch($module_name, $uniacid, $version_id);
	if (!empty($version_id)) {
		$version_info = wxapp_version($version_id);
	}
	if (empty($uniacid) && !empty($version_id)) {
		wxapp_save_switch($version_info['uniacid']);
		wxapp_update_last_use_version($version_info['uniacid'], $version_id);
		itoast('', url('wxapp/display/switch', array('module' => $module_name, 'version_id' => $version_id)), 'success');
	}
	if (!empty($uniacid)) {
		if (empty($version_id)) {
			itoast('', url('account/display/switch', array('uniacid' => $uniacid, 'module_name' => $module_name)), 'success');
		}
		if ($version_info['uniacid'] != $uniacid) {
			itoast('', url('account/display/switch', array('uniacid' => $uniacid, 'module_name' => $module_name, 'version_id' => $version_id)), 'success');
		} else {
			wxapp_save_switch($version_info['uniacid']);
			wxapp_update_last_use_version($version_info['uniacid'], $version_id);
			itoast('', url('wxapp/display/switch', array('module' => $module_name, 'version_id' => $version_id)), 'success');
		}
	}
}

if ($do == 'getall_last_switch') {
	set_time_limit(0);
	$user_module = user_modules($_W['uid']);
	$result = array();
	foreach ($user_module as $module_value) {
		$last_module_info = module_last_switch($module_value['name']);
		if (empty($last_module_info)) {
			$accounts_list = module_link_uniacid_fetch($_W['uid'], $module_value['name']);
			$current_account = current($accounts_list);
			$result[$module_value['name']] = array(
				'app_name' => $current_account['app_name'],
				'wxapp_name' => $current_account['wxapp_name'],
			);
			continue;
		}
		$account_info = uni_fetch($last_module_info['uniacid']);
		if ($account_info['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$result[$module_value['name']] = array(
				'app_name' => '',
				'wxapp_name' => $account_info['name']
			);
			continue;
		}
		if (!empty($last_module_info['version_id'])) {
			$version_info = wxapp_version($last_module_info['version_id']);
			$account_wxapp_info = wxapp_fetch($version_info['uniacid']);
			$result[$module_value['name']] = array(
				'app_name' => $account_info['name'],
				'wxapp_name' => $account_wxapp_info['name']
			);
		} else {
			$result[$module_value['name']] = array(
				'app_name' => $account_info['name'],
				'wxapp_name' => ''
			);
		}
	}
	iajax(0, $result);
}

if ($do == 'have_permission_uniacids') {
	$module_name = trim($_GPC['module_name']);
	$accounts_list = module_link_uniacid_fetch($_W['uid'], $module_name);
	iajax(0, $accounts_list);
}

if ($do == 'accounts_dropdown_menu') {
	$module_name = trim($_GPC['module_name']);
	if (empty($module_name)) {
		exit();
	}
	$last_module_info = module_last_switch($module_name);
	$accounts_list = module_link_uniacid_fetch($_W['uid'], $module_name);
	if (empty($accounts_list)) {
		exit();
	}
	$selected_account = array();
	foreach ($accounts_list as $account) {
		if (empty($account['uniacid']) || $account['uniacid'] != $_W['uniacid']) {
			continue;
		}
		if (in_array($_W['account']['type'], array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH))) {
			if (!empty($account['version_id'])) {
				$version_info = wxapp_version($account['version_id']);
				$account['version_info'] = $version_info;
			}
			$selected_account = $account;
			break;
		} elseif ($_W['account']['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$version_info = wxapp_version($account['version_id']);
			$account['version_info'] = $version_info;
			$selected_account = $account;
			break;
		}
	}
	foreach ($accounts_list as $key => $account) {
		$url = url('module/display/switch', array('uniacid' => $account['uniacid'], 'module_name' => $module_name));
		if (!empty($account['version_id'])) {
			$url .= '&version_id=' . $account['version_id'];
		}
		$accounts_list[$key]['url'] = $url;
	}
	echo template('module/dropdown-menu');
	exit;
}