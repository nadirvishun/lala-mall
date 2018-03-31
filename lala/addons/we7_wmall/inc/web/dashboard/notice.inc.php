<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '公告列表';

	if (checksubmit('submit')) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['titles'][$k]), 'link' => trim($_GPC['links'][$k]), 'displayorder' => intval($_GPC['displayorders'][$k]));
				pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}

		imessage('编辑成功', iurl('dashboard/notice/list'), 'success');
	}

	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(':uniacid' => $_W['uniacid'], ':type' => 'member');
	$agentid = intval($_GPC['agentid']);

	if (0 < $agentid) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$notices = pdo_fetchall('select * from' . tablename('tiny_wmall_notice') . $condition . ' order by displayorder desc', $params);
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑公告';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$notice = pdo_get('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	if (empty($notice)) {
		$notice = array('status' => 1);
	}

	if ($_W['ispost']) {
		$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'thumb' => trim($_GPC['thumb']), 'description' => trim($_GPC['description']), 'content' => htmlspecialchars_decode($_GPC['content']), 'link' => trim($_GPC['link']), 'displayorder' => intval($_GPC['displayorder']), 'status' => intval($_GPC['status']), 'addtime' => TIMESTAMP, 'type' => 'member');

		if (!empty($notice['id'])) {
			pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_notice', $data);
		}

		imessage(error(0, '更新公告成功'), iurl('dashboard/notice/list'), 'ajax');
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除公告成功'), iurl('dashboard/notice/list'), 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$data = array('status' => intval($_GPC['status']));
	pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('dashboard/notice');

?>
