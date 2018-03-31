<?php
//微擎应用 http://www.we7.cc   
function freelunch_get()
{
	global $_W;
	$freelunch = pdo_fetch('select * from ' . tablename('tiny_wmall_freelunch') . ' where uniacid = :uniacid and starttime < :time and endtime > :time and status = 1', array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));

	if (empty($freelunch)) {
		return error(-1, '暂无霸王餐活动');
	}

	if ($freelunch['pre_partaker_num'] <= 0) {
		return error(-1, '霸王餐参与人数必须大于0');
	}

	if (($freelunch['plus_status'] == 1) && ($freelunch['plus_partaker_num'] <= 0)) {
		return error(-1, '超级霸王餐参与人数必须大于0');
	}

	$freelunch['share'] = iunserializer($freelunch['share']);
	return $freelunch;
}

function freelunch_record_init()
{
	global $_W;
	$freelunch = freelunch_get();

	if (is_error($freelunch)) {
		return $freelunch;
	}

	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'serial_sn' => $freelunch['serial_sn'], 'type' => 'common', 'freelunch_id' => $freelunch['id']));
	if (empty($record) || empty($record['partaker_dosage'])) {
		$serial_sn = $freelunch['serial_sn'];
		if (!empty($record) && empty($record['partaker_dosage'])) {
			$freelunch['serial_sn'] = $serial_sn = $serial_sn + 1;
			pdo_update('tiny_wmall_freelunch', array('serial_sn' => $serial_sn), array('id' => $freelunch['id']));
		}

		$insert = array('uniacid' => $_W['uniacid'], 'freelunch_id' => $freelunch['id'], 'serial_sn' => $serial_sn ? $serial_sn : 1, 'type' => 'common', 'partaker_fee' => $freelunch['pre_partaker_fee'], 'partaker_total' => $freelunch['pre_partaker_num'], 'partaker_dosage' => $freelunch['pre_partaker_num'], 'reward_fee' => $freelunch['pre_reward_fee'], 'startime' => TIMESTAMP);
		pdo_insert('tiny_wmall_freelunch_record', $insert);
	}

	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'serial_sn' => $freelunch['plus_serial_sn'], 'type' => 'plus', 'freelunch_id' => $freelunch['id']));
	if ((empty($record) || empty($record['partaker_dosage'])) && ($freelunch['plus_status'] == 1)) {
		$serial_sn = $freelunch['plus_serial_sn'];
		if (!empty($record) && empty($record['partaker_dosage'])) {
			$freelunch['plus_serial_sn'] = $serial_sn = $serial_sn + 1;
			pdo_update('tiny_wmall_freelunch', array('plus_serial_sn' => $serial_sn), array('id' => $freelunch['id']));
		}

		$insert = array('uniacid' => $_W['uniacid'], 'freelunch_id' => $freelunch['id'], 'serial_sn' => $serial_sn ? $serial_sn : 1, 'type' => 'plus', 'partaker_fee' => $freelunch['plus_pre_partaker_fee'], 'partaker_total' => $freelunch['plus_partaker_num'], 'partaker_dosage' => $freelunch['plus_partaker_num'], 'reward_fee' => $freelunch['pre_plus_reward_fee'], 'startime' => TIMESTAMP);
		pdo_insert('tiny_wmall_freelunch_record', $insert);
	}

	$records = pdo_getall('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'status' => 1, 'partaker_dosage' => 0, 'reward_uid' => ''), array('id'));

	if (!empty($records)) {
		foreach ($records as $record) {
			freelunch_reward($record['id']);
		}
	}

	return $freelunch;
}

function freelunch_record_get($serial_sn, $type)
{
	global $_W;
	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'serial_sn' => $serial_sn, 'type' => $type));

	if (empty($record)) {
		return error(-1, '该期活动不存在');
	}

	$record['percent'] = round(1 - ($record['partaker_dosage'] / $record['partaker_total']), 2) * 100;
	return $record;
}

function freelunch_member_partaker($record_id, $uid = 0)
{
	global $_W;

	if (empty($uid)) {
		$uid = $_W['member']['uid'];
	}

	$partakers = pdo_fetchall('select * from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and record_id = :record_id and uid = :uid and is_pay = 1 order by id desc', array(':uniacid' => $_W['uniacid'], ':record_id' => $record_id, ':uid' => $uid));

	if (empty($partakers)) {
		$partakers = array();
	}

	$data = array('nums' => count($partakers), 'data' => $partakers);
	return $data;
}

function freelunch_record_partaker($record_id, $min_id)
{
	global $_W;
	$condition = ' where a.uniacid = :uniacid and a.record_id = :record_id and a.is_pay = 1';
	$params = array(':record_id' => $record_id, ':uniacid' => $_W['uniacid']);

	if (0 < $min_id) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $min_id;
	}

	$min = 0;
	$partakers = array();
	$temps = pdo_fetchall('select a.id as aid,a.addtime,b.avatar,b.nickname from ' . tablename('tiny_wmall_freelunch_partaker') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by a.id desc limit 10', $params, 'aid');

	if (!empty($temps)) {
		foreach ($temps as $val) {
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			$val['avatar'] = tomedia($val['avatar']);
			$partakers[$val['aid']] = $val;
		}

		$min = min(array_keys($partakers));
	}

	$currents = array_values($partakers);
	$respon = array('errno' => 0, 'message' => $currents, 'min' => $min);
	return $respon;
}

