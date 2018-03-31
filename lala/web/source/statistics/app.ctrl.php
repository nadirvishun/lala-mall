<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');
load()->model('statistics');

$dos = array('display', 'get_account_api', 'get_module_api');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'display') {
	$today = stat_visit_info('today');
	$yesterday = stat_visit_info('yesterday');
	$today_module_api = stat_all_visit_statistics($today);
	$yesterday_module_api = stat_all_visit_statistics($yesterday);
	template('statistics/display');
}

if ($do == 'get_account_api') {
}

if ($do == 'get_module_api') {
	$modules = array();
	$data = array();
	$modules_info = stat_modules_except_system();
	array_unshift($modules_info, array('name' => 'wesite', 'title' => '微站'));
	foreach ($modules_info as $info) {
		$modules[] = mb_substr($info['title'], 0, 5, 'utf-8');
	}
	$today = stat_visit_info('today');
	if (empty($today)) {
		foreach ($modules_info as $module) {
			$data[] = 0;
		}
	} else {
		foreach ($modules_info as $module) {
			$have_count = false;
			foreach ($today as $val) {
				if ($module['name'] == $val['module']) {
					$data[] = $val['count'];
					$have_count = true;
				}
			}
			if (empty($have_count)) {
				$data[] = 0;
			}
		}
	}

	iajax(0, array('modules' => $modules, 'data' => $data));
}