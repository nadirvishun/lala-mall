<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

function spread_groups() {
	global $_W;
	$groups = pdo_fetchall('select * from ' .tablename('tiny_wmall_spread_groups'). ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');
	return $groups;
}

function to_spreadgroup($groupid, $key = "all") {
	global $_W;
	$data = array();
	$groups = spread_groups();
	foreach ($groups as $val) {
		$data[$val['id']] = array(
			'title' => $val['title'],
		);
	}
	if($key == "all") {
		return $data;
	} elseif($key == "title") {
		return $data[$groupid]['title'];
	}
}

function spread_status($status, $key = 'all') {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '正常'
		),
		'2' => array(
			'css' => 'label label-default',
			'text' => '黑名单'
		),
		'0' => array(
			'css' => 'label label-warning',
			'text' => '待审核'
		),
	);
	if($key == 'all') {
		return $data;
	} elseif($key == 'text') {
		return $data[$status]['text'];
	} elseif($key == 'css') {
		return $data[$status]['css'];
	}
}

function spread_getcash_type($channel, $key = 'all') {
	$data = array(
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信',
		),
		'credit' => array(
			'css' => 'label label-warning',
			'text' => '账户余额',
		),
	);
	if($key == 'all') {
		return $data[$channel];
	} elseif($key == 'text') {
		return $data[$channel]['text'];
	} elseif($key == 'css') {
		return $data[$channel]['css'];
	}
}

function get_spread($spread_id = 0) {
	global $_W;
	if($spread_id == 0){
		$spread_id = $_W['member']['uid'];
	}
	$fields = array('is_spread', 'spreadcredit2', 'spread1', 'spread2', 'spread_groupid', 'spread_status', 'spreadtime');
	$spread = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $spread_id), $fields);
	$group= pdo_get('tiny_wmall_spread_groups', array('uniacid' => $_W['uniacid'], 'id' => $spread['spread_groupid']));
	$spread['groupname'] = $group['title'];
	return $spread;
}

