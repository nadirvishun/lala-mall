<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '找回密码';
$config_mall = $_W['we7_wmall']['config']['mall'];

if($_W['isajax']) {
	$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
	$password = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '请输入密码'), '', 'ajax');
	$length = strlen($password);
	if($length < 8 || $length > 20) {
		imessage(error(-1, '请输入8-20密码'), '', 'ajax');
	}
	if(!preg_match(IREGULAR_PASSWORD, $password)) {
		imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
	}
	$code = trim($_GPC['code']);
	$status = check_verifycode($mobile, $code);
	if(!$status) {
		imessage(error(-1, '验证码错误'), '', 'ajax');
	}
	$manager = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($manager)) {
		imessage(error(-1, '此手机号未注册'), '', 'ajax');
	}
	$update = array(
		'salt' => random(6, true),
	);
	$update['password'] = md5(md5($update['salt'] . trim($password)) . $update['salt']);
	pdo_update('tiny_wmall_clerk', $update, array('uniacid' => $_W['uniacid'], 'id' => $manager['id']));
	$manager['hash'] = $update['password'];
	$key = "we7_wmall_manager_session_{$_W['uniacid']}";
	$cookie = base64_encode(json_encode($manager));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);
	if(empty($forward)) {
		$forward = imurl('manage/home/index');
	}
	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/forget');