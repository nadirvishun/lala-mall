<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('plugin');
global $_W;
global $_GPC;
$name = $_W['_controller'];
if (defined('IN_AGENT') && !defined('IN_AGENT_PLUGIN')) {
	$name = 'agent';
}

$plugin = plugin_fetch($name);
$_W['_plugin'] = $plugin;

if (empty($plugin)) {
	imessage('插件不存在', referer(), 'error');
}

if (!$plugin['status']) {
	imessage('系统尚未开启该插件', referer(), 'error');
}

$status = plugin_account_has_perm($plugin['name']);

if (empty($status)) {
	imessage('公众号没有使用该插件的权限', referer(), 'error');
}

$_W['_plugin']['config'] = $_config_plugin = get_plugin_config($_W['_plugin']['name']);
pload()->model($_W['_plugin']['name']);

?>
