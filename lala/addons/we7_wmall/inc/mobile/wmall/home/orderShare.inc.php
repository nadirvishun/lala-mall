<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');
$_W['page']['title'] = '订单分享';

if ($ta == 'list') {
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$id = intval($_GPC['min']);

	if (0 < $id) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}

	$comments = pdo_fetchall('select a.id as aid, a.*,b.final_fee from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_order') . ' as b on a.oid = b.id' . $condition . ' order by a.id desc limit 15', $params, 'aid');
	$min = 0;

	if (!empty($comments)) {
		foreach ($comments as &$row) {
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
			$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : WE7_WMALL_TPL_URL . 'static/img/head.png';
			$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('title', 'logo', 'delivery_time'));
			$row['logo'] = tomedia($row['store']['logo']);
			$row['goods'] = pdo_fetchall('select a.goods_title,a.goods_num,b.thumb from ' . tablename('tiny_wmall_order_stat') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id where a.uniacid = :uniacid and a.oid = :oid', array(':uniacid' => $_W['uniacid'], ':oid' => $row['oid']));

			foreach ($row['goods'] as &$val) {
				$val['thumb'] = tomedia($val['thumb']);
			}

			$row['activity'] = store_fetch_activity($row['sid']);
		}

		$min = min(array_keys($comments));
	}

	if ($_W['ispost']) {
		$comments = array_values($comments);
		$respon = array('errno' => 0, 'message' => $comments, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if ($ta == 'detail') {
	$id = 131;
	$order = order_fetch($id);
	$store = store_fetch($order['sid']);
	$activity = store_fetch_activity($order['sid']);
	$goods = order_fetch_goods($order['id']);
	$discounts = order_fetch_discount($id);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'oid' => $id));

	if (!empty($comment['data'])) {
		$comment['data'] = iunserializer($comment['data']);
	}

	$comment['score'] = ($comment['delivery_service'] + $comment['goods_quality']) * 10;
	$hot_goods = pdo_fetchall('select id,title,price,sailed,thumb from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 order by id desc limit 6', array(':uniacid' => $_W['uniacid'], ':sid' => $order['sid']));
}

include itemplate('home/orderShare');

?>
