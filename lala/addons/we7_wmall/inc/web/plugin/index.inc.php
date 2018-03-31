<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('plugin');
mload()->model('cloud');
global $_W;
global $_GPC;
$_W['page']['title'] = '应用中心';
$_W['plugin_types'] = plugin_types();
$plugins = plugin_fetchall();
$perms = get_account_perm('plugins');
$_W['plugins'] = array();

foreach ($plugins as $row) {
	if (!empty($perms) && !in_array($row['name'], $perms)) {
		continue;
	}

	if (check_perm($row['name'])) {
		$_W['plugins'][$row['type']][] = $row;
		++$i;
	}
}

if (!$i) {
	imessage('没有可用的插件,请联系平台管理员开通', '', 'info');
}

include itemplate('plugin/index');

?>
