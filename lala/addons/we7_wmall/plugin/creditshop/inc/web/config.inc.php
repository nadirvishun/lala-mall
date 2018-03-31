<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '对接兑吧设置';

	if ($_W['ispost']) {
		$bargain = array('status' => intval($_GPC['status']), 'appkey' => trim($_GPC['appkey']), 'appsecret' => trim($_GPC['appsecret']));
		set_plugin_config('creditshop', $bargain);
		imessage(error(0, '对接兑吧设置成功'), 'refresh', 'ajax');
	}

	$config = get_plugin_config('creditshop');
	$urls = array('enter' => imurl('creditshop/enter', array(), true), 'consume' => $_W['siteroot'] . 'addons/we7_wmall/plugin/creditshop/notify.php?i=' . $_W['uniacid'] . '&channel=consume', 'notice' => $_W['siteroot'] . 'addons/we7_wmall/plugin/creditshop/notify.php?i=' . $_W['uniacid'] . '&channel=notice');
}

include itemplate('config');

?>
