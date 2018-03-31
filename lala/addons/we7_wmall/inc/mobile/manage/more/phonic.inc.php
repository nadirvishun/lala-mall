<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '语音提示设置';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');
$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $_W['manager']['id']));
$prefs = iunserializer($clerk['prefs']);

if ($ta == 'phonic_times') {
	$type = trim($_GPC['type']);

	if (!empty($type)) {
		$times = intval($_GPC['times']);
		$prefs[$type] = $times;
		pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	}

	imessage(error(0, '设置播放次数成功'), referer(), 'ajax');
}

if ($ta == 'voice_status') {
	$voice_status = intval($_GPC['voice_status']);
	$prefs['voice_status'] = $voice_status;
	pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	imessage(error(0, '语音提示设置成功'), '', 'ajax');
}

if ($ta == 'vibrance_status') {
	$vibrance_status = intval($_GPC['vibrance_status']);
	$prefs['vibrance_status'] = $vibrance_status;
	pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	imessage(error(0, '振动提示设置成功'), '', 'ajax');
}

include itemplate('more/phonic');

?>
