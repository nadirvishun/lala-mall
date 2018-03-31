<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '资讯分类';

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_news_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}

		imessage(error(0, '编辑成功'), referer(), 'ajax');
	}

	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_news_category') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . $condition . ' ORDER BY id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'post') {
	$_W['page']['title'] = '添加资讯分类';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$item = pdo_get('tiny_wmall_news_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	if ($_W['ispost']) {
		$_GPC['title'] = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '分类名称不能为空'), '', 'ajax');
		$data = array('uniacid' => $_W['uniacid'], 'title' => $_GPC['title'], 'displayorder' => intval($_GPC['displayorder']));

		if (!$id) {
			pdo_insert('tiny_wmall_news_category', $data);
		}
		else {
			pdo_update('tiny_wmall_news_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}

		imessage(error(0, '编辑分类成功'), iurl('merchant/newsCategory/list'), 'ajax');
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_news_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除分类成功'), '', 'ajax');
}

include itemplate('merchant/newsCategory');

?>
