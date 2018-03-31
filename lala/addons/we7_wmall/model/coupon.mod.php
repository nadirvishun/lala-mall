<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function coupon_cron() {
	global $_W;
	pdo_query("update " . tablename('tiny_wmall_activity_coupon_record') . ' set status = 3 where uniacid = :uniacid and status = 1 and endtime < :time', array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	return true;
}

function coupon_fetch($id, $activity_id) {
	global $_W;
	if(empty($activity_id)){
		$data = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'id' => $id));
	} else {
		$data = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'activity_id' => $activity_id));
	}
	$data['coupons'] = array_values(array_filter(iunserializer($data['coupons'])));
	$data['total'] = count($data['coupons']);
	$total = $data['total'] * $data['amount'];
	$data['dosage_total'] = $data['dosage'] * $data['total'];
	$data['dosage_percent'] = round($data['dosage_total'] / $total, 2) * 100;
	$data['is_use_total'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_coupon_record') . ' where uniacid = :uniacid and couponid = :couponid and status = 2', array(':uniacid' => $_W['uniacid'], ':couponid' => $data['id']));
	$data['is_use_percent'] = 0;
	if($data['is_use_total'] > 0) {
		$data['is_use_percent'] = round($data['is_use_total'] / $total, 2) * 100;
	}
	return $data;
}

function coupon_collect_member_available($sid) {
	global $_W;
	$coupon = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'couponCollect', 'status' => 1));
	if(empty($coupon)) {
		return false;
	}
	if($coupon['type_limit'] == 2 && $_W['member']['is_store_newmember'] == 0) {
		return false;
	}
	if($coupon['status'] == 1 && ($coupon['starttime'] > time() || $coupon['endtime'] < time() || $coupon['dosage'] >= $coupon['amount'])) {
		pdo_update('tiny_wmall_activity_coupon', array('status' => 0), array('id' => $coupon['id']));
		$coupon['status'] = 0;
	}
	if(empty($coupon['status'])) {
		return false;
	}
	$is_grant = pdo_get('tiny_wmall_activity_coupon_record', array('couponid' => $coupon['id'], 'uid' => $_W['member']['uid']));
	if(!empty($is_grant)) {
		return false;
	}
	$coupon['coupons'] = array_values(array_filter(iunserializer($coupon['coupons'])));
	foreach($coupon['coupons'] as $item) {
		$coupon['price'] += $item['discount'];
	}
	$coupon['num'] = count($coupon['coupons']);
	return $coupon;
}

//进店领券
function coupon_collect($sid) {
	global $_W;
	$token = coupon_collect_member_available($sid);
	if(empty($token)) {
		return error(-1, '没有可领取的优惠券');
	}
	foreach($token['coupons'] as $coupon) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'couponid' => $token['id'],
			'uid' => $_W['member']['uid'],
			'code' => random(8, true),
			'type' => 'couponCollect',
			'condition' => $coupon['condition'],
			'discount' => $coupon['discount'],
			'granttime' => TIMESTAMP,
			'endtime' => TIMESTAMP + intval($coupon['use_days_limit']) * 86400,
			'status' => 1,
			'remark' => '',
		);
		pdo_insert('tiny_wmall_activity_coupon_record', $data);
	}
	pdo_update('tiny_wmall_activity_coupon', array('dosage' => $token['dosage'] + 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $token['id']));
	return true;
}

//下单返券，判断店铺是否有可发放的代金券
function coupon_grant_available($sid, $price) {
	global $_W;
	$coupon = pdo_fetch('select * from ' . tablename('tiny_wmall_activity_coupon') . ' where uniacid = :uniacid and sid = :sid and status = 1 and type = :type and starttime < :time and endtime > :time and amount > dosage and `condition` <= :condition', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':type' => 'couponGrant', ':time' => TIMESTAMP, ':condition' => $price));
	if(empty($coupon)) {
		return false;
	}
	$coupon['coupons'] = iunserializer($coupon['coupons']);
	$coupon['discount'] = $coupon['coupons']['discount'];
	return $coupon;
}

function coupon_consume_available($sid, $price, $uid = 0) {
	global $_W;
	if($uid == 0) {
		$uid = $_W['member']['uid'];
	}
	$condition = ' where a.sid = :sid and a.uid = :uid and a.status = 1 and `condition` <= :price and a.endtime > :endtime and a.starttime <= :starttime';
	$params = array(
		':sid' => $sid,
		':price' => floatval($price),
		':uid' => $uid,
		':endtime' => TIMESTAMP,
		':starttime' => TIMESTAMP
	);
	$coupons = pdo_fetchall('SELECT a.*,b.logo,b.title FROM ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on b.id = a.sid' . $condition, $params);
	foreach($coupons as &$val) {
		$val['endtime'] = date('Y-m-d', $val['endtime']);
	}
	return $coupons;
}

//代金券核销
function coupon_consume($record_id, $extra = array()) {
	global $_W;
	pdo_update('tiny_wmall_activity_coupon_record', array('status' => 2, 'usetime' => TIMESTAMP, 'order_id' => $extra['order_id']), array('id' => $record_id, 'uniacid' => $_W['uniacid']));
}


function coupon_grant($params) {
	global $_W;
	if(!is_array($params)) {
		return error(-1, '优惠券信息有误');
	}
	if(empty($params['coupon_id'])) {
		return error(-1, '优惠券信息不能为空');
	}
	if(empty($params['sid'])) {
		return error(-1, '优惠券所属门店不能为空');
	}
	if(empty($params['channel'])) {
		return error(-1, '优惠券发放渠道不能为空');
	}
	if(empty($params['type'])) {
		return error(-1, '优惠券类型有误');
	}
	$params['discount'] = floatval($params['discount']);
	if(empty($params['discount'])) {
		return error(-1, '优惠券金额有误');
	}
	$params['use_days_limit'] = intval($params['use_days_limit']);
	if(empty($params['use_days_limit'])) {
		return error(-1, '优惠券有效期限有误');
	}
	$params['uid'] = intval($params['uid']);
	if(empty($params['uid'])) {
		return error(-1, '用户uid有误');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $params['sid'],
		'couponid' => $params['coupon_id'],
		'uid' => $params['uid'],
		'code' => random(8, true),
		'type' => $params['type'],
		'channel' => $params['channel'],
		'condition' => $params['condition'],
		'discount' => $params['discount'],
		'granttime' => TIMESTAMP,
		'endtime' => TIMESTAMP + intval($params['use_days_limit']) * 86400,
		'status' => 1,
		'remark' => '',
	);
	pdo_insert('tiny_wmall_activity_coupon_record', $data);
	pdo_query('update ' . tablename('tiny_wmall_activity_coupon') . ' set dosage = dosage + 1 where id = :id' , array('id' => $params['coupon_id']));
	return true;
}

function coupon_channels() {
	$channel = array(
		'' => array(
			'text' => '未知',
			'css' => 'label-danger'
		),
		'couponCollect' => array(
			'text' => '进店领券',
			'css' => 'label-success'
		),
		'couponGrant' => array(
			'text' => '满返优惠',
			'css' => 'label-info'
		)
	);
	return $channel;
}

function coupon_status() {
	$status = array(
		'1' => array(
			'text' => '未使用',
			'css' => 'label-info'
		),
		'2' => array(
			'text' => '已使用',
			'css' => 'label-success'
		),
		'3' => array(
			'text' => '已过期',
			'css' => 'label-default'
		),
	);
	return $status;
}