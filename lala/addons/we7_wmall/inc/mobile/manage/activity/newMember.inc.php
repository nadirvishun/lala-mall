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
$sid = intval($_GPC['__mg_sid']);

if($ta == 'index') {
	$_W['page']['title'] = '门店新用户';
	if($_W['isajax']) {
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
		$back = trim($_GPC['back']);
		if(empty($back)) {
			imessage(error(-1, '活动金额不能为空'), '', 'ajax');
		}
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => "本店新用户立减{$back}元",
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'newMember',
			'status' => 1,
			'data' => array(
				'back' => $back,
			),
		);
		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置新用户立减优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'newMember');
}

if($ta == 'del') {
	activity_del($sid, 'newMember');
	imessage(error(0, '撤销活动成功'), referer(), 'ajax');
}

include itemplate('activity/newMember');
