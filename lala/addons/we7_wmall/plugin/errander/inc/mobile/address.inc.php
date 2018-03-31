<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('goods');
icheckauth();
$_W['page']['title'] = '我的收货地址';
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');
$redirect_url = imurl('errander/category/index', array('id' => $_GPC['errander_id'])) . '&' . $_GPC['redirect_input'] . '=';

if ($op == 'post') {
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$address = member_fetch_address($id);

		if (empty($address)) {
			imessage('地址不存在或已经删除', referer(), 'error');
		}
	}
	else {
		$address = array('mobile' => $_W['member']['mobile'], 'realname' => $_W['member']['realname']);
	}

	if ($_GPC['d'] == 1) {
		$address['location_x'] = trim($_GPC['lat']);
		$address['location_y'] = trim($_GPC['lng']);
		$address['address'] = trim($_GPC['address']);
	}

	if ($_W['ispost']) {
		if (empty($_GPC['realname']) || empty($_GPC['mobile'])) {
			imessage(error(-1, '信息有误'), '', 'ajax');
		}

		$data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'realname' => trim($_GPC['realname']), 'sex' => trim($_GPC['sex']), 'mobile' => trim($_GPC['mobile']), 'address' => trim($_GPC['address']), 'number' => trim($_GPC['number']), 'location_x' => trim($_GPC['location_x']), 'location_y' => trim($_GPC['location_y']), 'type' => 1);
		if (empty($data['location_y']) || empty($data['location_x'])) {
			imessage(error(-1, '地址经纬度有误'), '', 'ajax');
		}

		$distance = distanceBetween($data['location_y'], $data['location_x'], $_config_plugin['map']['location_y'], $_config_plugin['map']['location_x']);
		if ((0 < $_config_plugin['serve_radius']) && (($_config_plugin['serve_radius'] * 1000) < $distance)) {
			imessage(error(-1, '该地址不跑腿服务范围内'), '', 'ajax');
		}

		if (!empty($address['id'])) {
			pdo_update('tiny_wmall_address', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_address', $data);
			$id = pdo_insertid();
		}

		imessage(error(0, $id), '', 'ajax');
	}
}

if ($op == 'serve_address') {
	$data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'name' => trim($_GPC['address']), 'address' => trim($_GPC['name']), 'location_x' => trim($_GPC['location_x']), 'location_y' => trim($_GPC['location_y']), 'number' => trim($_GPC['number']), 'type' => 2);
	if (empty($data['name']) || empty($data['location_x'])) {
		imessage(error(-1, '地址信息不完善'), '', 'ajax');
	}

	pdo_insert('tiny_wmall_address', $data);
	$id = pdo_insertid();
	imessage(error(0, $id), '', 'ajax');
}

if ($op == 'suggestion') {
	load()->func('communication');
	$key = trim($_GPC['key']);
	$query = array('keywords' => $key, 'city' => '全国', 'output' => 'json', 'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b', 'city' => $_config_plugin['city']);
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/assistant/inputtips?' . $query);

	if (is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}

	$result = @json_decode($result['content'], true);

	if (!empty($result['tips'])) {
		$distance_sort = 0;
		if ((0 < $_config_plugin['serve_radius']) && !empty($_config_plugin['map']['location_x']) && !empty($_config_plugin['map']['location_y'])) {
			$distance_sort = 1;
		}

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

				if ($distance_sort == 1) {
					$val['distance'] = distanceBetween($val['lng'], $val['lat'], $_config_plugin['map']['location_y'], $_config_plugin['map']['location_x']);
					$val['distance'] = round($val['distance'] / 1000, 2);
					$val['distance_available'] = 1;

					if ($_config_plugin['serve_radius'] < $val['distance']) {
						$val['address_available'] = 0;
					}
				}
			}

			if (!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			}
			else {
				$val['address'] = $val['district'];
			}
		}

		if ($distance_sort == 1) {
			$result['tips'] = array_sort($result['tips'], 'distance');
		}
	}

	imessage(error(0, $result['tips']), '', 'ajax');
}

include itemplate('address');

?>
