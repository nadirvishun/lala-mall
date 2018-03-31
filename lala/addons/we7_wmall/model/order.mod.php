<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('order.extra');
function set_order_data($id, $key, $value) {
	global $_W;
	$data = get_order_config();
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$data[$keys[0]] = $value;
	} elseif($counts == 2) {
		$data[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		$data[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_config', array('data' => iserializer($data)), array('uniacid' => $_W['uniacid'], 'id' => $id));
	return true;
}

function get_order_data($id, $key) {
	global $_W;
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('data', 'id'));
	if(empty($order['data'])) {
		return array();
	}
	$data = iunserializer($order['data']);
	if(!is_array($data)) {
		$data = array();
	}
	if(empty($key)) {
		return $data;
	}
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		return $data[$key];
	} elseif($counts == 2) {
		return $data[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $data[$keys[0]][$keys[1]][$keys[2]];
	}
}

function order_cancel_types($role = 'clerker') {
	$types = array(
		'clerker' => array(
			'fakeOrder' => '用户信息不符',
			'foodSoldOut' => '商品已经售完',
			'restaurantClosed' => '商家已经打烊',
			'distanceTooFar' => '超出配送范围',
			'restaurantTooBusy' => '商家现在太忙',
			'forceRejectOrder' => '用户申请取消',
			'deliveryFault' => '配送出现问题',
			'notSatisfiedDeliveryRequirement' => '不满足起送要求',
		),
		'manager' => array(
			'fakeOrder' => '用户信息不符',
			'foodSoldOut' => '商品已经售完',
			'restaurantClosed' => '商家已经打烊',
			'distanceTooFar' => '超出配送范围',
			'restaurantTooBusy' => '商家现在太忙',
			'forceRejectOrder' => '用户申请取消',
			'deliveryFault' => '配送出现问题',
			'notSatisfiedDeliveryRequirement' => '不满足起送要求',
		),
		'consumer' => array(),
	);
	return $types[$role];
}

function order_cancel_reason($id) {
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where oid = :id and status = 6 order by id desc', array(':id' => $id));
	if(empty($log)) {
		return '未知';
	}
	$reason = "未知";
	if(!empty($log['note'])) {
		$reason = "{$log['note']}";
	}
	if(!empty($log['role_cn'])) {
		$reason = "{$log['note']}。操作人:{$log['role_cn']}";
	}
	return $reason;
}
//order_insert_status_log
function order_insert_status_log($id, $type, $note = '', $role = '', $role_cn = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	mload()->model('store');
	$order = order_fetch($id);
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$notes = array(
		'place_order' => array(
			'status' => 1,
			'title' => '订单提交成功',
			'note' => "单号:{$order['ordersn']},请耐心等待商家确认",
			'ext' => array(
				array(
					'key' => 'pay_time_limit',
					'title' => '订单待支付',
					'note' => "请在订单提交后{$config_takeout['pay_time_limit']}分钟内完成支付",
				)
			)
		),
		'handle' => array(
			'status' => 2,
			'title' => '商户已确认订单',
			'note' => '正在为您准备商品'
		),
		'delivery_wait' => array(
			'status' => 3,
			'title' => '商品已准备就绪',
			'note' => '商品已准备就绪,正在分配配送员'
		),
		'delivery_ing' => array(
			'status' => 3,
			'title' => '商品已准备就绪',
			'note' => '商品已准备就绪,商家正在为您配送中'
		),
		'delivery_assign' => array(
			'status' => 4,
			'title' => '已分配配送员',
			'note' => ''
		),
		'delivery_instore' => array(
			'status' => 12,
			'title' => '配送员已到店',
			'note' => '配送员已到店, 取货完成, 骑士将骑上战马为您急速送达'
		),
		'delivery_transfer' => array(
			'status' => 13,
			'title' => '配送员申请转单',
			'note' => ''
		),
		'end' => array(
			'status' => 5,
			'title' => '订单已完成',
			'note' => '任何意见和吐槽,都欢迎联系我们'
		),
		'cancel' => array(
			'status' => 6,
			'title' => '订单已取消',
			'note' => ''
		),
		'pay' => array(
			'status' => 7,
			'title' => '订单已支付',
			'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s'),
			'ext' => array(
				array(
					'key' => 'handle_time_limit',
					'title' => '等待商户接单',
					'note' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,将自动取消订单",
				)
			)
		),
		'remind' => array(
			'status' => 8,
			'title' => '商家已收到催单',
			'note' => '商家会尽快回复您的催单请求'
		),
		'remind_reply' => array(
			'status' => 9,
			'title' => '商家回复了您的催单',
			'note' => ''
		),
		'delivery_success' => array(
			'status' => 10,
			'title' => '订单配送完成',
			'note' => ''
		),
		'delivery_fail' => array(
			'status' => 10,
			'title' => '订单配送失败',
			'note' => ''
		),
	);
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$role = !empty($role) ? $role : $_W['role'];
	$role_cn = !empty($role_cn) ? $role_cn : $_W['role_cn'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'status' => $notes[$type]['status'],
		'type' => $type,
		'role' => $role,
		'role_cn' => $role_cn,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_order_status_log', $data);
	if(!empty($notes[$type]['ext'])) {
		foreach($notes[$type]['ext'] as $val) {
			if($val['key'] == 'pay_time_limit' && !$config_takeout['pay_time_limit']) {
				unset($val['note']);
			}
			if($val['key'] == 'handle_time_limit' && empty($config_takeout['handle_time_limit'])) {
				unset($val['note']);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $id,
				'title' => $val['title'],
				'note' => $val['note'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_order_status_log', $data);
		}
	}
	return true;
}

//order_fetch_status_log
function order_fetch_status_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_status_log') . ' WHERE uniacid = :uniacid and oid = :oid order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id), 'id');
	return $data;
}

function order_fetch_refund_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_refund_log') . ' WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id, ':order_type' => 'order'), 'id');
	return $data;
}

//print_order
function order_print($id, $type = 'order') {
	global $_W;
	$order= order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$sid = intval($order['sid']);
	$store = store_fetch($order['sid'], array('title'));
	//获取该门店的所有打印机
	$prints = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($prints)) {
		return error(-1, '没有有效的打印机');
	}
	mload()->model('print');
	if($type == 'collect') {
		$content = array(
			"<CB>#{$order['serial_sn']} {$store['title']}</CB>",
			"<C>#{$order['serial_sn']} 号单已被配送员接单</C>",
		);
		foreach($prints as $li) {
			if(!empty($li['print_no'])) {
				//默认都只打印一次
				if(($li['type'] == 'feiyin' || $li['type'] == 'AiPrint')) {
					$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, 1, $order['ordersn'] . random(10, true));
				} else {
					$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, 1, $order['ordersn'] . random(10, true));
				}
			}
		}
	} else {
		$num = 0;
		$grant = pdo_get('tiny_wmall_order_discount', array('oid' => $id, 'type' => 'grant'));
		foreach($prints as $li) {
			if(!empty($li['print_no'])) {
				$content = array(
					'title' => "<CB>#{$order['serial_sn']} {$store['title']}</CB>",
				);
				if(!empty($order['note'])) {
					$content[] = "<L>备注:{$order['note']}</L>";
				}
				if($order['order_type'] == 1) {
					$content[] = "<L>配送时间:{$order['delivery_day']} {$order['delivery_time']}</L>";
				} elseif($order['order_type'] >= 3) {
					$content[] = "<CB>{$order['table']['title']}桌</CB>";
				}
				if($li['is_print_all'] == 1) {
					$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
					if(!empty($order['goods'])) {
						if(!empty($li['print_header'])) {
							$content[] = $li['print_header'];
						}
						$content[] = '名称　　　数量　　单价　　金额';
						$content[] = '******************************';
						foreach($order['goods'] as $di) {
							if(!empty($di['goods_number'])) {
								$di['goods_title'] = "{$di['goods_title']}-{$di['goods_number']}";
							}
							$content[] = "<L>{$di['goods_title']}</L>";
							$str = '';
							$str .= '　　　　　<L>X' . str_pad($di['goods_num'], '7', ' ', STR_PAD_RIGHT) . '</L>';
							$str .= '<L>' . str_pad($di['goods_unit_price'], '7', ' ', STR_PAD_RIGHT). '</L>';
							$str .= ' <L>' . str_pad($di['goods_price'], '5', ' ', STR_PAD_RIGHT). '</L>';
							$content[] = $str;
						}
						$content[] = '******************************';
						$content[] = "订单类型:{$order['order_type_cn']}";
						$content[] = "订单　号:{$order['ordersn']}";
						$content[] = "支付方式:{$order['pay_type_cn']}";
						if($order['box_price'] > 0) {
							$content[] = "餐盒　费:{$order['box_price']}元";
						}
						if($order['pack_fee'] > 0) {
							$content[] = "包装　费:{$order['pack_fee']}元";
						}
						if($order['delivery_fee'] > 0) {
							$content[] = "配送　费:{$order['delivery_fee']}元";
						}
						if($order['order_type'] >= 3) {
							$content[] = '';
							$content[] = "合　　计:<L>{$order['total_fee']}元</L>";
							$content[] = '';
							if($order['discount_fee'] > 0) {
								$content[] = "线上优惠:<L>-{$order['discount_fee']}元</L>";
								$content[] = '';
								$content[] = "实际支付:<L>{$order['final_fee']}元</L>";
								$content[] = '';
							}
						} else {
							$content[] = "合　　计:{$order['total_fee']}元";
							if($order['discount_fee'] > 0) {
								$content[] = "线上优惠:-{$order['discount_fee']}元";
								$content[] = "实际支付:{$order['final_fee']}元";
							}
						}
						if(!empty($grant)) {
							$content[] = "赠　　品:{$grant['note']}";
						}
						if($order['order_type'] == 1) {
							$content[] = "下单　人:{$order['username']}";
							$content[] = "联系电话:{$order['mobile']}";
							$content[] = "配送地址:{$order['address']}";
						} elseif($order['order_type'] == 2) {
							$content[] = "下单　人:{$order['username']}";
							$content[] = "联系电话:{$order['mobile']}";
						} elseif($order['order_type'] == 3) {
							$content[] = "来客人数:{$order['person_num']}";
						} elseif($order['order_type'] == 4) {
							$content[] = "预定时间:{$order['reserve_time']}";
							$content[] = "桌台类型:{$order['table_category']['title']}~{$order['table']['title']}桌";
						}
						$content[] = "下单时间:" . date('Y-m-d H:i', $order['addtime']);
						if(!empty($order['invoice'])) {
							$content[] = "发票信息:{$order['invoice']}";
						}
						if(!empty($li['print_footer'])) {
							$content[] = $li['print_footer'];
						}
						if($li['qrcode_type'] == 'delivery_assign') {
							$li['qrcode_link'] = imurl('delivery/order/takeout/detail', array('id' => $order['id'], 'r' => 'collect'), true);
						}
						if(!empty($li['qrcode_link'])) {
							if(strlen($li['qrcode_link']) > 110) {
								$li['qrcode_link'] = longurl2short($li['qrcode_link']);
								if(is_error($li['qrcode_link'])) {
									$li['qrcode_link'] = longurl2short($li['qrcode_link']);
								}
							}
							$content['qrcode'] = "<QR>{$li['qrcode_link']}</QR>";
						}
						if($li['type'] == 'feie') {
							$content[] = implode('', array("\x1b","\x64","\x01","\x1b","\x70","\x30","\x1e","\x78"));
						} elseif($li['type'] == 'qiyun' && $li['print_nums'] > 0) {
							$content[] = "<N>{$li['print_nums']}</N>";
						}

						if(($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
							for($i = 0; $i < $li['print_nums']; $i++) {
								$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, $li['print_nums'], $order['ordersn'] . random(10, true));
								if(!is_error($status)) {
									$num++;
								}
							}
						} else {
							$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, $li['print_nums'], $order['ordersn'] . random(10, true));
							if(!is_error($status)) {
								$num += $li['print_nums'];
							}
						}
					} else {
						return error(-1, '订单没有任何商品');
					}
				} else {
					$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
					if(!empty($order['goods'])) {
						$content = array(
							"订单　号:{$order['ordersn']}",
							"下单时间:" . date('Y-m-d H:i', $order['addtime']),
						);
						if($order['order_type'] == 1) {
							$content[] = "订单类型:外卖单";
							$content[] = "配送时间:{$order['delivery_day']} {$order['delivery_time']}";
						} elseif($order['order_type'] == 2) {
							$content[] = "订单类型:自提单";
							$content[] = "自提时间:{$order['delivery_day']} {$order['delivery_time']}";
						} elseif($order['order_type'] == 3) {
							$content[] = "订单类型:堂食单";
							$content[] = "桌　　号:{$order['table']['title']}桌";
						} elseif($order['order_type'] == 4) {
							$content[] = "订单类型:预定单";
							$content[] = "预定时间:{$order['reserve_time']}";
						}
						$content[] = '名称　　　数量　　单价　　金额';
						$content[] = '******************************';
						foreach($order['goods'] as $di) {
							$str = '';
							$str .= '　　　　　X' . str_pad($di['goods_num'], '7', ' ', STR_PAD_RIGHT);
							$str .= '' . str_pad($di['goods_unit_price'], '7', ' ', STR_PAD_RIGHT);
							$str .= ' ' . str_pad($di['goods_price'], '5', ' ', STR_PAD_RIGHT);
							if(!empty($di['goods_number'])) {
								$di['goods_title'] = "{$di['goods_title']}-{$di['goods_number']}";
							}
							$goods_info = array(
								$di['goods_title'],
								$str,
								'******************************'
							);
							$content_merge = array_merge($content, $goods_info);
							if($li['type'] == 'qiyun' && $li['print_nums'] > 0) {
								$content[] = "<N>{$li['print_nums']}</N>";
							}
							if(($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
								for($i = 0; $i < $li['print_nums']; $i++) {
									$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content_merge, $li['print_nums'], $order['ordersn'] . random(10, true));
									if(!is_error($status)) {
										$num++;
									}
								}
							} else {
								$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content_merge, $li['print_nums'], $order['ordersn'] . random(10, true));
								if(!is_error($status)) {
									$num += $li['print_nums'];
								}
							}
						}
					} else {
						return error(-1, '订单没有任何商品');
					}
				}
			}
		}
		if($num > 0) {
			pdo_query('UPDATE ' . tablename('tiny_wmall_order') . " SET print_nums = print_nums + {$num} WHERE uniacid = {$_W['uniacid']} AND id = {$order['id']}");
		} else {
			return error(-1,'发送打印指令失败。没有有效的打印机或没有开启打印机');
		}
	}
	return true;
}

