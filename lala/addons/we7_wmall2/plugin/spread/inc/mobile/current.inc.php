<?php
/**
 * 外送系统
 * @author 微猫源码
 * @QQ 2058430070
 * @url http://www.weixin2015.cn/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$_W['page']['title'] = '佣金明细';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$condition = ' where uniacid = :uniacid and spreadid = :spreadid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':spreadid' => $_W['member']['uid'],
	);
	$trade_type = isset($_GPC['trade_type']) ? intval($_GPC['trade_type']) : 0;
	if($trade_type > 0) {
		$condition .= " and trade_type = {$trade_type}";
	}

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= ' and id < :id';
		$params[':id'] = trim($_GPC['min']);
	}
	$current = pdo_fetchall('select * from' . tablename('tiny_wmall_spread_current_log') . $condition . ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($current)) {
		foreach($current as &$v) {
			$v['addtime'] = date('Y-m-d H:i', $v['addtime']);
		}
		$min = min(array_keys($current));
	}
	if($_W['ispost']) {
		$current = array_values($current);
		$respon = array('errno' => 0, 'message' => $current, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$detail = pdo_get('tiny_wmall_spread_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
}

include itemplate('current');