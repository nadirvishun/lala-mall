<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = "搜索";
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

$config = $_W['we7_wmall']['config']['mall'];
$config_takeout = $_W['we7_wmall']['config']['takeout'];

$discounts = store_discounts();
$categorys = store_fetchall_category();
$orderbys = store_orderbys();

$lat = $_GPC['__lat'];
$lng = $_GPC['__lng'];
$carousel = store_fetch_category();

if($ta == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	if(!empty($lat) && !empty($lng)) {
		isetcookie('__lat', $lat, 1800);
		isetcookie('__lng', $lng, 1800);
	}
	$condition = ' where uniacid = :uniacid and agentid = :agentid and status = 1';
	$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
	if($_GPC['cid'] > 0) {
		$condition .= ' and cid like :cid';
		$params[':cid'] = "%|{$_GPC['cid']}|%";
	}

	$dis = trim($_GPC['dis']);
	if(!empty($dis)) {
		if(in_array($dis, array('invoice_status'))) {
			$condition .= " and invoice_status = 1";
		} elseif($dis == 'delivery_price') {
			$condition .= " and (delivery_price = 0 or delivery_free_price > 0)";
		} else {
			$sids = pdo_getall('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'type' => $dis, 'status' => 1), array('sid'), 'sid');
			if(empty($sids)) {
				$sids = array(0);
			}
			$sids = implode(',', array_keys($sids));
			$condition .= " and id in ({$sids})";
		}
	}
	$order_by_type = trim($_GPC['order']) ? trim($_GPC['order']) : 'distance';
	$order_by = " order by is_stick desc, is_rest asc";
	if($order_by_type == 'sailed' || $order_by_type == 'score') {
		$order_by .= ", {$order_by_type} desc";
	} elseif ($order_by_type == 'send_price' || $order_by_type == 'delivery_time') {
		$order_by .= ", {$order_by_type} asc";
	}
	$stores = pdo_fetchall('select id,title,logo,content,label,serve_radius,not_in_serve_radius,delivery_areas,sailed,score,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url from ' . tablename('tiny_wmall_store') . "{$condition} {$order_by}", $params);
	$min = 0;
	if(!empty($stores)) {
		$store_label = category_store_label();
		foreach($stores as $key => &$row) {
			$row['logo'] = tomedia($row['logo']);
			$row['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			$row['activity'] = store_fetch_activity($row['id']);
			$row['activity']['activity_num'] += ($row['delivery_free_price'] > 0 ? 1 : 0);
			$row['score_cn'] = round($row['score'] / 5, 2) * 100;
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			if($row['label'] > 0) {
				$row['label_color'] = $store_label[$row['label']]['color'];
				$row['label_cn'] = $store_label[$row['label']]['title'];
			}
			if($row['delivery_fee_mode'] == 2) {
				$row['delivery_price'] = iunserializer($row['delivery_price']);
				$row['delivery_price'] = $row['delivery_price']['start_fee'];
			} elseif($row['delivery_fee_mode'] == 3) {
				$row['delivery_areas'] = iunserializer($row['delivery_areas']);
				if(!is_array($row['delivery_areas'])) {
					$row['delivery_areas'] = array();
				}
				$price = store_order_condition($row, array($lng, $lat));
				$row['delivery_price'] = $price['delivery_price'];
				$row['send_price'] = $price['send_price'];
			}

			if(!empty($lng) && !empty($lat)) {
				$row['distance'] = distanceBetween($row['location_y'], $row['location_x'], $lng, $lat);
				$row['distance'] = round($row['distance'] / 1000, 2);
				$in = is_in_store_radius($row, array($lng, $lat));
				if($config['store_overradius_display'] == 2 && !$in) {
					unset($stores[$key]);
				}
			} else {
				$row['distance'] = 0;
			}
			$row['distance_order'] = $row['distance'] + $row['distance'] * ($row['is_rest'] == 0 ? 1 : 100000) * ($row['is_stick'] == 1 ? 0 : 300000);
		}
		if(!empty($stores)) {
			$min = min(array_keys($stores));
			if($order_by_type == 'distance') {
				$stores = array_sort($stores, "{$order_by_type}_order", SORT_ASC);
			}
		}
	}
	$stores = array_values($stores);
	$respon = array('error' => 0, 'message' => $stores, 'min' => $min);
	message($respon, '', 'ajax');
}
include itemplate('home/search');

