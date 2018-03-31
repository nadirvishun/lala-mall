<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

function activity_cron() {
	global $_W;
	//1 正在进行中 2 未开始（待生效） 0 已结束或已撤销（撤销后不能再上架）
	$type_basic = array('discount', 'newMember', 'grant', 'mallNewMember', 'selfDelivery');
	$type_basics = "'" . implode("','", $type_basic) ."'";
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 2 where uniacid = :uniacid and starttime > :time and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 0 where uniacid = :uniacid and (endtime < :time or starttime > :time) and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and starttime < :time and endtime > :time and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));

	//进店领券
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 0 where uniacid = :uniacid and type = 'couponCollect' and status != 0 and ((endtime < :time) or (dosage >= amount))", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 1 where uniacid = :uniacid and type = 'couponCollect' and status != 0 and ((endtime > :time and starttime < :time and dosage < amount))", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 2 where uniacid = :uniacid and type = 'couponCollect' and status != 0 and (starttime > :time and dosage < amount)", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 0 where uniacid = :uniacid and type = 'couponCollect'", array(':uniacid' => $_W['uniacid']));
	$collect_yes = pdo_getall('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponCollect', 'status' => 1), array('activity_id'), 'activity_id');
	if(!empty($collect_yes)) {
		$collect_yes = implode(',', array_keys($collect_yes));
		$data = pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and id in ({$collect_yes})", array(':uniacid' => $_W['uniacid']));
	}
	$collect_ing = pdo_getall('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponCollect', 'status' => 2), array('activity_id'), 'activity_id');
	if(!empty($collect_ing)) {
		$collect_ing = implode(',', array_keys($collect_ing));
		pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 2 where uniacid = :uniacid and id in ({$collect_ing})", array(':uniacid' => $_W['uniacid']));
	}

	//下单返券
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 0 where uniacid = :uniacid and type = 'couponGrant' and status != 0 and ((endtime < :time) or (dosage >= amount))", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 1 where uniacid = :uniacid and type = 'couponGrant' and status != 0 and ((endtime > :time and starttime < :time and dosage < amount))", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 2 where uniacid = :uniacid and type = 'couponGrant' and status != 0 and starttime > :time and dosage < amount", array(':uniacid' => $_W['uniacid'], ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 0 where uniacid = :uniacid and type = 'couponGrant'", array(':uniacid' => $_W['uniacid']));
	$grant_yes = pdo_getall('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponGrant', 'status' => 1), array('activity_id'), 'activity_id');
	if(!empty($grant_yes)) {
		$grant_yes = implode(',', array_keys($grant_yes));
		pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and id in ({$grant_yes})", array(':uniacid' => $_W['uniacid']));
	}
	$grant_ing = pdo_getall('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponGrant', 'status' => 1), array('activity_id'), 'activity_id');
	if(!empty($grant_ing)) {
		$grant_ing = implode(',', array_keys($grant_ing));
		pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and id in ({$grant_ing})", array(':uniacid' => $_W['uniacid']));
	}

	//天天特价
	$params = array(
		':uniacid' => $_W['uniacid'],
		':time' => TIMESTAMP,
		':hour' => date('Hi'),
	);
	$params[':hour'] = ltrim($params[':hour'], '0');
	pdo_query('update ' . tablename('tiny_wmall_activity_bargain') . " set status = 1 where uniacid = :uniacid and starttime < :time and endtime > :time and starthour < :hour and endhour > :hour", $params);
	pdo_query('update ' . tablename('tiny_wmall_activity_bargain') . " set status = 0 where uniacid = :uniacid and (starttime > :time or endtime < :time or starthour > :hour or endhour < :hour)", $params);

	$bargain_yes = pdo_getall('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'sid'), 'id');
	pdo_update('tiny_wmall_activity_bargain_goods', array('status' => 0), array('uniacid' => $_W['uniacid']));
	if(!empty($bargain_yes)) {
		$bargain_id_yes = implode(',', array_keys($bargain_yes));
		pdo_query("update " . tablename('tiny_wmall_activity_bargain_goods') . " set status = 1 where uniacid = :uniacid and (discount_available_total = -1 or discount_available_total > 0) and bargain_id in ({$bargain_id_yes})", array(':uniacid' => $_W['uniacid']));
		$bargain_store_yes = array();
		foreach($bargain_yes as $row) {
			$bargain_store_yes[] = $row['sid'];
		}
	}
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 0 where uniacid = :uniacid and status = 1 and type = :type", array(':uniacid' => $_W['uniacid'], ':type' => 'bargain'));
	if(!empty($bargain_store_yes)) {
		$bargain_store_yes = implode(',', $bargain_store_yes);
		pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and type = :type and sid in ({$bargain_store_yes})", array(':uniacid' => $_W['uniacid'], ':type' => 'bargain'));
	}
	$params = array(
		':uniacid' => $_W['uniacid'],
		':time' => TIMESTAMP,
		':status' => 1,
	);
	$bargains = pdo_fetchall('select id from ' . tablename('tiny_wmall_activity_bargain') . ' where uniacid = :uniacid and status = :status and total_updatetime < :time order by total_updatetime asc limit 3', $params);
	if(!empty($bargains)) {
		$time = strtotime(date('Y-m-d')) + 86400;
		foreach($bargains as $bargain) {
			pdo_query('update' . tablename('tiny_wmall_activity_bargain_goods') . ' set discount_available_total = discount_total where bargain_id = :bargain_id', array(':bargain_id' => $bargain['id']));
			pdo_update('tiny_wmall_activity_bargain', array('total_updatetime' => $time), array('id' => $bargain['id']));
		}
	}
	return true;
}

