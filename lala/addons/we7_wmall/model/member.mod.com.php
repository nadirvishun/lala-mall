<?php
//微擎应用 http://www.we7.cc   
function icheckauth()
{
	global $_W;
	global $_GPC;
	load()->model('mc');
	$_W['member'] = array();

	if (is_weixin()) {
		if (!empty($_W['openid'])) {
			$member = get_member($_W['openid']);

			if (empty($member)) {
				$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid']));

				if (!empty($fans['uid'])) {
					$mc = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('uid', 'realname', 'mobile', 'gender', 'credit1', 'credit2'));
				}

				$fansInfo = mc_oauth_userinfo();

				if (empty($mc)) {
					$member = array('uniacid' => $_W['uniacid'], 'uid' => date('His') . random(3, true), 'openid' => $fansInfo['openid'], 'nickname' => $fansInfo['nickname'], 'realname' => $fansInfo['nickname'], 'sex' => $fansInfo['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($fansInfo['headimgurl'], '0'), 132) . 132, 'is_sys' => 2, 'status' => 1, 'token' => random(32), 'addtime' => TIMESTAMP);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = 0;
					$member['credit2'] = 0;
				}
				else {
					$member = array('uniacid' => $_W['uniacid'], 'uid' => $mc['uid'], 'openid' => $_W['openid'], 'nickname' => $fans['nickname'], 'realname' => $mc['realname'], 'mobile' => $mc['mobile'], 'sex' => $fansInfo['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($fansInfo['headimgurl'], '0'), 132) . 132, 'is_sys' => 1, 'status' => 1, 'token' => random(32), 'addtime' => TIMESTAMP);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = $mc['credit1'];
					$member['credit2'] = $mc['credit2'];
				}
			}

			$_W['member'] = $member;
		}
	}
	else {
		if ($_W['ispost']) {
			imessage(error(-1, '请在微信中访问'), imurl('wmall/home/index'), 'ajax');
		}

		imessage('请在微信中访问', imurl('wmall/home/index'), 'info');
	}

	if (empty($_W['openid'])) {
		$_W['openid'] = $_W['member']['openid'];
	}

	if (0 < $_W['member']['uid']) {
		$_W['member']['is_store_newmember'] = 1;
		$_W['member']['is_mall_newmember'] = 1;

		if (0 < $_GPC['sid']) {
			$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => intval($_GPC['sid']), 'uid' => $_W['member']['uid']), array('id'));

			if (!empty($is_exist)) {
				$_W['member']['is_store_newmember'] = 0;
				$_W['member']['is_mall_newmember'] = 0;
			}

			if ($_W['member']['is_mall_newmember'] == 1) {
				$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), array('id'));

				if (!empty($is_exist)) {
					$_W['member']['is_mall_newmember'] = 0;
				}
			}
		}

		if (!$_W['member']['status']) {
			imessage('您暂时无权访问平台,', 'close', 'info');
		}

		return true;
	}

	$forward = base64_encode($_SERVER['QUERY_STRING']);

	if ($_W['ispost']) {
		imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
	}

	header('location: ' . imurl('wmall/auth/login', array('forward' => $forward)), true);
	exit();
}

function get_member($openid)
{
	global $_W;
	$uid = intval($openid);
	$fields = array('id', 'uid', 'openid', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname', 'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime');

	if ($uid == 0) {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'openid' => $openid), $fields);

		if (empty($info)) {
			if (strexists($openid, 'sns_qq_')) {
				$openid = str_replace('sns_qq_', '', $openid);
				$condition = ' openid_qq = :openid';
			}
			else {
				if (strexists($openid, 'sns_wx_')) {
					$openid = str_replace('sns_wx_', '', $openid);
					$condition = ' openid_wx = :openid';
				}
			}

			if (!empty($condition)) {
				$info = pdo_fetch('select * from ' . tablename('tiny_wmall_members') . ' where uniacid=:uniacid and ' . $condition, array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
		}
	}
	else {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $openid), $fields);
	}

	if (!empty($info)) {
		if (empty($info['token'])) {
			$info['token'] = random(32);
			pdo_update('tiny_wmall_members', array('token' => $info['token']), array('id' => $info['id']));
		}

		$openid = $info['openid'];
		if (!empty($openid) && ($info['is_sys'] == 2)) {
			$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $openid));

			if (!empty($fans['uid'])) {
				$is_exist = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('credit1', 'credit2'));

				if (!empty($is_exist)) {
					$upgrade = array('uid' => $fans['uid'], 'is_sys' => 1);
					load()->model('mc');

					if (0 < $info['credit1']) {
						mc_credit_update($fans['uid'], 'credit1', $info['credit1']);
						$upgrade['credit1'] = 0;
					}

					if (0 < $info['credit2']) {
						mc_credit_update($fans['uid'], 'credit2', $info['credit2']);
						$upgrade['credit2'] = 0;
					}

					pdo_update('tiny_wmall_members', $upgrade, array('id' => $info['id']));

					if ($info['uid'] != $fans['uid']) {
						$tables = array('tiny_wmall_activity_coupon_grant_log', 'tiny_wmall_activity_coupon_record', 'tiny_wmall_address', 'tiny_wmall_order', 'tiny_wmall_order_comment');

						foreach ($tables as $table) {
							pdo_update($table, array('uid' => $fans['uid']), array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']));
						}
					}

					$info['uid'] = $fans['uid'];
					$info['is_sys'] = 1;
				}
			}
		}

		if ($info['is_sys'] == 1) {
			$member = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']), array('credit1', 'credit2'));

			if (empty($member)) {
				pdo_update('tiny_wmall_members', array('is_sys' => 2), array('id' => $info['id']));
			}
			else {
				$info['credit1'] = $member['credit1'];
				$info['credit2'] = $member['credit2'];
			}
		}
	}

	return $info;
}

