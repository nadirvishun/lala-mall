<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'role');
$all_perms = get_all_perms();

if ($op == 'list') {
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

if ($op == 'post') {
	$_W['page']['title'] = '编辑操作员';
	$id = intval($_GPC['id']);

	if (!empty($id)) {
		$user = pdo_get('tiny_wmall_perm_user', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$user['username'] = pdo_fetchcolumn('select username from ' . tablename('users') . ' where uid = :uid', array(':uid' => $user['uid']));
		$user['perms'] = explode(',', $user['perms']);
	}

	$roles = pdo_fetchall('select id, rolename, perms from ' . tablename('tiny_wmall_perm_role') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');

	if (!empty($roles)) {
		foreach ($roles as &$val) {
			$val['perms'] = explode(',', $val['perms']);
		}
	}

	if ($_W['ispost']) {
		load()->model('user');
		$member = array();
		$insert = array('uniacid' => $_W['uniacid'], 'roleid' => intval($_GPC['roleid']), 'status' => intval($_GPC['status']), 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']), 'perms' => implode(',', $_GPC['perms']));

		if (0 < $insert['roleid']) {
			$insert['perms'] = implode(',', array_diff($_GPC['perms'], $roles[$insert['roleid']]['perms']));
		}

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
			pdo_insert('uni_account_users', array('uid' => $insert['uid'], 'uniacid' => $insert['uniacid'], 'role' => 'operator'));
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

		imessage(error(0, '编辑操作员成功'), iurl('perm/user/list'), 'ajax');
	}
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_perm_user', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'del') {
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

include itemplate('perm/user');

?>
