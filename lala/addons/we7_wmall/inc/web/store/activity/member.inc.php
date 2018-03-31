<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '顾客列表';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$condition = ' where a.uniacid = :uniacid and a.sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$key = trim($_GPC['key']);

	if (!empty($key)) {
		$time = strtotime('-30 days');

		if ($key == 'success_30') {
			$condition .= ' and a.success_last_time >= :time';
		}
		else if ($key == 'noorder_30') {
			$condition .= ' and a.success_last_time < :time';
		}
		else {
			if ($key == 'cancel_30') {
				$condition .= ' and a.cancel_last_time >= :time';
			}
		}

		$params[':time'] = $time;
	}

	$sort = trim($_GPC['sort']);
	$sort_val = intval($_GPC['sort_val']);

	if (!empty($sort)) {
		if ($sort_val == 1) {
			$condition .= ' ORDER BY a.' . $sort . ' DESC';
		}
		else {
			$condition .= ' ORDER BY a.' . $sort . ' ASC';
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	$data = pdo_fetchall('select a.*,b.nickname from ' . tablename('tiny_wmall_store_members') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	foreach ($data as &$row) {
		if (0 < $row['success_num']) {
			$row['aveage'] = round($row['success_price'] / $row['success_num'], 2);
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

include itemplate('store/activity/member');

?>