function spread_commission_stat($spread_id = 0) {
	global $_W;
	if($spread_id == 0) {
		$spread_id = $_W['member']['uid'];
	}
	$commission_grandtotal = pdo_fetchcolumn('select sum(fee) from' . tablename('tiny_wmall_spread_current_log') . 'where uniacid = :uniacid and spreadid = :spreadid and trade_type = 1', array(':uniacid' => $_W['uniacid'], ':spreadid' => $spread_id));
	$commission_grandtotal = round($commission_grandtotal, 2);
	$commission_getcash_apply = pdo_fetchcolumn('select sum(get_fee) from' . tablename('tiny_wmall_spread_getcash_log') . 'where uniacid = :uniacid and status = 2 and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $spread_id));
	$commission_getcash_apply = round($commission_getcash_apply, 2);
	$commission_getcash_success = pdo_fetchcolumn('select sum(get_fee) from' .tablename('tiny_wmall_spread_getcash_log') . 'where uniacid = :uniacid and status = 1 and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $spread_id));
	$commission_getcash_success = round($commission_getcash_success, 2);
	$member = pdo_fetch('select spreadcredit2 from' . tablename('tiny_wmall_members') . 'where uniacid = :uniacid and uid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $spread_id));
	$spreadcredit2 = round($member['spreadcredit2'], 2);
	$data = array(
		'commission_getcash_apply' => $commission_getcash_apply,
		'commission_getcash_success' => $commission_getcash_success,
		'spreadcredit2' => $spreadcredit2,
		'commission_grandtotal' => $commission_grandtotal,
	);
	return $data;
}

function spread_group_update($spread_id = 0, $wx_tpl = false) {
	global $_W;
	$spread_id = intval($spread_id);
	if(empty($spread_id)) {
		$spread_id = $_W['member']['uid'];
	}
	//先查询升级条件
	$config = get_plugin_config('spread.relate');
	$condition = " where uniacid = :uniacid";
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	if($config['group_update_mode'] == 'order_money') {
		//推广订单总额满
		$condition .= " and is_pay = 1 and status = 5 and (spread1 = :spread or spread2 = :spread)";
		$params[':spread'] = $spread_id;
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config['group_update_mode'] == 'order_money_1') {
		//一级推广订单金额满
		$condition .= " and is_pay = 1 and status = 5 and spread1 = :spread";
		$params[':spread'] = $spread_id;
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config['group_update_mode'] == 'order_count') {
		//推广订单总数满
		$condition .= " and is_pay = 1 and status = 5";
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_order') . $condition, $params);
	} elseif($config['group_update_mode'] == 'order_count_1') {
		//一级推广订单总数满
		$condition .= " and is_pay = 1 and status = 5 and spread1 = :spread";
		$params[':spread'] = $spread_id;
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_order'). $condition, $params);
	} elseif($config['group_update_mode'] == 'self_order_money') {
		//自购订单金额满
		$condition .= " and is_pay = 1 and status = 5 and uid = :uid";
		$params[':uid'] = $spread_id;
		$result = pdo_fetchcolumn('select count(final_fee) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config['group_update_mode'] == 'self_order_count') {
		//自购订单数量满
		$condition .= " and is_pay = 1 and status =5 and uid = :uid";
		$params[':uid'] = $spread_id;
		$result = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_order') . $condition, $params);
	} elseif($config['group_update_mode'] == 'down_count') {
		//下线总人数满
		$condition .= " and (spread1 = :spread or spread2 = :spread)";
		$params[':spread'] = $spread_id;
		$result = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . $condition, $params);
	} elseif($config['group_update_mode'] == 'down_count_1') {
		//一级总人数满
		$condition .= " and spread1 = :spread";
		$params[':spread'] = $spread_id;
		$result = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . $condition, $params);
	}
	$groups = pdo_fetchall('select * from' . tablename('tiny_wmall_spread_groups'). ' where uniacid = :uniacid order by group_condition asc', array(':uniacid' => $_W['uniacid']), 'id');
	foreach($groups as $group) {
		if($result >= $group['group_condition']) {
			$group_id = $group['id'];
		}
	}
	pdo_update('tiny_wmall_members', array('spread_groupid' => $group_id), array('uniacid' => $_W['uniacid'], 'uid' => $spread_id));
	if($wx_tpl) {

	}
	return true;
}

//推广员申请入驻通知（申请，审核通过，审核不通过）
function sys_notice_spread_down($uid, $type, $extra = array()) {
	global $_W;
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('nickname', 'openid', 'spread1', 'spread2', 'spreadfixed'));
	if(empty($member)) {
		return error(-1, '用户不存在');
	}
	$spreads = member_spread($uid);
	if(empty($spreads)) {
		return error(-1, '推广上线不存在');
	}
	$spread1 = $spreads['spread1'];
	$config_mall = $_W['we7_wmall']['config']['mall'];
	$config_spread = get_plugin_config('spread');
	$acc = WeAccount::create($_W['acid']);
	if($type == 'pseudo_down') {
		if(!empty($spread1['openid'])){
			//通知一级上线
			$tips = "您好,【{$spread1['nickname']}】,{$member['nickname']}通过您分享的二维码登陆了{$config_mall['title']}。";
			$remark = array(
				"顾客昵称: {$member['nickname']}",
			);
			if(empty($member['spreadfixed'])) {
				if($config_spread['relate']['become_child'] == 1) {
					$remark[] = "注意:您并不是{$member['nickname']}的固定推广员,在{$member['nickname']}下单确认收货之前,他的推广员都有可能发生变化";
				} elseif($config_spread['relate']['become_child'] == 2) {
					$remark[] = "注意:您并不是{$member['nickname']}的固定推广员,在{$member['nickname']}下单确认收货并进行评价之前,他的推广员都有可能发生变化";
				}
			}
		}
	} elseif($type == 'new_down') {
		$tips = "您好,【{$spread1['nickname']}】";
		if($extra['channel'] == 'qrcode') {
			$tips .= ",{$member['nickname']}通过您分享的二维码登陆了{$config_mall['title']},您已成功升级为{$member['nickname']}的推广员";
		} elseif($extra['channel'] == 'order_end') {
			$tips .= "您成功推荐{$member['nickname']}在{$config_mall['title']}下单并已确认收货,您已成功升级为{$member['nickname']}的推广员";
		} elseif($extra['channel'] == 'order_comment') {
			$tips .= "您成功推荐{$member['nickname']}在{$config_mall['title']}下单,确认收货并已完成评价,您已成功升级为{$member['nickname']}的推广员";
		}
		$remark = array(
			"顾客昵称: {$member['nickname']}",
			"{$member['nickname']}今后在{$config_mall['title']}下单,您将会获得平台的返佣"
		);
	}
	$remark = implode("\n", $remark);
	$send = array(
		'first' => array(
			'value' => $tips,
			'color' => '#ff510',
		),
		'keyword1' => array(
			'value' => '您有新的推广用户',
			'color' => '#ff510',
		),
		'keyword2' => array(
			'value' => '推广通知',
			'color' => '#ff510',
		),
		'remark' => array(
			'value' => $remark,
			'color' => '#ff510',
		),
	);
	$status = $acc->sendTplNotice($spread1['openid'], $_W['we7_wmall']['config']['notice']['wechat']['task_tpl'], $send);
	if(is_error($status)) {
		slog('wxtplNotice', '新的推广下线通知', $send, $status['message']);
	}
	return $status;
}
//推广员申请入驻通知（申请，审核通过，审核不通过）
function sys_notice_spread_settle($spread_id, $type, $extra = array()) {
	global $_W;
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $spread_id));
	if(empty($member)) {
		return error(-1, '用户不存在');
	}
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		if(!empty($member['openid'])) {
			// 通知申请人
			$tips = "您好,【{$member['realname']}】,您的推广员入驻申请已经提交,请等待管理员审核";
			$remark = array(
				"申请　人: " . $member['realname'],
				"手机　号: " . $member['mobile'],
			);
			$remark = implode("\n", $remark);
			$send = array(
				'first' => array(
					'value' => $tips,
					'color' => '#ff510',
				),
				'keyword1' => array(
					'value' => $member['realname'],
					'color' => '#ff510',
				),
				'keyword2' => array(
					'value' => $member['realname'],
					'color' => '#ff510',
				),
				'keyword3' => array(
					'value' => date('Y-m-d H:i', time()),
					'color' => '#ff510',
				),
				'remark' => array(
					'value' => $remark,
					'color' => '#ff510',
				),
			);
			$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '推广员入驻申请提交', $send, $status['message']);
			}
		}
		$manager = $_W['we7_wmall']['config']['manager'];
		if(!empty($manager['openid'])){
			//通知平台管理员
			$tips =  "尊敬的【{$manager['nickname']}】，有新的推广员提交了入驻请求。请登录电脑进行权限分配";
			$remark = array(
				"申请　人: " . $member['realname'],
				"手机　号: " . $member['mobile'],
			);
			$remark = implode("\n", $remark);
			$send = array(
				'first' => array(
					'value' => $tips,
					'color' => '#ff510',
				),
				'keyword1' => array(
					'value' => $member['realname'],
					'color' => '#ff510',
				),
				'keyword2' => array(
					'value' => $member['realname'],
					'color' => '#ff510',
				),
				'keyword3' => array(
					'value' => date('Y-m-d H:i', time()),
					'color' => '#ff510',
				),
				'remark' => array(
					'value' => $remark,
					'color' => '#ff510',
				),
			);
			$status = $acc->sendTplNotice($manager['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '推广员入驻微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($member['openid'])){
			return error(-1, '推广员信息不完善');
		}
		$tips = "您好,【{$member['realname']}】,您的推广员入驻申请已经通过审核";
		$remark = array(
			'如有疑问请及时联系平台管理人员'
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510',
			),
			'keyword1' => array(
				'value' => $member['realname'],
				'color' => '#ff510',
			),
			'keyword2' => array(
				'value' => $member['realname'],
				'color' => '#ff510',
			),
			'keyword3' => array(
				'value' => date('Y-m-d H:i', time()),
				'color' => '#ff510',
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510',
			),
		);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '推广员入驻成功', $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($member['openid'])){
			return error(-1, '推广员信息不完善');
		}
		$tips = "您好,【{$member['realname']}】,您的推广员入驻申请失败";
		$remark = array(
			'如有疑问请及时联系平台管理人员'
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510',
			),
			'keyword1' => array(
				'value' => $member['realname'],
				'color' => '#ff510',
			),
			'keyword2' => array(
				'value' => $member['realname'],
				'color' => '#ff510',
			),
			'keyword3' => array(
				'value' => date('Y-m-d H:i', time()),
				'color' => '#ff510',
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510',
			),
		);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '推广员入驻申请失败', $send, $status['message']);
		}
	}
	return $status;
}

