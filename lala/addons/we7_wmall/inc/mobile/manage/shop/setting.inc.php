<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$_W['page']['title'] = '店铺设置';
if($ta == 'business_status') {
	$is_in_business =  intval($_GPC['is_in_business']);
	pdo_update('tiny_wmall_store', array('is_in_business' => $is_in_business), array('uniacid' => $_W['uniacid'], 'id' => $store['id']));
	$info = array('关店成功', '开店成功');
	imessage(error(0, $info[$is_in_business]), '', 'ajax');
}

if($ta == 'business_time') {
	$store = pdo_fetch('select id, business_hours from' . tablename('tiny_wmall_store') . 'where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $store['id']));
	$store['business_hours'] = iunserializer($store['business_hours']);
	if($_W['isajax']) {
		if(!empty($_GPC['business_time'])) {
			$hour = array();
			foreach ($_GPC['business_time'] as $v) {
				$v['business_start_hours'] = str_replace('：', ':', trim($v['business_start_hours']));
				if(!strexists($v['business_start_hours'], ':')) {
					$v['business_start_hours'] .= ':00';
				}
				$v['business_end_hours'] = str_replace('：', ':', trim($v['business_end_hours']));
				if(!strexists($v['business_end_hours'], ':')) {
					$v['business_start_hours'] .= ':00';
				}
				$hour[] = array('s' => $v['business_start_hours'], 'e' => $v['business_end_hours']);
			}
			$data['business_hours'] = iserializer($hour);
			pdo_update('tiny_wmall_store', $data, array('uniacid' => $_W['uniacid'], 'id' => $store['id']));
			store_business_hours_init($store['id']);
			imessage(error(0, '营业时间修改成功'), 'refresh', 'ajax');
		}
	}
	include itemplate('shop/settingOp');
	die();
}
include itemplate('shop/setting');