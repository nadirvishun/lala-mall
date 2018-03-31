<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$_W['page']['title'] = '系统消息';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	if (empty($_W['clerk'])) {
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_notice') . ' where uniacid = :uniacid and type = :type and status = 1', array(':uniacid' => $_W['uniacid'], ':type' => 'store'));
		$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_notice') . ' where uniacid = :uniacid and type = :type and status = 1 order by displayorder desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':type' => 'store'));
	}
	else {
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . ' as b on a.id = b.notice_id where b.uid = :uid and uniacid = :uniacid and type = :type and status = 1 ', array(':uid' => $_W['clerk']['id'], ':uniacid' => $_W['uniacid'], ':type' => 'store'));
		$data = pdo_fetchall('select a.*,b.uid,b.is_new from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . ' as b on a.id = b.notice_id where b.uid = :uid and uniacid = :uniacid and type = :type and status = 1 order by id desc, displayorder desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, array(':uid' => $_W['clerk']['id'], ':uniacid' => $_W['uniacid'], ':type' => 'store'));
	}

	$pager = pagination($total, $pindex, $psize);
}

if ($ta == 'detail') {
	$_W['page']['title'] = '消息详情';
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id, 'status' => 1, 'type' => 'store'));

	if (empty($item)) {
		imessage('该消息不存在或已删除', iurl('store/shop/notice/list'), 'error');
	}

	pdo_update('tiny_wmall_notice_read_log', array('is_new' => 0), array('notice_id' => $id, 'uid' => $_W['clerk']['id']));
}

include itemplate('store/shop/notice');

?>
