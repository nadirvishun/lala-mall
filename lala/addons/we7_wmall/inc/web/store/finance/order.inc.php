<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'log');

if ($ta == 'log') {
	$_W['page']['title'] = '交易记录';
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$status = intval($_GPC['status']);

	if (0 < $status) {
		if ($status == 7) {
			$condition .= ' and (status != 5 and status != 6)';
		}
		else {
			$condition .= ' and status = :status';
			$params[':status'] = $status;
		}
	}

	$stat_day = intval($_GPC['stat_day']);

	if (0 < $stat_day) {
		$condition .= ' and stat_day = :stat_day';
		$params[':stat_day'] = $stat_day;
		$starttime = $endtime = strtotime($stat_day);
	}
	else {
		if (!empty($_GPC['addtime'])) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']) + 86399;
		}
		else {
			$today = strtotime(date('Y-m-d'));
			$starttime = strtotime('-15 day', $today);
			$endtime = $today + 86399;
		}

		$condition .= ' and addtime >= :starttime and addtime <= :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}

	$condition .= ' order by id desc';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order') . $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$order_status = order_status();
	$order_type = order_types();
}

include itemplate('store/finance/order');

?>
