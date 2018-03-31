<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '热门搜索';
	$stores = store_fetchall_by_condition('hot');

	if (0 < $_W['member']['uid']) {
		mload()->model('member');
		$member = member_fetch();
	}

	include itemplate('home/huntIndex');
}

if ($ta == 'truncate') {
	if (0 < $_W['member']['uid']) {
		pdo_update('tiny_wmall_members', array('search_data' => ''), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}

	exit('success');
}

if ($ta == 'search_data') {
	if (0 < $_W['member']['uid']) {
		mload()->model('member');
		$key = trim($_GPC['key']);
		$member = member_fetch();

		if (!empty($member)) {
			$num = count($member['search_data']);

			if (5 <= $num) {
				array_pop($member['search_data']);
			}

			array_push($member['search_data'], $key);
			$search_data = iserializer(array_unique($member['search_data']));
			pdo_update('tiny_wmall_members', array('search_data' => $search_data), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		}
	}
}

if ($ta == 'search') {
	$_W['page']['title'] = '搜索结果';
	$key = trim($_GPC['key']);
	$sids = array(0);
	$sids_str = 0;
	$stores = array();
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and status = 1 and title like :key', array(':uniacid' => $_W['uniacid'], ':key' => '%' . $key . '%'));

	if (!empty($goods)) {
		$store_goods = array();

		foreach ($goods as $good) {
			$sids[] = $good['sid'];
			$store_goods[$good['sid']][] = $good;
		}

		$sids_str = implode(',', $sids);
		$stores = pdo_fetchall('select id,title,logo,content,business_hours,delivery_fee_mode,delivery_price,delivery_areas,send_price,delivery_time,delivery_mode,forward_mode,forward_url from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 and id in (' . $sids_str . ')', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	}

	$search_stores = pdo_fetchall('select id,title,logo,content,business_hours,delivery_fee_mode,delivery_price,delivery_areas,send_price,delivery_time,delivery_mode,forward_mode,forward_url from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 and id not in (' . $sids_str . ') and title like :key', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':key' => '%' . $key . '%'));
	$stores = array_merge($search_stores, $stores);

	foreach ($stores as &$row) {
		$row['goods'] = $store_goods[$row['id']];
		$row['activity'] = store_fetch_activity($row['id'], array('discount'));
		$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);

		if ($row['delivery_fee_mode'] == 2) {
			$row['delivery_price'] = iunserializer($row['delivery_price']);
			$row['delivery_price'] = $row['delivery_price']['start_fee'];
		}
		else {
			if ($row['delivery_fee_mode'] == 3) {
				$row['delivery_areas'] = iunserializer($row['delivery_areas']);

				if (!is_array($row['delivery_areas'])) {
					$row['delivery_areas'] = array();
				}

				$price = store_order_condition($row);
				$row['delivery_price'] = $price['delivery_price'];
				$row['send_price'] = $price['send_price'];
			}
		}
	}

	$num = count($stores);

	if ($num < 4) {
		$recommend_stores = store_fetchall_by_condition('recommend');
	}

	include itemplate('home/huntResult');
}

?>
