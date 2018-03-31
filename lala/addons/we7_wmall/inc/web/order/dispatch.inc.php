<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
$op = trim($_GPC['op'])? trim($_GPC['op']): 'map';
$_W['page']['title'] = '调度中心';
$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'address', 'location_x', 'location_y'), 'id');

if($op == 'map') {
	$deliveryer_alls = deliveryer_fetchall(0);
	$sid = intval($_GPC['sid']);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($_W['ispost']) {
		$condition = ' where uniacid = :uniacid and status = 3 and delivery_type = 2';
		$params = array(
			':uniacid' => $_W['uniacid']
		);
		if($sid > 0) {
			$condition .= ' and sid = :sid';
			$params[':sid'] = $sid;
		}
		$orders = pdo_fetchall('select id,sid,serial_sn,address,location_x,location_y,paytime from ' . tablename('tiny_wmall_order') . $condition, $params, 'id');
		if(!empty($orders)) {
			foreach($orders as &$val) {
				$data = $val;
				$val['store'] = $stores[$val['sid']];
				$val['paytime_cn'] = date('m-d H:i', $val['paytime']);
			}
		}
		$condition = ' where a.uniacid = :uniacid and a.sid = 0 and a.work_status = 1';
		$params = array(
			':uniacid' => $_W['uniacid']
		);
		if($deliveryer_id > 0) {
			$condition .= ' and b.id = :id';
			$params[':id'] = $deliveryer_id;
		}
		$deliveryers = pdo_fetchall('select b.id,b.mobile,b.title,b.location_x,b.location_y from ' . tablename('tiny_wmall_store_deliveryer') . ' as a left join' . tablename('tiny_wmall_deliveryer') . ' as b on a.deliveryer_id = b.id' . $condition, $params);
		if(!empty($deliveryers)) {
			foreach($deliveryers as &$row) {
				$row['finish'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 5', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $row['id'], ':stat_day' => date('Ymd')));
				$row['wait_pickup'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 7', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $row['id'], ':stat_day' => date('Ymd')));
				$row['wait_delivery'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 4', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $row['id'], ':stat_day' => date('Ymd')));
				$row['work_status'] = 1;
				$addtime = pdo_fetchcolumn('select addtime from ' . tablename('tiny_wmall_deliveryer_location_log') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id order by id desc limit 1', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $row['id']));
				if(TIMESTAMP - $addtime > 300) {
					$row['work_status'] = 2;
				}
				$row['css'] = '';
				if($row['work_status'] == 2) {
					$row['css'] = 'off-line';
				} elseif($row['work_status'] == 1 && empty($row['wait_pickup']) && empty($row['wait_delivery'])) {
					$row['css'] = 'active';
				}
			}
		}
		$dispatch = array(
			'orders' => $orders,
			'deliveryers' => $deliveryers
		);
		imessage(error(0, $dispatch), '', 'ajax');
	}
}

if($op == 'deliveryer') {
	if($_W['ispost']) {
		$id = intval($_GPC['id']);
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($deliveryer)) {
			imessage(error(-1, '配送员不存在或已删除'), referer(), 'ajax');
		}
		$params = array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id, ':stat_day' => date('Ymd'));
		$deliveryer['finish'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 5', $params);
		$deliveryer['wait_pickup'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 7', $params);
		$deliveryer['wait_delivery'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day = :stat_day and delivery_status = 4', $params);

		$deliveryer['orders'] = pdo_fetchall('select a.*, b.title, b.location_x as store_location_x,b.location_y as store_location_y from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store'). ' as b on a.sid = b.id where a.uniacid = :uniacid and a.deliveryer_id = :deliveryer_id and a.delivery_status in (4, 7)', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id));
		if(!empty($deliveryer['orders'])) {
			$delivery_status = order_delivery_status();
			foreach($deliveryer['orders'] as &$row) {
				$row['store_title'] = $row['title'];
				$row['delivery_status_cn'] = $delivery_status[$row['delivery_status']]['text'];
				$row['store'] = array(
					'location_x' => $row['store_location_x'],
					'location_y' => $row['store_location_y'],
				);
				$row['time_interval'] = order_time_analyse($row['id']);
			}
		}
		imessage(error(0, $deliveryer), '', 'ajax');
	}
}

if($op == 'menu') {
	$condition = ' where uniacid  = :uniacid and delivery_status > 0 and delivery_type = 2';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$delivery_status = isset($_GPC['delivery_status'])? intval($_GPC['delivery_status']): 0;
	if($delivery_status > 0) {
		$condition .= ' and delivery_status = :delivery_status';
		$params[':delivery_status'] = $delivery_status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT count(*) from ' . tablename('tiny_wmall_order') .  $condition, $params);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	$deliveryer_alls = deliveryer_all();
	if(!empty($orders)) {
		foreach($orders as &$value) {
			$value['store'] = $stores[$value['sid']];
			$value['deliveryer'] = $deliveryer_alls[$value['deliveryer_id']];
		}
	}
	$order_delivery_status = order_delivery_status();
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'dispatch') {
	$ids = $_GPC['ids'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if(!empty($ids)) {
		foreach($ids as $id) {
			$status = order_assign_deliveryer($id, $deliveryer_id, true, '本订单由平台管理员调度分配,请尽快处理');
			if(is_error($status)) {
				imessage($status, '', 'ajax');
			}
		}
	}
	imessage(error(0, '分配订单成功'), '', 'ajax');
}
include itemplate('order/dispatch');