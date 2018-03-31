<?php
//微擎应用 http://www.we7.cc   
function ordergrant_grant($id)
{
	global $_W;
	$config_ordergrant = get_plugin_config('ordergrant');

	if ($config_ordergrant['status'] == 0) {
		return error(-1, '该活动未开启');
	}

	$grantType = '积分';

	if ($config_ordergrant['grantType'] == 'credit2') {
		$grantType = '元';
	}

	mload()->model('member');
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'status' => 5, 'id' => $id), array('uid', 'mall_first_order', 'stat_day'));
	$insert = array('uniacid' => $_W['uniacid'], 'uid' => $order['uid'], 'oid' => $id, 'days' => 0, 'credittype' => $config_ordergrant['grantType'], 'stat_month' => date('Ym'), 'addtime' => TIMESTAMP);
	$time1 = strtotime(date('Ymd'));
	$time2 = strtotime(date('Ymd')) + 86400;
	$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order_grant_record') . ' where uniacid = :uniacid and uid = :uid and addtime > :time1 and addtime < :time2 and type <= 1', array(':uniacid' => $_W['uniacid'], ':uid' => $order['uid'], ':time1' => $time1, ':time2' => $time2));

	if (empty($is_exist)) {
		if ($order['mall_first_order'] == 1) {
			$insert['grant'] = $config_ordergrant['first_order_grant'];
			$insert['type'] = 3;
			$insert['mark'] = '下单有礼首单返' . $config_ordergrant['first_order_grant'] . $grantType;
		}
		else {
			$insert['grant'] = $config_ordergrant['days_order_grant'];
			$insert['type'] = 0;
			$insert['mark'] = '下单有礼日常返' . $config_ordergrant['days_order_grant'] . $grantType;
		}

		$log = array($order['uid'], $insert['mark']);
		member_credit_update($order['uid'], $config_ordergrant['grantType'], $insert['grant'], $log);
		pdo_insert('tiny_wmall_order_grant_record', $insert);
	}

	$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order_grant_record') . ' where uniacid = :uniacid and uid = :uid and addtime > :time1 and addtime < :time2 and type = 4', array(':uniacid' => $_W['uniacid'], ':uid' => $order['uid'], ':time1' => $time1, ':time2' => $time2));
	if (!empty($config_ordergrant['special']) && empty($is_exist)) {
		foreach ($config_ordergrant['special'] as $special) {
			if ($special['date'] == date('Y-m-d')) {
				$insert['grant'] = $special['grant'];
				$insert['type'] = 4;
				$insert['mark'] = '下单有礼优惠日奖励返' . $special['grant'] . $grantType;
				$log = array($order['uid'], $insert['mark']);
				member_credit_update($order['uid'], $config_ordergrant['grantType'], $special['grant'], $log);
				pdo_insert('tiny_wmall_order_grant_record', $insert);
			}
		}
	}

	return true;
}

function ordergrant_member_init($uid = 0)
{
	global $_W;
	$config_ordergrant = get_plugin_config('ordergrant');

	if ($config_ordergrant['status'] == 0) {
		return error(-1, '该活动未开启');
	}

	if ($uid == 0) {
		$uid = $_W['member']['uid'];
	}

	$condition = ' where uniacid = :uniacid and uid = :uid and status = 5';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $uid);

	if ($config_ordergrant['cycle'] == 1) {
		$condition .= ' and stat_month = :stat_month';
		$params[':stat_month'] = date('Ym');
	}

	$records = pdo_fetchall('select endtime from ' . tablename('tiny_wmall_order') . $condition . ' group by stat_day order by stat_day desc', $params);
	$sum = 0;
	$signed = 0;
	$index = 0;
	$yesterday = 0;
	$max = array();
	$continuous = 0;

	if (!empty($records)) {
		foreach ($records as $item) {
			++$sum;
			$day = date('Y-m-d', $item['endtime']);
			$today = date('Y-m-d', time());

			if ($day == $today) {
				$signed = 1;
			}

			$dday = date('d', $item['endtime']);

			if (($yesterday - 1) == $dday) {
				$yesterday = $dday;
			}
			else {
				$yesterday = $dday;
				++$index;
			}

			++$max[$index];

			if (dateplus($day, $continuous) == dateminus($today, 1)) {
				++$continuous;
			}
		}
	}

	$data = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'max' => empty($max) ? 0 : max($max), 'continuous' => empty($signed) ? $continuous : $continuous + 1, 'sum' => $sum, 'updatetime' => TIMESTAMP);
	$is_exist = pdo_get('tiny_wmall_order_grant', array('uniacid' => $_W['uniacid'], 'uid' => $uid));

	if (empty($is_exist)) {
		pdo_insert('tiny_wmall_order_grant', $data);
	}
	else {
		pdo_update('tiny_wmall_order_grant', $data, array('id' => $is_exist['id'], 'uniacid' => $_W['uniacid']));
	}

	return $data;
}

