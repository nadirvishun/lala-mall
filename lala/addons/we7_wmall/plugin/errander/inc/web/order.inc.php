<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('deliveryer');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '订单列表';

	if ($_W['isajax']) {
		$type = trim($_GPC['type']);
		$status = intval($_GPC['value']);
		isetcookie('_' . $type, $status, 1000000);
	}

	$condition = ' where uniacid = :uniacid and status = 3 and stat_day = :stat_day';
	$stat = pdo_fetch('select count(*) as total_num, sum(final_fee) as total_price from ' . tablename('tiny_wmall_errander_order') . $condition, array(':uniacid' => $_W['uniacid'], ':stat_day' => date('Ymd')));
	$filter_type = (trim($_GPC['filter_type']) ? trim($_GPC['filter_type']) : 'process');
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = intval($_GPC['status']);

	if (0 < $status) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	else {
		if ($filter_type == 'process') {
			$condition .= ' AND status >= 1 AND status <= 2';
		}
	}

	$re_status = intval($_GPC['refund_status']);

	if (0 < $re_status) {
		$condition .= ' AND refund_status = :refund_status';
		$params[':refund_status'] = $re_status;
	}

	if (0 < $status) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}

	$is_pay = (isset($_GPC['is_pay']) ? intval($_GPC['is_pay']) : -1);

	if (0 <= $is_pay) {
		$condition .= ' AND is_pay = :is_pay';
		$params[':is_pay'] = $is_pay;
	}

	$pay_type = trim($_GPC['pay_type']);

	if (!empty($pay_type)) {
		$condition .= ' AND is_pay = 1 AND pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' AND (accept_username LIKE \'%' . $keyword . '%\' OR accept_mobile LIKE \'%' . $keyword . '%\' OR order_sn LIKE \'%' . $keyword . '%\')';
	}

	if (!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	}
	else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}

	$condition .= ' AND addtime > :start AND addtime < :end';
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_errander_order') . $condition, $params);
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_errander_order') . $condition . ' ORDER BY id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params, 'id');

	if (!empty($orders)) {
		foreach ($orders as &$da) {
			$da['pay_type_class'] = '';

			if ($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';

				if ($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
		}
	}

	$pager = pagination($total, $pindex, $psize);
	$pay_types = order_pay_types();
	$errander_types = errander_types();
	$order_status = errander_order_status();
	$deliveryers = deliveryer_all();
	include itemplate('orderList');
}

if ($op == 'cancel') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		$id = intval($id);
		$status = errander_order_status_update($id, 'cancel');

		if (is_error($status)) {
			message($status, '', 'ajax');
		}
	}

	imessage(error(0, '取消订单成功'), '', 'ajax');
}

if ($op == 'end') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		$id = intval($id);
		$status = errander_order_status_update($id, 'end');

		if (is_error($status)) {
			message($status, '', 'ajax');
		}
	}

	imessage(error(0, '设置订单完成成功'), '', 'ajax');
}

if ($op == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		$id = intval($id);
		$order = pdo_get('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'id' => $id));

		if ($order['status'] != 4) {
			imessage(error(-1, '订单状态有误， 不能删除订单'), '', 'ajax');
		}

		pdo_delete('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_delete('tiny_wmall_errander_order_status_log', array('uniacid' => $_W['uniacid'], 'oid' => $id));
		pdo_delete('tiny_wmall_order_refund_log', array('uniacid' => $_W['uniacid'], 'oid' => $id, 'order_type' => 'errander'));
	}

	imessage(error(0, '删除订单成功'), '', 'ajax');
}

if ($op == 'refund_handle') {
	$id = intval($_GPC['id']);
	$result = errander_order_begin_payrefund($id);

	if (!is_error($result)) {
		$query = errander_order_query_payrefund($id);

		if (is_error($query)) {
			imessage(error(-1, '发起退款成功, 获取退款状态失败'), '', 'ajax');
		}
		else {
			imessage(error(0, '发起退款成功, 退款状态已更新'), '', 'ajax');
		}
	}
	else {
		message($result, '', 'ajax');
	}
}

if ($op == 'refund_query') {
	$id = intval($_GPC['id']);
	$query = errander_order_query_payrefund($id);

	if (is_error($query)) {
		imessage(error(-1, '获取退款状态失败'), '', 'ajax');
	}

	imessage(error(0, '更新退款状态成功'), '', 'ajax');
}

if ($op == 'refund_status') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_errander_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $id));
	errander_order_insert_refund_log($id, 'success');
	imessage(error(0, '设置为已退款成功'), referer(), 'ajax');
}

if ($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);

	if (empty($order)) {
		imessage('订单不存在或已经删除', referer(), 'error');
	}

	$pay_types = order_pay_types();
	$order_types = errander_types();
	$order_status = errander_order_status();
	$logs = errander_order_fetch_status_log($id);
	$refund_logs = errander_order_fetch_refund_status_log($id);
	include itemplate('orderDetail');
}

if ($op == 'analyse') {
	$id = intval($_GPC['id']);
	$deliveryers = errander_order_analyse($id);

	if (is_error($deliveryers)) {
		message($deliveryers, '', 'ajax');
	}

	message(error(0, $deliveryers), '', 'ajax');
}

if ($op == 'dispatch') {
	$order_id = intval($_GPC['order_id']);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$status = errander_order_assign_deliveryer($order_id, $deliveryer_id, true);

	if (is_error($status)) {
		message($status, '', 'ajax');
	}

	message(error(0, '分配订单成功'), '', 'ajax');
}

?>
