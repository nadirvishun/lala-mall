<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');
mload()->model('goods');
icheckauth();
$_W['page']['title'] = '商品列表';
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);

if (empty($store)) {
	imessage('门店不存在或已经删除', referer(), 'error');
}

$activity = store_fetch_activity($sid);
mload()->model('activity');
activity_store_cron($sid);
$_share = array('title' => $store['title'], 'desc' => $store['content'], 'imgUrl' => tomedia($store['logo']));

if ($_GPC['from'] == 'search') {
	pdo_query('update ' . tablename('tiny_wmall_store') . ' set click = click + 1 where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $sid));
}

if ($ta == 'index') {
	$title = $store['title'] . '-商品列表';
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));
	$result = goods_avaliable_fetchall($sid);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];
	$categorys_limit_status = 0;
	$categorys_limit = array();

	foreach ($categorys as $row) {
		if (0 < $row['min_fee']) {
			$categorys_limit_status = 1;
			$row['fee'] = 0;
			$categorys_limit[$row['id']] = $row;
		}
	}

	$categorys_index = array_keys($categorys_limit);
	mload()->model('coupon');
	$tokens = coupon_collect_member_available($sid);

	if (!empty($tokens)) {
		$token_nums = $tokens['num'];
		$token_price = $tokens['price'];
		$token = $tokens['coupons'][0];
	}

	if (!$_GPC['f']) {
		$cart = order_fetch_member_cart($sid);
	}
	else {
		$cart = order_place_again($sid, $_GPC['id']);

		if (empty($cart)) {
			$cart = order_fetch_member_cart($sid);
		}
	}

	include itemplate('home/newGoods');
}

if ($ta == 'cate') {
	$cart = order_insert_member_cart($sid);
	$cid = trim($_GPC['cid']);
	$result = goods_avaliable_fetchall($sid, $cid);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];
	$total = count($cate_goods[$cid]);
	$categorys_limit_status = 0;
	$categorys_limit = array();

	foreach ($categorys as $row) {
		if (0 < $row['min_fee']) {
			$categorys_limit_status = 1;
			$row['fee'] = 0;
			$categorys_limit[$row['id']] = $row;
		}
	}

	$categorys_index = array_keys($categorys_limit);
	include itemplate('store/goodsMarketCate');
}

if ($ta == 'detail') {
	if (!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}

	$id = intval($_GPC['id']);
	$goods = goods_fetch($id);

	if (is_error($goods)) {
		imessage(error(-1, '商品不存在或已删除'), '', 'ajax');
	}

	if (!$goods['comment_total']) {
		$goods['comment_good_percent'] = '0%';
	}
	else {
		$goods['comment_good_percent'] = round(($goods['comment_good'] / $goods['comment_total']) * 100, 2) . '%';
	}

	imessage(error(0, $goods), '', 'ajax');
}

if ($ta == 'cart_truncate') {
	if (!$_W['isajax']) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}

	pdo_delete('tiny_wmall_order_cart', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	imessage(error(0, ''), '', 'ajax');
}

if ($ta == 'search') {
	if (!$_W['isajax']) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}

	$key = trim($_GPC['key']);

	if (empty($key)) {
		imessage(error(-1, '关键词不能为空'), '', 'ajax');
	}

	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and status = 1 and title like :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => '%' . $key . '%'));

	if (!empty($goods)) {
		foreach ($goods as &$good) {
			$good['thumb_cn'] = tomedia($good['thumb']);
			$good['is_in_business_hours'] = $store['is_in_business_hours'];

			if ($good['is_options']) {
				$good['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
			}
		}
	}

	imessage(error(0, $goods), '', 'ajax');
}

?>
