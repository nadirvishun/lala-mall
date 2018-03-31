<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '营业统计';
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$days = (isset($_GPC['days']) ? intval($_GPC['days']) : 0);

	if ($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	}
	else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime('-' . $days . ' days', $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}

	if ($_W['isajax']) {
		$stat = array();
		$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['store_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
		$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
		$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params));
		$stat['avg_pre_order'] = floatval(0 < $stat['total_success_order'] ? $stat['total_fee'] / $stat['total_success_order'] : 0);
		$chart = array(
			'stat'   => $stat,
			'fields' => array('total_success_order', 'total_fee', 'store_final_fee', 'store_discount_fee', 'plateform_discount_fee', 'plateform_serve_fee', 'plateform_delivery_fee', 'total_cancel_order', 'total_cancel_fee'),
			'titles' => array('有效订单量', '营业总额', '总收入', '商家补贴', '平台补贴', '平台服务费', '平台配送费', '无效订单量', '损失营业额')
			);
		$i = $starttime;

		while ($i <= $endtime) {
			$chart['days'][] = $i;

			foreach ($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}

			$i = date('Ymd', strtotime($i) + 86400);
		}

		$records = pdo_fetchall("SELECT stat_day, count(*) as total_success_order,round(sum(total_fee), 2) as total_fee, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee\r\n\t\tFROM " . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params);

		if (!empty($records)) {
			foreach ($records as $record) {
				if (in_array($record['stat_day'], $chart['days'])) {
					foreach ($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		$cancel_records = pdo_fetchall("SELECT stat_day, count(*) as total_cancel_order, sum(total_fee) as total_cancel_fee\r\n\t\tFROM " . tablename('tiny_wmall_order') . $condition . ' and status = 6 group by stat_day', $params);

		if (!empty($cancel_records)) {
			foreach ($cancel_records as $record) {
				if (in_array($record['stat_day'], $chart['days'])) {
					foreach ($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		foreach ($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}

		imessage(error(0, $chart), '', 'ajax');
	}

	$records_temp = pdo_fetchall("SELECT stat_day, count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee\r\n\t FROM " . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall("SELECT stat_day, count(*) as total_cancel_order, round(sum(store_final_fee), 2) as store_final_fee\r\n\t FROM " . tablename('tiny_wmall_order') . $condition . ' and status = 6 group by stat_day', $params, 'stat_day');
	$records = array();
	$i = $endtime;

	while ($starttime <= $i) {
		if (empty($records_temp[$i])) {
			$records[] = array('stat_day' => $i, 'total_success_order' => 0, 'final_fee' => 0, 'store_final_fee' => 0, 'plateform_discount_fee' => 0, 'store_discount_fee' => 0, 'plateform_serve_fee' => 0, 'plateform_delivery_fee' => 0);
		}
		else {
			$records[] = $records_temp[$i];
		}

		$i = date('Ymd', strtotime($i) - 86400);
	}
}

include itemplate('store/statistic/order');

?>
