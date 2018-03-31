<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

$dos = array('oauth', 'save_oauth', 'uc_setting', 'upload_file');
$do = in_array($do, $dos) ? $do : 'oauth';
$_W['page']['title'] = '公众平台oAuth选项 - 会员中心';

load()->model('user');

if ($do == 'save_oauth') {
	$type = $_GPC['type'];
	$account = trim($_GPC['account']);
	if ($type == 'oauth') {
		$host = $_GPC['host'];
		$host = rtrim($host,'/');
		if(!empty($host) && !preg_match('/^http(s)?:\/\//', $host)) {
			$host = $_W['sitescheme'].$host;
		}
		$data = array(
			'host' => $host,
			'account' => $account,
		);
		pdo_update('uni_settings', array('oauth' => iserializer($data)), array('uniacid' => $_W['uniacid']));
		cache_delete("unisetting:{$_W['uniacid']}");
	}
	if ($type == 'jsoauth') {
		pdo_update('uni_settings', array('jsauth_acid' => $account), array('uniacid' => $_W['uniacid']));
		cache_delete("unisetting:{$_W['uniacid']}");
	}
	iajax(0, '');
}

if ($do == 'oauth') {
	$user_have_accounts = user_borrow_oauth_account_list();
	$oauth_accounts = $user_have_accounts['oauth_accounts'];
	$jsoauth_accounts = $user_have_accounts['jsoauth_accounts'];
	$oauth = pdo_fetchcolumn('SELECT `oauth` FROM '.tablename('uni_settings').' WHERE `uniacid` = :uniacid LIMIT 1',array(':uniacid' => $_W['uniacid']));
	$oauth = iunserializer($oauth) ? iunserializer($oauth) : array();
	$jsoauth = pdo_getcolumn('uni_settings', array('uniacid' => $_W['uniacid']), 'jsauth_acid');
}

template('profile/passport');
