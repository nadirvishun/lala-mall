<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$callback = $_GPC['callback'];
$discounts = store_discounts();
$data = array();
$data['takeout']['sys'] = array(
	'title' => '平台链接',
	'items' => array(
		array('title' => '平台首页', 'url' => imurl('wmall/home/index')),
		array('title' => '搜索商家', 'url' => imurl('wmall/home/hunt')),
		array('title' => '附近商家', 'url' => imurl('wmall/home/search')),
		array('title' => '会员中心', 'url' => imurl('wmall/member/mine')),
		array('title' => '我的订单', 'url' => imurl('wmall/order/index')),
		array('title' => '我的代金券', 'url' => imurl('wmall/member/coupon')),
		array('title' => '我的收货地址', 'url' => imurl('wmall/member/address')),
		array('title' => '我的收藏', 'url' => imurl('wmall/member/favorite')),
		array('title' => '我的评价', 'url' => imurl('wmall/member/comment')),
		array('title' => '配送会员卡', 'url' => imurl('deliveryCard/index')),
		array('title' => '领券中心', 'url' => imurl('wmall/channel/coupon')),
		array('title' => '余额充值', 'url' => imurl('wmall/member/recharge'))
		)
	);
$data['takeout']['dis'] = array(
	'title' => '优惠活动',
	'items' => array()
	);

foreach ($discounts as $row) {
	$data['takeout']['dis']['items'][] = array('title' => $row['title'], 'url' => imurl('wmall/home/search', array('dis' => $row['key'])));
}

if (check_plugin_perm('errander')) {
	$data['errander'] = array(
	array(
		'title' => '平台链接',
		'items' => array(
			array('title' => '跑腿首页', 'url' => imurl('errander/index')),
			array('title' => '跑腿订单', 'url' => imurl('errander/order/list'))
			)
		)
	);
	$data['errander']['business'] = array(
	'title' => '业务链接',
	'items' => array()
	);
	$categorys = pdo_getall('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));

	if (!empty($categorys)) {
		foreach ($categorys as $category) {
			$data['errander']['business']['items'][] = array('title' => $category['title'], 'url' => imurl('errander/category', array('id' => $category['id'])));
		}
	}
}

if (check_plugin_perm('ordergrant')) {
	$data['ordergrant'] = array(
	array(
		'title' => '平台链接',
		'items' => array(
			array('title' => '下单有礼入口', 'url' => imurl('ordergrant/index')),
			array('title' => '奖励记录', 'url' => imurl('ordergrant/record')),
			array('title' => '订单分享入口', 'url' => imurl('ordergrant/share'))
			)
		)
	);
}

include itemplate('public/link');

?>
