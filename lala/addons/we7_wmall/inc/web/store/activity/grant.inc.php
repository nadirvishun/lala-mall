<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('activity');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '满赠优惠';
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
					);
					$title[] = "满{$condition}元赠{$back}";
				}
			}
		}
		if(empty($data)) {
			imessage(error(-1, '满赠活动不能为空'), '', 'ajax');
		}
		$title = implode(',', $title);
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'grant',
			'status' => 1,
			'data' => iserializer($data),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置满赠优惠优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'grant');

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
	activity_del($sid, 'grant');
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('store/activity/grant');