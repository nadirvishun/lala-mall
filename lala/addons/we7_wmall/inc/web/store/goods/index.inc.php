<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'post') {
	$_W['page']['title'] = '商品编辑';
	load()->func('tpl');
	
	$category = pdo_fetchall('SELECT title, id FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE uniacid = :aid AND sid = :sid ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	$id = intval($_GPC['id']);
	if($id) {
		$item = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
		if(empty($item)) {
			imessage('商品不存在或已删除', iurl('store/goods/index/list'), 'info');
		}
		if($item['is_options']) {
			$item['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_options') . ' WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':goods_id' => $id));
		}
		$item['attrs'] = iunserializer($item['attrs']);
		if(!empty($item['attrs'])) {
			foreach($item['attrs'] as &$val) {
				$val['label'] = implode(',', $val['label']);
			}
		}
		$item['slides'] = iunserializer($item['slides']);
	} else {
		$item['total'] = -1;
		$item['unitname'] = '份';
	}

	$store_config = $_W['we7_wmall']['config']['store']['settle'];
	if($_W['ispost']) {
		$data = array(
			'sid' => $sid,
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'number' => trim($_GPC['number']),
			'price' => trim($_GPC['price']),
			'old_price' =>trim($_GPC['old_price']),
			'unitname' => trim($_GPC['unitname']),
			'total' => intval($_GPC['total']),
			'total_warning' => intval($_GPC['total_warning']),
			'total_update_type' => intval($_GPC['total_update_type']),
			'sailed' => intval($_GPC['sailed']),
			'status' => intval($_GPC['status']),
			'cid' => intval($_GPC['cid']),
			'box_price' => floatval($_GPC['box_price']),
			'thumb' => trim($_GPC['thumb']),
			'label' => trim($_GPC['label']),
			'displayorder' => intval($_GPC['displayorder']),
			'content' => trim($_GPC['content']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'is_options' => intval($_GPC['is_options']),
			'is_hot' => intval($_GPC['is_hot']),
			'print_label' => intval($_GPC['print_label']),
		);
		$data['slides'] = array();
		if(!empty($_GPC['slides'])) {
			foreach($_GPC['slides'] as $slides) {
				if(empty($slides)) continue;
				$data['slides'][] = $slides;
			}
		}
		$data['slides'] = iserializer($data['slides']);
		if(!$store_config['custom_goods_sailed_status']) {
			unset($data['sailed']);
		}
		if($data['is_options'] == 1) {
			$options = array();
			foreach($_GPC['options']['name'] as $key => $val) {
				$val = trim($val);
				$price = trim($_GPC['options']['price'][$key]);
				if(empty($val) || empty($price)) {
					continue;
				}
				$options[] = array(
					'id' => intval($_GPC['options']['id'][$key]),
					'name' => $val,
					'price' => $price,
					'total' => intval($_GPC['options']['total'][$key]),
					'total_warning' => intval($_GPC['options']['total_warning'][$key]),
					'displayorder' => intval($_GPC['options']['displayorder'][$key]),
				);
			}
			if(empty($options)) {
				imessage(error(-1, '没有设置有效的规格项'), '', 'ajax');
			}
		}
		$data['attrs'] = array();
		if(!empty($_GPC['attrs'])) {
			foreach($_GPC['attrs']['name'] as $key => $row) {
				$row = trim($row);
				if(empty($row)) {
					continue;
				}
				$labels = $_GPC['attrs']['label'][$key];
				$labels = array_filter(explode(',', str_replace('，', ',', $labels)), trim);
				if(empty($labels)) {
					continue;
				}
				$data['attrs'][] = array(
					'name' => $row,
					'label' => $labels
				);
			}
		}
		$data['attrs'] = iserializer($data['attrs']);
		if($id) {
			pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_goods', $data);
			$id = pdo_insertid();
		}
		$ids = array(0);
		if(!empty($options)) {
			foreach($options as $val) {
				$option_id = $val['id'];
				if($option_id > 0) {
					pdo_update('tiny_wmall_goods_options', $val, array('uniacid' => $_W['uniacid'], 'id' => $option_id, 'goods_id' => $id));
				} else {
					$val['uniacid'] = $_W['uniacid'];
					$val['sid'] = $sid;
					$val['goods_id'] = $id;
					pdo_insert('tiny_wmall_goods_options', $val);
					$option_id = pdo_insertid();
				}
				$ids[] = $option_id;
			}
		}
		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_goods_options') . " WHERE uniacid = :aid AND goods_id = :goods_id and id not in ({$ids})", array(':aid' => $_W['uniacid'], ':goods_id' => $id));
		imessage(error(0, '编辑商品成功'), iurl('store/goods/index/list'), 'ajax');
	}
	$print_labels = pdo_fetchall('select * from ' . tablename('tiny_wmall_printer_label') . ' where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
}

if($ta == 'list') {
	$_W['page']['title'] = '商品列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'price' => floatval($_GPC['prices'][$k]),
					'box_price' => floatval($_GPC['box_prices'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
					'total' => intval($_GPC['totals'][$k]),
				);
				pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '修改成功'), iurl('store/goods/index/list'), 'ajax');
	}

	$condition = ' where uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	if(!empty($_GPC['keyword'])) {
		$condition .= " AND (title LIKE '%{$_GPC['keyword']}%' OR number LIKE '%{$_GPC['keyword']}%')";
	}
	if(!empty($_GPC['cid'])) {
		$condition .= " AND cid = :cid";
		$params[':cid'] = intval($_GPC['cid']);
	}

	$order_by_type = trim($_GPC['order_by_type'])? trim($_GPC['order_by_type']): 'displayorder';
	$order_by = " ORDER BY {$order_by_type} DESC";
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_goods') . $condition, $params);
	if($order_by_type == 'total') {
		$lists = pdo_fetchall('SELECT *, CASE total WHEN -1 THEN 10000000 ELSE total END AS order_by_total FROM ' . tablename('tiny_wmall_goods') . "{$condition} ORDER BY order_by_total ASC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	} else {
		$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . "{$condition}{$order_by} LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	}
	$pager = pagination($total, $pindex, $psize);
	$category = pdo_fetchall('SELECT title, id FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE uniacid = :aid AND sid = :sid', array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
}

if($ta == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$state = pdo_update('tiny_wmall_goods', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($state === false) {
		imessage(error(-1, '操作失败'), '', 'ajax');
	}
	imessage(error(0, '操作成功'), '', 'ajax');
}

if($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$id = intval($id);
		if($id > 0) {
			pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
			pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
			pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
		}
	}
	imessage(error(0, '删除菜品成功'), '', 'ajax');
}

if($ta == 'export') {
	$_W['page']['title'] = '批量导入商品';
	if($_W['ispost']) {
		$file = upload_file($_FILES['file'], 'excel');
		if(is_error($file)) {
			imessage(error(-1, $file['message']), '', 'ajax');
		}
		$data = read_excel($file);
		if(is_error($data)) {
			imessage(error(-1, $data['message']), '', 'ajax');
		}
		unset($data[1]);
		if(empty($data)) {
			imessage(error(-1, '没有要导入的数据'), '', 'ajax');
		}
		foreach($data as $da) {
			if(empty($da['0']) || empty($da['1'])) {
				continue;
			}
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => trim($da[0]),
				'cid' => intval(pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_goods_category') . ' where uniacid = :uniacid and sid = :sid and title = :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => $da[1]))),
				'unitname' => trim($da[2]),
				'price' => trim($da[3]),
				'box_price' => trim($da[4]),
				'label' => trim($da[5]),
				'total' => intval($da[6]),
				'sailed' => trim($da[7]),
				'thumb' => trim($da[8]),
				'displayorder' => intval($da[9]),
				'description' => trim($da[10]),
			);

			if(!empty($da[12])) {
				$attrs = str_replace('，', ',', $da[12]);
				$attrs = explode(',', $attrs);
				$new_attrs = array();
				if(!empty($attrs)) {
					foreach($attrs as $attr) {
						$attr = array_filter(explode('|', $attr));
						$name = $attr[0];
						array_shift($attr);
						if(empty($name) || empty($attr)) {
							continue;
						}
						$new_attrs[] = array(
							'name' => $name,
							'label' => $attr
						);
					}
				}
				$insert['attrs'] = iserializer($new_attrs);
			}

			pdo_insert('tiny_wmall_goods', $insert);
			$goods_id = pdo_insertid();

			if(!empty($da[11])) {
				$options = str_replace('，', ',', $da[11]);
				$options = explode(',', $options);
				if(!empty($options)) {
					foreach($options as $option) {
						$option = explode('|', $option);
						if(count($option) == 4) {
							$insert = array(
								'uniacid' => $_W['uniacid'],
								'sid' => $sid,
								'goods_id' => $goods_id,
								'name' => trim($option[0]),
								'price' => trim($option[1]),
								'total' => intval($option[2]),
								'displayorder' => intval($option[3]),
							);
							pdo_insert('tiny_wmall_goods_options', $insert);
							$i++;
						}
					}
					if($i > 0) {
						pdo_update('tiny_wmall_goods', array('is_options' => 1), array('id' => $goods_id));
					}
				}
			}
		}
		imessage(error(0, '导入商品成功'), iurl('store/goods/index/list'), 'ajax');
	}
}

