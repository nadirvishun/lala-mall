<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('activity');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '门店新用户';

	if ($_W['ispost']) {
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
			'title'     => '本店新用户立减' . $back . '元',
			'starttime' => $starttime,
			'endtime'   => $endtime,
			'type'      => 'newMember',
			'status'    => 1,
			'data'      => array('back' => $back, 'plateform_charge' => 0, 'store_charge' => $back)
			);

		if (!empty($_W['ismanager'])) {
			$activity['data']['agent_charge'] = trim($_GPC['agent_charge']);
			$activity['data']['plateform_charge'] = trim($_GPC['plateform_charge']);

			if ($back < $activity['data']['agent_charge']) {
				$activity['data']['agent_charge'] = $back;
				$activity['data']['plateform_charge'] = 0;
				$activity['data']['store_charge'] = 0;
			}
			else if ($back < $activity['data']['plateform_charge']) {
				$activity['data']['plateform_charge'] = $back;
				$activity['data']['agent_charge'] = 0;
				$activity['data']['store_charge'] = 0;
			}
			else if ($back < ($activity['data']['plateform_charge'] + $activity['data']['agent_charge'])) {
				$activity['data']['plateform_charge'] = $back - $activity['data']['agent_charge'];
				$activity['data']['store_charge'] = 0;
			}
			else {
				$activity['data']['store_charge'] = round($back - $activity['data']['agent_charge'] - $activity['data']['plateform_charge'], 2);
			}

			if ($activity['data']['store_charge'] < 0) {
				$activity['data']['store_charge'] = 0;
			}
		}
		else {
			if (!empty($_W['isagenter'])) {
				$activity['data']['agent_charge'] = trim($_GPC['agent_charge']);

				if ($back < $activity['data']['agent_charge']) {
					$activity['data']['agent_charge'] = $back;
					$activity['data']['plateform_charge'] = 0;
					$activity['data']['store_charge'] = 0;
				}
				else {
					$activity['data']['store_charge'] = round($back - $activity['data']['agent_charge'], 2);
				}

				if ($activity['data']['store_charge'] < 0) {
					$activity['data']['store_charge'] = 0;
				}
			}
		}

		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);

		if (is_error($status)) {
			imessage($status, '', 'ajax');
		}

		imessage(error(0, '设置新用户立减优惠成功'), 'refresh', 'ajax');
	}

	$activity = activity_get($sid, 'newMember');
}

if ($ta == 'del') {
	activity_del($sid, 'newMember');
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('store/activity/newMember');

?>
