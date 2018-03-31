<?php
/** 更新缓存
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn: pro/web/source/system/updatecache.ctrl.php : v 25c4f271f9c1 : 2015/09/16 10:49:43 : RenChao $
 */
defined('IN_IA') or exit('Access Denied');

load()->model('cache');
load()->model('setting');
load()->object('cloudapi');

$_W['page']['title'] = '更新缓存 - 设置 - 系统管理';

//清空缓存分为两种，一种为重建，一种为清空。
//清空类的直接把缓存全部删除，不在一条一条的删除
if (checksubmit('submit', true)) {
	$cloud_api = new CloudApi();
	$cloud_cache_key = array(
		'key' => array(cache_system_key('module:all_uninstall'), cache_system_key('user_modules:' . $_W['uid']))
	);
	$cloud_api->post('cache', 'delete', $cloud_cache_key);
	$account_ticket_cache = cache_read('account:ticket');
	pdo_delete('core_cache');
	cache_clean();
	cache_write('account:ticket', $account_ticket_cache);
	unset($account_ticket_cache);

	cache_build_template();
	cache_build_users_struct();
	cache_build_module_status();
	cache_build_cloud_upgrade_module();
	cache_build_setting();
	cache_build_frame_menu();
	cache_build_module_subscribe_type();
	cache_build_cloud_ad();
	iajax(0, '更新缓存成功！', '');
}

template('system/updatecache');