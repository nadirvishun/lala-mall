<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function icheckdeliveryer() {
	global $_W, $_GPC;
	$_W['deliveryer'] = array();;
	if(is_weixin()) {
		if(!empty($_W['openid'])) {
			$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
			if(!empty($deliveryer)) {
				$_W['deliveryer'] = $deliveryer;
			}
		}
	} else {
		$key = "we7_wmall_deliveryer_session_{$_W['uniacid']}";
		if(isset($_GPC[$key])) {
			$session = json_decode(base64_decode($_GPC[$key]), true);
			if(is_array($session)) {
				$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $session['id']));
				if(is_array($deliveryer) && ($session['hash'] == $deliveryer['password'])) {
					$_W['deliveryer'] = $deliveryer;
				} else {
					isetcookie($key, false, -100);
				}
			} else {
				isetcookie($key, false, -100);
			}
		}
	}
	if(empty($_W['openid'])) {
		$_W['openid'] = $_W['deliveryer']['openid'];
	}
	if(!empty($_W['deliveryer'])) {
		$extra = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $_W['deliveryer']['id']), 'extra');
		$extra = iunserializer($extra['extra']);
		if(empty($extra)) {
			$extra = array(
				'accept_wechat_notice' => 0,
				'accept_voice_notice' => 0,
			);
		}
		$_W['deliveryer']['extra'] = $extra;
		return true;
	}
	if($_W['ispost']) {
		imessage(error(-1, '请先登录'), imurl('delivery/auth/login', array('force' => 1)), 'ajax');
	}
	header("location: " . imurl('delivery/auth/login', array('force' => 1)), true);
	exit;
}

//获取平台的所有配送员
function deliveryer_all($force_update = false) {
	global $_W;
	$cache_key = "tiny_wmall:deliveryers:{$_W['uniacid']}";
	$data = cache_read($cache_key);
	if(!empty($data) && !$force_update) {
		return $data;
	}
	$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_deliveryer') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']), 'id');
	cache_write($cache_key, $deliveryers);
	return $deliveryers;
}

function deliveryer_fetch($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_store_deliveryer') . ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id));
	if(!empty($data)) {
		$data['extra'] = iunserializer($data['extra']);
		$data['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	return $data;
}

function deliveryer_order_stat($sid, $deliveryer_id) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id)));
	return $stat;
}

function deliveryer_plateform_order_stat($deliveryer_id) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id)));
	return $stat;
}

function deliveryer_update_credit2($deliveryer_id, $fee, $trade_type, $extra, $remark = '', $order_type = 'order') {
	global $_W;
	//$trade_type 1: 订单入账, 2: 申请提现, 3: 其他变动
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '账户不存在');
	}
	$now_amount = $deliveryer['credit2'] + $fee;
	pdo_update('tiny_wmall_deliveryer', array('credit2' => $now_amount), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	$log = array(
		'uniacid' => $_W['uniacid'],
		'deliveryer_id' => $deliveryer_id,
		'order_type' => $order_type,
		'trade_type' => $trade_type,
		'extra' => $extra,
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_deliveryer_current_log', $log);
	return true;
}

//配送员app函数
function deliveryer_login($mobile, $password) {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($deliveryer)) {
		return ierror(-1, '账号不存在');
	}
	$password = md5(md5($deliveryer['salt'] . $password) . $deliveryer['salt']);
	if($password != $deliveryer['password']) {
		return ierror(-1, '密码错误');
	}
	if(empty($deliveryer['token'])) {
		$token = $deliveryer['token'] = random(32);
		pdo_update('tiny_wmall_deliveryer', array('token' => $token), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	}
	return ierror(0, '调用成功', $deliveryer);
}

function deliveryer_order_num_update($deliveryer_id) {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id), array('id'));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在');
	}
	$params = array(
		':uniacid' => $_W['uniacid'],
		':deliveryer_id' => $deliveryer_id,
	);
	$takeout_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 7 or delivery_status = 4) and status < 5', $params);
	$update = array(
		'order_takeout_num' => intval($takeout_num),
	);
	if(check_plugin_perm('errander')) {
		$errander_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 2 or delivery_status = 3) and status < 3', $params);
		$update['order_errander_num'] = intval($errander_num);
	}
	pdo_update('tiny_wmall_deliveryer', $update, array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	return true;
}