function sys_notice_spread_getcash($getcash_log_id, $type = 'apply', $note = array()) {
	global $_W;
	if($type == 'borrow_openid') {
		$member = pdo_get('tiny_wmall_members',  array('uniacid' => $_W['uniacid'], 'uid' => $getcash_log_id));
	} else {
		$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $getcash_log_id));
		if(empty($log)) {
			return error(-1, '提现记录不存在');
		}
		$member = pdo_get('tiny_wmall_members',  array('uniacid' => $_W['uniacid'], 'uid' => $log['spreadid']));
	}
	if(empty($member)) {
		return error(-1, '推广员不存在');
	}
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		if(!empty($member['openid'])) {
			//通知申请人
			$tips = "您好,【{$member['realname']}】, 您的推广佣金提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请　人: " . $member['realname'],
				"手机　号: " . $member['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
			);
			if(!empty($note)) {
				$remark[] = implode("\n", $note);
			}
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '推广员申请佣金提现微信通知申请人', $send, $status['message']);
			}
		}
		$manager = $_W['we7_wmall']['config']['manager'];
		if(!empty($manager['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$manager['nickname']}】,推广员【{$member['realname']}】申请佣金提现,请尽快处理";
			$remark = array(
				"申请　人: " . $member['realname'],
				"手机　号: " . $member['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
			);
			if(!empty($note)) {
				$remark[] = implode("\n", $note);
			}
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($manager['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '推广员申请佣金提现微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($member['openid'])) {
			return error(-1, '推广员信息不完善');
		}
		$tips = "您好,【{$member['realname']}】,您的推广佣金提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $member['realname'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['take_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_success_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '推广员申请佣金提现成功微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($member['openid'])) {
			return error(-1, '推广员信息不完善');
		}
		$tips = "您好,【{$member['realname']}】, 您的推广佣金提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $member['realname'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '推广员申请佣金提现失败微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'borrow_openid') {
		if(empty($member['openid'])) {
			return error(-1, '推广员信息不完善');
		}
		//通知申请人
		$tips = "您好,【{$member['realname']}】, 您正在进行推广佣金提现申请.平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
		$remark = array(
			"申请　人: " . $member['realname'],
			"手机　号: " . $member['mobile'],
			'请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'timet' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$url = imurl("wmall/auth/oauth", array('params' => base64_encode(json_encode($payment_wechat[$payment_wechat['type']]))), true);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '微信端推广员申请佣金提现授权微信通知申请人', $send, $status['message']);
		}
	} elseif($type == 'cancel') {
		if(empty($member['openid'])) {
			return error(-1, '推广员信息不完善');
		}
		$addtime = date('Y-m-d H:i', $log['addtime']);
		$tips = "您好,【{$member['realname']}】,您在{$addtime}的申请佣金提现已被平台管理员撤销,您可以重新发起提现申请";
		$remark = array(
			"订单　号: " . $log['trade_no'],
			"撤销时间: " . date('Y-m-d H:i', $log['endtime']),
			'如有疑问请及时联系平台管理人员'
		);
		if(!empty($note)) {
			$remark["撤销原因"] = implode("\n", $note);
		}
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($member['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '推广员申请佣金提现被平台管理员取消微信通知申请人', $send, $status['message']);
		}
	}
//	return $member['openid'];
	return $status;
}

//$extra参数 trade_type, extra, remark
function spread_update_credit2($spread_id, $fee, $extra = array()) {
	global $_W;
	//trade_type 1: 订单入账, 2: 申请提现, 3: 其他变动
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $spread_id), 'spreadcredit2');
	if(empty($member)) {
		return error(-1, '账户不存在');
	}
	$now_amount = $member['spreadcredit2'] + $fee;
	pdo_update('tiny_wmall_members', array('spreadcredit2' => $now_amount), array('uniacid' => $_W['uniacid'], 'uid' => $spread_id));
	$log = array(
		'uniacid' => $_W['uniacid'],
		'spreadid' => $spread_id,
		'trade_type' => $extra['trade_type'],
		'extra' => $extra['extra'],
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'remark' => $extra['remark']
	);
	pdo_insert('tiny_wmall_spread_current_log', $log);
	return true;
}

function spread_order_balance($order_id) {
	global $_W;
	$order = pdo_fetch('select a.id,a.spread1,a.spread2,a.spreadbalance,a.data,a.endtime,a.status,b.oid from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_order_comment') . ' as b on a.id = b.oid where a.uniacid = :uniacid and a.id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $order_id));
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	if($order['status'] != 5) {
		return error(-1, '订单未完成');
	}
	if($order['spreadbalance'] == 1) {
		return error(-1, '订单已经结算');
	}
	$config_settle = get_plugin_config('spread.settle');
	if((TIMESTAMP - $order['endtime']) < $config_settle['balance_days'] * 86400) {
		return error(-1, '未到结算时间');
	}
	$data = iunserializer($order['data']);
	$commission = $data['spread']['commission'];
	$balance = 1;
	if($config_settle['balance_condition'] == 2 && empty($order['oid'])) {
		//顾客确认收货并评价
		$balance = 0;
	}
	if($balance == 1) {
		if(!empty($commission)) {
			$fee_spread1 = $commission['spread1'];
			if($fee_spread1 >= 0) {
				$extra = array(
					'trade_type' => 1,
					'extra' => $order['id'],
					'remark' => "订单号:{$order['id']}, 一级下线佣金费率:{$commission['spread1_rate']}, 佣金:{$fee_spread1}元",
				);
				spread_update_credit2($order['spread1'], $fee_spread1, $extra);
			}
			$fee_spread2 = $commission['spread2'];
			if($fee_spread2 >= 0) {
				$extra = array(
					'trade_type' => 1,
					'extra' => $order['id'],
					'remark' => "订单号:{$order['id']}, 二级下线佣金费率:{$commission['spread2_rate']}, 佣金:{$fee_spread2}元",
				);
				spread_update_credit2($order['spread2'], $fee_spread2, $extra);
			}
		}
		pdo_update('tiny_wmall_order', array('spreadbalance' => 1), array('id' => $order['id']));
	}
	return true;
}

function spread_trade_type($status, $key = 'all') {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '推广佣金入账',
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '申请提现',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '其他变动',
		),
	);
	if($key == 'all') {
		return $data[$status];
	} elseif($key == 'text') {
		return $data[$status]['text'];
	} elseif($key == 'css') {
		return $data[$status]['css'];
	}
}

