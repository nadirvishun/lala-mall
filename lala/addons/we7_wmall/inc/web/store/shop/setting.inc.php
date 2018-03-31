<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'post';

if($ta == 'post') {
	$_W['page']['title'] = '门店信息';
	$id = $_W['we7_wmall']['sid'];
	if($id) {
		$item = store_fetch($id);
		if(empty($item)) {
			imessage('门店信息不存在或已删除', 'referer', 'error');
		} else {
			$item['map'] = array('lat' => $item['location_x'], 'lng' => $item['location_y']);
			$item['cid'] = array_filter(explode('|', $item['cid']));
			$item['isChange'] = ($item['delivery_mode'] == 1);
			$config_eleme = store_get_openplateform($id, 'eleme');
			$config_meituan = store_get_openplateform($sid, 'meituan');
		}
		$sys_url = imurl('wmall/store/goods', array('sid' => $item['id']), true);
		$wx_url = $item['wechat_qrcode']['url'];
	} else {
		if($_W['role'] == 'merchanter') {
			imessage('您没有该添加门店的权限', referer(), 'error');
		}
		$item['business_hours'] = array(array('s' => '8:00', 'e' => '20:00'));
		$item['sns'] = array();
		$item['mobile_verify'] = array();
		$item['payment'] = array();
		$item['remind_time_limit'] = 10;
		$item['status'] = 1;
		$item['remind_reply'] = array(
			'快递员狂奔在路上,请耐心等待'
		);
		$item['delivery_mode'] = 1;
		$item['delivery_fee_mode'] = 1;
		$item['qualification'] = array();
		$item['isChange'] = 1;
	}
	if($_W['ispost']) {
		$data = array(
			'title' => trim($_GPC['title']),
			'logo' => trim($_GPC['logo']),
			'telephone' => trim($_GPC['telephone']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'pack_price' =>trim($_GPC['pack_price']),
			'delivery_area' => trim($_GPC['delivery_area']),
			'address' =>  trim($_GPC['address']),
			'location_x' => $_GPC['map']['lat'],
			'location_y' => $_GPC['map']['lng'],
			'displayorder' => intval($_GPC['displayorder']),
			'notice' => trim($_GPC['notice']),
			'tips' => trim($_GPC['tips']),
			'content' => trim($_GPC['content']),
			'sns' => iserializer(array(
				'qq' => trim($_GPC['sns']['qq']),
				'weixin' => trim($_GPC['sns']['weixin']),
			)),
			'invoice_status' => intval($_GPC['invoice_status']),
			'token_status' => intval($_GPC['token_status']),
			'comment_status' => intval($_GPC['comment_status']),
			'payment' => iserializer($_GPC['payment']),
			'remind_time_limit' => intval($_GPC['remind_time_limit']),
			'delivery_type' => intval($_GPC['delivery_type']),
			'delivery_within_days' => intval($_GPC['delivery_within_days']),
			'delivery_reserve_days' => intval($_GPC['delivery_reserve_days']),
			'auto_handel_order' => intval($_GPC['auto_handel_order']),
			'auto_notice_deliveryer' => intval($_GPC['auto_notice_deliveryer']),
			'is_meal' => intval($_GPC['is_meal']),
			'is_paybill' => intval($_GPC['is_paybill']),
			'is_assign' => intval($_GPC['is_assign']),
			'is_reserve' => intval($_GPC['is_reserve']),
			'forward_mode' => intval($_GPC['forward_mode']),
			'forward_url' => trim($_GPC['forward_url']),
			'qualification' => iserializer(array(
				'business' => array(
					'thumb' => trim($_GPC['qualification']['business']),
				),
				'service' => array(
					'thumb' => trim($_GPC['qualification']['service']),
				)
			)),
			'elemeShopId' => trim($_GPC['elemeShopId']),
		);
		if($data['forward_mode'] == 5 && empty($data['forward_url'])) {
			$data['forward_mode'] = 0;
		}
		$cids = array();
		if(!empty($_GPC['cid'])) {
			foreach($_GPC['cid'] as $cid) {
				$cid = intval($cid);
				if($cid > 0) {
					$cids[] = $cid;
				}
			}
		}
		$cids = implode('|', $cids);
		if(!empty($_W['ismanager']) || !empty($_W['isoperator'])) {
			$data['cid'] = "|{$cids}|";
		}

		$serve_fee = array(
			'type' => intval($_GPC['serve_fee']['type']),
			'fee' => 0
		);
		if($serve_fee['type'] == 1) {
			$serve_fee['fee'] = trim($_GPC['serve_fee']['fee_1']);
		} else {
			$serve_fee['fee'] = trim($_GPC['serve_fee']['fee_2']);
		}
		$data['serve_fee'] = iserializer($serve_fee);
		if($item['delivery_mode'] == 1) {
			$data['delivery_fee_mode'] = intval($_GPC['delivery_fee_mode']);
			$data['delivery_price'] = intval($_GPC['delivery_price']);
			$data['auto_get_address'] = intval($_GPC['auto_get_address']);
			$data['send_price'] = intval($_GPC['send_price_1']);
			$data['delivery_free_price'] = intval($_GPC['delivery_free_price_1']);
			$data['pack_price'] = trim($_GPC['pack_price']);
			$data['delivery_time'] = intval($_GPC['delivery_time']);
			$data['serve_radius'] = floatval($_GPC['serve_radius']);
			$data['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
			if(!$data['not_in_serve_radius']) {
				$data['auto_get_address'] = 1;
				if(empty($data['serve_radius'])) {
					imessage('您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径', '', 'ajax');
				}
			}

			if($data['delivery_fee_mode'] == 1) {
				$data['delivery_price'] = trim($_GPC['delivery_price']);
			} elseif($data['delivery_fee_mode'] == 2) {
				$data['send_price'] = intval($_GPC['send_price_2']);
				$data['delivery_free_price'] = intval($_GPC['delivery_free_price_2']);
				$data['auto_get_address'] = 1;
				$data['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
				$data['delivery_price'] = iserializer(array(
					'start_fee' => trim($_GPC['start_fee']),
					'start_km' => trim($_GPC['start_km']),
					'pre_km_fee' => trim($_GPC['pre_km_fee']),
				));
			} elseif($update['delivery_fee_mode'] == 3) {
				$data['auto_get_address'] = 1;
			}
			$times = array();
			if(!empty($_GPC['times']['start'])) {
				foreach($_GPC['times']['start'] as $key => $val) {
					$start = trim($val);
					$end = trim($_GPC['times']['end'][$key]);
					if(empty($start) || empty($end)) {
						continue;
					}
					$times[] = array(
						'start' => $start,
						'end' => $end,
						'status' => intval($_GPC['times']['status'][$key]),
						'fee' => intval($_GPC['times']['fee'][$key])
					);
				}
				$data['delivery_times'] = iserializer($times);
			}
			$_GPC['areas'] = str_replace('&nbsp;', '#nbsp;', $_GPC['areas']);
			$_GPC['areas'] = json_decode(str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($_GPC['areas']))), true);
			foreach($_GPC['areas'] as $key => &$val) {
				if(empty($val['path'])) {
					unset($_GPC['areas'][$key]);
				}
				$path = array();
				foreach($val['path'] as $row) {
					$path[] = array($row['lng'], $row['lat']);
				}
				$val['path'] = $path;
				unset($val['isAdd'], $val['isActive']);
			}
			$data['delivery_areas'] = iserializer($_GPC['areas']);
		}
		$hour = array();
		if(!empty($_GPC['business_start_hours'])) {
			$hour = array();
			foreach($_GPC['business_start_hours'] as $k => $v) {
				$v = str_replace('：', ':', trim($v));
				if(!strexists($v, ':')) {
					$v .= ':00';
				}
				$end = str_replace('：', ':', trim($_GPC['business_end_hours'][$k]));
				if(!strexists($end, ':')) {
					$end.= ':00';
				}
				$hour[] = array('s' => $v, 'e' => $end);
			}
		}
		$data['business_hours'] = iserializer($hour);

		if(!empty($_GPC['thumbs']['image'])) {
			$thumbs = array();
			foreach($_GPC['thumbs']['image'] as $key => $image) {
				if(empty($image)) {
					continue;
				}
				$thumbs[] = array(
					'image' => $image,
					'url' => trim($_GPC['thumbs']['url'][$key]),
				);
			}
			$data['thumbs'] = iserializer($thumbs);
		} else {
			$data['thumbs'] = '';
		}
		if(!empty($_GPC['remind_reply'])) {
			$remind_reply = array();
			foreach($_GPC['remind_reply'] as $reply) {
				$reply = trim($reply);
				if(empty($reply)) {
					continue;
				}
				$remind_reply[] = $reply;
			}
			$data['remind_reply'] = iserializer($remind_reply);
		} else {
			$data['remind_reply'] = '';
		}
		if(!empty($_GPC['comment_reply'])) {
			$remind_reply = array();
			foreach($_GPC['comment_reply'] as $reply) {
				$reply = trim($reply);
				if(empty($reply)) {
					continue;
				}
				$comment_reply[] = $reply;
			}
			$data['comment_reply'] = iserializer($comment_reply);
		} else {
			$data['comment_reply'] = iserializer(array());
		}

		$data['order_note'] = array();
		if(!empty($_GPC['order_note'])) {
			foreach($_GPC['order_note'] as $order_note) {
				if(empty($order_note)) continue;
				$data['order_note'][] = $order_note;
			}
		}
		$data['order_note'] = iserializer($data['order_note']);

		if(!empty($_GPC['custom_title'])) {
			$custom_url = array();
			foreach($_GPC['custom_title'] as $key => $title) {
				$title = trim($title);
				$url = trim($_GPC['custom_link'][$key]);
				if(empty($title) || empty($url)) {
					continue;
				}
				$custom_url[] = array('title' => $title, 'url' => $url);
			}
			$data['custom_url'] = iserializer($custom_url);
		} else {
			$data['custom_url'] = iserializer(array());
		}

		//饿了么平台绑定
		$eleme_order = array(
			'auto_handel_order' =>  intval($_GPC['eleme']['auto_handel_order']),
			'auto_notice_deliveryer' =>  intval($_GPC['eleme']['auto_notice_deliveryer']),
			'auto_print' =>  intval($_GPC['eleme']['auto_print']),
		);
		store_set_openplateform($id, 'eleme.order', $eleme_order);
		//美团平台绑定
		$meituan_order = array(
			'auto_handel_order' =>  intval($_GPC['meituan']['auto_handel_order']),
			'auto_notice_deliveryer' =>  intval($_GPC['meituan']['auto_notice_deliveryer']),
			'auto_print' =>  intval($_GPC['meituan']['auto_print']),
		);
		store_set_openplateform($id, 'meituan.order', $meituan_order);

		pdo_update('tiny_wmall_store', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		$sid = $id;
		store_delivery_times($sid, true);
		imessage(error(0, '编辑门店信息成功'), iurl('store/shop/setting', array('_sid' => $sid)), 'ajax');
	}
	$categorys = store_fetchall_category();
	$pay = get_available_payment();
	if(empty($pay)) {
		imessage('公众号没有设置支付方式,请先设置支付方式', referer(), 'info');
	}
	include itemplate('store/shop/setting');
}

if($ta == 'template') {
	$sid = intval($_GPC['id']);
	$template = trim($_GPC['t']) ? trim($_GPC['t']) : 'index';
	pdo_update('tiny_wmall_store', array('template' => $template), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '设置页面风格成功'), referer(), 'ajax');
}
