<?php
//微擎应用 http://www.we7.cc   
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
$input = file_get_contents('php://input');
$isxml = true;
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);

	if (empty($data)) {
		$result = array('return_code' => 'FAIL', 'return_msg' => '');
		echo array2xml($result);
		exit();
	}

	if (($data['result_code'] != 'SUCCESS') || ($data['return_code'] != 'SUCCESS')) {
		$result = array('return_code' => 'FAIL', 'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']);
		echo array2xml($result);
		exit();
	}

	$get = $data;
}
else {
	$isxml = false;
	$get = $_GET;
}

require '../../../../addons/we7_wmall/payment/__init.php';
$_W['uniacid'] = $_W['weid'] = intval($get['attach']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$config_payment = get_system_config('payment');

if (is_array($config_payment)) {
	$wechat = $config_payment['wechat'];

	if (!empty($wechat)) {
		ksort($get);
		$string1 = '';

		foreach ($get as $k => $v) {
			if (($v != '') && ($k != 'sign')) {
				$string1 .= $k . '=' . $v . '&';
			}
		}

		if ($wechat['type'] == 'partner') {
			$wechat['apikey'] = $wechat['partner']['apikey'];
		}
		else {
			$wechat['apikey'] = $wechat[$wechat['type']]['version'] == 1 ? $wechat[$wechat['type']]['key'] : $wechat[$wechat['type']]['apikey'];
		}

		$sign = strtoupper(md5($string1 . 'key=' . $wechat['apikey']));

		if ($sign == $get['sign']) {
			$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid', array(':uniontid' => $get['out_trade_no']));
			if (!empty($log) && ($log['status'] == '0') && (($get['total_fee'] / 100) == $log['card_fee'])) {
				$log['tag'] = iunserializer($log['tag']);
				$log['tag']['transaction_id'] = $get['transaction_id'];
				$log['uid'] = $log['tag']['uid'];
				$record = array('status' => 1, 'tag' => iserializer($log['tag']));
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
						$log['transaction_id'] = $get['transaction_id'];
						$ret['trade_type'] = $get['trade_type'];
						$ret['follow'] = $get['is_subscribe'] == 'Y' ? 1 : 0;
						$ret['user'] = empty($get['openid']) ? $log['openid'] : $get['openid'];
						$ret['fee'] = $log['fee'];
						$ret['tag'] = $log['tag'];
						$ret['is_usecard'] = $log['is_usecard'];
						$ret['card_type'] = $log['card_type'];
						$ret['card_fee'] = $log['card_fee'];
						$ret['card_id'] = $log['card_id'];

						if (!empty($get['time_end'])) {
							$ret['paytime'] = strtotime($get['time_end']);
						}

						$site->$method($ret);

						if ($isxml) {
							$result = array('return_code' => 'SUCCESS', 'return_msg' => 'OK');
							echo array2xml($result);
							exit();
						}
						else {
							exit('success');
						}
					}
				}
			}
		}
	}
}

if ($isxml) {
	$result = array('return_code' => 'FAIL', 'return_msg' => '');
	echo array2xml($result);
	exit();
	return 1;
}

exit('fail');

?>