function order_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '所有',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-default',
			'text' => '待确认',
			'color' => '',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '处理中',
			'color' => 'color-primary'
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '待配送',
			'color' => 'color-warning'
		),
		'4' => array(
			'css' => 'label label-warning',
			'text' => '配送中',
			'color' => 'color-warning'
		),
		'5' => array(
			'css' => 'label label-success',
			'text' => '已完成',
			'color' => 'color-success'
		),
		'6' => array(
			'css' => 'label label-danger',
			'text' => '已取消',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function order_trade_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '交易成功',
		),
		'2' => array(
			'css' => 'label label-warning',
			'text' => '交易进行中',
		),
		'3' => array(
			'css' => 'label label-danger',
			'text' => '交易失败',
		),
		'4' => array(
			'css' => 'label label-default',
			'text' => '交易关闭',
		),
	);
	return $data;
}

function order_trade_type() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '外卖店内订单入账',
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '申请提现',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '其他变动',
		),
		'4' => array(
			'css' => 'label label-success',
			'text' => '买单订单入账',
		),
	);
	return $data;
}


function order_delivery_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '',
			'color' => ''
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '待配送',
			'color' => 'color-warning'
		),
		'4' => array(
			'css' => 'label label-warning',
			'text' => '配送中',
			'color' => 'color-warning'
		),
		'5' => array(
			'css' => 'label label-success',
			'text' => '配送成功',
			'color' => 'color-success'
		),
		'6' => array(
			'css' => 'label label-danger',
			'text' => '配送失败',
			'color' => 'color-danger'
		),
		'7' => array(
			'css' => 'label label-danger',
			'text' => '待取货',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function order_types() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '外卖',
			'color' => 'color-success'
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '自提',
			'color' => 'color-danger'
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '店内',
			'color' => 'color-info'
		),
		'4' => array(
			'css' => 'label label-info',
			'text' => '预定',
			'color' => 'color-info'
		),
	);
	return $data;
}

function order_reserve_type() {
	$data = array(
		'table' => array(
			'css' => 'label label-success',
			'text' => '只订座',
			'color' => 'color-success'
		),
		'order' => array(
			'css' => 'label label-danger',
			'text' => '预定商品',
			'color' => 'color-danger'
		),
	);
	return $data;
}

//order_status_notice
function order_status_notice($id, $status, $note = '') {
	global $_W;
	$status_arr = array(
		'handle', //处理中
		'delivery_assign', //抢单完成
		'delivery_instore', //确认到店
		'delivery_ing', //商家直接设置订单为配送中
		'end', //已完成
		'cancel',//已取消
		'pay',//已支付
		'remind',
		'reply_remind',
		'delivery_notice'
	);
	if(!in_array($status, $status_arr)) {
		return false;
	}
	$type = $status;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$store = store_fetch($order['sid'], array('title'));
	$deliveryer = array();
	if(!empty($order['deliveryer_id'])) {
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('id' => $order['deliveryer_id']));
	}
	if(!empty($order['openid'])) {
		$acc = WeAccount::create($order['acid']);
		if($type == 'pay') {
			$title = '您的订单已付款';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"支付方式: {$order['pay_type_cn']}",
				"支付时间: " . date('Y-m-d H: i', time()),
			);
		}

		if($type == 'handle') {
			$title = '商家已接单,正在准备商品中...';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"处理时间: " . date('Y-m-d H: i', time()),
			);
		}

		if($type == 'delivery_assign' || $type == 'delivery_ing') {
			$title = '您的订单正在为您配送中';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
			);
			$end_remark = "配送员已抢单, 正赶至商家取货, 骑士将骑上战马为您急速送达, 请保持电话畅通";
		}

		if($type == 'delivery_instore') {
			$title = '配送员已取货，正在配送中';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
			);
			$end_remark = "配送员已取货，正在为您配送中。";
		}

		if($type == 'delivery_notice') {
			$title = "配送员【{$deliveryer['title']}】已达到你下单时填写的送货地址, 配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电";
			$remark = array(
				"门店名称: {$store['title']}",
				"配送员: {$deliveryer['title']}",
				"手机号: {$deliveryer['mobile']}",
			);
			unset($note);
		}

		if($type == 'end') {
			$title = '订单处理完成';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"完成时间: " . date('Y-m-d H: i', time()),
			);
			$end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.";
			$grant = get_plugin_config('ordergrant.share');
			if(check_plugin_perm('ordergrant') && $grant['status'] && $grant['share_grant'] > 0) {
				$end_remark .= "评价并分享订单即可获取{$grant['share_grant']}{$grant['grantType_cn']}奖励";
			}
		}

		if($type == 'cancel') {
			$title = '订单已取消';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"取消时间: " . date('Y-m-d H: i', time()),
			);
		}

		if($type == 'reply_remind') {
			$title = '订单催单有新的回复';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"回复时间: " . date('Y-m-d H: i', time()),
			);
		}

		if($type == 'reserve_order_pay') {
			$title = '你的预定单已支付';
			$remark = array(
				"门店名称: {$store['title']}",
				"订单类型: {$order['order_type_cn']}",
				"支付方式: {$order['pay_type_cn']}",
				"预定时间: {$order['reserve_time']}",
				"预定桌台: {$order['table_category']['title']}",
				"预定类型: {$order['reserve_type_cn']}",
			);
		}

		if(!empty($note)) {
			if(!is_array($note)) {
				$remark[] = $note;
			} else {
				$remark[] = implode("\n", $note);;
			}
		}
		if(!empty($end_remark)) {
			$remark[] = $end_remark;
		}
		$url = imurl('wmall/order/index/detail', array('id' => $order['id']), true);
		if(is_array($remark)) {
			$remark = implode("\n", $remark);
		}
		$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', '订单状态改变微信通知顾客', $send, $status['message']);
		}
	}

	if($order['order_channel'] == 'app') {
		mload()->model('h5app');
		$router = array(
			'pay' => array(
				'title' => '您的订单已付款',
				'content' => "支付方式:{$order['pay_type_cn']},支付时间:" . date('Y-m-d H:i', $order['paytime']),
			),
			'handle' => array(
				'title' => '您的订单商家已接单',
				'content' => '商家已接单,正在为您准备商品中',
			),
			'delivery_assign' => array(
				'title' => '您的订单已分配配送员',
				'content' => "配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']},配送员已接单, 正赶至商家取货, 骑士将骑上战马为您急速送达, 请保持电话畅通",
			),
			'delivery_instore' => array(
				'title' => '配送员已取餐',
				'content' => "配送员已到店取餐,正在为您配送中,配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']}",
			),
			'end' => array(
				'title' => '您的订单已完成',
				'content' => "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.",
			),
			'cancel' => array(
				'title' => '您的订单已取消',
				'content' => "取消原因:" . (empty($extra['note']) ? '未知' : $extra['note'])
			),
			'delivery_notice' => array(
				'title' => '配送员到达您的收货地址',
				'content' => "配送员已到达你下单时填写的送货地址,配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电"
			),
			'reply_remind' => array(
				'title' => '催单回复',
				'content' => $note
			),
		);
		if(empty($router[$type])) {
			return true;
		}
		mload()->model('member');
		$token = member_uid2token($order['uid']);
		$data = $router[$type];
		if(!empty($token)) {
			mload()->model('h5app');
			$url = imurl('wmall/order/index/detail', array('id' => $order['id']), true);
			$status = h5app_push($token, $data['title'], $data['content'], $url);
		}
	}
	return true;
}

//order_clerk_notice
function order_clerk_notice($id, $type, $note = '') {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = store_fetch($order['sid'], array('title', 'id', 'push_token'));
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if(empty($clerks)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if($type == 'place_order') {
		pdo_update('tiny_wmall_order', array('last_notify_clerk_time' => TIMESTAMP, 'notify_clerk_total' => ($order['notify_clerk_total'] + 1)), array('id' => $order['id']));
		$title = "您的店铺有新的外卖订单,订单号为 #{$order['serial_sn']} ,订单金额:{$order['final_fee']}元,请请尽快处理";
		$goods_temp = order_fetch_goods($id);
		$goods = array();
		foreach($goods_temp as $row) {
			$goods[] = "{$row['goods_title']} x {$row['goods_num']};";
		}
		unset($goods_temp);
		$goods = implode('', $goods);
		$remark = array(
			"门店名称: {$store['title']}",
			"商品信息: {$goods}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"总金　额: {$order['final_fee']}",
			"支付状态: {$order['pay_type_cn']}",
			"订单类型: {$order['order_type_cn']}"
		);
	} elseif($type == 'remind') {
		$title = "订单号为 #{$order['serial_sn']} 的订单有催单, 请请尽快回复";
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
		);
	} elseif($type == 'collect') {
		$title = "您订单号为: {$order['ordersn']}的外卖单已被配送员接单";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
		);
	} elseif($type == 'store_order_place') {
		$title = '您的店铺有新的店内订单,订单号: ' . $order['ordersn'];
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"桌　　号: " . $order['table']['title'] . '桌',
			"客人数量: " . $order['person_num'] . '人',
		);
	} elseif($type == 'store_order_pay') {
		$title = "订单号为{$order['ordersn']}的订单已付款";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"桌　　号: " . $order['table']['title'] . '桌',
			"客人数量: " . $order['person_num'] . '人',
		);
	} elseif($type == 'reserve_order_pay') {
		$title = "您有新的预定订单,订单号{$order['ordersn']}, 已付款, 支付方式:{$order['pay_type_cn']}";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"预定时间: " . $order['reserve_time'],
			"预定类型: " . $order['reserve_type_cn'],
			"预定桌台: " . $order['table_category']['title'],
			"预定　人: " . $order['username'],
			"手机　号: " . $order['mobile'],
		);
	}
	if(!empty($note)) {
		if(!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);
		}
	}
	$url = imurl('manage/order/takeout/detail', array('id' => $order['id'], 'sid' => $order['sid']), true);
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach($clerks as $clerk) {
		if(!empty($clerk['mobile']) && in_array($type, array('place_order', 'store_order_place')) && $clerk['extra']['accept_voice_notice'] == 1) {
			$result = sms_singlecall($clerk['mobile'], array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), 'clerk');
			if(is_error($result)) {
				slog('alidayuCall', '订单状态变动阿里大鱼语音通知商户', array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), $result['message']);
			}
		}
		if($clerk['extra']['accept_wechat_notice'] == 1) {
			$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url);
		}
		if(is_error($status)) {
			slog('wxtplNotice', '订单状态变动微信通知商户', $send, $status['message']);
		}
	}
	if(in_array($type, array('place_order', 'remind'))) {
		$audience = array(
			'tag' => array(
				$store['push_token']
			)
		);
		$data = Jpush_clerk_send('您的店铺有新的订单', $title, array('voice_text' => $title, 'url' => $url, 'notify_type' => $type), $audience);
	}
	return true;
}

