<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function icheckauth($force = true) {
	global $_W, $_GPC;
	load()->model('mc');
	$_W['member'] = array();
	if(is_weixin()) {
		if(!empty($_W['openid'])) {
			$member = get_member($_W['openid']);
			if(empty($member) && $force) {
				$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid']));
				if(!empty($fans['uid'])) {
					$mc = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('uid', 'realname', 'mobile', 'gender', 'credit1', 'credit2'));
				}
				$fansInfo = mc_oauth_userinfo();
				if(empty($mc)) {
					$member = array(
						'uniacid' => $_W['uniacid'],
						'uid' => date('His') . random(3, true),
						'openid' => $fansInfo['openid'],
						'nickname' => $fansInfo['nickname'],
						'realname' => $fansInfo['nickname'],
						'sex' => ($fansInfo['sex'] == 1 ? '男' : '女'),
						'avatar' => rtrim(rtrim($fansInfo['headimgurl'], '0'), 132) . 132,
						'is_sys' => 2, //模拟用户
						'status' => 1,
						'token' => random(32),
						'addtime' => TIMESTAMP,
					);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = 0;
					$member['credit2'] = 0;
				} else {
					$member = array(
						'uniacid' => $_W['uniacid'],
						'uid' => $mc['uid'],
						'openid' => $_W['openid'],
						'nickname' => $fans['nickname'],
						'realname' => $mc['realname'],
						'mobile' => $mc['mobile'],
						'sex' => ($fansInfo['sex'] == 1 ? '男' : '女'),
						'avatar' => rtrim(rtrim($fansInfo['headimgurl'], '0'), 132) . 132,
						'is_sys' => 1,
						'status' => 1,
						'token' => random(32),
						'addtime' => TIMESTAMP,
					);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = $mc['credit1'];
					$member['credit2'] = $mc['credit2'];
				}
			}
			$_W['member'] = $member;
		}
	} else {
		if($force) {
			if($_W['ispost']) {
				imessage(error(-1, '请在微信中访问'), imurl('wmall/home/index'), 'ajax');
			}
			imessage('请在微信中访问', imurl('wmall/home/index'), 'info');
		}
	}
	if(empty($_W['openid'])) {
		$_W['openid'] = $_W['member']['openid'];
	}

	if($_W['member']['uid'] > 0) {
		member_group_update();
		$_W['member']['is_store_newmember'] = 1;
		$_W['member']['is_mall_newmember'] = 1;
		if($_GPC['sid'] > 0) {
			$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => intval($_GPC['sid']), 'uid' => $_W['member']['uid']), array('id'));
			if(!empty($is_exist)) {
				$_W['member']['is_store_newmember'] = 0;
				$_W['member']['is_mall_newmember'] = 0;
			}
			if($_W['member']['is_mall_newmember'] == 1) {
				$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), array('id'));
				if(!empty($is_exist)) {
					$_W['member']['is_mall_newmember'] = 0;
				}
			}
		}
		if(!$_W['member']['status'] && $force) {
			imessage('您暂时无权访问平台,', 'close', 'info');
		}
		return true;
	}
	if($force) {
		$forward = base64_encode($_SERVER['QUERY_STRING']);
		if(is_qianfan()) {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			include itemplate('auth/qianfan');
			exit();
		} elseif(is_majia()) {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			include itemplate('auth/majia');
			exit();
		} else {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			header("location: " . imurl('wmall/auth/login', array('forward' => $forward)), true);
		}
		exit;
	}
}

