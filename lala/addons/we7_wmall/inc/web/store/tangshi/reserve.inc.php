<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$_W['page']['title'] = '桌台预定开放时间列表';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$reserves = pdo_fetchall('select * from ' . tablename('tiny_wmall_reserve') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include itemplate('store/tangshi/reserve');
}

if ($ta == 'post') {
	$_W['page']['title'] = '新建桌台预定开放时间';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$time = (trim($_GPC['time']) ? trim($_GPC['time']) : imessage(error(0, '预定时间段不能为空'), referer(), 'ajax'));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'time' => $time, 'table_cid' => intval($_GPC['table_cid']), 'addtime' => time());

		if (!empty($id)) {
			pdo_update('tiny_wmall_reserve', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_reserve', $data);
		}

		imessage(error(0, '编辑预定时间段成功'), iurl('store/tangshi/reserve/list'), 'ajax');
	}

	if (0 < $id) {
		$item = pdo_get('tiny_wmall_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

		if (empty($item)) {
			imessage('预定时间段不存在或已删除', referer(), 'error');
		}
	}

	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

	if (empty($categorys)) {
		imessage('创建预订开放时间段前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');
	}

	include itemplate('store/tangshi/reserve');
}

if ($ta == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}

	imessage(error(0, '删除预订开放时间段成功'), referer(), 'ajax');
}

if ($ta == 'batch_post') {
	$_W['page']['title'] = '批量创建';

	if ($_W['ispost']) {
		$start = strtotime($_GPC['time']);
		$i = 0;

		while ($i < $_GPC['num']) {
			$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'time' => date('H:i', $start + ($i * $_GPC['time_space'] * 60)), 'table_cid' => intval($_GPC['table_cid']), 'addtime' => time());
			pdo_insert('tiny_wmall_reserve', $data);
			++$i;
		}

		imessage(error(0, '创建预定时间段成功'), iurl('store/tangshi/reserve/list'), 'ajax');
	}

	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

	if (empty($categorys)) {
		imessage('创建预订开放时间段前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');
	}

	include itemplate('store/tangshi/reserve');
}

?>
