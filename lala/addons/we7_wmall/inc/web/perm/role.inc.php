<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');
$all_perms = get_all_perms();

if ($op == 'list') {
	$_W['page']['title'] = '角色管理';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if (-1 < $status) {
		$condition .= ' and status = :status';
		$params['status'] = $status;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and rolename like \'%' . $keyword . '%\'';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_perm_role') . $condition, $params);
	$roles = pdo_fetchall('select * from ' . tablename('tiny_wmall_perm_role') . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$user_nums = pdo_fetchall('select count(*) as total, roleid from ' . tablename('tiny_wmall_perm_user') . ' where uniacid = :uniacid group by roleid', array(':uniacid' => $_W['uniacid']), 'roleid');
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑角色';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$insert = array('uniacid' => $_W['uniacid'], 'rolename' => trim($_GPC['rolename']), 'status' => intval($_GPC['status']), 'perms' => implode(',', $_GPC['perms']));

		if (0 < $id) {
			pdo_update('tiny_wmall_perm_role', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_perm_role', $insert);
		}

		imessage(error(0, '编辑角色成功'), iurl('perm/role/list'), 'ajax');
	}

	if (0 < $id) {
		$role = pdo_get('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$role['perms'] = explode(',', $role['perms']);
	}
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_perm_role', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_update('tiny_wmall_perm_user', array('roleid' => 0), array('uniacid' => $_W['uniacid'], 'roleid' => $id));
	}

	imessage(error(0, '删除角色成功'), '', 'ajax');
}

include itemplate('perm/role');

?>
