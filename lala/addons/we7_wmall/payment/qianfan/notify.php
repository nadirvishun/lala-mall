<?php
//微擎应用 http://www.we7.cc   
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';

if (empty($_POST['order_id'])) {
	exit('fail');
}

$wmall_log = pdo_get('tiny_wmall_paylog', array('out_trade_order_id' => $_POST['order_id']));

if (empty($wmall_log)) {
	exit('交易记录不存在');
}
else {
	if ($wmall_log['status'] == '1') {
		exit('success');
	}
}

$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE tid = :tid and module = :module', array(':tid' => $wmall_log['order_sn'], ':module' => 'we7_wmall'));

if (empty($log)) {
	exit('交易记录不存在');
}
else {
	if ($log['status'] == '1') {
		exit('success');
	}
}

$_W['uniacid'] = $_W['weid'] = intval($log['uniacid']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
require '../../../../addons/we7_wmall/payment/__init.php';
require '../../../../addons/we7_wmall/plugin/qianfanApp/model.php';
$sign = build_qianfan_sign($_POST);

if ($sign != $_POST['sign']) {
	exit('签名错误');
}

$order_id = trim($_POST['order_id']);
if (!empty($log) && ($log['status'] == '0')) {
	$log['tag'] = iunserializer($log['tag']);
	$log['tag']['transaction_id'] = $_POST['trade_no'];
	$log['uid'] = $log['tag']['uid'];
	$log['cost'] = array('cash_cost' => $_POST['cash_cost'], 'gold_cost' => $_POST['gold_cost']);
	$record = array('type' => 'qianfan', 'status' => 1, 'tag' => iserializer($log['tag']));
	pdo_update('core_paylog', $record, array('plid' => $log['plid']));
	$site = WeUtility::createModuleSite($log['module']);

	if (!is_error($site)) {
		$method = 'payResult';

		if (method_exists($site, $method)) {
			$ret = array();
			$ret['uniacid'] = $log['uniacid'];
			$ret['acid'] = $log['acid'];
			$ret['result'] = 'success';
			$ret['type'] = 'qianfan';
			$ret['channel'] = 'app';
			$ret['from'] = 'notify';
			$ret['tid'] = $log['tid'];
			$ret['uniontid'] = $_POST['out_trade_no'];
			$log['transaction_id'] = $_POST['trade_no'];
			$ret['fee'] = $log['fee'];
			$ret['tag'] = $log['tag'];
			$ret['is_usecard'] = $log['is_usecard'];
			$ret['card_type'] = $log['card_type'];
			$ret['card_fee'] = $log['card_fee'];
			$ret['card_id'] = $log['card_id'];

			if (!empty($_POST['pay_time'])) {
				$ret['paytime'] = strtotime($get['pay_time']);
			}

			$site->$method($ret);
			exit('success');
		}
	}
}

?>
