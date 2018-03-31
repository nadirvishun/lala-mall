<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('coupon');
mload()->model('paybill');
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

$sid = intval($_GPC['sid']);
$store = store_fetch($sid, array('agentid', 'cid', 'title', 'logo', 'is_paybill'));
if(empty($store)) {
	imessage(error(-1, '门店不存在'), '', 'ajax');
}
if($store['is_paybill'] == 0) {
	imessage(error(-1, '该门店暂未开启买单功能'), '', 'ajax');
}

if($ta == 'index') {
	$sid = $_GPC['sid'];
	$total_fee = floatval($_GPC['total_fee']);
	if(empty($total_fee)) {
		imessage(error(-1, "消费总额不能为空"), '', 'ajax');
	}
	$no_discount_part = $_GPC['no_discount_part'] ? floatval($_GPC['no_discount_part']) : 0;
	$couponId = intval($_GPC['couponId']);
	$condition = $total_fee - $no_discount_part;
	$coupon = pdo_fetch('select * from'. tablename('tiny_wmall_activity_coupon_record') . ' where uniacid = :uniacid and id = :id and sid = :sid and uid = :uid and `condition` <= :condition and endtime > :endtime and starttime <= :starttime', array(':uniacid' => $_W['uniacid'], ':id' => $couponId, ':sid' => $sid, ':uid' => $_W['member']['uid'], ':condition' => $condition, ':endtime' => TIMESTAMP, ':starttime' => TIMESTAMP));
	if($coupon['status'] > 1) {
		imessage(error(-1, "优惠券无效,请重新选择"), '', 'ajax');
	}
	$couponPrice = $coupon['discount'];
	$final_fee = $total_fee - $couponPrice;

	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'agentid' => $store['agentid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'serial_sn' => paybill_order_serial_sn($sid),
		'order_sn' => date('YmdHis') . random(6, true),
		'addtime' => TIMESTAMP,
		'total_fee' => $total_fee,
		'pay_type' => trim($_GPC['pay_type']),
		'no_discount_part' => $no_discount_part,
		'discount_fee' => $couponPrice,
		'final_fee' => $final_fee,
		'stat_year' => date('Y'),
		'stat_month' => date('Ym'),
		'stat_day' => date('Ymd'),
	);
	pdo_insert('tiny_wmall_paybill_order', $order);
	$order_id = pdo_insertid();
	$extra['order_id'] = $order_id;
	coupon_consume($couponId, $extra);
	paybill_order_update_bill($order_id);
	imessage(error(0, $order_id), '', 'ajax');
}

if($ta == 'coupon') {
	$sid = $_GPC['sid'];
	$price = $_GPC['sum'];
	$coupons = coupon_consume_available($sid, $price, $_W['member']['uid']);
	if(!empty($coupons)) {
		$coupons = array_values($coupons);
		$num = count($coupons);
	} else {
		$coupons = array();
		$num = 0;
	}
	$respon = array('errno' => 0, 'message' => $coupons , 'num' => $num);
	imessage($respon, '', 'ajax');
}
