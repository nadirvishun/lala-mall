<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'role');

if ($op == 'role') {
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

if ($op == 'role_post') {
	$_W['page']['title'] = '编辑角色';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$insert = array('uniacid' => $_W['uniacid'], 'rolename' => trim($_GPC['rolename']), 'status' => intval($_GPC['status']));

		if (0 < $id) {
			pdo_update('tiny_wmall_perm_role', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_perm_role', $insert);
		}

		imessage(error(0, '编辑角色成功'), iurl('system/perm/role'), 'ajax');
	}

	if (0 < $id) {
		$role = pdo_get('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
}

if ($op == 'role_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_perm_role', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'role_del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	imessage(error(0, '删除角色成功'), '', 'ajax');
}

if ($op == 'user') {
	$_W['page']['title'] = '操作员管理';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if (-1 < $status) {
		$condition .= ' and a.status = :status';
		$params['status'] = $status;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and b.username like \'%' . $keyword . '%\'';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . $condition, $params);
	$users = pdo_fetchall('select a.*, b.username from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . $condition . ' order by a.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$roles = pdo_fetchall('select id, rolename from ' . tablename('tiny_wmall_perm_role') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');
}

if ($op == 'user_post') {
	$_W['page']['title'] = '编辑操作员';
	$id = intval($_GPC['id']);

	if (!empty($id)) {
		$user = pdo_get('tiny_wmall_perm_user', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$user['username'] = pdo_fetchcolumn('select username from ' . tablename('users') . ' where uid = :uid', array(':uid' => $user['uid']));
	}

	if ($_W['ispost']) {
		load()->model('user');
		$member = array();
		$insert = array('uniacid' => $_W['uniacid'], 'roleid' => intval($_GPC['roleid']), 'status' => intval($_GPC['status']), 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']));
		$member['username'] = trim($_GPC['username']) ? trim($_GPC['username']) : imessage(error(-1, '操作员用户名不能为空'), referer(), 'ajax');

		if (empty($id)) {
			if (!preg_match(REGULAR_USERNAME, $member['username'])) {
				imessage(error(-1, '必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。'), referer(), 'ajax');
			}

			if (user_check(array('username' => $member['username']))) {
				imessage(error(-1, '非常抱歉，此用户名已经被注册，你需要更换注册名称！'), referer(), 'ajax');
			}

			$member['password'] = $_GPC['password'];

			if (istrlen($member['password']) < 8) {
				imessage(error(-1, '必须输入密码，且密码长度不得低于8位。'), referer(), 'ajax');
			}

			$member['starttime'] = TIMESTAMP;
			$uid = user_register($member);
			$insert['uid'] = $uid;
			pdo_insert('tiny_wmall_perm_user', $insert);
		}
		else {
			if (!empty($_GPC['password'])) {
				$password = $_GPC['password'];

				if (istrlen($password) < 8) {
					imessage(error(-1, '必须输入密码，且密码长度不得低于8位。'), referer(), 'ajax');
				}

				$salt = random(8);
				$password = user_hash($password, $salt);
				pdo_update('users', array('password' => $password, 'salt' => $salt), array('uid' => $user['uid']));
			}

			pdo_update('tiny_wmall_perm_user', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}

		imessage(error(0, '编辑操作员成功'), iurl('system/perm/user'), 'ajax');
	}

	$roles = pdo_fetchall('select id, rolename from ' . tablename('tiny_wmall_perm_role') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
}

if ($op == 'user_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_perm_user', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'user_del') {
	$uids = $_GPC['id'];

	if (!is_array($uids)) {
		$uids = array($uids);
	}

	foreach ($uids as $uid) {
		pdo_delete('tiny_wmall_perm_user', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		pdo_delete('users', array('uid' => $uid));
	}

	imessage(error(0, '删除操作员成功'), '', 'ajax');
}

include itemplate('system/perm');

?>