//获取用户的上线
function member_spread($uid = 0) {
	global $_W;
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('spread1', 'spread2'));
	if(empty($member['spread1'])) {
		return false;
	}
	$uids = implode(',', array($member['spread1'], $member['spread2']));
	$fields = implode(',', array('uid', 'nickname', 'mobile', 'realname', 'openid', 'avatar'));
	$members = pdo_fetchall("select {$fields} from " . tablename('tiny_wmall_members') . " where uniacid = :uniacid and uid in ({$uids})", array(':uniacid' => $_W['uniacid']), 'uid');
	$data = array(
		'spread1' => $members[$member['spread1']],
		'spread2' => $members[$member['spread2']],
	);
	return $data;
}

//绑定推广员
function member_spread_bind() {
	global $_W, $_GPC;
	$config_spread = get_plugin_config('spread');
	$config_spread_basic = $config_spread['basic'];
	if(empty($config_spread_basic['level'])) {
		return error(-1, '未开启推广层级');
	}
	if(empty($_W['member']['is_mall_newmember'])) {
		return error(-1, '您已经是平台老用户,不能参与推广活动');
	}
	if(!empty($_W['member']['spreadfixed'])) {
		return error(-1, '您的推广员已绑定,不能变更推广员');
	}
	$invite_uid = intval($_GPC['code']);
	if(!empty($invite_uid)) {
		if($invite_uid == $_W['member']['uid']) {
			return error(-1, '自己不能成为自己的推广员');
		}
		$spread = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $invite_uid, 'is_spread' => 1), array('uid', 'nickname', 'spread1', 'spread2'));
		if(empty($spread)) {
			return error(-1, '推广员不存在');
		}

		$config_spread_relate = $config_spread['relate'];
		$update = array(
			'spread1' => $spread['uid'],
			'spread2' => $spread['spread1'],
			'spreadfixed' => 0,
		);
		if($config_spread_relate['become_child'] == 0) {
			//首次点击分享链接
			$update['spreadfixed'] = 1;
		}
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		if($update['spreadfixed'] == 1) {
			sys_notice_spread_down($_W['member']['uid'], 'new_down', array('channel' => 'qrcode'));
		} else {
			sys_notice_spread_down($_W['member']['uid'], 'pseudo_down', array('channel' => 'qrcode'));
		}
		return $spread;
	}
	return error(-1, '推广员不存在');
}

