<?php
//微擎应用 http://www.we7.cc   
function sign($array)
{
	ksort($array);
	$string = '';

	while (list($key, $val) = each($array)) {
		$string = $string . $val;
	}

	return md5($string);
}

function signVerify($appSecret, $array)
{
	$newarray = array();
	$newarray['appSecret'] = $appSecret;
	reset($array);

	while (list($key, $val) = each($array)) {
		if ($key != 'sign') {
			$newarray[$key] = $val;
		}
	}

	$sign = sign($newarray);

	if ($sign == $array['sign']) {
		return true;
	}

	return false;
}

function buildCreditAutoLoginRequest($appKey, $appSecret, $uid, $credits)
{
	$url = 'http://www.duiba.com.cn/autoLogin/autologin?';
	$timestamp = (time() * 1000) . '';
	$array = array('uid' => $uid, 'credits' => $credits, 'appSecret' => $appSecret, 'appKey' => $appKey, 'timestamp' => $timestamp);
	$sign = sign($array);
	$url = $url . 'uid=' . $uid . '&credits=' . $credits . '&appKey=' . $appKey . '&sign=' . $sign . '&timestamp=' . $timestamp;
	return $url;
}

function buildCreditOrderStatusRequest($appKey, $appSecret, $orderNum, $bizId)
{
	$url = 'http://www.duiba.com.cn/status/orderStatus?';
	$timestamp = (time() * 1000) . '';
	$array = array('orderNum' => $orderNum, 'bizId' => $bizId, 'appKey' => $appKey, 'appSecret' => $appSecret, 'timestamp' => $timestamp);
	$sign = sign($array);
	$url = $url . 'orderNum=' . $orderNum . '&bizId=' . $bizId . '&appKey=' . $appKey . '&timestamp=' . $timestamp . '&sign=' . $sign;
	return $url;
}

function buildCreditAuditRequest($appKey, $appSecret, $passOrderNums, $rejectOrderNums)
{
	$url = 'http://www.duiba.com.cn/audit/apiAudit?';
	$timestamp = (time() * 1000) . '';
	$array = array('appKey' => $appKey, 'appSecret' => $appSecret, 'timestamp' => $timestamp);
	if (($passOrderNums != NULL) && !empty($passOrderNums)) {
		$string = NULL;

		while (list($key, $val) = each($passOrderNums)) {
			if ($string == NULL) {
				$string = $val;
			}
			else {
				$string = $string . ',' . $val;
			}
		}

		$array['passOrderNums'] = $string;
	}

	if (($rejectOrderNums != NULL) && !empty($rejectOrderNums)) {
		$string = NULL;

		while (list($key, $val) = each($rejectOrderNums)) {
			if ($string == NULL) {
				$string = $val;
			}
			else {
				$string = $string . ',' . $val;
			}
		}

		$array['rejectOrderNums'] = $string;
	}

	$sign = sign($array);
	$url = $url . 'appKey=' . $appKey . '&passOrderNums=' . $array['passOrderNums'] . '&rejectOrderNums=' . $array['rejectOrderNums'] . '&sign=' . $sign . '&timestamp=' . $timestamp;
	return $url;
}

function parseCreditConsume($appKey, $appSecret, $request_array)
{
	if ($request_array['appKey'] != $appKey) {
		throw new Exception('appKey not match');
	}

	if ($request_array['timestamp'] == NULL) {
		throw new Exception('timestamp can\'t be null');
	}

	$verify = signverify($appSecret, $request_array);

	if (!$verify) {
		throw new Exception('sign verify fail');
	}

	$ret = array('appKey' => $request_array['appKey'], 'credits' => $request_array['credits'], 'timestamp' => $request_array['timestamp'], 'description' => $request_array['description'], 'orderNum' => $request_array['orderNum']);
	return $ret;
}

function parseCreditNotify($appKey, $appSecret, $request_array)
{
	if ($request_array['appKey'] != $appKey) {
		throw new Exception('appKey not match');
	}

	if ($request_array['timestamp'] == NULL) {
		throw new Exception('timestamp can\'t be null');
	}

	$verify = signverify($appSecret, $request_array);

	if (!$verify) {
		throw new Exception('sign verify fail');
	}

	$ret = array('success' => $request_array['success'], 'errorMessage' => $request_array['errorMessage'], 'bizId' => $request_array['bizId']);
	return $ret;
}

defined('IN_IA') || exit('Access Denied');

?>
