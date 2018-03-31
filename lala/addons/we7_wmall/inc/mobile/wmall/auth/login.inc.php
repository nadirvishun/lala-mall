<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '登陆';
$config_mall = $_W['we7_wmall']['config']['mall'];
if (is_weixin() || !empty($_GPC['we7_wmall_member_session_' . $_W['uniacid']])) {
	header('location: ' . referer());
	exit();
}

if ($_W['isajax']) {
	$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax'));
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

	if (empty($member)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}

	$password = md5(md5($member['salt'] . trim($_GPC['password'])) . $member['salt']);

	if ($password != $member['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}

	$member['hash'] = $password;
	$key = 'we7_wmall_member_session_' . $_W['uniacid'];
	$cookie = array('uid' => $member['uid'], 'hash' => $member['hash']);
	$cookie = base64_encode(json_encode($cookie));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);

	if (empty($forward)) {
		$forward = imurl('wmall/home/index');
	}
	else {
		$forward = $_W['siteroot'] . 'app/index.php?' . base64_decode(urldecode($forward));
	}

	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/login');

?>