function sys_notice_deliveryer_settle($deliveryer_id, $note = '') {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在');
	}
	$maneger = $_W['we7_wmall']['config']['manager'];
	if(empty($maneger['openid'])) {
		return error(-1, '平台管理员信息不存在');
	}
	$tips = "尊敬的【{$maneger['nickname']}】，有新的配送员提交了入驻请求。请登录电脑进行权限分配";
	$remark = array(
		"性别 : {$deliveryer['sex']}",
		"年龄 : {$deliveryer['age']}",
		"申请人手机号: {$deliveryer['mobile']}",
		$note
	);
	$remark = implode("\n", $remark);
	$send = array(
		'first' => array(
			'value' => $tips,
			'color' => '#ff510'
		),
		'keyword1' => array(
			'value' => $deliveryer['title'],
			'color' => '#ff510'
		),
		'keyword2' => array(
			'value' => $deliveryer['title'],
			'color' => '#ff510'
		),
		'keyword3' => array(
			'value' => date('Y-m-d H:i', time()),
			'color' => '#ff510',
		),
		'remark' => array(
			'value' => $remark,
			'color' => '#ff510'
		),
	);
	$acc = WeAccount::create($_W['acid']);
	$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
	if(is_error($status)) {
		slog('wxtplNotice', '平台配送员入驻微信通知平台管理员', $send, $status['message']);
	}
	return $status;
}

function to_workstatus($status, $key = 'all') {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '接单中',
		),
		'0' => array(
			'css' => 'label label-danger',
			'text' => '休息中',
		),
	);
	if($key == 'all') {
		return $data[$status];
	} elseif($key == 'css') {
		return $data[$status]['css'];
	} elseif($key == 'text') {
		return $data[$status]['text'];
	}
}

function deliveryer_work_status_set($deliveryer_id, $status) {
	global $_W;
	$tips = array(
		'0' => '休息中',
		'1' => '接单中',
	);
	$status = intval($status);
	if(!in_array($status, array_keys($tips))) {
		return error(-1, '工作状态有误');
	}
	pdo_update('tiny_wmall_deliveryer', array('work_status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	pdo_update('tiny_wmall_store_deliveryer', array('work_status' => $status), array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id));
	$data = array(
		'work_status' => $status,
		'work_status_cn' => $tips[$status]
	);
	return $data;
}

function deliveryer_push_token($deliveryer_id) {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在');
	}
	if(empty($deliveryer['token'])) {
		$deliveryer['token'] = random(32);
		pdo_update('tiny_wmall_deliveryer', array('token' => $deliveryer['token']), array('id' => $deliveryer['id']));
	}

	$config = $_W['we7_wmall']['config']['app']['deliveryer'];
	$relation = array(
		'alias' => $deliveryer['token'],
		'tags' => array(
			$config['serial_sn'],
		),
	);
	if($deliveryer['work_status'] == 1) {
		$relation['tags'][] = $config['push_tags']['working'];
	} else {
		$relation['tags'][] = $config['push_tags']['rest'];
	}
	return $relation;
}

function deliveryer_set_extra($type, $value, $deliveryer_id = 0) {
	global $_W;
	if($deliveryer_id == 0) {
		$deliveryer_id = $_W['deliveryer']['id'];
	}
	$data = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id));
	if(!empty($data)) {
		if(empty($data['extra'])){
			if($type == 'accept_wechat_notice') {
				$extra[$type] = $value;
				$extra['accept_voice_notice'] = 0;
			}
			if($type == 'accept_voice_notice') {
				$extra['accept_wechat_notice'] = 0;
				$extra[$type] = $value;
			}
		} else {
			$extra = iunserializer($data['extra']);
			$extra[$type] = $value;
		}
		$update = array(
			'extra' => iserializer($extra),
		);
		pdo_update('tiny_wmall_store_deliveryer', $update, array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id));
	}
	return true;
}