<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '商户账户';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
	if(!empty($stores)) {
		$stores_ids = implode(',', array_keys($stores));
		pdo_query('delete from ' . tablename('tiny_wmall_store_account') . " where uniacid = :uniacid and sid not in ({$stores_ids})", array(':uniacid' => $_W['uniacid']));
	}

	$condition = ' as a left join ' . tablename('tiny_wmall_store_account') . ' as b on a.id = b.sid where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$sid = trim($_GPC['sid']);
	if(!empty($sid)) {
		$condition .= " and b.sid = :sid";
		$params[':sid'] = $sid;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store') . $condition, $params);
	$accounts = pdo_fetchall('SELECT a.*, b.sid, b.amount, b.fee_limit, b.fee_max, b.fee_min, b.fee_rate, b.id as bid FROM ' . tablename('tiny_wmall_store') . $condition . ' ORDER BY b.id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($accounts)) {
		foreach($accounts as &$row) {
			if($row['delivery_fee_mode'] == 2) {
				$row['delivery_price'] = iunserializer($row['delivery_price']);
			}
			if(empty($row['bid'])) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $row['id'],
					'amount' => 0,
				);
				pdo_insert('tiny_wmall_store_account', $data);
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$delivery_modes = store_delivery_modes();
}

if($op == 'turncate') {
	if(!$_W['isajax']) {
		return false;
	}
	if(empty($_GPC['ids'])) {
		imessage(error(-1, '请选择要操作的账户'), '', 'ajax');
	}
	$remark = trim($_GPC['remark']);
	foreach($_GPC['ids'] as $sid) {
		$sid = intval($sid);
		if(!$sid) continue;
		$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		if(empty($account) || empty($account['amount']) || $account['amount'] == 0) {
			continue;
		}
		store_update_account($sid, -$account['amount'], 3, '', $remark);
	}
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'changes') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id), array('logo', 'title', 'telephone'));
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $id), array('amount'));
	if($_W['ispost']) {
		$change_type = intval($_GPC['change_type']);
		$amount = floatval($_GPC['amount']);
		$remark= trim($_GPC['remark']);
		$fee =  $amount - $account['amount'] ;
		if($change_type == 1) {
			$fee = '+' . $amount;
			$amount = $account['amount'] + $amount;
		} elseif($change_type == 2) {
			$fee = '-' . $amount;
			$amount = $account['amount'] - $amount;
			if($amount < 0) {
				$amount = 0;
				$fee = '-' . $account['amount'];
			}
		}
		pdo_update('tiny_wmall_store_account', array('amount' => $amount), array('uniacid' => $_W['uniacid'], 'sid' => $id));
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $id,
			'trade_type' => 3,
			'fee' => $fee,
			'amount' => $amount,
			'addtime' => TIMESTAMP,
			'remark' => $remark
		);
		pdo_insert('tiny_wmall_store_current_log', $insert);
		imessage(error(0, '更改账余额成功'), referer(),'ajax');
	}
	include itemplate('merchant/accountOp');
	die;
}

