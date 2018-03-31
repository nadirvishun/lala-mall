<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
$_W['page']['title'] = $store['title'];

if (empty($store)) {
	imessage('门店不存在或已删除', 'close', 'info');
}

$activity = store_fetch_activity($sid);
$hot_goods = pdo_fetchall('select id,title,price,sailed,thumb from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 order by id desc limit 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
include itemplate('store/share');

?>
