<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function freelunch_fetch() {
	global $_W;
	$activity = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid'], 'status' => 1));
	return $activity;
}

function freelunch_record_update() {
	global $_W, $_GPC;
	$activity = freelunch_fetch();
	$record = pdo_get('tiny_wmall_freelunch_record', array('serial_sn' => $activity['serial_sn'], 'type' => 'common', 'freelunch_id' => $activity['id']));
	if(empty($record)) {
		$activity['serial_sn'] = $activity['serial_sn'] ? $activity['serial_sn']: 1;
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'freelunch_id' => $activity['id'],
			'serial_sn' => $activity['serial_sn'],
			'type' => 'common',
			'partaker_fee' => $activity['pre_partaker_fee'],
			'partaker_total' => $activity['pre_partaker_num'],
			'partaker_dosage' => $activity['pre_partaker_num'],
			'reward_fee' => $activity['pre_reward_fee'],
			'startime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_freelunch_record', $insert);
	}
	$record_plus = pdo_get('tiny_wmall_freelunch_record', array('serial_sn' => $activity['plus_serial_sn'], 'type' => 'plus', 'freelunch_id' => $activity['id']));
	if(empty($record_plus)) {
		$activity['plus_serial_sn'] = $activity['plus_serial_sn'] ? $activity['plus_serial_sn']: 1;
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'freelunch_id' => $activity['id'],
			'serial_sn' => $activity['plus_serial_sn'],
			'type' => 'plus',
			'partaker_fee' => $activity['plus_pre_partaker_fee'],
			'partaker_total' => $activity['plus_partaker_num'],
			'partaker_dosage' => $activity['plus_partaker_num'],
			'reward_fee' => $activity['pre_plus_reward_fee'],
			'startime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_freelunch_record', $insert);
	}
	$type = trim($_GPC['type']) ? trim($_GPC['type']): 'common';
	$data = pdo_fetch('select * from ' . tablename('tiny_wmall_freelunch_record') . ' where uniacid = :uniacid and type = :type and freelunch_id = :freelunch_id order by serial_sn desc', array(':uniacid' => $_W['uniacid'], ':type' => $type, ':freelunch_id' => $activity['id']));
	if($data['partaker_dosage'] == 0) {
		$data['serial_sn'] = $data['serial_sn'] + 1;
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'freelunch_id' => $data['freelunch_id'],
			'serial_sn' => $data['serial_sn'],
			'type' => $data['type'],
			'partaker_fee' => $data['partaker_fee'],
			'partaker_total' => $data['partaker_total'],
			'partaker_dosage' => $data['partaker_total'],
			'reward_fee' => $data['reward_fee'],
			'startime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_freelunch_record', $insert);
		$id = pdo_insertid();
		$data = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	return $data;
}










