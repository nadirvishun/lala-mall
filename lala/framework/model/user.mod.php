<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function user_register($user) {
	if (empty($user) || !is_array($user)) {
		return 0;
	}
	if (isset($user['uid'])) {
		unset($user['uid']);
	}
	$user['salt'] = random(8);
	$user['password'] = user_hash($user['password'], $user['salt']);
	$user['joinip'] = CLIENT_IP;
	$user['joindate'] = TIMESTAMP;
	$user['lastip'] = CLIENT_IP;
	$user['lastvisit'] = TIMESTAMP;
	if (empty($user['status'])) {
		$user['status'] = 2;
	}
	if (empty($user['type'])) {
		$user['type'] = USER_TYPE_COMMON;
	}
	$result = pdo_insert('users', $user);
	if (!empty($result)) {
		$user['uid'] = pdo_insertid();
	}
	return intval($user['uid']);
}


function user_check($user) {
	if (empty($user) || !is_array($user)) {
		return false;
	}
	$where = ' WHERE 1 ';
	$params = array();
	if (!empty($user['uid'])) {
		$where .= ' AND `uid`=:uid';
		$params[':uid'] = intval($user['uid']);
	}
	if (!empty($user['username'])) {
		$where .= ' AND `username`=:username';
		$params[':username'] = $user['username'];
	}
	if (!empty($user['status'])) {
		$where .= " AND `status`=:status";
		$params[':status'] = intval($user['status']);
	}
	if (empty($params)) {
		return false;
	}
	$sql = 'SELECT `password`,`salt` FROM ' . tablename('users') . "$where LIMIT 1";
	$record = pdo_fetch($sql, $params);
	if (empty($record) || empty($record['password']) || empty($record['salt'])) {
		return false;
	}
	if (!empty($user['password'])) {
		$password = user_hash($user['password'], $record['salt']);
		return $password == $record['password'];
	}
	return true;
}


function user_get_uid_byname($username = '') {
	$username = trim($username);
	if (empty($username)) {
		return false;
	}
	$uid = pdo_getcolumn('users', array('username' => $username, 'founder_groupid' => ACCOUNT_MANAGE_GROUP_VICE_FOUNDER), 'uid');
	return $uid;
}


function user_is_founder($uid) {
	global $_W;
	$founders = explode(',', $_W['config']['setting']['founder']);
	if (in_array($uid, $founders)) {
		return true;
	} else {
		$founder_groupid = pdo_getcolumn('users', array('uid' => $uid), 'founder_groupid');
		if ($founder_groupid == ACCOUNT_MANAGE_GROUP_VICE_FOUNDER) {
			return true;
		}
	}
	return false;
}


function user_is_vice_founder($uid = 0) {
	global $_W;
	$uid = intval($uid) > 0 ? intval($uid) : $_W['uid'];
	$user_info = user_single($uid);

	if (user_is_founder($uid) && $user_info['founder_groupid'] == ACCOUNT_MANAGE_GROUP_VICE_FOUNDER) {
		return true;
	}
	return false;
}


function user_delete($uid, $is_recycle = false) {
	if (!empty($is_recycle)) {
		pdo_update('users', array('status' => 3) , array('uid' => $uid));
		return true;
	}

	load()->model('cache');
	$founder_groupid = pdo_getcolumn('users', array('uid' => $uid), 'founder_groupid');
	if ($founder_groupid == ACCOUNT_MANAGE_GROUP_VICE_FOUNDER) {
		pdo_update('users', array('owner_uid' => 0), array('owner_uid' => $uid));
		pdo_update('users_group', array('owner_uid' => 0), array('owner_uid' => $uid));
		pdo_update('uni_group', array('owner_uid' => 0), array('owner_uid' => $uid));
	}
	pdo_delete('users', array('uid' => $uid));
	$user_set_account = pdo_getall('uni_account_users', array('uid' => $uid, 'role' => 'owner'));
	if (!empty($user_set_account)) {
		foreach ($user_set_account as $account) {
			cache_build_account_modules($account['uniacid']);
		}
	}
	pdo_delete('uni_account_users', array('uid' => $uid));
	pdo_delete('users_profile', array('uid' => $uid));
	return true;
}


