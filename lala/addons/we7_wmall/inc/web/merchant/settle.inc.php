<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '入驻列表';
	$condition = ' where uniacid = :uniacid and addtype = 2';
	$params[':uniacid'] = $_W['uniacid'];
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if (0 < $status) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}

	$agentid = intval($_GPC['agentid']);

	if (0 < $agentid) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store') . $condition . ' ORDER BY id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($lists)) {
		foreach ($lists as &$li) {
			$li['user'] = store_manager($li['id']);
		}
	}

	$store_status = store_status();
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'audit') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));

	if (empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}

	$clerk = store_manager($store['id']);

	if (empty($clerk)) {
		imessage(error(-1, '获取门店申请人失败'), '', 'ajax');
	}

	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$remark = trim($_GPC['remark']);
	sys_notice_settle($store['id'], 'clerk', $remark);
	imessage(error(0, '门店审核成功'), '', 'ajax');
}

include itemplate('merchant/settle');

?>
