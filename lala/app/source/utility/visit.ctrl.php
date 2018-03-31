<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('app');

$dos = array('showjs');
$do = in_array($do, $dos) ? $do : 'showjs';
if ($do == 'showjs') {
	$module_name = !empty($_GPC['module_name']) ? $_GPC['module_name'] : 'wesite';
	$uniacid = !empty($_GPC['uniacid']) ? intval($_GPC['uniacid']) : 0;
	app_update_today_visit($module_name);
}