function activity_store_cron($sid) {
	global $_W;
	$type_basic = array('discount', 'newMember', 'grant', 'mallNewMember', 'selfDelivery');
	$type_basics = "'" . implode("','", $type_basic) ."'";
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 0 where uniacid = :uniacid and sid = :sid and (endtime < :time or starttime > :time) and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 1 where uniacid = :uniacid and sid = :sid and starttime < :time and endtime > :time and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_store_activity') . " set status = 2 where uniacid = :uniacid and sid = :sid and starttime > :time and type in({$type_basics})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));

	//进店领券
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 0 where uniacid = :uniacid and sid = :sid and type = 'couponCollect' and status != 0 and ((endtime < :time) or (dosage >= amount))", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 1 where uniacid = :uniacid and sid = :sid and type = 'couponCollect' and status != 0 and ((endtime > :time and starttime < :time and dosage < amount))", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 2 where uniacid = :uniacid and sid = :sid and type = 'couponCollect' and status != 0 and (starttime > :time and dosage < amount)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	$collect_yes = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponCollect', 'status' => 1, 'sid' => $sid), array('activity_id'));
	$status = !empty($collect_yes) ? 1 : 0;
	pdo_update('tiny_wmall_store_activity', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'couponCollect'));
	$collect_ing = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponCollect', 'status' => 2, 'sid' => $sid), array('activity_id'));
	if(!empty($collect_ing)) {
		pdo_update('tiny_wmall_store_activity', array('status' => 2), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'couponCollect'));
	}

	//下单返券
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 0 where uniacid = :uniacid and sid = :sid and type = 'couponGrant' and status != 0 and ((endtime < :time) or (dosage >= amount))", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 1 where uniacid = :uniacid and sid = :sid and type = 'couponGrant' and status != 0 and ((endtime > :time and starttime < :time and dosage < amount))", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	pdo_query("update " . tablename('tiny_wmall_activity_coupon') . " set status = 2 where uniacid = :uniacid and sid = :sid and type = 'couponGrant' and status != 0 and ((starttime > :time  and dosage < amount))", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':time' => TIMESTAMP));
	$grant_yes = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponGrant', 'status' => 1, 'sid' => $sid), array('activity_id'));
	$status = !empty($grant_yes) ? 1 : 0;
	pdo_update('tiny_wmall_store_activity', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'couponGrant'));
	$grant_ing = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'type' => 'couponGrant', 'status' => 2, 'sid' => $sid), array('activity_id'));
	if(!empty($grant_ing)) {
		pdo_update('tiny_wmall_store_activity', array('status' => 2), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'couponGrant'));
	}

	//天天特价
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':time' => TIMESTAMP,
		':hour' => date('Hi'),
	);
	$params[':hour'] = ltrim($params[':hour'], '0');
	pdo_query('update ' . tablename('tiny_wmall_activity_bargain') . " set status = 1 where uniacid = :uniacid and sid = :sid and starttime < :time and endtime > :time and starthour < :hour and endhour > :hour", $params);
	pdo_query('update ' . tablename('tiny_wmall_activity_bargain') . " set status = 0 where uniacid = :uniacid and sid = :sid and (starttime > :time or endtime < :time or starthour > :hour or endhour < :hour)", $params);
	$bargain_yes = pdo_getall('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1), array('id'), 'id');
	pdo_update('tiny_wmall_activity_bargain_goods', array('status' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	$status = 0;
	if(!empty($bargain_yes)) {
		$bargain_yes = implode(',', array_keys($bargain_yes));
		pdo_query("update " . tablename('tiny_wmall_activity_bargain_goods') . " set status = 1 where uniacid = :uniacid and (discount_available_total = -1 or discount_available_total > 0) and bargain_id in ({$bargain_yes})", array(':uniacid' => $_W['uniacid']));
		$status = 1;
	}
	pdo_update('tiny_wmall_store_activity', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'bargain'));

	$params = array(
		':uniacid' => $_W['uniacid'],
		':time' => TIMESTAMP,
		':status' => 1,
		':sid' => $sid,
	);
	$bargains = pdo_fetchall('select id from ' . tablename('tiny_wmall_activity_bargain') . ' where uniacid = :uniacid and sid = :sid and status = :status and total_updatetime < :time order by total_updatetime asc limit 3', $params);
	if(!empty($bargains)) {
		$time = strtotime(date('Y-m-d')) + 86400;
		foreach($bargains as $bargain) {
			pdo_query('update' . tablename('tiny_wmall_activity_bargain_goods') . ' set discount_available_total = discount_total where bargain_id = :bargain_id', array(':bargain_id' => $bargain['id']));
			pdo_update('tiny_wmall_activity_bargain', array('total_updatetime' => $time), array('id' => $bargain['id']));
		}
	}

	return true;
}

function activity_get($sid, $type) {
	global $_W;
	$activity = pdo_get('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => $type));
	if(!empty($activity)) {
		$activity['data'] = iunserializer($activity['data']);
	}
	return $activity;
}

function activity_set($sid, $params) {
	global $_W;
	if(empty($params['type'])) {
		return error(-1, '活动类型不能为空');
	}
	$activity = pdo_get('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => $params['type']));
	if(empty($activity)) {
		$params['addtime'] = TIMESTAMP;
		pdo_insert('tiny_wmall_store_activity', $params);
		$activity['id'] = pdo_insertid();
	} else {
		pdo_update('tiny_wmall_store_activity', $params, array('id' => $activity['id']));
	}
	activity_cron();
	return $activity['id'];
}

function activity_del($sid, $type = '') {
	global $_W;
	if(empty($type)) {
		pdo_delete('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	} else {
		pdo_delete('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => $type));
	}
	if(in_array($type, array('couponGrant', 'couponCollect'))) {
		pdo_update('tiny_wmall_activity_coupon', array('status' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => $type));
	}
	activity_cron();
	return true;
}

function activity_stat($type = '') {
	global $_W;
	activity_cron();
	$condition = ' where uniacid = :uniacid and status = 1';
	$params = array(':uniacid' => $_W['uniacid']);
	if(!empty($type)) {
		$condition .= ' and type = :type';
		$params['type'] = $type;
	}
	$condition .= ' group by type';
	$stat = pdo_fetchall('select count(*) as num, type from ' . tablename('tiny_wmall_store_activity') . $condition, $params, 'type');
	if(!empty($type)) {
		return intval($stat[$type]);
	}
	return $stat;
}

function activity_bargain_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '进行中',
			'color' => 'color-success',
		),
		'0' => array(
			'css' => 'label label-danger',
			'text' => '活动未开始或已结束',
			'color' => 'color-danger',
		),
	);
	return $data;
}

function activity_bargain_update_goods_total() {
	global $_W;
	$params = array(
		':uniacid' => $_W['uniacid'],
		':time' => TIMESTAMP,
		':status' => 1,
	);
	$bargains = pdo_fetchall('select id from ' . tablename('tiny_wmall_activity_bargain') . ' where uniacid = :uniacid and status = :status and total_updatetime < :time order by total_updatetime asc limit 3', $params);
	if(!empty($bargains)) {
		$time = strtotime(date('Y-m-d')) + 86400;
		foreach($bargains as $bargain) {
			pdo_query('update' . tablename('tiny_wmall_activity_bargain_goods') . ' set discount_available_total = discount_total where bargain_id = :bargain_id', array(':bargain_id' => $bargain['id']));
			pdo_update('tiny_wmall_activity_bargain', array('total_updatetime' => $time), array('id' => $bargain['id']));
		}
	}
	return true;
}

