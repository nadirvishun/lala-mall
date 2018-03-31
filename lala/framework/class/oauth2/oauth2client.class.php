<?php

/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
abstract class OAuth2Client {
	protected $ak;
	protected $sk;
	protected $login_type;

	public function __construct($ak, $sk) {
		$this->ak = $ak;
		$this->sk = $sk;
	}

	public function getLoginType($login_type) {
		$this->login_type = $login_type;
	}

	public static function supportLoginType(){
		return array('system', 'qq', 'wechat');
	}

	public static function create($type, $appid, $appsecret) {
		$types = self::supportLoginType();
		if (in_array($type, $types)) {
			load()->classs('oauth2/' . $type);
			$type_name = ucfirst($type);
			$obj = new $type_name($appid, $appsecret);
			$obj->getLoginType($type);
			return $obj;
		}
		return null;
	}

	abstract function showLoginUrl($calback_url = '');

	abstract function user();

	public function we7user() {
		load()->model('user');
		$user = $this->user();
		if (is_error($user)) {
			return $user;
		}
		if (in_array($this->login_type, array('qq', 'wechat'))) {
			$user = user_third_info_register($user);
		}
		return $user;
	}


}