<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('deliveryer');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '平台配送员';
	$condition = ' WHERE a.uniacid = :uniacid and a.sid = 0';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$work_status = intval($_GPC['work_status']);
	if($work_status > 0) {
		$condition .= ' and b.work_status = :work_status';
		$params[':work_status'] = $work_status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (b.title like '%{$keyword}%' or b.nickname like '%{$keyword}%' or b.mobile like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_deliveryer') . ' as a left join '. tablename('tiny_wmall_deliveryer') . ' as b on a.deliveryer_id = b.id' .  $condition, $params);
	$data = pdo_fetchall('SELECT a.*,b.nickname,b.title,b.mobile,b.sex,b.age,b.avatar,b.credit2,b.work_status FROM ' . tablename('tiny_wmall_store_deliveryer') . ' as a left join '. tablename('tiny_wmall_deliveryer') . ' as b on a.deliveryer_id = b.id' . $condition . ' ORDER BY a.id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($data)) {
		$deliveryers = pdo_getall('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid']), array(), 'id');
		foreach($data as &$row) {
			$row['stores'] = pdo_fetchall('select sid from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and sid > 0', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $row['deliveryer_id']));
		}
		$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	}
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'account_turncate') {
	if(!$_W['isajax']) {
		return false;
	}
	if(empty($_GPC['ids'])) {
		imessage(error(-1, '请选择要操作的账户'), '', 'ajax');
	}
	$remark = trim($_GPC['remark']);
	foreach($_GPC['ids'] as $id) {
		$id = intval($id);
		if(!$id) continue;
		$account = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($account) || empty($account['credit2']) || $account['credit2'] == 0) {
			continue;
		}
		deliveryer_update_credit2($id, -$account['credit2'], 3, '', $remark);
	}
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'changes') {
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id), array('avatar', 'nickname', 'mobile', 'credit2'));
	if($_W['ispost']) {
		$change_type = intval($_GPC['change_type']);
		$credit2 = floatval($_GPC['credit2']);
		$remark= trim($_GPC['remark']);
		$fee =  $credit2 - $deliveryer['credit2'] ;
		if($change_type == 1) {
			$fee = '+' . $credit2;
			$credit2 = $deliveryer['credit2'] + $credit2;
		} elseif($change_type == 2) {
			$fee = '-' . $credit2;
			$credit2 = $deliveryer['credit2'] - $credit2;
			if($credit2 < 0) {
				$credit2 = 0;
				$fee = '-' . $deliveryer['credit2'];
			}
		}
		pdo_update('tiny_wmall_deliveryer', array('credit2' => $credit2), array('uniacid' => $_W['uniacid'], 'id' => $id));
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'deliveryer_id' => $id,
			'trade_type' => 3,
			'fee' => $fee,
			'amount' => $credit2,
			'addtime' => TIMESTAMP,
			'remark' => $remark
		);
		pdo_insert('tiny_wmall_deliveryer_current_log', $insert);
		imessage(error(0, '更改账户余额成功'), referer(),'ajax');
	}
	include itemplate('deliveryer/plateformOp');
	die();
}

if($op == 'add_ptf_deliveryer') {
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			imessage(error(-1, '手机号不能为空'), '', 'ajax');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($deliveryer)) {
			imessage(error(-1, '未找到该手机号对应的配送员'), '', 'ajax');
		}
		$is_exist = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer['id'], 'sid' => 0));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号对应的配送员已经是平台配送员, 请勿重复添加'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => 0,
			'deliveryer_id' => $deliveryer['id'],
			'delivery_type' => 2,
			'extra' => iserializer(array(
				'accept_wechat_notice' => 1,
				'accept_voice_notice' => 1,
			)),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_store_deliveryer', $data);
		imessage(error(0, '添加平台配送员成功'), referer(), 'ajax');
	}
	include itemplate('deliveryer/plateformAdd');
	die();
}

if($op == 'del_ptf_deliveryer') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => 0, 'id' => $id));
	imessage(error(0, '取消配送员平台配送权限成功'), referer(), 'ajax');
}

if($op == 'inout') {
	$title = '收支明细';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_current_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$order_trade_type = order_trade_type();
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
}

if($op == 'stat') {
	$_W['page']['title'] = '配送统计';
	$id = intval($_GPC['id']);
	$deliveryer = deliveryer_fetch($id);
	if(empty($deliveryer)) {
		imessage('配送员不存在', referer(), 'error');
	}
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;

	if($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array(),
		);
		for($i = 0; $i < $day_num; $i++){
			$key = date('m-d', $start + 86400 * $i);
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
		}
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id AND delivery_type = 2 and status = 5', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id));
		foreach($data as $da) {
			$key = date('m-d', $da['addtime']);
			if(in_array($key, array_keys($days))) {
				$datasets['flow1'][$key]++;
			}
		}
		$shuju['label'] = array_keys($days);
		$shuju['datasets'] = $datasets;
		exit(json_encode($shuju));
	}
	$stat = deliveryer_plateform_order_stat($id);
}

if($op == 'getcashlog') {
	$condition = ' WHERE uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];

	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
}

if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($log)) {
		message('提现记录不存在', referer(), 'error');
	}
	if($log['status'] == 1) {
		message('该提现记录已处理', referer(), 'error');
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $log['deliveryer_id']));
	if(empty($deliveryer) || empty($deliveryer['title']) || empty($deliveryer['openid'])) {
		message('配送员微信信息不完善,无法进行微信付款', referer(), 'error');
	}
	mload()->classs('wxpay');
	$pay = new WxPay();
	$params = array(
		'partner_trade_no' => $log['trade_no'],
		'openid' => $deliveryer['openid'],
		'check_name' => 'FORCE_CHECK',
		're_user_name' => $deliveryer['title'],
		'amount' => $log['final_fee'] * 100,
		'desc' => "{$deliveryer['title']}" . date('Y-m-d H:i') . "配送费提现申请"
	);
	$response = $pay->mktTransfers($params);
	if(is_error($response)) {
		message($response['message'], referer(), 'error');
	}
	sys_notice_deliveryer_getcash($log['deliveryer_id'], $id, 'success');
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('打款成功', referer(), 'success');
}

if($op == 'gatcashstatus') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('设置提现状态成功', referer(), 'success');
}

include itemplate('deliveryer/plateform');



