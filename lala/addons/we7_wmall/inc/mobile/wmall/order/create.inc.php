<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
icheckAuth();
$_W['page']['title'] = '提交订单';

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'goods';
$sid = intval($_GPC['sid']);
$store = store_fetch($sid, array('agentid', 'cid', 'title', 'logo', 'location_x', 'location_y', 'invoice_status', 'delivery_type', 'delivery_mode', 'delivery_price', 'delivery_fee_mode', 'delivery_areas', 'delivery_time', 'delivery_free_price', 'pack_price', 'delivery_within_days', 'delivery_reserve_days', 'order_note'));
if(empty($store)) {
	imessage('门店不存在', '', 'error');
}
$store['payment'] = get_available_payment('takeout', $sid);

if($ta == 'goods') {
	$cart = order_insert_member_cart($sid);
	if(is_error($cart)) {
		header('location:' . imurl('wmall/store/goods', array('sid' => $sid)));
		die;
	}
	header('location:' . imurl('wmall/order/create/index', array('sid' => $sid)));
	die;
}

if($ta == 'index') {
	$cart = order_fetch_member_cart($sid);
	if(empty($cart)) {
		if(!is_h5app()) {
			header('location:' . imurl('wmall/store/goods', array('sid' => $sid)));
		}
		die;
	}
	$pay_types = order_pay_types();
	//支付方式
	if(empty($store['payment'])) {
		imessage('店铺没有设置有效的支付方式', referer(), 'error');
	}
	$address = member_fetch_available_address($sid);
	$address_id = $address['id'];

	//商家配送方式
	$delivery_time = store_delivery_times($sid);
	$time_flag = 0;
	$predict_index = 0;
	$predict_timestamp = TIMESTAMP + 60 * $store['delivery_time'];
	if(!$delivery_time['reserve']) {
		$data = array_order(TIMESTAMP + 60 * $store['delivery_time'], $delivery_time['timestamp']);
		if(!empty($data)) {
			$time_flag = 1;
			$predict_index = array_search($data, $delivery_time['timestamp']);
			$predict_day = $delivery_time['days'][0];
			$predict_time = "{$delivery_time['times'][$predict_index]['start']}~{$delivery_time['times'][$predict_index]['end']}";
			$text_time = "尽快送达";
			$predict_extra_price = $delivery_time['times'][$predict_index]['fee'];
		} else {
			$predict_day = $delivery_time['days'][1];
			$predict_times = array_shift($delivery_time['times']);
			$predict_time = "{$predict_times['start']}~{$predict_times['end']}";
			$text_time = "{$predict_day} {$predict_time}";
		}
		$predict_delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
		if($store['delivery_fee_mode'] == 1) {
			$predict_delivery_price = "{$predict_delivery_price}元配送费";
		} else {
			$predict_delivery_price = "配送费{$predict_delivery_price}元起";
		}
	} else {
		$predict_day = $delivery_time['days'][0];
		$predict_time = $delivery_time['times'][0];
		$text_time = "{$predict_day} {$predict_time}";
	}

	//计算配送费
	$delivery_price = 0;
	if($store['delivery_type'] != 2) {
		if($store['delivery_fee_mode'] == 1) {
			$delivery_price_basic = $store['delivery_price'];
			$delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
		} elseif($store['delivery_fee_mode'] == 2) {
			$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['start_fee'];
			$distance = $address['distance'];
			if(!empty($address) && $distance > 0) {
				if($distance > $store['delivery_price_extra']['start_km']) {
					$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['pre_km_fee'];
				}
				$delivery_price = $delivery_price_basic = round($delivery_price, 2);
				$delivery_price += $delivery_time['times'][$predict_index]['fee'];
			}
		} elseif($store['delivery_fee_mode'] == 3) {
			if(!empty($address)) {
				$area_index = 0;
				foreach($store['delivery_areas'] as $key => $row) {
					$is_ok = isPointInPolygon($row['path'], array($address['location_y'], $address['location_x']));
					if($is_ok) {
						$area_index = $key;
						break;
					}
				}
				if(!empty($area_index)) {
					$area = $store['delivery_areas'][$area_index];
					$delivery_price = $delivery_price_basic = round($area['delivery_price'], 2);
					$send_price = $area['send_price'];
					$delivery_free_price = $area['delivery_free_price'];
					$delivery_price += $delivery_time['times'][$predict_index]['fee'];
				}
			}
		}
	}
	$cookie_price_original = array();
	if(!empty($_GPC['_cookie_price'])) {
		$cookie_price_original = iunserializer(base64_decode($_GPC['_cookie_price']));
	}
	$cookie_price = array(
		'delivery_price' => $delivery_price,
		'delivery_free_price' => $delivery_free_price,
	);
	isetcookie('_cookie_price', base64_encode(iserializer($cookie_price)), 180);
	$send_diff = 0;
	if($send_price > $cart['price']) {
		$send_diff = round($send_price - $cart['price'], 2);
	} else {
		if(!empty($address_id)) {
			isetcookie('__aid', $address['id'], 300);
		}
	}

	//代金券
	$coupon_text = '无可用代金券';
	$coupons = order_coupon_available($sid, $cart['price']);
	if(!empty($coupons)) {
		$coupon_text = count($coupons) . '张可用代金券';
	}
	//红包
	$redPacket_text = '无可用红包';
	$redPackets = order_redPacket_available($cart['price'], explode('|', $store['cid']));
	if(!empty($redPackets)) {
		$redPacket_text = count($redPackets) . '个可用红包';
	}
	$recordid = intval($_GPC['recordid']);
	$redPacket_id = intval($_GPC['redPacket_id']);
	$activityed = order_count_activity($sid, $cart, $recordid, $redPacket_id, $delivery_price, $delivery_free_price);
	if(!empty($activityed['list']['token'])) {
		$coupon_text = "{$activityed['list']['token']['value']}元券";
	}
	if(!empty($activityed['list']['redPacket'])) {
		$redPacket_text = "-￥{$activityed['list']['redPacket']['value']}";
		$redPacket = $activityed['redPacket'];
	}
	$activity_price = $activity_notSelfDelivery_price = $activityed['total'];
	$delivery_activity_price = 0;
	if(!empty($activityed) && !empty($activityed['list']['delivery'])) {
		$delivery_activity_price = $activityed['list']['delivery']['value'];
	}
	$self_delivery_activity_price = 0;
	if(!empty($activityed) && !empty($activityed['list']['selfDelivery'])) {
		$self_delivery_activity_price = $activityed['list']['selfDelivery']['value'];
	}
	$waitprice = $cart['price'] + $cart['box_price'] + $delivery_price + $store['pack_price'] - $activityed['total'] + $self_delivery_activity_price;
	$activity_price -= $self_delivery_activity_price;
	$activity_notSelfDelivery_price -= $self_delivery_activity_price;
	//如果门店只支持到店自提
	if($store['delivery_type'] == 2) {
		$waitprice -= $self_delivery_activity_price;
		$activity_price += $self_delivery_activity_price;
		$activity_notSelfDelivery_price += $self_delivery_activity_price;
	}
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
}

