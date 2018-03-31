<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$forward = trim($_GPC['forward']);
if($_W['ispost']) {
	$uid = intval($_GPC['uid']);
	$deviceid = trim($_GPC['deviceid']);
	if(empty($uid) || empty($deviceid)) {
		imessage(error(-1, '登陆失败'), '', 'ajax');
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid_qianfan' => $uid));
	if(empty($member)) {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'openid' => '',
			'uid' => date('His') . random(3, true),
			'uid_qianfan' => $uid,
			'mobile' => trim($_GPC['phone']),
			'nickname' => trim($_GPC['username']),
			'realname' => trim($_GPC['username']),
			'sex' => '',
			'avatar' => trim($_GPC['face']),
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
			'nickname' => trim($_GPC['username']),
			'avatar' => trim($_GPC['face']),
		);
		pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid_qianfan' => $uid));
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
		$forward = $_W['siteroot'] .  'app/index.php?' . base64_decode($_GPC['forward']);
	}
	imessage(error(0, '成功'), $forward, 'ajax');
}
include itemplate('auth/qianfan');
