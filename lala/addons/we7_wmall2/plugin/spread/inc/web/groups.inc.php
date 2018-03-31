<?php
/**
 * 外送系统
 * @author 微猫源码
 * @QQ 2058430070
 * @url http://www.weixin2015.cn/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$config = $_config_plugin['relate'];
if($op == 'index') {
	$_W['page']['title'] = '推广员等级';
	$groups = pdo_fetchall('select * from ' . tablename('tiny_wmall_spread_groups') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
}
if($op == 'post') {
	$_W['page']['title'] = '编辑推广员等级';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$ad = pdo_get('tiny_wmall_spread_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'commission1' => floatval($_GPC['commission1']),
			'commission2' => floatval($_GPC['commission2']),
			'group_condition' => floatval($_GPC['group_condition']),
		);
		if(!empty($ad['id'])) {
			pdo_update('tiny_wmall_spread_groups', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_spread_groups', $data);
		}
		imessage(error(0, '更新推广员等级成功'), iurl('spread/groups/index'), 'ajax');
	}
}
if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_spread_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除推广员等级成功'), iurl('spread/groups/index'), 'ajax');
}
include itemplate('groups');