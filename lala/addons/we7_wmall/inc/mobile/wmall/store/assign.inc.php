<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('table');
mload()->model('goods');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');
icheckauth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);

if (empty($store)) {
	imessage('门店不存在或已经删除', referer(), 'error');
}

if (!$store['is_assign']) {
	imessage('商家已经关闭排号功能', imurl('store', array('sid' => $sid)), 'info');
}

if ($ta == 'index') {
	$_W['page']['title'] = '取号';

	if ($_GPC['f'] == 'dish') {
		$cart = set_order_cart($sid);
	}

	$mine = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1));

	if (!empty($mine)) {
		header('Location:' . imurl('wmall/store/assign/mine', array('sid' => $sid)));
		exit();
	}

	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include itemplate('store/assign');
}

if ($ta == 'post') {
	$_W['page']['title'] = '我要排号';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$queue_ids = array();

	if (!empty($data)) {
		foreach ($data as $key => &$row) {
			if ((TIMESTAMP < strtotime($row['starttime'])) || (strtotime($row['endtime']) < TIMESTAMP)) {
				unset($data[$key]);
			}
		}

		$queue_ids = array_keys($data);
	}

	if ($_W['isajax']) {
		$guest_num = (intval($_GPC['guest_num']) ? intval($_GPC['guest_num']) : imessage(error(-1, '客人数量错误'), '', 'ajax'));
		$mobile = (trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '手机号错误'), '', 'ajax'));

		if ($store['assign_mode'] == 2) {
			$queue_id = (intval($_GPC['queue_id']) ? intval($_GPC['queue_id']) : imessage(error(-1, '队列错误'), '', 'ajax'));
		}
		else {
			foreach ($data as $da) {
				if ($da['guest_num'] < $guest_num) {
					continue;
				}

				if (!$queue_id) {
					$queue_id = $da['id'];
				}
			}
		}

		if (!in_array($queue_id, $queue_ids)) {
			imessage(error(-1, '不合法的队列'), '', 'ajax');
		}

		$queue = $data[$queue_id];
		$today = strtotime(date('Y-m-d'));

		if ($queue['updatetime'] < $today) {
			pdo_update('tiny_wmall_assign_queue', array('position' => 1, 'updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));
			$queue['position'] = 1;
		}

		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'queue_id' => $queue_id, 'openid' => $_W['openid'], 'mobile' => $mobile, 'guest_num' => $guest_num, 'position' => $queue['position'], 'number' => $queue['prefix'] . str_pad($queue['position'], 3, '0', STR_PAD_LEFT), 'status' => 1, 'is_notify' => 0, 'createtime' => TIMESTAMP);
		pdo_insert('tiny_wmall_assign_board', $data);
		$board_id = pdo_insertid();
		pdo_update('tiny_wmall_assign_queue', array('position' => $queue['position'] + 1, 'updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));
		assign_notice($sid, $board_id, 1);
		assign_notice_clerk($sid, $board_id);
		imessage(error(0, 'success'), 'ajax');
	}

	if (empty($queue_ids)) {
		imessage('门店未添加排号队列,无法取号', referer(), 'info');
	}

	include itemplate('store/assign');
}

if ($ta == 'mine') {
	$_W['page']['title'] = '排号';
	$mine = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1));

	if (empty($mine)) {
		header('Location:' . imurl('wmall/store/assign/index', array('sid' => $sid)));
		exit();
	}

	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');

	if (!empty($data)) {
		$wait = pdo_fetchall('select count(*) as num, queue_id from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 group by queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'queue_id');
	}

	$queue = $data[$mine['queue_id']];
	$now_number = pdo_fetchcolumn('select number from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id and  id < :id order by id desc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $mine['queue_id'], ':id' => $mine['id']));

	if (empty($now_number)) {
		$now_number = $mine['number'];
	}

	$count = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $mine['queue_id'], ':id' => $mine['id']));
	include itemplate('store/assign');
}

if ($ta == 'cancel') {
	if ($_W['isajax']) {
		$id = intval($_GPC['id']);
		$board = assign_board_fetch($id);

		if (empty($board)) {
			exit('排队不存在');
		}

		pdo_update('tiny_wmall_assign_board', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		assign_notice($sid, $id, 4);
		assign_notice_queue($board['id'], $board['queue_id']);
		exit('success');
	}
}

if ($ta == 'goods') {
	$_W['page']['title'] = '商品列表';
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));
	$result = goods_avaliable_fetchall($sid, 0, true);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];
	mload()->model('coupon');
	$tokens = coupon_collect_member_available($sid);

	if (!empty($tokens)) {
		$token_nums = $tokens['num'];
		$token_price = $tokens['price'];
		$token = $tokens['coupons'][0];
	}

	$cart = order_fetch_member_cart($sid);
	include itemplate('store/assignGoods');
}

if ($ta == 'goods_post') {
	$cart = order_insert_member_cart($sid);
	header('location:' . imurl('wmall/store/assign/mine', array('sid' => $sid)));
	exit();
}

?>
