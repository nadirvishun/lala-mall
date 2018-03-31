<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '注册配送员';

$config_mall = $_W['we7_wmall']['config']['mall'];
$config_deliveryer = $_W['we7_wmall']['config']['delivery'];
$agreement_delivery = get_config_text('agreement_delivery');


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
	$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '真实姓名不能为空'), '', 'ajax');
	$openid = trim($_GPC['openid']) ? trim($_GPC['openid']) : imessage(error(-1, '微信信息不能为空'), '', 'ajax');
	if($config_deliveryer['mobile_verify_status'] == 1) {
		$code = trim($_GPC['code']);
		$status = check_verifycode($mobile, $code);
		if(!$status) {
			imessage(error(-1, '验证码错误'), '', 'ajax');
		}
	}
	if($config_deliveryer['idCard'] == 1) {
		$idCardOne = trim($_GPC['idCardOne']);
		if(!$idCardOne) {
			imessage(error(-1, '手持身份证照片不能为空'), '', 'ajax');
		}
		$idCardTwo = trim($_GPC['idCardTwo']);
		if(!$idCardTwo) {
			imessage(error(-1, '身份证正面照片不能为空'), '', 'ajax');
		}
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(!empty($deliveryer)) {
		imessage(error(-1, '此手机号已注册, 请直接登录'), '', 'ajax');
	}
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
	if(!empty($is_exist)) {
		imessage(error(-1, '该微信号已绑定其他配送员, 请更换微信号'), '', 'ajax');
	}
	$deliveryer = array(
		'uniacid' => $_W['uniacid'],
		'openid' => trim($_GPC['openid']),
		'nickname' => trim($_GPC['nickname']),
		'avatar' => trim($_GPC['avatar']),
		'mobile' => $mobile,
		'title' => $title,
		'sex' => trim($_GPC['sex']),
		'age' => intval($_GPC['age']),
		'salt' => random(6),
		'token' => random(32),
		'addtime' => TIMESTAMP,
	);
	$deliveryer['password'] = md5(md5($deliveryer['salt'] . $password) . $deliveryer['salt']);
	if($config_deliveryer['settle']['idCard'] == 1) {
		$idCard = array(
			'idCardOne' => trim($_GPC['idCardOne']),
			'idCardTwo' => trim($_GPC['idCardTwo']),
		);
		if(empty($idCard['idCardOne'])){
			imessage(error(-1, '手持身份证照片不能为空'), '', 'ajax');
		}
		if(empty($idCard['idCardTwo'])) {
			imessage(error(-1, '身份证正面照片不能为空'), '', 'ajax');
		}
		$deliveryer['auth_info'] = iserializer($idCard);
	}
	pdo_insert('tiny_wmall_deliveryer', $deliveryer);
	$deliveryer_id = pdo_insertid();
	deliveryer_all(true);
	$key = "we7_wmall_deliveryer_session_{$_W['uniacid']}";
	$cookie = base64_encode(json_encode($deliveryer));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);
	if(empty($forward)) {
		$forward = imurl('delivery/home/index');
	}
	sys_notice_deliveryer_settle($deliveryer_id);
	imessage(error(0, $forward), '', 'ajax');
}

$fans = mc_oauth_userinfo();
if(is_error($fans)) {
	imessage('获取微信信息失败, 请刷新后重新注册', 'refresh', 'info');
}

include itemplate('auth/register');