function member_spread_confirm() {
	global $_W;
	$config_spread = get_plugin_config('spread');
	$config_spread_basic = $config_spread['basic'];
	if(empty($config_spread_basic['level'])) {
		return true;
	}
	if(!empty($_W['member']['spreadfixed'])) {
		return true;
	}
	if(empty($_W['member']['spread1'])) {
		return true;
	}
	$order = pdo_fetch('select a.id, b.oid from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_order_comment') . ' as b on a.id = b.oid where a.uniacid = :uniacid and a.uid = :uid and a.status = 5 order by a.id asc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(empty($order)) {
		return true;
	}
	$config_spread_relate = $config_spread['relate'];
	$update = array(
		'spreadfixed' => 0,
	);
	if($config_spread_relate['become_child'] == 1) {
		//首次下单并确认收货
		$update['spreadfixed'] = 1;
		$channel = 'order_end';
	} elseif($config_spread_relate['become_child'] == 2) {
		//首次下单确认收货并进行评价
		if(!empty($order['oid'])) {
			$update['spreadfixed'] = 1;
			$channel = 'order_comment';
		}
	}
	if($update['spreadfixed'] == 1) {
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		sys_notice_spread_down($_W['member']['uid'], 'new_down', array('channel' => $channel));
	}
	return true;
}