function order_deliveryer_notice($id, $type, $deliveryer_id = 0, $note = '') {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($order['order_type'] != 1) {
		return error(-1, '不是外卖订单');
	}
	mload()->model('deliveryer');
	$store = store_fetch($order['sid'], array('title', 'id', 'delivery_mode'));
	if($deliveryer_id > 0) {
		$deliveryers[] = deliveryer_fetch($deliveryer_id);
	} else {
		if($store['delivery_mode'] == 2) {
			//平台配送
			$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
			$filter = array();
			if(!$config_takeout['over_collect_max_notify'] && $config_takeout['deliveryer_collect_max'] > 0) {
				$filter = array('order_takeout_num' => $config_takeout['deliveryer_collect_max']);
			}
			$deliveryers = deliveryer_fetchall(0, $filter);
		} else {
			//只有是店内配送员模式的时候, 才可以指定配送员
			if($deliveryer_id > 0) {
				$deliveryers[] = deliveryer_fetch($deliveryer_id);
			} else {
				$deliveryers = deliveryer_fetchall($order['sid']);
			}
		}
	}
	if(empty($deliveryers)) {
		//通知平台管理员没有接单中的配送员
		if($store['delivery_mode'] == 2) {
			order_manager_notice($order['id'], 'no_working_deliveryer');
		}
		return false;
	}
	//注意：new_delivery：通知的是单个配送员。delivery_wait：通知的是平台所有配送员
	$acc = WeAccount::create($order['acid']);
	if($type == 'new_delivery') {
		$title = "店铺{$store['title']}有新的外卖配送订单, 配送地址为{$order['address']}, 快去处理吧";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"总金　额: {$order['final_fee']}",
			"支付状态: {$order['pay_type_cn']}",
			"下单　人: {$order['username']}",
			"联系手机: {$order['mobile']}",
			"送货地址: {$order['address']}",
			"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'),
		);
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/detail', array('id' => $order['id']), true);
	} else if($type == 'delivery_wait') {
		pdo_update('tiny_wmall_order', array('last_notify_deliveryer_time' => TIMESTAMP, 'notify_deliveryer_total' => $order['notify_deliveryer_total'] + 1), array('id' => $order['id']));

		$title = "店铺{$store['title']}有新的配送订单, 配送地址为{$order['address']}, 快去抢单吧";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"下单　人: {$order['username']}",
			"联系手机: {$order['mobile']}",
			"送货地址: {$order['address']}",
			"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
		);
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/list', array(), true);
		Jpush_deliveryer_send('您有新的外卖待抢订单', $title, array('voice_text' => $title, 'notify_type' => 'ordernew', 'redirect_type' => 'takeout', 'redirect_extra' => 3));
	} else if($type == 'cancel') {
		$title = "收货地址为{$order['address']}, 收货人为{$order['username']}的订单已取消,请及时调整配送顺序";
		$remark = array(
			"门店名称: {$store['title']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"下单　人: {$order['username']}",
			"联系手机: {$order['mobile']}",
			"送货地址: {$order['address']}",
			"订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单')
		);
		if(!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = imurl('delivery/order/takeout/detail', array('id' => $order['id']), true);
	}
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach($deliveryers as $deliveryer) {
		if(!empty($deliveryer['deliveryer']['mobile']) && $deliveryer['extra']['accept_voice_notice'] == 1) {
			$data = sms_singlecall($deliveryer['deliveryer']['mobile'], array('name' => $deliveryer['deliveryer']['title'], 'store' => $store['title']), 'deliveryer');
			if(is_error($data)) {
				slog('alidayuCall', '订单状态变动阿里大鱼语音通知配送员',array('name' => $deliveryer['deliveryer']['title'], 'store' => $store['title']), $data['message']);
			}
		}
		if($deliveryer['extra']['accept_wechat_notice'] == 1) {
			$status = $acc->sendTplNotice($deliveryer['deliveryer']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url);
			if(is_error($status)) {
				slog('wxtplNotice', '订单状态变动微信通知配送员', $send, $status['message']);
			}
		}
		if($type == 'new_delivery') {
			if(!empty($deliveryer['deliveryer']['token'])) {
				$audience = array(
					'alias' => array($deliveryer['deliveryer']['token'])
				);
				Jpush_deliveryer_send('您有新的外卖配送订单', $title, array('voice_text' => $title, 'notify_type' => 'orderassign', 'redirect_type' => 'takeout', 'redirect_extra' => 7), $audience);
			}
		} elseif($type == 'cancel') {
			if(!empty($deliveryer['deliveryer']['token'])) {
				$audience = array(
					'alias' => array($deliveryer['deliveryer']['token'])
				);
				Jpush_deliveryer_send('订单取消通知', $title, array('voice_text' => $title, 'notify_type' => 'ordercancel','redirect_type' => 'takeout', 'redirect_extra' => 3), $audience);
			}
		}
	}
	return true;
}

function order_refund_fetch($order_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if(empty($refund)) {
		return error(-1, '退款记录不存在');
	}
	$refund_status = order_refund_status();
	$refund_channel = order_refund_channel();
	$refund['status_cn'] = $refund_status[$refund['status']]['text'];
	$refund['channel_cn'] = $refund_channel[$refund['channel']]['text'];
	return $refund;
}

function order_refund_notice($order_id, $type, $note = '') {
	global $_W;
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = store_fetch($order['sid'], array('title', 'id'));
	$refund = order_refund_fetch($order_id);
	$acc = WeAccount::create($order['acid']);
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if($type == 'apply') {
		if($order['agentid'] > 0) {
			$_W['agentid'] = 0;
			$_W['we7_wmall']['config'] = get_system_config();
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您的平台有新的【退款申请】, 单号【{$refund['out_refund_no']}】,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"退款单号: " . $refund['out_refund_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				$note
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '申请退款微信通知平台管理员', $send, $status['message']);
			}
		}

		if(!empty($clerks)) {
			//通知门店店员
			$tips = "您的【退款申请】已提交,单号【{$refund['out_refund_no']}】,平台会尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"退款单号: " . $refund['out_refund_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				"已付款项会在1-15工作日内返回到用户的账号, 如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			foreach($clerks as $clerk) {
				if($clerk['extra']['accept_wechat_notice'] == 1) {
					$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
					if(is_error($status)) {
						slog('wxtplNotice', '申请退款微信通知门店店员', $send, $status['message']);
					}
				}
			}
		}
	} elseif($type == 'success') {
		if(!empty($clerks)) {
			//通知门店店员
			$tips = "您店铺单号为【{$refund['out_refund_no']}】的退款已退款成功";
			$remark = array(
				"申请门店: " . $store['title'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['username'],
				"联系方式: " . $order['mobile'],
				"退款渠道: " . $refund['channel_cn'],
				"退款账户: " . $refund['account'],
				"如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);

			foreach($clerks as $clerk) {
				if($clerk['extra']['accept_wechat_notice'] == 1) {
					$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
					if(is_error($status)) {
						slog('wxtplNotice', '申请退款成功微信通知门店店员', $send, $status['message']);
					}
				}
			}
		}

		if(!empty($order['openid'])) {
			//通知顾客
			$tips = "您订单号为【{$order['ordersn']}】的退款已退款成功";
			$remark = array(
				"下单门店: " . $store['title'],
				"支付方式: " . $order['pay_type_cn'],
				"退款渠道: " . $refund['channel_cn'],
				"退款账户: " . $refund['account'],
				"如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['refund_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '申请退款成功微信通知顾客', $send, $status['message']);
			}
		}
	}
	return true;
}

//pay_types
function order_pay_types() {
	$pay_types = array(
		'' => '未支付',
		'alipay' => array(
			'css' => 'label label-info',
			'text' => '支付宝',
		),
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信支付',
		),
		'credit' => array(
			'css' => 'label label-warning',
			'text' => '余额支付',
		),
		'delivery' => array(
			'css' => 'label label-primary',
			'text' => '货到付款',
		),
		'cash' => array(
			'css' => 'label label-primary',
			'text' => '现金支付',
		),
		'qianfan' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'majia' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'peerpay' => array(
			'css' => 'label label-primary',
			'text' => '找人代付',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么支付',
		),
		'maituan' => array(
			'css' => 'label label-primary',
			'text' => '美团支付',
		),
	);
	return $pay_types;
}

function order_check_member_cart($sid) {
	global $_W;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if(empty($cart)) {
		return error(-1, '购物车为空');
	}
	$cart['data'] = iunserializer($cart['data']);
	if(empty($cart['data'])) {
		return error(-1, '购物车为空');
	}
	$errno = 0;
	$errmessage = '';
	$goods_ids = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :uniacid AND sid = :sid AND id IN ($goods_ids)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach($cart['data'] as $goods_id => $cart_item) {
		if(!empty($errno)) {
			break;
		}
		$goods = $goods_info[$goods_id];
		if(!$goods_info[$goods_id]['is_options']) {
			$option_item = $cart_item[0];
			if($option_item['discount_num'] > 0) {
				$bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $option_item['bargain_id'], 'sid' => $sid, 'status' => '1'));
				if(empty($bargain)) {
					$errno = -3; //特价活动已结束
					$errmessage = "特价活动{$bargain['title']}已结束！";
					break;
				}
				$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $option_item['bargain_id'], 'goods_id' => $goods_id));
				if($bargain_goods['discount_available_total'] != -1 && $option_item['discount_num'] > $bargain_goods['discount_available_total']) {
					$errno = -4; //特价商品库存不足
					$errmessage = "参与特价活动{$bargain['title']}的{$goods['title']}库存不足！";
					break;
				}
			} else {
				if($goods['total'] != -1 && $option_item['num'] > $goods['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		} else {
			foreach($cart_item as $option_id => $option_item) {
				$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
				if(empty($option)) {
					continue;
				}
				if($option['total'] != -1 && $cart_item['num'] > $option['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		}
	}
	if(!empty($errno)) {
		return error($errno, $errmessage);
	}
	return $cart;
}

function order_insert_cart($sid) {
	global $_W, $_GPC;
	if(!empty($_GPC['goods'])) {
		$num = 0;
		$price = 0;
		$box_price = 0;
		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		foreach($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			$goods_box_price = $goods_info[$k]['box_price'];
			if(!$goods_info[$k]['is_options']) {
				$v = intval($v['options'][0]);
				if($v > 0) {
					$goods[$k][0] = array(
						'title' => $goods_info[$k]['title'],
						'num' => $v,
						'price' => $goods_info[$k]['price'],
					);
					$num += $v;
					$price += $goods_info[$k]['price'] * $v;
					$box_price += $goods_box_price * $v;
				}
			} else {
				foreach($v['options'] as $key => $val) {
					$key = intval($key);
					$val = intval($val);
					if($key > 0 && $val > 0) {
						$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $key));
						if(empty($option)) {
							continue;
						}
						$goods[$k][$key] = array(
							'title' => $goods_info[$k]['title'] . "({$option['name']})",
							'num' => $val,
							'price' => $option['price'],
						);
						$num += $val;
						$price += $option['price'] * $val;
						$box_price += $goods_box_price * $val;
					}
				}
			}
		}

		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'uid' => $_W['member']['uid'],
			'groupid' => $_W['member']['groupid'],
			'num' => $num,
			'price' => $price,
			'box_price' => $box_price,
			'data' => iserializer($goods),
			'addtime' => TIMESTAMP,
		);
		if(empty($isexist)) {
			pdo_insert('tiny_wmall_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
		}
		$data['data'] = $goods;
		return $data;
	} else {
		return error(-1, '商品信息错误');
	}
	return true;
}

//order_fetch_member_cart
function order_fetch_member_cart($sid) {
	global $_W, $_GPC;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if(empty($cart)) {
		return false;
	}
	if((TIMESTAMP - $cart['addtime']) > 7*86400) {
		pdo_delete('tiny_wmall_order_cart', array('id' => $cart['id']));
	}
	$cart['data'] = iunserializer($cart['data']);
	$cart['original_data'] = (array)iunserializer($cart['original_data']);
	return $cart;
}

//order_del_member_cart
function order_del_member_cart($sid) {
	global $_W;
	pdo_delete('tiny_wmall_order_cart', array('sid' => $sid, 'uid' => $_W['member']['uid']));
	return true;
}

//order_order_update_goods_info
function order_update_goods_info($order_id, $sid, $cart = array()) {
	global $_W;
	if(empty($cart)) {
		$cart = order_fetch_member_cart($sid);
	}
	if(empty($cart['data'])) {
		return false;
	}
	$categorys = pdo_getall('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid']), array(), 'id');
	$ids_str = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT id,cid,title,number,price,total,total_warning,total_update_type,print_label FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach($cart['data'] as $goods_id => $options) {
		$goods = $goods_info[$goods_id];
		if(empty($goods)) {
			continue;
		}
		foreach($options as $option_id => $item) {
			if($goods['total_update_type'] == 1) {
				if(!$option_id) {
					if($goods['total'] != -1 && $goods['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $goods_id));
						$total_now = $goods['total'] - $item['num'];
						if($goods['total_warning'] > 0 && $total_now <= $goods['total_warning']) {
							//库存报警
							goods_total_warning_notice($goods, 0, array('total_now' => $total_now));
						}
					}
					if($item['bargain_id'] > 0 && $item['discount_num'] > 0) {
						$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $item['bargain_id'], 'goods_id' => $goods_id));
						if($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$item['discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $item['bargain_id'], ':goods_id' => $goods_id));
						}
					}
				} else {
					$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
					if(!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_goods_options') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $option_id));
						$total_now = $option['total'] - $item['num'];
						if($option['total_warning'] > 0 && $total_now <= $option['total_warning']) {
							//库存报警
							goods_total_warning_notice($goods, $option, array('total_now' => $total_now));
						}
					}
				}
			}
			$stat = array();
			if($item['num'] > 0) {
				$stat['oid'] = $order_id;
				$stat['uniacid'] = $_W['uniacid'];
				$stat['sid'] = $sid;
				$stat['uid'] = $_W['member']['uid'];
				$stat['goods_id'] = $goods_id;
				$stat['goods_cid'] = $goods['cid'];
				$stat['option_id'] = $option_id;
				$stat['goods_category_title'] = $categorys[$goods['cid']]['title'];
				$stat['goods_title'] = $item['title'];
				$stat['goods_number'] = $goods['number'];
				$stat['goods_num'] = $item['num'];
				$stat['goods_discount_num'] = $item['discount_num'];
				$stat['goods_unit_price'] = $option_id > 0 ? $option['price'] : $goods['price'];
				$stat['goods_price'] = $item['total_discount_price'];
				$stat['goods_original_price'] = $item['total_price'];
				$stat['bargain_id'] = $item['bargain_id'];
				$stat['total_update_status'] = $goods['total_update_type'] == 2 ? 0 : 1;
				$stat['print_label'] = $goods['print_label'];
				$stat['addtime'] = TIMESTAMP;
				$stat['stat_year'] = date('Y');
				$stat['stat_month'] = date('Ym');
				$stat['stat_day'] = date('Ymd');
				$stat['stat_week'] = date('YW');
				pdo_insert('tiny_wmall_order_stat', $stat);
			}
		}
	}
	return true;
}

