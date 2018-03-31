<?php
//微擎应用 http://www.we7.cc   
function parse_duiba_notify($request_array)
{
	global $_W;
	global $_GPC;
	$config = get_plugin_config('creditshop');
	if (empty($config) || !is_array($config)) {
		return error(-1, '积分商城配置出错');
	}

	if (empty($config['appkey'])) {
		return error(-1, '兑吧appkey为空');
	}

	if (empty($config['appsecret'])) {
		return error(-1, '兑吧appsecret为空');
	}

	pload()->func('duiba');
	$filter = array('i', 'channel');

	foreach ($request_array as $key => $val) {
		if (in_array($key, $filter)) {
			unset($request_array[$key]);
		}
	}

	if (empty($request_array['channel']) || ($request_array['channel'] == 'credit')) {
		$result = parseCreditConsume($config['appkey'], $config['appsecret'], $request_array);
	}
	else {
		$result = parseCreditNotify($config['appkey'], $config['appsecret'], $request_array);
	}

	if (!is_array($result)) {
		return error(-1, $result);
	}

	return $result;
}

defined('IN_IA') || exit('Access Denied');

?>
