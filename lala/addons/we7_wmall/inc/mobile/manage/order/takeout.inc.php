<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
mload()->model('deliveryer');
$_W['page']['title'] = '订单管理';
$config = get_system_config('takeout.order');
if($ta == 'list') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid AND (order_type = 1 or order_type = 2)';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	if($status == 1 && $config['show_no_pay'] == 1) {
		$condition .= ' AND is_pay = 1';
	}
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach($orders as &$da) {
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
			$da['goods'] = order_fetch_goods($da['id']);
		}
		$min = min(array_keys($orders));
	}
	$order_status = order_status();
	$pay_types = order_pay_types();
	$deliveryers = deliveryer_fetchall($sid);
	include itemplate('order/takeoutList');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid and id < :id AND (order_type = 1 or order_type = 2)';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':id' => $id
	);
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 10', $params, 'id');
	if(!empty($orders)) {
		$pay_types = order_pay_types();
		$order_status = order_status();
		foreach ($orders as &$row) {
			$row['goods'] = order_fetch_goods($row['id']);
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $order_status[$row['status']]['color'];
			$row['status_cn'] = $order_status[$row['status']]['text'];
			$row['delivery_mode'] = $store['delivery_mode'];
			$row['pay_type_class'] = '';
			if($row['is_pay'] == 1) {
				$row['pay_type_class'] = 'have-pay';
				if($row['pay_type'] == 'delivery') {
					$row['pay_type_class'] = 'delivery-pay';
				}
			}
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}

if($ta == 'detail' || $ta == 'consume') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	$goods = order_fetch_goods($order['id']);
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$activityed = order_fetch_discount($id);
	$logs = order_fetch_status_log($id);
	if(!empty($logs)) {
		$maxid = max(array_keys($logs));
	}
	if($order['refund_status']) {
		$refund = order_refund_fetch($id);
		$refund_logs = order_fetch_refund_log($id);
		if(!empty($refund_logs)) {
			$refundmaxid = max(array_keys($refund_logs));
		}
	}
	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	$deliveryers = deliveryer_fetchall($sid);
	include itemplate('order/takeoutDetail');
}

if($ta == 'print') {
	$id = intval($_GPC['id']);
	$status = order_print($id);
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

if($ta == 'status') {
	$id = $_GPC['id'];
	$type = trim($_GPC['type']);
	$result = order_status_update($id, $type);
	if(is_error($result)) {
		imessage(error(-1, "处理订单失败:{$result['message']}"), '', 'ajax');
	}
	imessage(error(0, $result['message']), referer(), 'ajax');
}

if($ta == 'cancel') {
	$id = $_GPC['id'];
	$reasons = order_cancel_types('clerker');
	if($_W['ispost']) {
		$reason = $_GPC['reason'];
		if(empty($reason)) {
			imessage(error(-1, '请选择退款理由'), '', 'ajax');
		}
		$remark = trim($_GPC['remark']);
		$result = order_status_update($id, 'cancel', array('reason' => $reason, 'remark' => $remark, 'note' => "{$reasons[$reason]} {$remark}"));
		if(is_error($result)) {
			imessage(error(-1, $result['message']), '', 'ajax');
		}
		if($result['message']['is_refund']) {
			imessage(error(0, '取消订单成功, 退款会在1-15个工作日打到客户账户'), imurl('manage/order/takeout/list'), 'ajax');
		} else {
			imessage(error(0, '取消订单成功'), imurl('manage/order/takeout/list'), 'ajax');
		}
	}
	include itemplate('order/takeoutOp');
}

if($ta == 'deliveryer') {
	$id = $_GPC['id'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$result = order_assign_deliveryer($id, $deliveryer_id);
	if(is_error($result)) {
		imessage(error(-1, "ID为{$id}的订单分配配送员失败"), '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

if($ta == 'consume_post') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		imessage(error(-1, "订单不存在或已经删除"), '', 'ajax');
	}

	$result = order_status_update($id, 'end');
	if(is_error($result)) {
		imessage(error(-1, "核销订单失败:{$result['message']}"), '', 'ajax');
	}
	imessage(error(0, "核销订单成功"), referer(), 'ajax');
}

if($ta == 'reply') {
	$id = intval($_GPC['id']);
	$reply = trim($_GPC['reply']);
	$result = order_status_update($id, 'reply', array('reply' => $reply));
	if(is_error($result)) {
		imessage(error(-1, "回复催单失败:{$result['message']}"), referer(), 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}
