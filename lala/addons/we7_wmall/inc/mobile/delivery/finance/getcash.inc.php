<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '申请提现';
$config_delivery = $_W['we7_wmall']['config']['delivery']['cash'];

if ($_W['isajax']) {
	if ($_deliveryer_type == 2) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}

	if (empty($_deliveryer['openid']) || empty($_deliveryer['title'])) {
		imessage(error(-1, '配送员账户不完善, 无法提现'), '', 'ajax');
	}

	$get_fee = floatval($_GPC['fee']);

	if (!$get_fee) {
		imessage(error(-1, '提现金额有误'), '', 'ajax');
	}

	if ($get_fee < $config_delivery['get_cash_fee_limit']) {
		imessage(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
	}

	if ($_deliveryer['credit2'] < $get_fee) {
		imessage(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
	}

	$take_fee = round($get_fee * ($config_delivery['get_cash_fee_rate'] / 100), 2);
	$take_fee = max($take_fee, $config_delivery['get_cash_fee_min']);

	if (0 < $config_delivery['get_cash_fee_max']) {
		$take_fee = min($take_fee, $config_delivery['get_cash_fee_max']);
	}

	$final_fee = $get_fee - $take_fee;

	if ($final_fee < 0) {
		$final_fee = 0;
	}

	$openid = mktTransfers_get_openid($_deliveryer['id'], $_deliveryer['openid'], $get_fee, 'deliveryer');

	if (is_error($openid)) {
		imessage($openid, '', 'ajax');
	}

	$data = array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $_deliveryer['id'], 'trade_no' => date('YmdHis') . random(10, true), 'get_fee' => $get_fee, 'take_fee' => $take_fee, 'final_fee' => $final_fee, 'account' => iserializer(array('nickname' => $_deliveryer['nickname'], 'openid' => $openid, 'avatar' => $_deliveryer['avatar'], 'realname' => $_deliveryer['title'])), 'status' => 2, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_deliveryer_getcash_log', $data);
	$getcash_id = pdo_insertid();
	$remark = date('Y-m-d H:i:s') . '申请提现,提现金额' . $get_fee . '元, 手续费' . $take_fee . '元, 实际到账' . $final_fee . '元';
	deliveryer_update_credit2($_deliveryer['id'], 0 - $get_fee, 2, $getcash_id, $remark);
	sys_notice_deliveryer_getcash($_deliveryer['id'], $getcash_id, 'apply');
	imessage(error(0, '申请提现成功'), '', 'ajax');
}

include itemplate('finance/getcash');

?>
