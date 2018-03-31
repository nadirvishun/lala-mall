<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '选择收货地址';
	icheckauth(false);
	$initials = pdo_fetchall('select distinct(initial) from ' . tablename('tiny_wmall_agent') . ' where uniacid = :uniacid and status = 1 order by initial', array(':uniacid' => $_W['uniacid']));
	$agents = pdo_fetchall('select id,title,area,initial from ' . tablename('tiny_wmall_agent') . ' where uniacid = :uniacid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));

	if (!empty($initials)) {
		foreach ($initials as &$row) {
			foreach ($agents as $val) {
				if ($row['initial'] == $val['initial']) {
					$row['agent'][] = $val;
				}
			}
		}
	}

	$agent = $agents[0];

	if (!empty($_W['agent'])) {
		$agent = $_W['agent'];
	}

	$addresss = member_fetchall_address();
}

if ($ta == 'suggestion') {
	load()->func('communication');
	$key = trim($_GPC['key']);
	$lat = intval($_GPC['lat']);
	$lng = intval($_GPC['lng']);
	$query = array('keywords' => $key, 'city' => trim($_GPC['city']), 'output' => 'json', 'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b', 'citylimit' => 'true');
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/assistant/inputtips?' . $query);

	if (is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}

	$result = @json_decode($result['content'], true);

	if (!empty($result['tips'])) {
		foreach ($result['tips'] as $key => &$val) {
			$val['distance'] = 10000000;
			$val['distance_available'] = 0;
			$val['address_available'] = 1;

			if (is_array($val['location'])) {
				unset($val[$key]);
			}
			else {
				$location = explode(',', $val['location']);
				$val['lng'] = $location[0];
				$val['lat'] = $location[1];
				$val['distance'] = distanceBetween($val['lng'], $val['lat'], $lng, $lat);
				$val['distance'] = round($val['distance'] / 1000, 2);
			}

			if (!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			}
			else {
				$val['address'] = $val['district'];
			}
		}

		$result['tips'] = array_sort($result['tips'], 'distance');
	}

	imessage(error(0, $result['tips']), '', 'ajax');
}

include itemplate('home/agent');

?>
