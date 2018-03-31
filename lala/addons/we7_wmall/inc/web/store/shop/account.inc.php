<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '账户修改';
$id = $_W['clerk']['id'];
$clerk = pdo_get('tiny_wmall_clerk', array('id' => $id, 'uniacid' => $_W['uniacid']));

if ($_W['ispost']) {
	$mobile = trim($_GPC['mobile']);

	if (!preg_match(IREGULAR_MOBILE, $mobile)) {
		imessage(error(-1, '手机号格式错误'), referer(), 'ajax');
	}

	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));

	if (!empty($is_exist)) {
		imessage(error(-1, '该手机号已绑定其他店员, 请更换手机号'), referer(), 'ajax');
	}

	$openid = trim($_GPC['wechat']['openid']);
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));

	if (!empty($is_exist)) {
		imessage(error(-1, '该微信信息已绑定其他店员, 请更换微信信息'), referer(), 'ajax');
	}

	$data = array('uniacid' => $_W['uniacid'], 'mobile' => $mobile, 'title' => trim($_GPC['title']), 'openid' => $openid, 'nickname' => trim($_GPC['wechat']['nickname']), 'avatar' => trim($_GPC['wechat']['avatar']));
	$password = trim($_GPC['password']);

	if (!empty($password)) {
		$data['salt'] = random(6);
		$data['password'] = md5(md5($data['salt'] . $password) . $data['salt']);
	}

	pdo_update('tiny_wmall_clerk', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '修改成功'), iurl('store/shop/account', array('id' => $id)), 'ajax');
}

include itemplate('store/shop/account');

?>
