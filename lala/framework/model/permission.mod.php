<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function permission_build() {
	global $_W;
	$we7_file_permission = require IA_ROOT . '/web/common/permission.inc.php';
	$permission_frames = require IA_ROOT . '/web/common/frames.inc.php';
	if (!in_array($_W['role'], array(ACCOUNT_MANAGE_NAME_OPERATOR, ACCOUNT_MANAGE_NAME_MANAGER)) || empty($_W['uniacid'])) {
		return $we7_file_permission;
	}

	$cachekey = cache_system_key("permission:{$_W['uniacid']}:{$_W['uid']}");
	$cache = cache_load($cachekey);
	if (!empty($cache)) {
		return $cache;
	}
	$permission_exist = permission_account_user_permission_exist($_W['uid'], $_W['uniacid']);
	if (empty($permission_exist)) {
		$we7_file_permission['platform'][$_W['role']] = array('platform*');
		$we7_file_permission['site'][$_W['role']] = array('site*');
		$we7_file_permission['mc'][$_W['role']] = array('mc*');
		$we7_file_permission['profile'][$_W['role']] = array('profile*');
		$we7_file_permission['module'][$_W['role']] = array('manage-account', 'display');
		$we7_file_permission['wxapp'][$_W['role']] = array('display', 'payment', 'post', 'version');
		cache_write($cachekey, $we7_file_permission);
		return $we7_file_permission;
	}
	$user_account_permission = permission_account_user_menu($_W['uid'], $_W['uniacid'], PERMISSION_ACCOUNT);
	$user_wxapp_permission = permission_account_user_menu($_W['uid'], $_W['uniacid'], PERMISSION_WXAPP);
	$user_permission = array_merge($user_account_permission, $user_wxapp_permission);

	$permission_contain = array('account', 'wxapp', 'system');
	$section = array();
	$permission_result = array();
	foreach ($permission_frames as $key => $frames) {
		if (!in_array($key, $permission_contain) || empty($frames['section'])) {
			continue;
		}
		foreach ($frames['section'] as $frame_key => $frame) {
			if (empty($frame['menu'])) {
				continue;
			}
			$section[$key][$frame_key] = $frame['menu'];
		}
	}
	$account = permission_get_nameandurl($section[$permission_contain[0]]);
	$wxapp = permission_get_nameandurl($section[$permission_contain[1]]);
	$system = permission_get_nameandurl($section[$permission_contain[2]]);
	$permission_result = array_merge($account, $wxapp, $system);

	foreach ($permission_result as $permission_val) {
		if (in_array($permission_val['permission_name'], $user_permission)) {
			$we7_file_permission[$permission_val['controller']][$_W['role']][] = $permission_val['action'];
		}
	}
	cache_write($cachekey, $we7_file_permission);
	return $we7_file_permission;
}


function permission_get_nameandurl($permission) {
	$result = array();
	if (empty($permission)) {
		return $result;
	}
	foreach ($permission as $menu) {
		if (empty($menu)) {
			continue;
		}
		foreach ($menu as $permission_name) {
			$url_query_array = url_params($permission_name['url']);
			$result[] = array(
				'url' => $permission_name['url'],
				'controller' => $url_query_array['c'],
				'action' => $url_query_array['a'],
				'permission_name' => $permission_name['permission_name']
			);
		}
	}
	return $result;
}


