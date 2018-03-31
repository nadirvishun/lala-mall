<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('plugin');
pload()->model('advertise');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$amount = $store['account']['amount'];
$slideOrderDetail = get_advertise_info('slideOrderDetail');
if($ta == 'index') {
	$_W['page']['title'] = '订单详情幻灯片';
	include itemplate('advertise/orderDetail');
}
if($ta == 'submit') {
	if($_W['isajax']) {
		if(!$slideOrderDetail['leave']) {
			imessage(error(-1, '该广告位已售空，请选择其他广告位'), '', 'ajax');
		}
		$day = intval($_GPC['day']);
		if(!$day) {
			imessage(error(-1, '请选择购买天数'), '', 'ajax');
		}
		$pay_type = trim($_GPC['pay_type']);
		if(!$pay_type) {
			imessage(error(-1, '请选择支付方式'), '', 'ajax');
		}
		$finalfee = $slideOrderDetail['prices'][$day]['fee'];
		if($pay_type == 'credit' && $amount < $finalfee) {
			imessage(error(-1,'余额不足，请选择其他支付方式'), '', 'ajax');
		}
		$orderDetailData = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'type' => 'slideOrderDetail',
			'displayorder' => 0,
			'title' => "订单详情页幻灯片展示{$day}天",
			'status' => 0,
			'addtime' => TIMESTAMP,
			'starttime' => TIMESTAMP,
			'endtime' => TIMESTAMP,
			'final_fee' => $finalfee,
			'pay_type' => $pay_type,
			'days' => $day,
			'is_pay' => 0,
			'order_sn' => date('YmdHis', time()).random(6, true),
			'data' => '',
		);
		pdo_insert('tiny_wmall_advertise_trade', $orderDetailData);
		$id = pdo_insertid();
		imessage(error(0, array('id' => $id, 'sid' => $sid)), '', 'ajax');
	}
}



