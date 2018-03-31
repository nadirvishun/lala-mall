<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

define('FRAME', 'system');
load()->model('system');
load()->model('wxapp');

$dos = array('delete', 'display', 'edit_version', 'del_version', 'get_available_apps');
$do = in_array($do, $dos) ? $do : 'display';

$uniacid = intval($_GPC['uniacid']);
$acid = intval($_GPC['acid']);
if (empty($uniacid)) {
	itoast('请选择要编辑的小程序', referer(), 'error');
}

$state = permission_account_user_role($_W['uid'], $uniacid);
$role_permission = in_array($state, array(ACCOUNT_MANAGE_NAME_OWNER, ACCOUNT_MANAGE_NAME_FOUNDER, ACCOUNT_MANAGE_NAME_MANAGER, ACCOUNT_MANAGE_NAME_VICE_FOUNDER));
if (!$role_permission) {
	itoast('无权限操作！', referer(), 'error');
}

if ($do == 'display') {
	$account = uni_fetch($uniacid);
	if (is_error($account)) {
		itoast($account['message'], url('account/manage', array('account_type' => ACCOUNT_TYPE_APP_NORMAL)), 'error');
	} else {
		$wxapp_info = pdo_get('account_wxapp', array('uniacid' => $account['uniacid']));
		$version_exist = wxapp_fetch($account['uniacid']);
		if (!empty($version_exist)) {
			$wxapp_version_lists = wxapp_version_all($account['uniacid']);
			$wxapp_modules = wxapp_support_uniacid_modules();
		}
	}
	template('wxapp/manage');
}

if ($do == 'edit_version') {
	if (empty($_GPC['version_info']) || !is_array($_GPC['version_info'])) {
		iajax(1, '数据错误！');
	}
	if (empty($_GPC['version_info']['modules'])) {
		iajax(1, '应用模块不可为空！');
	}
	$versionid = intval($_GPC['version_info']['id']);
	$version_exist = wxapp_fetch($uniacid, $versionid);
	if(empty($version_exist)) {
		iajax(1, '版本不存在或已删除！');
	}
	$have_permission = false;
	$wxapp_modules = wxapp_support_wxapp_modules();
	$supoort_modulenames = array_keys($wxapp_modules);
	$new_module_data = array();
	if (intval($_GPC['version_info']['design_method']) == WXAPP_TEMPLATE) {
		foreach ($_GPC['version_info']['modules'] as $module_val) {
			if (!in_array($module_val['name'], $supoort_modulenames)) {
				iajax(1, '没有模块：' . $module_val['name'] . '的权限！');
			} else {
								$new_module_data[] = array(
					'name' => $module_val['name'],
					'version' => $module_val['version']
				);
			}
		}
	}
	if (intval($_GPC['version_info']['design_method']) == WXAPP_MODULE) {
		$module_name = trim($_GPC['version_info']['modules'][0]['name']);
		$module_version = trim($_GPC['version_info']['modules'][0]['version']);
		$have_permission = in_array($module_name, $supoort_modulenames);
		if (!empty($have_permission)) {
			$new_module_data = array(
					$module_name => array(
						'name' => $module_name,
						'version' => $module_version
					)
				);
		} else {
			iajax(1, '没有此模块的权限！');
		}
	}
	if (empty($new_module_data)) {
		iajax(1, '应用模块不可为空！');
	}
	$data = array('modules' => iserializer($new_module_data), 'version' => trim($_GPC['version_info']['version']), 'description' => trim($_GPC['version_info']['description']));
	pdo_update('wxapp_versions', $data, array('id' => $versionid));
	iajax(0, '修改成功！', referer());
}

if ($do == 'del_version') {
	$id = intval($_GPC['versionid']);
	if (empty($id)) {
		iajax(1, '参数错误！');
	}
	$version_exist = pdo_get('wxapp_versions', array('id' => $id, 'uniacid' => $uniacid));
	if (empty($version_exist)) {
		iajax(1, '模块版本不存在！');
	}
	$result = pdo_delete('wxapp_versions', array('id' => $id, 'uniacid' => $uniacid));
	if (!empty($result)) {
		iajax(0, '删除成功！', referer());
	} else {
		iajax(1, '删除失败，请稍候重试！');
	}
}

if ($do == 'delete') {
	$id = intval($_GPC['id']);
	$version_info = pdo_get('wxapp_versions', array('id' => $id));
	if (!empty($version_info)) {
		$allversions = wxapp_version_all($uniacid);
		if (count($allversions) <= 1) {
			itoast('请至少保留一个版本！', referer(), 'error');
		}
		pdo_delete('wxapp_versions', array('id' => $id));
	} else {
		itoast('版本不存在', referer(), 'error');
	}
	itoast('删除成功', referer(), 'success');
}
