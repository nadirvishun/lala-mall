<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'menu');

if ($op == 'menu') {
	$_W['page']['title'] = '菜单设置';

	if ($_W['ispost']) {
		$menu = $_GPC['menu'];
		set_plugin_config('diypage.menu', $menu);
		imessage(error(0, '菜单设置成功'), referer(), 'ajax');
	}

	$config_menu = get_plugin_config('diypage.menu');
	$menus = pdo_getall('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid']), array('id', 'name'));
}

include itemplate('mall');

?>
