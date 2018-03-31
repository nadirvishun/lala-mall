<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '问题列表';

	if (checksubmit('submit')) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}

			imessage('保存修改成功', iurl('config/help/list'), 'success');
		}
	}

	$condition = ' WHERE uniacid = :uniacid order by displayorder desc, id asc';
	$params[':uniacid'] = $_W['uniacid'];
	$helps = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_help') . $condition, $params);
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑问题';
	$id = $_GPC['id'];
	$item = pdo_get('tiny_wmall_help', array('id' => $id));

	if ($_W['ispost']) {
		$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'content' => htmlspecialchars_decode($_GPC['content']), 'displayorder' => intval($_GPC['displayorder']));

		if ($id) {
			pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_help', $data);
		}

		imessage(error(0, '编辑问题成功'), iurl('config/help/list'), 'ajax');
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_help', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除内容成功'), referer(), 'ajax');
}

include itemplate('config/help');

?>
