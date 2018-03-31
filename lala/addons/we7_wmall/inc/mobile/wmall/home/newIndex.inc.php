<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');
$config = $_W['we7_wmall']['config']['mall'];
$config_takeout = $_W['we7_wmall']['config']['takeout'];
$_W['page']['title'] = $config['title'];

if ($config['version'] == 2) {
	$url = imurl('wmall/store/newGoods', array('sid' => $config['default_sid']));
	header('location:' . $url);
	exit();
}

$hots = store_fetchall_by_condition('hot');
$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);
$notices = pdo_fetchall('select id,title,link,displayorder,status from' . tablename('tiny_wmall_notice') . ' where uniacid = :uniacid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
$orderbys = store_orderbys();
$discounts = store_discounts();
$recommends = pdo_fetchall('select id,title,logo from' . tablename('tiny_wmall_store') . 'where uniacid = :uniacid and is_recommend = 1 order by displayorder desc limit 6', array(':uniacid' => $_W['uniacid']));

if (check_plugin_perm('bargain')) {
	$_config_bargain = get_plugin_config('bargain');
	if (($_config_bargain['status'] == 1) && ($_config_bargain['is_home_display'] == 1)) {
		$bargains = pdo_fetchall('select a.discount_price,a.goods_id,b.title,b.thumb,b.price,b.sid from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id where a.uniacid = :uniacid and a.status = 1 order by a.mall_displayorder desc limit 4', array(':uniacid' => $_W['uniacid']));

		foreach ($bargains as &$val) {
			$val['discount'] = round(($val['discount_price'] / $val['price']) * 10, 1);
		}
	}
}

$cubes = pdo_fetchall('select * from ' . tablename('tiny_wmall_cube') . ' where uniacid = :uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']));

if ($ta == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	isetcookie('__lat', $lat, 120);
	isetcookie('__lng', $lng, 120);
	$stores = pdo_fetchall('select id,score,title,logo,sailed,score,label,business_hours,is_in_business,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and status = 1', array(':uniacid' => $_W['uniacid']));
	$min = 0;

	if (!empty($stores)) {
		$store_label = category_store_label();

		foreach ($stores as $key => &$da) {
			$da['logo'] = tomedia($da['logo']);
			$da['business_hours'] = (array) iunserializer($da['business_hours']);
			$da['is_in_business_hours'] = $da['is_in_business'] && store_is_in_business_hours($da['business_hours']);
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['num'] += (0 < $da['delivery_free_price'] ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);

			if (0 < $da['label']) {
				$da['label_color'] = $store_label[$da['label']]['color'];
				$da['label_cn'] = $store_label[$da['label']]['title'];
			}

			if ($da['delivery_fee_mode'] == 2) {
				$da['delivery_price'] = iunserializer($da['delivery_price']);
				$da['delivery_price'] = $da['delivery_price']['start_fee'];
			}

			if (!empty($lng) && !empty($lat)) {
				$da['distance'] = distanceBetween($da['location_y'], $da['location_x'], $lng, $lat);
				$da['distance'] = round($da['distance'] / 1000, 2);
				if (($config['store_overradius_display'] == 2) && ($config_takeout['range']['serve_radius'] < $da['distance'])) {
					unset($stores[$key]);
				}
			}
			else {
				$da['distance'] = 0;
			}

			if ($da['is_in_business_hours'] == 1) {
				$da['is_in_business_hours_'] = 100000;
			}

			$da['displayorder_order'] = $da['displayorder'] + (($da['displayorder'] + 1) * $da['is_in_business_hours_']);
			$da['sailed_order'] = $da['sailed'] + (($da['sailed'] + 1) * $da['is_in_business_hours_']);
			$da['score_order'] = $da['score'] + (($da['score'] + 1) * $da['is_in_business_hours_']);
			$da['click_order'] = $da['click'] + (($da['click'] + 1) * $da['p']);
			$da['distance_order'] = $da['distance'] + ($da['distance'] * ($da['is_in_business_hours'] == 1 ? 0 : 100000));
		}

		$order_by_type = ($config['store_orderby_type'] ? $config['store_orderby_type'] : 'distance');

		if (empty($lat)) {
			$order_by_type = 'displayorder';
		}

		if (!empty($stores)) {
			$min = min(array_keys($stores));

			if (in_array($order_by_type, array('distance'))) {
				$stores = array_sort($stores, $order_by_type . '_order', SORT_ASC);
			}
			else {
				$stores = array_sort($stores, $order_by_type . '_order', SORT_DESC);
			}
		}
	}

	$stores = array_values($stores);
	$respon = array('error' => 0, 'message' => $stores, 'min' => $min);
	imessage($respon, '', 'ajax');
}

$address_id = intval($_GPC['aid']);

if (0 < $address_id) {
	isetcookie('__aid', $address_id, 1800);
}

$_share = get_mall_share();
include itemplate('home/newIndex');

?>
