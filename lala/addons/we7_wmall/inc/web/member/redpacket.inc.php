<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');
mload()->model('redPacket');

if ($op == 'list') {
	$_W['page']['title'] = '会员红包';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$channel = trim($_GPC['channel']);

	if (!empty($channel)) {
		$condition .= ' and a.channel = :channel';
		$params[':channel'] = $channel;
	}

	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : 0);

	if (0 < $status) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}

	if (empty($grant_starttime) || empty($grant_endtime)) {
		$grant_starttime = strtotime('-1 month');
		$grant_endtime = time();
	}

	if (empty($use_starttime) || empty($use_endtime)) {
		$use_starttime = strtotime('-1 month');
		$use_endtime = time();
	}

	if (!empty($_GPC['granttime']['start']) && !empty($_GPC['granttime']['end'])) {
		$grant_starttime = strtotime($_GPC['granttime']['start']);
		$grant_endtime = strtotime($_GPC['granttime']['end']);
		$condition .= ' and a.granttime >= :grant_starttime and a.granttime <= :grant_endtime';
		$params[':grant_starttime'] = $grant_starttime;
		$params[':grant_endtime'] = $grant_endtime;
	}

	if (!empty($_GPC['usetime']['start']) && !empty($_GPC['usetime']['end'])) {
		$use_starttime = strtotime($_GPC['usetime']['start']);
		$use_endtime = strtotime($_GPC['usetime']['end']);
		$condition .= ' and a.usetime >= :use_starttime and a.usetime <= :use_endtime';
		$params[':use_starttime'] = $use_starttime;
		$params[':use_endtime'] = $use_endtime;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (b.realname like :keyword or b.mobile like :keyword)';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_redpacket_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	$redpackets = pdo_fetchall('select a.*,b.avatar,b.realname from ' . tablename('tiny_wmall_activity_redpacket_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by a.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$redpacket_channels = redpacket_channels();
	$redpacket_status = redpacket_status();
}

if ($op == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_activity_redpacket_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	imessage(error(0, '删除红包记录成功'), '', 'ajax');
}

include itemplate('member/redpacket');

?>
