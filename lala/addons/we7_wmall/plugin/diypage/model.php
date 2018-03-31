<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function diypage_menu($id) {
	global $_W;
	$menu = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_diypage_menu') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(!empty($menu)) {
		$menu['data'] = json_decode(base64_decode($menu['data']), true);
	}
	return $menu;
}

function diypage_menus() {
	global $_W;
	$menu = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_diypage_menu') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	return $menu;
}

function get_diypage_diy($id, $mobile = false) {
	global $_W;
	$id = intval($id);
	if(empty($id)) {
		return false;
	}
	$page = pdo_get('tiny_wmall_diypage', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($page)) {
		return false;
	}
	$page['data'] = base64_decode($page['data']);
	$page['data'] = json_decode($page['data'], true);
	if(!$mobile) {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'waimai_goods') {
					$item['data'] = get_diypage_waimai_goods($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'waimai_stores') {
					$item['data'] = get_diypage_waimai_store($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'notice') {
					$item['data'] = get_diypage_notice($item);
					if(empty($page['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'richtext') {
					$item['params']['content'] = htmlspecialchars_decode($item['params']['content']);
				} else {
					if(($item['id'] == 'picture') || ($item['id'] == 'picturew')) {
						if(empty($item['style'])) {
							$item['style'] = array(
								'background' => '#ffffff',
								'paddingtop' => '0',
								'paddingleft' => '0'
							);
						}
					} elseif(empty($item['id'])) {
						unset($page['data']['items'][$itemid]);
					}
				}
			}
			unset($item);
			pdo_update('tiny_wmall_diypage', array('data' => base64_encode(json_encode($page['data']))), array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
	} else {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'waimai_goods') {
					$item['data'] = get_diypage_waimai_goods($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'waimai_stores') {
					$item['data'] = get_diypage_waimai_store($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'notice') {
					$item['data'] = get_diypage_notice($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				}
			}
			unset($item);
		}
	}
	return $page;
}

function get_diypage_waimai_goods($item, $mobile = false) {
	global $_W;
	if($item['params']['goodsdata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$goodsids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['goods_id'])) {
					$goodsids[] = $data['goods_id'];
				}
			}
			if(!empty($goodsids)) {
				$item['data'] = array();
				$goodsids_str = implode(',', $goodsids);
				$goods = pdo_fetchall('select a.*, b.title as store_title from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') .
					" as b on a.sid = b.id where a.uniacid = :uniacid and a.status = 1 and a.id in ({$goodsids_str}) order by a.displayorder desc", array(':uniacid' => $_W['uniacid']));
				if(!empty($goods)) {
					foreach($goodsids as $goodsid) {
						foreach($goods as $good) {
							if($good['id'] == $goodsid) {
								$childid = rand(1000000000, 9999999999);
								$childid = "C{$childid}";
								$item['data'][$childid] = array(
									'goods_id' => $good['id'],
									'sid' => $good['sid'],
									'store_title' => $good['store_title'],
									'thumb' => $good['thumb'],
									'title' => $good['title'],
									'price' => $good['price'],
									'old_price' => $good['old_price'],
									'sailed' => $good['sailed'],
									'total' => ($good['total'] != -1 ? $good['total'] : '无限'),
									'discount' => ($good['old_price'] == 0 ? 0 : (round(($good['price'] / $good['old_price']) * 10, 1))),
									'comment_good_percent' => ($good['comment_total'] == 0 ? 0 : (round(($good['comment_good'] / $good['comment_total']) * 100, 2) . "%")),
								);
							}
						}
					}
				}
			}
		}
	} elseif($item['params']['goodsdata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		//在手机端获取数据
		$item['data'] = array();
		$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1';
		$params = array(
			':uniacid' => $_W['uniacid'],
			':agentid' => $_W['agentid'],
		);
		$limit = intval($item['params']['goodsnum']);
		$limit = $limit ? $limit : 20;
		$goods = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id ' . $condition  . " order by a.mall_displayorder desc limit {$limit}", $params);
		if(!empty($goods)) {
			$stores = pdo_fetchall('select distinct(a.sid),b.title as store_title,b.is_rest from ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'sid');
			foreach($goods as &$good) {
				$childid = rand(1000000000, 9999999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'goods_id' => $good['id'],
					'sid' => $good['sid'],
					'store_title' => $stores[$good['sid']]['store_title'],
					'thumb' => $good['thumb'],
					'title' => $good['title'],
					'price' => $good['discount_price'],
					'old_price' => $good['old_price'],
					'sailed' => $good['sailed'],
					'total' => ($good['discount_available_total'] != -1 ? $good['discount_available_total'] : '无限'),
					'discount' => ($good['old_price'] == 0 ? 0 : (round(($good['discount_price'] / $good['old_price']) * 10, 1))),
					'comment_good_percent' => ($good['comment_total'] == 0 ? 0 : (round(($good['comment_good'] / $good['comment_total']) * 100, 2) . "%")),
				);
			}
		}
	}
	return $item['data'];
}

function get_diypage_waimai_store($item, $mobile = false) {
	global $_W, $_GPC;
	if($item['params']['storedata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$storeids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['store_id'])) {
					$storeids[] = $data['store_id'];
				}
			}
			if(!empty($storeids)) {
				$item['data'] = array();
				$storeids_str = implode(',', $storeids);
				$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,label from ' . tablename('tiny_wmall_store') . "where uniacid = :uniacid and id in ({$storeids_str})", array(':uniacid' => $_W['uniacid']));
			}
		}
	} elseif($item['params']['storedata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		$limit = intval($item['params']['goodsnum']);
		$limit = $limit ? $limit : 20;
		$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,label from ' . tablename('tiny_wmall_store') . "where uniacid = :uniacid and is_recommend = 1 order by displayorder desc limit {$limit}", array(':uniacid' => $_W['uniacid']));
	}
	if(!empty($stores)) {
		$store_label = category_store_label();
		$item['data'] = array();
		foreach($stores as &$row) {
			$row['store_id'] = $row['id'];
			if($row['label'] > 0) {
				$row['label_color'] = $store_label[$row['label']]['color'];
				$row['label_cn'] = $store_label[$row['label']]['title'];
			}
			$row['logo'] = tomedia($row['logo']);
			$row['price'] = store_order_condition($row['id']);
			$row['send_price'] = $row['price']['send_price'];
			$row['delivery_price'] = $row['price']['delivery_price'];
			$row['score_cn'] = round($row['score'] / 5, 2) * 100;
			$row['hot_goods'] = array();
			$hot_goods = pdo_fetchall('select id,title,price,old_price,thumb from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			if(!empty($hot_goods)) {
				foreach($hot_goods as &$goods) {
					if($goods['old_price'] != 0) {
						$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
					} else {
						$goods['discount'] = 0;
					}
					$childid = rand(1000000000, 9999999999);
					$childid = "C{$childid}";
					$row['hot_goods'][$childid] = $goods;
				}
				unset($hot_goods);
			}
			$row['activity'] = array();
			$activitys = store_fetch_activity($row['id']);
			if(!empty($activitys['items'])) {
				foreach($activitys['items'] as $avtivity_item) {
					if(empty($avtivity_item['title'])) {
						continue;
					}
					$childid = rand(1000000000, 9999999999);
					$childid = "C{$childid}";
					$row['activity']['items'][$childid] = array(
						'type' => $avtivity_item['type'],
						'title' => $avtivity_item['title'],
					);
				}
				$row['activity']['num'] = $activitys['num'];
				unset($activitys);
			}
			$childid = rand(1000000000, 9999999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = $row;
			unset($row);
		}
	}
	return $item['data'];
}

function get_diypage_share($page) {
	global $_W;
	$share = array(
		'title' => $page['data']['page']['title'],
		'imgUrl' => tomedia($page['data']['page']['thumb']),
		'desc' => $page['data']['page']['desc'],
		'link' => imurl('diypage/diy/index', array('id' => $page['id']), true),
	);
	return $share;
}

function get_diypage_notice($item, $mobile = false){
	global $_W;
	if($item['params']['noticedata'] == 0) {
		$noticenum = $item['params']['noticenum'];
		$notice = pdo_fetchall('select id, title, displayorder, link, status from' .tablename('tiny_wmall_notice'). 'where status = 1 and uniacid = :uniacid and type = :type order by displayorder desc limit '.$noticenum, array(':uniacid' => $_W['uniacid'], ':type' => 'member'));
		$item['data'] = array();
		if (!empty($notice)) {
			foreach ($notice as &$data) {
				$childid = rand(1000000000, 9999999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'id' => $data['id'],
					'title' => $data['title'],
					'linkurl' => $data['link'],
				);
			}
		}
	}
	return $item['data'];
}