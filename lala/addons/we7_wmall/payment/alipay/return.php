<?php
//微擎应用 http://www.we7.cc   
error_reporting(0);
define('IN_MOBILE', true);

if (empty($_GET['out_trade_no'])) {
	exit('fail');
}

require '../../../../framework/bootstrap.inc.php';
require '../../../../addons/we7_wmall/payment/__init.php';
$out_trade_no = $_GET['out_trade_no'];
$_W['uniacid'] = $_W['weid'] = intval($_GET['body']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$config_payment = get_system_config('payment');
$config_alipay = $config_payment['alipay'];

if (empty($config_alipay)) {
	exit('fail');
}

$prepares = array();

foreach ($_GET as $key => $value) {
	if (($key != 'sign') && ($key != 'sign_type')) {
		$prepares[] = $key . '=' . $value;
	}
}

sort($prepares);
$string = implode($prepares, '&');
$string .= $config_alipay['secret'];
$sign = md5($string);

if ($sign == $_GET['sign']) {
	$_GET['query_type'] = 'return';
	if (($_GET['is_success'] == 'T') && (($_GET['trade_status'] == 'TRADE_FINISHED') || ($_GET['trade_status'] == 'TRADE_SUCCESS'))) {
		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid', array(':uniontid' => $out_trade_no));

		if (!empty($log)) {
			$site = WeUtility::createModuleSite($log['module']);
			$method = 'payResult';

			if (!is_error($site)) {
				$ret['uniacid'] = $log['uniacid'];
				$ret['acid'] = $log['acid'];
				$ret['tid'] = $log['tid'];
				$ret['result'] = 'success';
				$ret['from'] = 'return';
				$ret['type'] = $log['type'];
				$site->$method($ret);
				exit();
			}
		}
	}
}

exit('fail');

?>
