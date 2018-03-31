<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '订单列表';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 3;
	$condition .= ' and delivery_status = :status';
	$params[':status'] = $status;

	if($status == 3) {
		if($_deliveryer_type == 1) {
			$condition .= ' and delivery_type = 2';
		} elseif ($_deliveryer_type == 2) {
			$condition .= " and delivery_type = 1 and sid in ({$_stores_cn})";
		} else {
			$condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in ({$_stores_cn})))";
		}
	} else {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $_deliveryer['id'];
		$condition .= ' order by id desc limit 15';
	}
	$orders = pdo_fetchall('SELECT id, serial_sn, addtime, is_pay, pay_type, status, username, mobile, address, delivery_status, plateform_deliveryer_fee, delivery_type, delivery_fee, delivery_time,sid, num, final_fee FROM ' . tablename('tiny_wmall_order') . $condition, $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$stores_id = array();
		foreach($orders as &$da) {
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
			$stores_id[] = $da['sid'];
		}
		$stores_str = implode(',', array_unique($stores_id));
		$stores = pdo_fetchall('select id, title, address, telephone from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');
		$min = min(array_keys($orders));
	}
	$delivery_status = order_delivery_status();
	include itemplate('order/takeoutList');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$status = intval($_GPC['status']);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and id < :id order by id desc limit 15', array(':uniacid' => $_W['uniacid'], ':delivery_status' => $status, ':deliveryer_id' => $_deliveryer['id'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$delivery_status = order_delivery_status();
		foreach ($orders as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $delivery_status[$row['delivery_status']]['color'];
			$row['status_cn'] = $delivery_status[$row['delivery_status']]['text'];
			$row['store'] = pdo_fetch('select address, telephone from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $row['sid']));
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}

if($ta == 'detail') {
	$_W['page']['title'] = '订单详情';
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	if(!empty($order['deliveryer_id']) && $order['deliveryer_id'] != $_deliveryer['id']) {
		imessage('该订单不是由您配送,无权查看订单详情', referer(), 'error');
	}
	$order['deliveryer_transfer_status'] = ($config_takeout['order']['deliveryer_transfer_status'] && ($order['delivery_status'] == 4 || $order['delivery_status'] == 7)) ? "1" : 0;

	$goods = order_fetch_goods($order['id']);
	$activityed = order_fetch_discount($id);
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('id', 'title', 'address', 'telephone', 'logo', 'location_x', 'location_y'));
	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	include itemplate('order/takeoutDetail');
}

if($ta == 'collect') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_assign', array('deliveryer_id' => $_deliveryer['id']));
	if(is_error($result)) {
		imessage($result, '', 'ajax');
	}
	imessage(error(0, '抢单成功'), referer(), 'ajax');
}

if($ta == 'success') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_success', array('deliveryer_id' => $_deliveryer['id']));
	if(is_error($result)) {
		imessage($result, '', 'ajax');
	}
	imessage(error(0, '确认送达成功'), referer(), 'ajax');
}

if($ta == 'notice') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $_deliveryer['id']) {
		imessage(error(-1, '该订单不是由您配送,不能进行微信通知'), '', 'ajax');
	}
	$content = array('title' => $_deliveryer['title'], 'mobile' => $_deliveryer['mobile']);
	order_status_notice($id, 'delivery_notice', $content);
	imessage(error(0, '通知成功'), referer(), 'ajax');
}

if($ta == 'instore') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_instore', array('deliveryer_id' => $deliveryer['id'], 'delivery_handle_type' => 'wechat'));
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	imessage(error(0, '确认到店成功'), referer(), 'ajax');
}

if($ta == 'delivery_transfer') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_transfer', array('deliveryer_id' => $deliveryer['id'], 'reason' => trim($_GPC['reason'])));
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	imessage(error(0, '转单成功'), referer(), 'ajax');
}

if($ta == 'op') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$type = trim($_GPC['type']);
	if($type == 'transfer') {
		$reasons = $config_takeout['order']['deliveryer_transfer_reason'];
		include itemplate('order/takeoutOp');
		die();
	}
}


