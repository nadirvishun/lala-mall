<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function is_open_order($order) {
	if(!is_array($order) || empty($order['order_plateform'])) {
		$id = intval($order);
		$order = pdo_get('tiny_wmall_order', array('id' => $id), array('order_plateform'));
	}
	return $order['order_plateform'] != 'we7_wmall';
}

function order_fetch($id, $oauth = false) {
	global $_W;
	$id = intval($id);
	$condition = ' where uniacid = :uniacid and id = :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':id' => $id,
	);
	if($oauth) {
		$condition .= ' and uid = :uid';
		$params[':uid'] = $_W['member']['uid'];
	}
	$order = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition, $params);
	if(empty($order)) {
		return false;
	}
	$order_status = order_status();
	$pay_types = order_pay_types();
	$order_types = order_types();
	$order['order_type_cn'] = $order_types[$order['order_type']]['text'];
	$order['status_cn'] = $order_status[$order['status']]['text'];
	if(!empty($order['plateform_serve'])) {
		$order['plateform_serve'] = iunserializer($order['plateform_serve']);
	}
	if(!empty($order['agent_serve'])) {
		$order['agent_serve'] = iunserializer($order['agent_serve']);
	}
	if(empty($order['is_pay'])) {
		$order['pay_type_cn'] = '未支付';
	} else {
		$order['pay_type_cn'] = !empty($pay_types[$order['pay_type']]['text']) ? $pay_types[$order['pay_type']]['text'] : '其他支付方式';
	}
	if(empty($order['delivery_time'])) {
		$order['delivery_time'] = '尽快送出';
	}
	if($order['order_type'] == 3) {
		//扫码点餐
		$table = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'id' => $order['table_id']));
		$order['table'] = $table;
	} elseif($order['order_type'] == 4) {
		//预定
		$reserve_type = order_reserve_type();
		$order['reserve_type_cn'] = $reserve_type[$order['reserve_type']]['text'];
		$category = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $order['table_cid']));
		$order['table_category'] = $category;
	}
	$order['pay_type_class'] = '';
	if($order['is_pay'] == 1) {
		$order['pay_type_class'] = 'have-pay';
		if($order['pay_type'] == 'delivery') {
			$order['pay_type_class'] = 'delivery-pay';
		}
	}
	return $order;
}

function order_fetch_goods($oid, $print_lable = '') {
	global $_W;
	$oid = intval($oid);
	$condition = 'WHERE uniacid = :uniacid AND oid = :oid';
	if(!empty($print_lable)) {
		$condition .= " AND print_label in ({$print_lable})";
	}
	$params = array(
		':uniacid' => $_W['uniacid'],
		':oid' => $oid,
	);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order_stat') . $condition, $params);
	return $data;
}

function order_fetch_discount($id, $type = '') {
	global $_W;
	if(empty($type))  {
		$data = pdo_getall('tiny_wmall_order_discount', array('uniacid' => $_W['uniacid'], 'oid' => $id));
	} else {
		$data = pdo_get('tiny_wmall_order_discount', array('uniacid' => $_W['uniacid'], 'oid' => $id, 'type' => $type));
	}
	return $data;
}

function order_place_again($sid, $order_id) {
	global $_W;
	$order = order_fetch($order_id);
	if(empty($order)) {
		return false;
	}
	$order['data'] = iunserializer($order['data']);
	$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	$data = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'groupid' => $_W['member']['groupid'],
		'num' => $order['num'],
		'price' => $order['price'],
		'box_price' => $order['box_price'],
		'original_data' => $order['data']['cart'] ? $order['data']['cart'] : $order['data'],
		'addtime' => TIMESTAMP,
	);
	$original_data = $data['original_data'];
	$data['original_data'] = iserializer($original_data);
	if(empty($isexist)) {
		pdo_insert('tiny_wmall_order_cart', $data);
	} else {
		pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
	}
	$data['original_data'] = $original_data;
	return $data;
}

