<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'index';
if($ta == 'index') {
	$_W['page']['title'] = '商户中心';
	$sid = intval($_GPC['__mg_sid']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('title'));
	if($_W['ispost']) {
		$type = trim($_GPC['type']);
		$value = intval(!$_W['manager']['extra'][$type]);
		clerk_set_extra($type, $value, $_W['manager']['id'], $sid);
		imessage(error(0, '修改成功'), imurl('manage/more/index'), 'ajax');
	}
}

include itemplate('more/index');
