<?php
//微擎应用 http://www.we7.cc   
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
require '../../../../addons/we7_wmall/defines.php';
require '../../../../addons/we7_wmall/model.php';
mload()->model('common');
mload()->model('plugin');
mload()->model('member');
global $_W;
global $_GPC;
$_W['uniacid'] = $_W['acid'] = intval($_GPC['i']);
$_W['we7_wmall']['config'] = get_system_config();
$_W['siteroot'] = str_replace('/addons/we7_wmall/plugin/creditshop', '', $_W['siteroot']);
$_W['_plugin'] = array('name' => 'creditshop');
pload()->func('duiba');
pload()->model('creditshop');
$uid = intval($_GPC['uid']);
$member = get_member($uid);
$result = parse_duiba_notify($_GET);

if (is_error($result)) {
	exit(json_encode(array('status' => 'fail', 'errorMessage' => $result['message'], 'credits' => $member['credit1'])));
}

if ($_GET['channel'] == 'consume') {
	$order = pdo_get('tiny_wmall_creditshop_order', array('uniacid' => $_W['uniacid'], 'ordernum' => $_GPC['orderMum']));

	if (!empty($order)) {
		exit(json_encode(array('status' => 'fail', 'errorMessage' => '订单已存在', 'credits' => $member['credit1'])));
	}

	if ($member['credit1'] < $_GPC['credits']) {
		exit(json_encode(array('status' => 'fail', 'errorMessage' => '积分不足', 'credits' => $member['credit1'])));
	}

	$result = member_credit_update($uid, 'credit1', 0 - $_GPC['credits'], array(0, '积分商城兑换商品:' . $_GPC['description']));

	if (is_error($result)) {
		exit(json_encode(array('status' => 'fail', 'errorMessage' => '积分不足', 'credits' => $member['credit1'])));
	}

	$credit1 = floatval(round($member['credit1'] - $_GPC['credits'], 2));
	$order = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'ordersn' => date('YmdHis') . random(6, true), 'ordernum' => $_GPC['orderNum'], 'status' => 0, 'credits' => $_GPC['credits'], 'itemcode' => $_GPC['itemCode'], 'actualPrice' => round($_GPC['actualPrice'] / 100, 2), 'faceprice' => round($_GPC['facePrice'] / 100, 2), 'description' => $_GPC['description'], 'addtime' => $_GPC['timestamp'] / 1000);
	pdo_insert('tiny_wmall_creditshop_order', $order);
	$return = array('status' => 'ok', 'errorMessage' => '', 'bizId' => $order['ordersn'], 'credits' => $credit1);
	exit(json_encode($return));
	return 1;
}

$order = pdo_get('tiny_wmall_creditshop_order', array('uniacid' => $_W['uniacid'], 'ordernum' => $_GPC['orderNum']));

if (!empty($order)) {
	$success = trim($_GET['success']);

	if ($success == 'true') {
		pdo_update('tiny_wmall_creditshop_order', array('status' => 1), array('id' => $order['id']));
	}
	else {
		pdo_update('tiny_wmall_creditshop_order', array('status' => 0), array('id' => $order['id']));
		member_credit_update($order['uid'], 'credit1', $order['credits'], array(0, '兑换商品失败,返回扣除的积分'));
	}
}

echo 'ok';
exit();

?>
