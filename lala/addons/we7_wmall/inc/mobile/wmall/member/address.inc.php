<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('goods');
$_W['page']['title'] = '我的收货地址';
icheckauth();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');
$sid = intval($_GPC['sid']);
$order_address_limit = 1;
$store = array('auto_get_address' => 1, 'order_address_limit' => 1, 'serve_radius' => 0);

if (0 < $sid) {
	$store = store_fetch($sid);
	$order_address_limit = $store['order_address_limit'];
}

if ($_W['is_agent']) {
	$store['auto_get_address'] = 1;
}

$redirect_type = trim($_GPC['redirect_type']);
$redirect_input = trim($_GPC['redirect_input']);
$routes = array('order' => imurl('wmall/order/create/index', array('sid' => $_GPC['sid'], 'r' => 1, 'recordid' => $_GPC['recordid'], 'redPacket_id' => $_GPC['redPacket_id'])) . '&address_id=');
$redirect_url = $routes[$redirect_type];

if ($ta == 'location') {
	$config = $_W['we7_wmall']['config']['takeout'];
	$map = array(
		'center'       => array('location_x' => '39.90923', 'location_y' => '116.397428'),
		'serve_radius' => 0
		);

	if (!empty($config['range']['map'])) {
		$map['center'] = array('location_x' => $config['range']['map']['location_x'], 'location_y' => $config['range']['map']['location_y']);
	}

	if (!empty($config['range']['serve_radius'])) {
		$map['serve_radius'] = $config['range']['serve_radius'];
	}
}

if ($ta == 'list') {
	$addresses = member_fetchall_address();

	if (1 < $store['order_address_limit']) {
		$available = array();
		$dis_available = array();

		foreach ($addresses as $li) {
			if (!empty($li['location_x']) && !empty($li['location_y'])) {
				$li['is_ok'] = is_in_store_radius($store, array($li['location_y'], $li['location_x']));

				if ($li['is_ok'] == 1) {
					$available[] = $li;
				}
				else {
					$dis_available[] = $li;
				}
			}
			else {
				$dis_available[] = $li;
			}
		}
	}
}

if ($ta == 'post') {
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

		if ($_W['is_agent']) {
			$store['auto_get_address'] = 1;
			$agentid = get_location_agent($data['location_x'], $data['location_y']);

			if ($agentid < 0) {
			}
		}

		if (!$store['auto_get_address']) {
			$data['location_x'] = '';
			$data['location_y'] = '';
		}
		else if ($store['order_address_limit'] == 2) {
			$distance = distanceBetween($data['location_y'], $data['location_x'], $store['location_y'], $store['location_x']);

			if (($store['serve_radius'] * 1000) < $distance) {
				imessage(error(-1, '商户配送范围' . $store['serve_radius'] . '公里, 当前地址不在商户配送范围内'), '', 'ajax');
			}
		}
		else {
			if ($store['order_address_limit'] == 4) {
				$store['delivery_areas'] = iunserializer($store['delivery_areas']);
				if (empty($store['delivery_areas']) || !is_array($store['delivery_areas'])) {
					imessage(error(-1, '商户没有完善配送区域'), '', 'ajax');
				}

				foreach ($store['delivery_areas'] as $area) {
					$flag = isPointInPolygon($area['path'], array($data['location_x'], $data['location_y']));

					if ($flag) {
						break;
					}
				}

				if (empty($flag)) {
					imessage(error(-1, '当前地址不在商户的配送范围'), '', 'ajax');
				}
			}
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

if ($ta == 'del') {
	if (!$_W['isajax']) {
		exit();
	}

	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if ($ta == 'default') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_address', array('is_default' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'type' => 1));
	pdo_update('tiny_wmall_address', array('is_default' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	exit();
}

include itemplate('member/address');

?>
