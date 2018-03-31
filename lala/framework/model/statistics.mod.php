<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');



function stat_visit_info($type, $module = '', $daterange = array()) {
	global $_W;
	$result = array();
	if (empty($type)) {
		return $result;
	}
	$params = array('uniacid' => $_W['uniacid']);
	if (!empty($module)) {
		$params['module'] = $module;
	}
	switch ($type) {
		case 'today':
			$params['date'] = date('Ymd');
			break;
		case 'yesterday':
			$params['date'] = date('Ymd', strtotime('-1 days'));
			break;
		case 'week':
			$params['date >'] = date('Ymd', strtotime('-7 days'));
			$params['date <='] = date('Ymd');
			break;
		case 'month':
			$params['date >'] = date('Ymd', strtotime('-30 days'));
			$params['date <='] = date('Ymd');
			break;
		case 'daterange':
			if (empty($daterange)) {
				return stat_visit_info('month', $module);
			}
			$params['date >='] = date('Ymd', strtotime($daterange['start']));
			$params['date <='] = date('Ymd', strtotime($daterange['end']));
			break;
	}
	$result = pdo_getall('stat_visit', $params);

	return $result;
}


function stat_all_visit_statistics($data) {
	$modules = uni_modules();
	$modules_count = count($modules);
	$result = array(
		'visit_sum' => 0,
		'visit_highest' => 0,
		'visit_avg' => 0
	);
	if (empty($data)) {
		return $result;
	}
	foreach ($data as $val) {
		$result['visit_sum'] += $val['count'];
		if ($result['visit_highest'] < $val['count']) {
			$result['visit_highest'] = $val['count'];
		}
	}
	$result['visit_avg'] = round($result['visit_sum'] / $modules_count);
	return $result;
}


function stat_modules_except_system() {
	$modules = uni_modules();
	if (!empty($modules)) {
		foreach ($modules as $key => $module) {
			if (!empty($module['issystem'])) {
				unset($modules[$key]);
			}
		}
	}
	return $modules;
}