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

		imessage('编辑成功', iurl('merchant/notice/list'), 'success');
	}

	$notices = pdo_fetchall('select * from' . tablename('tiny_wmall_notice') . 'where uniacid = :uniacid and type = :type order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':type' => 'store'));
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
		$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'content' => htmlspecialchars_decode($_GPC['content']), 'link' => trim($_GPC['link']), 'displayorder' => intval($_GPC['displayorder']), 'status' => intval($_GPC['status']), 'addtime' => TIMESTAMP, 'type' => 'store');

		if (!empty($notice['id'])) {
			pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_notice', $data);
		}

		imessage(error(0, '更新公告成功'), iurl('merchant/notice/list'), 'ajax');
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除公告成功'), iurl('merchant/notice/list'), 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$data = array('status' => intval($_GPC['status']));
	pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('merchant/notice');

?>
