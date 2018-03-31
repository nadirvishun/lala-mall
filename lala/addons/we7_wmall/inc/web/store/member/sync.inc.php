<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('member');
$_W['page']['title'] = '同步会员数据';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	if ($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		pdo_update('tiny_wmall_store_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}

	$uids = pdo_getall('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid), array('uid'), 'uid');
	$uids = array_keys($uids);
}

include itemplate('store/member/sync');

?>
