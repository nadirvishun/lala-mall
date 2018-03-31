<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'config');

if ($op == 'config') {
	$_W['page']['title'] = '活动设置';

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '活动主题不能为空'), '', 'ajax'));
		$starttime = (trim($_GPC['starttime']) ? trim($_GPC['starttime']) : imessage(error(-1, '活动开始时间不能为空'), '', 'ajax'));
		$endtime = (trim($_GPC['endtime']) ? trim($_GPC['endtime']) : imessage(error(-1, '活动结束时间不能为空'), '', 'ajax'));
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);

		if ($endtime <= $starttime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}

		if (intval($_GPC['status']) == 1) {
			if (intval($_GPC['pre_partaker_num']) <= 0) {
				imessage(error(-1, '霸王餐开奖人数必须大于0'), '', 'ajax');
			}

			if ((intval($_GPC['plus_status']) == 1) && (intval($_GPC['plus_partaker_num']) <= 0)) {
				imessage(error(-1, '霸王餐PLUS开奖人数必须大于0'), '', 'ajax');
			}
		}

		$share = array('title' => trim($_GPC['share_title']), 'desc' => trim($_GPC['desc']), 'imgUrl' => trim($_GPC['imgUrl']), 'link' => trim($_GPC['link']));
		$data = array('title' => $title, 'uniacid' => $_W['uniacid'], 'thumb' => trim($_GPC['thumb']), 'max_partake_times' => intval($_GPC['max_partake_times']), 'partake_grant_type' => intval($_GPC['partake_grant_type']), 'reward_grant_type' => intval($_GPC['reward_grant_type']), 'redpacket_days_limit' => intval($_GPC['redpacket_days_limit']), 'pre_partaker_num' => intval($_GPC['pre_partaker_num']), 'pre_partaker_fee' => floatval($_GPC['pre_partaker_fee']), 'pre_reward_fee' => floatval($_GPC['pre_reward_fee']), 'pre_max_partake_times' => intval($_GPC['pre_max_partake_times']), 'plus_status' => intval($_GPC['plus_status']), 'plus_thumb' => trim($_GPC['plus_thumb']), 'plus_reward_num' => intval($_GPC['plus_reward_num']), 'plus_partaker_num' => intval($_GPC['plus_partaker_num']), 'plus_pre_partaker_fee' => floatval($_GPC['plus_pre_partaker_fee']), 'pre_plus_reward_fee' => floatval($_GPC['pre_plus_reward_fee']), 'plus_pre_max_partake_times' => intval($_GPC['plus_pre_max_partake_times']), 'share' => iserializer($share), 'agreement' => htmlspecialchars_decode($_GPC['agreement']), 'status' => intval($_GPC['status']), 'starttime' => $starttime, 'endtime' => $endtime, 'addtime' => TIMESTAMP);
		$id = intval($_GPC['id']);

		if (0 < $id) {
			pdo_update('tiny_wmall_freelunch', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			pdo_insert('tiny_wmall_freelunch', $data);
		}

		imessage(error(0, '设置活动成功'), referer(), 'ajax');
	}

	$activity = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid']));

	if (!empty($activity['share'])) {
		$activity['share'] = iunserializer($activity['share']);
	}
}

