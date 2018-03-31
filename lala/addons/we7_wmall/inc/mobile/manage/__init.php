<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('manage');
mload()->model('clerk');
$relation = array();
if($_W['_action'] != 'auth') {
	icheckmanage();
	$sids = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $_W['manager']['id']), array(), 'sid');
	if(empty($sids)) {
		imessage('您没有管理店铺的权限', imurl('wmall/member/mine'), 'error');
	}
	$relation = clerk_push_token($_W['manager']['id']);
	if($_W['_action'] != 'home') {
		$sid = intval($_GPC['sid']) ? intval($_GPC['sid']) : intval($_GPC['__mg_sid']);
		if(empty($sid)) {
			imessage('请先选择要管理的门店', imurl('manage/home/index'), 'error');
		}
		$permiss = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $_W['manager']['id']));
		if(empty($permiss)) {
			isetcookie('__mg_sid', 0, -1000);
			imessage('您没有该门店的管理权限', referer(), 'error');
		}
		$extra = iunserializer($permiss['extra']);
		if(empty($extra)) {
			$extra = array(
				'accept_wechat_notice' => 0,
				'accept_voice_notice' => 0
			);
		}
		$_W['manager']['extra'] = $extra;
		isetcookie('__mg_sid', $sid, 86400 * 7);
		$_GPC['__mg_sid'] = $sid;
		$store = store_fetch($sid);
		$store['account'] = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $store['id']));
		if(!empty($store['account'])) {
			$store['account']['wechat'] = iunserializer($store['account']['wechat']);
		}
		$_W['we7_wmall']['store'] = $store;
	}
}
$_W['role'] = 'clerker';
$_W['role_cn'] = "店铺店员:{$_W['manager']['title']}";

