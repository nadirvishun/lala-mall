<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '门店列表';
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'click' => intval($_GPC['click'][$k]),
					'sailed' => intval($_GPC['sailed'][$k]),
				);
				pdo_update('tiny_wmall_store', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage('编辑成功', iurl('merchant/store/list'), 'success');
	}

	$store_label = category_store_label();
	$condition = ' uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= " AND cid LIKE :cid";
		$params[':cid'] = "%|{$cid}|%";
	}
	$label = intval($_GPC['label']);
	if($label > 0) {
		$condition .= " AND label = :label";
		$params[':label'] = $label;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	if(!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if(!empty($lists)) {
		foreach($lists as &$li) {
			$li['cid'] = explode('|', $li['cid']);
			$li['sys_url'] = imurl('wmall/store/goods', array('sid' => $li['id']), true);
			$li['wechat_qrcode'] = (array)iunserializer($li['wechat_qrcode']);
			$li['wechat_url'] = $li['wechat_qrcode']['url'];
		}
	}
	$categorys = store_fetchall_category();
	$store_status = store_status();
	include itemplate('merchant/list');
}

if($op == 'post') {
	$_W['page']['title'] = '添加门店';
	$perm = check_max_store_perm();
	if(empty($perm)) {
		imessage('门店入驻量已超过上限,请联系公众号管理员', '', 'info');
	}

	$config_store = $_W['we7_wmall']['config']['store'];
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'logo' => trim($_GPC['logo']),
			'telephone' => trim($_GPC['telephone']),
			'content' => trim($_GPC['content']),
			'address' =>  trim($_GPC['address']),
			'displayorder' => intval($_GPC['displayorder']),
			'delivery_mode' => $config_store['delivery']['delivery_mode'] ? $config_store['delivery']['delivery_mode'] : 1,
			'delivery_fee_mode' => 1,
			'delivery_price' => $config_store['delivery']['delivery_fee'],
			'business_hours' => iserializer(array()),
			'addtime' => TIMESTAMP,
			'push_token' => random(32),
			'self_audit_comment' => intval($config_store['settle']['self_audit_comment']),
		);
		if(empty($config_store['delivery'])) {
			$config_store['delivery'] = array(
				'delivery_fee_mode' => 1,
				'delivery_price' => 0
			);
		}
		if($config_store['delivery']['delivery_fee_mode'] == 2) {
			$data['delivery_fee_mode'] = 2;
			$data['delivery_price'] = iserializer($data['delivery_price']);
		} else {
			$data['delivery_fee_mode'] = 1;
			$data['delivery_price'] = floatval($data['delivery_price']);
		}
		$delivery_times = get_config_text('takeout_delivery_time');
		$data['delivery_times'] = iserializer($delivery_times);
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
		$data['cid'] = "|{$cids}|";
		pdo_insert('tiny_wmall_store', $data);
		$sid = pdo_insertid();

		//添加门店账户数据
		$config_settle = $config_store['settle'];
		$store_account = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'fee_takeout' => iserializer($config_settle['fee_takeout']),
			'fee_selfDelivery' => iserializer($config_settle['fee_selfDelivery']),
			'fee_instore' => iserializer($config_settle['fee_instore']),
			'fee_paybill' => iserializer($config_settle['fee_paybill']),
			'fee_limit' => $config_settle['get_cash_fee_limit'],
			'fee_rate' => $config_settle['get_cash_fee_rate'],
			'fee_min' => $config_settle['get_cash_fee_min'],
			'fee_max' => $config_settle['get_cash_fee_max'],
		);
		pdo_insert('tiny_wmall_store_account', $store_account);
		imessage(error(0, '添加门店成功'), iurl('store/shop/setting', array('_sid' => $sid)), 'ajax');
	}
	$categorys = store_fetchall_category();
	include itemplate('merchant/post');
}

if($op == 'template') {
	$sid = intval($_GPC['id']);
	$template = trim($_GPC['t']) ? trim($_GPC['t']) : 'index';
	pdo_update('tiny_wmall_store', array('template' => $template), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '设置页面风格成功'), referer(), 'ajax');
}

if($op == 'label') {
	$sid = intval($_GPC['sid']);
	$label = intval($_GPC['label']);
	pdo_update('tiny_wmall_store', array('label' => $label), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '设置商户标签成功'), '', 'ajax');
}

if($op == 'label_del') {
	$sid = intval($_GPC['id']);
	pdo_update('tiny_wmall_store', array('label' => -1), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '删除商户标签成功'), '', 'ajax');
}

