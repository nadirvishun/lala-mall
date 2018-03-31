<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
mload()->model('activity');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'post') {
	$_W['page']['title'] = '创建特价活动';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$title = (!empty($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '活动主题不能为空'), referer(), 'ajax'));
		$goods = array();

		if (!empty($_GPC['goods_id'])) {
			foreach ($_GPC['goods_id'] as $key => $goods_id) {
				$temp = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $goods_id), array('id', 'price'));

				if (empty($temp)) {
					continue;
				}

				$row = array('goods_id' => $goods_id, 'discount_price' => floatval($_GPC['discount_price'][$key]), 'max_buy_limit' => intval($_GPC['max_buy_limit'][$key]), 'poi_user_type' => trim($_GPC['poi_user_type'][$key]) == 'all' ? 'all' : 'new', 'discount_total' => intval($_GPC['discount_total'][$key]), 'discount_available_total' => intval($_GPC['discount_available_total'][$key]));
				$goods[$goods_id] = $row;
			}
		}

		if (empty($goods)) {
			imessage(error(-1, '请选择参与活动的商品'), referer(), 'ajax');
		}

		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $title, 'content' => trim($_GPC['content']), 'order_limit' => intval($_GPC['order_limit']), 'goods_limit' => intval($_GPC['goods_limit']), 'starttime' => strtotime($_GPC['time']['start']), 'endtime' => strtotime($_GPC['time']['end']) + 86399, 'starthour' => str_replace(':', '', trim($_GPC['starthour'])), 'endhour' => str_replace(':', '', trim($_GPC['endhour'])), 'use_limit' => intval($_GPC['use_limit']), 'addtime' => TIMESTAMP, 'total_updatetime' => strtotime(date('Y-m-d')) + 86400);
		$activity = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $title, 'starttime' => 0, 'endtime' => 0, 'type' => 'bargain', 'status' => 1);
		$status = activity_set($sid, $activity);

		if (is_error($status)) {
			imessage($status, '', 'ajax');
		}

		if (0 < $id) {
			pdo_update('tiny_wmall_activity_bargain', $data, array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_activity_bargain', $data);
			$id = pdo_insertid();
		}

		foreach ($goods as $row) {
			$data = array('uniacid' => $_W['uniacid'], 'bargain_id' => $id, 'sid' => $store['id'], 'goods_id' => $row['goods_id'], 'discount_price' => $row['discount_price'], 'max_buy_limit' => $row['max_buy_limit'], 'poi_user_type' => $row['poi_user_type'], 'discount_total' => $row['discount_total'], 'discount_available_total' => $row['discount_available_total']);
			$is_exist = pdo_get('tiny_wmall_activity_bargain_goods', array('bargain_id' => $id, 'goods_id' => $row['goods_id']));

			if (empty($is_exist)) {
				pdo_insert('tiny_wmall_activity_bargain_goods', $data);
			}
			else {
				pdo_update('tiny_wmall_activity_bargain_goods', $data, array('uniacid' => $_W['uniacid'], 'bargain_id' => $id, 'goods_id' => $row['goods_id']));
			}
		}

		$goods_ids = implode(',', array_keys($goods));
		pdo_query('delete from ' . tablename('tiny_wmall_activity_bargain_goods') . ' where uniacid = :uniacid and sid = :sid and bargain_id = :bargain_id and goods_id not in (' . $goods_ids . ')', array(':uniacid' => $_W['uniacid'], ':sid' => $store['id'], ':bargain_id' => $id));
		activity_cron();
		imessage(error(0, '编辑特价活动成功'), iurl('store/activity/bargain/list'), 'ajax');
	}

	if (0 < $id) {
		$bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'id' => $id));

		if (empty($bargain)) {
			imessage('特价活动不存在或已删除', referer(), 'error');
		}

		if (strlen($bargain['starthour']) < 4) {
			$bargain['starthour'] = '0' . $bargain['starthour'];
		}

		if (strlen($bargain['endhour']) < 4) {
			$bargain['endhour'] = '0' . $bargain['endhour'];
		}

		$bargain['starthour'] = trim(chunk_split($bargain['starthour'], 2, ':'), ':');
		$bargain['endhour'] = trim(chunk_split($bargain['endhour'], 2, ':'), ':');
		$row = pdo_fetchall('select a.*,b.title,b.price,b.thumb from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id where bargain_id = :bargain_id order by a.displayorder desc', array(':bargain_id' => $bargain['id']), 'goods_id');
		$bargain['goods'] = $row;
	}

	if (empty($bargain)) {
		$bargain = array(
			'starttime'   => TIMESTAMP,
			'endtime'     => TIMESTAMP + (86400 * 15),
			'starthour'   => '08:00',
			'endhour'     => '12:00',
			'goods_limit' => 1,
			'order_limit' => 1,
			'goods'       => array()
			);
	}
}

if ($ta == 'list') {
	$_W['page']['title'] = '活动列表';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_activity_bargain') . ' where uniacid = :uniacid and  sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $store['id']));
	$bargains = pdo_fetchall('select * from' . tablename('tiny_wmall_activity_bargain') . ' where uniacid = :uniacid and sid = :sid order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':sid' => $store['id']), 'id');
	$pager = pagination($total, $pindex, $psize);

	if (!empty($bargains)) {
		$bargain_status = activity_bargain_status();

		foreach ($bargains as &$row) {
			if (strlen($row['starthour']) < 4) {
				$row['starthour'] = '0' . $row['starthour'];
			}

			if (strlen($row['endhour']) < 4) {
				$row['endhour'] = '0' . $row['endhour'];
			}

			$row['starthour'] = trim(chunk_split($row['starthour'], 2, ':'), ':');
			$row['endhour'] = trim(chunk_split($row['endhour'], 2, ':'), ':');
		}
	}
}

if ($ta == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $id, 'sid' => $sid));
		pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $id, 'sid' => $sid));
	}

	$bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'status' => 1, 'sid' => $sid));

	if (empty($bargain)) {
		activity_del($sid, 'bargain');
	}

	imessage(error(0, '删除活动成功'), '', 'ajax');
}

include itemplate('store/activity/bargain');

?>