//order_insert_discount
function order_insert_discount($id, $sid, $discount_data) {
	global $_W;
	if(empty($discount_data)) {
		return false;
	}
	if(!empty($discount_data['token'])) {
		pdo_update('tiny_wmall_activity_coupon_record', array('status' => 2, 'usetime' => TIMESTAMP, 'order_id' => $id), array('uniacid' => $_W['uniacid'], 'id' => $discount_data['token']['recordid']));
	}
	if(!empty($discount_data['redPacket'])) {
		pdo_update('tiny_wmall_activity_redpacket_record', array('status' => 2, 'usetime' => TIMESTAMP, 'order_id' => $id), array('uniacid' => $_W['uniacid'], 'id' => $discount_data['redPacket']['redPacket_id']));
	}
	foreach($discount_data as $data) {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'oid' => $id,
			'type' => $data['type'],
			'name' => $data['name'],
			'icon' => $data['icon'],
			'note' => $data['text'],
			'fee' => $data['value'],
			'store_discount_fee' => floatval($data['store_discount_fee']),
			'agent_discount_fee' => floatval($data['agent_discount_fee']),
			'plateform_discount_fee' => floatval($data['plateform_discount_fee']),
		);
		pdo_insert('tiny_wmall_order_discount', $insert);
	}
	return true;
}