if($ta == 'copy') {
	$id = intval($_GPC['id']);
	$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($goods)) {
		imessage(error(-1, '商品不存在或已删除'), '', 'ajax');
	}
	if($goods['is_options']) {
		$options = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	}
	unset($goods['id']);
	$goods['title'] = $goods['title'] . '-复制';
	pdo_insert('tiny_wmall_goods', $goods);
	$goods_id = pdo_insertid();
	if(!empty($options) && $goods_id) {
		foreach($options as $option) {
			unset($option['id']);
			$option['goods_id'] = $goods_id;
			pdo_insert('tiny_wmall_goods_options', $option);
		}
	}
	imessage(error(0, '复制商品成功, 现在进入编辑页'), iurl('store/goods/index/post', array('id' => $goods_id)), 'ajax');
}


if($ta == 'eleme_category') {
	$_W['page']['title'] = '从饿了么导入';
	if($_W['ispost']) {
		mload()->model('plugin');
		$_W['_plugin'] = array('name' => 'eleme');
		pload()->classs('product');
		$product = new product($sid);
		$results = $product->getShopCategoriesWithChildren();
		if(!empty($results)) {
			$insert = 0;
			$update = 0;
			foreach($results as $result) {
				$category = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'elemeId' => $result['id']));
				if(empty($category)) {
					$data = array(
						'uniacid' => $_W['uniacid'],
						'sid' => $sid,
						'title' => $result['name'],
						'status' => $result['isValid'],
						'elemeId' => $result['id'],
					);
					pdo_insert('tiny_wmall_goods_category', $data);
					$insert++;
				} else {
					$data = array(
						'title' => $result['name'],
						'status' => $result['isValid'],
					);
					pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'elemeId' => $category['elemeId']));
					$update++;
				}
			}
			cache_write("we7wmall:eleme:{$_W['uniacid']}:{$sid}", $results);
			imessage(error(0, "导入分类成功,本次操作导入{$insert}条数据,更新{$update}条数据"), iurl('store/goods/index/eleme'), 'ajax');
		} else {
			imessage(error(-1, "饿了么暂无分类"), "", 'ajax');
		}
	}
}