function order_amount_stat($sid) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$month_starttime = strtotime(date('Y-m'));
	$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	return $stat;
}

function order_count_activity($sid, $cart, $recordid = 0, $redPacket_id = 0, $delivery_price = 0, $delivery_free_price = 0, $order_type = '') {
	global $_W, $_GPC;
	$activityed = array('list' => '', 'total' => 0, 'activity' => 0, 'token' => 0, 'store_discount_fee' => 0, 'agent_discount_fee' => 0, 'plateform_discount_fee' => 0);
	$store = store_fetch($sid, array('delivery_mode', 'delivery_extra', 'delivery_free_price', 'cid'));
	if($_GPC['ac'] == 'order' && $_GPC['op'] == 'create' && ($order_type == 1 || empty($order_type))) {
		if(!empty($delivery_free_price)) {
			$store['delivery_free_price'] = $delivery_free_price;
		}
		if(!empty($delivery_price) && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) {
			if($store['delivery_mode'] == 1) {
				//店内配送模式
				$store_discount_fee = $delivery_price;
				$agent_discount_fee = 0;
				$plateform_discount_fee = 0;
			} else {
				//平台配送模式
				$delivery_free_bear = trim($store['delivery_extra']['delivery_free_bear']);
				if($_W['is_agent']) {
					$agent_discount_fee = $delivery_price;
					$plateform_discount_fee = 0;
					$store_discount_fee = 0;
					if($delivery_free_bear == 'store') {
						$agent_discount_fee = 0;
						$store_discount_fee = $delivery_price;
					}
				} else {
					$agent_discount_fee = 0;
					$plateform_discount_fee = $delivery_price;
					$store_discount_fee = 0;
					if($delivery_free_bear == 'store') {
						$plateform_discount_fee = 0;
						$store_discount_fee = $delivery_price;
					}
				}
			}
			$activityed['list']['delivery'] = array(
				'text' => "-￥{$delivery_price}",
				'value' => $delivery_price,
				'type' => 'delivery',
				'name' => "满{$store['delivery_free_price']}元免配送费",
				'icon' => 'mian_b.png',
				'plateform_discount_fee' => $plateform_discount_fee,
				'store_discount_fee' => $store_discount_fee,
				'agent_discount_fee' => $agent_discount_fee
			);
			$activityed['total'] += $delivery_price;
			$activityed['activity'] += $delivery_price;
			$activityed['store_discount_fee'] += $store_discount_fee;
			$activityed['plateform_discount_fee'] += $plateform_discount_fee;
			$activityed['agent_discount_fee'] += $agent_discount_fee;
		}
		//免配送费
		if(empty($activityed['list']['delivery']) && $store['delivery_mode'] == 2 && !empty($delivery_price)) {
			if($_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] >= TIMESTAMP) {
				$nums = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and vip_free_delivery_fee = 1 and status != 6 and addtime >= :addtime', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':addtime' => strtotime(date('Y-m-d'))));
				if($nums < $_W['member']['setmeal_day_free_limit']) {
					$activityed['list']['vip_delivery'] = array('text' => "-￥{$delivery_price}", 'value' => $delivery_price, 'type' => 'delivery', 'name' => '会员免配送费', 'icon' => 'mian_b.png', 'plateform_discount_fee' => $delivery_price, 'agent_discount_fee' => 0, 'store_discount_fee' => 0);
					$activityed['total'] += $delivery_price;
					$activityed['activity'] += $delivery_price;
					$activityed['store_discount_fee'] += 0;
					$activityed['plateform_discount_fee'] += $delivery_price;
					$activityed['agent_discount_fee'] += 0;
				}
			}
		}
	}
	if($cart['bargain_use_limit'] == 2) {
		return $activityed;
	}

	//平台首单红包不与其他优惠同时享受。（可与免配送费同享）
	if($redPacket_id > 0) {
		mload()->model('redPacket');
		$record = redpacket_available_check($redPacket_id, $cart['price'], explode('|', $store['cid']));
		if(!is_error($record) && ($record['type'] != 'mallNewMember' || ($record['type'] == 'mallNewMember' && $_W['member']['is_mall_newmember']))) {
			$activityed['list']['redPacket'] = array('text' => "-￥{$record['discount']}", 'value' => $record['discount'], 'type' => 'redPacket', 'name' => '平台红包优惠', 'icon' => 'redPacket_b.png', 'redPacket_id' => $redPacket_id, 'plateform_discount_fee' => $record['discount'], 'agent_discount_fee' => 0, 'store_discount_fee' => 0);
			$activityed['redPacket'] = $record;
			$activityed['total'] += $record['discount'];
			$activityed['activity'] += $record['discount'];
			$activityed['store_discount_fee'] += 0;
			$activityed['agent_discount_fee'] += 0;
			$activityed['plateform_discount_fee'] += $record['discount'];
			if($record['type'] == 'mallNewMember') {
				return $activityed;
			}
		}
	}

	//新用户支付优惠和满减优惠不能同时享受
	mload()->model('activity');
	$activity = activity_getall($sid, 1);
	if(!empty($activity) && ($order_type == 2 || empty($order_type))) {
		$selfDelivery = $activity['selfDelivery'];
		if(!empty($selfDelivery['status'])) {
			$discount_temp = array_compare($cart['price'], $selfDelivery['data']);
			if(!empty($discount_temp)) {
				$discount_fee = round((10 - $discount_temp['back']) / 10 * $cart['price'], 2);
				$discount = array(
					'back' => $discount_temp['back'],
					'value' => $discount_fee,
					'plateform_discount_fee' => round($discount_fee * $discount_temp['plateform_charge'] / $discount_temp['back'], 2),
					'agent_discount_fee' => round($discount_fee * $discount_temp['agent_charge'] / $discount_temp['back'], 2),
					'store_discount_fee' => round($discount_fee * $discount_temp['store_charge'] / $discount_temp['back'], 2),
				);
				$activityed['list']['selfDelivery'] = array('text' => "-￥{$discount['value']}", 'value' => $discount['value'], 'type' => 'discount', 'name' => '自提优惠', 'icon' => 'selfDelivery_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
				$activityed['total'] += $discount['value'];
				$activityed['activity'] += $discount['value'];
				$activityed['store_discount_fee'] += $discount['store_discount_fee'];
				$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
				if($order_type == 2) {
					return $activityed;
				}
			}
		}
	}

	if($recordid > 0) {
		$record = pdo_get('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1, 'id' => $recordid));
		if(!empty($record) && $record['starttime'] <= TIMESTAMP && $record['endtime'] >= TIMESTAMP && $cart['price'] >= $record['condition']) {
			$activityed['list']['token'] = array('text' => "-￥{$record['discount']}", 'value' => $record['discount'], 'type' => 'couponCollect', 'name' => '代金券优惠', 'icon' => 'couponCollect_b.png', 'recordid' => $recordid, 'plateform_discount_fee' => 0, 'agent_discount_fee' => 0, 'store_discount_fee' => $record['discount']);
			$activityed['total'] += $record['discount'];
			$activityed['activity'] += $record['discount'];
			$activityed['store_discount_fee'] += $record['discount'];
			$activityed['agent_discount_fee'] += 0;
			$activityed['plateform_discount_fee'] += 0;
		}
	}

	if(!empty($activity)) {
		$mallNewMember = $activity['mallNewMember'];
		if(!empty($mallNewMember['status'])) {
			if(!empty($_W['member']['is_mall_newmember'])) {
				$discount = array(
					'back' => $mallNewMember['data']['back'],
					'plateform_discount_fee' => $mallNewMember['data']['plateform_charge'],
					'store_discount_fee' => floatval($mallNewMember['data']['store_charge']),
					'agent_discount_fee' => $mallNewMember['data']['agent_charge'],
				);
				$activityed['list']['mallNewMember'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'mallNewMember', 'name' => '首单优惠', 'icon' => 'mallNewMember_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee']);
				$activityed['total'] += $discount['back'];
				$activityed['activity'] += $discount['back'];
				$activityed['store_discount_fee'] += $discount['store_discount_fee'];
				$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
			}
		}

		if(!empty($activity['newMember'])) {
			$newMember = $activity['newMember'];
			if($newMember['status'] == 1 && !empty($_W['member']['is_store_newmember'])) {
				$discount = array(
					'back' => $newMember['data']['back'],
					'plateform_discount_fee' => $newMember['data']['plateform_charge'],
					'store_discount_fee' => $newMember['data']['store_charge'],
					'agent_discount_fee' => $newMember['data']['agent_charge'],
				);
				$activityed['list']['newMember'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'newMember', 'name' => '新用户优惠', 'icon' => 'newMember_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
				$activityed['total'] += $discount['back'];
				$activityed['activity'] += $discount['back'];
				$activityed['store_discount_fee'] += $discount['store_discount_fee'];
				$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
				$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
			}
		}

		if(empty($activityed['list']['mallNewMember']) && !empty($activity['discount'])) {
			$activity_discount = $activity['discount'];
			if($activity_discount['status'] == 1) {
				$discount_temp = array_compare($cart['price'], $activity_discount['data']);
				if(!empty($discount_temp)) {
					$discount = array(
						'back' => $discount_temp['back'],
						'plateform_discount_fee' => $discount_temp['plateform_charge'],
						'store_discount_fee' => $discount_temp['store_charge'],
						'agent_discount_fee' => $discount_temp['agent_charge'],
					);
					$activityed['list']['discount'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'discount', 'name' => '满减优惠', 'icon' => 'discount_b.png', 'store_discount_fee' => $discount['store_discount_fee'], 'agent_discount_fee' => $discount['agent_discount_fee'], 'plateform_discount_fee' => $discount['plateform_discount_fee']);
					$activityed['total'] += $discount['back'];
					$activityed['activity'] += $discount['back'];
					$activityed['store_discount_fee'] += $discount['store_discount_fee'];
					$activityed['plateform_discount_fee'] += $discount['plateform_discount_fee'];
					$activityed['agent_discount_fee'] += $discount['agent_discount_fee'];
				}
			}
		}

		$cashGrant = $activity['cashGrant'];
		if(!empty($cashGrant['status'])) {
			$discount = array_compare($cart['price'], $cashGrant['data']);
			if(!empty($discount)) {
				$activityed['list']['cashGrant'] = array('text' => "返{$discount['back']}元", 'value' => $discount['back'], 'type' => 'cashGrant', 'name' => '返现优惠', 'icon' => 'cashGrant_b.png', 'store_discount_fee' => $discount['store_charge'], 'agent_discount_fee' => $discount['agent_charge'], 'plateform_discount_fee' => $discount['plateform_charge']);
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
				$activityed['store_discount_fee'] += $discount['store_charge'];
				$activityed['plateform_discount_fee'] += $discount['plateform_charge'];
				$activityed['agent_discount_fee'] += $discount['agent_charge'];
			}
		}

		$grant = $activity['grant'];
		if(!empty($grant['status'])) {
			$discount = array_compare($cart['price'], $grant['data']);
			if(!empty($discount)) {
				$activityed['list']['grant'] = array('text' => "{$discount['back']}", 'value' => 0, 'type' => 'grant', 'name' => '满赠优惠', 'icon' => 'grant_b.png');
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
			}
		}

		$coupon_grant = $activity['couponGrant'];
		if(!empty($coupon_grant['status'])) {
			mload()->model('coupon');
			$coupon = coupon_grant_available($sid, $cart['price']);
			if(!empty($coupon)) {
				$activityed['list']['couponGrant'] = array('text' => "返{$coupon['discount']}元代金券", 'value' => 0, 'type' => 'couponGrant', 'name' => '满返优惠', 'icon' => 'couponGrant_b.png');
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
			}
		}
	}
	return $activityed;
}

function order_coupon_available($sid, $price) {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid and uid = :uid and status = 1 and `condition` <= :price';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':price' => floatval($price), ':uid' => $_W['member']['uid']);
	$coupons = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_coupon_record') . $condition, $params);
	return $coupons;
}

function order_redPacket_available($price, $category = array()) {
	global $_W;
	$condition = ' where uniacid = :uniacid and uid = :uid and status = 1 and `condition` <= :price';
	$params = array(':uniacid' => $_W['uniacid'], ':price' => floatval($price), ':uid' => $_W['member']['uid']);
	if(!$_W['member']['is_mall_newmember'] && 0) {
		$condition .= ' and type != :type';
		$params[':type'] = 'mallNewMember';
	}
	$redPackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . $condition, $params);
	if(!empty($redPackets)) {
		mload()->model('redPacket');
		foreach($redPackets as $key => &$redPacket) {
			$check = redpacket_available_check($redPacket['id'], $price, $category);
			if(is_error($check)) {
				unset($redPackets[$key]);
			}
			$redPacket['time_cn'] = totime($redPacket['times_limit']);
			if(!empty($redPacket['time_cn'])) {
				$redPacket['time_cn'] = "仅限{$redPacket['time_cn']}时段使用";
			}
			$redPacket['category_cn'] = tocategory($redPacket['category_limit']);
			if(!empty($redPacket['category_cn'])) {
				$redPacket['category_cn'] = "仅限{$redPacket['category_cn']}分类使用";
			}
		}
	}
	return $redPackets;
}

