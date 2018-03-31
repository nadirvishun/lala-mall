<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op'])? trim($_GPC['op']): 'list';

if($op == 'list') {
	$_W['page']['title'] = '超级红包列表';
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':type' => 'superRedpacket'
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) FROM ' . tablename('tiny_wmall_superredpacket') .  $condition, $params);
	$superRedpackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_superredpacket') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($superRedpackets as &$row) {
		$row['grant_object'] = iunserializer($row['grant_object']);
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('grantList');
}

if($op == 'post') {
	$_W['page']['title'] = '超级红包设置';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$data = $_GPC['data'];
		if($data['customer']['type'] == 1) {
			$data['customer']['uid'] = str_replace('，', ',', $data['customer']['uid']);
			$uid = array_filter(explode(',', $data['customer']['uid']));
		} else {
			$condition = ' where uniacid = :uniacid';
			$params = array(
				':uniacid' => $_W['uniacid']
			);
			if($data['customer']['type'] > 1) {
				$time = strtotime('-30 days');
				if($data['customer']['type'] == 2) {
					$condition .= ' and success_last_time >= :time';
				} elseif($data['customer']['type'] == 3) {
					$condition .= ' and success_last_time < :time';
				} elseif($data['customer']['type'] == 4) {
					$condition .= ' and cancel_last_time >= :time';
				}
				$params[':time'] = $time;
			}
			$uid = pdo_fetchall('select uid from ' . tablename('tiny_wmall_members') . $condition, $params, 'uid');
			$uid = array_keys($uid);
		}
		if(empty($uid)) {
			imessage(error(-1, '发放对象为空'), referer(), 'ajax');
		}
		$grant_object = array(
			'total' => count($uid),
			'grant_success' => 0,
			'grant_uid' => $uid,
			'unissued_uid' => $uid
		);
		$menudata = array(
			'uniacid' => $_W['uniacid'],
			'name' => $data['name'],
			'type' => 'superRedpacket',
			'data' => base64_encode(json_encode($data)),
			'grant_object' => iserializer($grant_object)
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_superredpacket', $menudata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$menudata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_superredpacket', $menudata);
			$id = pdo_insertid();
		}
		imessage(error(0, '超级红包设置成功'), iurl('superRedpacket/grant/send', array('id' => $id)), 'ajax');
	}
	if(!empty($id)) {
		$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if(!empty($superRedpacket)) {
			$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
			$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
		}
	}
	include itemplate('grantPost');
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除成功'), referer(), 'ajax');
}

if($op == 'send') {
	$_W['page']['title'] = '发送超级红包';
	$id = intval($_GPC['id']);
	$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(empty($superRedpacket)) {
		imessage('该红包不存在或已经删除', referer(), 'error');
	}
	$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
	$superRedpacket['grant_object']['unissued_uid'] = array_values($superRedpacket['grant_object']['unissued_uid']);
	if($_W['ispost']) {
		$uid = $_GPC['__input']['uid'];
		mload()->model('redPacket');
		mload()->model('member');
		$discount = 0;
		$num = 0;
		foreach($superRedpacket['data']['redpackets'] as $redpacket) {
			if(empty($redpacket['times'])) {
				$redpacket['times'] = array();
			}
			$params = array(
				'title' => $redpacket['name'],
				'activity_id' => $superRedpacket['id'],
				'uid' => $uid,
				'channel' => 'superRedpacket',
				'type' => 'grant',
				'discount' => $redpacket['discount'],
				'condition' => $redpacket['condition'],
				'grant_days_effect' => $redpacket['grant_days_effect'],
				'days_limit' =>  $redpacket['use_days_limit'],
				'is_show' => 0
			);
			$times_limit = array();
			if(!empty($redpacket['times'])) {
				foreach($redpacket['times'] as $time) {
					if($time['start_hour'] && $time['end_hour']) {
						$times_limit[] = $time;
					}
				}
			}
			if(!empty($times_limit)) {
				$params['times_limit'] = iserializer($times_limit);
			}
			$category_limit = array();
			if(!empty($redpacket['categorys'])) {
				foreach($redpacket['categorys'] as $category) {
					$category_limit[] = $category['id'];
				}
			}
			$params['category_limit'] = implode('|', $category_limit);
			redPacket_grant($params, false);
			$discount += $params['discount'];
			$num++;
		}
		if($superRedpacket['data']['customer']['template_notice'] == 1) {
			$openid = member_uid2openid($uid);
			if(!empty($openid)) {
				$config = $_W['we7_wmall']['config'];
				$params = array(
					'first' => "您在{$config['mall']['title']}的账户有{$num}个代金券到账",
					'keyword1' => date('Y-m-d H:i', TIMESTAMP),
					'keyword2' => "代金券到账",
					'keyword3' => "{$discount}元",
					'remark' => implode("\n", array(
						"感谢您对{$config['mall']['title']}平台的支持与厚爱。点击查看红包>>"
					))
				);
				$send = sys_wechat_tpl_format($params);
				$acc = WeAccount::create($_W['acid']);
				$url = imurl('wmall/home/index', array(), true);
				$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send, $url);
				if(is_error($status)) {
					slog('wxtplNotice', '发放平台红包微信通知顾客', $send, $status['message']);
				}
			}
		}
		$key = array_search($uid, $superRedpacket['grant_object']['unissued_uid']);
		unset($superRedpacket['grant_object']['unissued_uid'][$key]);
		$superRedpacket['grant_object']['unissued_uid'] = array_values($superRedpacket['grant_object']['unissued_uid']);
		$superRedpacket['grant_object']['grant_success']++;
		pdo_update('tiny_wmall_superredpacket', array('grant_object' => iserializer($superRedpacket['grant_object'])), array('id' => $id, 'uniacid' => $_W['uniacid']));
		imessage(error(0, $superRedpacket['grant_object']), '', 'ajax');
	}
	include itemplate('grantSend');
}