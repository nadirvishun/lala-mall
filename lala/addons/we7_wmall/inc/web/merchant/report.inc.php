<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '商户投诉';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if (0 < $status) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}

	$addtime = (isset($_GPC['addtime']) ? intval($_GPC['addtime']) : -1);

	if (0 < $addtime) {
		$condition .= ' and addtime >= :addtime';
		$params[':addtime'] = strtotime('-' . $addtime . 'days', strtotime(date('Y-m-d')));
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 40;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_report') . $condition, $params);
	$reports = pdo_fetchall('select * from ' . tablename('tiny_wmall_report') . $condition . ' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($reports)) {
		$stores = array();

		foreach ($reports as &$row) {
			$row['thumbs'] = iunserializer($row['thumbs']);
			$stores[] = $row['sid'];
		}

		$stores_str = implode(',', $stores);
		$stores = pdo_fetchall('select id,title from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id in (' . $stores_str . ')', array(':uniacid' => $_W['uniacid']), 'id');
	}

	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_report', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '设置状态成功'), '', 'ajax');
}

include itemplate('merchant/report');

?>
