<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('activity');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '店铺活动类型';
	$activitys = activity_getall($sid, 1);
	$stats = activity_stat();
}

include itemplate('store/activity/index');

?>
