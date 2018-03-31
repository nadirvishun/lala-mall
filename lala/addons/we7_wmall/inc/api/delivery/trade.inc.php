<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'inout');
$config_cash = $config_delivery['cash'];

if ($op == 'getcash_config') {
	$data = array('delivery_type' => $_W['we7_wmall']['deliveryer']['type'], 'credit2' => $deliveryer['credit2'], 'rule' => $config_cash);
	message(ierror(0, '', $data), '', 'ajax');
}

if ($op == 'getcash_submit') {
	if (empty($deliveryer['openid']) || empty($deliveryer['title'])) {
		message(ierror(-1, '配送员账户不完善, 无法提现'), '', 'ajax');
	}

	$get_fee = floatval($_GPC['fee']);
	if (!$get_fee || ($get_fee <= 0)) {
		message(ierror(-1, '提现金额有误'), '', 'ajax');
	}

	if ($get_fee < $config_cash['get_cash_fee_limit']) {
		message(ierror(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
	}

	if ($deliveryer['credit2'] < $get_fee) {
		message(ierror(-1, '提现金额大于账户可用余额'), '', 'ajax');
	}

	$take_fee = round($get_fee * ($config_cash['get_cash_fee_rate'] / 100), 2);
	$take_fee = max($take_fee, $config_cash['get_cash_fee_min']);

	if (0 < $config_cash['get_cash_fee_max']) {
		$take_fee = min($take_fee, $config_cash['get_cash_fee_max']);
	}

	$final_fee = $get_fee - $take_fee;

	if ($final_fee < 0) {
		message(ierror(-1, '提现金额' . $get_fee . '元, 需要收取手续费' . $take_fee . '元, 实际到账金额' . $final_fee . ', 无法体现'), '', 'ajax');
	}

	$openid = mktTransfers_get_openid($deliveryer['id'], $deliveryer['openid'], $get_fee, 'deliveryer');

	if (is_error($openid)) {
		imessage($openid, '', 'ajax');
	}

	$data = array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer['id'], 'trade_no' => date('YmdHis') . random(10, true), 'get_fee' => $get_fee, 'take_fee' => $take_fee, 'final_fee' => $final_fee, 'account' => iserializer(array('nickname' => $deliveryer['nickname'], 'openid' => $openid, 'avatar' => $deliveryer['avatar'], 'realname' => $deliveryer['title'])), 'status' => 2, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_deliveryer_getcash_log', $data);
	$getcash_id = pdo_insertid();
	$remark = date('Y-m-d H:i:s') . '申请提现,提现金额' . $get_fee . '元, 手续费' . $take_fee . '元, 实际到账' . $final_fee . '元';
	deliveryer_update_credit2($deliveryer['id'], 0 - $get_fee, 2, $getcash_id, $remark);
	sys_notice_deliveryer_getcash($deliveryer['id'], $getcash_id, 'apply');
	message(ierror(0, '申请提现成功', array('getcash_id' => $getcash_id)), '', 'ajax');
}

if ($op == 'getcash_detail') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id, 'deliveryer_id' => $deliveryer['id']));

	if (empty($log)) {
		message(ierror(-1, '提现记录不存在或已经删除'), '', 'ajax');
	}

	$log['title'] = $deliveryer['title'];
	$log['nickname'] = $deliveryer['nickname'];
	$log['openid'] = $deliveryer['openid'];
	$log['addtime_cn'] = date('Y-m-d H:i', $log['addtime']);
	$log['endtime_cn'] = date('Y-m-d H:i', $log['endtime']);
	$status = array(1 => '提现成功', 2 => '申请中');
	$log['status_cn'] = $status[$log['status']];
	message(ierror(0, '申请提现成功', $log), '', 'ajax');
}

if ($op == 'inout') {
	$condition = ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id';
	$params = array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer['id']);
	$trade_type = intval($_GPC['trade_type']);

	if (0 < $trade_type) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}

	$type = (trim($_GPC['type']) ? trim($_GPC['type']) : 'load');
	$id = intval($_GPC['id']);

	if ($type == 'load') {
		if (0 < $id) {
			$condition .= ' and id < :id';
			$params[':id'] = $id;
		}
	}
	else {
		$condition .= ' and id > :id';
		$params[':id'] = $id;
	}

	$min_id = intval(pdo_fetchcolumn('SELECT min(id) as min_id FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition, $params));
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT 20', $params, 'id');
	$min = $max = 0;

	if (!empty($records)) {
		$trade_types = array(1 => '配送费入账', 2 => '申请提现', 3 => '其他变动');

		foreach ($records as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['trade_type_cn'] = $trade_types[$row['trade_type']];
		}

		$more = 1;
		$min = min(array_keys($orders));
		$max = max(array_keys($orders));

		if ($min <= $min_id) {
			$more = 0;
		}
	}

	$records = array_values($records);
	$data = array('list' => $records, 'max_id' => $max, 'min_id' => $min, 'more' => $more);
	$respon = array('resultCode' => 0, 'resultMessage' => '调用成功', 'data' => $data);
	message($respon, '', 'ajax');
}

?>