if($ta == 'submit') {
	if(!$_W['isajax']) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}
	$cart = order_check_member_cart($sid);
	if(is_error($cart)) {
		imessage($cart, '', 'ajax');
	}
	if($_GPC['order_type'] == 1) {
		$address = member_fetch_address($_GPC['address_id']);
		if(empty($address)) {
			imessage(error(-1, '收货地址信息错误'), '', 'ajax');
		}
		$delivery_time = store_delivery_times($sid);
		//计算配送费
		$predict_index = intval($_GPC['delivery_index']);
		$delivery_price = 0;
		$distance = 0;
		if($store['delivery_type'] != 2) {
			if($store['delivery_fee_mode'] == 1) {
				$delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
			} elseif($store['delivery_fee_mode'] == 2) {
				$distance = distanceBetween($address['location_y'], $address['location_x'], $store['location_y'], $store['location_x']);
				$distance = $distance / 1000;
				$delivery_price = $store['delivery_price_extra']['start_fee'];
				if($distance > 0) {
					if($distance > $store['delivery_price_extra']['start_km']) {
						$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['pre_km_fee'];
					}
					$delivery_price = round($delivery_price, 2);
					$delivery_price += $delivery_time['times'][$predict_index]['fee'];
				}
			} elseif($store['delivery_fee_mode'] == 3) {
				$area_index = 0;
				$is_ok = is_in_store_radius($store, array($address['location_y'], $address['location_x']));
				if(!$is_ok) {
					imessage(error(-1, '收货地址不在商家配送范围'), '', 'ajax');
				}
				$price = store_order_condition($store, array($address['location_y'], $address['location_x']));
				$send_price = $price['send_price'];
				if($send_price > $cart['price']) {
					imessage(error(-1, '当前商品不满起送价'), '', 'ajax');
				}
				$delivery_price = round($price['delivery_price'], 2);
				$delivery_free_price = $price['delivery_free_price'];
				$delivery_price += $delivery_time['times'][$predict_index]['fee'];
				$distance = distanceBetween($address['location_y'], $address['location_x'], $store['location_y'], $store['location_x']);
				$distance = $distance / 1000;
			}
		}
		$distance = round($distance, 2);
	} elseif($_GPC['order_type'] == 2) {
		$address = array(
			'realname' => trim($_GPC['username']),
			'mobile' => trim($_GPC['mobile'])
		);
	}
	isetcookie('_cookie_price', '', -100);
	$order_type = intval($_GPC['order_type']) ? intval($_GPC['order_type']) : 1;
	$recordid = intval($_GPC['record_id']);
	$redPacket_id = intval($_GPC['redPacket_id']);
	$activityed = order_count_activity($sid, $cart, $recordid, $redPacket_id, $delivery_price, $delivery_free_price, $order_type);

	$total_fee = $cart['price'] + $cart['box_price'] + $store['pack_price'] + $delivery_price;
	$order = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $store['agentid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'mall_first_order' => $_W['member']['is_mall_newmember'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
		'code' => random(4, true),
		'order_type' => $order_type,
		'openid' => $_W['openid'],
		'mobile' => $address['mobile'],
		'username' => $address['realname'],
		'sex' => $address['sex'],
		'address' => $address['address'] . $address['number'],
		'location_x' => $address['location_x'],
		'location_y' => $address['location_y'],
		'delivery_day' => trim($_GPC['delivery_day']) ? (date('Y') .'-'. trim($_GPC['delivery_day'])) : date('Y-m-d'),
		'delivery_time' => trim($_GPC['delivery_time']) ? trim($_GPC['delivery_time']) : '尽快送出',
		'delivery_fee' => $delivery_price,
		'pack_fee' => $store['pack_price'],
		'pay_type' => trim($_GPC['pay_type']),
		'num' => $cart['num'],
		'distance' => $distance,
		'box_price' => $cart['box_price'],
		'price' => $cart['price'],
		'total_fee' => $total_fee,
		'discount_fee' => $activityed['total'],
		'store_discount_fee' => $activityed['store_discount_fee'],
		'plateform_discount_fee' => $activityed['plateform_discount_fee'],
		'agent_discount_fee' => $activityed['agent_discount_fee'],
		'final_fee' => $total_fee - $activityed['total'],
		'vip_free_delivery_fee' => !empty($activityed['list']['vip_delivery']) ? 1 : 0,
		'delivery_type' => $store['delivery_mode'],
		'status' => 1,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => array(
			'cart' => iunserializer($cart['original_data']),
			'commission' => array(
				'spread1_rate' => "0%",
				'spread1' => 0,
				'spread2_rate' => "0%",
				'spread2' => 0,
			)
		),
		'note' => trim($_GPC['note']),
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	$order['spreadbalance'] = 1;

	if(check_plugin_perm('spread')) {
		if(!empty($_W['member']['spread1']) && $_W['member']['spreadfixed'] == 1) {
			mload()->model('plugin');
			$_W['plugin'] = array(
				'name' => 'spread'
			);
			pload()->model('spread');
			$config_spread = get_plugin_config('spread');

			$order['spread1'] = $_W['member']['spread1'];
			if($config_spread['basic']['level'] == 2) {
				$order['spread2'] = $_W['member']['spread2'];
			}
			$spreads = pdo_fetchall('select uid,spread_groupid from ' . tablename('tiny_wmall_members') . ' where uid = :uid1 or uid = :uid2', array(':uid1' => $order['spread1'], ':uid2' => $order['spread2']), 'uid');
			if(!empty($spreads)) {
				$order['spreadbalance'] = 0;
				$groups = spread_groups();
				$spread1_rate = $groups[$spreads[$order['spread1']]['spread_groupid']]['commission1'] / 100;
				$commission_spread1 = round($spread1_rate * $order['final_fee'], 2);
				if(!empty($order['spread2'])) {
					$spread2_rate = $groups[$spreads[$order['spread2']]['spread_groupid']]['commission2'] / 100;
					$commission_spread2 = round($spread2_rate * $order['final_fee'], 2);
				}
				$spread1_rate = $spread1_rate * 100;
				$spread2_rate = $spread2_rate * 100;
				$order['data']['spread'] = array(
					'commission' => array(
						'spread1_rate' => "{$spread1_rate}%",
						'spread1' => $commission_spread1,
						'spread2_rate' => "{$spread2_rate}%",
						'spread2' => $commission_spread2,
					)
				);
			}
		}
	}
	$order['data'] = iserializer($order['data']);
	pdo_insert('tiny_wmall_order', $order);
	$order_id = pdo_insertid();
	order_update_bill($order_id, array('activity' => $activityed));
	order_insert_discount($order_id, $sid, $activityed['list']);
	order_insert_status_log($order_id, 'place_order');
	order_update_goods_info($order_id, $sid);
	order_del_member_cart($sid);
	imessage(error(0, $order_id), '', 'ajax');
}
include itemplate('order/create');
