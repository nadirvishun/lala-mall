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
mload()->model('table');
$_W['page']['title'] = '店内订单';

if($ta == 'list') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid AND order_type > 2';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach($orders as &$da) {
			$table = table_fetch($da['table_id']);
			$da['table_sn'] = $table['title'];
			$da['goods'] = order_fetch_goods($da['id']);
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
		}
		$min = min(array_keys($orders));
	}
	$pay_types = order_pay_types();
	$order_status = order_status();
	$order_reserve_types = order_reserve_type();
	$table_categorys = table_category_fetchall($sid);
	include itemplate('order/tangshiList');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$status = intval($_GPC['status']);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid AND status = :status and id < :id AND order_type > 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':status' => $status,
		':id' => $id
	);
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 10', $params, 'id');
	if(!empty($orders)) {
		$pay_types = order_pay_types();
		$order_status = order_status();
		$order_reserve_types = order_reserve_type();
		$table_categorys = table_category_fetchall($sid);
		$da['table_sn'] = $table['title'];
		foreach ($orders as &$row) {
			$table = table_fetch($da['table_id']);
			$row['goods'] = order_fetch_goods($row['id']);
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $order_status[$row['status']]['color'];
			$row['status_cn'] = $order_status[$row['status']]['text'];
			$row['category_cn']= $table_categorys[$row['table_cid']]['title'];
		 	$row['reserve_types_cn'] = $order_reserve_types[$row['reserve_type']]['text'];
			$row['pay_types_cn'] = $pay_types[$row['pay_type']]['text'];
			$row['table_sn'] = $table['title'];
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

if($ta == 'detail') {
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
	$table_categorys = table_category_fetchall($sid);
	include itemplate('order/tangshiDetail');
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
	$result = order_status_update($id, 'cancel');
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	if($result['message']['is_refund']) {
		imessage(error(0, '取消订单成功, 退款会在1-15个工作日打到客户账户'), referer(), 'ajax');
	} else {
		imessage(error(0, '取消订单成功'), referer(), 'ajax');
	}
}

if($ta == 'pay_status') {
	$id = intval($_GPC['id']);
	$result = order_status_update($id, 'pay');
	if(is_error($result)) {
		imessage($result['message'], referer(), 'error');
	}
	imessage(error(0, '设置订单支付成功'), referer(), 'ajax');
}


