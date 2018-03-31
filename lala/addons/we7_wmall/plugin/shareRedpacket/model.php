<?php
//微擎应用 http://www.we7.cc   
function shareRedpacket_get()
{
	global $_W;
	$shareRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_shareredpacket') . ' where uniacid = :uniacid and starttime < :time and endtime > :time and status = 1', array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));

	if (empty($shareRedpacket)) {
		return error(-1, '暂无分享红包活动');
	}

	$shareRedpacket['share'] = iunserializer($shareRedpacket['share']);
	return $shareRedpacket;
}

function shareRedpacket_sharer_grant($uid)
{
	global $_W;
	$redPacket = shareredpacket_get();

	if (empty($redPacket)) {
		return $redPacket;
	}

	$invite = pdo_get('tiny_wmall_shareredpacket_invite_record', array('follow_uid' => $uid, 'status' => 0));

	if (empty($invite)) {
		return error(-1, '邀请记录不存在');
	}

	mload()->model('redPacket');
	$params = array('activity_id' => $redPacket['id'], 'title' => '邀请新用户奖励红包', 'channel' => 'shareRedpacket', 'type' => 'common', 'uid' => $invite['share_uid'], 'discount' => $invite['share_redpacket_discount'], 'condition' => $invite['share_redpacket_condition'], 'days_limit' => $invite['share_redpacket_days_limit']);
	$status = redPacket_grant($params);

	if (is_error($status)) {
		return $status;
	}

	pdo_update('tiny_wmall_shareredpacket_invite_record', array('status' => 1), array('id' => $invite['id']));
	return true;
}

defined('IN_IA') || exit('Access Denied');

?>
