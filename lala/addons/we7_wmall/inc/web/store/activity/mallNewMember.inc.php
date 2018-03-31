<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('activity');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '平台新用户';

	if ($_W['ispost']) {
		if (empty($_W['ismanager'])) {
			imessage(error(-1, '您没有权限进行该操作'), '', 'ajax');
		}

		$starttime = trim($_GPC['starttime']);

		if (empty($starttime)) {
			imessage(error(-1, '活动开始时间不能为空'), '', 'ajax');
		}

		$endtime = trim($_GPC['endtime']);

		if (empty($endtime)) {
			imessage(error(-1, '活动结束时间不能为空'), '', 'ajax');
		}

		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);

		if ($endtime <= $starttime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}

		$back = trim($_GPC['back']);

		if (empty($back)) {
			imessage(error(-1, '立减活动不能为空'), '', 'ajax');
		}

		$activity = array(
			'uniacid'   => $_W['uniacid'],
			'sid'       => $sid,
			'title'     => '平台新用户立减' . $back . '元',
			'starttime' => $starttime,
			'endtime'   => $endtime,
			'type'      => 'mallNewMember',
			'status'    => 1,
			'data'      => array('back' => $back, 'plateform_charge' => trim($_GPC['plateform_charge']), 'store_charge' => 0)
			);

		if ($back < $activity['data']['plateform_charge']) {
			$activity['data']['plateform_charge'] = $back;
		}

		$activity['data']['store_charge'] = round($back - $activity['data']['plateform_charge'], 2);
		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);

		if (is_error($status)) {
			imessage($status, '', 'ajax');
		}

		imessage(error(0, '设置新用户立减优惠成功'), 'refresh', 'ajax');
	}

	$activity = activity_get($sid, 'mallNewMember');
	if (empty($_W['ismanager']) && empty($activity)) {
		imessage('该门店没有设置平台新用户优惠活动,如需设置,请联系平台负责人设置', referer(), 'info');
	}
}

if ($ta == 'del') {
	activity_del($sid, 'mallNewMember');
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('store/activity/mallNewMember');

?>
