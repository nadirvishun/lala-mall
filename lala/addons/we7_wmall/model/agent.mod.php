<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function get_agents($status = -1) {
	global $_W;
	$condition = array(
		'uniacid' => $_W['uniacid'],
	);
	if($status > -1) {
		$condition['status'] = $status;
	}
	$agents = pdo_getall('tiny_wmall_agent', $condition, array('id', 'title', 'area'), 'id');
	return $agents;
}

function get_agent($id = 0, $fields = '*') {
	global $_W;
	if(is_array($fields)) {
		$fields = implode(',', $fields);
	}
	if(empty($id)) {
		$id = $_W['agentid'];
	}
	$agent = pdo_fetch("select {$fields} from " . tablename('tiny_wmall_agent') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if(empty($agent)) {
		return array();
	}
	foreach($agent as $key => $val) {
		if(in_array($key, array('fee', 'account', 'pluginset', 'sysset', 'geofence'))) {
			$agent[$key] = iunserializer($val);
		}
	}
	return $agent;
}

function toagent($id) {
	global $_W;
	$agents = $_W['agents'];
	if(empty($agents)) {
		$agents = get_agents();
	}
	if(empty($agents[$id])) {
		return '未知';
	}
	return $agents[$id]['area'];
}

function agent_serve_fee_items() {
	return array(
		'yes' => array(
			'price' => '商品费用',
			'box_price' => '餐盒费',
			'pack_fee' => '包装费',
			'delivery_fee' => '配送费',
		),
		'no' => array(
			'store_discount_fee' => '商户活动补贴',
			'agent_discount_fee' => '代理商活动补贴',
		)
	);
}

function agent_update_account($id, $fee, $trade_type, $extra, $remark = '', $order_type = 'takeout') {
	global $_W;
	//$trade_type 1: 订单入账, 2: 申请提现, 3: 账户清零
	$agent = pdo_get('tiny_wmall_agent', array('uniacid' => $_W['uniacid'], 'id' => $id), array('amount'));
	if(empty($agent)) {
		return error(-1, '账户不存在');
	}
	$now_amount = $agent['amount'] + $fee;
	pdo_update('tiny_wmall_agent', array('amount' => $now_amount), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$log = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $id,
		'trade_type' => $trade_type,
		'order_type' => $order_type,
		'extra' => $extra,
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_agent_current_log', $log);
	return true;
}


function get_location_agent($location_x, $location_y) {
	global $_W;
	$agentid = -1;
	$agents = pdo_getall('tiny_wmall_agent', array('uniacid' => $_W['uniacid']), array('id', 'geofence'));
	if(!empty($agents)) {
		foreach($agents as $agent) {
			if($agentid > 0) {
				break;
			}
			if(empty($agent)) {
				continue;
			}
			$agent['geofence'] = iunserializer($agent['geofence']);
			if(!is_array($agent['geofence']) || !is_array($agent['geofence']['areas'])) {
				continue;
			}
			$flag = isPointInPolygon($agent['geofence']['areas'], array($location_y, $location_x));
			if($flag) {
				$agentid = $agent['id'];
				break;
			}
		}
	}
	return $agentid;
}

function agent_area() {
	global $_W;
	$initials = pdo_fetchall('select distinct(initial) from ' . tablename('tiny_wmall_agent') . ' where uniacid = :uniacid and status = 1 order by initial', array(':uniacid' => $_W['uniacid']));
	$agents = pdo_fetchall('select id,title,area,initial from ' . tablename('tiny_wmall_agent') . ' where uniacid = :uniacid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
	if(!empty($initials)) {
		foreach($initials as &$row) {
			foreach($agents as $val) {
				if($row['initial'] == $val['initial']) {
					$row['agent'][] = $val;
				}
			}
		}
	}
	return $initials;
}

function sys_notice_agent_getcash($agentid, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$agent = get_agent($agentid, array('id', 'title', 'account'));
	if(empty($agent)) {
		return error(-1, '代理不存在');
	}
	if($type != 'borrow_openid') {
		$log = pdo_get('tiny_wmall_agent_getcash_log', array('uniacid' => $_W['uniacid'], 'agentid' => $agentid, 'id' => $getcash_log_id));
		if(empty($log)) {
			return error(-1, '提现记录不存在');
		}
	}
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		if(!empty($agent['account']) && !empty($agent['account']['openid'])) {
			//通知申请人
			$tips = "您好,【{$agent['account']['nickname']}】,【{$agent['title']}】账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请代理: " . $agent['title'],
				"账户类型: 微信",
				"真实姓名: " . $agent['account']['realname'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($agent['account']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '代理申请提现微信通知申请人', $send, $status['message']);
			}
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,代理【{$agent['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请代理: " . $agent['title'],
				"账户类型: 微信",
				"真实姓名: " . $agent['account']['realname'],
				"提现总金额: " . $log['get_fee'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '代理申请提现微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($agent['account']) || empty($agent['account']['openid'])) {
			return error(-1, '代理提现账户信息不完善');
		}
		$tips = "您好,【{$agent['account']['nickname']}】,【{$agent['title']}】账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请代理: " . $agent['title'],
			"账户类型: 微信",
			"真实姓名: " . $agent['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($agent['account']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_success_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '代理申请提现成功微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($agent['account']) || empty($agent['account']['openid'])) {
			return error(-1, '代理提现账户信息不完善');
		}
		$tips = "您好,【{$agent['account']['nickname']}】, 【{$agent['title']}】账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请代理: " . $agent['title'],
			"账户类型: 微信",
			"真实姓名: " . $agent['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($agent['account']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '代理申请提现失败微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'borrow_openid') {
		if(empty($agent['account']) || empty($agent['account']['openid'])) {
			return error(-1, '代理提现账户信息不完善');
		}
		$tips = "您好,【{$agent['account']['nickname']}】, 您正在进行代理【{$agent['title']}】的提现申请。平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
		$remark = array(
			"申请门店: " . $agent['title'],
			"账户类型: 微信",
			'请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员'
		);
		$params = array(
			'first' => $tips,
			'money' => $getcash_log_id,
			'timet' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$url = imurl("wmall/auth/oauth", array('params' => base64_encode(json_encode($payment_wechat[$payment_wechat['type']]))), true);
		$status = $acc->sendTplNotice($agent['account']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '微信端代理申请提现授权微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'cancel') {
		if(empty($agent['account']) || empty($agent['account']['openid'])) {
			return error(-1, '代理提现账户信息不完善');
		}
		$addtime = date('Y-m-d H:i', $log['addtime']);
		$tips = "您好,【{$agent['account']['nickname']}】,【{$agent['title']}】在{$addtime}的申请提现已被平台管理员撤销";
		$remark = array(
			"订单　号: " . $log['trade_no'],
			"申请代理: " . $agent['title'],
			"撤销时间: " . date('Y-m-d H:i', $log['endtime']),
			'撤销原因: ' . $note,
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($agent['account']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '代理申请提现被平台管理员取消微信通知申请人', $send, $status['message']);
		}
	}
	return $status;
}

function update_store_agent($storeid, $agentid) {
	global $_W;
	pdo_update('tiny_wmall_store', array('agentid' => $agentid), array('uniacid' => $_W['uniacid'], 'id' => $storeid));
	return true;
}