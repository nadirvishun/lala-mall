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

if($op == 'index') {
	$_W['page']['title'] = '外卖订单统计';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'));

	$condition = ' WHERE uniacid = :uniacid and order_type <= 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
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
	if($_W['isajax']) {
		$stat = array();
		$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
		$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
		$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params));
		$stat['avg_pre_order'] = floatval($stat['total_success_order'] > 0 ? ($stat['total_fee'] / $stat['total_success_order']) : 0);
		$stat['total_serve_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_serve_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_delivery_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_delivery_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_deliveryer_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_deliveryer_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['plateform_discount_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_discount_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_refund_fee'] = floatval(pdo_fetchcolumn('select round(sum(refund_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and refund_status > 1', $params));

		$chart = array(
			'stat' => $stat,
			'fields' => array('total_success_order', 'total_fee', 'final_fee', 'plateform_delivery_fee', 'plateform_serve_fee', 'plateform_deliveryer_fee', 'plateform_discount_fee', 'refund_fee'),
			'titles' => array('有效订单量','营业总额','总入账','平台配送费收入','佣金收入','配送员配送费支出','平台补贴','总退款'),
		);
		for($i = $starttime; $i <= $endtime;) {
			$chart['days'][] = $i;
			foreach($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}
			$i = date('Ymd', strtotime($i) + 86400);
		}
		$records = pdo_fetchall('SELECT stat_day,
			count(*) as total_success_order,
			round(sum(total_fee), 2) as total_fee,
			round(sum(final_fee), 2) as final_fee,
			round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee,
			round(sum(plateform_deliveryer_fee), 2) as plateform_deliveryer_fee,
			round(sum(plateform_serve_fee), 2) as plateform_serve_fee,
			round(sum(plateform_discount_fee), 2) as plateform_discount_fee,
			round(sum(refund_fee), 2) as refund_fee
		FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params);
		if(!empty($records)) {
			foreach($records as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}
		$cancel_records = pdo_fetchall('SELECT stat_day, count(*) as total_cancel_order, sum(total_fee) as total_cancel_fee
		FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 6 group by stat_day', $params);
		if(!empty($cancel_records)) {
			foreach($cancel_records as $record) {
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

	$records_temp = pdo_fetchall('SELECT stat_day,
		count(*) as total_success_order,
		round(sum(total_fee), 2) as total_fee,
		round(sum(final_fee), 2) as final_fee,
		round(sum(plateform_serve_fee), 2) as plateform_serve_fee,
		round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee,
		round(sum(plateform_deliveryer_fee), 2) as plateform_deliveryer_fee,
		round(sum(plateform_discount_fee), 2) as plateform_discount_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall('SELECT stat_day, round(sum(refund_fee), 2) as refund_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and refund_status > 1 group by stat_day', $params, 'stat_day');
	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		if(empty($records_temp[$i])) {
			$records[] = array(
				'stat_day' => $i,
				'total_success_order' => 0,
				'total_fee' => 0,
				'final_fee' => 0,
				'plateform_serve_fee' => 0,
				'plateform_deliveryer_fee' => 0,
				'plateform_delivery_fee' => 0,
				'plateform_discount_fee' => 0,
				'refund_fee' => 0,
			);
		} else {
			$records[] = $records_temp[$i];
		}
		$i = date('Ymd', strtotime($i) - 86400);
	}
}
include itemplate('statcenter/takeout');



