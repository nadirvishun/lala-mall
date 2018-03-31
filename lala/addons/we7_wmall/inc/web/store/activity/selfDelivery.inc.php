<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('activity');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '自提优惠';
	if($_W['ispost']) {
		$starttime = trim($_GPC['starttime']);
		if(empty($starttime)) {
			imessage(error(-1, '活动开始时间不能为空'), '', 'ajax');
		}
		$endtime = trim($_GPC['endtime']);
		if(empty($endtime)) {
			imessage(error(-1, '活动结束时间不能为空'), '', 'ajax');
		}
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);
		if($starttime >= $endtime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}
		$data = array();
		$title = array();
		if(!empty($_GPC['condition'])) {
			foreach ($_GPC['condition'] as $key => $value) {
				$condition = intval($value);
				$back = trim($_GPC['back'][$key]);

				if($condition && $back) {
					$data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
						'plateform_charge' => 0,
						'store_charge' => $back,
					);

					if(!empty($_W['ismanager'])) {
						$data[$condition]['agent_charge'] = trim($_GPC['agent_charge'][$key]);
						$data[$condition]['plateform_charge'] = trim($_GPC['plateform_charge'][$key]);
						if($data[$condition]['agent_charge'] > $back) {
							$data[$condition]['agent_charge'] = $back;
							$data[$condition]['plateform_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} elseif($data[$condition]['plateform_charge'] > $back) {
							$data[$condition]['plateform_charge'] = $back;
							$data[$condition]['agent_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} elseif($data[$condition]['plateform_charge'] + $data[$condition]['agent_charge'] > $back) {
							$data[$condition]['plateform_charge'] = $back - $data[$condition]['agent_charge'];
							$data[$condition]['store_charge'] = 0;
						} else {
							$data[$condition]['store_charge'] = round($back - $data[$condition]['agent_charge'] - $data[$condition]['plateform_charge'], 2);
						}
						if($data[$condition]['store_charge'] < 0) {
							$data[$condition]['store_charge'] = 0;
						}
					} elseif(!empty($_W['isagenter'])) {
						$data[$condition]['agent_charge'] = trim($_GPC['agent_charge'][$key]);
						if($data[$condition]['agent_charge'] > $back) {
							$data[$condition]['agent_charge'] = $back;
							$data[$condition]['plateform_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} else {
							$data[$condition]['store_charge'] = round($back - $data[$condition]['agent_charge'], 2);
						}
						if($data[$condition]['store_charge'] < 0) {
							$data[$condition]['store_charge'] = 0;
						}
					}

					$title[] = "满{$condition}元打{$back}折";
				}

			}
		}
		if(empty($data)) {
			imessage(error(-1, '自提优惠不能为空'), '', 'ajax');
		}

		$title = implode(',', $title);
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'selfDelivery',
			'status' => 1,
			'data' => iserializer($data),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置自提优惠成功'), referer(), 'ajax');
	}
	$activity = activity_get($sid, 'selfDelivery');

	if(!empty($activity)) {
		foreach($activity['data'] as &$row) {
			if(!is_array($row)) {
				continue;
			}
			$data[] = $row;
		}
		$activity['data'] = $data;
	}

	$count = count($activity['data']);
	for($i = 0; $i < 4 - $count; $i++) {
		$activity['data'][] = array(
			'condition' => '',
			'back' => '',
		);
	}
}

if($ta == 'del') {
	activity_del($sid, 'selfDelivery');
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('store/activity/selfDelivery');