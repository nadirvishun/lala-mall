<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;

if ($_W['ispost']) {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	if (empty($lat) || empty($lng)) {
		imessage(error(-1, '获取位置失败'), imurl('wmall/home/near'), 'ajax');
	}

	$stores = pdo_fetchall('select id,location_x,location_y,serve_radius from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and status = 1', array(':uniacid' => $_W['uniacid']));

	if (empty($stores)) {
		imessage(error(-1, '还没有门店哦'), referer(), 'ajax');
	}

	$distance = array();
	if (!empty($lat) && !empty($lng)) {
		foreach ($stores as $key => &$row) {
			$row['distance'] = distanceBetween($row['location_y'], $row['location_x'], $lng, $lat);
			$row['distance'] = round($row['distance'] / 1000, 2);
			if ((0 < $row['serve_radius']) && ($row['serve_radius'] < $row['distance'])) {
				unset($stores[$key]);
			}
			else {
				$distance[$row['id']] = $row['distance'];
			}
		}
	}

	$sid = 0;
	$min_distance = min($distance);
	$sid = array_search($min_distance, $distance);

	if (0 < $sid) {
		$url = imurl('wmall/store/goods', array('sid' => $sid));
	}
	else {
		$url = imurl('wmall/home/index');
	}

	imessage(error(0, ''), $url, 'ajax');
}

include itemplate('home/near');

?>
