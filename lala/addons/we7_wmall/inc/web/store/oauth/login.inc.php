<?php
//微擎应用 http://www.we7.cc   
function _login($forward = '')
{
	global $_GPC;
	global $_W;
	load()->model('user');
	$username = trim($_GPC['username']);

	if (empty($username)) {
		imessage('请输入要登录的用户名', '', 'info');
	}

	$password = trim($_GPC['password']);

	if (empty($password)) {
		imessage('请输入密码', '', 'info');
	}

	$record = array();
	$temp = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $username));

	if (!empty($temp)) {
		$password = md5(md5($temp['salt'] . $password) . $temp['salt']);

		if ($password == $temp['password']) {
			$record = $temp;
		}
	}

	if (!empty($record)) {
		if ($record['status'] == 2) {
			imessage('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！', '', 'info');
		}

		$stores = clerk_manage($record['id']);

		if (empty($stores)) {
			imessage('您的申请是店员身份,没有权限管理店铺！', '', 'info');
		}

		if (!empty($_W['siteclose'])) {
			imessage('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], '', 'info');
		}

		$cookie = array();
		$cookie['clerk_id'] = $record['id'];
		$cookie['hash'] = $password;
		$session = base64_encode(json_encode($cookie));
		isetcookie('__we7_wmall_store', $session, 7 * 86400);

		if (empty($forward)) {
			$forward = $_GPC['forward'];
		}

		if (empty($forward)) {
			$forward = iurl('store/order/takeout');
		}

		imessage('欢迎回来，' . $record['title'] . '。', $forward, '', 'success');
		return NULL;
	}

	imessage('登录失败，请检查您输入的用户名和密码！', '', 'error');
}

defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '商户登录';

if (checksubmit()) {
	_login($_GPC['referer']);
}

$setting = $_W['setting'];
include itemplate('store/oauth/login');

?>
