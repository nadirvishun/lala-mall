<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '配送员登陆';
$config_mall = $_W['we7_wmall']['config']['mall'];
if (empty($_GPC['force']) && (is_weixin() || !empty($_GPC['we7_wmall_deliveryer_session_' . $_W['uniacid']]))) {
	header('location: ' . imurl('delivery/home/index'));
	exit();
}

if ($_W['isajax']) {
	$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax'));
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

	if (empty($deliveryer)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}

	$password = md5(md5($deliveryer['salt'] . trim($_GPC['password'])) . $deliveryer['salt']);

	if ($password != $deliveryer['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}

	$deliveryer['hash'] = $password;
	$key = 'we7_wmall_deliveryer_session_' . $_W['uniacid'];
	$cookie = base64_encode(json_encode($deliveryer));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);

	if (empty($forward)) {
		$forward = imurl('delivery/home/index');
	}

	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/login');

?>
