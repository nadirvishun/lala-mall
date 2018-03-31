<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '导航图标列表';

	if (checksubmit('submit')) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_store_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}

			imessage('编辑成功', iurl('dashboard/nav/list'), 'success');
		}
	}

	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$agentid = intval($_GPC['agentid']);

	if (0 < $agentid) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_category') . $condition, $params);
	$navs = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_category') . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params, 'id');
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除导航图标成功'), iurl('dashboard/nav/list'), 'ajax');
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑导航图标';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$category = pdo_get('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));

		if (!empty($category)) {
			$category['nav'] = (array) iunserializer($category['nav']);
			$category['slide'] = (array) iunserializer($category['slide']);

			if (!empty($category['slide'])) {
				$category['slide'] = array_sort($category['slide'], 'displayorder', SORT_DESC);
			}
		}
	}

	if (empty($category)) {
		$category = array(
			'slide_status' => 0,
			'slide'        => array(),
			'nav_status'   => 0,
			'nav'          => array()
			);
	}

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax'));
		$nav = array();

		if (!empty($_GPC['nav_thumb'])) {
			foreach ($_GPC['nav_thumb'] as $k => $v) {
				if (empty($_GPC['nav_title'][$k])) {
					continue;
				}

				$nav[] = array('title' => trim($_GPC['nav_title'][$k]), 'sub_title' => trim($_GPC['nav_sub_title'][$k]), 'link' => trim($_GPC['nav_links'][$k]), 'thumb' => trim($v));
			}
		}

		$slide = array();

		if (!empty($_GPC['slide_image'])) {
			foreach ($_GPC['slide_image'] as $k => $v) {
				if (empty($v)) {
					continue;
				}

				$slide[] = array('thumb' => trim($v), 'link' => trim($_GPC['slide_links'][$k]), 'displayorder' => trim($_GPC['slide_displayorder'][$k]));
			}
		}

		$data = array('uniacid' => $_W['uniacid'], 'title' => $title, 'thumb' => trim($_GPC['thumb']), 'link' => trim($_GPC['link']), 'displayorder' => intval($_GPC['displayorder']), 'nav_status' => intval($_GPC['nav_status']), 'slide_status' => intval($_GPC['slide_status']), 'nav' => iserializer($nav), 'slide' => iserializer($slide));

		if (empty($_GPC['id'])) {
			pdo_insert('tiny_wmall_store_category', $data);
		}
		else {
			pdo_update('tiny_wmall_store_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}

		imessage(error(0, '编辑导航图标成功'), iurl('dashboard/nav/list'), 'ajax');
	}
}

include itemplate('dashboard/nav');

?>
