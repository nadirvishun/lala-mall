<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '商户详情';
icheckauth(false);
$id = $sid = intval($_GPC['sid']);
$store = store_fetch($id);
$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

if (0 < $_W['member']['uid']) {
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
}

$activity = store_fetch_activity($sid);
$_share = array('title' => $store['title'], 'desc' => $store['content'], 'imgUrl' => tomedia($store['logo']), 'link' => imurl('wmall/store/share', array('sid' => $sid), true));
include itemplate('store/index');

?>
