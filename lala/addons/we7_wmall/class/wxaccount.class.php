<?php
//微擎应用 http://www.we7.cc   
class WxAccount
{
	protected $acc;

	public function __construct($account = '')
	{
		global $_W;

		if (empty($account)) {
			$account = $_W['acid'];
		}

		if (!is_array($account)) {
			$account = $account;
			$account = account_fetch($account);
		}
		else {
			if (empty($account['acid'])) {
				$account['acid'] = $account['appid'];
			}

			if (empty($account['type'])) {
				$account['type'] = 1;
			}

			$account['key'] = $account['appid'];
			$account['secret'] = $account['appsecret'];
		}

		$acc = WeAccount::create($account);

		if (is_error($acc)) {
			return $acc;
		}

		$this->acc = $acc;
	}

	public function media_download($media_id)
	{
		global $_W;
		$access_token = $this->acc->getAccessToken();

		if (is_error($access_token)) {
			return $access_token;
		}

		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
		$response = ihttp_get($url);

		if (is_error($response)) {
			return error(-1, '访问公众平台接口失败, 错误: ' . $response['message']);
		}

		$result = @json_decode($response['content'], true);

		if (!empty($result['errcode'])) {
			return error(-1, '访问微信接口错误, 错误代码: ' . $result['errcode'] . ', 错误信息: ' . $result['errmsg']);
		}

		load()->func('file');
		$path = 'images/' . $_W['uniacid'] . '/' . date('Y/m/');
		$filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, 'jpg');
		$filename = $path . $filename;
		$status = file_write($filename, $response['content']);

		if (!$status) {
			return error(-1, '保存图片失败');
		}

		$status = file_remote_upload($filename);

		if (is_error($status)) {
			return error(-1, '上传到远程失败');
		}

		return $filename;
	}

	public function getOauthCodeUrl($callback, $state = '')
	{
		return $this->acc->getOauthCodeUrl(urlencode($callback), $state);
	}

	public function getOauthInfo($code)
	{
		$result = $this->acc->getOauthInfo($code);

		if (!empty($result['errcode'])) {
			return error(-1, '错误码:' . $result['errcode'] . ',详细信息:' . $result['errmsg']);
		}

		return $result;
	}
}

defined('IN_IA') || exit('Access Denied');
load()->func('communication');

?>
