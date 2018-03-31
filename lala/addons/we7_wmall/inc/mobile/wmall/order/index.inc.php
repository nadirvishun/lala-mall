<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = "订单列表";
	$orders = pdo_fetchall('select a.id as aid, a.*, b.title, b.logo, b.delivery_mode from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;
	if(!empty($orders)) {
		$share = get_plugin_config('ordergrant.share');

		$order_status = order_status();
		foreach($orders as &$da) {
			$da['goods'] = pdo_get('tiny_wmall_order_stat', array('oid' => $da['id']));
			$da['comment_cn'] = '去评价';
			if($share['status'] == 1 && $da['status'] == 5 && $da['is_comment'] == 0 && $da['endtime'] >= (time() - $share['share_grant_days_limit'] * 86400)) {
				$da['comment_cn'] = '<span class="color-danger">评价赢好礼</span>';
			}
		}
		$min = min(array_keys($orders));
	}
	include itemplate('order/list');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$orders = pdo_fetchall('select a.id as aid, a.*, b.title, b.logo, b.delivery_mode from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');
	$min = 0;
	if(!empty($orders)) {
		$share = get_plugin_config('ordergrant.share');

		$order_status = order_status();
		foreach($orders as &$order) {
			$order['goods'] = pdo_get('tiny_wmall_order_stat', array('oid' => $order['aid']), array('goods_title'));
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['logo_cn'] = tomedia($order['logo']);

			$order['comment_cn'] = '去评价';
			if($share['status'] == 1 && $order['status'] == 5 && $order['is_comment'] == 0 && $order['endtime'] >= (time() - $share['share_grant_days_limit'] * 86400)) {
				$order['comment_cn'] = '<span class="color-danger">评价赢好礼</span>';
			}
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}

if($ta == 'detail') {
	$_W['page']['title'] = "订单详情";
	$id = intval($_GPC['id']);
	$order = order_fetch($id, true);
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	$store = store_fetch($order['sid'], array('id', 'title', 'telephone', 'pack_price', 'logo', 'delivery_price', 'address', 'location_x', 'location_y'));
	$store['logo'] = tomedia($store['logo']);
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
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));

	$share = get_plugin_config('ordergrant.share');
	$comment = pdo_get('tiny_wmall_order_comment', array('oid' => $order['id']));
	$share_button = 0;
	$order['comment_cn'] = '去评价';
	if($share['status'] == 1 && !$comment['is_share'] && $order['status'] == 5 && $order['endtime'] >= (time() - $share['share_grant_days_limit'] * 86400)) {
		if($order['is_comment'] == 0) {
			$order['comment_cn'] = '<span class="color-danger">评价赢好礼</span>';
			$share_button = 1;
		} else {
			$share_button = 2;
		}
		$_share = array(
			'title' => $share['share']['title'],
			'desc' => $share['share']['desc'],
			'imgUrl' => tomedia($share['share']['imgUrl']),
			'link' => imurl('ordergrant/share/detail', array('id' => $order['id']), 'true')
		);
	}

	if(check_plugin_perm('superRedpacket')) {
		$superRedpacket = pdo_get('tiny_wmall_superredpacket_grant', array('uniacid' => $_W['uniacid'], 'order_id' => $id, 'uid' => $_W['member']['uid']));
		if(!empty($superRedpacket) && $superRedpacket['packet_dosage'] > 0) {
			$superRedpacket_share = pdo_get('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $superRedpacket['activity_id'], 'type' => 'share', 'status' => 1));
			$superRedpacket_share_status = 0;
			if(!empty($superRedpacket_share)) {
				$share_button = 0;
				$superRedpacket_share_status = 1;
				$superRedpacket_share['data'] = json_decode(base64_decode($superRedpacket_share['data']), true);
				$_share = array(
					'title' => $superRedpacket_share['data']['share']['title'],
					'desc' => $superRedpacket_share['data']['share']['desc'],
					'imgUrl' => tomedia($superRedpacket_share['data']['share']['imgUrl']),
					'link' => imurl('superRedpacket/share/index', array('order_id' => $id), true)
				);
			}
		}
	}

	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	include itemplate('order/detail');
}

if($ta == 'remind') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid and status = 8 order by id desc',  array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('remind_time_limit'));
	$remind_time_limit = intval($store['remind_time_limit']) ? intval($store['remind_time_limit']) : 10;
	if($log['addtime'] >= (time() - $remind_time_limit * 60)) {
		imessage(error(-1, "距离上次催单不超过{$remind_time_limit}分钟,不能催单"), '', 'ajax');
	}
	order_insert_status_log($id, 'remind');
	order_clerk_notice($id, 'remind');
	pdo_update('tiny_wmall_order', array('is_remind' => '1'), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '催单成功'), '', 'ajax');
}

if($ta == 'cancel') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] != 1) {
		imessage(error(-1, '商户已接单,如需取消订单请联系商户处理'), '', 'ajax');
	}
	if(!$order['is_pay']) {
		$result = order_status_update($id, 'cancel');
		if(is_error($result)) {
			imessage(error(-1, $result['message']), '', 'ajax');
		}
	} else {
		imessage(error(-1, '该订单已支付,如需取消订单请联系商户处理'), '', 'ajax');
	}
	imessage(error(0, '取消订单成功'), referer(), 'ajax');
}

if($ta == 'end') {
	$id = intval($_GPC['id']);
	$result = order_status_update($id, 'end');
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	imessage(error(0, '确认订单完成成功'), imurl('wmall/order/comment', array('id' => $id)), 'ajax');
}