function order_insert_refund_log($id, $type, $note = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	$notes = array(
		'apply' => array(
			'status' => 1,
			'title' => '提交退款申请',
			'note' => "",
		),
		'handle' => array(
			'status' => 2,
			'title' => "{$_W['we7_wmall']['config']['title']}接受退款申请",
			'note' => ''
		),
		'success' => array(
			'status' => 3,
			'title' => "退款成功",
			'note' => ''
		),
		'fail' => array(
			'status' => 4,
			'title' => "退款失败",
			'note' => ''
		),
	);
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'order_type' => 'order',
		'status' => $notes[$type]['status'],
		'type' => $type,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_order_refund_log', $data);
	return true;
}

function order_begin_payrefund($order_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if(empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if($refund['status'] == 2) {
		return error(-1, '退款进行中, 不能发起退款');
	}
	if($refund['status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if($refund['pay_type'] == 'credit') {
		if($refund['uid'] > 0) {
			$log = array(
				$refund['uid'],
				"外送模块订单退款, 订单号:{$refund['order_sn']}, 退款金额:{$refund['fee']}元",
				'we7_wmall'
			);
			mload()->model('member');
			member_credit_update($refund['uid'], 'credit2', $refund['fee'], $log);
			$refund_update = array(
				'status' => 3,
				'account' => '支付用户的平台余额',
				'channel' => 'ORIGINAL',
				'handle_time' => TIMESTAMP,
				'success_time' => TIMESTAMP,
			);
			pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			pdo_update('tiny_wmall_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			order_insert_refund_log($refund['order_id'], 'handle');
			order_insert_refund_log($refund['order_id'], 'success');
		}
		return error(0, '退款成功,支付金额已退款至顾客的平台余额');
	} elseif($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay($refund['order_channel']);
		$params = array(
			'total_fee' => $refund['fee'] * 100,
			'refund_fee' => $refund['fee'] * 100,
			'out_trade_no' => $refund['out_trade_no'],
			'out_refund_no' => $refund['out_refund_no'],
		);
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		pdo_update('tiny_wmall_order', array('refund_status' => 2), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		pdo_update('tiny_wmall_order_refund', array('status' => 2, 'handle_time' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		$query = order_query_payrefund($refund['order_id']);
		return $query;
	} elseif($refund['pay_type'] == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay($refund['order_channel']);
		$params = array(
			'refund_fee' => $refund['fee'],
			'out_trade_no' => $refund['out_trade_no'],
		);
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		$refund_update = array(
			'status' => 3,
			'account' => $response['buyer_logon_id'],
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		pdo_update('tiny_wmall_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		order_insert_refund_log($refund['order_id'], 'success');
		return error(0, "退款成功,支付金额已退款至顾客的支付宝账户:{$response['buyer_logon_id']}");
	} elseif($refund['pay_type'] == 'qianfan') {
		$member = pdo_get('tiny_wmall_members', array('uid' => $refund['uid']));
		if(empty($member['uid_qianfan'])) {
			return error(-1, "获取用户uid失败");
		}
		mload()->model('plugin');
		pload()->model('qianfanApp');
		$status = qianfan_user_credit_add($member['uid_qianfan'], $refund['fee']);
		if(is_error($status)) {
			return error(-1, "退款失败:{$status['message']}");
		}
		$refund_update = array(
			'status' => 3,
			'account' => '支付用户的APP账户余额',
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		pdo_update('tiny_wmall_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		order_insert_refund_log($refund['order_id'], 'success');
		return error(0, '退款成功,支付金额已退款至顾客的APP账户余额');
	}
}

function order_query_payrefund($order_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if(empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if($refund['status'] != 2) {
		return error(-1, '退款已处理');
	}
	if($refund['pay_type'] == 'wechat') {
		//只有微信需要查询,余额和支付宝不需要
		mload()->classs('wxpay');
		$pay = new WxPay();
		$response = $pay->payRefund_query(array('out_refund_no' => $refund['out_refund_no']));
		if(is_error($response)) {
			return $response;
		}
		$wechat_status = $pay->payRefund_status();
		$update = array(
			'status' => $wechat_status[$response['refund_status_0']]['value'],
		);
		if($response['refund_status_0'] == 'SUCCESS') {
			$update['channel'] = $response['refund_channel_0'];
			$update['account'] = $response['refund_recv_accout_0'];
			$update['success_time'] = TIMESTAMP;
			pdo_update('tiny_wmall_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			pdo_update('tiny_wmall_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			order_insert_refund_log($refund['order_id'], 'success');
			return error(0, "退款成功,支付金额已退款至顾客的微信账号:{$response['refund_recv_accout_0']}");
		} else {
			pdo_update('tiny_wmall_order', array('refund_status' => $update['status']), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			pdo_update('tiny_wmall_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			return error(0, '退款进行中, 请耐心等待');
		}
	}
	return true;
}

//pay_types
function order_refund_status() {
	$refund_status = array(
		'1' => array(
			'css' => 'label label-info',
			'text' => '退款申请中',
		),
		'2' => array(
			'css' => 'label label-warning',
			'text' => '退款处理中',
		),
		'3' => array(
			'css' => 'label label-success',
			'text' => '退款成功',
		),
		'4' => array(
			'css' => 'label label-danger',
			'text' => '退款失败',
		),
		'5' => array(
			'css' => 'label label-default',
			'text' => '退款状态未确定',
		),
	);
	return $refund_status;
}

function to_paytype($type, $key = 'all') {
	$data = array(
		'' => '未支付',
		'alipay' => array(
			'css' => 'label label-info',
			'text' => '支付宝',
		),
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信支付',
		),
		'credit' => array(
			'css' => 'label label-warning',
			'text' => '余额支付',
		),
		'delivery' => array(
			'css' => 'label label-primary',
			'text' => '货到付款',
		),
		'cash' => array(
			'css' => 'label label-primary',
			'text' => '现金支付',
		),
		'qianfan' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'majia' => array(
			'css' => 'label label-primary',
			'text' => 'APP支付',
		),
		'peerpay' => array(
			'css' => 'label label-primary',
			'text' => '找人代付',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么支付',
		),
		'maituan' => array(
			'css' => 'label label-primary',
			'text' => '美团支付',
		),
	);
	if($key == 'all') {
		return $data[$type];
	} elseif($key == 'text') {
		return $data[$type]['text'];
	} elseif($key == 'css') {
		return $data[$type]['css'];
	}
}

function order_refund_channel() {
	$refund_channel = array(
		'ORIGINAL' => array(
			'css' => 'label label-warning',
			'text' => '原路返回',
		),
		'BALANCE' => array(
			'css' => 'label label-danger',
			'text' => '退回余额',
		),
	);
	return $refund_channel;
}

function order_comment_status() {
	$status = array(
		'0' => array(
			'css' => 'color-primary',
			'text' => '待审核',
		),
		'1' => array(
			'css' => 'color-success',
			'text' => '审核通过',
		),
		'2' => array(
			'css' => 'color-danger',
			'text' => '审核未通过',
		),
	);
	return $status;
}

function order_status_update($id, $type, $extra = array()) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$_W['agentid'] = $order['agentid'];
	if($_W['agentid'] > 0) {
		$_W['we7_wmall']['config'] = get_system_config();
		$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_notice_deliveryer', 'openplateform_extra'));

	if(is_open_order($order)) {
		$store['openplateform_extra'] = iunserializer($store['openplateform_extra']);
		mload()->model('plugin');
		if($order['order_plateform'] == 'eleme') {
			$_W['_plugin'] = array(
				'name' => 'eleme'
			);
			$config_open = $store['openplateform_extra']['eleme'];
			$openOrderId = $order['elemeOrderId'];
		} elseif($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			$config_open = $store['openplateform_extra']['meituan'];
			$openOrderId = $order['meituanOrderId'];
		}
		pload()->classs('order');
		$openOrder = new order($order['sid']);
		$store['auto_handel_order'] = $config_open['order']['auto_handel_order'];
		$store['auto_notice_deliveryer'] = $config_open['order']['auto_notice_deliveryer'];
		$store['auto_print'] = $config_open['order']['auto_print'];
		$store['delivery_mode'] = $config_open['delivery']['delivery_mode'];
	}

	if($type == 'handle') {
		if($order['status'] != 1) {
			return error(-1, '订单状态不是待处理状态,不能接单');
		}
		if(!$order['is_pay'] && $order['order_type'] <= 2) {
			return error(-1, '该订单属于外卖单,并且未支付,不能接单');
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台接单后,消息推送时不执行此方法(饿了么推送已接单消息的时候， role = eleme, role != eleme代表非饿了么消息推送接单，而是商户主动接单)
				$result = $openOrder->confirmOrderLite($openOrderId);
				if(is_error($result)) {
					return $result;
				}
			}
		}
		$update = array(
			'status' => 2,
			'handletime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 2), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'handle');
		order_status_notice($order['id'], 'handle');
		if($store['auto_notice_deliveryer'] == 1) {
			order_status_update($order['id'], 'notify_deliveryer_collect');
		}
		return error(0, '接单成功');
	} elseif($type == 'notify_deliveryer_collect') {
		if($order['order_type'] > 1) {
			return error(-1, '订单类型不是外卖单,不需要通知配送员抢单');
		}
		if($order['status'] > 3) {
			return error(-1, '订单状态有误');
		}
		$update = array(
			'status' => 3,
			'delivery_status' => 3,
			'delivery_type' => $store['delivery_mode'],
			'clerk_notify_collect_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 3), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_wait');

		//如果由第三方平台配送,delivery_type为0,所以不需要通知系统的配送员进行配送
		if(!$update['delivery_type']) {
			return error(0, '通知配送员抢单成功,请耐心等待配送员接单');
		}
		if($config_takeout['dispatch_mode'] <= 1 || empty($store['delivery_mode']) || !empty($extra['force'])) {
			//抢单模式
			if(empty($extra['notify_channel']) || ($extra['notify_channel'] == 'first' && empty($config_takeout['notify_rule_deliveryer']['notify_delay']))) {
				order_deliveryer_notice($order['id'], 'delivery_wait');
			}
		} elseif($config_takeout['dispatch_mode'] == 2) {
			//管理员派单(只需要通知平台管理员调度即可)
			order_manager_notice($order['id'], 'new_delivery');
		} else {
			//系统自动分配
			$order = order_dispatch_analyse($id);
			if(is_error($order)) {
				order_manager_notice($order['id'], 'dispatch_error', "失败原因：{$order['message']}");
			}
			$deliveryer = array_shift($order['deliveryers']);
			$status = order_assign_deliveryer($id, $deliveryer['deliveryer']['id']);
		}
		return error(0, '通知配送员抢单成功,请耐心等待配送员接单');
	} elseif($type == 'cancel') {
		//订单状态, 1:已提交,待确认 2:商家已接单 3: 待配送 4:配送中 5:已完成 6:已取消
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能取消订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能取消订单');
		}
		if($order['delivery_type'] == 2 && $order['delivery_status'] >= 4 && empty($extra['force_cancel'])) {
			return error(-1, '该订单由平台配送，配送员已取货，正在配送中， 如需取消订单，请联系平台管理员');
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台取消订单后,消息推送时不执行此方法
				if($order['order_plateform'] == 'meituan') {
					$extra['remark'] = $extra['note'];
				}
				$result = $openOrder->cancelOrderLite($openOrderId, $extra['reason'], $extra['remark']);
				if(is_error($result)) {
					return $result;
				}
			}
		}
		$is_refund = 0;
		pdo_update('tiny_wmall_order_stat', array('status' => 6), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		//第三方平台的订单取消后,由第三方进行退款
		if(is_open_order($order) || !$order['is_pay'] || $order['final_fee'] <= 0 || ($order['is_pay'] == 1 && $order['pay_type'] == 'delivery' || $order['pay_type'] == 'cash')) {
			pdo_update('tiny_wmall_order', array('status' => 6, 'delivery_status' => 6, 'spreadbalance' => 1, 'is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);
			order_status_notice($order['id'], 'cancel', $extra['note']);
			if($order['deliveryer_id'] > 0) {
				order_deliveryer_notice($order['id'], 'cancel', $order['deliveryer_id']);
			}
		} else {
			if($order['refund_status'] > 0) {
				return error(-1, '退款申请处理中, 请勿重复发起');
			}
			$update = array(
				'status' => 6,
				'delivery_status' => 6,
				'refund_status' => 1, //发起退款申请
				'refund_fee' => $order['final_fee'],
				'spreadbalance' => 1,
				'is_remind' => 0,
			);
			pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);
			$refund = array(
				'uniacid' => $order['uniacid'],
				'acid' => $order['acid'],
				'sid' => $order['sid'],
				'uid' => $order['uid'],
				'order_id' => $order['id'],
				'order_sn' => $order['ordersn'],
				'order_channel' => $order['order_channel'],
				'pay_type' => $order['pay_type'],
				'fee' => $order['final_fee'],
				'status' => 1,
				'out_trade_no' => $order['out_trade_no'],
				'out_refund_no' => date('YmdHis') . random(10, true),
				'apply_time' => TIMESTAMP,
				'reason' => $extra['note'] ? $extra['note'] : '订单取消，发起退款'
			);
			pdo_insert('tiny_wmall_order_refund', $refund);
			$is_refund = 1;
			order_insert_refund_log($order['id'], 'apply');
			$note = array(
				"取消原因: {$extra['note']}",
				"退款金额: {$order['final_fee']}元",
				"已付款项会在1-15工作日内返回您的账号",
			);
			order_status_notice($order['id'], 'cancel', $note);
			order_refund_notice($order['id'], 'apply');
			if($order['deliveryer_id'] > 0) {
				order_deliveryer_notice($order['id'], 'cancel', $order['deliveryer_id']);
			}
		}
		//对顾客的订单数据进行更新处理
		$member_mall = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));
		if(!empty($member_mall)) {
			$member_update = array(
				'cancel_num' => $member_mall['cancel_num'] + 1,
				'cancel_price' => round($member_mall['cancel_price'] + $order['final_fee'], 2),
				'cancel_last_time' => TIMESTAMP,
			);
			if(empty($member_mall['cancel_first_time'])) {
				$member_update['cancel_first_time'] = TIMESTAMP;
			}
			pdo_update('tiny_wmall_members', $member_update, array('id' => $member_mall['id']));
			$member_store = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $order['sid'], 'uid' => $order['uid']));
			if(empty($member_store)) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $order['sid'],
					'uid' => $order['uid'],
					'openid' => $order['openid'],
					'cancel_first_time' => TIMESTAMP,
					'cancel_last_time' => TIMESTAMP,
					'cancel_num' => 1,
					'cancel_price' => $order['final_fee'],
				);
				pdo_insert('tiny_wmall_store_members', $insert);
			} else {
				$member_update = array(
					'cancel_num' => $member_store['cancel_num'] + 1,
					'cancel_price' => round($member_store['cancel_price'] + $order['final_fee'], 2),
					'cancel_last_time' => TIMESTAMP,
				);
				pdo_update('tiny_wmall_store_members', $member_update, array('id' => $member_store['id']));
			}
		}
		return error(0, array('is_refund' => $is_refund));
	} elseif($type == 'end') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		if(is_open_order($order)) {
			if(!in_array($extra['role'], array('eleme', 'meituan'))) {
				//饿了么开放平台完成订单后,消息推送时不执行此方法
				$result = $openOrder->receivedOrderLite($openOrderId);
				if(is_error($result)) {
					return $result;
				}
			}
		}

		$is_timeout = 0;
		if(($config_takeout['timeout_limit'] > 0) && (TIMESTAMP - $order['paytime'] > $config_takeout['timeout_limit'] * 60)) {
			$is_timeout = 1;
		}
		$update = array(
			'is_timeout' => $is_timeout,
			'status' => 5,
			'delivery_status' => 5, //已送达
			'endtime' => TIMESTAMP,
			'delivery_success_time' => TIMESTAMP,
			'delivery_success_location_x' => $extra['delivery_success_location_x'],
			'delivery_success_location_y' => $extra['delivery_success_location_y'],
			'is_remind' => 0
		);
		if(!empty($extra['deliveryer_id'])) {
			$update['deliveryer_id'] = $extra['deliveryer_id'];
		}
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 5), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));

		//订单统计， 商家服务费处理等操作, 客户积分赠送
		if($order['delivery_type'] == 2 && $order['plateform_deliveryer_fee'] > 0) {
			mload()->model('deliveryer');
			deliveryer_update_credit2($order['deliveryer_id'], $order['plateform_deliveryer_fee'], 1, $order['id']);
			if($order['pay_type'] == 'delivery') {
				$note = "{$order['id']}属于货到支付单,您线下收取客户{$order['final_fee']}元,平台从您的账户扣除该费用";
				deliveryer_update_credit2($order['deliveryer_id'], -$order['final_fee'], 3, $order['id'], $note);
			}
		}
		if($order['is_pay'] == 1) {
			if(in_array($order['pay_type'], array('wechat', 'alipay', 'credit', 'peerpay', 'qianfan', 'majia', 'eleme', 'meituan')) || ($order['delivery_type'] == 2 && $order['pay_type'] == 'delivery')) {
				store_update_account($order['sid'], $order['store_final_fee'], 1, $order['id']);
			} else {
				$remark = "编号为{$order['id']}的订单属于线下支付,平台需要扣除{$order['plateform_serve_fee']}元服务费";
				store_update_account($order['sid'], -$order['plateform_serve_fee'], 3, $order['id'], $remark);
			}
			if($order['agentid'] > 0) {
				$remark = '';
				agent_update_account($order['agentid'], $order['agent_final_fee'], 1, $order['id'], $remark, 'takeout');
			}
		}
		//订单完成对客户赠送积分
		$credit1_config = $config_takeout['grant_credit']['credit1'];
		if($credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if($credit1 > 0) {
					mload()->model('member');
					member_credit_update($order['uid'], 'credit1', $credit1, array(0, "外送模块订单完成, 赠送{$credit1}积分"));
				}
			}
		}
		//赠送余额
		$cash_grant = order_fetch_discount($order['id'], 'cashGrant');
		if(!empty($cash_grant) && $cash_grant['fee'] > 0) {
			member_credit_update($order['uid'], 'credit2', $cash_grant['fee'], array(0, "外送模块订单完成, 赠送{$cash_grant['fee']}元"));
		}
		//赠送优惠券
		order_coupon_grant($order['id']);
		mload()->model('plugin');
		//赠送红包
		if($order['mall_first_order'] == 1 && check_plugin_perm('shareRedpacket')) {
			pload()->model('shareRedpacket');
			shareRedpacket_sharer_grant($order['uid']);
		}
		if(check_plugin_perm('ordergrant')) {
			pload()->model('ordergrant');
			ordergrant_grant($id);
		}
		if(check_plugin_perm('spread')) {
			pload()->model('spread');
			member_spread_confirm();
			spread_order_balance($id);
		}

		//对顾客的订单数据进行更新处理
		$member_mall = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));
		if(!empty($member_mall)) {
			$member_update = array(
				'success_num' => $member_mall['success_num'] + 1,
				'success_price' => round($member_mall['success_price'] + $order['final_fee'], 2),
				'success_last_time' => TIMESTAMP,
			);
			if(!$member_mall['success_first_time']) {
				$member_update['success_first_time'] = TIMESTAMP;
			}
			pdo_update('tiny_wmall_members', $member_update, array('id' => $member_mall['id']));
			$member_store = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $order['sid'], 'uid' => $order['uid']));
			if(empty($member_store)) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $order['sid'],
					'uid' => $order['uid'],
					'openid' => $order['openid'],
					'success_first_time' => TIMESTAMP,
					'success_last_time' => TIMESTAMP,
					'success_num' => 1,
					'success_price' => $order['final_fee'],
				);
				pdo_insert('tiny_wmall_store_members', $insert);
			} else {
				$member_update = array(
					'success_num' => $member_store['success_num'] + 1,
					'success_price' => round($member_store['success_price'] + $order['final_fee'], 2),
					'success_last_time' => TIMESTAMP,
				);
				pdo_update('tiny_wmall_store_members', $member_update, array('id' => $member_store['id']));
			}
		}
		order_insert_status_log($order['id'], 'end');
		order_status_notice($order['id'], 'end');
		return error(0, '完成订单成功');
	} elseif($type == 'delivery_ing') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		if($store['delivery_mode'] == 2) {
			return error(-1, '门店配送模式为平台配送， 不能直接设置为配送中');
		}
		if($order['deliveryer_id'] > 0) {
			return error(-1, '该订单已有配送员接单, 不能直接设置为配送中');
		}
		$update = array(
			'status' => 4,
			'delivery_status' => 4,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));

		order_insert_status_log($order['id'], 'delivery_ing');
		order_status_notice($order['id'], 'delivery_ing');
		//订单配送信息同步到美团
		if($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			mload()->model('plugin');
			pload()->classs('order');
			$openOrderId = $order['meituanOrderId'];
			$openOrder = new Order($order['sid']);
			$openOrder->updateOrderDeliverying($openOrderId);
		}
		return error(0, '变更订单状态成功');
	} elseif($type == 'remind') {
		$is_exist = 0;
		if($extra['role'] == 'eleme') {
			$log = pdo_get('tiny_wmall_order_remind_log', array('remindid' => $extra['remindId'], 'oid' => $order['id']));
			if(!empty($log)) {
				$is_exist = 1;
			}
		}
		if(empty($is_exist)) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $order['id'],
				'remindid' => $extra['remindId'] ? $extra['remindId'] : date('YmdHis') . random(5, true),
				'status' => 0,
				'channel' => $extra['role'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_order_remind_log', $log);
		}
		pdo_update('tiny_wmall_order', array('is_remind' => '1'), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($id, 'remind');
		order_clerk_notice($id, 'remind');
	} elseif($type == 'reply') {
		$reply = trim($extra['reply']);
		if(empty($reply)) {
			return error(-1, '回复内容不能为空');
		}
		if($order['order_plateform'] == 'eleme') {
			$remind_log = pdo_fetch('select id, remindid from ' . tablename('tiny_wmall_order_remind_log') . ' where oid = :oid and channel = :channel order by id desc', array(':oid' => $order['id'], ':channel' => 'eleme'));
			if(empty($remind_log)) {
				return error(-1, '未找到饿了么的催单记录');
			}
			$result = $openOrder->replyReminder($remind_log['remindid'], 'custom', $reply);
			if(is_error($result)) {
				return $result;
			}
		} else {
			$remind_log = pdo_fetch('select id,remindid from ' . tablename('tiny_wmall_order_remind_log') . ' where oid = :oid and channel = :channel order by id desc', array(':oid' => $order['id'], ':channel' => 'system'));
		}
		pdo_update('tiny_wmall_order_remind_log', array('reply' => $reply), array('id' => $remind_log['id']));
		pdo_update('tiny_wmall_order', array('is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($order['id'], 'remind_reply', $reply);
		order_status_notice($order['id'], 'reply_remind', "回复内容：" . $reply);
		return error(0, '回复顾客催单成功');
	} elseif($type == 'notify_clerk_handle') {
		order_clerk_notice($order['id'], 'place_order', '平台管理员催促您尽快处理该订单');
		return error(0, '通知商户接单成功');
	} elseif($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'pay_type' => 'cash',
			'paytime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'pay');
		order_status_notice($order['id'], 'pay');
		return error(0, '设置订单支付成功');
	}
}

function order_assign_deliveryer($order_id, $deliveryer_id, $update_deliveryer = false, $note = '') {
	global $_W;
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($order['status'] == 5) {
		return error(-1, '系统已完成， 请勿重复操作');
	}
	if($order['status'] == 6) {
		return error(-1, '系统已取消， 不能在进行其他操作');
	}
	if($order['deliveryer_id'] > 0 && !$update_deliveryer) {
		return error(-1, '该订单已经分配给其他配送员，不能重新指定配送员');
	}
	mload()->model('deliveryer');
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在或已经删除,请指定其他配送员配送');
	}
	$permission = pdo_getall('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id), array('sid'), 'sid');
	if(empty($permission)) {
		return error(-1, "配送员{$deliveryer['title']}没有配送订单的权限");
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode'));
	if($store['delivery_mode'] == 1) {
		if(!in_array($order['sid'], array_keys($permission))) {
			return error(-1, "门店配送模式为店内配送，该配送员没有该门店的配送权限");
		}
	} else {
		if(!in_array(0, array_keys($permission))) {
			return error(-1, "该配送员没有平台订单的配送权限");
		}
	}
	$update = array(
		'status' => 4,
		'delivery_status' => 7, //订单已被抢单
		'deliveryer_id' => $deliveryer_id,
		'delivery_type' => $store['delivery_mode'],
		'delivery_assign_time' => TIMESTAMP,
	);
	pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
	if($order['deliveryer_id'] > 0) {
		deliveryer_order_num_update($order['deliveryer_id']);
	}
	deliveryer_order_num_update($deliveryer_id);
	$note = "配送员：{$deliveryer['title']}, 手机号：<a href='tel:{$deliveryer['mobile']}'>{$deliveryer['mobile']}</a>";
	order_insert_status_log($order['id'], 'delivery_assign', $note);
	$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
	order_status_notice($order['id'], 'delivery_assign', $remark);
	order_deliveryer_notice($order['id'], 'new_delivery', $deliveryer['id']);
	//订单配送信息同步到美团
	if($order['order_plateform'] == 'meituan') {
		$_W['_plugin'] = array(
			'name' => 'meituan'
		);
		mload()->model('plugin');
		pload()->classs('order');
		$openOrderId = $order['meituanOrderId'];
		$openOrder = new Order($order['sid']);
		$openOrder->updateOrderDeliverying($openOrderId, $deliveryer);
	}
	return error(0, '订单分派配送员成功');
}

function order_system_status_update($id, $type, $extra = array()) {
	global $_W;
	set_time_limit(0);
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_notice_deliveryer'));
	$_W['agentid'] = $order['agentid'];
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	if($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'order_channel' => $extra['channel'],
			'pay_type' => $extra['type'],
			'final_fee' => $extra['card_fee'],
			'is_pay' => 1,
			'paytime' => TIMESTAMP,
			'transaction_id' => $extra['transaction_id'],
			'out_trade_no' => $extra['uniontid'],
		);
		if($order['order_type'] <= 2) {
			if($store['auto_handel_order'] == 1) {
				$update['status'] = 2;
				$update['handletime'] = TIMESTAMP;
				if($order['order_type'] == 2) {
					$update['status'] = 4;
				}
				if($store['auto_notice_deliveryer'] == 1 && $order['order_type'] == 1) {
					$update['delivery_type'] = $store['delivery_mode'];
					$update['status'] = 3; //待配送（待抢单）
					$update['delivery_status'] = 3;
					$update['deliveryer_id'] = 0;
					$update['clerk_notify_collect_time'] = TIMESTAMP;
				}
				pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
				order_insert_status_log($order['id'], 'pay');
				order_insert_status_log($order['id'], 'handle');
				if($store['auto_notice_deliveryer'] == 1) {
					order_insert_status_log($order['id'], 'delivery_wait');
				}
				order_print($order['id']);
				order_status_notice($order['id'], 'handle');
				order_clerk_notice($order['id'], 'place_order');
				if($store['auto_notice_deliveryer'] == 1) {
					order_status_update($order['id'], 'notify_deliveryer_collect', array('notify_channel' => 'first'));
				}
			} else {
				pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
				order_insert_status_log($order['id'], 'pay');
				order_print($order['id']);
				order_status_notice($order['id'], 'pay');
				//下单立即通知
				if(empty($config_takeout['notify_rule_clerk']['notify_delay'])) {
					order_clerk_notice($order['id'], 'place_order');
				}
			}
			if(check_plugin_perm('superRedpacket')) {
				mload()->model('plugin');
				pload()->model('superRedpacket');
				superRedpacket_share_insert($order['id']);
			}
		} elseif($order['order_type'] == 3) {
			//店内订单
			mload()->model('table');
			$update['status'] = 2;
			pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));

			table_order_update($order['table_id'], $order['id'], 4);
			order_insert_status_log($order['id'], 'pay');
			order_print($order['id']);
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'store_order_pay');
		} elseif ($order['order_type'] == 4) {
			$update['status'] = 2;
			pdo_update('tiny_wmall_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
			order_insert_status_log($order['id'], 'pay');
			order_print($order['id']);
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'reserve_order_pay');
		}
		//减库存
		$stat = pdo_getall('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'oid' => $order['id']), array('id', 'sid', 'goods_id', 'option_id', 'goods_num', 'goods_discount_num', 'bargain_id', 'total_update_status'));
		if(!empty($stat)) {
			foreach($stat as $row) {
				pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set sailed = sailed + {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['goods_id']));
				pdo_query('UPDATE ' . tablename('tiny_wmall_store') . " set sailed = sailed + {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['sid']));
				if(!$row['total_update_status']) {
					if(!$row['option_id']) {
						$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']));
						if($goods['total'] != -1 && $goods['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$row['goods_num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $row['goods_id']));
							$total_now = $goods['total'] - $row['goods_num'];
							if($goods['total_warning'] > 0 && $total_now <= $goods['total_warning']) {
								//库存报警
								goods_total_warning_notice($goods, 0, array('total_now' => $total_now));
							}
						}
						if($row['bargain_id'] > 0 && $row['goods_discount_num'] > 0) {
							$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $row['bargain_id'], 'goods_id' => $row['goods_id']));
							if($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
								pdo_query('UPDATE ' . tablename('tiny_wmall_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$row['goods_discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $row['bargain_id'], ':goods_id' => $row['goods_id']));
							}
						}
					} else {
						$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $row['option_id']));
						if(!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_goods_options') . " set total = total - {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['option_id']));
							$total_now = $option['total'] - $row['goods_num'];
							if($option['total_warning'] > 0 && $total_now <= $option['total_warning']) {
								//库存报警
								goods_total_warning_notice($option['goods_id'], $option, array('total_now' => $total_now));
							}
						}
					}
					pdo_update('tiny_wmall_order_stat', array('total_update_status' => 1), array('id' => $stat['id']));
				}
			}
		}
		return error(0, '订单支付成功');
	}
}

