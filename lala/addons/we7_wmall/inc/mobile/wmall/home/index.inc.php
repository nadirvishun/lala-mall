<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'index';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$config = $_W['we7_wmall']['config']['mall'];
$config_takeout = $_W['we7_wmall']['config']['takeout'];

$_W['page']['title'] = $config['title'];
if($config['version'] == 2) {
	$url = imurl('wmall/store/goods', array('sid' => $config['default_sid']));
	header('location:' . $url);
	die;
}
$location_url = imurl('wmall/home/location');
if(check_plugin_perm('agent') && get_plugin_config('agent.basic.status') == 1) {
	$location_url = imurl('wmall/home/agent');
}

$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);
$discounts = store_discounts();
$recommends = pdo_fetchall('select id,title,logo from' .tablename('tiny_wmall_store') . 'where uniacid = :uniacid and agentid = :agentid and is_recommend = 1 order by displayorder desc limit 6', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));

if(check_plugin_perm('bargain')) {
	$_config_bargain = get_plugin_config('bargain');
	if($_config_bargain['status'] == 1 && $_config_bargain['is_home_display'] == 1) {
		$bargains = pdo_fetchall('select a.discount_price,a.goods_id,b.title,b.thumb,b.price,b.sid from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1 order by a.mall_displayorder desc limit 4', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
		foreach($bargains as &$val) {
			$val['discount'] = round(($val['discount_price'] / $val['price'] * 10), 1);
		}
	}
}
$cubes = pdo_fetchall('select * from ' . tablename('tiny_wmall_cube') . ' where uniacid = :uniacid and agentid = :agentid order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
if(!empty($_GPC['__address'])) {
	$_GPC['__address'] = urldecode($_GPC['__address']);
}
if($ta == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	$position = trim($_GPC['position']);
	if(!empty($position)) {
		$position = urldecode($position);
		isetcookie('__address', $position, 1800);
	}
	isetcookie('__lat', $lat, 1800);
	isetcookie('__lng', $lng, 1800);
	$order_by_type = $config['store_orderby_type'] ? $config['store_orderby_type'] : 'distance';
	$order_by = ' order by is_stick desc, is_rest asc';
	if(!empty($order_by_type) && $order_by_type != 'distance') {
		$order_by .= ", {$order_by_type} desc";
	}
	$stores = pdo_fetchall('select id,agentid,score,title,logo,content,sailed,score,label,serve_radius,not_in_serve_radius,delivery_areas,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and agentid = :agentid and status = 1 {$order_by}", array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	$min = 0;
	if(!empty($stores)) {
		$store_label = category_store_label();
		foreach($stores as $key => &$da) {
			$da['logo'] = tomedia($da['logo']);
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
			if($da['label'] > 0) {
				$da['label_color'] = $store_label[$da['label']]['color'];
				$da['label_cn'] = $store_label[$da['label']]['title'];
			}
			if($da['delivery_fee_mode'] == 2) {
				$da['delivery_price'] = iunserializer($da['delivery_price']);
				$da['delivery_price'] = $da['delivery_price']['start_fee'];
			} elseif($da['delivery_fee_mode'] == 3) {
				$da['delivery_areas'] = iunserializer($da['delivery_areas']);
				if(!is_array($da['delivery_areas'])) {
					$da['delivery_areas'] = array();
				}
				$price = store_order_condition($da, array($lng, $lat));
				$da['delivery_price'] = $price['delivery_price'];
				$da['send_price'] = $price['send_price'];
			}

			if(!empty($lng) && !empty($lat)) {
				$da['distance'] = distanceBetween($da['location_y'], $da['location_x'], $lng, $lat);
				$da['distance'] = round($da['distance'] / 1000, 2);
				$in = is_in_store_radius($da, array($lng, $lat));
				if($config['store_overradius_display'] == 2 && !$in) {
					unset($stores[$key]);
				}
				$da['distance_order'] = $da['distance'] + $da['distance'] * ($da['is_rest'] == 0 ? 1 : 100000) * ($da['is_stick'] == 1 ? 0 : 300000);
			} else {
				$da['distance'] = 0;
			}
		}
		if(!empty($stores)) {
			$min = min(array_keys($stores));
			if($order_by_type == 'distance' && !empty($lng)) {
				$stores = array_sort($stores, "distance_order", SORT_ASC);
			}
		}
	}
	$stores = array_values($stores);
	$respon = array('error' => 0, 'message' => $stores, 'min' => $min);
	message($respon, '', 'ajax');
}
$address_id = intval($_GPC['aid']);
if($address_id > 0) {
	isetcookie('__aid', $address_id, 1800);
} else {
	isetcookie('__aid', 0, -1000);
}
$_share = get_mall_share();
include itemplate('home/index');