function permission_create_account($uid, $type = ACCOUNT_TYPE_OFFCIAL_NORMAL) {
	$uid = intval($uid);
	if (empty($uid)) {
		return error(-1, '用户数据错误！');
	}
	$user_table = table('users');
	$userinfo = $user_table->usersInfo($uid);
	$groupdata = $user_table->usersGroupInfo($userinfo['groupid']);
	$list = pdo_fetchall('SELECT d.type, count(*) AS count FROM (SELECT u.uniacid, a.default_acid FROM ' . tablename('uni_account_users') . ' as u RIGHT JOIN '. tablename('uni_account').' as a  ON a.uniacid = u.uniacid  WHERE u.uid = :uid AND u.role = :role ) AS c LEFT JOIN '.tablename('account').' as d ON c.default_acid = d.acid WHERE d.isdeleted = 0 GROUP BY d.type', array(':uid' => $uid, ':role' => 'owner'));
	foreach ($list as $item) {
		if ($item['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$wxapp_num = $item['count'];
		} else {
			$account_num = $item['count'];
		}
	}
		if ($type == ACCOUNT_TYPE_OFFCIAL_NORMAL || $type == ACCOUNT_TYPE_OFFCIAL_AUTH) {
		if ($account_num >= $groupdata['maxaccount']) {
			return error('-1', '您所在的用户组最多只能创建' . $groupdata['maxaccount'] . '个主公众号');
		}
	} elseif ($type == ACCOUNT_TYPE_APP_NORMAL) {
		if ($wxapp_num >= $groupdata['maxwxapp']) {
			return error('-1', '您所在的用户组最多只能创建' . $groupdata['maxwxapp'] . '个小程序');
		}
	}
	return true;
}


function permission_account_user_role($uid = 0, $uniacid = 0) {
	global $_W;
	load()->model('user');
	$role = '';
	$uid = empty($uid) ? $_W['uid'] : intval($uid);

	if (user_is_founder($uid) && !user_is_vice_founder($uid)) {
		return ACCOUNT_MANAGE_NAME_FOUNDER;
	}

	if (user_is_vice_founder($uid)) {
		return ACCOUNT_MANAGE_NAME_VICE_FOUNDER;
	}
	$user_table = table('users');
	if (!empty($uniacid)) {
		$role = $user_table->userOwnedAccountRole($uid, $uniacid);
		if ($role == ACCOUNT_MANAGE_NAME_OWNER) {
			$role = ACCOUNT_MANAGE_NAME_OWNER;
		} elseif ($role == ACCOUNT_MANAGE_NAME_VICE_FOUNDER) {
			$role = ACCOUNT_MANAGE_NAME_VICE_FOUNDER;
		} elseif ($role == ACCOUNT_MANAGE_NAME_MANAGER) {
			$role = ACCOUNT_MANAGE_NAME_MANAGER;
		} elseif ($role == ACCOUNT_MANAGE_NAME_OPERATOR) {
			$role = ACCOUNT_MANAGE_NAME_OPERATOR;
		} elseif ($role == ACCOUNT_MANAGE_NAME_CLERK) {
			$role = ACCOUNT_MANAGE_NAME_CLERK;
		}
	} else {
		$roles = $user_table->userOwnedAccountRole($uid);
		if (in_array(ACCOUNT_MANAGE_NAME_VICE_FOUNDER, $roles)) {
			$role = ACCOUNT_MANAGE_NAME_VICE_FOUNDER;
		} elseif (in_array(ACCOUNT_MANAGE_NAME_OWNER, $roles)) {
			$role = ACCOUNT_MANAGE_NAME_OWNER;
		} elseif (in_array(ACCOUNT_MANAGE_NAME_MANAGER, $roles)) {
			$role = ACCOUNT_MANAGE_NAME_MANAGER;
		} elseif (in_array(ACCOUNT_MANAGE_NAME_OPERATOR, $roles)) {
			$role = ACCOUNT_MANAGE_NAME_OPERATOR;
		} elseif (in_array(ACCOUNT_MANAGE_NAME_CLERK, $roles)) {
			$role = ACCOUNT_MANAGE_NAME_CLERK;
		}
	}
	$role = empty($role) ? ACCOUNT_MANAGE_NAME_OPERATOR : $role;
	return $role;
}


function permission_account_user_permission_exist($uid = 0, $uniacid = 0) {
	global $_W;
	load()->model('user');
	$uid = intval($uid) > 0 ? $uid : $_W['uid'];
	$uniacid = intval($uniacid) > 0 ? $uniacid : $_W['uniacid'];
	if (user_is_founder($uid)) {
		return false;
	}
	if (FRAME == 'system') {
		return true;
	}
	$is_exist = table('users')->userPermissionInfo($uid, $uniacid);
	if(empty($is_exist)) {
		return false;
	} else {
		return true;
	}
}


function permission_account_user($type = 'system') {
	global $_W;
	$user_permission = table('users')->userPermissionInfo($_W['uid'], $_W['uniacid'], $type);
	$user_permission = $user_permission['permission'];
	if (!empty($user_permission)) {
		$user_permission = explode('|', $user_permission);
	} else {
		$user_permission = array('account*', 'wxapp*');
	}
	$permission_append = frames_menu_append();
		if (!empty($permission_append[$_W['role']])) {
		$user_permission = array_merge($user_permission, $permission_append[$_W['role']]);
	}
		if (empty($_W['role']) && empty($_W['uniacid'])) {
		$user_permission = array_merge($user_permission, $permission_append['operator']);
	}
	return (array)$user_permission;
}


function permission_account_user_menu($uid, $uniacid, $type) {
	$user_menu_permission = array();

	$uid = intval($uid);
	$uniacid = intval($uniacid);
	$type = trim($type);
	if (empty($uid) || empty($uniacid) || empty($type)) {
		return error(-1, '参数错误！');
	}
	$permission_exist = permission_account_user_permission_exist($uid, $uniacid);
	if (empty($permission_exist)) {
		return array('all');
	}
	$user_table = table('users');
	if ($type == 'modules') {
		$user_menu_permission = $user_table->userModulesPermission($uid, $uniacid);
	} else {
		$module = uni_modules_by_uniacid($uniacid);
		$module = array_keys($module);
		if (in_array($type, $module) || in_array($type, array(PERMISSION_ACCOUNT, PERMISSION_WXAPP, PERMISSION_SYSTEM))) {
			$menu_permission = $user_table->userPermissionInfo($uid, $uniacid, $type);
			if (!empty($menu_permission['permission'])) {
				$user_menu_permission = explode('|', $menu_permission['permission']);
			}
		}
	}

	return $user_menu_permission;
}


function permission_menu_name() {
	load()->model('system');
	$menu_permission = array();

	$menu_list = system_menu_permission_list();
	$middle_menu = array();
	$middle_sub_menu = array();
	if (!empty($menu_list)) {
		foreach ($menu_list as $nav_id => $section) {
			foreach ($section['section'] as $section_id => $section) {
				if (!empty($section['menu'])) {
					$middle_menu[] = $section['menu'];
				}
			}
		}
	}

	if (!empty($middle_menu)) {
		foreach ($middle_menu as $menu) {
			foreach ($menu as $menu_val) {
				$menu_permission[] = $menu_val['permission_name'];
				if (!empty($menu_val['sub_permission'])) {
					$middle_sub_menu[] = $menu_val['sub_permission'];
				}
			}
		}
	}

	if (!empty($middle_sub_menu)) {
		foreach ($middle_sub_menu as $sub_menu) {
			foreach ($sub_menu as $sub_menu_val) {
				$menu_permission[] = $sub_menu_val['permission_name'];
			}
		}
	}
	return $menu_permission;
}


function permission_update_account_user($uid, $uniacid, $data) {
	$uid = intval($uid);
	$uniacid = intval($uniacid);
	if (empty($uid) || empty($uniacid) || !in_array($data['type'], array(PERMISSION_ACCOUNT, PERMISSION_WXAPP, PERMISSION_SYSTEM))) {
		return error('-1', '参数错误！');
	}
	$user_menu_permission = permission_account_user_menu($uid, $uniacid, $data['type']);
	if (is_error($user_menu_permission)) {
		return error('-1', '参数错误！');
	}

	if (empty($user_menu_permission)) {
		$insert = array(
			'uniacid' => $uniacid,
			'uid' => $uid,
			'type' => $data['type'],
			'permission' => $data['permission'],
		);
		$result = pdo_insert('users_permission', $insert);
	} else {
		$update = array(
			'permission' => $data['permission'],
		);
		$result = pdo_update('users_permission', $update, array('uniacid' => $uniacid, 'uid' => $uid, 'type' => $data['type']));
	}
	return $result;
}

function permission_check_account_user($permission_name, $show_message = true, $action = '') {
	global $_W, $_GPC;
	$user_has_permission = permission_account_user_permission_exist();
	if (empty($user_has_permission)) {
		return true;
	}
	$modulename = trim($_GPC['m']);
	$do = trim($_GPC['do']);
	$entry_id = intval($_GPC['eid']);

	if ($action == 'reply') {
		$system_modules = system_modules();
		if (!empty($modulename) && !in_array($modulename, $system_modules)) {
			$permission_name = $modulename . '_rule';
			$users_permission = permission_account_user($modulename);
		}
	} elseif ($action == 'cover' && $entry_id > 0) {
		load()->model('module');
		$entry = module_entry($entry_id);
		if (!empty($entry)) {
			$permission_name = $entry['module'] . '_cover_' . trim($entry['do']);
			$users_permission = permission_account_user($entry['module']);
		}
	} elseif ($action == 'nav') {
				if(!empty($modulename)) {
			$permission_name = "{$modulename}_{$do}";
			$users_permission = permission_account_user($modulename);
		} else {
			return true;
		}
	} elseif ($action == 'wxapp' || !empty($_W['account']) && $_W['account']['type'] == ACCOUNT_TYPE_APP_NORMAL) {
		$users_permission = permission_account_user('wxapp');
	} else {
		$users_permission = permission_account_user('system');
	}
	if (!isset($users_permission)) {
		$users_permission = permission_account_user('system');
	}
	if ($users_permission[0] != 'all' && !in_array($permission_name, $users_permission)) {
		if ($show_message) {
			itoast('您没有进行该操作的权限', referer(), 'error');
		} else {
			return false;
		}
	}
	return true;
}


function permission_check_account_user_module($action = '', $module_name = '') {
	global $_GPC;
	$status = permission_account_user_permission_exist();
	if(empty($status)) {
		return true;
	}
	$a = trim($_GPC['a']);
	$do = trim($_GPC['do']);
	$m = trim($_GPC['m']);
		if ($a == 'module' && $do == 'setting' && !empty($m)) {
		$permission_name = $m . '_setting';
		$users_permission = permission_account_user($m);
		if ($users_permission[0] != 'all' && !in_array($permission_name, $users_permission)) {
			return false;
		}
			} elseif (!empty($do) && !empty($m)) {
		$is_exist = table('module')->moduleBindingsInfo($m, $do, 'menu');
		if(empty($is_exist)) {
			return true;
		}
	}
	if(empty($module_name)) {
		$module_name = IN_MODULE;
	}
	$permission = permission_account_user($module_name);
	if(empty($permission) || ($permission[0] != 'all' && !empty($action) && !in_array($action, $permission))) {
		return false;
	}
	return true;
}


function permission_user_account_num($uid = 0) {
	global $_W;
	$uid = intval($uid);
	if ($uid <= 0) {
		$user = $_W['user'];
	} else {
		load()->model('user');
		$user = user_single($uid);
	}
	$user_table = table('users');
	if (user_is_vice_founder($user['uid']) && empty($uid)) {
		$role = ACCOUNT_MANAGE_NAME_VICE_FOUNDER;
		$group = $user_table->userFounderGroupInfo($user['groupid']);
		$group_num = uni_owner_account_nums($user['uid'], $role);
	} else {
		$role = ACCOUNT_MANAGE_NAME_OWNER;
		$group = $user_table->usersGroupInfo($user['groupid']);
		$group_num = uni_owner_account_nums($user['uid'], $role);
		if (empty($_W['isfounder'])) {
			if (!empty($user['owner_uid'])) {
				$owner_info = $user_table->usersInfo($user['owner_uid']);
				$group_vice = $user_table->userFounderGroupInfo($owner_info['groupid']);
				$group['maxaccount'] = min(intval($group['maxaccount']), intval($group_vice['maxaccount']));
				$group['maxwxapp'] = min(intval($group['maxwxapp']), intval($group_vice['maxwxapp']));
			}
		}
	}

	$store_table = table('store');
	$create_buy_account_num = $store_table->searchUserCreateAccountNum($_W['uid']);
	$create_buy_wxapp_num = $store_table->searchUserCreateWxappNum($_W['uid']);
	$store_buy_account = $store_table->searchUserBuyAccount($_W['uid']);
	$store_buy_wxapp = $store_table->searchUserBuyWxapp($_W['uid']);
	$uniacid_limit = max((intval($group['maxaccount']) + intval($store_buy_account) - $group_num['account_num']), 0);
	$wxapp_limit = max((intval($group['maxwxapp']) + intval($store_buy_wxapp) - $group_num['wxapp_num']), 0);
	$data = array(
		'group_name' => $group['name'],
		'vice_group_name' => $group_vice['name'],
		'maxaccount' => $group['maxaccount'] + $store_buy_account,
		'usergroup_account_limit' => max($group['maxaccount'] - $group_num['account_num'] - $create_buy_account_num, 0),		'usergroup_wxapp_limit' => max($group['maxwxapp'] - $group_num['wxapp_num'] - $create_buy_wxapp_num, 0),		'uniacid_num' => $group_num['account_num'],
		'uniacid_limit' => max($uniacid_limit, 0),
		'maxwxapp' => $group['maxwxapp'] + $store_buy_wxapp,
		'wxapp_num' => $group_num['wxapp_num'],
		'wxapp_limit' => max($wxapp_limit, 0)
	);
	return $data;
}