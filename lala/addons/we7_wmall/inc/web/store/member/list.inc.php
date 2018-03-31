<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('member');
$_W['page']['title'] = '顾客列表';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and uid in (select uid from ' . tablename('tiny_wmall_members') . ' where (realname like :keyword or mobile like :keyword))';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$sort = trim($_GPC['sort']);
	$sort_val = intval($_GPC['sort_val']);

	if (!empty($sort)) {
		if ($sort_val == 1) {
			$condition .= ' ORDER BY ' . $sort . ' DESC';
		}
		else {
			$condition .= ' ORDER BY ' . $sort . ' ASC';
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 40;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_members') . $condition . ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($data)) {
		$users = array();

		foreach ($data as $da) {
			$users[] = $da['uid'];
		}

		$users = implode(',', $users);
		$users = pdo_fetchall('select * from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and uid in (' . $users . ')', array(':uniacid' => $_W['uniacid']), 'uid');
	}

	$pager = pagination($total, $pindex, $psize);
	$stat = member_amount_stat($sid);
}

if ($ta == 'sync') {
	if ($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$member = pdo_get('tiny_wmall_members', array('uid' => $uid));

		if (!empty($member)) {
			$data = array();

			if (strexists($member['avatar'], '/132132')) {
				$data['avatar'] = str_replace('/132132', '/132', $member['avatar']);
			}

			if (($member['sex'] == '1') || ($member['sex'] == '2')) {
				$data['sex'] = $member['sex'] == '1' ? '男' : '女';
			}

			pdo_update('tiny_wmall_members', $data, array('uid' => $uid));
		}

		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}

	$uids = pdo_getall('tiny_wmall_members', array('uniacid' => $_W['uniacid']), array('uid'), 'uid');
	$uids = array_keys($uids);
}

include itemplate('store/member/list');

?>
