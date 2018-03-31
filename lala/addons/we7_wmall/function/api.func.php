<?php
//微擎应用 http://www.we7.cc   
function array2url($params, $force = false)
{
	$str = '';

	foreach ($params as $key => $val) {
		if ($force && empty($val)) {
			continue;
		}

		$str .= $key . '=' . $val . '&';
	}

	$str = trim($str, '&');
	return $str;
}

function api_build_sign($params)
{
	unset($params['sign']);
	ksort($params);
	$string = array2url($params, true);
	$string = md5($string);
	$result = strtoupper($string);
	return $result;
}

function api_check_sign($params, $sign)
{
	$build_sign = api_build_sign($params);

	if ($build_sign != $sign) {
		return false;
	}

	return true;
}

defined('IN_IA') || exit('Access Denied');

?>