function get_member($openid) {
	global $_W;
	$uid = intval($openid);
	$fields = array('id', 'uid', 'groupid', 'groupid_updatetime', 'uid_majia', 'uid_qianfan', 'openid', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname', 'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime', 'spread1', 'spread2', 'spreadcredit2', 'spreadfixed');
	if($uid == 0) {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'openid' => $openid), $fields);
		if(empty($info)) {
			if(strexists($openid, 'sns_qq_')) {
				$openid = str_replace('sns_qq_', '', $openid);
				$condition = ' openid_qq = :openid';
			} elseif (strexists($openid, 'sns_wx_')) {
				$openid = str_replace('sns_wx_', '', $openid);
				$condition = ' openid_wx = :openid';
			}
			if(!empty($condition)) {
				$info = pdo_fetch('select * from ' . tablename('tiny_wmall_members') . " where uniacid=:uniacid and {$condition}", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
		}
	} else {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $openid), $fields);
	}
	if(!empty($info)) {
		$groups = member_groups();
		$info['groupname'] = $groups[$info['groupid']]['title'];
		if(empty($info['token'])) {
			$info['token'] = random(32);
			pdo_update('tiny_wmall_members', array('token' => $info['token']), array('id' => $info['id']));
		}
		$openid = $info['openid'];
		if(!empty($openid) && $info['is_sys'] == 2) {
			$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $openid));
			if(!empty($fans['uid'])) {
				$is_exist = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('credit1', 'credit2'));
				if(!empty($is_exist)) {
					$upgrade = array('uid' => $fans['uid'], 'is_sys' => 1);
					load()->model('mc');
					if($info['credit1'] > 0) {
						mc_credit_update($fans['uid'], 'credit1', $info['credit1']);
						$upgrade['credit1'] = 0;
					}
					if($info['credit2'] > 0) {
						mc_credit_update($fans['uid'], 'credit2', $info['credit2']);
						$upgrade['credit2'] = 0;
					}
					pdo_update('tiny_wmall_members', $upgrade, array('id' => $info['id']));
					if($info['uid'] != $fans['uid']) {
						$tables = array(
							'tiny_wmall_activity_coupon_grant_log',
							'tiny_wmall_activity_coupon_record',
							'tiny_wmall_address',
							'tiny_wmall_order',
							'tiny_wmall_order_comment',
						);
						foreach($tables as $table) {
							pdo_update($table, array('uid' => $fans['uid']), array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']));
						}
					}
					$info['uid'] = $fans['uid'];
					$info['is_sys'] = 1;
				}
			}
		}
		if($info['is_sys'] == 1) {
			$member = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']), array('credit1', 'credit2'));
			if(empty($member)) {
				pdo_update('tiny_wmall_members', array('is_sys' => 2), array('id' => $info['id']));
			} else {
				$info['credit1'] = $member['credit1'];
				$info['credit2'] = $member['credit2'];
			}
		}
	}
	return $info;
}

function member_register($params) {
	global $_W;
	if(empty($params['openid'])) {
		return error(-1, '微信信息错误');
	}
	$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $params['openid']));
	if(!empty($fans['uid'])) {
		$mc = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('uid', 'realname', 'mobile', 'gender', 'credit1', 'credit2'));
	}
	if(empty($mc)) {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => date('His') . random(3, true),
			'openid' => $params['openid'],
			'mobile' => $params['mobile'],
			'nickname' => $params['nickname'],
			'realname' => $params['nickname'],
			'sex' => ($params['sex'] == 1 ? '男' : '女'),
			'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132,
			'is_sys' => 2, //模拟用户
			'status' => 1,
			'token' => random(32),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_members', $member);
	} else {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $mc['uid'],
			'openid' => $params['openid'],
			'nickname' => $params['nickname'],
			'realname' => $mc['realname'],
			'mobile' => $params['mobile'] ? $params['mobile'] : $mc['mobile'],
			'sex' => ($params['sex'] == 1 ? '男' : '女'),
			'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132,
			'is_sys' => 1,
			'status' => 1,
			'token' => random(32),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_members', $member);
	}
	return $member;
}

function member_uid2token($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$token = pdo_fetchcolumn('select token from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $token;
}

function member_uid2openid($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$openid = pdo_fetchcolumn('select openid from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $openid;
}

function member_credit_update($uid, $credittype, $creditval = 0, $log = array(), $wxtpl_notice = true) {
	global $_W;
	$fields = array('id', 'uid', 'groupid', 'groupid_updatetime', 'uid_majia', 'uid_qianfan', 'openid', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname', 'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime', 'spread1', 'spread2');
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), $fields);
	if(empty($member)) {
		return error(-1, '会员不存在');
	}
	if(!in_array($credittype, array('credit1', 'credit2'))) {
		return error('-1', "积分类型有误");
	}
	$credittype = trim($credittype);
	$creditval = floatval($creditval);
	if(empty($creditval)) {
		return true;
	}
	if($member['is_sys'] == 1) {
		load()->model('mc');
		$result = mc_credit_update($uid, $credittype, $creditval, $log);
	} else {
		$value = $member[$credittype];
		if($creditval > 0 || ($value + $creditval >= 0)) {
			pdo_update('tiny_wmall_members', array($credittype => $value + $creditval), array('uid' => $uid));
			$result = true;
		} else {
			return error('-1', "积分类型为“{$credittype}”的积分不够，无法操作。");
		}
	}

	if(!empty($wxtpl_notice)) {
		$openid = member_uid2openid($uid);
		if(empty($openid)) {
			return true;
		}
		$member = get_member($uid);
		$config = $_W['we7_wmall']['config'];
		if($credittype == 'credit1') {
			$params = array(
				'first' => "您在{$config['mall']['title']}的账户积分有新的变动",
				'keyword1' => date('Y-m-d H:i', TIMESTAMP),
				'keyword2' => $creditval > 0 ? '积分充值' : '积分消费',
				'keyword3' => "{$creditval}积分",
				'remark' => implode("\n", array(
					"积分余额:{$member['credit1']}",
					"备注: {$log[1]}"
				))
			);
		} else {
			$params = array(
				'first' => "您在{$config['mall']['title']}的账户余额有新的变动",
				'keyword1' => date('Y-m-d H:i', TIMESTAMP),
				'keyword2' => $creditval > 0 ? '余额充值' : '余额消费',
				'keyword3' => "{$creditval}元",
				'remark' => implode("\n", array(
					"账户余额:{$member['credit2']}" ,
					"备注: {$log[1]}"
				))
			);
		}
		$send = sys_wechat_tpl_format($params);
		$acc = WeAccount::create($_W['acid']);
		$url = imurl('wmall/member/mine', array(), true);
		if(!is_error($acc)) {
			$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send, $url);
			if(is_error($status)) {
				slog('wxtplNotice', '平台账户变动微信通知会员', $send, $status['message']);
			}
		}
	}
	return $result;
}

