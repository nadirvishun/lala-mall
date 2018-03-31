<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'set');

if ($op == 'set') {
	$_W['page']['title'] = '短信平台';

	if ($_W['ispost']) {
		$sms = array('status' => intval($_GPC['status']), 'version' => intval($_GPC['version']), 'key' => trim($_GPC['key']), 'secret' => trim($_GPC['secret']), 'sign' => trim($_GPC['sign']));
		set_system_config('sms.set', $sms);
		imessage(error(0, '短信平台设置成功'), referer(), 'ajax');
	}

	$sms = $_config['sms']['set'];
	include itemplate('config/sms');
}

if ($op == 'template') {
	$_W['page']['title'] = '短信平台';

	if ($_W['ispost']) {
		$template = array('verify_code_tpl' => trim($_GPC['verify_code_tpl']));
		set_system_config('sms.template', $template);
		imessage(error(0, '短信模板设置成功'), referer(), 'ajax');
	}

	$sms = $_config['sms']['template'];
	include itemplate('config/sms');
}

?>
