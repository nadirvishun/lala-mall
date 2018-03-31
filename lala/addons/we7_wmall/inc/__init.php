<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->func('common');

$routers = str_replace('//', '/', trim($_GPC['r'], '/'));
$routers = explode('.', $routers);

$_W['_do'] = !empty($_W['_do']) ? $_W['_do'] : trim($_GPC['do']);
$_W['_controller'] = !empty($_W['_controller']) ? $_W['_controller'] : trim($_GPC['ctrl']);
$_W['_action'] = trim($_GPC['ac']);
$_W['_op'] = trim($_GPC['op']);
$_W['_ta'] = trim($_GPC['ta']);
$_W['_router'] = implode('/', array($_W['_controller'], $_W['_action'], $_W['_op']));
$_plugins = pdo_getall('tiny_wmall_plugin', array(), array('name', 'title'), 'name');
in_array($_W['_controller'], array_keys($_plugins)) && define('IN_PLUGIN', 1);
if(strexists($_W['siteurl'], 'web/index.php')) {
	define('IN_MANAGE', 1);
} elseif(strexists($_W['siteurl'], 'web/wagent.php')) {
	define('IN_PLUGIN', 1);
	define('IN_AGENT', 1);
}
if(defined('IN_SYS')) {
	if(empty($_W['uniacid'])) {
		message('公众号信息错误,请重新管理公众号', url('account/display'), 'info');
	}
	if($_W['_controller'] == 'store') {
		define('IN_MERCHANT', 1);
	}
	if(empty($_W['_controller'])) {
		$_W['_controller'] = 'dashboard';
		$_W['_action'] = 'index';
	}
	require(WE7_WMALL_PATH . "inc/web/__init.php");
	$file_init = WE7_WMALL_PATH . "inc/web/{$_W['_controller']}/__init.php";
	$file_path = WE7_WMALL_PATH . "inc/web/{$_W['_controller']}/{$_W['_action']}.inc.php";
	if(defined('IN_MERCHANT')) {
		$file_path = WE7_WMALL_PATH . "inc/web/{$_W['_controller']}/{$_W['_action']}/{$_W['_op']}.inc.php";
		if(!is_file($file_path)) {
			imessage("控制器 {$_W['_controller']} 方法 {$_W['_action']}/{$_W['_op']} 未找到!", '', 'info');
		}
	} elseif(defined('IN_PLUGIN')) {
		$plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
		require($plugin_init);
		$file_init = WE7_WMALL_PLUGIN_PATH . "{$_W['_controller']}/inc/web/__init.php";
		$file_path = WE7_WMALL_PLUGIN_PATH . "{$_W['_controller']}/inc/web/{$_W['_action']}.inc.php";
		if(defined('IN_AGENT')) {
			$file_init = WE7_WMALL_PLUGIN_PATH . "agent/inc/web/__init.php";
			$file_path = WE7_WMALL_PLUGIN_PATH . "agent/inc/web/manage/{$_W['_controller']}/{$_W['_action']}.inc.php";
			if(in_array($_W['_controller'],  array('errander', 'bargain'))) {
				define('IN_AGENT_PLUGIN', 1);
				$plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
				require($plugin_init);
				$file_path = WE7_WMALL_PLUGIN_PATH . "agent/plugin/{$_W['_controller']}/inc/web/{$_W['_action']}.inc.php";
			}
		}
	}
	if(is_file($file_init)) {
		require($file_init);
	}
	if(!is_file($file_path)) {
		imessage("控制器 {$_W['_controller']} 方法 {$_W['_action']} 未找到!", '', 'info');
	}
} else {
	require(WE7_WMALL_PATH . "inc/mobile/__init.php");
	$file_init = WE7_WMALL_PATH . "inc/mobile/{$_W['_controller']}/__init.php";
	$file_path = WE7_WMALL_PATH . "inc/mobile/{$_W['_controller']}/{$_W['_action']}/{$_W['_op']}.inc.php";
	if(defined('IN_PLUGIN')) {
		$plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
		require($plugin_init);
		$file_init = WE7_WMALL_PLUGIN_PATH . "{$_W['_controller']}/inc/mobile/__init.php";
		$file_path = WE7_WMALL_PLUGIN_PATH . "{$_W['_controller']}/inc/mobile/{$_W['_action']}.inc.php";
	}
	if(is_file($file_init)) {
		require($file_init);
	}
	if(!is_file($file_path)) {
		imessage("控制器 {$_W['_controller']} 方法 {$_W['_action']}/{$_W['_op']} 未找到!", 'close', 'error');
	}
}
require($file_path);








