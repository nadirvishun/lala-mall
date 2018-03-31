<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'config');

if ($op == 'config') {
	$_W['page']['title'] = '设置红包活动';

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '活动主题不能为空'), '', 'ajax'));
		$starttime = (trim($_GPC['starttime']) ? trim($_GPC['starttime']) : imessage(error(-1, '活动开始时间不能为空'), '', 'ajax'));
		$endtime = (trim($_GPC['endtime']) ? trim($_GPC['endtime']) : imessage(error(-1, '活动结束时间不能为空'), '', 'ajax'));
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);

		if ($endtime <= $starttime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}

		$share = array('title' => trim($_GPC['share_title']), 'desc' => trim($_GPC['desc']), 'imgUrl' => trim($_GPC['imgUrl']));
		$data = array('title' => $title, 'uniacid' => $_W['uniacid'], 'share_redpacket_condition' => floatval($_GPC['share_redpacket_condition']), 'share_redpacket_min' => floatval($_GPC['share_redpacket_min']), 'share_redpacket_max' => floatval($_GPC['share_redpacket_max']), 'share_redpacket_days_limit' => intval($_GPC['share_redpacket_days_limit']), 'follow_redpacket_min' => floatval($_GPC['follow_redpacket_min']), 'follow_redpacket_max' => floatval($_GPC['follow_redpacket_max']), 'follow_redpacket_days_limit' => intval($_GPC['follow_redpacket_days_limit']), 'share' => iserializer($share), 'agreement' => htmlspecialchars_decode($_GPC['agreement']), 'status' => intval($_GPC['status']), 'starttime' => $starttime, 'endtime' => $endtime, 'addtime' => TIMESTAMP);
		$id = intval($_GPC['id']);

		if (0 < $id) {
			pdo_update('tiny_wmall_shareredpacket', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			pdo_insert('tiny_wmall_shareredpacket', $data);
		}

		imessage(error(0, '设置红包活动成功'), referer(), 'ajax');
	}

	$activity = pdo_get('tiny_wmall_shareredpacket', array('uniacid' => $_W['uniacid']));

	if (!empty($activity['share'])) {
		$activity['share'] = iunserializer($activity['share']);
	}
}

if ($op == 'invite') {
	$_W['page']['title'] = '邀请记录';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if (-1 < $status) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
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
	$share_uid = intval($_GPC['share_uid']);

	if (0 < $share_uid) {
		$condition .= ' and a.share_uid = :share_uid';
		$params[':share_uid'] = $share_uid;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (b.realname like \'%' . $keyword . '%\' or b.nickname like \'%' . $keyword . '%\' or mobile like \'%' . $keyword . '%\')';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.share_uid = b.uid' . $condition, $params);
	$invites = pdo_fetchall('select a.*,b.realname as share_realname,b.avatar as share_avatar from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.share_uid = b.uid' . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($invites)) {
		foreach ($invites as &$row) {
			$row['share_avatar'] = tomedia($row['share_avatar']);
			$follow_member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $row['follow_uid']));
			$row['follow_realname'] = $follow_member['realname'];
			$row['follow_avatar'] = tomedia($follow_member['avatar']);
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'ranking') {
	$_W['page']['title'] = '分享排行';
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

	$condition .= ' and a.addtime >= :starttime and a.addtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (b.realname like \'%' . $keyword . '%\' or b.nickname like \'%' . $keyword . '%\' or mobile like \'%' . $keyword . '%\')';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(distinct a.share_uid) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.share_uid = b.uid ' . $condition, $params);
	$rankings = pdo_fetchall('select count(*) as total,sum(share_redpacket_discount) as pre_sum, a.*,b.realname as share_realname,b.avatar as share_avatar from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.share_uid = b.uid ' . $condition . ' group by a.share_uid order by total desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($rankings)) {
		foreach ($rankings as &$row) {
			$row['sum'] = pdo_fetchcolumn('select sum(share_redpacket_discount) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' where uniacid = :uniacid and share_uid = :share_uid and status = 1', array(':uniacid' => $_W['uniacid'], ':share_uid' => $row['share_uid']));
			$row['sum'] = intval($row['sum']);
			$row['has_ordered'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' where uniacid = :uniacid and share_uid = :share_uid and status = 1', array(':uniacid' => $_W['uniacid'], ':share_uid' => $row['share_uid']));
		}
	}

	$pager = pagination($total, $pindex, $psize);
}

include itemplate('activity');

?>