function order_deliveryer_update_status($id, $type, $extra = array()) {
	global $_W;
	$order = order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];

	if($type == 'delivery_assign') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if($order['deliveryer_id'] > 0) {
			return error(-1, '来迟了, 该订单已被别人接单');
		}
		if(in_array($extra['role'], array('eleme', 'meituan'))) {
			$deliveryer = $extra['deliveryer'];
		} else {
			if(empty($extra['deliveryer_id'])) {
				return error(-1, '配送员id不存在');
			}
			$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
			if(empty($deliveryer)) {
				return error(-1, '配送员不存在');
			}
			if($order['delivery_type'] == 2) {
				if($config_takeout['deliveryer_collect_max'] > 0) {
					$params = array(
						':uniacid' => $_W['uniacid'],
						':deliveryer_id' => $deliveryer['id'],
					);
					$num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 7 or delivery_status = 4)', $params);
					$num = intval($num);
					if($num >= $config_takeout['deliveryer_collect_max']) {
						return error(-1, "每人最多可抢{$config_takeout['deliveryer_collect_max']}个外卖单");
					}
				}
			}
		}
		$update = array(
			'status' => 4,
			'delivery_status' => 7, //订单已被抢单
			'deliveryer_id' => $extra['deliveryer_id'],
			'delivery_assign_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		mload()->model('deliveryer');
		if($order['deliveryer_id'] > 0) {
			deliveryer_order_num_update($order['deliveryer_id']);
		}
		deliveryer_order_num_update($deliveryer['id']);
		$note = "配送员：{$deliveryer['title']}, 手机号：<a href='tel:{$deliveryer['mobile']}'>{$deliveryer['mobile']}</a>";
		order_insert_status_log($order['id'], 'delivery_assign', $note);
		$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
		order_status_notice($order['id'], 'delivery_assign', $remark);
		if($config_takeout['deliveryer_collect_notify_clerk'] == 1) {
			order_clerk_notice($order['id'], 'collect', $remark);
			order_print($order['id'], 'collect');
		}
		//订单配送信息同步到美团
		if($order['order_plateform'] == 'meituan') {
			$_W['_plugin'] = array(
				'name' => 'meituan'
			);
			mload()->model('plugin');
			pload()->classs('order');
			$openOrderId = $order['meituanOrderId'];
			$openOrder = new Order($order['sid']);
			$openOrder->updateOrderDeliverying($openOrderId, $deliveryer);
		}
		return error(0, '抢单成功');
	} elseif($type == 'delivery_instore') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if(!in_array($extra['role'], array('eleme', 'meituan'))) {
			if(empty($extra['deliveryer_id'])) {
				return error(-1, '配送员不存在');
			}
			$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
			if(empty($deliveryer)) {
				return error(-1, '配送员不存在');
			}
			if($order['deliveryer_id'] != $deliveryer['id']) {
				return error(-1, '该订单不是您配送，不能确认取货');
			}
		}
		$update = array(
			'delivery_status' => 4, //已取货
			'delivery_instore_time' => TIMESTAMP,
			'delivery_handle_type' => !empty($extra['delivery_handle_type']) ? $extra['delivery_handle_type'] : 'wechat'
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_instore');
		order_status_notice($order['id'], 'delivery_instore');
		return error(0, '确认到店成功');
	} elseif($type == 'delivery_success') {
		$result = order_status_update($order['id'], 'end', $extra);
		if(is_error($result)) {
			return $result;
		}
		mload()->model('deliveryer');
		deliveryer_order_num_update($order['deliveryer_id']);
		return error(0, '确认送达成功');
	} elseif($type == 'delivery_transfer') {
		if($order['status'] == 5) {
			return error(-1, '系统已完成， 不能申请转单');
		}
		if($order['status'] == 6) {
			return error(-1, '系统已取消， 不能申请转单');
		}
		if($order['delivery_type'] != 2) {
			return error(-1, '该单属于店内配送单，不能申请转单');
		}
		if(!$config_takeout['deliveryer_transfer_status']) {
			return error(-1, '平台不允许转单');
		}
		if(empty($extra['reason'])) {
			return error(-1, '转单理由不能为空');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能申请转单');
		}
		$transfer_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer_transfer_log') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and order_type = :order_type and stat_day = :stat_day', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $extra['deliveryer_id'], ':order_type' => 'takeout', ':stat_day' => date('Ymd')));
		if($config_takeout['deliveryer_transfer_max'] > 0 && $transfer_num >= $config_takeout['deliveryer_transfer_max']) {
			return error(-1, "每天最多可以转单{$config_takeout['deliveryer_transfer_max']}次,您已超过限定次数");
		}
		$transfer_log = array(
			'uniacid' => $_W['uniacid'],
			'deliveryer_id' => $extra['deliveryer_id'],
			'order_type' => 'takeout',
			'order_id' => $order['id'],
			'reason' => $extra['reason'],
			'addtime' => TIMESTAMP,
			'stat_year' => date('Y'),
			'stat_month' => date('Ym'),
			'stat_day' => date('Ymd'),
		);
		pdo_insert('tiny_wmall_deliveryer_transfer_log', $transfer_log);
		$update = array(
			'status' => 3,
			'delivery_status' => 3,
			'delivery_handle_type' => 'wechat',
			'deliveryer_id' => 0,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_transfer', "转单理由:{$extra['reason']},等待其他配送员接单");
		order_status_notice($order['id'], 'notify_deliveryer_collect');
		return error(0, '转单成功');
	}
}

