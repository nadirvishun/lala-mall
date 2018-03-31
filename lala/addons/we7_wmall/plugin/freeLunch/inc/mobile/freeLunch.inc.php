<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = $freelunch['title'];
	$type = (trim($_GPC['type']) ? trim($_GPC['type']) : 'common');
	$serial_sn = ($type == 'common' ? $freelunch['serial_sn'] : $freelunch['plus_serial_sn']);
	$record = freelunch_record_get($serial_sn, $type);

	if ($record['status'] == 2) {
		imessage('活动已开奖,现在去参加新一期活动', 'refresh', 'info');
	}

	$partake_status = freelunch_partaker_partake_status($_W['member']['uid'], $record['id'], $type);
	$member_partaker = freelunch_member_partaker($record['id']);
	$luckiers = pdo_fetchall('select a.*,b.nickname,b.mobile,b.avatar from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid= b.uid where a.uniacid = :uniacid and a.serial_sn < :serial_sn and a.type = :type and a.freelunch_id = :freelunch_id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':serial_sn' => $freelunch['serial_sn'], ':type' => $type, ':freelunch_id' => $freelunch['id']));

	if (!empty($luckiers)) {
		foreach ($luckiers as &$row) {
			$row['avatar'] = tomedia($row['avatar']);
			$row['time'] = sub_time($row['endtime']);
		}
	}

	$min_id = intval($_GPC['min']);
	$partakers = freelunch_record_partaker($record['id'], $min_id);

	if ($_W['ispost']) {
		imessage($partakers, '', 'ajax');
	}
}

if ($op == 'partake') {
	$time = TIMESTAMP - 600;
	pdo_query('delete from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and addtime < :time and is_pay = 0', array(':uniacid' => $_W['uniacid'], ':time' => $time));
	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => intval($_GPC['record_id'])));

	if (empty($record)) {
		imessage('活动不存在', referer(), 'error');
	}

	if ($record['status'] == 2) {
		imessage('活动已开奖,现在去参加新一期活动', referer(), 'info');
	}

	$partake_status = freelunch_partaker_partake_status($_W['member']['uid'], $record['id'], $record['type']);

	if ($partake_status['errno'] == -1) {
		imessage($partake_status['message'], referer(), 'info');
	}

	$insert = array('uniacid' => $_W['uniacid'], 'freelunch_id' => $freelunch['id'], 'record_id' => $record['id'], 'serial_sn' => $record['serial_sn'], 'uid' => $_W['member']['uid'], 'number' => '', 'addtime' => TIMESTAMP, 'final_fee' => $record['partaker_fee'], 'order_sn' => date('YmdHis') . random(6, true), 'is_pay' => 0);
	pdo_insert('tiny_wmall_freelunch_partaker', $insert);
	$partaker_id = pdo_insertid();
	header('location:' . imurl('system/paycenter/pay', array('id' => $partaker_id, 'order_type' => 'freelunch')));
}

if ($op == 'partake_success') {
	$_W['page']['title'] = '参与成功';
	$partaker = pdo_fetch('select * from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 order by id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	$num = $partaker['number'] - 10000;
}

if ($op == 'detail') {
	$_W['page']['title'] = '参与详情';
	$id = intval($_GPC['record_id']);
	$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$record['percent'] = round(1 - ($record['partaker_dosage'] / $record['partaker_total']), 2) * 100;

	if (0 < $record['reward_number']) {
		$record['reward_number'] = str_split($record['reward_number']);
		$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $record['reward_uid']), array('avatar', 'nickname'));
	}

	$mine_partaker = freelunch_member_partaker($record['id']);
	$partake_status = freelunch_partaker_partake_status($_W['member']['uid'], $record['id'], $record['type']);
	$winner_partaker = freelunch_member_partaker($record['id'], $record['reward_uid']);
	$min_id = intval($_GPC['min']);
	$partakers = freelunch_record_partaker($record['id'], $min_id);

	if ($_W['ispost']) {
		imessage($partakers, '', 'ajax');
	}
}

if ($op == 'luckier') {
	$_W['page']['title'] = '往期幸运星';
	$condition = ' where a.uniacid = :uniacid and a.status = 2 and a.freelunch_id = :freelunch_id';
	$params = array(':uniacid' => $_W['uniacid'], ':freelunch_id' => $freelunch['id']);
	$id = intval($_GPC['min']);

	if (0 < $id) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}

	$luckiers = pdo_fetchall('select a.*,b.nickname,b.mobile,b.avatar from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid= b.uid ' . $condition . ' order by a.id desc limit 15', $params, 'id');
	$min = 0;

	if (!empty($luckiers)) {
		foreach ($luckiers as &$value) {
			$value['avatar'] = tomedia($value['avatar']);
			$value['endtime'] = date('Y-m-d H:i:s', $value['endtime']);
			$value['total'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_freelunch_partaker') . ' where uniacid = :uniacid and record_id = :record_id and uid = :uid and is_pay = 1', array(':uniacid' => $_W['uniacid'], ':record_id' => $value['id'], ':uid' => $value['reward_uid']));
		}

		$min = min(array_keys($luckiers));
	}

	if ($_W['ispost']) {
		$luckiers = array_values($luckiers);
		$respon = array('errno' => 0, 'message' => $luckiers, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

include itemplate('freeLunch');

?>
