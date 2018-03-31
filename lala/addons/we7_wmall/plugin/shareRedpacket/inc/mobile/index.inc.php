<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_GPC;
global $_W;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'invite');
$_W['page']['title'] = $redPacket['title'];
icheckauth();

if ($op == 'invite') {
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' where uniacid = :uniacid and share_uid = :share_uid', array(':uniacid' => $_W['uniacid'], ':share_uid' => $_W['member']['uid']));
	$redPacket_num = pdo_fetchcolumn('select sum(share_redPacket_discount) from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' where uniacid = :uniacid and share_uid = :share_uid and status = 1', array(':uniacid' => $_W['uniacid'], ':share_uid' => $_W['member']['uid']));
	$id = intval($_GPC['min']);
	$condition = ' where a.uniacid = :uniacid and a.share_uid = :share_uid';
	$params = array(':uniacid' => $_W['uniacid'], ':share_uid' => $_W['member']['uid']);

	if (0 < $id) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}

	$invited_info = pdo_fetchall('select a.*, b.nickname,b.avatar from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join' . tablename('tiny_wmall_members') . ' as b on a.follow_uid = b.uid ' . $condition . ' order by a.id desc limit 15', $params, 'id');
	$min = 0;

	if (!empty($invited_info)) {
		foreach ($invited_info as &$row) {
			$row['avatar'] = tomedia($row['avatar']);
		}

		$min = min(array_keys($invited_info));
	}

	if ($_W['isajax']) {
		$invited_info = array_values($invited_info);
		$respon = array('errno' => 0, 'message' => $invited_info, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if ($op == 'ranking') {
	$min = 2;
	$pindex = max(1, intval($_GPC['min']));
	$rankings = pdo_fetchall('select count(*) as total, a.*,b.nickname,b.avatar from ' . tablename('tiny_wmall_shareredpacket_invite_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.share_uid = b.uid  where a.uniacid = :uniacid group by a.share_uid order by total desc limit ' . (($pindex - 1) * 15) . ',' . 15, array(':uniacid' => $_W['uniacid']));
	++$pindex;

	if (empty($rankings)) {
		$pindex = 0;
	}
	else {
		foreach ($rankings as &$val) {
			$val['avatar'] = tomedia($val['avatar']);
		}
	}

	if ($_W['isajax']) {
		$rankings = array_values($rankings);
		$respon = array('errno' => 0, 'message' => $rankings, 'min' => $pindex);
		imessage($respon, '', 'ajax');
	}
}

$_share = array('title' => $redPacket['share']['title'], 'desc' => $redPacket['share']['desc'], 'link' => imurl('shareRedpacket/share/invite', array('u' => $_W['member']['uid']), true), 'imgUrl' => tomedia($redPacket['share']['imgUrl']));
include itemplate('index');

?>
