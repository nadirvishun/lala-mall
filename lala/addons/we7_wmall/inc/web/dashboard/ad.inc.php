<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'post') {
	$_W['page']['title'] = '编辑引导页';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$slide = pdo_get('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id, 'type' => 1));

		if (empty($slide)) {
			imessage('广告不存在或已删除', referer(), 'error');
		}
	}

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax'));
		$data = array('uniacid' => $_W['uniacid'], 'title' => $title, 'thumb' => trim($_GPC['thumb']), 'link' => trim($_GPC['link']), 'type' => 1, 'status' => intval($_GPC['status']), 'displayorder' => intval($_GPC['displayorder']));

		if (!empty($slide)) {
			pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_slide', $data);
		}

		imessage(error(0, '编辑引导页成功'), iurl('dashboard/ad/list'), 'ajax');
	}
}

if ($op == 'list') {
	$_W['page']['title'] = '引导页列表';
	$condition = ' where uniacid = :uniacid and type = 1';
	$params = array(':uniacid' => $_W['uniacid']);
	$agentid = intval($_GPC['agentid']);

	if (0 < $agentid) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_slide') . $condition . ' order by displayorder desc', $params);
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除引导页成功'), '', 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_slide', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'displayorder') {
	$id = intval($_GPC['id']);
	$displayorder = intval($_GPC['displayorder']);
	pdo_update('tiny_wmall_slide', array('displayorder' => $displayorder), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('dashboard/ad');

?>
