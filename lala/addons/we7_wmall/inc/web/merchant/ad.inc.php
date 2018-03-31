<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '广告列表';

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['titles'][$k]), 'displayorder' => intval($_GPC['displayorders'][$k]));
				pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}

		imessage(error(0, '编辑成功'), iurl('merchant/ad/list'), 'ajax');
	}

	$ads = pdo_fetchall('select * from ' . tablename('tiny_wmall_slide') . ' where uniacid = :uniacid and type = :type order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':type' => 3));
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑广告';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$ad = pdo_get('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id, 'type' => 3));
	}

	if ($_W['ispost']) {
		$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'thumb' => trim($_GPC['thumb']), 'link' => trim($_GPC['link']), 'displayorder' => intval($_GPC['displayorder']), 'status' => intval($_GPC['status']), 'type' => 3);

		if (!empty($ad['id'])) {
			pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_slide', $data);
		}

		imessage(error(0, '更新广告成功'), iurl('merchant/ad/list'), 'ajax');
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除广告成功'), iurl('merchant/ad/list'), 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$data = array('status' => intval($_GPC['status']));
	pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('merchant/ad');

?>