function user_single($user_or_uid) {
	$user = $user_or_uid;
	if (empty($user)) {
		return false;
	}
	if (is_numeric($user)) {
		$user = array('uid' => $user);
	}
	if (!is_array($user)) {
		return false;
	}
	$where = ' WHERE 1 ';
	$params = array();
	if (!empty($user['uid'])) {
		$where .= ' AND `uid`=:uid';
		$params[':uid'] = intval($user['uid']);
	}
	if (!empty($user['username'])) {
		$where .= ' AND `username`=:username';
		$params[':username'] = $user['username'];
	}
	if (!empty($user['email'])) {
		$where .= ' AND `email`=:email';
		$params[':email'] = $user['email'];
	}
	if (!empty($user['status'])) {
		$where .= " AND `status`=:status";
		$params[':status'] = intval($user['status']);
	}
	if (empty($params)) {
		return false;
	}
	$sql = 'SELECT * FROM ' . tablename('users') . " $where LIMIT 1";
	$record = pdo_fetch($sql, $params);
	if (empty($record)) {
		return false;
	}
	if (!empty($user['password'])) {
		$password = user_hash($user['password'], $record['salt']);
		if ($password != $record['password']) {
			return false;
		}
	}
	if (!empty($record['owner_uid'])) {
		$record['vice_founder_name'] = pdo_getcolumn('users', array('uid' => $record['owner_uid']), 'username');
	}
	if($record['type'] == ACCOUNT_OPERATE_CLERK) {
		$clerk = pdo_get('activity_clerks', array('uid' => $record['uid']));
		if(!empty($clerk)) {
			$record['name'] = $clerk['name'];
			$record['clerk_id'] = $clerk['id'];
			$record['store_id'] = $clerk['storeid'];
			$record['store_name'] = pdo_fetchcolumn('SELECT business_name FROM ' . tablename('activity_stores') . ' WHERE id = :id', array(':id' => $clerk['storeid']));
			$record['clerk_type'] = '3';
			$record['uniacid'] = $clerk['uniacid'];
		}
	} else {
				$record['name'] = $record['username'];
		$record['clerk_id'] = $user['uid'];
		$record['store_id'] = 0;
		$record['clerk_type'] = '2';
	}
	return $record;
}


function user_update($user) {
	if (empty($user['uid']) || !is_array($user)) {
		return false;
	}
	$record = array();
	if (!empty($user['username'])) {
		$record['username'] = $user['username'];
	}
	if (!empty($user['password'])) {
		$record['password'] = user_hash($user['password'], $user['salt']);
	}
	if (!empty($user['lastvisit'])) {
		$record['lastvisit'] = (strlen($user['lastvisit']) == 10) ? $user['lastvisit'] : strtotime($user['lastvisit']);
	}
	if (!empty($user['lastip'])) {
		$record['lastip'] = $user['lastip'];
	}
	if (isset($user['joinip'])) {
		$record['joinip'] = $user['joinip'];
	}
	if (isset($user['remark'])) {
		$record['remark'] = $user['remark'];
	}
	if (isset($user['type'])) {
		$record['type'] = $user['type'];
	}
	if (isset($user['status'])) {
		$status = intval($user['status']);
		if (!in_array($status, array(1, 2))) {
			$status = 2;
		}
		$record['status'] = $status;
	}
	if (isset($user['groupid'])) {
		$record['groupid'] = $user['groupid'];
	}
	if (isset($user['starttime'])) {
		$record['starttime'] = $user['starttime'];
	}
	if (isset($user['endtime'])) {
		$record['endtime'] = $user['endtime'];
	}
	if(isset($user['lastuniacid'])) {
		$record['lastuniacid'] = intval($user['lastuniacid']);
	}
	if (empty($record)) {
		return false;
	}
	return pdo_update('users', $record, array('uid' => intval($user['uid'])));
}


function user_hash($passwordinput, $salt) {
	global $_W;
	$passwordinput = "{$passwordinput}-{$salt}-{$_W['config']['setting']['authkey']}";
	return sha1($passwordinput);
}