function order_manager_notice($order_id, $type, $note = '') {
	global $_W;
	$maneger = $_W['we7_wmall']['config']['manager'];
	if(empty($maneger)) {
		return error(-1, '管理员信息不完善');
	}
	$order = order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	$store = store_fetch($order['sid'], array('id', 'title'));
	$acc = WeAccount::create($order['acid']);
	if($type == 'new_delivery') {
		$title = '平台有新的外卖订单，请尽快登陆后台调度处理';
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('Y-m-d H:i', $order['paytime']),
		);
	} elseif($type == 'dispatch_error') {
		$title = '平台有新的外卖订单，系统自动调度失败，请登录后台人工调度';
		$remark = array(
			"门店名称: {$store['title']}",
			"订单类型: {$order['order_type_cn']}",
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('Y-m-d H:i', $order['paytime']),
		);
	} elseif($type == 'no_working_deliveryer') {
		$title = '平台有新的待配送外卖订单,但没有接单中的配送员,请尽快协调';
		$remark = array(
			"订单类型: 外卖订单",
		);
	}
	if(!empty($note)) {
		if(!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);;
		}
	}
	if(!empty($end_remark)) {
		$remark[] = $end_remark;
	}
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send);
	if(is_error($status)) {
		slog('wxtplNotice', '平台新订单微信通知平台管理员', $send, $status['message']);
	}
	return $status;
}

