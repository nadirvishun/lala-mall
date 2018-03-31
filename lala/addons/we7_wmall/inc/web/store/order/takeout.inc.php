<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
$ta= trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
$_W['_process'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and order_type < 3 and status >= 1 and status <= 4', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
$_W['_remind'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and order_type < 3 and status >= 1 and status <= 4 and is_remind = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
$_W['_refund'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and order_type < 3 and refund_status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

if($ta == 'list'){
	$_W['page']['title'] = '外卖订单';

	if($_W['isajax']) {
		$type = trim($_GPC['type']);
		$status = intval($_GPC['value']);
		isetcookie("_{$type}", $status, 1000000);
	}
	//订单统计
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 5 and order_type < 3 and stat_day = :stat_day';
	$stat = pdo_fetch('select count(*) as total_num, sum(store_final_fee) as store_final_fee from ' . tablename('tiny_wmall_order') . $condition, array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':stat_day' => date('Ymd')));

	$filter_type = trim($_GPC['filter_type']) ? trim($_GPC['filter_type']) : 'process';
	$condition = ' WHERE uniacid = :uniacid and sid = :sid and order_type < 3';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);
	if($filter_type == 'process') {
		$condition .= ' AND (status != 5 and status != 6)';
	}
	$uid = intval($_GPC['uid']);
	if($uid > 0) {
		$condition .= ' AND uid = :uid';
		$params[':uid'] = $uid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$is_remind = intval($_GPC['is_remind']);
	if($is_remind > 0) {
		$condition .= ' AND is_remind = :is_remind';
		$params[':is_remind'] = $is_remind;
	}
	$re_status = intval($_GPC['refund_status']);
	if($re_status > 0) {
		$condition .= ' AND refund_status = :refund_status';
		$params[':refund_status'] = $re_status;
	}
	$is_pay = intval($_GPC['is_pay']) ? intval($_GPC['is_pay']) : -1;
	if($is_pay > -1) {
		$condition .= ' AND is_pay = :is_pay';
		$params[':is_pay'] = $is_pay;
	}
	$pay_type = trim($_GPC['pay_type']);
	if(!empty($pay_type)) {
		$condition .= ' AND is_pay = 1 AND pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	$order_plateform = trim($_GPC['order_plateform']);
	if(!empty($order_plateform)) {
		$condition .= ' AND order_plateform = :order_plateform';
		$params[':order_plateform'] = $order_plateform;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (username LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%' OR ordersn LIKE '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order') .  $condition, $params);
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' ORDER BY addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($orders)) {
		$order_ids = implode(',', array_keys($orders));
		$goods_temp = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_stat') . " where uniacid = :uniacid and oid in ({$order_ids})", array(':uniacid' => $_W['uniacid']));
		$goods_all = array();
		foreach($goods_temp as $row) {
			$goods_all[$row['oid']][] =  $row;
		}
		foreach($orders as &$da) {
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
			if($da['status'] == '6') {
				$da['cancel_reason'] = order_cancel_reason($da['id']);
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);

	$pay_types = order_pay_types();
	$order_types = order_types();
	$order_status = order_status();
	$refund_status = order_refund_status();
	$deliveryers = deliveryer_all();
	include itemplate('store/order/takeoutList');
}

if($ta == 'detail') {
	$_W['page']['title'] = '订单详情';
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage('订单不存在或已经删除', iurl('order/takeout/list'), 'error');
	}
	$order['goods'] = order_fetch_goods($order['id']);
	if($order['is_comment'] == 1) {
		$comment = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_comment') .' WHERE uniacid = :aid AND oid = :oid', array(':aid' => $_W['uniacid'], ':oid' => $id));
		if(!empty($comment)) {
			$comment['data'] = iunserializer($comment['data']);
			$comment['thumbs'] = iunserializer($comment['thumbs']);
		}
	}
	if($order['discount_fee'] > 0) {
		$discount = order_fetch_discount($id);
	}
	$pay_types = order_pay_types();
	$order_types = order_types();
	$order_status = order_status();
	$logs = order_fetch_status_log($id);
	include itemplate('order/takeoutDetail');
}

if($ta == 'status') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	$type = trim($_GPC['type']);
	if(empty($type)) {
		imessage(error(-1, '订单状态错误'), '', 'ajax');
	}
	foreach($ids as $id) {
		$id = intval($id);
		if($id <= 0) continue;
		$result = order_status_update($id, $type);
		if(is_error($result)) {
			imessage(error(-1, "处理编号为:{$id}的订单失败，具体原因：{$result['message']}"), '', 'ajax');
		}
	}
	imessage(error(0, '更新订状态成功'), '', 'ajax');
}

if($ta == 'cancel') {
	$id = intval($_GPC['id']);
	$reasons = order_cancel_types('manager');
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
			imessage(error(0, "取消订单成功, 退款会在1-15个工作日打到客户账户"), iurl('store/order/takeout/list'), 'ajax');
		} else {
			imessage(error(0, '取消订单成功'), iurl('store/order/takeout/list'), 'ajax');
		}
	}
	include itemplate('store/order/takeoutOp');
}

if($ta == 'remind'){
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$reply = trim($_GPC['reply']);
		$result = order_status_update($id, 'reply', array('reply' => $reply));
		if(is_error($result)) {
			imessage(error(-1, "回复催单失败:{$result['message']}"), referer(), 'ajax');
		}
		imessage(error(0, '回复催单成功'), referer(), 'ajax');
	}
	include itemplate('store/order/takeoutOp');
}

if($ta == 'print') {
	$id = intval($_GPC['id']);
	$status = order_print($id);
	if(is_error($status)) {
		imessage(error(-1, $status['message']), '', 'ajax');
	}
	imessage(error(0, '发送打印指定成功'), '', 'ajax');
}

if($ta == 'select_deliveryer') {
	$id = intval($_GPC['id']);
	if($_W['we7_wmall']['store']['delivery_mode'] == 2) {
		imessage(error(-1, '当前配送模式为平台配送模式, 不能指定配送员'), '', 'ajax');
	}
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_deliveryer') . $condition, $params);
	if(!empty($deliveryers)) {
		foreach($deliveryers as &$da) {
			$da['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('id' => $da['deliveryer_id']));
		}
	}
	include itemplate('store/order/takeoutOp');
}

if($ta == 'set_deliveryer') {
	if($_W['we7_wmall']['store']['delivery_mode'] == 2) {
		imessage(error(-1, '当前配送模式为平台配送模式, 不能指定配送员'), '', 'ajax');
	}
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$id = intval($_GPC['id']);
	$result = order_assign_deliveryer($id, $deliveryer_id);
	if(is_error($result)) {
		imessage(error(-1, "ID为{$id}的订单分配配送员失败:{$result['message']}"), '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

