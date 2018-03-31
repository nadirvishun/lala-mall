<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '店员登陆';
$config_mall = $_W['we7_wmall']['config']['mall'];
if (empty($_GPC['force']) && (is_weixin() || !empty($_GPC['we7_wmall_manager_session_' . $_W['uniacid']]))) {
	header('location: ' . imurl('manage/home/index'));
	exit();
}

if ($_W['isajax']) {
	$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax'));
	$manager = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

	if (empty($manager)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}

	$password = md5(md5($manager['salt'] . trim($_GPC['password'])) . $manager['salt']);

	if ($password != $manager['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}

	if (empty($manager['token'])) {
		$token = $manager['token'] = random(32);
		pdo_update('tiny_wmall_clerk', array('token' => $token), array('uniacid' => $_W['uniacid'], 'id' => $manager['id']));
	}

	$manager['hash'] = $password;
	$key = 'we7_wmall_manager_session_' . $_W['uniacid'];
	$cookie = base64_encode(json_encode($manager));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);

	if (empty($forward)) {
		$forward = imurl('manage/home/index');
	}

	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/login');

?>
