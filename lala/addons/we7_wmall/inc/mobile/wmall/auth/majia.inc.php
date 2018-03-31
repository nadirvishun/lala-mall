<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('plugin');
pload()->model('majiaApp');

$forward = trim($_GPC['forward']);
//$token = get_majia_agent('token');
$token = trim($_GPC['token']);
if(empty($token)) {
	include itemplate('auth/majia');
	die;
}

isetcookie('__majia_token', $token, 1800);
$_GPC['__majia_token'] = $token;

$userinfo = get_user_info();
if(is_error($userinfo)) {
	imessage($userinfo['message'], '', 'info');
}
$uid = intval($userinfo['user_id']);
$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid_majia' => $uid));
if(empty($member)) {
	$member = array(
		'uniacid' => $_W['uniacid'],
		'openid' => '',
		'uid' => date('His') . random(3, true),
		'uid_majia' => $uid,
		'mobile' => trim($userinfo['phone']),
		'nickname' => trim($userinfo['name']),
		'realname' => trim($userinfo['name']),
		'sex' => ($userinfo['sex'] == 1 ? '男' : '女'),
		'avatar' => trim($userinfo['head']),
		'is_sys' => 2, //模拟用户
		'status' => 1,
		'token' => random(32),
		'addtime' => TIMESTAMP,
		'salt' => random(6, true),
		'register_type' => 'app',
	);
	$member['password'] = md5(md5($member['salt'] . trim($deviceid)) . $member['salt']);
	pdo_insert('tiny_wmall_members', $member);
} else {
	$data = array(
		'nickname' => trim($userinfo['name']),
		'avatar' => trim($userinfo['head']),
	);
	pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid_majia' => $uid));
}

$cookie = array(
	'uid' => $member['uid'],
	'hash' => $member['password'],
);
$cookie = base64_encode(json_encode($cookie));
$key = "we7_wmall_member_session_{$_W['uniacid']}";
isetcookie($key, $cookie, 3600);

if(empty($forward)) {
	$forward = imurl('wmall/home/index');
} else {
	$forward = $_W['siteroot'] . 'app/index.php?' . base64_decode($_GPC['forward']);
}
if($_W['ispost']) {
	imessage(error(0, '成功'), $forward, 'ajax');
}
header('location:' . $forward);
die;