function order_calculate_deliveryer_fee($order) {
	global $_W;
	if($order['order_type'] != 1) {
		return 0;
	}
	if($order['delivery_type'] == 1) {
		return 0;
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$plateform_deliveryer_fee = floatval($config_takeout['deliveryer_fee']);
	if($config_takeout['deliveryer_fee_type'] == 2) {
		if(is_open_order($order)) {
			$order['delivery_fee'] = $config_takeout['plateform_delivery_fee'];
		}
		$plateform_deliveryer_fee = round($order['delivery_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
	} elseif($config_takeout['deliveryer_fee_type'] == 3) {
		$config_deliveryer_fee = $config_takeout['deliveryer_fee'];
		$plateform_deliveryer_fee = floatval($config_deliveryer_fee['start_fee']);
		$over_km = $order['distance'] - $config_deliveryer_fee['start_km'];
		if($over_km > 0) {
			$over_fee = round($over_km * $config_deliveryer_fee['pre_km'], 2);
		}
		$plateform_deliveryer_fee += $over_fee;
		$plateform_deliveryer_fee = min($plateform_deliveryer_fee, $config_deliveryer_fee['max_fee']);
	}
	return $plateform_deliveryer_fee;
}

function order_update_bill($order_id, $extra_data = array()) {
	global $_W;
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$account = store_account($order['sid']);
	$plateform_delivery_fee = 0;
	if($order['order_type'] == 1) {
		$fee_type = 'fee_takeout';
		if($order['delivery_type'] == 2) {
			//平台配送模式
			$plateform_delivery_fee = $order['delivery_fee'];
		}
	} elseif($order['order_type'] == 2) {
		$fee_type = 'fee_selfDelivery';
	} else {
		$fee_type = 'fee_instore';
	}

	$fee_config = $account[$fee_type];
	if($fee_config['type'] == 2) {
		$plateform_serve_rate = 0;
		$platform_serve_fee = floatval($fee_config['fee']);
		$plateform_serve = array(
			'fee_type' => 2,
			'fee_rate' => 0,
			'fee' => $platform_serve_fee,
			'note' => "每单固定{$platform_serve_fee}元"
		);
	} else {
		$basic = 0;
		$note = array(
			'yes' => array(),
			'no' => array(),
		);
		$fee_items = store_serve_fee_items();
		if(!empty($fee_config['items_yes'])) {
			foreach($fee_config['items_yes'] as $item) {
				if($item == 'delivery_fee' && $order['delivery_type'] == 2) {
					continue;
				}
				$basic += $order[$item];
				$note['yes'][] = "{$fee_items['yes'][$item]} ￥{$order[$item]}";
			}
		}
		if(!empty($fee_config['items_no'])) {
			foreach($fee_config['items_no'] as $item) {
				$basic -= $order[$item];
				$note['no'][] = "{$fee_items['no'][$item]} ￥{$order[$item]}";
			}
		}
		if($basic < 0) {
			$basic = 0;
		}
		$plateform_serve_rate = $fee_config['fee_rate'];
		$platform_serve_fee = round($basic * ($plateform_serve_rate / 100), 2);
		$text = '(' . implode(' + ', $note['yes']);
		if(!empty($note['no'])) {
			$text .= ' - ' . implode(' - ', $note['no']);
		}
		$text .= ") x {$plateform_serve_rate}%";
		$plateform_serve = array(
			'fee_type' => 1,
			'fee_rate' => $plateform_serve_rate,
			'fee' => $platform_serve_fee,
			'note' => $text
		);
	}
	$store_order_total_fee = $order['price'] + $order['box_price'] + $order['pack_fee'] + $order['serve_fee'];
	if($order['order_type'] == 1) {
		$store_bear_deliveryprice = 0;
		if($order['delivery_type'] == 1) {
			//店内配送模式
			$store_order_total_fee += $order['delivery_fee'];
		} else {
			//平台配送模式下,如果商家设置承担额外配送费,则要从商家的利润中扣除额外配送费
			$store = pdo_get('tiny_wmall_store', array('id' => $order['sid']), array('delivery_extra'));
			$extra = unserialize($store['delivery_extra']);
			$store_bear_deliveryprice = floatval($extra['store_bear_deliveryprice']);
			if(!empty($store_bear_deliveryprice)) {
				$platform_serve_fee += $store_bear_deliveryprice;
				$plateform_serve['fee'] += $store_bear_deliveryprice;
				$plateform_serve['note'] .= " + 商家额外承担配送费 ￥{$store_bear_deliveryprice}";
			}
		}
	}
	if(!empty($extra_data['activity']['list']['cashGrant'])) {
		$order['discount_fee'] += $extra_data['activity']['list']['cashGrant']['value'];
	}
	$store_final_fee = $store_order_total_fee - $order['discount_fee'] - $platform_serve_fee + $order['plateform_discount_fee'] + $order['agent_discount_fee'];
	$store_final_fee = $store_final_fee < 0 ? 0 : $store_final_fee;

	if($_W['is_agent']) {
		$account_agent = get_agent($order['agentid'], 'fee');
		$agent_fee_config = $account_agent['fee'][$fee_type];
		if($agent_fee_config['type'] == 2) {
			$agent_serve_fee = floatval($agent_fee_config['fee']);
			$agent_serve = array(
				'fee_type' => 2,
				'fee_rate' => 0,
				'fee' => $agent_serve_fee,
				'note' => "每单固定{$agent_serve_fee}元"
			);
		} else {
			$basic = 0;
			$note = array(
				'yes' => array(),
				'no' => array(),
			);
			$fee_items = agent_serve_fee_items();
			if(!empty($agent_fee_config['items_yes'])) {
				foreach($agent_fee_config['items_yes'] as $item) {
					if($item == 'delivery_fee' && $order['delivery_type'] == 2) {
						continue;
					}
					$basic += $order[$item];
					$note['yes'][] = "{$fee_items['yes'][$item]} ￥{$order[$item]}";
				}
			}
			if(!empty($agent_fee_config['items_no'])) {
				foreach($agent_fee_config['items_no'] as $item) {
					$basic -= $order[$item];
					$note['no'][] = "{$fee_items['no'][$item]} ￥{$order[$item]}";
				}
			}
			if($basic < 0) {
				$basic = 0;
			}
			$agent_serve_rate = floatval($agent_fee_config['fee_rate']);
			$agent_serve_fee = round($basic * ($agent_serve_rate / 100), 2);
			$text = '(' . implode(' + ', $note['yes']);
			if(!empty($note['no'])) {
				$text .= ' - ' . implode(' - ', $note['no']);
			}
			$text .= ") x {$agent_serve_rate}%";
			$agent_serve = array(
				'fee_type' => 1,
				'fee_rate' => $agent_serve_rate,
				'fee' => $agent_serve_fee,
				'note' => $text,
			);
		}
	}
	$plateform_deliveryer_fee = order_calculate_deliveryer_fee($order);
	$agent_final_fee = $platform_serve_fee - $agent_serve_fee - $order['agent_discount_fee'];
	if($order['delivery_type'] == 2) {
		//平台配送模式下,平台需要给配送员支付配送费，所以从代理的利润里扣除
		$agent_final_fee = $agent_final_fee + $plateform_delivery_fee - $plateform_deliveryer_fee;
	}
	$agent_serve['final'] = "(代理商抽取佣金 ￥{$platform_serve_fee} - 平台服务佣金 ￥{$agent_serve_fee} - 代理商补贴 ￥{$order['agent_discount_fee']} + 代理商配送费 ￥{$plateform_delivery_fee} - 代理商支付给配送员配送费 ￥{$plateform_deliveryer_fee})";
	$data = array(
		'plateform_delivery_fee' => $plateform_delivery_fee,
		'plateform_deliveryer_fee' => $plateform_deliveryer_fee,
		'plateform_serve_rate' => $plateform_serve_rate,
		'plateform_serve_fee' => $platform_serve_fee,
		'plateform_serve' => iserializer($plateform_serve),
		'store_final_fee' => $store_final_fee,
		'agent_final_fee' => $agent_final_fee,
		'agent_serve' => iserializer($agent_serve),
		'agent_serve_fee' => $agent_serve_fee,
		'stat_year' => date('Y', $order['addtime']),
		'stat_month' => date('Ym', $order['addtime']),
		'stat_day' => date('Ymd', $order['addtime']),
	);
	pdo_update('tiny_wmall_order', $data, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	return true;
}

function order_mall_remind() {
	global $_W;
	icheckauth(false);
	$order = array();
	if($_W['we7_wmall']['config']['close']['status'] != 2 && $_W['member']['uid'] > 0) {
		$order = pdo_fetch('select id,status,addtime,paytime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and order_type = 1 and is_pay = 1 and status < 5 order by id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
		if(!empty($order)) {
			$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where oid = :id order by id desc', array(':id' => $order['id']));
			$order['log'] = $log;
		}
	}
	return $order;
}

function order_coupon_grant($id) {
	global $_W;
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id), array('id', 'sid', 'final_fee', 'uid'));
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	mload()->model('coupon');
	$coupon = coupon_grant_available($order['sid'], $order['final_fee']);
	if(empty($coupon) || !is_array($coupon)) {
		return error(-1, '门店没有设置满赠券活动');
	}
	$params = $coupon['coupons'];
	$params['coupon_id'] = $coupon['id'];
	$params['sid'] = $order['sid'];
	$params['channel'] = 'couponGrant';
	$params['type'] = 'couponGrant';
	$params['uid'] = $order['uid'];
	//$params['order_id'] = $order['id'];
	$result = coupon_grant($params);
	return $result;
}


function order_time_analyse($id) {
	global $_W;
	$order = order_fetch($id);
	$time_interval = array(
		'store_consum_time' => transform_time($order['handletime'] - $order['paytime']),
		'deliveryer_consum_time' => transform_time($order['endtime'] - $order['delivery_assign_time']),
		'order_consum_time' => transform_time($order['endtime'] - $order['paytime']),
	);
	$timeout_limit = $_W['we7_wmall']['config']['takeout']['order']['timeout_limit'];
	if($timeout_limit > 0) {
		$time_interval['timeout_text'] = '';
		$endtime = TIMESTAMP;
		if($order['status'] == 5) {
			$endtime = $order['endtime'];
		}
		$time_difference = $endtime - $order['paytime'] - $timeout_limit * 60;
		if($time_difference > 0) {
			$time_interval['is_timeout'] = 1;
			$time_difference = transform_time($time_difference);
			$time_interval['timeout_text'] = "已超时{$time_difference}";
			$time_interval['timeout_css'] = 'color-danger';
		} else {
			$time_interval['is_timeout'] = 0;
			$time_difference = -$time_difference;
			$time_difference = transform_time($time_difference);
			$time_interval['timeout_text'] = "距超时{$time_difference}";
			$time_interval['timeout_css'] = 'color-default';
		}
	}
	return $time_interval;
}

function goods_total_warning_notice($goodsOrid, $optionOrid, $extra = array()) {
	global $_W;
	$goods = $goodsOrid;
	if(!is_array($goodsOrid)) {
		$goodsOrid = intval($goodsOrid);
		$goods = pdo_get('tiny_wmall_goods', array(':uniacid' => $_W['uniacid'], 'id' => $goodsOrid));
	}
	if(empty($goods)) {
		return error(-1, '商品不存在');
	}
	if(!empty($optionOrid)) {
		$option = $optionOrid;
		if(!is_array($optionOrid)) {
			$optionOrid = intval($optionOrid);
			$option = pdo_get('tiny_wmall_goods_option', array(':uniacid' => $_W['uniacid'], 'id' => $optionOrid));
		}
		if(empty($option)) {
			return error(-1, '商品规格不存在');
		}
	}
	$tips = "商品{$goods['title']}";
	if(!empty($option)) {
		$tips .= "(规格:{$option['name']}),当前库存{$extra['total_now']}{$goods['uniname']},达到预警值,快去备货吧！";
	}
	$params = array(
		'first' => $tips,
		'keyword1' => "商品{$goods['title']}库存不足",
		'keyword2' => "{$_W['we7_wmall']['config']['mall']['title']}",
		'keyword3' => date('Y-m-d H:i:s'),
		'keyword4' => "商品{$goods['title']}库存不足",
	);
	$send = sys_wechat_tpl_format($params);
	mload()->model('clerk');
	$clerks = clerk_fetchall($goods['sid']);
	$acc = WeAccount::create($_W['acid']);
	foreach($clerks as $clerk) {
		if($clerk['extra']['accept_wechat_notice']) {
			$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['wechat']['warning_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '库存预警通知店员', $send, $status['message']);
			}
		}
	}
	return true;
}

