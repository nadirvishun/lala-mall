<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '申请提现';
$sid = intval($_GPC['__mg_sid']);
$account = $store['account'];

if ($_W['isajax']) {
	if (empty($account['wechat']['openid']) || empty($account['wechat']['realname'])) {
		imessage(error(-1, '提现账户不完善, 无法提现'), '', 'ajax');
	}

	$get_fee = floatval($_GPC['fee']);

	if (!$get_fee) {
		imessage(error(-1, '提现金额有误'), '', 'ajax');
	}

	if ($get_fee < $account['fee_limit']) {
		imessage(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
	}

	if ($account['amount'] < $get_fee) {
		imessage(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
	}

	$take_fee = round($get_fee * ($account['fee_rate'] / 100), 2);
	$take_fee = max($take_fee, $account['fee_min']);

	if (0 < $account['fee_max']) {
		$take_fee = min($take_fee, $account['fee_max']);
	}

	$final_fee = $get_fee - $take_fee;

	if ($final_fee <= 0) {
		imessage(error(-1, '实际到账金额小于0元'), '', 'ajax');
	}

	$openid = mktTransfers_get_openid($sid, $account['wechat']['openid'], $get_fee);

	if (is_error($openid)) {
		imessage($openid, '', 'ajax');
	}

	$account['wechat']['openid'] = $openid;
	$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'trade_no' => date('YmdHis') . random(10, true), 'get_fee' => $get_fee, 'take_fee' => $take_fee, 'final_fee' => $final_fee, 'account' => iserializer($account['wechat']), 'status' => 2, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_store_getcash_log', $data);
	$getcash_id = pdo_insertid();
	store_update_account($sid, 0 - $get_fee, 2, $getcash_id);
	sys_notice_store_getcash($sid, $getcash_id, 'apply');
	imessage(error(0, '申请提现成功'), '', 'ajax');
}

include itemplate('finance/getcash');

?>
