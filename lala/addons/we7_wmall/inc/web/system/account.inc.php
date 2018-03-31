<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('plugin');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '公众号权限';
	$condition = ' where 1';
	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and b.name like :keyword';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_perm_account') . ' as a left join ' . tablename('account_wechats') . ' as b on a.uniacid = b.uniacid ' . $condition, $params);
	$accounts = pdo_fetchall('select a.*, b.name from ' . tablename('tiny_wmall_perm_account') . ' as a left join ' . tablename('account_wechats') . ' as b on a.uniacid = b.uniacid ' . $condition . ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($accounts)) {
		foreach ($accounts as &$row) {
			$row['plugins'] = iunserializer($row['plugins']);
		}
	}

	$pager = pagination($total, $pindex, $psize);
	$plugins = plugin_fetchall();
	include itemplate('system/account');
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑公众号权限';
	$uniacid = intval($_GPC['uniacid']);
	$perm = get_account_perm('', $uniacid);

	if ($_W['ispost']) {
		if (empty($uniacid)) {
			imessage(error(-1, '请先选择公众号'), '', 'ajax');
		}

		$data = array('uniacid' => $uniacid, 'max_store' => intval($_GPC['max_store']), 'plugins' => iserializer($_GPC['plugins']));

		if (empty($perm['id'])) {
			pdo_insert('tiny_wmall_perm_account', $data);
		}
		else {
			pdo_update('tiny_wmall_perm_account', $data, array('id' => $perm['id']));
		}

		imessage(error(0, '编辑公众号权限成功'), iurl('system/account/post', array('uniacid' => $uniacid)), 'ajax');
	}

	$plugins = plugin_fetchall();
	include itemplate('system/account');
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_perm_account', array('id' => $id));
	imessage(error(0, '删除公众号权限成功'), '', 'ajax');
}

?>