function user_level() {
	static $level = array(
		'-3' => '锁定用户',
		'-2' => '禁止访问',
		'-1' => '禁止发言',
		'0' => '普通会员',
		'1' => '管理员',
	);
	return $level;
}


function user_group() {
	global $_W;
	if (user_is_vice_founder()) {
		$condition = array(
			'owner_uid' => $_W['uid'],
		);
	}
	$groups = pdo_getall('users_group', $condition, array('id', 'name', 'package'), 'id', 'id ASC');
	return $groups;
}


function user_founder_group() {
	$groups = pdo_getall('users_founder_group', array(), array('id', 'name', 'package'), 'id', 'id ASC');
	return $groups;
}


function user_group_detail_info($groupid = 0) {
	$group_info = array();

	$groupid = is_array($groupid) ? 0 : intval($groupid);
	if(empty($groupid)) {
		return $group_info;
	}
	$group_info = pdo_get('users_group', array('id' => $groupid));
	if (empty($group_info)) {
		return $group_info;
	}

	$group_info['package'] = (array)iunserializer($group_info['package']);
	if (!empty($group_info['package'])) {
		$group_info['package_detail'] = uni_groups($group_info['package']);
	}
	return $group_info;
}


function user_founder_group_detail_info($groupid = 0) {
	$group_info = array();

	$groupid = is_array($groupid) ? 0 : intval($groupid);
	if(empty($groupid)) {
		return $group_info;
	}
	$group_info = pdo_get('users_founder_group', array('id' => $groupid));
	if (empty($group_info)) {
		return $group_info;
	}

	$group_info['package'] = (array)iunserializer($group_info['package']);
	if (!empty($group_info['package'])) {
		$group_info['package_detail'] = uni_groups($group_info['package']);
	}
	return $group_info;
}