// order_insert_member_cart
function order_insert_member_cart($sid, $ignore_bargain = false) {
	global $_W, $_GPC;
	if(!empty($_GPC['goods'])) {
		//修复&nbsp;在utf8编码下被转换成黑块的坑
		$_GPC['goods'] = str_replace('&nbsp;', '#nbsp;', $_GPC['goods']);
		$_GPC['goods'] = json_decode(str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($_GPC['goods']))), true);
		if(empty($_GPC['goods'])) {
			return array();
		}
		mload()->model('goods');

		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :uniacid AND sid = :sid AND id IN ($ids_str)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
		$options = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods_options') . " where uniacid = :uniacid and sid = :sid and goods_id in ($ids_str) ", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
		foreach($options as $option) {
			$goods_info[$option['goods_id']]['options'][$option['id']] = $option;
		}

		if(!$ignore_bargain) {
			mload()->model('activity');
			activity_store_cron($sid);

			$bargains = pdo_getall('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => '1'), array(), 'id');
			if(!empty($bargains)) {
				$bargain_ids = implode(',', array_keys($bargains));
				$bargain_goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_bargain_goods') . " where uniacid = :uniacid and sid = :sid and bargain_id in ({$bargain_ids})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
				$bargain_goods_group = array();
				if(!empty($bargain_goods)) {
					foreach($bargain_goods as &$row) {
						$row['available_buy_limit'] = $row['max_buy_limit'];
						$bargain_goods_group[$row['bargain_id']][$row['goods_id']] = $row;
					}
				}
				foreach($bargains as &$row) {
					$row['available_goods_limit'] = $row['goods_limit'];
					$row['goods'] = $bargain_goods_group[$row['id']];
				}
			} else {
				$bargains = array();
			}
		}
		$total_num = 0;
		$total_original_price = 0;
		$total_price = 0;
		$total_box_price = 0;
		$cart_bargain = array();
		foreach($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			$goods = $goods_info[$k];
			if(empty($goods) || $k == '88888') {
				continue;
			}
			$goods['options_data'] = goods_build_options($goods);
			$goods_box_price = $goods['box_price'];
			if(!$goods['is_options']) {
				$discount_num = 0;
				foreach($v['options'] as $key => $val) {
					$key = trim($key);
					$option = $goods['options_data'][$key];
					if(empty($option)) {
						continue;
					}
					$num = intval($val['num']);
					if($num <= 0) {
						continue;
					}
					$title = $goods_info[$k]['title'];
					if(!empty($key)) {
						$title = "{$title}({$option['name']})";
					}
					$cart_item = array(
						'title' => $title,
						'option_title' => $option['name'],
						'num' => $num,
						'price' => $goods_info[$k]['price'],
						'discount_price' => $goods_info[$k]['price'],
						'discount_num' => 0,
						'price_num' => $num,
						'total_price' => round($goods_info[$k]['price'] * $num, 2),
						'total_discount_price' => round($goods_info[$k]['price'] * $num, 2),
						'bargain_id' => 0
					);
					$bargain = $bargains[$val['bargain_id']];
					$bargain_goods = $bargain['goods'][$k];

					if($val['bargain_id'] > 0 && $val['discount_num'] > 0) {
						//max_buy_limit：每单限购
						if($val['discount_num'] > $bargain_goods['max_buy_limit']) {
							$val['discount_num'] = $bargain_goods['max_buy_limit'];
						}
						$params = array(
							':uniacid' => $_W['uniacid'],
							':uid' => $_W['member']['uid'],
							':stat_day' => date('Ymd'),
							':bargain_id' => $bargain['id'],
						);
						$numed = pdo_fetchcolumn('select count(distinct(oid))  from ' . tablename('tiny_wmall_order_stat') . ' where uniacid = :uniacid and uid = :uid and bargain_id = :bargain_id and stat_day = :stat_day', $params);
						$numed = intval($numed);
						//available_goods_limit:每单限购几种折扣商品，available_buy_limit：没单限购折扣商品几份，discount_available_total：折扣商品的库存
						if($bargain['order_limit'] > $numed && $bargain['available_goods_limit'] > 0 && $bargain_goods['available_buy_limit'] > 0) {
							for($i = 0; $i < $val['discount_num']; $i++) {
								if($bargain_goods['poi_user_type'] == 'new' && empty($_W['member']['is_store_newmember'])) {
									break;
								}
								if(($bargain_goods['discount_available_total'] == -1 || $bargain_goods['discount_available_total'] > 0) && $bargain_goods['available_buy_limit'] > 0) {
									$cart_item['discount_price'] = $bargain_goods['discount_price'];
									$cart_item['discount_num']++;
									$cart_item['bargain_id'] = $bargain['id'];
									$cart_bargain[] = $bargain['use_limit'];
									if($cart_item['price_num'] > 0) {
										$cart_item['price_num']--;
									}
									if($bargain_goods['discount_available_total'] > 0) {
										$bargain_goods['discount_available_total']--;
										$bargains[$val['bargain_id']]['goods'][$k]['discount_available_total']--;
									}
									$bargain_goods['available_buy_limit']--;
									$bargains[$val['bargain_id']]['goods'][$k]['available_buy_limit']--;
									$discount_num++;
								} else {
									break;
								}
							}
							$cart_item['total_discount_price'] = $cart_item['discount_num'] * $bargain_goods['discount_price'] + $cart_item['price_num'] * $goods_info[$k]['price'] ;
							$cart_item['total_discount_price'] = round($cart_item['total_discount_price'], 2);
						}
					}

					$total_num += $num;
					$total_price += $cart_item['total_discount_price'];
					$total_original_price += $cart_item['total_price'];
					$total_box_price += ($goods_box_price * $num);
					$cart_goods[$k][$key] = $cart_item;
				}

				if($discount_num > 0) {
					$bargain['available_goods_limit']--;
					$bargains[$val['bargain_id']]['goods'][$k]['available_goods_limit']--;
				}
			} else {
				foreach($v['options'] as $key => $val) {
					$key = trim($key);
					$option = $goods['options_data'][$key];
					if(empty($option)) {
						continue;
					}
					$title = $goods_info[$k]['title'];
					if(!empty($key)) {
						$title = "{$title}({$option['name']})";
					}
					$cart_goods[$k][$key] = array(
						'title' => $title,
						'option_title' => $option['name'],
						'num' => $val['num'],
						'price' => $option['price'],
						'discount_price' => $option['price'],
						'discount_num' => 0,
						'price_num' => $num,
						'total_price' => round($option['price'] * $val['num'], 2),
						'total_discount_price' => round($option['price'] * $val['num'], 2),
						'bargain_id' => 0
					);
					$total_num += $val['num'];
					$total_price += $option['price'] * $val['num'];
					$total_original_price += $option['price'] * $val['num'];
					$total_box_price += $goods_box_price * $val['num'];
				}
			}
		}
		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'uid' => $_W['member']['uid'],
			'groupid' => $_W['member']['groupid'],
			'num' => $total_num,
			'price' => $total_price,
			'box_price' => round($total_box_price, 2),
			'data' => iserializer($cart_goods),
			'original_data' => iserializer($_GPC['goods']),
			'addtime' => TIMESTAMP,
			'bargain_use_limit' => 0,
		);
		if(!empty($cart_bargain)) {
			$cart_bargain = array_unique($cart_bargain);
			if(in_array(1, $cart_bargain)) {
				$data['bargain_use_limit'] = 1;
			}
			if(in_array(2, $cart_bargain)) {
				$data['bargain_use_limit'] = 2;
			}
		}
		if(empty($isexist)) {
			pdo_insert('tiny_wmall_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
		}
		$data['data'] = $cart_goods;
		$data['original_data'] = $_GPC['goods'];
		return $data;
	} else {
		return error(-1, '商品信息错误');
	}
	return true;
}