function dateplus($date, $day)
{
	$time = strtotime($date);
	$time = $time + (86400 * $day);
	$date = date('Y-m-d', $time);
	return $date;
}

function dateminus($date, $day)
{
	$time = strtotime($date);
	$time = $time - (86400 * $day);
	$date = date('Y-m-d', $time);
	return $date;
}

function ordergrant_grant_record($days, $type, $uid)
{
	global $_W;
	$config_ordergrant = get_plugin_config('ordergrant');

	if ($config_ordergrant['status'] == 0) {
		return error(-1, '该活动未开启');
	}

	$grantType = '积分';

	if ($config_ordergrant['grantType'] == 'credit2') {
		$grantType = '元';
	}

	$condition = ' where uniacid = :uniacid and uid = :uid and days = :days and type = :type';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $uid, ':days' => $days, ':type' => $type);

	if ($config_ordergrant['cycle'] == 1) {
		$condition .= ' and stat_month = :stat_month';
		$params[':stat_month'] = date('Ym');
	}

	$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order_grant_record') . $condition, $params);

	if (!empty($is_exist)) {
		return error(-1, '您已领取该奖励');
	}

	$grant = 0;
	$mark = '';

	if ($type == 1) {
		foreach ($config_ordergrant['continuous'] as $row) {
			if ($row['days'] == $days) {
				$mark = '连续下单' . $days . '天奖励' . $row['grant'] . $grantType;
				$grant = $row['grant'];
			}
		}
	}
	else {
		if ($type == 2) {
			foreach ($config_ordergrant['all'] as $row) {
				if ($row['days'] == $days) {
					$mark = '累计下单' . $days . '天奖励' . $row['grant'] . $grantType;
					$grant = $row['grant'];
				}
			}
		}
	}

	$insert = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'days' => $days, 'grant' => $grant, 'credittype' => $config_ordergrant['grantType'], 'type' => $type, 'addtime' => TIMESTAMP, 'mark' => $mark);

	if ($config_ordergrant['cycle'] == 1) {
		$insert['stat_month'] = date('Ym');
	}

	pdo_insert('tiny_wmall_order_grant_record', $insert);
	mload()->model('member');
	$log = array($uid, '下单有礼返' . $grant . $grantType);
	member_credit_update($uid, $config_ordergrant['grantType'], $grant, $log);
	return error(0, '领取奖励成功');
}

function getCalendar($year = NULL, $month = NULL, $week = true)
{
	global $_W;
	$config_ordergrant = get_plugin_config('ordergrant');

	if ($config_ordergrant['status'] == 0) {
		return error(-1, '该活动未开启');
	}

	if (empty($year)) {
		$year = date('Y', time());
	}

	if (empty($month)) {
		$month = date('m', time());
	}

	$date = getDates(array('year' => $year, 'month' => $month));
	$array = array();
	$maxday = 28;

	if (28 < $date['days']) {
		$maxday = 35;
	}

	$i = 1;

	while ($i <= $maxday) {
		$day = 0;

		if ($i <= $date['days']) {
			$day = $i;
		}

		$today = 0;
		if (($date['thisyear'] == $year) && ($date['thismonth'] == $month) && ($date['doday'] == $i)) {
			$today = 1;
		}

		$array[$i] = array('year' => $date['year'], 'month' => $date['month'], 'day' => $day, 'date' => $date['year'] . '-' . $date['month'] . '-' . $day, 'signed' => 0, 'today' => $today);
		++$i;
	}

	$records = pdo_fetchall('select endtime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 5 and endtime between :starttime and :endtime group by stat_day', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':starttime' => $date['firsttime'], ':endtime' => $date['lasttime']));

	if (!empty($records)) {
		foreach ($records as $item) {
			$sign_date = array('year' => date('Y', $item['endtime']), 'month' => date('m', $item['endtime']), 'day' => date('d', $item['endtime']));

			foreach ($array as $day => &$row) {
				if ($day == $sign_date['day']) {
					$row['signed'] = 1;
				}
			}

			unset($row);
		}
	}

	$special = $config_ordergrant['special'];

	if (!empty($special)) {
		foreach ($special as $item) {
			$item['date'] = explode('-', $item['date']);
			$sign_date = array('year' => $item['date'][0], 'month' => $item['date'][1], 'day' => $item['date'][2]);

			foreach ($array as $day => &$row) {
				if (($row['day'] == $sign_date['day']) && ($row['month'] == $sign_date['month']) && ($row['year'] == $sign_date['year'])) {
					$row['title'] = $item['title'];
					$row['color'] = $item['color'];
				}
			}

			unset($row);
		}
	}

	foreach ($array as $day => &$row) {
		$difference = $row['day'] - date('d');
		if ((0 < $difference) || (($difference == 0) && ($row['signed'] == 0))) {
			$grant = ordergrant_next_grant($difference);
			$row['grant'] = $grant['total'];
		}
	}

	unset($row);

	if ($week) {
		$calendar = array();

		foreach ($array as $index => $row) {
			if ((1 <= $index) && ($index <= 7)) {
				$cindex = 0;
			}
			else {
				if ((8 <= $index) && ($index <= 14)) {
					$cindex = 1;
				}
				else {
					if ((15 <= $index) && ($index <= 21)) {
						$cindex = 2;
					}
					else {
						if ((22 <= $index) && ($index <= 28)) {
							$cindex = 3;
						}
						else {
							if ((29 <= $index) && ($index <= 35)) {
								$cindex = 4;
							}
						}
					}
				}
			}

			$calendar[$cindex][] = $row;
		}
	}
	else {
		$calendar = $array;
	}

	return $calendar;
}

