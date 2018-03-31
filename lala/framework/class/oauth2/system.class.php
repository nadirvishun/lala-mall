<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

class System extends OAuth2Client {
	private $calback_url;

	public function __construct($ak, $sk) {
		parent::__construct($ak, $sk);
	}

	public function showLoginUrl($calback_url = '') {
		return '';
	}

	public function user() {
		global $_GPC;
		$username = trim($_GPC['username']);
		pdo_query('DELETE FROM'.tablename('users_failed_login'). ' WHERE lastupdate < :timestamp', array(':timestamp' => TIMESTAMP-300));
		$failed = pdo_get('users_failed_login', array('username' => $username, 'ip' => CLIENT_IP));
		if ($failed['count'] >= 5) {
			return error('-1', '输入密码错误次数超过5次，请在5分钟后再登录');
		}
		if (!empty($_W['setting']['copyright']['verifycode'])) {
			$verify = trim($_GPC['verify']);
			if (empty($verify)) {
				return error('-1', '请输入验证码');
			}
			$result = checkcaptcha($verify);
			if (empty($result)) {
				return error('-1', '输入验证码错误');
			}
		}
		if (empty($username)) {
			return error('-1', '请输入要登录的用户名');
		}
		$member['username'] = $username;
		$member['password'] = $_GPC['password'];
		if (empty($member['password'])) {
			return error('-1', '请输入密码');
		}
		return $member;
	}
}