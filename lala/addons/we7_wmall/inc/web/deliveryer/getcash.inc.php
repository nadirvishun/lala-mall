<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('deliveryer');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '配送员提现记录';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);

	if (0 < $deliveryer_id) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}

	$agentid = intval($_GPC['agentid']);

	if (0 < $agentid) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$status = intval($_GPC['status']);

	if (0 < $status) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}

	$days = (isset($_GPC['days']) ? intval($_GPC['days']) : -2);
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;

	if (-2 < $days) {
		if ($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']) + 86399;
			$condition .= ' AND addtime > :start AND addtime < :end';
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		}
		else {
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$condition .= ' and addtime >= :start';
			$params[':start'] = $starttime;
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition . ' ORDER BY id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
}

if ($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));

	if (empty($log)) {
		imessage(error(-1, '提现记录不存在'), '', 'ajax');
	}

	$log['account'] = iunserializer($log['account']);

	if (!is_array($log['account'])) {
		$log['account'] = array();
	}

	if ($log['status'] == 1) {
		imessage(error(-1, '该提现记录已处理'), '', 'ajax');
	}

	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $log['deliveryer_id']));
	if (empty($deliveryer) || empty($deliveryer['title']) || empty($deliveryer['openid'])) {
		imessage(error(-1, '配送员微信信息不完善,无法进行微信付款'), '', 'ajax');
	}

	mload()->classs('wxpay');
	$pay = new WxPay();
	$params = array('partner_trade_no' => $log['trade_no'], 'openid' => !empty($log['account']['openid']) ? $log['account']['openid'] : $deliveryer['openid'], 'check_name' => 'FORCE_CHECK', 're_user_name' => $deliveryer['title'], 'amount' => $log['final_fee'] * 100, 'desc' => $deliveryer['title'] . date('Y-m-d H:i', $log['addtime']) . '配送费提现申请');
	$response = $pay->mktTransfers($params);

	if (is_error($response)) {
		imessage(error(-1, $response['message']), '', 'ajax');
	}

	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	sys_notice_deliveryer_getcash($log['deliveryer_id'], $id, 'success');
	imessage(error(0, '打款成功'), '', 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '设置提现状态成功'), '', 'ajax');
}

if ($op == 'cancel') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));

	if ($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), referer(), 'ajax');
	}
	else {
		if ($log['status'] == 3) {
			imessage(error(-1, '本次提现已撤销'), referer(), 'ajax');
		}
	}

	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $log['deliveryer_id']));

	if ($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		deliveryer_update_credit2($log['deliveryer_id'], $log['get_fee'], 3, '', $remark, '');
		pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => 3, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
		sys_notice_deliveryer_getcash($log['deliveryer_id'], $id, 'cancel', $remark);
		imessage(error(0, '提现撤销成功'), referer(), 'ajax');
	}

	include itemplate('deliveryer/plateformOp');
	exit();
}

include itemplate('deliveryer/getcash');

?>
