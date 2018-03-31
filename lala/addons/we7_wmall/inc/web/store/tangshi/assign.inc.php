<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('table');
$colors = array('block-gray', 'block-red', 'block-primary', 'block-success', 'block-orange');
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'board_list');

if ($ta == 'queue_list') {
	$_W['page']['title'] = '队列列表';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include itemplate('store/tangshi/queue');
}

if ($ta == 'queue_post') {
	$_W['page']['title'] = '编辑队列';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$title = (trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(0, '队列名称不能为空'), referer(), 'ajax'));
		$guest_num = (intval($_GPC['guest_num']) ? intval($_GPC['guest_num']) : imessage(error(0, '客人数量少于等于多少人排入此队列必须大于0'), referer(), 'ajax'));
		$starttime = trim($_GPC['starttime']);
		$endtime = trim($_GPC['endtime']);

		if (strtotime(date('Y-m-d ') . $endtime) < strtotime(date('Y-m-d ') . $starttime)) {
			imessage(error(0, '开放排队时间不能大于结束排队时间'), referer(), 'ajax');
		}

		$notify_num = (intval($_GPC['notify_num']) ? intval($_GPC['notify_num']) : imessage(error(0, '提前通知人数必须大于0'), referer(), 'ajax'));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $title, 'guest_num' => $guest_num, 'notify_num' => $notify_num, 'starttime' => trim($_GPC['starttime']), 'endtime' => trim($_GPC['endtime']), 'prefix' => trim($_GPC['prefix']), 'status' => intval($_GPC['status']));

		if (!empty($id)) {
			pdo_update('tiny_wmall_assign_queue', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_assign_queue', $data);
		}

		imessage(error(0, '编辑队列成功'), iurl('store/tangshi/assign/queue_list'), 'ajax');
	}

	if (0 < $id) {
		$item = pdo_get('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

		if (empty($item)) {
			imessage('队列不存在或已删除', referer(), 'error');
		}
	}
	else {
		$item = array('starttime' => '00:00', 'endtime' => '23:59', 'status' => 1);
	}

	include itemplate('store/tangshi/queue');
}

if ($ta == 'queue_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'queue_id' => $id));
	imessage('删除队列成功', referer(), 'success');
}

if ($ta == 'board_list') {
	$_W['page']['title'] = '客人队列';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

	if (!empty($data)) {
		$wait = pdo_fetchall('select count(*) as num, queue_id from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 group by queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'queue_id');
	}

	include itemplate('store/tangshi/board');
}

if ($ta == 'board_detail') {
	$_W['page']['title'] = '队列详情';
	$queue_id = intval($_GPC['id']);
	$queue = pdo_get('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));

	if (empty($queue)) {
		imessage('队列不存在或已删除', referer(), 'error');
	}

	$colors = assign_board_status();
	$condition = '';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id);
	$status = (isset($_GPC['status']) ? intval($_GPC['status']) : -1);

	if ($status != -1) {
		$condition .= ' and status = :status';
		$params['status'] = $status;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and queue_id = :queue_id ' . $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and queue_id = :queue_id ' . $condition . ' order by id asc limit ' . (($pindex - 1) * $psize) . ', ' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	include itemplate('store/tangshi/board');
}

if ($ta == 'board_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$board = assign_board_fetch($id);

	if (empty($board)) {
		imessage(error(-1, '排队不存在'), '', 'ajax');
	}

	pdo_update('tiny_wmall_assign_board', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	$status = assign_notice($sid, $id, $status);

	if (!is_error($status)) {
		pdo_update('tiny_wmall_assign_board', array('is_notify' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}

	assign_notice_queue($board['id'], $board['queue_id']);
	imessage(error(0, '改变状态成功'), referer(), 'ajax');
}

if ($ta == 'board_notity') {
	if ($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		$board = assign_board_fetch($id);

		if (empty($board)) {
			imessage(error(0, '排队不存在'), referer(), 'ajax');
		}

		$status = assign_notice($sid, $id, 5);

		if (!is_error($status)) {
			pdo_update('tiny_wmall_assign_board', array('is_notify' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
			imessage(error(0, '通知成功'), referer(), 'ajax');
		}

		imessage(error(-1, '通知失败:' . $status['message']), referer(), 'ajax');
	}
}

if ($ta == 'board_del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		$id = intval($id);

		if ($id <= 0) {
			continue;
		}

		pdo_delete('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}

	imessage(error(0, '删除排号成功'), referer(), 'ajax');
}

if ($ta == 'board_post') {
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));

	if (empty($item)) {
		imessage('排号不存在或已经删除', referer(), 'error');
	}

	if ($_W['ispost']) {
		$number = (trim($_GPC['number']) ? trim($_GPC['number']) : imessage(error(0, '号码不能为空'), referer(), 'ajax'));
		$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(0, '手机不能为空'), referer(), 'ajax'));
		$data = array('number' => $number, 'mobile' => $mobile, 'guest_num' => intval($_GPC['guest_num']));
		pdo_update('tiny_wmall_assign_board', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		imessage(error(0, '更新客人队列成功'), iurl('store/tangshi/assign/board_detail', array('id' => $item['queue_id'])), 'ajax');
	}

	include itemplate('store/tangshi/board');
}

if ($ta == 'set') {
	$_W['page']['title'] = '排号设置';
	$_GPC['t'] = trim($_GPC['t']) ? trim($_GPC['t']) : 'mode';
	$store = store_fetch($sid);

	if ($_W['ispost']) {
		$data = array('assign_mode' => intval($_GPC['assign_mode']));
		pdo_update('tiny_wmall_store', $data, array('uniacid' => $_W['uniacid'], 'id' => $sid));
		imessage(error(0, '设置排号模式成功'), referer(), 'ajax');
	}

	include itemplate('store/tangshi/queue');
}

if ($ta == 'cover') {
	$_W['page']['title'] = '排号入口';
	$store = store_fetch($sid);
	$urls = array('sys' => imurl('wmall/store/assign', array('sid' => $sid), true));

	if (!empty($store['assign_qrcode'])) {
		$store['assign_qrcode'] = iunserializer($store['assign_qrcode']);

		if (is_array($store['assign_qrcode'])) {
			$urls['wx'] = $store['assign_qrcode']['url'];
		}
	}

	include itemplate('store/tangshi/queue');
}

?>