if($op == 'set') {
	$_W['page']['title'] = '账户设置';
	$sid = intval($_GPC['id']);
	$store = store_fetch($sid);
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$item = $store;
	$item['isChange'] = 1;
	$account = store_account($sid);
	if($_W['ispost']) {
		$fee_takeout = $account['fee_takeout'];
		$takeout_GPC = $_GPC['fee_takeout'];
		$fee_takeout['type'] = intval($takeout_GPC['type']) ? intval($takeout_GPC['type']) : 1;
		if($fee_takeout['type'] == 2) {
			$fee_takeout['fee'] = floatval($takeout_GPC['fee']);
		} else {
			$fee_takeout['fee_rate'] = floatval($takeout_GPC['fee_rate']);
			$items_yes = array_filter($takeout_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_takeout['items_yes'] = $items_yes;
			$items_no = array_filter($takeout_GPC['items_no'], trim);
			$fee_takeout['items_no'] = $items_no;
		}

		$fee_selfDelivery = $account['fee_selfDelivery'];
		$selfDelivery_GPC = $_GPC['fee_selfDelivery'];
		$fee_selfDelivery['type'] = intval($selfDelivery_GPC['type']) ? intval($selfDelivery_GPC['type']) : 1;
		if($fee_selfDelivery['type'] == 2) {
			$fee_selfDelivery['fee'] = floatval($selfDelivery_GPC['fee']);
		} else {
			$fee_selfDelivery['fee_rate'] = floatval($selfDelivery_GPC['fee_rate']);
			$items_yes = array_filter($selfDelivery_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_selfDelivery['items_yes'] = $items_yes;
			$items_no = array_filter($selfDelivery_GPC['items_no'], trim);
			$fee_selfDelivery['items_no'] = $items_no;
		}

		$fee_instore = $account['fee_instore'];
		$instore_GPC = $_GPC['fee_instore'];
		$fee_instore['type'] = intval($instore_GPC['type']) ? intval($instore_GPC['type']) : 1;
		if($fee_instore['type'] == 2) {
			$fee_instore['fee'] = floatval($instore_GPC['fee']);
		} else {
			$fee_instore['fee_rate'] = floatval($instore_GPC['fee_rate']);
			$items_yes = array_filter($instore_GPC['items_yes'], trim);
			if(empty($items_yes)) {
				imessage(error(-1, '至少选择一项抽佣项目'), '', 'ajax');
			}
			$fee_instore['items_yes'] = $items_yes;
			$items_no = array_filter($instore_GPC['items_no'], trim);
			$fee_instore['items_no'] = $items_no;
		}

		$fee_paybill = $account['fee_paybill'];
		$paybill_GPC = $_GPC['fee_paybill'];
		$fee_paybill['type'] = intval($paybill_GPC['type']) ? intval($paybill_GPC['type']) : 1;
		if($fee_paybill['type'] == 2) {
			$fee_paybill['fee'] = floatval($paybill_GPC['fee']);
		} else {
			$fee_paybill['fee_rate'] = floatval($paybill_GPC['fee_rate']);
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'fee_takeout' => iserializer($fee_takeout),
			'fee_selfDelivery' => iserializer($fee_selfDelivery),
			'fee_instore' => iserializer($fee_instore),
			'fee_paybill' => iserializer($fee_paybill),
			'fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'fee_rate' => trim($_GPC['get_cash_fee_rate']) ? trim($_GPC['get_cash_fee_rate']) : 0,
			'fee_min' => intval($_GPC['get_cash_fee_min']),
			'fee_max' => intval($_GPC['get_cash_fee_max']),
		);
		if($_GPC['fee_eleme']['fee_type'] == 1) {
			$data['fee_eleme'] = iserializer(array(
				'fee_type' => 1,
				'fee_rate' => floatval($_GPC['fee_eleme']['fee_rate']),
			));
		} else {
			$data['fee_eleme'] = iserializer(array(
				'fee_type' => 2,
				'fee' => floatval($_GPC['fee_eleme']['fee']),
			));
		}
		if($_GPC['fee_meituan']['fee_type'] == 1) {
			$data['fee_meituan'] = iserializer(array(
				'fee_type' => 1,
				'fee_rate' => floatval($_GPC['fee_meituan']['fee_rate']),
			));
		} else {
			$data['fee_meituan'] = iserializer(array(
				'fee_type' => 2,
				'fee' => floatval($_GPC['fee_meituan']['fee']),
			));
		}
		if(empty($account)) {
			$data['amount'] = 0.00;
			pdo_insert('tiny_wmall_store_account', $data);
		} else {
			pdo_update('tiny_wmall_store_account', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		}
		$update = array(
			'delivery_mode' => intval($_GPC['delivery_mode']),
			'delivery_fee_mode' => intval($_GPC['delivery_fee_mode']),
			'auto_get_address' => intval($_GPC['auto_get_address']),
			'send_price' => intval($_GPC['send_price_1']),
			'delivery_free_price' => intval($_GPC['delivery_free_price_1']),
			'delivery_time' => intval($_GPC['delivery_time']),
			'serve_radius' => floatval($_GPC['serve_radius']),
			'not_in_serve_radius' => intval($_GPC['not_in_serve_radius']),
			'delivery_extra' => iserializer(array(
				'store_bear_deliveryprice' => floatval($_GPC['store_bear_deliveryprice']),
				'delivery_free_bear' => trim($_GPC['delivery_free_bear']),
			)),
			'eleme_status' => intval($_GPC['eleme_status']),
			'meituan_status' => intval($_GPC['meituan_status']),
		);

		if($update['delivery_fee_mode'] == 1) {
			$update['delivery_price'] = trim($_GPC['delivery_price']);
			if(!$update['not_in_serve_radius']) {
				$update['auto_get_address'] = 1;
				if(empty($update['serve_radius'])) {
					imessage(error(-1, '您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径'), '', 'ajax');
				}
			}
		} elseif($update['delivery_fee_mode'] == 2) {
			$update['auto_get_address'] = 1;
			$update['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
			$update['send_price'] = intval($_GPC['send_price_2']);
			$update['delivery_free_price'] = intval($_GPC['delivery_free_price_2']);

			$update['delivery_price'] = iserializer(array(
				'start_fee' => trim($_GPC['start_fee']),
				'start_km' => trim($_GPC['start_km']),
				'pre_km_fee' => trim($_GPC['pre_km_fee']),
			));
		} elseif($update['delivery_fee_mode'] == 3) {
			$update['auto_get_address'] = 1;
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
					'fee' => trim($_GPC['times']['fee'][$key])
				);
			}
			$update['delivery_times'] = iserializer($times);
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
		$update['delivery_areas'] = iserializer($_GPC['areas']);

		$eleme_delivery = array(
			'delivery_mode' => intval($_GPC['eleme']['delivery_mode']),
			'delivery_fee_mode' => intval($_GPC['eleme']['delivery_fee_mode']),
			'delivery_price' => floatval($_GPC['eleme']['delivery_price']),
		);
		if($eleme_delivery['delivery_fee_mode'] == 2) {
			$eleme_delivery['delivery_price'] = array(
				'start_fee' => trim($_GPC['eleme']['start_fee']),
				'start_km' => trim($_GPC['eleme']['start_km']),
				'pre_km_fee' => trim($_GPC['eleme']['pre_km_fee']),
			);
		}
		store_set_openplateform($sid, 'eleme.delivery', $eleme_delivery);

		$meituan_delivery = array(
			'delivery_mode' => intval($_GPC['meituan']['delivery_mode']),
			'delivery_fee_mode' => intval($_GPC['meituan']['delivery_fee_mode']),
			'delivery_price' => floatval($_GPC['meituan']['delivery_price']),
		);
		if($meituan_delivery['delivery_fee_mode'] == 2) {
			$meituan_delivery['delivery_price'] = array(
				'start_fee' => trim($_GPC['meituan']['start_fee']),
				'start_km' => trim($_GPC['meituan']['start_km']),
				'pre_km_fee' => trim($_GPC['meituan']['pre_km_fee']),
			);
		}
		store_set_openplateform($sid, 'meituan.delivery', $meituan_delivery);

		pdo_update('tiny_wmall_store', $update, array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_query('update ' .  tablename('tiny_wmall_store_deliveryer') . ' set delivery_type = :delivery_type where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':delivery_type' => $update['delivery_mode'], ':sid' => $sid));
		store_delivery_times($sid, true);
		imessage(error(0, '设置门店账户成功'), 'refresh', 'ajax');
	}
}
include itemplate('merchant/account');



