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
$_W['page']['title'] = '推广订单';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$basic = get_plugin_config('spread.basic');
$settle = get_plugin_config('spread.settle');
if($op == 'index') {
	$condition = " where uniacid = :uniacid and is_pay = 1 ";
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	if($basic['level'] == 2) {
		$condition .= " and (spread1 = :spread or spread2 = :spread)";
	} elseif($basic['level'] == 1) {
		$condition .= " and spread1 = :spread";
	}
	$params[':spread'] = $_W['member']['uid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status == 0) {
		$condition .= " and status < 5";
	} elseif($status == 5) {
		$condition .= " and status = 5";
	} elseif($status == -1) {
		$condition .= " and status <= 6";
	} elseif($status == 6) {
		$condition .= " and status = 6";
	}

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= ' and id < :id';
		$params[':id'] = trim($_GPC['min']);
	}
	$orders = pdo_fetchall('select id,spread1,spread2,ordersn,paytime,spreadbalance,data,status from' . tablename('tiny_wmall_order'). $condition . ' order by id desc limit 11', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach($orders as &$value) {
			$value['data'] = iunserializer($value['data']);
			$value['paytime'] = date('Y-m-d H:i:s', $value['paytime']);
			$value['spreadid'] = $_W['member']['uid'];
			if($value['spread1'] == $value['spreadid']) {
				$value['commission'] = $value['data']['spread']['commission']['spread1'];
			}
			if($value['spread2'] == $value['spreadid']) {
				$value['commission'] = $value['data']['spread']['commission']['spread2'];
			}
		}
		$min = min(array_keys($orders));
	}
	if($_W['ispost']) {
		$orders = array_values($orders);
		$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$order['data'] = iunserializer($order['data']);
}

include itemplate('order');