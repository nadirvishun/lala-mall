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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'create') {
	$id = intval($_GPC['id']);
	$category = errander_category_fetch($id);
	if(empty($category)) {
		imessage(error(-1, '跑腿类型不存在'), '', 'ajax');
	}

	$goods_name = trim($_GPC['goods_name']);
	if(empty($goods_name)) {
		imessage(error(-1, '商品名称不能为空'), '', 'ajax');
	}

	$start_address_id = intval($_GPC['start_address_id']);
	if($category['type'] == 'buy') {
		$start_address = member_fetch_serve_address($start_address_id);
	} elseif($category['type'] == 'multiaddress') {
		$start_address_num = count($_GPC['multiaddress']);
		if(!$start_address_num) {
			imessage(error(-1, '购买地址不能为空'), '', 'ajax');
		}
		$address = implode(',', $_GPC['multiaddress']);
		$start_address = array(
			'address' => $address
		);
	} else {
		$start_address = member_fetch_address($start_address_id);
		if(empty($start_address)) {
			imessage(error(-1, '取货地址不存在'), '', 'ajax');
		}
	}
	$end_address_id = intval($_GPC['end_address_id']);
	$end_address = member_fetch_address($end_address_id);
	if(empty($end_address)) {
		imessage(error(-1, '收货地址不存在'), '', 'ajax');
	}
	$extra = array(
		'start_address_num' => $start_address_num,
		'start_address' => $start_address,
		'end_address' => $end_address,
		'goods_weight' => floatval($_GPC['goods_weight']),
		'predict_index' => intval($_GPC['predict_index']),
		'delivery_tips' => floatval($_GPC['delivery_tips']),
	);
	$fee = errander_order_delivery_fee($id, $extra);
	if(is_error($fee)) {
		imessage(error(-1, $fee['message']), '', 'ajax');
	}

	$pay_type = trim($_GPC['pay_type']);
	$payment = get_available_payment('errander');
	if(!in_array($pay_type, $payment)) {
		imessage(error(-1, '支付方式有误'), '', 'ajax');
	}
	if(!is_array($_GPC['thumbs'])) {
		$_GPC['thumbs'] = array();
	}
	$delivery_day = trim($_GPC['delivery_day']) ? (date('Y') .'-'. trim($_GPC['delivery_day'])) : date('Y-m-d');
	$delivery_time = trim($_GPC['delivery_time']) ? trim($_GPC['delivery_time']) : '尽快送出';
	$order = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $category['agentid'],
		'acid' => $_W['acid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'code' => random(4, true),
		'order_sn' => date('YmdHis') . random(6, true),
		'order_type' => $category['type'],
		'order_cid' => $category['id'],
		'buy_username' => $start_address['realname'],
		'buy_mobile' => $start_address['mobile'],
		'buy_sex' => $start_address['sex'],
		'buy_address' => $start_address['address'] . $start_address['number'],
		'buy_location_x' => $start_address['location_x'],
		'buy_location_y' => $start_address['location_y'],
		'accept_mobile' => $end_address['mobile'],
		'accept_username' => $end_address['realname'],
		'accept_sex' => $end_address['sex'],
		'accept_address' => $end_address['address'] . $end_address['number'],
		'accept_location_x' => $end_address['location_x'],
		'accept_location_y' => $end_address['location_y'],
		'distance' => $fee['distance'],
		'delivery_time' => "{$delivery_day} {$delivery_time}",
		'goods_name' => $goods_name,
		'goods_price' => (in_array($category['type'], array('buy', 'multiaddress')) ? trim($_GPC['goods_price']) : trim($_GPC['goods_price_cn'])),
		'goods_weight' => trim($_GPC['goods_weight']),
		'thumbs' => iserializer(array_filter($_GPC['thumbs'], trim)),
		'note' => trim($_GPC['note']),
		'delivery_fee' => $fee['delivery_fee'],
		'delivery_tips' => floatval($fee['tip']),
		'total_fee' => $fee['total_fee'],
		'discount_fee' => $fee['discount_fee'],
		'final_fee' => $fee['final_fee'],
		'deliveryer_fee' => 0,
		'deliveryer_total_fee' => 0,
		'is_anonymous' => intval($_GPC['is_anonymous']),
		'is_pay' => 0,
		'pay_type' => $pay_type,
		'note' => trim($_GPC['note']),
		'status' => 1,
		'delivery_status' => 1,
		'addtime' => TIMESTAMP,
		'stat_year' => date('Y', TIMESTAMP),
		'stat_month' => date('Ym', TIMESTAMP),
		'stat_day' => date('Ymd', TIMESTAMP),
		'agent_discount_fee' => 0
	);
	if($_config_plugin['deliveryer_fee_type'] == 1) {
		$order['deliveryer_fee'] = $_config_plugin['deliveryer_fee'];
	} elseif($_config_plugin['deliveryer_fee_type'] == 2) {
		$order['deliveryer_fee'] = round(($order['delivery_fee'] - $order['discount_fee']) * $_config_plugin['deliveryer_fee'] / 100, 2);
	} elseif($_config_plugin['deliveryer_fee_type'] == 3) {
		$config_deliveryer_fee = $_config_plugin['deliveryer_fee'];
		$plateform_deliveryer_fee = floatval($config_deliveryer_fee['start_fee']);
		$over_km = $order['distance'] - $config_deliveryer_fee['start_km'];
		if($over_km > 0) {
			$over_fee = round($over_km * $config_deliveryer_fee['pre_km'], 2);
		}
		$plateform_deliveryer_fee += $over_fee;
		$plateform_deliveryer_fee = min($plateform_deliveryer_fee, $config_deliveryer_fee['max_fee']);
		$order['deliveryer_fee'] = $plateform_deliveryer_fee;
	}
	$order['deliveryer_total_fee'] = $order['deliveryer_fee'] + $order['delivery_tips'];
	$name = $order['accept_username'];
	if($order['is_anonymous'] == 1) {
		if(!empty($_config_plugin['anonymous'])) {
			$index = array_rand($_config_plugin['anonymous']);
			$name = $_config_plugin['anonymous'][$index];
		} else {
			$name = cutstr($order['accept_username'], 1) . '**';
		}
	}
	$order['anonymous_username'] = $name;

	$order['plateform_serve_fee'] = $order['delivery_fee'] + $order['delivery_tips'];
	$order['plateform_serve'] = iserializer(array(
		'fee' => $order['plateform_serve_see'],
		'note' => "订单配送费 ￥{$order['delivery_fee']} + 订单小费 ￥{$order['delivery_tips']}",
	));
	if($_W['is_agent']) {
		$account_agent = get_agent($order['agentid'], 'fee');
		$agent_fee_config = $account_agent['fee']['fee_errander'];
		if($agent_fee_config['type'] == 2) {
			$agent_serve_fee = floatval($agent_fee_config['fee']);
			$agent_serve = array(
				'fee_type' => 2,
				'fee_rate' => 0,
				'fee' => $agent_serve_fee,
				'note' => "每单固定{$agent_serve_fee}元"
			);
		} else {
			$basic = 0;
			$note = array(
				'yes' => array(),
				'no' => array(),
			);
			$fee_items = agent_serve_fee_items();
			if(!empty($agent_fee_config['items_yes'])) {
				foreach($agent_fee_config['items_yes'] as $item) {
					$basic += $order[$item];
					$note['yes'][] = "{$fee_items['yes'][$item]} ￥{$order[$item]}";
				}
			}
			if(!empty($agent_fee_config['items_no'])) {
				foreach($agent_fee_config['items_no'] as $item) {
					$basic -= $order[$item];
					$note['no'][] = "{$fee_items['no'][$item]} ￥{$order[$item]}";
				}
			}
			if($basic < 0) {
				$basic = 0;
			}
			$agent_serve_rate = floatval($agent_fee_config['fee_rate']);
			$agent_serve_fee = round($basic * ($agent_serve_rate / 100), 2);
			$text = '(' . implode(' + ', $note['yes']);
			if(!empty($note['no'])) {
				$text .= ' - ' . implode(' - ', $note['no']);
			}
			$text .= ") x {$agent_serve_rate}%";
			$agent_serve = array(
				'fee_type' => 1,
				'fee_rate' => $agent_serve_rate,
				'fee' => $agent_serve_fee,
				'note' => $text,
			);
		}
		$agent_serve['final'] = "(代理商抽取佣金 ￥{$order['plateform_serve_see']} - 平台服务佣金 ￥{$agent_serve_fee} - 代理商补贴 ￥{$order['agent_discount_fee']} - 代理商支付给配送员配送费 ￥{$order['deliveryer_total_fee']})";
		$order['agent_serve'] = iserializer($agent_serve);
		$order['agent_serve_fee'] = $agent_serve_fee;
		$order['agent_final_fee'] = $order['plateform_serve_fee'] - $agent_serve_fee - $order['agent_discount_fee'] - $order['deliveryer_total_fee'];
	}
	pdo_insert('tiny_wmall_errander_order', $order);
	$id = pdo_insertid();
	errander_order_insert_status_log($id, 'place_order');
	isetcookie('errander_order', '', -1000);
	imessage(error(0, $id), '', 'ajax');
}