if($ta == 'eleme') {
	$_W['page']['title'] = '从饿了么导入';
	mload()->model('plugin');
	$_W['_plugin'] = array('name' => 'eleme');
	pload()->classs('product');
	$product = new product($sid);
	$category = cache_read("we7wmall:eleme:{$_W['uniacid']}:{$sid}");
	if($_W['ispost']) {
		$categoryId = $_GPC['__input']['category']['id'];
		$classId = pdo_fetch('select id from' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid and elemeId = :elemeId', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':elemeId' => $categoryId));
		$goods = $product->getItemsByCategoryId($categoryId);
		foreach($goods as $good) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'cid' => $classId['id'],
				'title' => $good['name'],
				'unitname' => $good['unit'],
				'sailed' => $good['recentPopularity'],
				'thumb' => $good['imageUrl'],
				'status' => $good['isValid'],
				'price' => $good['specs'][0]['price'],
				'total' => $good['specs'][0]['stock'],
				'elemeId' => $good['id'],
			);
			//商品属性
			if(!empty($good['attributes'])) {
				foreach($good['attributes'] as $attr) {
					$data['attrs'][] = array(
						'name' => $attr['name'],
						'label' => $attr['details'],
					);
				}
				$data['attrs'] = iserializer($data['attrs']);
			}
			$commodity = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'elemeId' => $good['id']));
			pdo_delete('tiny_wmall_goods_options', array('sid' => $sid, 'goods_id' => $commodity['id']));
			if(empty($commodity)) {
				pdo_insert('tiny_wmall_goods', $data);
				$goods_id = pdo_insertid();
			} else {
				pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $commodity['id']));
				$goods_id = $commodity['id'];
			}
			//商品规格
			if(!empty($good['specs'])) {
				if(count($good['specs']) != 1) {
					foreach($good['specs'] as $option) {
						$options = array(
							'uniacid' => $_W['uniacid'],
							'sid' => $sid,
							'goods_id' => $goods_id,
							'name' => $option['name'],
							'price' => $option['price'],
							'total' => $option['stock'],
						);
						pdo_insert('tiny_wmall_goods_options', $options);
					}
					pdo_update('tiny_wmall_goods', array('is_options' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $goods_id));
				}
			}

		}
		$key = array_search($categoryId, $category);
		unset($category[$key]);
		$category = array_values($category);
		cache_write("we7wmall:eleme:{$_W['uniacid']}:{$sid}", $category);
		imessage(error(0, $category), '', 'ajax');
	}
}

