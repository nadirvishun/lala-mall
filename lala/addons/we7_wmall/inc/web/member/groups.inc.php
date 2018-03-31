<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
$config = get_system_config('member.group_update_mode');

if($op == 'index') {
	$_W['page']['title'] = '顾客等级';
	$groups = pdo_fetchall('select * from' . tablename('tiny_wmall_member_groups') . 'where uniacid = :uniacid', array('uniacid' => $_W['uniacid']));
}

if($op == 'post') {
	$_W['page']['title'] = '编辑顾客等级';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$group = pdo_get('tiny_wmall_member_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']);
		if(empty($title)) {
			imessage(error(-1, '等级名称不能为空'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'group_condition' => floatval($_GPC['group_condition']),
		);
		if(empty($group['id'])) {
			pdo_insert('tiny_wmall_member_groups', $data);
		} else {
			pdo_update('tiny_wmall_member_groups', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		set_system_config('member.group', '');
		imessage(error(0, '顾客等级更新成功'), iurl('member/groups/index'), 'ajax');
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_member_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
	set_system_config('member.group', '');
	imessage(error(0, '删除顾客等级成功'), iurl('member/groups/index'), 'ajax');
}

include itemplate('member/groups');