if ($op == 'period') {
	$_W['page']['title'] = '活动期数';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);

	if (!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	}
	else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}

	$condition .= ' and a.startime >= :starttime and a.startime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$serial_sn = (!empty($_GPC['serial_sn']) ? intval($_GPC['serial_sn']) : '');

	if (0 < $serial_sn) {
		$condition .= ' and a.serial_sn = :serial_sn';
		$params[':serial_sn'] = $serial_sn;
	}

	$type = trim($_GPC['type']);

	if (!empty($type)) {
		$condition .= ' and a.type = :type';
		$params[':type'] = $type;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid = b.uid' . $condition, $params);
	$periods = pdo_fetchall('select a.*,b.nickname,b.avatar from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid = b.uid' . $condition . ' order by a.serial_sn desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($periods)) {
		$freelunch = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid'], 'status' => 1), array('title'));

		foreach ($periods as &$value) {
			$value['title'] = $freelunch['title'];

			if ($value['type'] == 'plus') {
				$value['title'] = $value['title'] . 'Plus';
			}

			$value['avatar'] = tomedia($value['avatar']);
			$value['startime'] = date('Y-m-d H:i:s', $value['startime']);
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'period_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_freelunch_record', array('id' => $id, 'uniacid' => $_W['uniacid']));
	pdo_delete('tiny_wmall_freelunch_partaker', array('record_id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, '删除活动期数成功'), referer(), 'ajax');
}

if ($op == 'partaker') {
	$_W['page']['title'] = '参与记录';
	$condition = ' where a.uniacid = :uniacid and is_pay = 1';
	$params[':uniacid'] = $_W['uniacid'];
	$record_id = intval($_GPC['id']);

	if (0 < $record_id) {
		$condition .= ' and a.record_id = :record_id';
		$params[':record_id'] = $record_id;
	}

	if (!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	}
	else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}

	$condition .= ' and a.addtime >= :starttime and a.addtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$number = (!empty($_GPC['number']) ? intval($_GPC['number']) : '');

	if (0 < $number) {
		$condition .= ' and (a.serial_sn = :number or a.number = :number)';
		$params[':number'] = $number;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (b.realname like \'%' . $keyword . '%\' or b.nickname like \'%' . $keyword . '%\' or mobile like \'%' . $keyword . '%\')';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_freelunch_partaker') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	$partakers = pdo_fetchall('select a.*,b.nickname,b.mobile,b.avatar from ' . tablename('tiny_wmall_freelunch_partaker') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by a.serial_sn desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($partakers)) {
		$freelunch = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid'], 'status' => 1), array('title'));

		foreach ($partakers as &$val) {
			$val['avatar'] = tomedia($val['avatar']);
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			$record = pdo_get('tiny_wmall_freelunch_record', array('uniacid' => $_W['uniacid'], 'id' => $val['record_id']), array('type', 'reward_uid', 'reward_number'));
			$val['is_reward'] = 0;
			if (($val['number'] == $record['reward_number']) && ($val['uid'] == $record['reward_uid'])) {
				$val['is_reward'] = 1;
			}

			$val['title'] = $freelunch['title'];

			if ($record['type'] == 'plus') {
				$val['title'] = $val['title'] . 'Plus';
			}
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'record') {
	$_W['page']['title'] = '获奖记录';
	$condition = ' where a.uniacid = :uniacid and a.partaker_dosage = 0';
	$params[':uniacid'] = $_W['uniacid'];

	if (!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	}
	else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}

	$condition .= ' and a.endtime >= :starttime and a.endtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$number = (!empty($_GPC['number']) ? intval($_GPC['number']) : '');

	if (0 < $number) {
		$condition .= ' and (a.serial_sn = :number or a.reward_number = :number)';
		$params[':number'] = $number;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (b.realname like \'%' . $keyword . '%\' or b.nickname like \'%' . $keyword . '%\' or mobile like \'%' . $keyword . '%\')';
	}

	$type = trim($_GPC['type']);

	if (!empty($type)) {
		$condition .= ' and a.type = :type';
		$params[':type'] = $type;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid = b.uid' . $condition, $params);
	$records = pdo_fetchall('select a.*,b.nickname,b.mobile,b.avatar from ' . tablename('tiny_wmall_freelunch_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.reward_uid = b.uid' . $condition . ' order by a.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($records)) {
		$freelunch = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid'], 'status' => 1), array('title'));

		foreach ($records as &$row) {
			$row['avatar'] = tomedia($row['avatar']);
			$row['endtime'] = date('Y-m-d H:i:s', $row['endtime']);
			$row['title'] = $freelunch['title'];

			if ($row['type'] == 'plus') {
				$row['title'] = $row['title'] . 'Plus';
			}
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

include itemplate('activity');

?>
