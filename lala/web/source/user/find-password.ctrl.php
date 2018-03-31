<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');

$dos = array('find_password', 'valid_mobile', 'valid_code', 'set_password', 'success');
$do = in_array($do, $dos) ? $do : 'find_password';

$mobile = trim($_GPC['mobile']);
if (in_array($do, array('valid_mobile', 'valid_code', 'set_password'))) {
	if (empty($mobile)) {
		iajax(-1, '手机号不能为空');
	}
	if (!preg_match(REGULAR_MOBILE, $mobile)) {
		iajax(-1, '手机号格式不正确');
	}

	$user_profile = table('users');
	$find_mobile = $user_profile->userProfileMobile($mobile);
	if (empty($find_mobile)) {
		iajax(-1, '手机号不存在');
	}
}
if ($do == 'valid_mobile') {
	iajax(0, '本地校验成功');
}

if ($do == 'valid_code') {
	if ($_W['isajax'] && $_W['ispost']) {
		$code = trim($_GPC['code']);
		$image_verify =trim($_GPC['verify']);

		if (empty($code)) {
			iajax(-1, '短信验证码不能为空');
		}

		if (empty($image_verify)) {
			iajax(-1, '图形验证码不能为空');
		}

		$captcha = checkcaptcha($image_verify);
		if (empty($captcha)) {
			iajax(-1, '图形验证码错误,请重新获取');
		}

		$user_table = table('users');
		$code_info = $user_table->userVerifyCode($mobile, $code);
		if (empty($code_info)) {
			iajax(-1, '短信验证码不正确');
		}
		if ($code_info['createtime'] + 120 < TIMESTAMP) {
			iajax(-1, '短信验证码已过期，请重新获取');
		}

		iajax(0, '');
	} else {
		iajax(-1, '非法请求');
	}
}

if ($do == 'set_password') {
	if ($_W['isajax'] && $_W['ispost']) {
		$password = $_GPC['password'];
		$repassword = $_GPC['repassword'];
		if (empty($password) || empty($repassword)) {
			iajax(-1, '密码不能为空');
		}

		if ($password != $repassword) {
			iajax(-1, '两次密码不一致');
		}

		$user_info = user_single($find_mobile['uid']);
		$password = user_hash($password, $user_info['salt']);
		if ($password == $user_info['password']) {
			iajax(-2, '不能使用最近使用的密码');
		}
		$result = pdo_update('users', array('password' => $password), array('uid' => $user_info['uid']));
		if (empty($result)) {
			iajax(0, '设置密码成功');
		}
		iajax(0);
	}
}
template('user/find-password');