if($ta == 'meituan_category') {
	$_W['page']['title'] = '从美团导入';
	if($_W['ispost']) {
		mload()->model('plugin');
		$_W['_plugin'] = array('name' => 'meituan');
		pload()->classs('product');
		$product = new product($sid);
		$results = $product->queryCatList();
		if(!empty($results)) {
			$insert = 0;
			$update = 0;
			foreach($results as $result) {
				$category = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $result['name']));
				if(empty($category)) {
					$data = array(
						'uniacid' => $_W['uniacid'],
						'sid' => $sid,
						'title' => $result['name'],
						'status' => 1,
					);
					pdo_insert('tiny_wmall_goods_category', $data);
					$insert++;
				} else {
					$data = array(
						'title' => $result['name'],
					);
					pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $category['id'], 'sid' => $sid));
					$update++;
				}
			}
			$basic = $product->queryBaseListByEPoiId($_W['store']['meituanShopId']);
			if(!empty($basic)) {
				$goods = array();
				foreach($basic as $item) {
					$categoryId = pdo_fetch('select id from' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid and title = :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => $item['categoryName']));
					$basicGood = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $categoryId['id'], 'meituanId' => $item['dishId']));
					if(!empty($categoryId)) {
						$record = array(
							'uniacid' => $_W['uniacid'],
							'sid' => $sid,
							'cid' => $categoryId['id'],
							'title' => $item['dishName'],
							'openplateformCode' => $item['eDishCode'],
							'meituanId' => $item['dishId'],
						);
						if(empty($basicGood)) {
							pdo_insert('tiny_wmall_goods', $record);
						} else {
							pdo_update('tiny_wmall_goods', $record, array('uniacid' => $_W['uniacid'], 'id' => $basicGood['id'], 'sid' => $sid));
						}
					}
				}
			}
			$goods = pdo_fetchall('select * from' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and meituanId > 0', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
			cache_write("we7wmall:meituan:{$_W['uniacid']}:{$sid}", $goods);
			imessage(error(0, "导入分类成功,本次操作导入{$insert}条数据,更新{$update}条数据"), iurl('store/goods/index/meituan'), 'ajax');
		} else {
			imessage(error(-1, "美团暂无分类"), "", 'ajax');
		}
	}
}

if($ta == 'meituan') {
	$_W['page']['title'] = '从美团导入';
	mload()->model('plugin');
	$_W['_plugin'] = array('name' => 'meituan');
	pload()->classs('product');
	$product = new product($sid);
	$goods = cache_read("we7wmall:meituan:{$_W['uniacid']}:{$sid}");
	if($_W['ispost']) {
		$good_id = $_GPC['__input']['good']['id'];
		$good = $product->queryListByEdishCodes($good_id, $_W['store']['meituanShopId']);
		$data = array(
			'description' => $good['description'],
			'title' => $good['dishName'],
			'box_price' => $good['boxPrice'],
			'unitname' => $good['unit'],
			'thumb' => $good['picture'],
			'price' => $good['price'],
		);
		if(!empty($good['attrs'])) {
			foreach($good['attrs'] as $attr) {
				$data['attrs'][] = array(
					'name' => $attr['propertyName'],
					'label' => $attr['values'],
				);
			}
			$data['attrs'] = iserializer($data['attrs']);
		}
		pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $good_id));
		pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $good_id));
		if(count($good['skus']) != 1) {
			foreach($good['skus'] as $v) {
				$options = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $sid,
					'goods_id' => $good_id,
					'name' => $v['spec'],
					'total' => $v['stock'],
					'price' => $v['price'],
				);
				pdo_insert('tiny_wmall_goods_options', $options);
			}
			pdo_update('tiny_wmall_goods', array('is_options' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $good_id));
		}
		$key = array_search($good_id, $goods);
		unset($goods[$key]);
		$goods = array_values($goods);
		cache_write("we7wmall:meituan:{$_W['uniacid']}:{$sid}", $goods);
		imessage(error(0, $goods), '', 'ajax');
	}
}
include itemplate('store/goods/index');