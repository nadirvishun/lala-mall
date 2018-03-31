<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '跑腿订单统计';
	$condition = ' WHERE uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
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
		$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1', $params));
		$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1', $params);
		$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 4', $params);
		$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 4', $params));
		$stat['avg_pre_order'] = floatval(0 < $stat['total_success_order'] ? $stat['total_fee'] / $stat['total_success_order'] : 0);
		$stat['total_delivery_fee'] = floatval(pdo_fetchcolumn('select round(sum(delivery_fee), 2) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1', $params));
		$stat['total_deliveryer_fee'] = floatval(pdo_fetchcolumn('select round(sum(deliveryer_fee), 2) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1', $params));
		$stat['total_refund_fee'] = floatval(pdo_fetchcolumn('select round(sum(final_fee), 2) from ' . tablename('tiny_wmall_errander_order') . $condition . ' and refund_status > 1', $params));
		$chart = array(
			'stat'   => $stat,
			'fields' => array('total_success_order', 'total_fee', 'delivery_fee', 'deliveryer_fee', 'refund_fee'),
			'titles' => array('有效订单量', '营业总额', '总入账', '配送费收入', '配送员配送费支出', '总退款')
			);
		$i = $starttime;

		while ($i <= $endtime) {
			$chart['days'][] = $i;

			foreach ($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}

			$i = date('Ymd', strtotime($i) + 86400);
		}

		$records = pdo_fetchall("SELECT stat_day, count(*) as total_success_order,round(sum(total_fee), 2) as total_fee, round(sum(delivery_fee), 2) as delivery_fee, round(sum(deliveryer_fee), 2) as deliveryer_fee\r\n\t\tFROM " . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1 group by stat_day', $params);

		if (!empty($records)) {
			foreach ($records as $record) {
				if (in_array($record['stat_day'], $chart['days'])) {
					foreach ($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		$cancel_records = pdo_fetchall('SELECT stat_day, sum(final_fee) as refund_fee FROM ' . tablename('tiny_wmall_errander_order') . $condition . ' and status = 4 and refund_status > 1 group by stat_day', $params);

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

	$records_temp = pdo_fetchall("SELECT stat_day, count(*) as total_success_order, round(sum(total_fee), 2) as total_fee, round(sum(delivery_fee), 2) as delivery_fee, round(sum(deliveryer_fee), 2) as deliveryer_fee\r\n\t FROM " . tablename('tiny_wmall_errander_order') . $condition . ' and status = 3 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall("SELECT stat_day, round(sum(final_fee), 2) as refund_fee\r\n\t FROM " . tablename('tiny_wmall_errander_order') . $condition . ' and refund_status > 1 group by stat_day', $params, 'stat_day');
	$records = array();
	$i = $endtime;

	while ($starttime <= $i) {
		if (empty($records_temp[$i])) {
			$records[] = array('stat_day' => $i, 'total_success_order' => 0, 'total_fee' => 0, 'deliveryer_fee' => 0, 'delivery_fee' => 0, 'refund_fee' => 0);
		}
		else {
			$records[] = $records_temp[$i];
		}

		$i = date('Ymd', strtotime($i) - 86400);
	}
}

include itemplate('statcenter');

?>
