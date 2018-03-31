<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '购买记录';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and uid in (select uid from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and (realname like :keyword or mobile like :keyword))';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$uid = intval($_GPC['uid']);

	if (0 < $uid) {
		$condition .= ' and uid = :uid';
		$params[':uid'] = $uid;
	}

	$setmeal_id = (isset($_GPC['setmeal_id']) ? intval($_GPC['setmeal_id']) : -1);

	if (0 < $setmeal_id) {
		$condition .= ' and card_id = :setmeal_id';
		$params[':setmeal_id'] = $setmeal_id;
	}

	$paytime = (isset($_GPC['paytime']) ? intval($_GPC['paytime']) : -1);

	if (0 < $paytime) {
		$condition .= ' and paytime >= :paytime';
		$params[':paytime'] = strtotime('-' . $paytime . 'days', strtotime(date('Y-m-d')));
	}

	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if ($status == 0) {
		$condition .= ' and endtime <= :endtime';
		$params[':endtime'] = TIMESTAMP;
	}
	else {
		if ($status == 1) {
			$endtime = (isset($_GPC['endtime']) ? intval($_GPC['endtime']) : -1);

			if (0 < $endtime) {
				$condition .= ' and endtime > :starttime and endtime <= :endtime';
				$params[':starttime'] = TIMESTAMP;
				$params[':endtime'] = strtotime('+' . $endtime . 'days', strtotime(date('Y-m-d')));
			}
			else {
				$condition .= ' and endtime > :endtime';
				$params[':endtime'] = TIMESTAMP;
			}
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_delivery_cards_order') . $condition, $params);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards_order') . $condition . ' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($orders)) {
		$uids = array();

		foreach ($orders as $order) {
			$uids[] = $order['uid'];
		}

		$uids = implode(',', array_unique($uids));
		$users = pdo_fetchall('select id, uid, realname, avatar from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and uid in (' . $uids . ')', array(':uniacid' => $_W['uniacid']), 'uid');
	}

	$pager = pagination($total, $pindex, $psize);
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']), 'id');
	$pay_types = order_pay_types();
}

include itemplate('order');

?>
