<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('activity');
mload()->model('coupon');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$sid = intval($_GPC['__mg_sid']);
if($ta == 'index') {
	$_W['page']['title'] = '已创建活动';
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;

	$activity = activity_getall($sid, $status);
	foreach ($activity as $key => &$val) {
		$val['until'] = round(($val['endtime'] - time()) / 86400);
		if($key == 'couponGrant' || $key == 'couponCollect') {
			$val['coupon_detail'] = coupon_fetch(0, $val['id']);
		}
		if($key == 'bargain'){
			unset($activity['bargain']);
		}
	}
}

if($ta == 'del') {
	$type = $_GPC['type'];
	activity_del($sid, $type);
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('activity/list');