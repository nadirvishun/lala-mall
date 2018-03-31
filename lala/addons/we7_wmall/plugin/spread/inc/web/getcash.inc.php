<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '推广员提现记录';
	$condition = " where a.uniacid = :uniacid";
	$params[':uniacid'] = $_W['uniacid'];
	$status = $_GPC['status'] ? intval($_GPC['status']) : 0;
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = intval($_GPC['status']);
	}
	$keywords = trim($_GPC['keywords']);
	if(!empty($keywords)) {
		$condition .= " and (b.realname like '%{$keywords}%' or mobile like '%{$keywords}%')";
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']) + 86399;

			$condition .= " AND a.addtime > :start AND a.addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$condition .= ' and a.addtime >= :start';
			$params[':start'] = $starttime;
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_spread_getcash_log') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.spreadid = b.uid ' . $condition, $params);
	$records = pdo_fetchall('SELECT a.*,b.realname,b.avatar FROM ' . tablename('tiny_wmall_spread_getcash_log') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.spreadid = b.uid' . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_spread_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$member = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	//需要有微信模板消息通知
	sys_notice_spread_getcash($id, 'success');
	imessage(error(0, '设置提现状态成功'), '', 'ajax');
}

if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), referer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), referer(), 'ajax');
	}
	$log['account'] = iunserializer($log['account']);
	if(empty($log['account']['realname']) || empty($log['account']['openid'])) {
		imessage(error(-1, '推广员微信信息不完善,无法进行微信付款'), '', 'ajax');
	}
	mload()->classs('wxpay');
	$pay = new Wxpay();
	$params = array(
		'partner_trade_no' => $log['trade_no'],
		'openid' => !empty($log['account']['openid']) ? $log['account']['openid'] : $log['account']['openid'],
		'check_name' => 'FORCE_CHECK',
		're_user_name' => $log['account']['realname'],
		'amount' => $log['final_fee'] * 100,
		'desc' => "{$log['account']['realname']}" . date('Y-m-d H:i', $log['addtime']) . "配送费提现申请"
	);
	$response = $pay->mktTransfers($params);
	if(is_error($response)) {
		imessage(error(-1, $response['message']), '', 'ajax');
	}
	pdo_update('tiny_wmall_spread_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	sys_notice_spread_getcash($id, 'success');
	imessage(error(0, '打款成功'), '', 'ajax');
}

if($op == 'balance') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$uid = $log['spreadid'];
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), referer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), referer(), 'ajax');
	}
	pdo_update('tiny_wmall_spread_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	member_credit_update($uid, 'credit2', $log['final_fee']);
	imessage(error(0, '打款成功'), '', 'ajax');
}


if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), referer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), referer(), 'ajax');
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $log['spreadid']));
	if($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		$extra = array(
			'trade_type' => 3,
			'extra' => '',
			'remark' => $remark,
		);
		spread_update_credit2($log['spreadid'], $log['get_fee'], $extra);
		pdo_update('tiny_wmall_spread_getcash_log', array('status' => 3, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
		sys_notice_spread_getcash($id, 'cancel', $remark);
		imessage(error(0, '提现撤销成功'), referer(), 'ajax');
	}
	include itemplate('getcashOp');
	die();
}

include itemplate('getcash');