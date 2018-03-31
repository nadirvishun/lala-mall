<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '账户余额提现';
	$account = store_account($sid);
	if(empty($account['wechat']['openid'])) {
		header('location:' . iurl('store/finance/getcash/account'));
		die;
	}
	if($_W['ispost']) {
		if(empty($account['wechat']['openid']) || empty($account['wechat']['realname'])) {
			imessage(error(-1, '提现前请先完善提现账户'), '', 'ajax');
		}

		$get_fee = intval($_GPC['get_fee']);
		if($get_fee < $account['fee_limit']) {
			imessage(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
		}
		if($get_fee > $account['amount']) {
			imessage(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
		}
		$take_fee = round($get_fee * ($account['fee_rate'] / 100), 2);
		$take_fee = max($take_fee, $account['fee_min']);
		if($account['fee_max'] > 0) {
			$take_fee = min($take_fee, $account['fee_max']);
		}
		$final_fee = $get_fee - $take_fee;
		if($final_fee < 0)  {
			$final_fee = 0;
		}
		$openid = mktTransfers_get_openid($sid, $account['wechat']['openid'], $_GPC['get_fee']);
		if(is_error($openid)) {
			imessage($openid, '', 'ajax');
		}
		$account['wechat']['openid'] = $openid;
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'trade_no' => date('YmdHis') . random(10, true),
			'get_fee' => $get_fee,
			'take_fee' => $take_fee,
			'final_fee' => $final_fee,
			'account' => iserializer($account['wechat']),
			'status' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_store_getcash_log', $data);
		$getcash_id = pdo_insertid();
		store_update_account($sid, -$get_fee, 2, $getcash_id);
		//提现通知
		sys_notice_store_getcash($sid, $getcash_id, 'apply');
		imessage(error(0, '申请提现成功,等待平台管理员审核'), iurl('store/finance/getcash/log') , 'ajax');
	}
}

if($ta == 'account') {
	$_W['page']['title'] = '设置提现账户';
	$account = store_account($sid);
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
		);
		$wechat = array();
		$wechat['openid'] = trim($_GPC['wechat']['openid']) ? trim($_GPC['wechat']['openid']) : imessage(error(-1, '微信昵称不能为空'), '', 'ajax');
		$wechat['nickname'] = trim($_GPC['wechat']['nickname']);
		$wechat['avatar'] = trim($_GPC['wechat']['avatar']);
		$wechat['realname'] = trim($_GPC['wechat']['realname']) ? trim($_GPC['wechat']['realname']) : imessage(error(-1, '微信实名认证姓名不能为空'), '', 'ajax');
		$data['wechat'] = iserializer($wechat);

		if(empty($account)) {
			$data['amount'] = 0.00;
			pdo_insert('tiny_wmall_store_account', $data);
		} else {
			pdo_update('tiny_wmall_store_account', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		}
		imessage(error(0, '设置提现账户成功'), iurl('store/finance/getcash/account'), 'ajax');
	}
}

if($ta == 'log') {
	$_W['page']['title'] = '提现记录';
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}
include itemplate('store/finance/getcash');