function getDates($date = array())
{
	global $_W;

	if (empty($date)) {
		$date = array('year' => date('y', time()), 'month' => date('m', time()), 'day' => date('d', time()));
	}

	$days = date('t', strtotime($date['year'] . '-' . $date['month']));
	$result = array('firstday' => 1, 'lastday' => $days, 'firsttime' => strtotime($date['year'] . '-' . $date['month'] . '-1'), 'lasttime' => strtotime($date['year'] . '-' . ($date['month'] + 1) . '-1') - 1, 'year' => $date['year'], 'thisyear' => date('Y', time()), 'month' => $date['month'], 'thismonth' => date('m', time()), 'day' => $date['day'], 'doday' => date('d', time()), 'days' => $days);
	return $result;
}

function ordergrant_next_grant($difference)
{
	global $_W;
	$config_ordergrant = get_plugin_config('ordergrant');

	if ($config_ordergrant['status'] == 0) {
		return error(-1, '该活动未开启');
	}

	$grantType = '积分';

	if ($config_ordergrant['grantType'] == 'credit2') {
		$grantType = '元';
	}

	if (0 <= $difference) {
		$time1 = strtotime(date('Ymd'));
		$time2 = strtotime(date('Ymd')) + 86400;
		$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order_grant_record') . ' where uniacid = :uniacid and uid = :uid and addtime > :time1 and addtime < :time2 and type <= 1', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':time1' => $time1, ':time2' => $time2));
	}

	$order_days_amount = pdo_get('tiny_wmall_order_grant', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	$continuous = $order_days_amount['continuous'] + $difference;
	$sum = $order_days_amount['sum'] + $difference;

	if (empty($is_exist)) {
		$continuous = $order_days_amount['continuous'] + $difference + 1;
		$sum = $order_days_amount['sum'] + $difference + 1;
	}

	$grant = array('days' => $config_ordergrant['days_order_grant']);
	$message = '下单即可领取日常奖励' . $grant['days'] . $grantType . '<br>';
	$text = '';

	if (!empty($config_ordergrant['continuous'])) {
		foreach ($config_ordergrant['continuous'] as $val) {
			if ($continuous == $val['days']) {
				$condition = ' where uniacid = :uniacid and uid = :uid and days = :days';
				$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':days' => $continuous);

				if ($config_ordergrant['cycle'] == 1) {
					$condition .= ' and stat_month = :stat_month';
					$params[':stat_month'] = date('Ym');
				}

				$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order_grant_record') . $condition, $params);

				if (!empty($is_exist)) {
					$text = '(已领取)';
				}

				$grant['continuous'] = $val['grant'];
				$message .= '连续' . $continuous . '天下单奖励' . $val['grant'] . $grantType . ' ' . $text . '<br>';
			}
		}
	}

	if (!empty($config_ordergrant['all'])) {
		foreach ($config_ordergrant['all'] as $val) {
			if ($sum == $val['days']) {
				$grant['sum'] = $val['grant'];
				$message .= '累计' . $sum . '天下单奖励' . $val['grant'] . $grantType . '<br>';
			}
		}
	}

	if (!empty($config_ordergrant['special'])) {
		$special_day = TIMESTAMP + ($difference * 86400);
		$special = date('Y-m-d', $special_day);

		foreach ($config_ordergrant['special'] as $val) {
			if ($special == $val['date']) {
				$grant['special'] = $val['grant'];
				$message .= '优惠日下单奖励' . $val['grant'] . $grantType . '<br>';
			}
		}
	}

	if (empty($text)) {
		$total = array_sum($grant);
	}
	else {
		$total = array_sum(array($grant['days'], $grant['sum'], $grant['special']));
	}

	$grant = array('total' => $total, 'message' => $message);
	return $grant;
}

function grant_types()
{
	$labels = array(
		array('css' => 'label-success', 'text' => '日常奖励'),
		array('css' => 'label-warning', 'text' => '连续下单奖励'),
		array('css' => 'label-info', 'text' => '累计下单奖励'),
		array('css' => 'label-primary', 'text' => '首单奖励'),
		array('css' => 'label-danger', 'text' => '优惠日下单奖励'),
		array('css' => 'label-default', 'text' => '分享奖励')
		);
	return $labels;
}

defined('IN_IA') || exit('Access Denied');

?>