if($op == 'delivery_fee') {
	if($_W['ispost']) {
		$id = intval($_GPC['id']);
		$extra = array(
			'start_address_num' => intval($_GPC['start_address_num']),
			'start_address' => $_GPC['start_address'],
			'end_address' => $_GPC['end_address'],
			'goods_weight' => $_GPC['goods_weight'],
			'predict_index' => intval($_GPC['predict_index']),
			'delivery_tips' => floatval($_GPC['delivery_tips']),
		);
		$fee = errander_order_delivery_fee($id, $extra);
		if(is_error($fee)) {
			imessage(error(-1, $fee['message']), '', 'ajax');
		}
		imessage(error(0, $fee), '', 'ajax');
	}
}

if($op == 'list') {
	$_W['page']['title'] = '跑腿订单';
	$total_user = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		$min = min(array_keys($orders));
		foreach($orders as &$row) {
			if($row['deliveryer_id'] > 0) {
				$row['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $row['deliveryer_id']));
			}
		}
	} else {
		$others = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid']), 'id');
	}
	include itemplate('orderList');
}

if($op == 'more') {
	$id = intval($_GPC['min']);
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		foreach($orders as &$order) {
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['thumb'] = tomedia($order['thumb']);
			$order['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$status = errander_order_status_update($id, 'cancel');
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, '订单取消成功'), referer(), 'ajax');
}

if($op == 'detail') {
	$_W['page']['title'] = '订单详情';
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_errander_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$logs = errander_order_fetch_status_log($id);
	if(!empty($logs)) {
		$maxid = max(array_keys($logs));
	}
	if($order['refund_status'] > 0) {
		$refund_logs = errander_order_fetch_refund_status_log($id);
		if(!empty($refund_logs)) {
			$refundmaxid = max(array_keys($refund_logs));
		}
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
	$order_types = errander_types();
	$pay_types = order_pay_types();
	$order_status = errander_order_status();
	include itemplate('orderDetail');
}

