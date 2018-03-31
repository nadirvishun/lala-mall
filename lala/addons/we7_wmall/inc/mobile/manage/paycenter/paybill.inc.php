<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
mload()->model('order');
$_W['page']['title'] = '买单';

if($ta == 'index') {
	$condition = ' WHERE a.uniacid = :uniacid and sid = :sid and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	if(trim($_GPC['pay_type']) == 'all') {
		$pay_type = 'all';
	} else {
		$pay_type = trim($_GPC['pay_type']);
		$condition .= ' and a.pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	
	$min = 0;
	$orders = pdo_fetchall('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' ORDER BY addtime desc limit 15', $params, 'addtime');
	if(!empty($orders)) {
		$min = min(array_keys($orders));
	}
	include itemplate('paycenter/paybill');
}


if($ta == 'detail') {
	$id = intval($_GPC['id']);
	$condition = ' WHERE a.uniacid = :uniacid and a.id = :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':id' => $id,
	);
	$order = pdo_fetch('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	include itemplate('paycenter/paybill');
}

if($ta = 'more') {
	$addtime = intval($_GPC['min']);
	$condition = ' WHERE a.uniacid = :uniacid and a.addtime < :addtime and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':addtime' => $addtime,
	);
	if(trim($_GPC['pay_type']) == 'all') {
		$pay_type = 'all';
	} else {
		$pay_type = trim($_GPC['pay_type']);
		$condition .= ' and a.pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	$orders = pdo_fetchall('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' ORDER BY addtime desc limit 15', $params, 'addtime');
	if(!empty($orders)) {
		foreach ($orders as &$value) {
			$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}
