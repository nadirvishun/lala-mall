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
	$password = (trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '请输入密码'), '', 'ajax'));
	$length = strlen($password);
	if (($length < 8) || (20 < $length)) {
		imessage(error(-1, '请输入8-20密码'), '', 'ajax');
	}

	if (!preg_match(IREGULAR_PASSWORD, $password)) {
		imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
	}

	$code = trim($_GPC['code']);
	$status = check_verifycode($mobile, $code);

	if (!$status) {
		imessage(error(-1, '验证码错误'), '', 'ajax');
	}

	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

	if (empty($member)) {
		imessage(error(-1, '此手机号未注册'), '', 'ajax');
	}

	$update = array('mobile_audit' => 1, 'salt' => random(6, true));
	$update['password'] = md5(md5($update['salt'] . trim($password)) . $update['salt']);
	pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
	$member['hash'] = $update['password'];
	$key = 'we7_wmall_member_session_' . $_W['uniacid'];
	$cookie = array('uid' => $member['uid'], 'hash' => $member['hash']);
	$cookie = base64_encode(json_encode($cookie));
	isetcookie($key, $cookie, 7 * 86400);
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);

	if (empty($forward)) {
		$forward = imurl('wmall/home/index');
	}

	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/forget');

?>
