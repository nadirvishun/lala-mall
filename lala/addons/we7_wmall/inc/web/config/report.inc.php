<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '举报类型';

	if ($_W['ispost']) {
		$report = array();

		foreach ($_GPC['report'] as $value) {
			$value = trim($value);

			if (empty($value)) {
				continue;
			}

			$report[] = $value;
		}

		if (!empty($report)) {
			set_system_config('report', $report);
		}

		imessage(error(0, '设置举报类型成功'), referer(), 'ajax');
	}

	$report = $_config['report'];
}

include itemplate('config/report');

?>