function freelunch_partaker_partake_status($uid, $id, $type)
{
	global $_W;
	$freelunch = freelunch_get();

	if (0 < $freelunch['max_partake_times']) {
		$today_partake = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and uid = :uid and addtime > :time', array(':uniacid' => $_W['uniacid'], ':uid' => $uid, ':time' => strtotime(date('Y-m-d'))));

		if ($freelunch['max_partake_times'] <= $today_partake) {
			return error(-1, '每天最多参与' . $freelunch['max_partake_times'] . '次');
		}
	}

	$member_partaker = freelunch_member_partaker($id);
	$pre_max_partake_times = $freelunch['pre_max_partake_times'];

	if ($type == 'plus') {
		$pre_max_partake_times = $freelunch['plus_pre_max_partake_times'];
	}

	if (($pre_max_partake_times <= $member_partaker['nums']) && (0 < $pre_max_partake_times)) {
		return error(-1, '每期最多参与' . $pre_max_partake_times . '次');
	}

	return error(0, '');
}

function freelunch_partaker_status_update($order_id, $type)
{
	global $_W;
	$freelunch = freelunch_get();

	if (is_error($freelunch)) {
		return $freelunch;
	}

	$order = pdo_get('tiny_wmall_freelunch_partaker', array('uniacid' => $_W['uniacid'], 'id' => $order_id));

	if (empty($order)) {
		return error(-1, '支付订单不存在');
	}

	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => $order['record_id']));

	if (empty($record)) {
		return error(-1, '该期活动不存在');
	}

	if ($type == 'pay') {
		$number = pdo_fetchcolumn('select number from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and serial_sn = :serial_sn and record_id = :record_id and freelunch_id = :freelunch_id and is_pay = 1 order by number desc', array(':uniacid' => $_W['uniacid'], ':serial_sn' => $order['serial_sn'], ':record_id' => $order['record_id'], ':freelunch_id' => $order['freelunch_id']));

		if (empty($number)) {
			$number = 10001;
		}
		else {
			++$number;
		}

		$update = array('number' => $number, 'is_pay' => 1);
		pdo_update('tiny_wmall_freelunch_partaker', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
		$partaker_dosage = $record['partaker_dosage'] - 1;

		if ($partaker_dosage < 0) {
			$partaker_dosage = 0;
		}

		pdo_update('tiny_wmall_freelunch_record', array('partaker_dosage' => $partaker_dosage), array('uniacid' => $_W['uniacid'], 'id' => $order['record_id']));

		if ($freelunch['partake_grant_type'] == 1) {
			mload()->model('redPacket');
			$params = array('title' => $freelunch['title'], 'channel' => 'freeLunch', 'type' => 'common', 'uid' => $order['uid'], 'discount' => $order['final_fee'], 'condition' => 0, 'days_limit' => $freelunch['redpacket_days_limit']);
			redPacket_grant($params);
		}
		else {
			if ($freelunch['partake_grant_type'] == 2) {
				mload()->model('member');
				$log = array($record['uid'], '霸王餐返现金' . $order['final_fee'] . '元');
				member_credit_update($order['uid'], 'credit2', $order['final_fee'], $log);
			}
		}

		if (!$partaker_dosage) {
			freelunch_reward($order['record_id']);

			if ($record['type'] == 'plus') {
				pdo_query('update tiny_wmall_freelunch set plus_serial_sn = plus_serial_sn + 1 where id = ' . $record['freelunch_id']);
			}
			else {
				pdo_query('update tiny_wmall_freelunch set serial_sn = serial_sn + 1 where id = ' . $record['freelunch_id']);
			}

			freelunch_record_init();
		}

		return true;
	}

	return true;
}

function freelunch_reward($record_id)
{
	global $_W;
	$freelunch = freelunch_get();

	if (is_error($freelunch)) {
		return $freelunch;
	}

	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => $record_id));

	if (empty($record)) {
		return error(-1, '该期活动不存在');
	}

	$reward_number = 0;
	$reward = pdo_getall('tiny_wmall_freelunch_partaker', array('uniacid' => $_W['uniacid'], 'record_id' => $record_id, 'is_pay' => 1), array('addtime'));

	foreach ($reward as $row) {
		$reward_number += date('His', $row['addtime']);
	}

	$reward_number = ($reward_number % $record['partaker_total']) + 10001;
	$partaker = pdo_get('tiny_wmall_freelunch_partaker', array('uniacid' => $_W['uniacid'], 'record_id' => $record_id, 'number' => $reward_number, 'is_pay' => 1), array('uid'));
	pdo_update('tiny_wmall_freelunch_record', array('reward_uid' => $partaker['uid'], 'status' => 2, 'reward_number' => $reward_number, 'partaker_dosage' => 0, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $record_id));

	if ($freelunch['reward_grant_type'] == 1) {
		mload()->model('redPacket');
		$params = array('title' => $freelunch['title'], 'channel' => 'freeLunch', 'type' => 'common', 'uid' => $partaker['uid'], 'condition' => 0, 'discount' => $record['reward_fee'], 'days_limit' => $freelunch['redpacket_days_limit']);
		redPacket_grant($params);
	}
	else {
		if ($freelunch['reward_grant_type'] == 2) {
			mload()->model('member');
			$log = array($partaker['uid'], '霸王餐获奖返现金' . $record['reward_fee'] . '元');
			member_credit_update($partaker['uid'], 'credit2', $record['reward_fee'], $log);
		}
	}

	return true;
}

defined('IN_IA') || exit('Access Denied');

?>