function member_register($params)
{
	global $_W;

	if (empty($params['openid'])) {
		return error(-1, '微信信息错误');
	}

	$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $params['openid']));

	if (!empty($fans['uid'])) {
		$mc = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('uid', 'realname', 'mobile', 'gender', 'credit1', 'credit2'));
	}

	if (empty($mc)) {
		$member = array('uniacid' => $_W['uniacid'], 'uid' => date('His') . random(3, true), 'openid' => $params['openid'], 'mobile' => $params['mobile'], 'nickname' => $params['nickname'], 'realname' => $params['nickname'], 'sex' => $params['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132, 'is_sys' => 2, 'status' => 1, 'token' => random(32), 'addtime' => TIMESTAMP);
		pdo_insert('tiny_wmall_members', $member);
	}
	else {
		$member = array('uniacid' => $_W['uniacid'], 'uid' => $mc['uid'], 'openid' => $params['openid'], 'nickname' => $params['nickname'], 'realname' => $mc['realname'], 'mobile' => $params['mobile'] ? $params['mobile'] : $mc['mobile'], 'sex' => $params['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132, 'is_sys' => 1, 'status' => 1, 'token' => random(32), 'addtime' => TIMESTAMP);
		pdo_insert('tiny_wmall_members', $member);
	}

	return $member;
}

function member_uid2token($uid = 0)
{
	global $_W;

	if (empty($uid)) {
		$uid = $_W['member']['uid'];
	}

	$token = pdo_fetchcolumn('select token from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $token;
}

function member_uid2openid($uid = 0)
{
	global $_W;

	if (empty($uid)) {
		$uid = $_W['member']['uid'];
	}

	$openid = pdo_fetchcolumn('select openid from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $openid;
}

function member_credit_update($uid, $credittype, $creditval = 0, $log = array(), $wxtpl_notice = true)
{
	global $_W;
	$member = get_member($uid);

	if (empty($member)) {
		return error(-1, '会员不存在');
	}

	if (!in_array($credittype, array('credit1', 'credit2'))) {
		return error('-1', '积分类型有误');
	}

	$credittype = trim($credittype);
	$creditval = floatval($creditval);

	if (empty($creditval)) {
		return true;
	}

	if ($member['is_sys'] == 1) {
		load()->model('mc');
		$result = mc_credit_update($uid, $credittype, $creditval, $log);
	}
	else {
		$value = $member[$credittype];
		if ((0 < $creditval) || (0 <= $value + $creditval)) {
			pdo_update('tiny_wmall_members', array($credittype => $value + $creditval), array('uid' => $uid));
			$result = true;
		}
		else {
			return error('-1', '积分类型为“' . $credittype . '”的积分不够，无法操作。');
		}
	}

	if (!empty($wxtpl_notice)) {
		$openid = member_uid2openid($uid);

		if (empty($openid)) {
			return true;
		}

		$member = get_member($uid);
		$config = $_W['we7_wmall']['config'];

		if ($credittype == 'credit1') {
			$params = array('first' => '您在' . $config['mall']['title'] . '的账户积分有新的变动', 'keyword1' => date('Y-m-d H:i', TIMESTAMP), 'keyword2' => 0 < $creditval ? '积分充值' : '积分消费', 'keyword3' => $creditval . '积分', 'remark' => implode("\n", array('积分余额:' . $member['credit1'])));
		}
		else {
			$params = array('first' => '您在' . $config['mall']['title'] . '的账户余额有新的变动', 'keyword1' => date('Y-m-d H:i', TIMESTAMP), 'keyword2' => 0 < $creditval ? '余额充值' : '余额消费', 'keyword3' => $creditval . '元', 'remark' => implode("\n", array('账户余额:' . $member['credit2'])));
		}

		$send = sys_wechat_tpl_format($params);
		$acc = WeAccount::create($_W['acid']);
		$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send);
	}

	return $result;
}

function member_oauth_info($url, $account = array())
{
	global $_W;
	global $_GPC;
	mload()->classs('wxaccount');

	if (empty($account)) {
		$account = $_W['acid'];
	}

	$acc = new WxAccount($account);

	if (is_error($acc)) {
		return $acc;
	}

	$code = trim($_GPC['code']);

	if (empty($code)) {
		$data = $acc->getOauthCodeUrl($url);

		if (is_error($data)) {
			return $data;
		}

		header('Location: ' . $data);
		exit();
		return NULL;
	}

	$data = $acc->getOauthInfo($code);
	return $data;
}

function member_recharge_status_update($order_id, $type, $params)
{
	global $_W;
	$order = pdo_get('tiny_wmall_member_recharge', array('uniacid' => $_W['uniacid'], 'id' => $order_id));

	if (empty($order)) {
		return error(-1, '充值订单不存在');
	}

	if ($type == 'pay') {
		$update = array('is_pay' => 1, 'pay_type' => $params['type'], 'paytime' => TIMESTAMP);
		pdo_update('tiny_wmall_member_recharge', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
		$tag = iunserializer($order['tag']);

		if (0 < $tag['credit2']) {
			$log = array($order['uid'], '用户充值' . $tag['credit2'] . '元');
			member_credit_update($order['uid'], 'credit2', $tag['credit2'], $log);
		}

		if (!empty($tag['grant'])) {
			$array = array('credit1' => '积分', 'credit2' => '元');
			$log = array($order['uid'], '用户充值' . $tag['credit2'] . '元赠送' . $tag['grant']['num'] . $array[$tag['grant']['type']]);
			member_credit_update($order['uid'], $tag['grant']['type'], $tag['grant']['back'], $log);
		}

		return true;
	}

	return true;
}

function member_fetchall_address($filter = array())
{
	global $_W;
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 1 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if (!empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();

		foreach ($data as $li) {
			if (!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if (!empty($filter['serve_radius']) && (($filter['serve_radius'] * 1000) < $dist)) {
					$dis_available[] = $li;
				}
				else {
					$available[] = $li;
				}
			}
			else {
				$dis_available[] = $li;
			}
		}

		return array('available' => $available, 'dis_available' => $dis_available);
	}

	return $data;
}

function member_fetch_address($id)
{
	global $_W;
	$data = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_fetch_available_address($sid)
{
	global $_W;
	global $_GPC;
	$store = store_fetch($sid);
	$address = array();

	if (!$_GPC['r']) {
		if (0 < $_GPC['__aid']) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => intval($_GPC['__aid'])));

			if (1 < $store['order_address_limit']) {
				if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
					$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
					if ((($store['order_address_limit'] == 2) && ($dist <= $store['serve_radius'] * 1000)) || ($store['order_address_limit'] == 3)) {
						$temp['distance'] = $dist / 1000;
						$address = $temp;
					}
				}
			}
			else {
				$address = $temp;
			}
		}

		if (empty($address)) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'type' => 1, 'is_default' => 1));

			if (1 < $store['order_address_limit']) {
				if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
					$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
					if ((($store['order_address_limit'] == 2) && ($dist <= $store['serve_radius'] * 1000)) || ($store['order_address_limit'] == 3)) {
						$temp['distance'] = $dist / 1000;
						$address = $temp;
					}
				}
			}
			else {
				$address = $temp;
			}
		}

		if (empty($address)) {
			$addresses = member_fetchall_address();

			foreach ($addresses as $li) {
				if (1 < $store['order_address_limit']) {
					if (!empty($li['location_x']) && !empty($li['location_y'])) {
						$dist = distanceBetween($li['location_y'], $li['location_x'], $store['location_y'], $store['location_x']);
						if ((($store['order_address_limit'] == 2) && ($dist <= $store['serve_radius'] * 1000)) || ($store['order_address_limit'] == 3)) {
							$li['distance'] = $dist / 1000;
							$address = $li;
							break;
						}
					}
				}
				else {
					$address = $li;
					break;
				}
			}
		}
	}
	else {
		$address_id = intval($_GPC['address_id']);
		$temp = member_fetch_address($address_id);

		if (1 < $store['order_address_limit']) {
			if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
				$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
				if ((($store['order_address_limit'] == 2) && ($dist <= $store['serve_radius'] * 1000)) || ($store['order_address_limit'] == 3)) {
					$temp['distance'] = $dist / 1000;
					$address = $temp;
				}
			}
		}
		else {
			$address = $temp;
		}
	}

	return $address;
}

function member_amount_stat($sid)
{
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

function member_fetch($uid = 0)
{
	global $_W;

	if (!$uid) {
		$uid = $_W['member']['uid'];
	}

	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid));

	if (!empty($member)) {
		$member['search_data'] = iunserializer($member['search_data']);

		if (!is_array($member['search_data'])) {
			$member['search_data'] = array();
		}
	}

	return $member;
}

function member_fetchall_serve_address($filter = array())
{
	global $_W;
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 2 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if (!empty($filter['serve_radius']) && !empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();

		foreach ($data as $li) {
			if (!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);

				if (($filter['serve_radius'] * 1000) < $dist) {
					$dis_available[] = $li;
				}
				else {
					$available[] = $li;
				}
			}
			else {
				$dis_available[] = $li;
			}
		}

		return array('available' => $available, 'dis_available' => $dis_available);
	}

	return $data;
}

function member_fetch_serve_address($id)
{
	global $_W;
	$data = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 2', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_plateform_amount_stat()
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and success_first_time >= :starttime and success_first_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	return $stat;
}

defined('IN_IA') || exit('Access Denied');

?>
