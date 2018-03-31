<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('table');
$colors = array('block-gray', 'block-red', 'block-primary', 'block-success', 'block-orange');
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'category_list');

if ($ta == 'category_list') {
	$_W['page']['title'] = '桌台类型';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$tables = pdo_fetchall('select *, count(*) as num from ' . tablename('tiny_wmall_tables') . ' where uniacid = :uniacid and sid = :sid group by cid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'cid');
	include itemplate('store/tangshi/category');
}

if ($ta == 'category_post') {
	$_W['page']['title'] = '编辑桌台类型';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage('名称不能为空', '', 'error'));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $title, 'limit_price' => trim($_GPC['limit_price']), 'reservation_price' => trim($_GPC['reservation_price']));

		if (!empty($id)) {
			pdo_update('tiny_wmall_tables_category', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_tables_category', $data);
		}

		imessage(error(0, '编辑餐桌类型成功'), iurl('store/tangshi/table/category_list'), 'ajax');
	}

	if (0 < $id) {
		$item = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

		if (empty($item)) {
			imessage('餐桌类型不存在或已删除', referer(), 'error');
		}
	}

	include itemplate('store/tangshi/category');
}

if ($ta == 'category_del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}

	imessage(error(0, '删除桌台类型成功'), referer(), 'ajax');
}

if ($ta == 'table_del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}

	imessage(error(0, '删除桌台成功'), referer(), 'ajax');
}

if ($ta == 'table_post') {
	$_W['page']['title'] = '餐桌管理';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

	if (empty($categorys)) {
		imessage('创建桌台前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');
	}

	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage('桌台号不能为空', '', 'error'));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $title, 'guest_num' => intval($_GPC['guest_num']), 'cid' => intval($_GPC['cid']), 'displayorder' => intval($_GPC['displayorder']));

		if (!empty($id)) {
			pdo_update('tiny_wmall_tables', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_tables', $data);
			$table_id = pdo_insertid();
			imessage(error(0, '添加桌台成功, 生成桌台二维码中'), iurl('store/common/qrcode/build', array('store_id' => $sid, 'table_id' => $table_id, 'type' => 'table')), 'ajax');
		}

		imessage(error(0, '编辑桌台成功'), iurl('store/tangshi/table/list'), 'ajax');
	}

	if (0 < $id) {
		$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

		if (empty($item)) {
			imessage('桌台不存在或已删除', referer(), 'error');
		}
	}

	include itemplate('store/tangshi/table');
}

if ($ta == 'list') {
	$_W['page']['title'] = '餐桌列表';
	$_GPC['t'] = $_GPC['t'] ? $_GPC['t'] : 'list';
	$table_status = table_status();
	$condition = 'where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$cid = intval($_GPC['cid']);

	if (0 < $cid) {
		$condition .= ' and cid = :cid';
		$params[':cid'] = $cid;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and title like :title';
		$params[':title'] = '%' . $keyword . '%';
	}

	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables') . ' ' . $condition . ' order by displayorder desc', $params);

	if (!empty($data)) {
		foreach ($data as &$row) {
			$row['sys_url'] = imurl('wmall/store/table/index', array('table_id' => $row['id'], 'sid' => $sid, 'mode' => 1, 'f' => 1), true, true);

			if (!empty($row['qrcode'])) {
				$row['qrcode'] = iunserializer($row['qrcode']);
				$row['wx_url'] = $row['qrcode']['url'];
			}
		}
	}

	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	include itemplate('store/tangshi/table');
}

if ($ta == 'table_status') {
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

	if (empty($item)) {
		exit('桌台不存在或已删除');
	}

	pdo_update('tiny_wmall_tables', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	exit('success');
}

?>
