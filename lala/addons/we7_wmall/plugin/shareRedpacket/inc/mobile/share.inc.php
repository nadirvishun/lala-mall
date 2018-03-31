<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_GPC;
global $_W;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'invite');
$_W['page']['title'] = $redPacket['title'];

if (!is_weixin()) {
	imessage('请在微信中访问该链接', '', 'info');
}

if ($op == 'invite') {
	if ($_W['ispost']) {
		$uid = intval($_GPC['uid']);
		$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax'));
		$is_exist = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

		if (!empty($is_exist)) {
			imessage(error(-1, '您已是老用户'), imurl('shareRedpacket/share/repeat'), 'ajax');
		}

		$code = trim($_GPC['code']);
		$status = check_verifycode($mobile, $code);

		if (!$status) {
			imessage(error(-1, '验证码错误'), '', 'ajax');
		}

		$openid = (trim($_GPC['fans']['openid']) ? trim($_GPC['fans']['openid']) : imessage(error(-1, '微信信息错误'), '', 'ajax'));
		$is_exist = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'openid' => $openid));

		if (!empty($is_exist)) {
			imessage(error(-1, '您已是老用户'), imurl('shareRedpacket/share/repeat'), 'ajax');
		}

		$params = array('openid' => $openid, 'nickname' => $_GPC['fans']['nickname'], 'headimgurl' => $_GPC['fans']['headimgurl'], 'sex' => $_GPC['fans']['sex'], 'mobile' => $mobile);
		mload()->model('member');
		$new_member = member_register($params);

		if (is_error($new_member)) {
			message($new_member['message'], '', 'error');
		}

		$is_get = pdo_get('tiny_wmall_shareredpacket_invite_record', array('uniacid' => $_W['uniacid'], 'activity_id' => $redPacket['id'], 'share_uid' => $uid, 'follow_uid' => $new_member['uid']));

		if (!empty($is_get)) {
			imessage(error(-1, '您已领取过这个红包'), '', 'ajax');
		}

		$share_redpacket = rand($redPacket['share_redpacket_min'], $redPacket['share_redpacket_max']);
		$follow_redpacket = rand($redPacket['follow_redpacket_min'], $redPacket['follow_redpacket_max']);
		$insert = array('uniacid' => $_W['uniacid'], 'activity_id' => $redPacket['id'], 'share_uid' => $uid, 'follow_uid' => $new_member['uid'], 'share_redpacket_condition' => $redPacket['share_redpacket_condition'], 'share_redpacket_discount' => $share_redpacket, 'share_redpacket_days_limit' => $redPacket['share_redpacket_days_limit'], 'follow_redpacket_condition' => 0, 'follow_redpacket_discount' => $follow_redpacket, 'follow_redpacket_days_limit' => $redPacket['follow_redpacket_days_limit'], 'addtime' => TIMESTAMP);
		pdo_insert('tiny_wmall_shareredpacket_invite_record', $insert);
		mload()->model('redPacket');
		$params = array('activity_id' => $redPacket['id'], 'title' => '新用户专享红包', 'channel' => 'shareRedpacket', 'type' => 'mallMewMember', 'uid' => $new_member['uid'], 'discount' => $follow_redpacket, 'condition' => 0, 'days_limit' => $redPacket['follow_redpacket_days_limit']);
		$status = redPacket_grant($params);

		if (is_error($status)) {
			imessage($status, '', 'ajax');
		}

		imessage(error(0, $new_member['uid']), '', 'ajax');
	}

	$uid = intval($_GPC['u']);
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('nickname', 'avatar', 'addtime'));

	if (empty($member)) {
		imessage('分享人不存在', '', 'info');
	}

	$days_format = ceil(($member['addtime'] - time()) / 86400) . '天';
	$fansInfo = mc_oauth_userinfo();

	if (is_error($fansInfo)) {
		imessage('微信授权失败,请重新访问该链接', 'refresh', 'info');
	}

	$is_exist = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'openid' => $fansInfo['openid']));

	if (!empty($is_exist)) {
		header('location:' . imurl('shareRedpacket/share/repeat'));
		exit();
	}
}

if ($op == 'success') {
	$uid = intval($_GPC['uid']);
	$data = pdo_get('tiny_wmall_shareredpacket_invite_record', array('uniacid' => $_W['uniacid'], 'follow_uid' => $uid));
}

include itemplate('share');

?>
