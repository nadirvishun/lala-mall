<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
load()->func('communication');

define('Wechat_PLATFORM_API_OAUTH_LOGIN_URL', 'https://open.weixin.qq.com/connect/qrconnect?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_login&state=%s#wechat_redirect');
define('Wechat_PLATFORM_API_GET_ACCESS_TOKEN', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code');
define('Wechat_PLATFORM_API_GET_USERINFO', 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN');
class Wechat extends OAuth2Client {
	private $calback_url;

	public function __construct($ak, $sk, $calback_url = '') {
		global $_W;
		parent::__construct($ak, $sk);
		$this->calback_url = $_W['siteroot'] . 'web/index.php';
	}

	public function showLoginUrl($calback_url = '') {
		global $_W;
		$redirect_uri = urlencode($this->calback_url);
		$state = !empty($state) ? $state : $_W['token'];
		$state = $state . 'from=wechat';
		return sprintf(Wechat_PLATFORM_API_OAUTH_LOGIN_URL, $this->ak, $redirect_uri, $state);
	}

	public function getUserInfo($token, $openid) {
		if (empty($openid) || empty($token)) {
			return error(-1, '参数错误');
		}
		$user_info_url = sprintf(Wechat_PLATFORM_API_GET_USERINFO, $token, $openid);
		$response = $this->requestApi($user_info_url);
		return $response;
	}

	public function getOauthInfo() {
		global $_GPC, $_W;
		$state = $_GPC['state'];
		$code = $_GPC['code'];
		if (empty($state) || empty($code)) {
			return error(-1, '参数错误');
		}
		if ($state != $_W['token'] . 'from=wechat') {
			return error(-1, '重新登陆');
		}
		$access_url = sprintf(Wechat_PLATFORM_API_GET_ACCESS_TOKEN, $this->ak, $this->sk, $code, urlencode($this->calback_url));
		$response = $this->requestApi($access_url);
		return $response;
	}

	public function user() {
		$oauth_info = $this->getOauthInfo();
		$openid = $oauth_info['openid'];
		$user_info = $this->getUserInfo($oauth_info['access_token'], $openid);
		if (is_error($user_info)) {
			return $user_info;
		}
		$user = array();
		$user['type'] = USER_TYPE_COMMON;
		$user['avatar'] = $user_info['headimgurl'];
		$user['openid'] = $user_info['openid'];
		$user['register_type'] = USER_REGISTER_TYPE_WECHAT;
		$user['nickname'] = $user_info['nickname'];
		$user['gender'] = $user_info['sex'];
		$user['resideprovince'] = $user_info['province'];
		$user['residecity'] = $user_info['city'];
		$user['birthyear'] = '';
		return $user;
	}

	protected function requestApi($url, $post = '') {
		$response = ihttp_request($url, $post);

		$result = @json_decode($response['content'], true);
		if(is_error($response)) {
			return error($result['errcode'], "访问公众平台接口失败, 错误详情: {$result['errmsg']}");
		}
		if(empty($result)) {
			return error(-1, "接口调用失败, 元数据: {$response['meta']}");
		} elseif(!empty($result['errcode'])) {
			return error($result['errcode'], "访问公众平台接口失败, 错误: {$result['errmsg']}");
		}
		return $result;
	}
}