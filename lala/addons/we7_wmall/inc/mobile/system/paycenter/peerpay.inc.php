<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta'])? trim($_GPC['ta']): 'paytype';
if(!is_weixin()) {
	imessage('请在微信中访问该链接', '', 'info');
}
icheckauth();
$config_peerpay = $_W['we7_wmall']['config']['payment']['peerpay'];

if($ta == 'paytype' || $ta == 'message') {
	$_W['page']['title'] = '选择代付方式';
	$plid = intval($_GPC['id']);
	$paylog = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => $plid));
	if(empty($paylog)) {
		imessage('订单交易记录不存在', referer(), 'error');
	}
	$member = get_member($paylog['uid']);
}

if($ta == 'message') {
	$_W['page']['title'] = '编辑代付留言';
	$type = intval($_GPC['type']);
	$peerpay = pdo_get('tiny_wmall_order_peerpay', array('uniacid' => $_W['uniacid'], 'plid' => $paylog['id']));
	if(empty($peerpay)) {
		$peerpay = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $paylog['uid'],
			'plid' => $paylog['id'],
			'orderid' => $paylog['order_id'],
			'peerpay_type' => $type,
			'peerpay_price' => $paylog['fee'],
			'peerpay_realprice' => $paylog['fee'],
			'createtime' => TIMESTAMP,
			'data' => $paylog['data']
		);
		if($peerpay['peerpay_type'] == 1) {
			$peerpay['peerpay_selfpay'] = $config_peerpay['peerpay_max_limit'];
		}
		pdo_insert('tiny_wmall_order_peerpay', $peerpay);
		$peerpay['id'] = pdo_insertid();
	}
	pdo_update('tiny_wmall_order', array('pay_type' => 'peerpay'), array('uniacid' => $_W['uniacid'], 'id' => $peerpay['orderid']));
	$peerpay['data'] = iunserializer($peerpay['data']);
	if($_W['ispost']) {
		$message = trim($_GPC['message']);
		if(!empty($message)) {
			pdo_update('tiny_wmall_order_peerpay', array('peerpay_message' => $message), array('uniacid' => $_W['uniacid'], 'id' => $peerpay['id']));
		}
	}
}

if($ta == 'paylist') {
	$id = intval($_GPC['id']);
	$peerpay = pdo_get('tiny_wmall_order_peerpay', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($peerpay)) {
		$payinfo_id = intval($_GPC['payinfo_id']);
		$payinfo = pdo_get('tiny_wmall_order_peerpay_payinfo', array('uniacid' => $_W['uniacid'], 'id' => $payinfo_id));
		$peerpay = pdo_get('tiny_wmall_order_peerpay', array('uniacid' => $_W['uniacid'], 'id' => $payinfo['pid']));
	}
	if(empty($peerpay)) {
		imessage('代付记录不存在', referer(), 'error');
	}
	$peerpay['data'] = iunserializer($peerpay['data']);
	$paylog = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'id' => $peerpay['plid']));
	if(empty($paylog)) {
		imessage('订单交易记录不存在', referer(), 'error');
	}
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $peerpay['orderid']), array('id', 'status', 'addtime'));
	if(empty($order)) {
		imessage('订单不存在', referer(), 'error');
	}
	if($order['status'] == 6) {
		imessage('订单已取消,不能进行代付了', imurl('wmall/home/index'), 'error');
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	if(is_array($config_takeout) && $config_takeout['pay_time_limit'] > 0) {
		$order['pay_endtime'] = $order['addtime'] + $config_takeout['pay_time_limit'] * 60;
		$order['pay_endtime_cn'] = date('Y/m/d H:i:s', $order['pay_endtime']);
		if($order['pay_endtime'] < TIMESTAMP) {
			$order['pay_endtime'] = 0;
		}
	}

	$member = get_member($paylog['uid']);
	$_W['page']['title'] = "{$member['nickname']}的代付";
	$peerpay['peerpay_realprice'] = floatval($peerpay['peerpay_realprice']);
	$differ = $peerpay['peerpay_price'] - $peerpay['peerpay_realprice'];
	$percent = round(($peerpay['peerpay_price'] - $peerpay['peerpay_realprice']) / $peerpay['peerpay_price'], 2) * 100;
	$payinfos = pdo_getall('tiny_wmall_order_peerpay_payinfo', array('pid' => $peerpay['id'], 'is_pay' => 1));
}

if($ta == 'payment') {
	$id = intval($_GPC['id']);
	$peerpay = pdo_get('tiny_wmall_order_peerpay', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($peerpay)) {
		imessage('代付记录不存在', referer(), 'error');
	}
	if($peerpay['status'] == 1) {
		imessage('该订单已代付成功', referer(), 'info');
	}
	$peerpay['peerpay_selfpay'] = floatval($peerpay['peerpay_selfpay']);
	if(empty($peerpay['peerpay_selfpay'])) {
		$peerpay['peerpay_selfpay'] = $peerpay['peerpay_realprice'];
	}
	$paylog = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'id' => $peerpay['plid']));
	if(empty($paylog)) {
		imessage('订单交易记录不存在', referer(), 'error');
	}
	$member = get_member($paylog['uid']);
	$_W['page']['title'] = "{$member['nickname']}的代付";

	shuffle($config_peerpay['notes']);
	$note = array_pop($config_peerpay['notes']);

	if($_W['ispost']) {
		$fee = floatval($_GPC['val']);
		if(empty($fee)) {
			imessage(error(-1, '代付金额必须大于0'), '', 'ajax');
		}
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'pid' => $peerpay['id'],
			'uid' => $_W['member']['uid'],
			'headimg' => $_W['member']['avatar'],
			'openid' => $_W['member']['openid'],
			'uname' => $_W['member']['nickname'],
			'usay' => trim($_GPC['note']),
			'final_fee' => $fee,
			'createtime' => TIMESTAMP,
			'order_sn' => date('YmdHis') . random(6, true),
		);
		if($insert['final_fee'] > $peerpay['peerpay_selfpay']) {
			$insert['final_fee'] = $peerpay['peerpay_selfpay'];
		}
		$is_exist = pdo_get('tiny_wmall_order_peerpay_payinfo', array('pid' => $peerpay['id'], 'openid' => $_GPC['openid']), array('id'));
		if(empty($is_exist)) {
			pdo_insert('tiny_wmall_order_peerpay_payinfo', $insert);
			$id = pdo_insertid();
		} else {
			pdo_update('tiny_wmall_order_peerpay_payinfo', $insert, array('id' => $is_exist['id']));
			$id = $is_exist['id'];
		}
		imessage(error(0, imurl('system/paycenter/pay', array('order_type' => 'peerpay', 'id' => $id))), '', 'ajax');
	}
}

$title = '亲爱哒，帮我付一下呗~';
if(!empty($config_peerpay['help_words'])) {
	shuffle($config_peerpay['help_words']);
	$title = array_pop($config_peerpay['help_words']);
}
$_share = array(
	'title' => $title,
	'desc' => $peerpay['peerpay_message'],
	'imgUrl' => tomedia($peerpay['data']['logo']),
	'link' => imurl('system/paycenter/peerpay/paylist', array('id' => $peerpay['id']), true)
);

include itemplate('paycenter/peerpay');