function member_oauth_info($url, $account, $openid = '') {
	global $_W, $_GPC;
	if(empty($openid))  {
		$openid = $_W['openid'];
	}
	$oauth = pdo_get('tiny_wmall_oauth_fans', array('appid' => $account['appid'], 'openid' => $openid));
	if(!empty($oauth)) {
		$oauth = array(
			'appid' => $account['appid'],
			'openid' => $oauth['oauth_openid']
		);
		return $oauth;
	}
	mload()->classs('wxaccount');
	$acc = new WxAccount($account);
	if(is_error($acc)) {
		return $acc;
	}
	$code = trim($_GPC['code']);
	if(empty($code)) {
		$state = 'we7sid-'.$_W['session_id'];
		$data = $acc->getOauthCodeUrl($url, $state);
		if(is_error($data)) {
			return $data;
		}
		header('Location: ' . $data);
		die;
	} else {
		$data = $acc->getOauthInfo($code);
		if(!is_error($data)) {
			$oauth_openid = $data['openid'];
			$is_exist = pdo_get('tiny_wmall_oauth_fans', array('appid' => $account['appid'], 'openid' => $openid, 'oauth_openid' => $oauth_openid));
			if(empty($is_exist)) {
				$insert = array(
					'appid' => $account['appid'],
					'openid' => $openid,
					'oauth_openid' => $oauth_openid,
				);
				pdo_insert('tiny_wmall_oauth_fans', $insert);
			}
		}
		return $data;
	}
}

function member_recharge_status_update($order_id, $type, $params) {
	global $_W;
	$order = pdo_get('tiny_wmall_member_recharge', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '充值订单不存在');
	}
	if($type == 'pay') {
		$update = array(
			'is_pay' => 1,
			'pay_type' => $params['type'],
			'paytime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_member_recharge', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
		$tag = iunserializer($order['tag']);
		if($tag['credit2'] > 0) {
			$log = array(
				$order['uid'],
				"用户充值{$tag['credit2']}元"
			);
			member_credit_update($order['uid'], 'credit2', $tag['credit2'], $log);
		}
		if(!empty($tag['grant'])) {
			$array = array(
				'credit1' => '积分',
				'credit2' => '元',
			);
			$log = array(
				$order['uid'],
				"用户充值{$tag['credit2']}元赠送{$tag['grant']['num']}{$array[$tag['grant']['type']]}"
			);
			member_credit_update($order['uid'], $tag['grant']['type'], $tag['grant']['back'], $log);
		}
		return true;
	}
	return true;
}

