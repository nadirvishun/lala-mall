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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'post';
$sid = intval($_GPC['__mg_sid']);

if($ta == 'post') {
	$_W['page']['title'] = '进店领券';
	$type_limit = array(
		array('id' => 1, 'title' => '新老用户通用'),
		array('id' => 2, 'title' => '新用户'),
	);
	if($_W['isajax']) {
		$activitytitle = trim($_GPC['title']);
		if(empty($activitytitle)) {
			imessage(error(-1, '活动名称不能为空'), '', 'ajax');
		}
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
		$amount = trim($_GPC['amount']);
		if(empty($amount)) {
			imessage(error(-1, '券包总数不能为空'), '', 'ajax');
		}
		$type_limit = trim($_GPC['type_limit']);
		if(empty($type_limit)) {
			imessage(error(-1, '请选择面向人群'), '', 'ajax');
		}
		if(!empty($_GPC['coupon'])) {
			$discount = array();
			foreach ($_GPC['coupon'] as $coupon) {
				$discount[] = $coupon['discount'];
			}
			if(empty($discount)) {
				imessage(error(-1, '请先添加优惠券'), '', 'ajax');
			}
			$min = min($discount);
			$max = max($discount);
			if($min == $max) {
				$title = "进店可领{$min}元代金券";
			} else {
				$title = "进店可领{$min}~{$max}元代金券";
			}
		} else {
			imessage(error(-1, '请先添加优惠券'), '', 'ajax');
		}
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponCollect',
			'status' => 1,
			'data' => iserializer($_GPC['coupon']),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		$coupon = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'activity_id' => $status,
			'title' => $activitytitle,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponCollect',
			'type_limit' => $type_limit,
			'status' => 1,
			'amount' => $amount,
			'coupons' => iserializer($_GPC['coupon']),
		);
		pdo_insert('tiny_wmall_activity_coupon', $coupon);
		imessage(error(0, '进店领券活动添加成功'), 'refresh', 'ajax');
	}

}

include itemplate('activity/couponCollect');