<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @微擎应用   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function mload() {
	static $mloader;
	if(empty($mloader)) {
		$mloader = new Mloader();
	}
	return $mloader;
}

function pload() {
	static $ploader;
	if(empty($ploader)) {
		$ploader = new Ploader();
	}
	return $ploader;
}

function check_plugin_perm($name) {
	global $_W;
	static $_plugins = array();
	if(isset($_plugins[$name])) {
		return $_plugins[$name];
	}
	$dir = WE7_WMALL_PLUGIN_PATH . $name . '/inc';
	if(!is_dir($dir)) {
		$_plugins[$name] = false;
		return $_plugins[$name];
	}
	$plugin = pdo_get('tiny_wmall_plugin', array('name' => $name), array('id', 'name'));
	if(empty($plugin)) {
		$_plugins[$name] = false;
		return $_plugins[$name];
	}
	mload()->model('common');
	$perms = get_account_perm();
	if(empty($perms) || in_array($name, $perms['plugins'])) {
		$_plugins[$name] = true;
	} else {
		$_plugins[$name] = false;
	}
	return $_plugins[$name];
}

function check_plugin_exist($name) {
	global $_W;
	static $_plugins_exist = array();
	if(isset($_plugins_exist[$name])) {
		return $_plugins_exist[$name];
	}
	$plugin = pdo_get('tiny_wmall_plugin', array('name' => $name), array('id', 'name'));
	if(empty($plugin)) {
		$_plugins_exist[$name] = false;
		return $_plugins_exist[$name];
	}
	$_plugins_exist[$name] = true;
	return $_plugins_exist[$name];
}

function fans_info_query($openid) {
	global $_W;
	static $account_api;
	if(empty($account_api)) {
		$account_api = WeAccount::create();
	}
	$fan = $account_api->fansQueryInfo($openid, true);
	if(!is_error($fan) && $fan['subscribe'] == 1) {
		$fan['nickname'] = stripcslashes($fan['nickname']);
		$fan['remark'] = !empty($fan['remark']) ? stripslashes($fan['remark']) : '';
	} else {
		$fan = array();
	}
	return $fan;
}

