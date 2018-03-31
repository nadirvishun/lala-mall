<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('goods');
mload()->model('table');
global $_W, $_GPC;
icheckauth();
$_W['page']['title'] = '商品列表';
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);

if(empty($store)) {
	imessage('门店不存在或已经删除', referer(), 'error');
}

if($store['is_meal'] != 1) {
	imessage('店内点餐暂未开启', imurl('wmall/store/index'), 'info');
}

$table_id = intval($_GPC['table_id']);
$table = table_fetch($table_id);
if(empty($table)) {
	imessage('桌号不存在', imurl('wmall/store/index'), 'error');
}

$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$title = '商品列表';
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$result = goods_avaliable_fetchall($sid, 0, true);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];

	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_collect_member_available($sid);
	if(!empty($tokens)) {
		$token_nums = $tokens['num'];
		$token_price = $tokens['price'];
		$token = $tokens['coupons'][0];
	}

	$cart = order_fetch_member_cart($sid);
	include itemplate('store/tableGoods');
}

if($ta == 'post') {
	$_W['page']['title'] = '提交订单';
	$cart = order_insert_member_cart($sid, true);
	if(is_error($cart)) {
		$cart = order_fetch_member_cart($sid);
		if(empty($cart)) {
			header('location:' . imurl('wmall/store/table', array('sid' => $sid, 'table_id' => $table_id)));
			die;
		}
	}
	$pay_types = order_pay_types();
	//支付方式
	if(empty($store['payment'])) {
		imessage('店铺没有设置有效的支付方式', referer(), 'error');
	}
	//代金券
	$coupon_text = '无可用代金券';
	$coupons = order_coupon_available($sid, $cart['price']);
	if(!empty($coupons)) {
		$coupon_text = count($coupons) . '张可用代金券';
	}
	$recordid = intval($_GPC['recordid']);

	$activityed = order_count_activity($sid, $cart, $recordid, 0, 0, 0, 3);
	if(!empty($activityed['list']['token'])) {
		$coupon_text = "{$activityed['list']['token']['value']}元券";
		$conpon = $activityed['list']['token']['coupon'];
	}
	//计算服务费
	$serve_fee = 0;
	if($store['serve_fee']['type'] > 0 && $store['serve_fee']['fee'] > 0) {
		$serve_fee = $store['serve_fee']['fee'];
		if($store['serve_fee']['type'] == 2) {
			$serve_fee = round($store['serve_fee']['fee'] * $cart['price'] / 100, 2);
		}
	}
	$waitprice = $cart['price'] + $serve_fee - $activityed['total'];
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
	include itemplate('store/tableSubmit');
}

if($ta == 'submit') {
	if(!$_W['isajax']) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}
	$cart = order_fetch_member_cart($sid);
	if(empty($cart)) {
		header('location:' . imurl('wmall/store/goods', array('sid' => $sid)));
		die;
	}
	$recordid = intval($_GPC['record_id']);
	$activityed = order_count_activity($sid, $cart, $recordid);
	//计算服务费
	$serve_fee = 0;
	if($store['serve_fee']['type'] > 0 && $store['serve_fee']['fee'] > 0) {
		$serve_fee = $store['serve_fee']['fee'];
		if($store['serve_fee']['type'] == 2) {
			$serve_fee = round($store['serve_fee']['fee'] * $cart['price'] / 100, 2);
		}
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
		'code' => random(4, true),
		//'groupid' => $cart['groupid'],
		'order_type' => 3, //店内单
		'openid' => $_W['openid'],
		'mobile' => '',
		'username' => trim($_GPC['username']),
		'person_num' => intval($_GPC['person_num']),
		'table_id' => intval($_GPC['table_id']),
		'sex' => '',
		'address' => '',
		'location_x' => '',
		'location_y' => '',
		'delivery_day' => '',
		'delivery_time' => '',
		'delivery_fee' => 0,
		'pack_fee' => 0,
		'pay_type' => trim($_GPC['pay_type']),
		'num' => $cart['num'],
		'price' => $cart['price'],
		'serve_fee' => $serve_fee,
		'total_fee' => $cart['price'] + $serve_fee,
		'discount_fee' => $activityed['total'],
		'store_discount_fee' => $activityed['store_discount_fee'],
		'plateform_discount_fee' => $activityed['plateform_discount_fee'],
		'final_fee' => $cart['price'] + $serve_fee - $activityed['total'],
		'status' => 1,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => iserializer($cart['data']),
		'note' => trim($_GPC['note'])
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	pdo_insert('tiny_wmall_order', $order);
	$id = pdo_insertid();

	order_update_bill($id);
	//更新对应的桌号为已下单
	table_order_update($table_id, $id,  3);
	order_insert_discount($id, $sid, $activityed['list']);
	order_insert_status_log($id, 'place_order');
	order_update_goods_info($id, $sid);
	order_del_member_cart($sid);
	
	order_clerk_notice($sid, $id, 'store_order_place');
	imessage(error(0, $id), '', 'ajax');
}