if($op == 'is_in_business') {
	$sid = intval($_GPC['id']);
	$is_in_business = intval($_GPC['is_in_business']);
	pdo_update('tiny_wmall_store', array('is_in_business' => $is_in_business), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	store_business_hours_init($sid);
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'status') {
	$sid = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'is_recommend') {
	$sid = intval($_GPC['id']);
	$recommend = intval($_GPC['is_recommend']);
	pdo_update('tiny_wmall_store', array('is_recommend' => $recommend), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'is_stick') {
	$sid = intval($_GPC['id']);
	$is_stick = intval($_GPC['is_stick']);
	pdo_update('tiny_wmall_store', array('is_stick' => $is_stick), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'batch') {
	$type = trim($_GPC['type']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_store', array($type => $value), array('uniacid' => $_W['uniacid'], 'status' => 1));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'copy') {
	set_time_limit(0);
	$sid = intval($_GPC['sid']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid));
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$store['title'] = $store['title'] . "-复制";
	unset($store['id'], $store['wechat_qrcode'], $store['assign_qrcode']);
	pdo_insert('tiny_wmall_store', $store);
	$store_id = pdo_insertid();

	//门店账户
	$config_settle = $_W['we7_wmall']['config']['settle'];
	$store_account = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $store_id,
		'fee_limit' => $config_settle['get_cash_fee_limit'],
		'fee_rate' => $config_settle['get_cash_fee_rate'],
		'fee_min' => $config_settle['get_cash_fee_min'],
		'fee_max' => $config_settle['get_cash_fee_max'],
	);
	pdo_insert('tiny_wmall_store_account', $store_account);

	//复制菜品分类
	$goods_categorys = pdo_getall('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($goods_categorys)) {
		foreach($goods_categorys as $category) {
			$cid = $category['id'];
			unset($category['id']);
			$category['sid'] = $store_id;
			pdo_insert('tiny_wmall_goods_category', $category);
			$category_id = pdo_insertid();
			$goods = pdo_getall('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $cid));
			if(!empty($goods)) {
				foreach($goods as $good) {
					$goods_id = $good['id'];
					unset($good['id']);
					$good['sid'] = $store_id;
					$good['cid'] = $category_id;
					pdo_insert('tiny_wmall_goods', $good);
					$new_goods_id = pdo_insertid();
					if($good['is_options'] == 1) {
						$options = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $goods_id));
						if(!empty($options)) {
							foreach($options as $option) {
								unset($option['id']);
								$option['sid'] = $store_id;
								$option['goods_id'] = $new_goods_id;
								pdo_insert('tiny_wmall_goods_options', $option);
							}
						}
					}
				}
			}
		}
	}

	//复制桌台类型
	$table_categorys = pdo_getall('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($table_categorys)) {
		foreach($table_categorys as $category) {
			$cid = $category['id'];
			unset($category['id']);
			$category['sid'] = $store_id;
			pdo_insert('tiny_wmall_tables_category', $category);
			$category_id = pdo_insertid();
			$tables = pdo_getall('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $cid));
			if(!empty($tables)) {
				foreach($tables as $table) {
					unset($table['id']);
					unset($table['qrcode']);
					$table['sid'] = $store_id;
					$table['cid'] = $category_id;
					pdo_insert('tiny_wmall_tables', $table);
				}
			}
			//复制预定
			$reserves = pdo_getall('tiny_wmall_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'table_cid' => $cid));
			if(!empty($reserves)) {
				foreach($reserves as $reserve) {
					unset($reserve['id']);
					$reserve['sid'] = $store_id;
					$reserve['table_cid'] = $category_id;
					pdo_insert('tiny_wmall_reserve', $reserve);
				}
			}
		}
	}

	//复制排号
	$assigns = pdo_getall('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($assigns)) {
		foreach($assigns as $assign) {
			unset($assign['id']);
			$assign['sid'] = $store_id;
			pdo_insert('tiny_wmall_assign_queue', $assign);
		}
	}
	imessage(error(0, '复制门店成功'), '', 'ajax');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$tables = array(
		'tiny_wmall_activity_bargain',
		'tiny_wmall_activity_bargain_goods',
		'tiny_wmall_activity_coupon',
		'tiny_wmall_activity_coupon_grant_log',
		'tiny_wmall_activity_coupon_record',
		'tiny_wmall_assign_board',
		'tiny_wmall_assign_queue',
		'tiny_wmall_clerk',
		'tiny_wmall_goods',
		'tiny_wmall_goods_category',
		'tiny_wmall_goods_options',
		'tiny_wmall_order_cart',
		'tiny_wmall_order_stat',
		'tiny_wmall_printer',
		'tiny_wmall_printer_label',
		'tiny_wmall_reply',
		'tiny_wmall_report',
		'tiny_wmall_reserve',
		'tiny_wmall_sms_send_log',
		'tiny_wmall_store_account',
		'tiny_wmall_store_activity',
		'tiny_wmall_store_clerk',
		'tiny_wmall_store_current_log',
		'tiny_wmall_store_deliveryer',
		'tiny_wmall_store_favorite',
		'tiny_wmall_store_getcash_log',
		'tiny_wmall_store_members',
		'tiny_wmall_tables',
		'tiny_wmall_tables_category',
		'tiny_wmall_tables_scan',
	);
	foreach($tables as $table) {
		if(pdo_tableexists($table) && pdo_fieldexists($table, 'sid')) {
			pdo_delete($table, array('uniacid' => $_W['uniacid'], 'sid' => $id));
		}
	}
	imessage(error(0, '删除门店成功'), '', 'ajax');
}

if($op == 'lots') {
	//$agents = get_agents();
	if($_W['ispost'] && $_GPC['set'] == 1) {
		$sid = explode(',', $_GPC['sid']);
		if(empty($sid)){
			imessage(error(-1, '请选择需要修改的门店'), '', 'ajax');
		}
/*		$agentid = $_GPC['agentid'];
		if(empty($agentid)) {
			imessage(error(-1, '请选择代理名称'), '', 'ajax');
		}*/
		$data = array(
			'self_audit_comment' => intval($_GPC['self_audit_comment']),
			'auto_handel_order' => intval($_GPC['auto_handel_order']),
			'auto_notice_deliveryer' => intval($_GPC['auto_notice_deliveryer'])
		);
		if(!$data['self_audit_comment']) {
			$data['comment_status'] = 1;
		} else {
			$data['comment_status'] = intval($_GPC['comment_status']);
		}
		foreach($sid as &$row) {
			//update_store_agent($row, $agentid);
			pdo_update('tiny_wmall_store', $data, array('id' => $row, 'uniacid' => $_W['uniacid']));
		}
		imessage(error(0, '批量操作修改成功'), iurl('merchant/store/list'), 'ajax');
	}
	$ids = $_GPC['id'];
	if(empty($ids)) {
		imessage(error(-1, '请选择需要操作的门店'), '', 'ajax');
	}
	$ids = implode(',', $ids);
	include itemplate('merchant/listOp');
}