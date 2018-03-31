<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('deliveryer');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '店员入口';
	$urls = array('wmerchant' => iurl('store/oauth/login', array(), true), 'register' => imurl('manage/auth/register', array(), true), 'login' => imurl('manage/auth/login', array(), true));
	include itemplate('clerk/cover');
}

?>
