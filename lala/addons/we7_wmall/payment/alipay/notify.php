<?php
//微擎应用 http://www.we7.cc   
error_reporting(0);
define('IN_MOBILE', true);

if (!empty($_POST)) {
	require '../../../../framework/bootstrap.inc.php';
	require '../../../../addons/we7_wmall/payment/__init.php';
	$out_trade_no = $_POST['out_trade_no'];
	$_W['uniacid'] = $_W['weid'] = intval($_POST['body']);
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['uniaccount']['acid'];
	$config_payment = get_system_config('payment');
	$config_alipay = $config_payment['alipay'];

	if (empty($config_alipay)) {
		exit('fail');
	}

	$prepares = array();

	foreach ($_POST as $key => $value) {
		if (($key != 'sign') && ($key != 'sign_type')) {
			$prepares[] = $key . '=' . $value;
		}
	}

	sort($prepares);
	$string = implode($prepares, '&');
	$string .= $config_alipay['secret'];
	$sign = md5($string);

	if ($sign == $_POST['sign']) {
		$_POST['query_type'] = 'notify';
		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid', array(':uniontid' => $out_trade_no));
		if (!empty($log) && ($log['status'] == '0') && ($_POST['total_fee'] == $log['card_fee'])) {
			$log['transaction_id'] = $_POST['trade_no'];
			$record = array();
			$record['status'] = '1';
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));
			$site = WeUtility::createModuleSite($log['module']);

			if (!is_error($site)) {
				$method = 'payResult';

				if (method_exists($site, $method)) {
					$ret = array();
					$ret['uniacid'] = $log['uniacid'];
					$ret['acid'] = $log['acid'];
					$ret['result'] = 'success';
					$ret['type'] = $log['type'];
					$ret['from'] = 'notify';
					$ret['tid'] = $log['tid'];
					$ret['uniontid'] = $log['uniontid'];
					$ret['transaction_id'] = $log['transaction_id'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					$ret['is_usecard'] = $log['is_usecard'];
					$ret['card_type'] = $log['card_type'];
					$ret['card_fee'] = $log['card_fee'];
					$ret['card_id'] = $log['card_id'];
					$site->$method($ret);
					exit('success');
				}
			}
		}
	}
}

exit('fail');

?>