function order_dispatch_analyse($id) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$_W['agentid'] = $order['agentid'];
	$store = pdo_get('tiny_wmall_store', array('id' => $order['sid']), array('location_x', 'location_y'));
	$order['store'] = $store;

	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$filter = array();
	if(!$config_takeout['over_collect_max_notify'] && $config_takeout['deliveryer_collect_max'] > 0) {
		$filter = array('order_takeout_num' => $config_takeout['deliveryer_collect_max']);
	}
	$deliveryers = deliveryer_fetchall($filter);
	if(!empty($deliveryers)) {
		foreach($deliveryers as &$deliveryer) {
			$deliveryer['order_id'] = $id;
			if(empty($order['location_x']) || empty($order['location_y']) || empty($deliveryer['deliveryer']['location_y']) || empty($deliveryer['deliveryer']['location_x'])) {
				$deliveryer['store2deliveryer_distance'] = '未知';
				$deliveryer['store2user_distance'] = '未知';
			} else {
				$deliveryer['store2user_distance'] = distanceBetween($order['location_y'], $order['buy_location_x'], $store['location_y'], $store['location_x']);
				$deliveryer['store2user_distance'] = round($deliveryer['store2user_distance']/1000, 2) . 'km';
				$deliveryer['store2deliveryer_distance'] = distanceBetween($store['location_y'], $store['location_x'], $deliveryer['deliveryer']['location_y'], $deliveryer['deliveryer']['location_x']);
				$deliveryer['store2deliveryer_distance'] = round($deliveryer['store2deliveryer_distance']/1000, 2) . 'km';
			}
		}
		$deliveryers = array_sort($deliveryers, 'store2deliveryer_distance');
		$order['deliveryers'] = $deliveryers;
	} else {
		return error(-1, '没有平台配送员，无法进行自动调度');
	}
	return $order;
}

function deliveryer_fetchall($sid = 0, $filter = array()) {
	global $_W;
	$data = pdo_fetchall("SELECT id,sid,deliveryer_id,delivery_type,work_status,extra FROM " . tablename('tiny_wmall_store_deliveryer') . ' WHERE uniacid = :uniacid and agentid = :agentid and sid = :sid and work_status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':sid' => $sid), 'deliveryer_id');
	if(!empty($data)) {
		$ids = implode(',', array_keys($data));
		$condition = " WHERE uniacid = {$_W['uniacid']} and agentid = {$_W['agentid']} and id in ({$ids})";
		if(!empty($filter)) {
			foreach($filter as $key => $val) {
				if(in_array($key, array('order_takeout_num', 'order_errander_num'))) {
					$condition .= " and {$key} < {$val}";
				}
			}
		}
		$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_deliveryer') . $condition, array(), 'id');
		foreach($data as &$da) {
			$da['extra'] = iunserializer($da['extra']);
			$deliveryers[$da['deliveryer_id']]['avatar'] = tomedia($deliveryers[$da['deliveryer_id']]['avatar']);
			$da['deliveryer'] = $deliveryers[$da['deliveryer_id']];
		}
	}
	return $data;
}

function activity_getall($sid, $status = -1) {
	global $_W;
	activity_store_cron($sid);
	$params =  array('uniacid' => $_W['uniacid'], 'sid' => $sid);
	if($status >= 0) {
		$params['status'] = $status;
	}
	$activity = pdo_getall('tiny_wmall_store_activity', $params, array(), 'type');
	if(!empty($activity)) {
		foreach($activity as &$row) {
			$row['data'] = iunserializer($row['data']);
		}
	}
	return $activity;
}
