<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function redPacket_cron() {
	global $_W;
	pdo_query("update " . tablename('tiny_wmall_activity_redpacket_record') . ' set status = 3 where uniacid = :uniacid and status = 1 and endtime < :time', array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	return true;
}

function redPacket_grant($params, $wxtpl_notice = true) {
	global $_W;
	if(empty($params['title'])) {
		return error(-1, '红包标题不能为空');
	}
	if(empty($params['channel'])) {
		return error(-1, '红包发放渠道不能为空');
	}
	if(empty($params['type'])) {
		return error(-1, '红包类型有误');
	}
	$params['discount'] = floatval($params['discount']);
	if(empty($params['discount'])) {
		return error(-1, '红包金额有误');
	}
	$params['days_limit'] = intval($params['days_limit']);
	if(empty($params['days_limit'])) {
		return error(-1, '红包有效期限有误');
	}
	$params['uid'] = intval($params['uid']);
	if(empty($params['uid'])) {
		return error(-1, '用户uid有误');
	}
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'title' => $params['title'],
		'activity_id' => $params['activity_id'],
		'uid' => $params['uid'],
		'channel' => $params['channel'],
		'type' => $params['type'],
		'code' => random(8, true),
		'discount' => $params['discount'],
		'condition' => $params['condition'],
		'starttime' => TIMESTAMP,
		'endtime' => TIMESTAMP + $params['days_limit'] * 86400,
		'category_limit' => $params['category_limit'],
		'times_limit' => $params['times_limit'],
		'status' => 1,
		'granttime' => TIMESTAMP,
	);
	if(!empty($params['super_share_id'])) {
		$insert['super_share_id'] = $params['super_share_id'];
	}
	if($params['grant_days_effect'] > 0) {
		$insert['starttime'] += $params['grant_days_effect'] * 86400;
		$insert['endtime'] += $params['grant_days_effect'] * 86400;
	}
	if(isset($params['is_show'])) {
		$insert['is_show'] = $params['is_show'];
	}
	pdo_insert('tiny_wmall_activity_redpacket_record', $insert);
	$uid = $params['uid'];

	if(!empty($wxtpl_notice)) {
		$openid = member_uid2openid($uid);
		if(empty($openid)) {
			return true;
		}
		$config = $_W['we7_wmall']['config'];

		$params = array(
			'first' => "您在{$config['mall']['title']}的账户有新的红包",
			'keyword1' => date('Y-m-d H:i', TIMESTAMP),
			'keyword2' => "红包到账",
			'keyword3' => "{$params['discount']}元",
			'remark' => implode("\n", array(
				"使用条件：满{$params['condition']}元可用" ,
				"截至日期：" . date('Y-m-d H:i', $insert['endtime']) ,
			))
		);
		$send = sys_wechat_tpl_format($params);
		$acc = WeAccount::create($_W['acid']);
		$url = imurl('wmall/member/redPacket', array(), true);
		$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '平台红包微信通知顾客', $send, $status['message']);
		}
	}
	return true;
}

function redpacket_channels() {
	$channel = array(
		'' => array(
			'text' => '未知',
			'css' => 'label-danger'
		),
		'shareRedpacket' => array(
			'text' => '分享红包',
			'css' => 'label-success'
		),
		'freeLunch' => array(
			'text' => '霸王餐',
			'css' => 'label-info'
		),
		'superRedpacket' => array(
			'text' => '超级红包',
			'css' => 'label-warning'
		),
	);
	return $channel;
}

function redpacket_status() {
	$status = array(
		'1' => array(
			'text' => '未使用',
			'css' => 'label-info'
		),
		'2' => array(
			'text' => '已使用',
			'css' => 'label-success'
		),
		'3' => array(
			'text' => '已过期',
			'css' => 'label-default'
		),
	);
	return $status;
}

//检测红包是否可使用
function redpacket_available_check($redpacketOrId, $price = 0, $category = array()) {
	global $_W;
	$redpacket = $redpacketOrId;
	if(!is_array($redpacketOrId)) {
		$redpacketOrId = intval($redpacketOrId);
		$redpacket = pdo_get('tiny_wmall_activity_redpacket_record', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'status' => 1, 'id' => $redpacketOrId));
	}
	if(empty($redpacket)) {
		return error(-1, '红包记录不存在');
	}
	if($redpacket['starttime'] > TIMESTAMP || $redpacket['endtime'] < TIMESTAMP) {
		return error(-1, '红包使用期限无效');
	}
	if($price < $redpacket['condition']) {
		return error(-1, '未达到红包使用金额');
	}
	$category = array_filter($category, trim);
	$redpacket['available_category'] = 1;
	if(!empty($redpacket['category_limit'])) {
		$redpacket['available_category'] = 0;
		$redpacket['category_limit'] = explode('|', $redpacket['category_limit']);
		if(!empty($category)) {
			foreach($category as $cid) {
				if(in_array($cid, $redpacket['category_limit'])) {
					$redpacket['available_category'] = 1;
					break;
				}
			}
		}
	}
	if(!$redpacket['available_category']) {
		return error(-1, '红包使用分类无效');
	}
	$redpacket['available_times'] = 1;
	if(!empty($redpacket['times_limit'])) {
		$redpacket['available_times'] = 0;
		$redpacket['times_limit'] = iunserializer($redpacket['times_limit']);
		if(!empty($redpacket['times_limit'])) {
			$now = date('Hi');
			foreach($redpacket['times_limit'] as $time) {
				$time['start_hour'] = str_replace(':', '', $time['start_hour']);
				$time['end_hour'] = str_replace(':', '',  $time['end_hour']);
				if($now >= $time['start_hour'] && $now <= $time['end_hour']) {
					$redpacket['available_times'] = 1;
					break;
				}
			}
		}
	}
	if(!$redpacket['available_times']) {
		return error(-1, '红包使用时间段无效');
	}
	return $redpacket;
}










