<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');
load()->model('wxapp');

$dos = array('module_link_uniacid', 'search_link_account', 'module_unlink_uniacid');
$do = in_array($do, $dos) ? $do : 'module_link_uniacid';

$_W['page']['title'] = '数据同步 - 小程序 - 管理';

$version_id = intval($_GPC['version_id']);
$wxapp_info = wxapp_fetch($_W['uniacid']);
if (!empty($version_id)) {
	$version_info = wxapp_version($version_id);
}


if ($do == 'module_link_uniacid') {
	$module_name = trim($_GPC['module_name']);
	$version_info = wxapp_version($version_id);

	if (checksubmit('submit')) {
		$uniacid = intval($_GPC['uniacid']);
		if (empty($module_name) || empty($uniacid)) {
			iajax('1', '参数错误！');
		}
		$module = module_fetch($module_name);
		if (empty($module)) {
			iajax('1', '模块不存在！');
		}
		$module_update = array();
		$module_update[$module['name']] = array('name' => $module['name'], 'version' => $module['version'], 'uniacid' => $uniacid);
		pdo_update('wxapp_versions', array('modules' => serialize($module_update)), array('id' => $version_id));
		iajax(0, '关联公众号成功');
	}
	template('wxapp/version-module-link-uniacid');
}

if ($do == 'module_unlink_uniacid') {
	if (!empty($version_info)) {
		$module = current($version_info['modules']);
		$version_modules = array(
				$module['name'] => array(
					'name' => $module['name'],
					'version' => $module['version']
					)
			);
	}
	$version_modules = serialize($version_modules);
	$result = pdo_update('wxapp_versions', array('modules' => $version_modules), array('id' => $version_info['id']));
	if ($result) {
		iajax(0, '删除成功！', referer());
	} else {
		iajax(0, '删除失败！', referer());
	}
}

if ($do == 'search_link_account') {
	$module_name = trim($_GPC['module_name']);
	if (empty($module_name)) {
		iajax(0, array());
	}
	$account_list = wxapp_search_link_account($module_name);
	iajax(0, $account_list);
}