//member_fetchall_address
function member_fetchall_address($filter = array()) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 1 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach($data as $li) {
			if(!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if(!empty($filter['serve_radius']) && $dist > ($filter['serve_radius'] * 1000)) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return $data;
}

//member_fetch_address
function member_fetch_address($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_fetch_available_address($sid) {
	global $_W, $_GPC;
	$store = store_fetch($sid);
	$address = array();
	if(!$_GPC['r']) {
		$is_ok = 0;
		if($_GPC['__aid'] > 0) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => intval($_GPC['__aid'])));
			$is_ok = is_in_store_radius($store, array($temp['location_y'], $temp['location_x']));
		}
		if(empty($is_ok)) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'uid' => $_W['member']['uid'], 'type' => 1, 'is_default' => 1));
			$is_ok = is_in_store_radius($store, array($temp['location_y'], $temp['location_x']), $_GPC['_guess_area']);
		}
		if(empty($is_ok)) {
			$addresses = member_fetchall_address();
			foreach($addresses as $li) {
				$is_ok = is_in_store_radius($store, array($li['location_y'], $li['location_x']), $_GPC['_guess_area']);
				if(!empty($is_ok)) {
					$address = $li;
					break;
				}
			}
		} else {
			$address = $temp;
		}
	} else {
		$address_id = intval($_GPC['address_id']);
		$temp = member_fetch_address($address_id);
		$is_ok = is_in_store_radius($store, array($temp['location_y'], $temp['location_x']));
		if($is_ok) {
			$address = $temp;
		}
	}
	if(!empty($address)) {
		$dist = distanceBetween($address['location_y'], $address['location_x'], $store['location_y'], $store['location_x']);
		$address['distance'] = round($dist / 1000, 2);
	}
	return $address;
}

function member_amount_stat($sid) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime and success_first_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	return $stat;
}

function member_fetch($uid = 0) {
	global $_W;
	if(!$uid) {
		$uid = $_W['member']['uid'];
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	if(!empty($member)) {
		$member['search_data'] = iunserializer($member['search_data']);
		if(!is_array($member['search_data'])) {
			$member['search_data'] = array();
		}
	}
	return $member;
}

function member_fetchall_serve_address($filter = array()) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 2 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($filter['serve_radius']) && !empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach($data as $li) {
			if(!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if($dist > ($filter['serve_radius'] * 1000)) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return array('available' => $data);
}

function member_fetch_serve_address($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 2', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_plateform_amount_stat() {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	return $stat;
}

function tosetmeal($setmeal_id, $or = false) {
	global $_W;
	$data = pdo_fetch('select id, uniacid, title from' . tablename('tiny_wmall_delivery_cards') . ' where id = :id and uniacid = :uniacid', array(':id' => $setmeal_id, ':uniacid' => $_W['uniacid']));
	if($or) {
		return $data;
	} else {
		return $data['title'];
	}
}

function member_groups() {
	global $_W;
	$config_member = $_W['we7_wmall']['config']['member'];
	if(empty($config_member['group'])) {
		$groups = pdo_fetchall('select * from' . tablename('tiny_wmall_member_groups') . 'where uniacid = :uniacid order by group_condition asc', array(':uniacid' => $_W['uniacid']), 'id');
		$_W['we7_wmall']['config']['member']['group'] = $groups;
		pdo_update('tiny_wmall_config', array('sysset' => iserializer($_W['we7_wmall']['config'])), array('uniacid' => $_W['uniacid']));
	} else {
		$groups = $config_member['group'];
	}
	return $groups;
}

function member_group_update($wx_tpl = false) {
	global $_W;
	if($_W['member']['groupid_updatetime'] > TIMESTAMP - 600) {
		return true;
	}
	$condition = ' where uniacid = :uniacid and is_pay = 1 and uid = :uid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':uid' => $_W['member']['uid'],
	);
	$config_member = $_W['we7_wmall']['config']['member'];
	if($config_member['group_update_mode'] == 'order_money') {
		//外卖订单消费总额满
		$condition .= " and status = 5";
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config_member['group_update_mode'] == 'order_count') {
		//外卖订单消费次数满
		$condition .= " and status = 5";
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = intval($result);
	} elseif($config_member['group_update_mode'] == 'delivery_money') {
		//跑腿订单消费总额满
		$condition .= " and status = 3";
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_errander_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config_member['group_update_mode'] == 'delivery_count') {
		//跑腿订单消费次数满
		$condition .= " and status = 3";
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_errander_order') . $condition, $params);
		$result = intval($result);
	} elseif($config_member['group_update_mode'] == 'count_money') {
		//外卖订单和跑腿订单消费总额满
		$order = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition . " and status = 5", $params);
		$errander = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_errander_order') . $condition . " and status = 3", $params);
		$result = $order + $errander;
		$result = round($result, 2);
	}
	$old_group_id = $_W['member']['groupid'];
	$groups = member_groups();
	foreach($groups as $group) {
		if(($result >= $group['group_condition']) && ($group['group_condition'] > $groups[$old_group_id]['group_condition'])) {
			$group_id = $group['id'];
		}
	}
	pdo_update('tiny_wmall_members', array('groupid' => $group_id, 'groupid_updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	if($wx_tpl) {
		//微信模板消息
	}
	$_W['member']['groupid'] = $group_id;
	$_W['member']['groupname'] = $groups[$group_id]['title'];
	return true;
}



