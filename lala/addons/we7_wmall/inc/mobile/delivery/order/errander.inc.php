<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
include WE7_WMALL_PLUGIN_PATH . 'errander/model.php';
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($_delivery_type == 2) {
	imessage('您没有配送订单的权限', imurl('wmall/member/mine'), 'error');
}
$config_errander = get_plugin_config('errander');

if($ta == 'list') {
	$_W['page']['title'] = '订单列表';
	$condition = ' where a.uniacid = :uniacid and a.is_pay = 1 and a.status != 4';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition .= ' and delivery_status = :status';
	$params[':status'] = $status;
	if($status != 1) {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $_deliveryer['id'];
		$condition .= ' order by a.id desc limit 15';
	}
	$orders = pdo_fetchall('SELECT  a.*, b.title FROM' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id '. $condition, $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$types = errander_types();
		$delivery_status = errander_order_delivery_status();
		foreach($orders as &$row) {
			$row['order_type_cn'] = $types[$row['order_type']]['text'];
			$row['order_type_bg'] = $types[$row['order_type']]['bg'];
			$row['delivery_status_cn'] = $delivery_status[$row['delivery_status']]['text'];
			$row['delivery_status_color'] = $delivery_status[$row['delivery_status']]['color'];
			$row['verification_code'] =  ($config_errander['verification_code'] == 1) ? 1 : 0;
			if(empty($row['buy_address'])) {
				$row['buy_address'] = '用户未指定,您可以自由寻找商户购买';
			}
			if(empty($order['goods_price'])) {
				$row['goods_price'] = '未填写,请联系顾客沟通';
			}
		}
		$min = min(array_keys($orders));
	}

	include itemplate('order/erranderList');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$status = intval($_GPC['status']);
	$orders = pdo_fetchall('select a.*, b.title from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id  where a.uniacid = :uniacid and a.status != 4 and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and a.id < :id order by id desc limit 15', array(':uniacid' => $_W['uniacid'], ':delivery_status' => $status, ':deliveryer_id' => $_deliveryer['id'], ':id' => $id), 'id');
	if(!empty($orders)) {
		$types = errander_types();
		foreach($orders as &$order) {
			$order['addtime'] = date('Y-m-d H:i',$order['addtime']);
			$order['order_type_cn'] = $types[$order['order_type']]['text'];
			$order['order_type_bg'] = $types[$order['order_type']]['bg'];
			$order['delivery_status_cn'] = $delivery_status[$order['delivery_status']]['text'];
			$order['delivery_status_color'] = $delivery_status[$order['delivery_status']]['color'];
		}
	}
	$min = 0;
	if(!empty($orders)) {
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}

if($ta == 'detail') {
	$_W['page']['title'] = '订单详情';
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	if(!empty($order['deliveryer_id']) && $order['deliveryer_id'] != $_deliveryer['id']) {
		imessage('该订单不是由您配送,无权查看订单详情', referer(), 'error');
	}
	$order['deliveryer_transfer_status'] = ($config_errander['deliveryer_transfer_status'] && ($order['delivery_status'] == 2 || $order['delivery_status'] == 3)) ? "1" : 0;
	$order['verification_code'] =  ($config_errander['verification_code'] == 1) ? 1 : 0;
	include itemplate('order/erranderDetail');
}

//抢单
if($ta == 'collect') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_assign', array('deliveryer_id' => $_deliveryer['id']));
	if(is_error($status)) {
		imessage(error(-1, $status['message']), '', 'ajax');
	}
	imessage(error(0, '抢单成功'), referer(), 'ajax');
}

//确认已取货
if($ta == 'instore') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_instore', array('deliveryer_id' => $_deliveryer['id']));
	if(is_error($status)) {
		imessage(error(-1, $status['message']), '', 'ajax');
	}
	imessage(error(0, '确认取货成功'), referer(), 'ajax');
}

//配送成功
if($ta == 'success') {
	$id = intval($_GPC['id']);
	$status = errander_order_status_update($id, 'delivery_success', array('deliveryer_id' => $_deliveryer['id'], 'code' => trim($_GPC['code'])));
	if(is_error($status)) {
		imessage(error(-1, $status['message']), '', 'ajax');
	}
	imessage(error(0, '确认送达成功'), referer(), 'ajax');
}

if($ta == 'delivery_transfer') {
	$id = intval($_GPC['id']);
	$result = errander_order_status_update($id, 'delivery_transfer', array('deliveryer_id' => $_deliveryer['id'], 'reason' => trim($_GPC['reason'])));
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	imessage(error(0, '转单成功'), referer(), 'ajax');
}

if($ta == 'op') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$type = trim($_GPC['type']);
	if($type == 'transfer') {
		$reasons = $config_errander['deliveryer_transfer_reason'];
		include itemplate('order/erranderOp');
		die();
	}
}





