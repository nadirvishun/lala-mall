<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '店铺列表';
$sid_str = implode(', ', array_unique(array_keys($sids)));
$stores = pdo_fetchall('select id, title, logo from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id in (' . $sid_str . ')', array(':uniacid' => $_W['uniacid']));

if (count($stores) == 1) {
	header('location:' . imurl('manage/home/switch', array('sid' => $stores[0]['id'])));
	exit();
}

include itemplate('home/index');

?>