function user_account_detail_info($uid) {
	$account_lists = $app_user_info = $wxapp_user_info = array();
	$uid = intval($uid);
	if (empty($uid)) {
		return $account_lists;
	}

	$account_users_info = table('account')->userOwnedAccount($uid);
	if (!empty($account_users_info)) {
		foreach ($account_users_info as $uniacid => $account) {
			if ($account['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $account['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
				$app_user_info[$uniacid] = $account;
			} elseif ($account['type'] == ACCOUNT_TYPE_APP_NORMAL) {
				$wxapp_user_info[$uniacid] = $account;
			}
		}
	}

	$wxapps = $wechats = array();
	if (!empty($wxapp_user_info)) {
		$wxapps = table('account')->accountWxappInfo(array_keys($wxapp_user_info), $uid);
	}
	if (!empty($app_user_info)) {
		$wechats = table('account')->accountWechatsInfo(array_keys($app_user_info), $uid);
	}
	$accounts = array_merge($wxapps, $wechats);
	if (!empty($accounts)) {
		foreach ($accounts as &$account_val) {
			$account_val['thumb'] = tomedia('headimg_'.$account_val['default_acid']. '.jpg');
			foreach ($account_users_info as $uniacid => $user_info) {
				if ($account_val['uniacid'] == $uniacid) {
					$account_val['type'] = $user_info['type'];
					if ($user_info['type'] == ACCOUNT_TYPE_APP_NORMAL) {
						$account_lists['wxapp'][$uniacid] = $account_val;
					} elseif ($user_info['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $user_info['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
						$account_lists['wechat'][$uniacid] = $account_val;
					}
				}
			}
		}
		unset($account_val);
	}
	return $account_lists;
}


function user_modules($uid) {
	global $_W;
	load()->model('module');
	$cachekey = cache_system_key("user_modules:" . $uid);
	$modules = cache_load($cachekey);
	if (empty($modules)) {
		$user_info = user_single(array ('uid' => $uid));

		$system_modules = pdo_getall('modules', array('issystem' => 1), array('name'), 'name');
		if (empty($uid)  || user_is_founder($uid) && !user_is_vice_founder($uid)) {
			$module_list = pdo_getall('modules', array(), array('name'), 'name', array('mid DESC'));
		} elseif (!empty($user_info) && $user_info['type'] == ACCOUNT_OPERATE_CLERK) {
			$clerk_module = pdo_fetch("SELECT p.type FROM " . tablename('users_permission') . " p LEFT JOIN " . tablename('uni_account_users') . " u ON p.uid = u.uid AND p.uniacid = u.uniacid WHERE u.role = :role AND p.uid = :uid", array(':role' => ACCOUNT_MANAGE_NAME_CLERK, ':uid' => $uid));
			if (empty($clerk_module)) {
				return array();
			}
			$module_list = array($clerk_module['type'] => $clerk_module['type']);
		} elseif (!empty($user_info) && empty($user_info['groupid'])) {
			$module_list = $system_modules;
		} else {
			if ($user_info['founder_groupid'] == ACCOUNT_MANAGE_GROUP_VICE_FOUNDER) {
				$user_group_info = user_founder_group_detail_info($user_info['groupid']);
			} else {
				$user_group_info = user_group_detail_info($user_info['groupid']);
			}
			$packageids = $user_group_info['package'];

						if (!empty($packageids) && in_array('-1', $packageids)) {
				$module_list = pdo_getall('modules', array(), array('name'), 'name', array('mid DESC'));
			} else {
								$package_group = pdo_getall('uni_group', array('id' => $packageids));
				if (!empty($package_group)) {
					$package_group_module = array();
					foreach ($package_group as $row) {
						if (!empty($row['modules'])) {
							$row['modules'] = (array)unserialize($row['modules']);
						}
						if (!empty($row['modules'])) {
							foreach ($row['modules'] as $modulename => $module) {
								if (!is_array($module)) {
									$modulename = $module;
								}
								$package_group_module[$modulename] = $modulename;
							}
						}
					}
				}
				$module_list = pdo_fetchall("SELECT name FROM ".tablename('modules')." WHERE
										name IN ('" . implode("','", $package_group_module) . "') OR issystem = '1' ORDER BY mid DESC", array(), 'name');
			}
		}
		$module_list = array_keys($module_list);
		$plugin_list = $modules = array();
		if (pdo_tableexists('modules_plugin')) {
			$plugin_list = pdo_getall('modules_plugin', array('name' => $module_list), array());
		}
		$have_plugin_module = array();
		if (!empty($plugin_list)) {
			foreach ($plugin_list as $plugin) {
				$have_plugin_module[$plugin['main_module']][$plugin['name']] = $plugin['name'];
				$module_key = array_search($plugin['name'], $module_list);
				if ($module_key !== false) {
					unset($module_list[$module_key]);
				}
			}
		}
		if (!empty($module_list)) {
			foreach ($module_list as $module) {
				$modules[] = $module;
				if (!empty($have_plugin_module[$module])) {
					foreach ($have_plugin_module[$module] as $plugin) {
						$modules[] = $plugin;
					}
				}
			}
		}
		cache_write($cachekey, $modules);
	}

	$module_list = array();
	if (!empty($modules)) {
		foreach ($modules as $module) {
			$module_info = module_fetch($module);
			if (!empty($module_info)) {
				$module_list[$module] = $module_info;
			}
		}
	}
	return $module_list;
}


function user_uniacid_modules($uid) {
	if (user_is_vice_founder($uid)) {
		$module_list = user_modules($uid);
		if (empty($module_list)) {
			return $module_list;
		}
		foreach ($module_list as $module => $module_info) {
			if (!empty($module_info['issystem'])) {
				unset($module_list[$module]);
			}
		}
	} else {
		$module_list = pdo_getall('modules', array('issystem' => 0), array(), 'name', 'mid DESC');
	}
	return $module_list;
}


function user_login_forward($forward = '') {
	global $_W;
	$login_forward = trim($forward);

	if (!empty($forward)) {
		return $login_forward;
	}
	if (user_is_vice_founder()) {
		return url('account/manage', array('account_type' => 1));
	}
	if (!empty($_W['isfounder'])) {
		return url('home/welcome/system');
	}
	if ($_W['user']['type'] == ACCOUNT_OPERATE_CLERK) {
		return url('module/display');
	}

	$login_forward = url('account/display');
	if (!empty($_W['uniacid']) && !empty($_W['account'])) {
		$permission = permission_account_user_role($_W['uid'], $_W['uniacid']);
		if (empty($permission)) {
			return $login_forward;
		}
		if ($_W['account']['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $_W['account']['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
			$login_forward = url('home/welcome');
		} elseif ($_W['account']['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$login_forward = url('wxapp/display/home');
		}
	}

	return $login_forward;
}

function user_module_by_account_type($type) {
	global $_W;
	$module_list = user_modules($_W['uid']);
	if (!empty($module_list)) {
		foreach ($module_list as $key => &$module) {
			if ((!empty($module['issystem']) && $module['name'] != 'we7_coupon')) {
				unset($module_list[$key]);
			}
			if ($module['wxapp_support'] != 2 && $type == 'wxapp') {
				unset($module_list[$key]);
			}
			if ($module['app_support'] != 2 && $type == 'account') {
				unset($module_list[$key]);
			}
		}
		unset($module);
	}
	return $module_list;
}

function user_invite_register_url($uid = 0) {
	global $_W;
	if (empty($uid)) {
		$uid = $_W['uid'];
	}
	return $_W['siteroot'] . 'index.php?c=user&a=register&owner_uid=' . $uid;
}


function user_save_group($group_info) {
	global $_W;
	$name = trim($group_info['name']);
	if (empty($name)) {
		return error(-1, '用户权限组名不能为空');
	}

	if (!empty($group_info['id'])) {
		$name_exist = pdo_get('users_group', array('id <>' => $group_info['id'], 'name' => $name));
	} else {
		$name_exist = pdo_get('users_group', array('name' => $name));
	}

	if (!empty($name_exist)) {
		return error(-1, '用户权限组名已存在！');
	}

	if (!empty($group_info['package'])) {
		foreach ($group_info['package'] as $value) {
			$package[] = intval($value);
		}
	}
	$group_info['package'] = iserializer($package);
	if (user_is_vice_founder()) {
		$group_info['owner_uid'] = $_W['uid'];
	}

	if (empty($group_info['id'])) {
		pdo_insert('users_group', $group_info);
	} else {
		pdo_update('users_group', $group_info, array('id' => $group_info['id']));
	}

	return error(0, '添加成功');
}


function user_save_founder_group($group_info) {
	$name = trim($group_info['name']);
	if (empty($name)) {
		return error(-1, '用户权限组名不能为空');
	}

	if (!empty($group_info['id'])) {
		$name_exist = pdo_get('users_founder_group', array('id <>' => $group_info['id'], 'name' => $name));
	} else {
		$name_exist = pdo_get('users_founder_group', array('name' => $name));
	}

	if (!empty($name_exist)) {
		return error(-1, '用户权限组名已存在！');
	}

	if (!empty($group_info['package'])) {
		foreach ($group_info['package'] as $value) {
			$package[] = intval($value);
		}
	}
	$group_info['package'] = iserializer($package);

	if (empty($group_info['id'])) {
		pdo_insert('users_founder_group', $group_info);
	} else {
		pdo_update('users_founder_group', $group_info, array('id' => $group_info['id']));
	}

	return error(0, '添加成功');
}


function user_group_format($lists) {
	if (empty($lists)) {
		return $lists;
	}
	foreach ($lists as $key => $group) {
		$package = iunserializer($group['package']);
		$group['package'] = uni_groups($package);
		if (empty($package)) {
			$lists[$key]['module_nums'] = '系统默认';
			$lists[$key]['wxapp_nums'] = '系统默认';
			continue;
		}
		if (is_array($package) && in_array(-1, $package)) {
			$lists[$key]['module_nums'] = -1;
			$lists[$key]['wxapp_nums'] = -1;
			continue;
		}
		$names = array();
		if (!empty($group['package'])) {
			foreach ($group['package'] as $modules) {
				$names[] = $modules['name'];
				$lists[$key]['module_nums'] = count($modules['modules']);
				$lists[$key]['wxapp_nums'] = count($modules['wxapp']);
			}
		}
		$lists[$key]['packages'] = implode(',', $names);
	}
	return $lists;
}


function user_list_format($users) {
	if (empty($users)) {
		return array();
	}
	$users_table = table('users');
	$groups = $users_table->usersGroup();
	$founder_groups = $users_table->usersFounderGroup();
	foreach ($users as &$user) {
		$user['avatar'] = !empty($user['avatar']) ? $user['avatar'] : './resource/images/nopic-user.png';
		$user['joindate'] = date('Y-m-d', $user['joindate']);
		if (empty($user['endtime'])) {
			$user['endtime'] = '永久有效';
		} else {
			$user['endtime'] = $user['endtime'] <= TIMESTAMP ? '服务已到期' : date('Y-m-d', $user['endtime']);
		}

		$user['module_num'] =array();
		if ($user['founder_groupid'] == ACCOUNT_MANAGE_GROUP_VICE_FOUNDER) {
			$group = $founder_groups[$user['groupid']];
		} else {
			$group = $groups[$user['groupid']];
		}
		$user['maxaccount'] = $user['founder_groupid'] == 1 ? '不限' : (empty($group) ? 0 : $group['maxaccount']);
		$user['maxwxapp'] = $user['founder_groupid'] == 1 ? '不限' : (empty($group) ? 0 : $group['maxwxapp']);
		$user['groupname'] = $group['name'];
		unset($user);
	}
	return $users;
}


function user_info_save($user, $is_founder_group = false) {
	global $_W;
	if (!preg_match(REGULAR_USERNAME, $user['username'])) {
		return error(-1, '必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。');
	}
	if (user_check(array('username' => $user['username']))) {
		return error(-1, '非常抱歉，此用户名已经被注册，你需要更换注册名称！');
	}
	if (istrlen($user['password']) < 8) {
		return error(-1, '必须输入密码，且密码长度不得低于8位。');
	}
	if (trim($user['password']) !== trim($user['repassword'])) {
		return error(-1, '两次密码不一致！');
	}
	if (!intval($user['groupid'])) {
		return error(-1, '请选择所属用户组');
	}

	if ($is_founder_group) {
		$group = user_founder_group_detail_info($user['groupid']);
	} else {
		$group = user_group_detail_info($user['groupid']);
	}
	if (empty($group)) {
		return error(-1, '会员组不存在');
	}

	$timelimit = intval($group['timelimit']);
	$timeadd = 0;
	if ($timelimit > 0) {
		$timeadd = strtotime($timelimit . ' days');
	}
	$user['endtime'] = $timeadd;
	$user['owner_uid'] = user_get_uid_byname($user['vice_founder_name']);
	if (user_is_vice_founder()) {
		$user['owner_uid'] = $_W['uid'];
	}
	unset($user['vice_founder_name']);
	unset($user['repassword']);
	$user_add_id = user_register($user);
	if (empty($user_add_id)) {
		return error(-1, '增加失败，请稍候重试或联系网站管理员解决！');
	}
	return array('uid' => $user_add_id);
}


function user_detail_formate($profile) {
	if (!empty($profile)) {
		$profile['reside'] = array(
			'province' => $profile['resideprovince'],
			'city' => $profile['residecity'],
			'district' => $profile['residedist']
		);
		$profile['birth'] = array(
			'year' => $profile['birthyear'],
			'month' => $profile['birthmonth'],
			'day' => $profile['birthday'],
		);
		$profile['avatar'] = tomedia($profile['avatar']);
		$profile['resides'] = $profile['resideprovince'] . $profile['residecity'] . $profile['residedist'] ;
		$profile['births'] =($profile['birthyear'] ? $profile['birthyear'] : '--') . '年' . ($profile['birthmonth'] ? $profile['birthmonth'] : '--') . '月' . ($profile['birthday'] ? $profile['birthday'] : '--') .'日';
	}
	return $profile;
}
function user_expire_notice() {
	load()->model('cloud');
	load()->model('setting');
	$setting_sms_sign = setting_load('site_sms_sign');
	$day = !empty($setting_sms_sign['site_sms_sign']['day']) ? $setting_sms_sign['site_sms_sign']['day'] : 1;

	$user_table = table('users');
	$user_table->searchWithMobile();
	$user_table->searchWithEndtime($day);
	$user_table->searchWithSendStatus();
	$users_expire = $user_table->searchUsersList();

	if (empty($users_expire)) {
		return true;
	}
	foreach ($users_expire as $v) {
		if (empty($v['puid'])) {
			continue;
		}
		if (!empty($v['mobile']) && preg_match(REGULAR_MOBILE, $v['mobile'])) {
			$content = $v['username'] . "即将到期";
			$result = cloud_sms_send($v['mobile'], $content);
		}
		if (is_error($result)) {
			pdo_insert('core_sendsms_log', array('mobile' => $v['mobile'], 'content' => $content, 'result' => $result['errno'] . $result['message'], 'createtime' => TIMESTAMP));
		}
		if ($result) {
			pdo_update('users_profile', array('send_expire_status' => 1), array('uid' => $v['uid']));
		}
	}
	return true;
}


function user_support_urls() {
	global $_W;
	load()->classs('oauth2/oauth2client');
	$types = OAuth2Client::supportLoginType();
	$login_urls = array();
	foreach ($types as $type) {
		if (!empty($_W['setting']['thirdlogin'][$type]['authstate'])) {
			$login_urls[$type] = OAuth2Client::create($type, $_W['setting']['thirdlogin'][$type]['appid'], $_W['setting']['thirdlogin'][$type]['appsecret'])->showLoginUrl();
		}
	}
	if (empty($login_urls)) {
		$login_urls['system'] = true;
	}
	return $login_urls;
}


function user_third_info_register($user_info) {
	global $_W;
	$user_id = pdo_getcolumn('users', array('openid' => $user_info['openid']), 'uid');
	$username = strip_emoji($user_info['nickname']);
	if (empty($user_id)) {
		$status = !empty($_W['setting']['register']['verify']) ? 1 : 2;
		$groupid = intval($_W['setting']['register']['groupid']);
		$salt = random(8);
		pdo_insert('users', array('groupid' => $groupid, 'type' => USER_TYPE_COMMON, 'salt' => $salt,'joindate' => TIMESTAMP, 'status' => $status, 'starttime' => TIMESTAMP, 'register_type' => $user_info['register_type'], 'openid' => $user_info['openid']));
		$user_id = pdo_insertid();
		pdo_update('users', array('username' => $username . $user_id . rand(999,99999), 'password' => user_hash('', $salt)), array('uid' => $user_id));
		pdo_insert('users_profile', array('uid' => $user_id, 'createtime' => TIMESTAMP, 'nickname' => $username, 'avatar' => $user_info['avatar'], 'gender' => $user_info['gender'], 'resideprovince' => $user_info['province'], 'residecity' => $user_info['city'], 'birthyear' => $user_info['year']));
	} else {
		pdo_update('users', array('username' => $username . $user_id . rand(999,99999)), array('uid' => $user_id));
		pdo_update('users_profile', array('nickname' => $username, 'avatar' => $user_info['avatar'], 'gender' => $user_info['gender'], 'resideprovince' => $user_info['province'], 'residecity' => $user_info['city'], 'birthyear' => $user_info['year']), array('uid' => $user_id));
	}
	return $user_id;
}


function user_borrow_oauth_account_list() {
	global $_W;
	$user_have_accounts = uni_user_accounts($_W['uid']);
	$oauth_accounts = array();
	$jsoauth_accounts = array();
	if(!empty($user_have_accounts)) {
		foreach($user_have_accounts as $account) {
			if(!empty($account['key']) && !empty($account['secret'])) {
				if (in_array($account['level'], array(ACCOUNT_SERVICE_VERIFY))) {
					$oauth_accounts[$account['acid']] = $account['name'];
				}
				if (in_array($account['level'], array(ACCOUNT_SUBSCRIPTION_VERIFY, ACCOUNT_SERVICE_VERIFY))) {
					$jsoauth_accounts[$account['acid']] = $account['name'];
				}
			}
		}
	}
	return array(
		'oauth_accounts' => $oauth_accounts,
		'jsoauth_accounts' => $jsoauth_accounts
	);
}
