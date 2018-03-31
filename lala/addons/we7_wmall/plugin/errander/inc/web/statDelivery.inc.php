<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$config = get_plugin_config('errander');  
$config_takeout['delivery_timeout_limit'] = intval($config['delivery_timeout_limit']); 
if(empty($config_takeout['delivery_timeout_limit'])) {
	$config_takeout['delivery_timeout_limit'] = 45;
}
$config_takeout['delivery_before_limit'] = intval($config['delivery_before_limit']);  
if(empty($config_takeout['delivery_before_limit'])) {
	$config_takeout['delivery_before_limit'] = 30;
}
if($op == 'index') {
	$_W['page']['title'] = '配送统计';
	$deliveryers = deliveryer_fetchall(0); 
	$condition = " where uniacid = :uniacid and status = 3 and deliveryer_id != 0";  
	$params[':uniacid'] = $_W['uniacid'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= " and deliveryer_id = :deliveryer_id";
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= " and stat_day >= :stat_day and stat_day < :end_day";
		$params[':stat_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= " and stat_day >= :stat_day";
		$params[':stat_day'] = $starttime;
	}

	$condition_normal = "{$condition} and (delivery_success_time - delivery_assign_time < {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_timeout = "{$condition} and (delivery_success_time - delivery_assign_time > {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_before = "{$condition} and (delivery_success_time - delivery_assign_time <= {$config_takeout['delivery_before_limit']} * 60)";
	if($_W['isajax']) {
		$stat = array();
		$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition, $params);   
		$stat['total_normal_order'] = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_errander_order') . $condition_normal, $params); 
		$stat['total_timeout_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition_timeout, $params);  
		$stat['total_before_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition_before, $params);  
		$stat['avg_normal_delivery_time'] = floatval(pdo_fetchcolumn('select round(avg(delivery_success_time - delivery_assign_time) / 60, 2) from ' . tablename('tiny_wmall_errander_order') . " {$condition_normal}", $params)); 
		if($stat['total_success_order'] == 0) {
			$stat['percent_normal'] = 0;  
			$stat['percent_timeout'] = 0;  
		} else {
			$stat['percent_normal'] = round(($stat['total_success_order'] - $stat['total_timeout_order']) / $stat['total_success_order'] , 2) * 100;
			$stat['percent_timeout'] = round($stat['total_timeout_order'] / $stat['total_success_order'] ,2) * 100;
		}
		$chart = array(
			'stat' => $stat,
			'fields' => array('total_success_order', 'total_normal_order', 'total_timeout_order', 'total_before_order', 'avg_normal_delivery_time'),
			'titles' => array('总配送', '正常送达订单', '超时订单', '提前订单', '普通单平均配送时长'),
		);
		for($i = $starttime; $i <= $endtime;) {
			$chart['days'][] = $i;
			foreach($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}
			$i = date('Ymd', strtotime($i) + 86400);
		}
		$records = pdo_fetchall('SELECT
			stat_day,
			count(*) as total_success_order
			FROM ' . tablename('tiny_wmall_errander_order') . " {$condition} group by stat_day", $params, 'stat_day');

		if(!empty($records)) {
			foreach($records as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}
		$records_normal = pdo_fetchall('SELECT
			stat_day,
			count(*) as total_normal_order,
			round(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_normal_delivery_time
			FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_normal} group by stat_day", $params, 'stat_day');
		if(!empty($records_normal)) {
			foreach($records_normal as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		$records_timeouts = pdo_fetchall('SELECT
			stat_day,
			count(*) as total_timeout_order
			FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_timeout} group by stat_day", $params, 'stat_day');
		if(!empty($records_timeouts)) {
			foreach($records_timeouts as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		$records_befores = pdo_fetchall('SELECT
			stat_day,
			count(*) as total_before_order
			FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_before} group by stat_day", $params, 'stat_day');
		if(!empty($records_befores)) {
			foreach($records_befores as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		foreach($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}
		message(error(0, $chart), '', 'ajax');

	}

	$records_temp = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_success_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition} group by stat_day", $params, 'stat_day');
	$records_normal = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_normal_order,
		round(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_normal_delivery_time,
		round(avg(delivery_assign_time - paytime) / 60, 2) as avg_delivery_notify_time,
		round(avg(delivery_instore_time - delivery_assign_time) / 60, 2) as avg_delivery_instore_time,
		round(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_delivery_success_time
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_normal} group by stat_day", $params, 'stat_day');

	$records_timeout = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_timeout_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_timeout} group by stat_day", $params, 'stat_day');

	$records_before = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_before_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_before} group by stat_day", $params, 'stat_day');

	$records = array();
	$basic = array(
		'stat_day' => 0,
		'total_success_order' => 0,
		'total_timeout_order' => 0,
		'total_before_order' => 0,
		'avg_delivery_time' => 0,
		'avg_normal_delivery_time' => 0,
		'percent_normal' => 0,
		'percent_timeout' => 0,
		'avg_delivery_notify_time' => 0,
		'avg_delivery_instore_time' => 0,
		'avg_delivery_success_time' => 0,
	);
	for($i = $endtime; $i >= $starttime;) {
		$basic['stat_day'] = $i;
		$records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
		$records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
		$records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
		$records_before[$i] = empty($records_before[$i]) ? array() : $records_before[$i];
		$data= array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i], $records_before[$i]);
		if(!empty($data['total_success_order'])) {
			$data['percent_normal'] = round(($data['total_success_order'] - $data['total_timeout_order']) / $data['total_success_order'], 2) * 100;
			$data['percent_timeout'] = round($data['total_timeout_order'] / $data['total_success_order'], 2) * 100;
		}
		$records[] = $data;
		$i = date('Ymd', strtotime($i) - 86400);
	}
	include itemplate('deliveryIndex');
}

if($op == 'day') {
	$_W['page']['title'] = '配送详情';
	$condition = " where uniacid = :uniacid and status = 3 and deliveryer_id != 0 ";
	$params[':uniacid'] = $_W['uniacid'];
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		if(is_array($_GPC['stat_day'])) {
			$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
			$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		} else {
			$stat_day = intval($_GPC['stat_day']);
			$starttime = str_replace('-', '', $stat_day);
			$endtime = str_replace('-', '', date('Ymd', (strtotime($stat_day) + 86399)));
		}
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}

	$condition_normal = "{$condition} and (delivery_success_time - delivery_assign_time < {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_timeout = "{$condition} and (delivery_success_time - delivery_assign_time > {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_before = "{$condition} and (delivery_success_time - delivery_assign_time <= {$config_takeout['delivery_before_limit']} * 60)";

	$stat = array();
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition, $params);    
	$stat['total_normal_order'] = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_errander_order') . $condition_normal, $params);  
	$stat['total_timeout_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition_timeout, $params); 
	$stat['total_before_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition_before, $params); 
	$stat['avg_normal_delivery_time'] = floatval(pdo_fetchcolumn('select round(avg(delivery_success_time - delivery_assign_time) / 60, 2) from ' . tablename('tiny_wmall_errander_order') . " {$condition_normal}", $params)); 
	if(!$stat['total_success_order']) {
		$stat['percent_normal'] = 0;
		$stat['percent_timeout'] = 0;
	} else {
		$stat['percent_normal'] = round(($stat['total_success_order'] - $stat['total_timeout_order']) / $stat['total_success_order'], 2) * 100;
		$stat['percent_timeout'] = round($stat['total_timeout_order'] / $stat['total_success_order'], 2) * 100;
	}

	$records_temp = pdo_fetchall('SELECT
		stat_day,
		deliveryer_id,
		count(*) as total_success_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition} group by deliveryer_id", $params, 'deliveryer_id');

	$records_normal = pdo_fetchall('SELECT
		stat_day,
		deliveryer_id,
		count(*) as total_normal_order,
		round(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_normal_delivery_time,
		round(avg(delivery_assign_time - paytime) / 60, 2) as avg_delivery_notify_time,
		round(avg(delivery_instore_time - delivery_assign_time) / 60, 2) as avg_delivery_instore_time,
		round(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_delivery_success_time
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_normal} group by deliveryer_id", $params, 'deliveryer_id');

	$records_timeout = pdo_fetchall('SELECT
		stat_day,
		deliveryer_id,
		count(*) as total_timeout_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_timeout} group by deliveryer_id", $params, 'deliveryer_id');

	$records_before = pdo_fetchall('SELECT
		stat_day,
		deliveryer_id,
		count(*) as total_before_order
	 FROM ' . tablename('tiny_wmall_errander_order') . " {$condition_before} group by deliveryer_id", $params, 'deliveryer_id');
	$records = array();
	$basic = array(
		'id' => 0,
		'total_success_order' => 0,
		'total_timeout_order' => 0,
		'total_normal_order' => 0,
		'total_before_order' => 0,
		'avg_delivery_time' => 0,
		'avg_normal_delivery_time' => 0,
		'percent_normal' => 0,
		'percent_timeout' => 0,
		'avg_delivery_notify_time' => 0,
		'avg_delivery_instore_time' => 0,
		'avg_delivery_success_time' => 0,
	);
	$records = array();
	$deliveryers = deliveryer_fetchall(0);
	foreach($deliveryers as $deliveryer) {
		$i = $deliveryer['deliveryer_id'];
		$records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
		$records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
		$records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
		$records_before[$i] = empty($records_before[$i]) ? array() : $records_before[$i];
		$data= array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i], $records_before[$i]);
		if(!empty($data['total_success_order'])) {
			$data['percent_normal'] = round(($data['total_success_order'] - $data['total_timeout_order']) / $data['total_success_order'], 2) * 100;
			$data['percent_timeout'] = round($data['total_timeout_order'] / $data['total_success_order'], 2) * 100;
			$data['total_percent_timeout'] = round($data['total_timeout_order'] / $stat['total_timeout_order'], 4) * 100;
		}
		$data['title'] = $deliveryer['deliveryer']['title'];
		$records[] = $data;
	}
	$records = array_sort($records, 'total_success_order', SORT_DESC);
	unset($deliveryers);
	

	include itemplate('deliveryDay');
}